<?php

require_once 'sys/DSession.php';
require_once 'sys/AuthSpecs.php';


class VFSession extends DSession
{
  static private $instance;
   
  function __construct($cookiename, $cargs, $dbh) {
    // Get an activity log just to keep things quiet
    
    
    
    parent::__construct($cookiename, $cargs, $dargs);
  }

// Change for PHP7 based on following:
// Warning: Declaration of VFSession::singleton($confdir = false) should be compatible
// with DSession::singleton($cookiename, $cookieargs = false, $dargs = false) in
// /htapps/test.catalog/web/sys/VFSession.php on line 8
// Parse error: syntax error, unexpected 'new' (T_NEW) in /usr/share/php/HTTP/Request.php on line 412
//
// Create a new method, 'instance', out of current 'session', and use it to grab the config and then
// call the DSession#singleton method.
  
  static function instance($confdir = false)
  {
    if (!$confdir) {
      preg_match('/(.*)\/sys.*/', __FILE__, $match);
      $rootdir = $match[1];
      $confdir = $rootdir . '/conf';
    }
    
    
    if (!isset(self::$instance)) {
      $authspecs = AuthSpecs::singleton($confdir . '/authspecs.yaml');
      $cargs = $authspecs['DSessionCookie'];
      $dargs = $authspecs['DSessionDB'];
      $cookiename = $cargs['cookiename'];
      self::$instance = self::singleton($cookiename, $cargs, $dargs);
    }    
    return self::$instance;    
  }
  
}


?>
