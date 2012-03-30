<?php


class User
{
  public $username;                        // string(30)  not_null unique_key
  public $password;                        // string(32)  not_null
  public $firstname;                       // string(50)  not_null
  public $lastname;                        // string(50)  not_null
  public $email;                           // string(250)  not_null
  public $cat_username;                    // string(50)  
  public $cat_password;                    // string(50)  
  public $college;                         // string(100)  not_null
  public $major;                           // string(100)  not_null
  public $created;                         // datetime(19)  not_null binary
  public $ip;  // ip address from session
}


?>
