<?php

$server = preg_replace('/\.lib\.umich\.edu/', '', $_SERVER['SERVER_NAME']);

$year = $_GET['year'];
$month = sprintf("%02d", $_GET['month']);
$day = sprintf("%02d", $_GET['day']);

$count = 0;
$advanced = 0;



$log = implode('', array('/l/local/apache/logs/', $server, '/access_log-', $year, $month, $day));

$handle = fopen($log, 'r');
if (!$handle) {
  exit;
 }



while (!feof($handle)) {
  $line = fgets($handle, 4096);
  if (!preg_match('/GET \/Search/', $line)) {
    continue;
  }

  // Eliminate Bill and Tim
  if (preg_match('/141\.211\.43\.237/', $line) || preg_match('/141\.211\.43\.192/', $line)) {
     continue;
   }

  $comps = preg_split('/"/', $line);
  $url = $comps[1];


  // Only count searches with actual search terms, and only the initial search (noticed via checkspelling)
  if ((!preg_match('/lookfor/', $url)) || (!preg_match('/checkspelling/', $url))) {
    continue;
  }

  $count++;
  if (preg_match('/Advanced/', $url)) {
    $advanced++;
  }
}

echo implode("\t", array($year, $month, $day, $count - $advanced, $advanced)), "\n";

?>