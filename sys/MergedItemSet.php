<?php

require_once 'vendor/autoload.php';
#require_once 'Apache/Solr/Service.php';
require_once 'sys/SolrConnection.php';

$path = explode('/', __FILE__);
array_pop($path);
require_once(implode('/', $path) . '/QObj.php');
require_once(implode('/', $path) . '/Normalize.php');
require_once 'sys/Normalize.php';

class MergedItemSet
{
  public $qobjs = array();
  public $qstrings = array();
  public $solrQueryComponents = array();
  private $_data;
  public $commonargs = array(
   //'fl' => 'score,id,ht_json,title,year,publishDate,oclc,lccn,isbn,issn'
   'fl' => "*"
  );
  public $docs = array();
  public $options = array();
  public $solr;
  
  function __construct($qst = array(), $options = array()) {
    
#    $this->solr = new Apache_Solr_Service('solr-sdr-catalog', '9033', '/catalog');
    $this->options = array_merge($this->options, $options);
    
    $qst = is_array($qst)? $qst : array($qst);
    foreach ($qst as $qs) {
      $this->addQueryString($qs);
    }
  }
  
  function addQueryString($qs) {
    $this->qstrings[] = $qs;
    $nqo = new QObj($qs);
    $this->solrQueryComponents = array_merge($this->solrQueryComponents, $nqo->qspecs);
    $this->qobjs[$qs] = $nqo;    
  }
  
  function solrQuery() {
    return join(" OR ", $this->solrQueryComponents);
  }
  
  function fetch() {
    $solr = new SolrConnection();

    $args = $this->commonargs;
    $args['q'] = $this->solrQuery();

    foreach ($args as $key => $value) {
      $solr->add([[$key, $value]]);
     }

    try {
      $results = $solr->send_for_obj();
      
    } catch (Exception $e) {
      header('Location: https://www.hathitrust.org/temporary-outage');
      die();
    }
      

    // Index the docs
    foreach ($results->response->docs as $doc) {
      $this->docs[$doc->id] = $doc;
    }
    
    // Find the matches
    $matches = array();
    $matchingdocs = array();
    $allmatches = array();


    foreach ($this->qobjs as $qobj) {
      $qobj->setDocMatches($this->docs);
      $id = $qobj->id();
      $allmatches[$id] = array();
      // Get the record
      $records = $qobj->recordsStructure($this->docs);
      $items   = $qobj->itemsStructure($this->docs);
      $htj     = $qobj->ht_jsons($this->docs);
      if (count($items) > 1) {
        usort($items,  array('MergedItemSet','enumsort'));
      }
      $allmatches[$id]['records'] = $records;
      $allmatches[$id]['items'] = $items;
      $allmatches[$id]['htjson'] = $htj;
    }
    
    
    $this->_data = $allmatches;
  }


  function allrecords() {
    $allrecords = array();
    foreach ($this->data() as $id => $recs) {
      $allrecords[] = $recs;
    }
    return $allrecords;
  }

  function combined_ht_json() {
    $htjsons = array();
    foreach ($this->data() as $r) {
      $htjsons = array_merge($htjsons, $r['htjson']);
    }
    return $htjsons;
  }

  function data() {
    if (isset($this->_data)) {
      return $this->_data;
    } else {
      $this->fetch();
      return $this->_data;
    }
  }  
  
  function firstRecordID() {
    foreach ($this->qobjs as $qobj) {
      if (count($qobj->matches)) {
        return $qobj->matches[0];
      }
    }
    return -1;
  }
  
  public $memoize = array();
  function sortstringFromEnumcron($str) {
    if (isset($this->memoize[$str])) {
      return $this->memoize[$str];
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
    $this->memoize[$str] = $rv;
    return $rv;
  }
  
  public function enumsort($a, $b) {
    $sa = $this->sortstringFromEnumcron($a['enumcron']);
    $sb = $this->sortstringFromEnumcron($b['enumcron']);
    if ($sa == $sb) {
        return 0;
    }
    return ($sa < $sb) ? -1 : 1;
  }
  
  
  
}


// $m = new MergedItemSet(array('oclc:59243'));
// print_r($m->data());
// 
// echo "First match is ", $m->firstRecordID(), "\n";

?>