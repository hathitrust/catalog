<?php

$parent = dirname(__FILE__) . '/..';

set_include_path(get_include_path() . ':' . $parent);
echo get_include_path();


// Set up for autoload
function sample_autoloader($class) {
  require str_replace('_', '/', $class) . '.php';
}
spl_autoload_register('sample_autoloader');


$yamlmap = file_get_contents('http://mirlyn-aleph.lib.umich.edu/namespacemap.yaml');
$rawmap = Horde_Yaml::load($yamlmap);

echo "return array(";
foreach ($rawmap as $key => $valhash) {
  echo "\"$key\" => \"", $valhash['desc'], "\",\n";
}
echo ");\n";