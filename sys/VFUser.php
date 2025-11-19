<?php

/*
 * Not used for HathiTrust.
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
    $sess = VFSession::instance();

  }
  
  
  public static function singleton()
  {
      return NULL;
  }
  
}                                    
?>
