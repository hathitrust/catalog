<?php

set_include_path(get_include_path() . ':../..');

// Bail immediately if there's no query

// print_r($_REQUEST);
if (!isset($_REQUEST['q']) || !preg_match('/\S/', $_REQUEST['q'])) {
	header("HTTP/1.0 400 Malformed");
	exit();
}

require_once 'PEAR.php';
# require_once 'Apache/Solr/Service.php';
require_once 'sys/SolrConnection.php';
require_once 'services/Record/RecordUtils.php';
require_once 'sys/Normalize.php';

// Set up for autoload
function sample_autoloader($class) {
  require str_replace('_', '/', $class) . '.php';}
spl_autoload_register('sample_autoloader');

# $solr = new Apache_Solr_Service('solr-sdr-catalog', '9033', '/catalog');

# Get configArray
$configArray = parse_ini_file('../../conf/config.ini', true);

## Now munge it based on the hostname

$hn =  $_SERVER['HTTP_HOST'];

if (isset($configArray[$hn])) {
  foreach ($configArray[$hn] as $key => $val) {
    $configArray['Site'][$key] = $val;
  }
}

# So, we need to put ACAO in the header
# no matter what happens.

header('Access-Control-Allow-Origin: *');




// Map api fields to solr fields
$fieldmap = array(
  'umid' => 'id',
  'sysid' => 'id',
  'recordid' => 'id',
  'recordnumber' => 'id',
  'htid' => 'ht_id',
  'oclc' => 'oclc_search'
);


// $rightsmap = array(
//   'pd' => 'Full view',
//   'ic' => "Limited (search-only)",
//   'opb' => "Limited (search-only)",
//   'op' => "Limited (search-only)",
//   'orph' => "Limited (search-only)",
//   'orphcand' => "Limited (search-only)",
//   'umall' => "Limited (search-only)",
//   'world' => 'Full view',
//   'ic-world' => 'Full view',
//   'und-world' => 'Full view',
//   'und' => "Limited (search-only)",
//   'nobody' => "Limited (search-only)",
//   'pdus' => 'Full view',
//   'cc-by' => 'Full view',
//   'cc-by-nd' => 'Full view',
//   'cc-by-nc-nd' => 'Full view',
//   'cc-by-nc' => 'Full view',
//   'cc-by-nc-sa' => 'Full view',
//   'cc-by-sa' => 'Full view',
//   'cc-zero' => 'Full view'
// );


$collectionsmap = eval(file_get_contents($configArray['Site']['facetDir'] . '/ht_collections.php'));

$commonargs = array(
  'fl' => 'score,id,ht_json,title,year,publishDate,oclc,oclc_search,lccn,isbn,issn',
  'rows' => 200
);

if ($_REQUEST['brevity'] == 'full') {
  $commonargs['fl']  = $commonargs['fl']  . ',fullrecord';
}


$validField = array(
  'id' => 'passthrough',
  'htid' => 'passthrough',
  'ht_id' => 'passthrough',
  'isbn' => 'isbnlongify',
  'oclc' => 'numeric',
  'issn' => 'Normalize::normalize_issn',
  'lccn' => 'lccnnormalize',
  'umid' => 'numeric',
  'sysid' => 'numeric',
  'recordid' => 'numeric',
  'recordnumber' => 'zero_pad_id'
);


class QObj
{
  public $string;
  private $_id;
  public $tspecs = array(); # Transformed specs
  public $qspecs = array(); # Query specs for solr
  public $matches = array(); # Matching document IDs
  
  
  
  function __construct($str) {
    global $validField;
    global $fieldmap;
    
    $this->string = $str;
    
    $specs = explode(';', $str);
    foreach ($specs as $spec) {
      $fv = explode(':', $spec, 2);
#      if (count($fv) > 2) {
#        $fv[1] = implode(':', array_slice($fv, 1));
#      }
      $field = trimlower($fv[0]);

      if ($field == 'id') {
        $this->_id = $fv[1];
	continue;
      }
      if (!isset($fv[1])) {
        continue;
      }
      if (! ( preg_match('/\S/', $field) || preg_match('/\S/', $fv[1]))) {
        continue;
      }

      $val = trimlower($fv[1]);

      // 
      // echo "Q is " . $_REQUEST['q'];
      // echo "Looking for $field = $val\n";

      if (!isset($validField[$field])) {
        // echo "Skipping $field -- not set in validField\n";
        continue;
      }
      $fixedval = $validField[$field]($val); // weird call-variable-value-as-name-of-function
     
      // Escape the colons
      
      
#      $fixedval = preg_replace('/:/', '\:', $fixedval);
      $fixedval = $this->lucene_escape($fixedval);
      $qfield = isset($fieldmap[$field])? $fieldmap[$field] : $field;


      $this->qspecs[] = "$qfield:$fixedval";
      if ($qfield == 'lccn') {
        $this->qspecs[] = "lccn:\" $fixedval\"";
        $this->qspecs[] = "lccn:\"  $fixedval\"";
        $this->qspecs[] = "lccn:\"   $fixedval\"";
        $this->qspecs[] = "lccn:\"    $fixedval\"";
        $this->qspecs[] = "lccn:\"     $fixedval\"";
        $this->qspecs[] = "lccn:\"      $fixedval\"";
        
      }
      $this->tspecs[] = array($field, $qfield, $fixedval);
    }
  }

  function lucene_escape($str) {
    $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
    $replace = '\\\$1';
    return preg_replace($pattern, $replace, $str);
  }

  
  function id() {
    if (isset($this->_id)) {
      return $this->_id;
    } else {
      return $this->string;
    }
  }
  
  function setDocMatches($docs) {
    global $validField;
    foreach ($docs as $doc) {
      $match = false;
      $nonmatch = false;
      foreach ($this->tspecs as $fv) {
        $ofield = $fv[0]; // original query field name
        $qfield = $fv[1]; // query field as a solr field
        $qval   = $fv[2]; // alredy fixed query value

        // Special-case htid because we don't actually store the ht_id
        $is_htidspec = ($qfield == 'ht_id');
        if ($is_htidspec) {
          $qfield = 'ht_json';
        }

        $docq = isset($doc[$qfield]) ? $doc[$qfield] : null;

    if (isset($docq) && ((!is_array($docq) || count($docq) > 0))) {
          if ($is_htidspec) {
            $dvals = array();
            foreach (json_decode($doc['ht_json'], true) as $ht) {
              $dvals[] = $ht['htid'];
            }
          } else {
            $dvals = $docq;
          }
          if (!is_array($dvals)) {
            $dvals = array($dvals);
          }
          
          // Normalize
          foreach ($dvals as $i => $dval) {
            $dvals[$i] = $validField[$ofield]($dval);
          }

          // For an array of vals, it matches if at least one matches
          $gotone = false;
          foreach ($dvals as $d) {
            $d_esc = Normalize::lucene_escape($d);
            if ($d_esc == $qval) {
              $gotone = true;
              $match = true;
#             echo "Matched '$d' and '$qval' for '$qfield' against $this->string\n";
              continue;
            } 
          }
          // We wouldn't be here if there weren't values to match.
          // If none of them did, it's a nonmatch
          if (!$gotone) {
            $nonmatch = true;
          }
        }
      }
      // WTH is going on here???
      if ($match) { // && !$nonmatch) {
        $this->matches[] = $doc['id'];
      }
    }    
  }
  
  function recordsStructure($docs) {
    global $validField;
    
    $records = array();
    foreach ($this->matches as $docid) {
      $doc = $docs[$docid];
      $rinfo = array();
      $rinfo['recordURL'] = 'https://catalog.hathitrust.org/Record/' . $docid;
      foreach (array('title', 'isbn', 'issn', 'oclc', 'lccn', 'publishDate') as $index) {
        if (isset($doc[$index])) {
          $vals = is_array($doc[$index])? $doc[$index] : array($doc[$index]);
          $rinfo[$index . 's']  = array();
          foreach ($vals as $val) {
            $rinfo[$index . 's'][] = isset($validField[$index])? $validField[$index]($val) : $val;
            // $rinfo[$index . 's'][] =  $val;
          }
        } else {
          $rinfo[$index . 's'] = array();
        }
      }
      if ($_REQUEST['brevity'] == 'full') {
        $rinfo['marc-xml'] = $doc['fullrecord'];
      }
      $records[$docid] = $rinfo;
    }
    return $records;
  }
  
  function itemsStructure($docs) {
    global $collectionsmap;
    // global $rightsmap;
    
    $ru = new RecordUtils();
    
    $items = array();
    foreach ($this->matches as $docid) {
      $doc = $docs[$docid];
      foreach (json_decode($doc['ht_json'], true) as $ht) {
        $iinfo = array();
        $htid = $ht['htid'];
        $collection_code = $ht['collection_code'];
        $iinfo['orig'] = $collectionsmap[$collection_code]['original_from'];

        $iinfo['fromRecord'] = $docid;
        $iinfo['htid'] = $htid;
        $iinfo['itemURL'] = "https://babel.hathitrust.org/cgi/pt?id=" . $htid;

        $rc = isset($ht['rights']) ? $ht['rights'] : 'ic';
	$rc = is_array($rc) ? $rc[0] : $rc;
        $iinfo['rightsCode'] = $rc;
        $iinfo['lastUpdate'] = $ht['ingest'];
        $iinfo['enumcron'] = (isset($ht['enumcron']) && preg_match('/\S/', $ht['enumcron']))? $ht['enumcron'] : false;
        $iinfo['usRightsString'] = $ru->is_fullview($iinfo['rightsCode'], true) ? "Full view" : "Limited (search-only)";
        $items[] = $iinfo;
      }
    }
    return $items;
  }
  
}


// Parse out the query string

$origQuery = $_REQUEST['q'];
$qstrings = explode('|', $_REQUEST['q']);
$qobjs = array();
$solrQueryComponents = array();


foreach ($qstrings as $qstring) {
  $nqo = new QObj($qstring);
  $solrQueryComponents = array_merge($solrQueryComponents, $nqo->qspecs);
  $qobjs[$qstring] = $nqo;
}

// print_r($solrQueryComponents);
// Build the query
$q =  join(' OR ', $solrQueryComponents);

// If there's no $q, throw a 400

if (!preg_match('/\S/', $q)) {
  header('HTTP/1.1 400 Bad Request');
  echo "Query '$origQuery' is invalid";
  exit();
}


// ***** Put this in a try/catch?

$commonargs['q'] = $q;
$solr = new SolrConnection();

foreach ($commonargs as $key => $value) {
  $solr->add([[$key, $value]]);
 }

#$results = $solr->search($q, 0, 200, $commonargs);
$results = $solr->send();

# Index the documents;
$docs = array();
foreach ($results['response']['docs'] as $doc) {
  $docs[$doc['id']] = $doc;
}

// OK. Now we need to go through and find the matches
$matches = array();
$matchingdocs = array();
$allmatches = array();

foreach ($qobjs as $qobj) {
  $qobj->setDocMatches($docs);
  $id = $qobj->id();
  $allmatches[$id] = array();
  // Get the record
  $records = $qobj->recordsStructure($docs);
  $items   = $qobj->itemsStructure($docs);

if (count($items)) {
    usort($items, 'enumsort');
  }
  $allmatches[$id]['records'] = $records;
  $allmatches[$id]['items'] = $items;
}

// $allmatches now has the return structure


if (isset($_REQUEST['single']) && $_REQUEST['single']) {
  foreach ($allmatches as $key => $val) {
    $allmatches = $val;
  }
}



if ($_REQUEST['type'] == 'json') {
  if (isset($allmatches['records']) && count($allmatches['records']) == 0) {
    echo  "{\n \"records\": {}, \"items\": []\n}";
    exit;
  } else {
    $json = json_encode($allmatches);
    if (isset($_REQUEST['callback'])) {
      header('Content-type: application/javascript; charset=UTF-8');  
      echo $_REQUEST['callback'] . "( $json)";
    } else {
      header('Content-type: application/json; charset=UTF-8');
      echo $json;
    }
  }
}

if ($_REQUEST['type'] == 'oclcscrape') {
  require_once 'Smarty/Smarty.class.php';
  $interface = new Smarty();
  $interface->compile_dir = '../../interface/compile';
  $interface->template_dir = 'templates';
  $interface->assign('data', $allmatches);

  # Get the first doc
  foreach ($allmatches['records'] as $id => $doc) {
    $interface->assign('doc', $doc);
    continue; # only get the first one
  }

  $interface->display('volumes/oclchtml.tpl');  
}



//------------------------------
//---- Support Functions ---
//-----------------------------

$memoize = array();
function sortstringFromEnumcron($str) {
  global $memoize;
  if (isset($memoize[$str])) {
    return $memoize[$str];
  }
  $rv = '';
  preg_match('/\d+/', $str, $match);
  if (isset($match[0])) {
    if (!is_array($match[0])) {
      $match[0]  = array($match[0]);
    }
    foreach ($match[0] as $digits) {
      $rv .= strlen($digits) . $digits;
    }
  }
  $memoize[$str] = $rv;
  return $rv;
}


function enumsort($a, $b) {
  $sa = sortstringFromEnumcron($a['enumcron']);
  $sb = sortstringFromEnumcron($b['enumcron']);
  if ($sa == $sb) {
      return 0;
  }
  return ($sa < $sb) ? -1 : 1;
}

// 
// {
//     "records":
//       {
//         "000366004":
//           {
//             "recordURL" : "http://catalog.hathitrust.org/Record/000366004",
//             "titles": ["The Sneetches, and other stories. Written and illustrated by Dr. Seuss."],
//             "isbns": [],
//             "issns": [],
//             "oclcs": ["00470409"],
//             "lccns": ["68001537"]
//           }
//       }
//     "items": [
//       {
//         "fromRecord": "000366004",
//         "htid": "mdp.39015079651611",
//         "itemURL": "http://babel.hathitrust.org/cgi/pt?id=mdp.39015079651611",
//         "rightscode": "ic",
//         "lastUpdate": "20091004",
//         "orig": "University of Michigan",
//         "enumcron": false
//       }
//     ],
//   }

//============================================
// NORMALIZATION FUNCTIONS
//============================================

function zero_pad_id($str) {
  while (strlen($str) < 9) {
    $str = '0' . $str;
  }
  return $str;
  
}

function passthrough($str) {
  return $str;
}

function trimlower($str) {
  return trim(strtolower($str));
}

   // <!-- Simple type to normalize isbn/issn -->
   //  <fieldType name="stdnum" class="solr.TextField" sortMissingLast="true" omitNorms="true" >
   //    <analyzer>
   //      <tokenizer class="solr.KeywordTokenizerFactory"/>
   //      <filter class="solr.LowerCaseFilterFactory"/>
   //      <filter class="solr.TrimFilterFactory"/>
   //      <!--   pattern="^\s*0*([\d\-\.]+[xX]?).*$" replacement="$1"  -->
   //      <!--   pattern="^[\s0\-\.]+([\d\-\.]+[xX]?).*$" replacement="$1" -->
   //      <filter class="solr.PatternReplaceFilterFactory"
   //           pattern="^[\s0\-\.]*([\d\.\-]+x?).*$" replacement="$1"
   //      />
   //      <filter class="solr.PatternReplaceFilterFactory"
   //           pattern="[\-\.]" replacement=""  replace="all"
   //      />
   //    </analyzer>
   //  </fieldType>
   
   
function stdnum($str) {
  $str = trim(strtolower($str));
  $str = preg_replace('/^[\s0\-\.]*([\d\.\-]+x?).*$/', '$1', $str);
  return preg_replace('/[\-\.]/', '', $str);
  
}

// <fieldType name="exactmatcher" class="solr.TextField" omitNorms="true">
//        <analyzer>
//          <tokenizer class="solr.KeywordTokenizerFactory"/> 
//          <filter class="schema.UnicodeNormalizationFilterFactory" version="icu4j" composed="false" remove_diacritics="true" remove_modifiers="true" fold="true"/>
//          <filter class="solr.LowerCaseFilterFactory"/>
//          <filter class="solr.TrimFilterFactory"/>
//          <filter class="solr.PatternReplaceFilterFactory"
//               pattern="[^\p{L}\p{N}]" replacement=""  replace="all"
//          />
//        </analyzer>
//      </fieldType>

function exactmatcher($str) {
  return preg_replace('/[^\p{L}\p{N}]/', '', trim(strtolower($str)));
}

// <fieldType name="numeric" class="solr.TextField" sortMissingLast="true" omitNorms="true" >
//   <analyzer>
//     <tokenizer class="solr.KeywordTokenizerFactory"/> 
//     <filter class="solr.LowerCaseFilterFactory"/>
//     <filter class="solr.TrimFilterFactory"/>
//     <filter class="solr.PatternReplaceFilterFactory"
//          pattern="[^0-9]*([0-9]+)[^0-9]*" replacement="$1"
//     />
//     <filter class="solr.PatternReplaceFilterFactory"
//          pattern="^0*(.*)" replacement="$1"
//     />
//   </analyzer>
// </fieldType>

function numeric($str) {
  $str = preg_replace('/^[^0-9]*?([0-9]+)/', '$1', trim(strtolower($str)));
  return preg_replace('/^0+/', '', $str);
}


function isbnlongify($str) {
  // Ditch any dashes or dots
  $str = preg_replace('/[\-\.]/', '', $str);
  if (!preg_match('/^.*\b(\d{9})[\dXx](?:\Z|\D).*$/', $str, $match)) {
    return $str;
  }
  $longisbn = '978' . $match[1];
  $sum = 0;
  for ($i = 0; $i < 12; $i++) {
    $sum += $longisbn[$i] + (2 * $longisbn[$i] * ($i % 2));
  }
  $top = $sum + (10 - ($sum % 10));
  $check = $top - $sum;
  if ($check == 10) {
    return $longisbn . '0';
  } else {
    return $longisbn . $check;
  }
  
}

// Normalization pattern from http://www.loc.gov/marc/lccn-namespace.html#syntax

function lccnnormalize($val) {
  // First, ditch the spaces
  $val = preg_replace('/\s/', '', $val);
  
  // Lose any trailing slash-plus-characters
  if (preg_match('/^(.*?)\//', $val, $match)) {
    $val = $match[1];
  }
  
  // if there's a hyphen, remove it and right-zero-pad the remaining digits to six chars
  if (preg_match('/^(\w+)-(\d+)/', $val, $match)) {
    $prefix = $match[1];
    $digits = $match[2];
    $digits = sprintf('%06d', $digits);
    return $prefix . $digits;
  } else {
    return $val;
  }
}







