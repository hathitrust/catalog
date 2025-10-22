<?php

require_once 'services/Search/SearchStructure.php';

require_once 'services/Record/FilterFormat.php';
require_once 'services/Record/RecordUtils.php';


require_once 'sys/VFSession.php';
require_once 'sys/VFUser.php';

require_once "feedcreator/include/feedcreator.class.php";

require_once 'sys/DBH.php';
require_once 'sys/AuthSpecs.php';
require_once 'sys/JSON.php';
require_once 'sys/Normalize.php';

class SearchExport {

  private $db;
  public $ss;
  private $session;
  private $ru;
  private $results;
  private $tagSpecs;
  private $formatMap = 'Hello';
  private $source = 'record';
  private $tempset = false;
  private $controltags = array('001' => true, '002' => true, '003' => true, '004' => true, '005' => true, '006' => true,
      '007' => true, '008' => true, '009' => true);

  /**
   * launch() -- setup and dispatch
   *
   * Sets up the solr and ss objects, determines the method to run, and runs it. If the method is
   * invalid, return a 403 Forbidden
   */
  function launch($ss = null) {
    global $configArray;

    $class = $configArray['Index']['engine'];

    $this->db = new $class($configArray['Index']['url']);
    $this->session = VFSession::instance();

    // We have a couple special cases. if tempset is true, just get the tempset (selected items).
    // If handpicked is true, just use those IDs (used esp. from single-record export)
    // Otherwise, get a regular $ss.
    // Should probably MOVE THIS TO SearchStructure!!!!!
    $handpicked = isset($_REQUEST['handpicked']) ? $_REQUEST['handpicked'] : false;
    $tempset = isset($_REQUEST['tempset']) ? true : false;

    if ($tempset) {
      $this->source = 'selected';
    }

    if (!($handpicked || $tempset)) {
      $this->ss = new SearchStructure();
    } else {
      $this->ss = new SearchStructure(true); // blank;
    }

    if ($handpicked) {
      $hp = preg_split('/\s*[,;]\s*/', $handpicked);
      $this->ss->addIDs($hp);
    } elseif ($tempset) {
      $this->ss->addTag($this->session->uuid);
      $this->tempset = true;
    }

    // Get the results from the session if we've got a callback from refworks

    if (isset($_REQUEST['rfref'])) {
      $authspecs = AuthSpecs::singleton('conf/authspecs.yaml');
      $dargs = $authspecs['DSessionDB'];
      $dbh = DBH::singleton($dargs);
      $sth = $dbh->prepare('select results from  refworks_redirects where uuid=? and expires > ?');
      $sth->execute(array($_REQUEST['rfref'], time()));
      $dataarr = $sth->fetch(PDO::FETCH_NUM);
      if ($dataarr) {
        $this->results = unserialize($dataarr[0]);
      } else {
        echo "No results found" . '. Result sets expire after 10 minutes; if it\'s been longer than that, please export again.';
        return;
      }
    }

    if (!isset($this->results)) {
      $this->results = $this->db->simplesearch($this->ss, 0, 150); # limit to 150 results
    }

    // Abort on empty
    if ($this->results['RecordCount'] == 0) {
      return;
    }


    $this->ru = new RecordUtils();

    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : null;
    if ($method && is_callable(array($this, $method))) {
      $this->$method();
    } else {
      header("HTTP/1.0 403 Forbidden");
      echo "Method $method not recognized.";
    }
  }

  function rss() {
    $this->atom();
  }

  function mrc() {
    return $this->marcfirst();
  }

  function marc() {
    return $this->marc_pretty();
  }


  function marc_pretty() {
    global $interface;

    header("Content-type: text/html; charset=UTF-8");
    foreach ($this->results['record'] as $rawr) {
      $marc = $this->ru->getMarcRecord($rawr);
      $interface->assign('title', $rawr['title'][0]);
      break; # only return one
    }
    $interface->assign('marc', $marc);
    $interface->assign('fields', $marc->getFields());
    echo $interface->fetch("Record/marc_pretty.tpl");


  }

  function marcfirst() {
    header('Content-type: application/marc; charset=UTF-8');
    foreach ($this->results['record'] as $rawr) {
      $marc = $this->ru->getMarcRecord($rawr);
      echo $marc->toRaw();
      break; # only return one
    }
  }

  function marcxmlfirst() {
    header('Content-type: text/xml; charset=UTF-8');
    foreach ($this->results['record'] as $rawr) {
      $marc = $this->ru->getMarcRecord($rawr);
      echo $marc->toXML();
      break; # only return one
    }
  }

  function json() {
    header('Content-type: application/json; charset=UTF-8');
    foreach ($this->results['record'] as $rawr) {
      $marc = $this->ru->getMarcRecord($rawr);
      $json = new JSON();
      echo $json->encode_marc($marc);
      break; # only return one
    }
  }

  function rdftext() {
    $this->rdf('text/xml');
  }

  // function rdf($contenttype = "application/rdf+xml") {
  //   global $configArray;
  //   global $interface;
  //   header('Content-type: $contenttype; charset=UTF-8');
  //   $r = $this->results['record'][0];
  // 
  //   // Standardize the numbers
  //   foreach (array('isbn') as $num) {
  //     if (isset($r[$num])) {
  //       foreach ($r[$num] as $i => $v) {
  //         $r[$num][$i] = Normalize::stdnum($v, true); # true to leave leading zeros
  //       }
  //     }
  //   }
  //   $interface->assign('record', $r);
  //   $interface->assign('marc', $this->ru->getMarcRecord($r));
  //   $interface->assign('url', $configArray['Site']['url']);
  //   echo $interface->fetch('Record/rdf.tpl');
  // }

  function xml() {
    $this->marcxmlfirst();
  }

  //#######################
  // TAGGED FORMATS
  //#######################

  function ris() {
    $this->genericRIS('conf/risexport.yaml');
  }

  function zoteroRIS() {
    $this->genericRIS('conf/zoteroRIS.yaml');
  }

  function genericRIS($filename) {
    // header('Content-type: text/plain; charset=UTF-8');
    header('Content-type: application/x-Research-Info-Systems; charset=UTF-8');
    header('Content-disposition: attachment; filename=references.ris');

    $this->tagSpecs = yaml_parse_file($filename);
    $this->_buildFormatMap($this->tagSpecs['typemap']);

    foreach ($this->results['record'] as $rawr) {
      echo $this->taggedRecord($rawr, $this->tagSpecs['tagprefix'], $this->tagSpecs['tagsuffix']);
      echo "\nER  - \n\n";
    }
  }

  function ristext() {
    header("Content-type: text/plain");
    $this->tagSpecs = yaml_parse_file('conf/risexport.yaml');
    $this->_buildFormatMap($this->tagSpecs['typemap']);

    foreach ($this->results['record'] as $rawr) {
      echo $this->taggedRecord($rawr, $this->tagSpecs['tagprefix'], $this->tagSpecs['tagsuffix']);
      echo "\nER  - \n\n";
    }
  }

  function endnote() {
    header('Content-type: application/x-endnote-refer; charset=UTF-8');
    header('Content-disposition: attachment; filename=references.enw');
    // header('Content-type: text/plain; charset=UTF-8');

    $this->tagSpecs = yaml_parse_file('conf/endnote.yaml');
    $this->_buildFormatMap($this->tagSpecs['typemap']);

    foreach ($this->results['record'] as $rawr) {
      echo $this->taggedRecord($rawr, $this->tagSpecs['tagprefix'], $this->tagSpecs['tagsuffix']);
      echo "\n\n";
    }
  }

  // Refworks tagged format
  function rfFormat() {
    header('Content-type: text/plain; charset=UTF-8');
    header('Content-disposition: attachment; filename=references.rft');
    $this->tagSpecs = yaml_parse_file('conf/refworksFormatExport.yaml');
    $this->_buildFormatMap($this->tagSpecs['typemap']);

    foreach ($this->results['record'] as $rawr) {
      echo $this->taggedRecord($rawr, $this->tagSpecs['tagprefix'], $this->tagSpecs['tagsuffix']);
      echo "\n\n";
    }
  }

  function refworks() {
    return $this->rfFormat();
  }

  //######################################
  // Produce a tagged record format
  //######################################

  function taggedRecord($record, $tagprefix = '', $tagsuffix = '') {
    global $configArray;
    $m = $this->ru->getMarcRecord($record);
    $lines = array();

    foreach($this->tagSpecs['fieldmap'] as $fieldMapItem) {
      foreach ($fieldMapItem as $exportTag => $tagspec) {
        foreach ($tagspec as $tuple) {
          $line = $tagprefix . $exportTag . $tagsuffix;

          // Take care of special cases
          $tag = $tuple[0];

          if (preg_match('/^\d+$/', $tag)) {
            $tag = sprintf('%03d', $tag);
          }
          if ($tag == 'TITLE') {
            $titles = $this->ru->getLongTitles($m);
            $title = $titles[0];
            array_push($lines, $line . $title);
            continue;
          }
          if ($tag == 'ID') {
            array_push($lines, $line . $record['id']);
            continue;
          }
          if ($tag == 'RECORDURL') {
            $url = $configArray['Site']['url'];
            if (substr($url, 0, 2) === '//') {
              $url = 'https:' . $url;
            }
            array_push($lines, $line . $url . '/Record/' . $record['id']);
            continue;
          }
          if ($tag == 'TYPE') {
            $type = $this->bestFormat($record['baseFormat']);
            array_push($lines, $line . $type);
            continue;
          }

          if ($tag == 'ALEPHTYPE') {
            $type = $this->bestFormat($record['baseFormat'], true);
            array_push($lines, $line . $type);
            continue;
          }

          if ($tag == 'HTLINK') {
            foreach ($m->getFields('974', true) as $f) {
              $htid = $f->getSubfield('u')->getData();
              $ecron = $f->getSubfield('z');
              if ($ecron) {
                $htid .= " (" . $ecron->getData() . ")";
              }
              array_push($lines, $line . "https://hdl.handle.net/2027/$htid");
            }
            continue;
          }


          # if it's a control tag...

          if ($tag < 10 && preg_match('/^\d+$/', $tag)) {
            foreach ($this->ru->getRDAFields($m, $tag, true) as $cfield) {
              $data = $cfield->getData();
              if (isset($tuple[1])) {
                $start = $tuple[1] - 1;
                $length = $tuple[2];
                $data = substr($data, $start, $length);
              }
              $newline = $line . $data;
              if (!isset($alllines[$newline])) {
                array_push($lines, $newline);
                $alllines[$newline] = true;
              }
            }
            continue;
          }

          # Otherwise, data
          $join = isset($tuple[2]) ? $tuple[2] : '';
          $join = $join == 'SPACE' ? ' ' : $join;
          $join = $join == '~' ? '' : $join;

          $match = $tuple[1];
          $match = $match == '~' ? null : $match;

          $alwaysmatch = isset($match) ? false : true;

          $realtag = substr($tag, 0, 3);

          $ind1 = substr($tag, 3, 1);
          $ind2 = substr($tag, 4, 1);

          foreach ($this->ru->getRDAFields($m, $realtag, true) as $dfield) {
            if (!(preg_match("/$ind1/", $dfield->getIndicator(1)) &&
                    preg_match("/$ind2/", $dfield->getIndicator(2)))
            ) {
              continue;
            }
            $str = array();
            foreach ($dfield->getSubfields() as $sub) {
              // if ($alwaysmatch || strspn($sub->getCode(), $match)) {
              $code = $sub->getCode();
              if (!($alwaysmatch || preg_match("/$code/i", $match))) {
                continue;
              }
              $val = $sub->getData();
              if (preg_match('/^Mode of access:/i', $val)) {
                continue;
              }
              $val = preg_replace('/(\d)[.\s]+$/', '$1', $val);
              // Add it unless we've got a case mismatch (hence uppercase) and already have a value
              // if (!(!preg_match("/$code/", $match) && count($str))) {
              $str[$val] = true;
              // }
            }
            if (count($str)) {
              $data = implode($join, array_keys($str));
              $data = preg_replace('/[;:,\/\s+]+$/', '', $data);
              $data = preg_replace('/^\[c?(\d+)\]\s*$/', '$1', $data);
              $data = preg_replace('/^c(\d{4})[;\-\.]*$/', '$1', $data); // Fudge for dates

              $newline = $line . $data;
              $newlineclean = preg_replace('/\W$/', '', $newline);
              if (!isset($alllines[$newlineclean])) {
                array_push($lines, $newline);
                $alllines[$newlineclean] = true;
              }
            }
          }
        }
      }
    }
    return implode("\n", $lines);
  }

  function _buildformatMap($typemap) {
    $i = 0;
    $tmap = array();
    foreach ($typemap as $tm) {
      $tmap[$tm[0]] = array($i, $tm[1]);
      $i++;
    }
    $this->formatMap = $tmap;
  }

  function bestFormat($formats, $aleph = false) {
    $formats = is_array($formats) ? $formats : array($formats);
    $best = 1000; // impossibly high
    $current = null;
    foreach ($formats as $f) {
      if (isset($this->formatMap[$f]) && ($this->formatMap[$f][0] < $best)) {
        $best = $this->formatMap[$f][0];
        if ($aleph) {
          $current = $f;
        } else {
          $current = $this->formatMap[$f][1];
        }
      }
    }
    return isset($current) ? $current : 'GEN';
  }

  /**
   * Send temp records to Refworks.
   * Call this by pulling a call to this function in a separate window; this will
   * put stuff in a temporary holding cell in teh db and then redirect to refworks
   * with an appropriate URL
   *
   * */
  function refworksRedirect() {
    global $configArray;

    // First, dump the results to the database for a little while
    $hostIPsuffix = substr($_SERVER['SERVER_ADDR'], -7); # get (at least) the last two triples of the IP
    $uuid = uniqid($hostIPsuffix, true);
    $expires = time() + 600; // 10 minutes

    $authspecs = AuthSpecs::singleton('conf/authspecs.yaml');
    $dargs = $authspecs['DSessionDB'];
    $dbh = DBH::singleton($dargs);
    $sth = $dbh->prepare('insert into refworks_redirects values (?, ?, ?)');
    $sth->execute(array($uuid, $expires, serialize($this->results)));




    // Now send refworks a request.

    $rfSpec = yaml_parse_file('conf/refworks.yaml');

    if (isset($_REQUEST['campus'])) {
      $baseURL = $rfSpec['baseURLs'][$_REQUEST['campus']];
    } else {
      $baseURL = $rfSpec['defaultBaseURL'];
    }

    $rfargs = $rfSpec['urlArgs'];


    // The return URL is a pull back to here with the current args and the refworks method

    $rurl = $configArray['Site']['url'] . "/Search/SearchExport?method=rfFormat&rfref=$uuid";

    $rfargs['url'] = $rurl;
    $args = array();
    foreach ($rfargs as $k => $v) {
      $args[] = rawurlencode($k) . '=' . rawurlencode($v);
    }

    $url = $baseURL . implode('&', $args);
    // echo($url);
    // return;
    header('Location: ' . $url);
  }


  /**
   * Produce an Atom feed
   *
   * */
  function atom() {
    global $configArray;


    $feed = new UniversalFeedCreator();
    $feed->useCached(false);
    $feed->title = implode(' / ', $this->ss->searchtermsForDisplay());
    $feed->description = implode(", ", $this->ss->facetsForDisplay());
    $feed->link = $configArray['Site']['url'] . '/Search/Home?' . $this->ss->asURL();
    $feed->syndicationURL = $configArray['Site']['url'] . '/Search/Home?' . $this->ss->asURL() . '&method=atom';
//          $holdings = $this->rutils->getStatuses($this->results);


    foreach ($this->results['record'] as $r) {
      $desc = array();
      $marc = $this->ru->getMarcRecord($r);
      $entry = new FeedItem();
      $titles = $this->ru->getLongTitles($marc);
      $entry->title = array_shift($titles);
      $entry->link = $configArray['Site']['url'] . '/Record/' . $r['id'];
      $entry->descriptionHtmlSyndicated = true;

      $desc = $titles;
      if (isset($r['author'])) {
        if (!is_array($r['author'])) {
          $r['author'] = array($r['author']);
        }
        $entry->author = $r['author'][0];
        $entry->authorURL = $configArray['Site']['url'] . '/Search/Home?type=realauth&lookfor=' . rawurlencode($entry->author);
        $entry->description .= 'by ';
        $authors = array();
        foreach ($r['author'] as $auth) {
          $url = $configArray['Site']['url'] . '/Search/Home?type=realauth&lookfor=' . rawurlencode($auth);
          $authors[] = "<a href=\"$url\">$auth</a>";
        }

        // Add the authors
        if (count($authors) > 0) {
          $desc[] = 'by ' . implode(', ', $authors);
        }
      } else {
        $entry->author = "<No author>";
      }

      // Add the pubdate
      if (isset($r['publishDate'])) {
        $desc[] = 'Publication date: ' . $r['publishDate'][0];
      }


//              foreach ($holdings[$r['id']] as $h) {
//                $loc = $h['location'] . ': ';
//                $loc .= isset($h['callnumber'])? $h['callnumber'] . ' ' : '';
//                $loc .= isset($h['status'])? '(' . $h['status'] . ')' : '';
//                if (isset($configArray['Switches']['onlyHathiHoldings']) && $configArray['Switches']['onlyHathiHoldings']) {
//		  if (!preg_match('/HathiTrust/', $loc)) {
//		    continue;
//		  }
//            $desc[] = $loc;
//        }
//    }
      $entry->description = implode("\n<br/>", $desc);
      $feed->addItem($entry);
    }

    $feed->outputFeed("ATOM1.0");
  }

  function marcjson() {
    $marc = $this->ru->getMarcRecord($this->results['record'][0]);
    $l = $marc->getLeader();
    $h = array('type' => 'marc-hash', 'version' => array(1, 0), 'leader' => "$l");
    $fields = array();
    foreach ($marc->getFields() as $tag => $f) {
      if ($f->isControlField()) {
        $data = $f->getData();
        $fields[] = array($tag, "$data");
      } else {
        $ind1 = $f->getIndicator(1);
        $ind2 = $f->getIndicator(2);
        $newfield = array($tag, "$ind1", "$ind2");
        $sf = array();
        foreach ($f->getSubfields() as $code => $subdata) {
          $data = $subdata->getData();
          $sf[] = array($code, "$data");
        }
        $newfield[] = $sf;
        $fields[] = $newfield;
      }
    }
    $h['fields'] = $fields;
    header("Content-Type: application/json; charset=utf-8");
    echo $this->json_indent(json_encode($h));
  }

  /**
   * Indents a flat JSON string to make it more human-readable
   *
   * @param string $json The original JSON string to process
   * @return string Indented version of the original JSON string
   */
  public
  function json_indent($json) {

    $result = '';
    $pos = 0;
    $strLen = strlen($json);
    $indentStr = '  ';
    $newLine = "\n";

    for ($i = 0; $i <= $strLen; $i++) {

      // Grab the next character in the string
      $char = substr($json, $i, 1);

      // If this character is the end of an element,
      // output a new line and indent the next line
      if ($char == '}' || $char == ']') {
        $result .= $newLine;
        $pos--;
        for ($j = 0; $j < $pos; $j++) {
          $result .= $indentStr;
        }
      }

      // Add the character to the result string
      $result .= $char;

      // If the last character was the beginning of an element,
      // output a new line and indent the next line
      if ($char == ',' || $char == '{' || $char == '[') {
        $result .= $newLine;
        if ($char == '{' || $char == '[') {
          $pos++;
        }
        for ($j = 0; $j < $pos; $j++) {
          $result .= $indentStr;
        }
      }
    }

    return $result;
  }

}

?>
