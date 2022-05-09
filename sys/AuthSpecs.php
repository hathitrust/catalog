<?php

class AuthSpecs 
{
  private static $instance = false;
  private static $data;
  
  private function __construct($file) {
    self::$data = yaml_parse_file($file);
  }
  
  public function singleton($file = 'config/authspecs.yaml') {
    if (!self::$instance) {
      $c = __CLASS__;
      self::$instance = new $c($file);
    }
    return self::$data;
  }
  
}


?>