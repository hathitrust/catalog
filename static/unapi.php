<?php

$id = isset($_REQUEST['id'])? $_REQUEST['id'] : false;
$format = isset($_REQUEST['format'])? $_REQUEST['format'] : false;

if (!($id || $format)) {
  header('Content-type: application/xml');
  echo '<?xml version="1.0" encoding="UTF-8"?>
  <formats>
  <format name="ris" type="application/x-Research-Info-Systems" docs="http://www.refman.com/support/risformat_intro.asp"/>
  </formats>
  ';
exit;  
}



if ($id && !$format) {
  header('Content-type: application/xml');
  echo '<?xml version="1.0" encoding="UTF-8"?>
  <formats id="' . $id . '">
  <format name="ris" type="application/x-Research-Info-Systems" docs="http://www.refman.com/support/risformat_intro.asp"/>
  </formats>
  ';  
exit;  
}


// Otherwise...

preg_match('/^urn:(.*?):(.*)$/', $id, $match);
$type = $match[1];
$id = $match[2];

if ($type == 'bibnum') {
  $type="handpicked";
}

if ($format == 'ris') {
  $format = 'zoteroRIS';
}
header("Location: /Search/SearchExport?$type=$id&method=$format&donotlog=1", true, 302);


?>