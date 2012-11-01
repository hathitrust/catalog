<?php
$path = explode('/', __FILE__);
array_pop($path);
require_once(implode('/', $path) . '/Normalize.php');



class QObj
{
  public $string;
  private $_id;
  public $tspecs = array(); # Transformed specs
  public $qspecs = array(); # Query specs for solr
  public $matches = array(); # Matching document IDs


  // Map api fields to solr fields
  public $fieldmap = array(
    'umid' => 'id',
    'htid' => 'ht_id'
  );


  public $rightsmap = array(
    'pd' => 'Full view',
    'ic' => "Limited (search-only)",
    'opb' => "Limited (search-only)",
    'op' => "Limited (search-only)",
    'orph' => "Limited (search-only)",
    'umall' => "Limited (search-only)",
    'world' => 'Full view',
    'und' => "Limited (search-only)",
    'nobody' => "Limited (search-only)",
    'pdus' => 'Full view'
  );



  public $namespacemap; 


  public $validField = array(
    'htid' => array('Normalize', 'exactmatcher'),
    'isbn' => array('Normalize', 'isbnlongify'),
    'oclc' => array('Normalize', 'numeric'),
    'issn' => array('Normalize', 'stdnum'),
    'lccn' => array('Normalize', 'lccnnormalize'),
    'umid' => array('Normalize', 'numeric')
  );
  
  
  
  function __construct($str) {

    global $configArray;

    
    $this->string = $str;
    $this->namespacemap =  eval(file_get_contents($configArray['Site']['facetDir'] . '/ht_namespaces.php'));
    
    $specs = explode(';', $str);
    foreach ($specs as $spec) {
      $fv = explode(':', $spec);
      $field = Normalize::trimlower($fv[0]);
      if ($field == 'id') {
        $this->_id = $fv[1];
      }

      $val = Normalize::trimlower($fv[1]);

      if (!isset($this->validField[$field])) {
        #echo "Skipping $field\n";
        continue;
      }
      $fixedval = $this->validField[$field]? call_user_func($this->validField[$field], $val) : $val; 
      $qfield = isset($this->fieldmap[$field])? $this->fieldmap[$field] : $field;
      $this->qspecs[] = "$qfield:$val";
      $this->tspecs[] = array($field, $qfield, $fixedval);
    }
  }
  
  function id() {
    if (isset($this->_id)) {
      return $this->_id;
    } else {
      return $this->string;
    }
  }
  
  function setDocMatches($docs) {
    foreach ($docs as $doc) {
      $match = false;
      $nonmatch = false;
      foreach ($this->tspecs as $fv) {
        $ofield = $fv[0]; // original query field name
        $qfield = $fv[1]; // query field as a solr field
        $qval   = $fv[2]; // alredy fixed query value
        if (isset($doc->$qfield) && (!is_array($doc->$qfield) || count($doc->$qfield) > 0)) {
          $dvals = $doc->$qfield;
          if (!is_array($dvals)) {
            $dvals = array($dvals);
          } 
          foreach ($dvals as $i => $dval) {
            $dvals[$i] = $this->validField[$ofield]? call_user_func($this->validField[$ofield], $dval) : $dval; 
          }

          // For an array of vals, it matches if at least one matches
          $gotone = false;
          foreach ($dvals as $d) {
            if ($d == $qval) {
              $gotone = true;
              $match = true;
              // echo "Matched '$d' and '$qval' for '$qfield' against $this->string\n";
              continue 2;
            } 
          }
          // We wouldn't be here if there weren't values to match.
          // If none of them did, it's a nonmatch
          if (!$gotone) {
            $nonmatch = true;
          }
        }
      }
      if ($match && !$nonmatch) {
        $this->matches[] = $doc->id;
      }
    }    
  }
  
  function recordsStructure($docs) {
    $records = array();
    foreach ($this->matches as $docid) {
      $doc = $docs[$docid];
      $rinfo = array();
      $rinfo['recordURL'] = 'http://catalog.hathitrust.org/Record/' . $docid;
      foreach (array('title', 'isbn', 'issn', 'oclc', 'lccn') as $index) {
        if (isset($doc->$index)) {
          $rinfo[$index . 's']  = is_array($doc->$index)? $doc->$index : array($doc->$index);
        } else {
          $rinfo[$index . 's'] = array();
        }
      }
      $records[$docid] = $rinfo;
    }
    return $records;
  }
  
  function itemsStructure($docs) {

    $items = array();
    foreach ($this->matches as $docid) {
      $doc = $docs[$docid];
      foreach (json_decode($doc->ht_json, true) as $ht) {
        $iinfo = array();

        $htid = $ht['htid'];
        preg_match('/(.*?)\./', $htid, $match);
        $iinfo['orig'] = $this->namespacemap[$match[1]];

        $iinfo['fromRecord'] = $docid;
        $iinfo['htid'] = $htid;
        $iinfo['itemURL'] = "http://hdl.handle.net/2027/" . $htid;
        $iinfo['rightsCode'] = $ht['rights'];
        $iinfo['lastUpdate'] = $ht['ingest'];
        $iinfo['enumcron'] = isset($ht['enumcron'])? $ht['enumcron'] : false;
        $iinfo['usRightsString'] = $this->usrights($ht['rights']);
        
        $items[] = $iinfo;
      }
    }
    return $items;
  }
  
  function usrights($r) {
    if (isset($this->rightsmap[$r])) {
      return $this->rightsmap[$r];
    }
    
    if (preg_match('/^cc/', $r)) {
      return $this->rightsmap['pd'];
    }
    
    return $this->rightsmap['ic'];
  }
  
}


?>
