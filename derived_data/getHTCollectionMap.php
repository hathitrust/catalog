<?php

# Get the collection map from the aleph machine (irene) and turn
# it into a php array.
#


##########
# admin/housekeeping
##########

# Set up the include path so we can find the Horde_Yaml library
$parent = dirname(__FILE__) . '/..';
set_include_path(get_include_path() . ':' . $parent);


// Set up for autoload
function sample_autoloader($class) {
    require str_replace('_', '/', $class) . '.php';
}
spl_autoload_register('sample_autoloader');


####################
# Configuration
####################

# Where is it?
$yaml_url = 'http://mirlyn-aleph.lib.umich.edu/hathitrust_collection_map.yaml';


# We need to stick a copy in each of the current directory
# and the orphans directory

$output_dirs = array();
$output_dirs[] = dirname(__FILE__);
$output_dirs[] = dirname(__FILE__) ."/orphans";


$output_files = array();
foreach ($output_dirs as $d) {
    $output_files[] = "$d/ht_collections.php";
}


##################
# Actual work
##################

# Get the file
$yamlmap = file_get_contents($yaml_url);

# Bail if we didn't get it -- we'll just keep the last version
if (!$yamlmap) {
    exit;
}

# OK. So now we need to de-yaml it...

$rawmap = Horde_Yaml::load($yamlmap);
$arr_text = 'return ' . var_export($rawmap, true) . ';';

# ... and write a copy to each of the output files
foreach ($output_files as $filename) {
    file_put_contents($filename, $arr_text);
}


