<?php

require_once 'PEAR.php';
require_once 'Apache/Solr/Service.php';

// Set up for autoload
function sample_autoloader($class) {
  require str_replace('_', '/', $class) . '.php';
}
spl_autoload_register('sample_autoloader');

$solr = new Apache_Solr_Service('solr-vufind', '8026', '/solr/biblio');

$args = array();
$args['fl'] = 'fullrecord';

$id = $_REQUEST['id'];
$results = $solr->search("id:$id", 0, 1, $args);
$doc = $results->response->docs[0];
header('Content-type: text/xml; charset=UTF-8');
echo $doc->fullrecord;
