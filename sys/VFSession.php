<?php

require_once 'sys/DSession.php';
require_once 'sys/AuthSpecs.php';
require_once 'sys/ActivityLog.php';


class VFSession extends DSession
{
  static private $instance;
   
  function __construct($cookiename, $cargs, $dbh) {
    // Get an activity log just to keep things quiet
    
    
    
    parent::__construct($cookiename, $cargs, $dargs);
  }
  
  function singleton($confdir = false)
  {
    if (!$confdir) {
      preg_match('/(.*)\/sys.*/', __FILE__, $match);
      $rootdir = $match[1];
      $confdir = $rootdir . '/conf';
    }
    
    
    if (!isset(self::$instance)) {
      $authspecs = AuthSpecs::singleton($confdir . '/authspecs.yaml');
      $alog = ActivityLog::singleton("$confdir/activitylog.yaml");
      $cargs = $authspecs['DSessionCookie'];
      $dargs = $authspecs['DSessionDB'];
      $cookiename = $cargs['cookiename'];
      self::$instance = parent::singleton($cookiename, $cargs, $dargs);
    }    
    return self::$instance;    
  }
  
}


?>