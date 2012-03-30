<?php

require_once 'services/MyResearch/MyResearch.php';

class Patron extends MyResearch
{
  function __construct() {
    parent::__construct();
  }
  
  
  function login($name, $pass) {
    // global $user;
    // $name = $user->cat_username;
    // $pass = $user->cat_password;
    error_log("About to call catalog->patronLogin");
   return $this->catalog->patronLogin($name, $pass);
  }
  
}
?>