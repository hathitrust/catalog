<?php
  
/**
  * Figure out the Record ID from an HTID and do a redirect
**/

require_once 'PEAR.php';
require_once 'Apache/Solr/Service.php';
require_once 'sys/SolrConnection.php';

class HTID
{
  
  function launch() {
    $solr = new SolrConnection();
    $htid = $_REQUEST['htid'];
    $args = array('fl' => 'id');
    $solr->add([['q', "ht_id:$htid"]]);
    $results = $solr->send_for_obj();


    if ($results->response->numFound == 0) {
      header('HTTP/1.0 404 Not Found');
      header('Content-type: text/html; charset=UTF-8');
      echo "<h1>Not found</h1>";
      echo "'$id' is not a valid record identifier.";
      exit;
    }

    $doc = $results->response->docs[0];
    $id = $doc->id;

    if (isset($_REQUEST['format'])) {
      $id = $id . '.' . $_REQUEST['format'];
    }
    
      
    header("Location: /Record/$id", true, '301 Moved Permanently');
    

  }  
  
  
  
}
  
  
  
  
  
  
  
?>