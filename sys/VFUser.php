<?php

require_once 'sys/ActivityLog.php';
/*
  Create a specialized user class, since the ones that came with VUFind are a little scattered.
  We'll make it a singleton, returning false if the user is not logged in, getting the whole thing
  from the session if it's there, and pulling from Patron if need be (i.e., we just logged in with the
    external script for the first time);
*/

class VFUser
{
  public $username;
  public $password;
  public $cat_username;
  public $cat_password;
  public $ip;
  
  public $id;
  public $firstname;
  public $lastname;
  public $errorMsg;
  public $college;
  
  public $patron;
  
  protected static $instance;
  
  protected function __construct() 
  {
    global $configArray;
    $sess = VFSession::singleton();
    
    // echo "Calling construct\n";

  }
  
  
  public function singleton()
  {
      return NULL;
//    global $configArray;
//    $sess = VFSession::singleton();
//
//    // Get the user from (a) the instance, (b) the session, or (c) the constructor
//    // and make sure it ends up in self::$instance
//
//
//    $user = self::$instance;
//
//    if (!isset($user)) {
//      if ($sess->is_set('user')) {
//        $user = $sess->get('user');
//        // echo "Got from session<br>";
//      } else {
//        $c = __CLASS__;
//        $user = new $c();
//        $sess->set('user', $user);
//        // echo "Created new<br>";
//      }
//      self::$instance = $user;
//    }
//
//    // If we've got a patron, we're already logged in
//
//    if (isset($user, $user->patron)) {
//      // echo "Already logged in<br>";
//      return $user;
//    }
//
//    // If we don't have a uniqname, we're not logged in
//
//    if (!$sess->is_set('uniqname')) {
//      // echo "Not logged in<br>";
//      return NULL;
//    }
//
//    // Otherwise, we're logged in and need patron data.
//
//    // echo "Getting login information<br>";
//    $uq = $sess->get('uniqname');
//    $catalog = new CatalogConnection($configArray['Catalog']['driver']);
//    $user->username = $uq;
//    $user->password = $uq;
//    $user->cat_username = $uq;
//    $user->cat_password = $uq;
//    $user->ip = $sess->get('ip');
//    $user->patron = $catalog->patronLogin($uq, $uq);
//
//    $alog = ActivityLog::singleton();
//    if (isset($user->patron)) {
//      $alog->log('login', $uq, $user->patron->campus, $user->patron->bor_status);
//    } else {
//      $alog->log('login', $uq, 'NPR');
//    }
//
//    return $user;
  }
  
}                                    
?>