<?php

class DBH {
   private static $instance;
   private static $dbh;
   
   private function __construct($dargs)
   {
     if (isset($dargs['dbh'])) {
       self::$dbh = $dargs['dbh'];
       return;
     }
     
     // Otherwise, build it
     $host = $dargs['host'];
     $db = $dargs['db'];
     $uname = $dargs['username'];
     $pass = $dargs['password'];
     
     try {
       self::$dbh = new PDO("mysql:host=$host;dbname=$db", $uname, $pass, array(
           PDO::ATTR_PERSISTENT => true
       )); 
     } catch (PDOException $e) {
       print "Error!: " . $e->getMessage() . "<br/> $host / $db / $uname/ $pass";
       die();
     }
   }

  public static function singleton($dargs) 
  {
    if (!isset(self::$instance)) {
      $c = __CLASS__;
      self::$instance = new $c($dargs);
    }
    return self::$dbh;
  }  

}


?>