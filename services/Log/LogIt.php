<?php

require_once('sys/ActivityLog.php');


class LogIt
{

  function launch() {
    
  
    $alog = ActivityLog::singleton();

    //######################################
    // Do default logging based on $_REQUEST
    //######################################

    if (isset($_REQUEST['lc'])) {
      $d1 = isset($_REQUEST['lv1'])? $_REQUEST['lv1'] : '';
      $d2 = isset($_REQUEST['lv2'])? $_REQUEST['lv2'] : '';
      $d3 = isset($_REQUEST['lv3'])? $_REQUEST['lv3'] : '';
      $d4 = isset($_REQUEST['lv4'])? $_REQUEST['lv4'] : '';
      $alog->log($_REQUEST['lc'], $d1, $d2, $d3, $d4);
    }
  }

}

?>