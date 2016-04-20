<?php

# Get the mapping from collection codes to display strings
# and save it as PHP


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

$host  =  'mysql-sdr';
$uname =  "vufind";
$pass  =  "notvillanova";
$db    =  "ht";

     try {
       $dbh = new PDO("mysql:host=$host;dbname=$db", $uname, $pass, array(
           // PDO::ATTR_PERSISTENT => true
       )); 
       $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
       print "Error!: " . $e->getMessage() . "\n$host / $db / $uname/ $pass\n\n";
       die();
     }


$sql_text = "
select lower(collection) as collection_code, 
       name as display_name 
from ht_institutions i 
join ht_collections c on c.original_from_inst_id = i.inst_id 
order by collection";

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


foreach ($dbh->query($sql_text) as $row) {
  echo "$row[0] => $row[1]";
}

exit;



# Bail if we didn't get it -- we'll just keep the last version
if (!$yamlmap) {
    exit;
}

# OK. So now we need to de-yaml it...

$rawmap = Horde_Yaml::load($yamlmap);

# Need them of the form code=>{original_from=>"Dispay Text"}

$map = array();
foreach ($rawmap as $code => $display) {
  $map[$code] = array('original_from' => $display);
}
$arr_text = 'return ' . var_export($map, true) . ';';

# ... and write a copy to each of the output files
foreach ($output_files as $filename) {
    file_put_contents($filename, $arr_text);
}


