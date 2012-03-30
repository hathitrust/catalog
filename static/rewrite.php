<?php
$referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "(no referer)";
error_log("mirlyn-classic redirect: " . $_SERVER['REQUEST_URI'] . " |  $referer | " . $_SERVER['REMOTE_ADDR']);

if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'direct') {
  header('Location: /Record/' . $_REQUEST['doc_number'], true, 301);
 } else {
  header('Location: http://mirlyn-classic.lib.umich.edu' . $_SERVER['REQUEST_URI']);
 }
?>