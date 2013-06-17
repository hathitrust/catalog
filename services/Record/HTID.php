<?php
  
/**
  * Figure out the Record ID from an HTID and do a redirect
**/

require_once 'PEAR.php';
require_once 'Apache/Solr/Service.php';


class HTID
{
  
  function launch() {

    $solr = new Apache_Solr_Service('solr-sdr-catalog', '9033', '/catalog');
    $htid = $_REQUEST['htid'];
    $args = array('fl' => 'id');
    $results = $solr->search("ht_id:\"$htid\"", 0, 1, $args);


    if ($results->response->numFound == 0) {
      header('HTTP/1.0 404 Not Found');
      header('Content-type: text/html; charset=UTF-8');
      echo "<h1>Not found</h1>";
      echo "'$id' is not a valid record identifier.";
      exit;
    }

    $doc = $results->response->docs[0];
    $id = $doc->id;
    
      
    header("Location: /Record/$id", true, '301 Moved Permanently');
    

  }  
  
  
  
}
  
  
  
  
  
  
  
?>