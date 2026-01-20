<?php

set_include_path(get_include_path() . ':../..');
require_once 'vendor/autoload.php';
require_once 'Apache/Solr/Service.php';

// Set up for autoload
function sample_autoloader($class) {
  require str_replace('_', '/', $class) . '.php';
}
spl_autoload_register('sample_autoloader');

$solr = new Apache_Solr_Service('solr-sdr-catalog', '9033', '/solr/catalog');

$args = array();
$args['fl'] = 'fullrecord';

$id = $_REQUEST['id'];
$results = $solr->search("ht_id:\"$id\"", 0, 1, $args);

if ($results->response->numFound == 0) {
  header('HTTP/1.0 404 Not Found');
  header('Content-type: text/html; charset=UTF-8');
  echo "<h1>Not found</h1>";
  echo "'$id' is not a valid record identifier.";
  exit;
}

$doc = $results->response->docs[0];


header('Content-type: text/xml; charset=UTF-8');
echo $doc->fullrecord;
