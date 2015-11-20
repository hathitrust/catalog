<?php

$HT_COLLECTIONS =  eval(file_get_contents('../derived_data/ht_collections.php'));

header('Content-type: application/json; charset=UTF-8');
echo json_encode($HT_COLLECTIONS);
?>