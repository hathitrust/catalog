<?php
require_once "sys/DBH.php";

class DSession
{
  private static $instance;
  private static $dbh;
  private static $cookiename;
  public $insertsth;
  public $updatesth;
  public $selectsth;
  public $deletesth;
  public $cargs;
  public $uuid = false;
  public $isnew = true;
  public $data = array();

  protected static function singleton($cookiename, $cookieargs = false, $dargs=false) 
  {
    if (!isset(self::$instance)) {
      if (isset($dargs['dbh'])) {
        $dbh = $dargs['dbh'];
      } else {
        if (!$dargs) {
          error_log("Need initial call to DSession to include cookiename, cookieargs and dbargs!");
          die();
        }
        $dbh  =  DBH::singleton($dargs);
      }
      $c = __CLASS__;
      if (!$cookieargs || !$dargs) {
        error_log("Need initial call to DSession to include cookiename, cookieargs and dbargs!");
        die();
      }
      self::$instance = new $c($cookiename, $cookieargs, $dbh, $dargs['table']);
    }
    return self::$instance;
  }

  private function __construct($cookiename, $cargs, $dbh, $table) {
    self::$dbh = $dbh;    
    $this->cargs = $cargs;
    
    self::$cookiename = $cookiename;
    
    // set up inserts and deletes and updates and such
    $this->insertsth = $dbh->prepare("insert into $table (id, cookie, expires, data) values (?,?, ?, ?)");
    $this->updatesth = $dbh->prepare("update $table set data=?, expires=? where id=? and cookie=?");
    $this->selectsth = $dbh->prepare("select data from $table where id = ? and  expires > ? and cookie=?");
    $this->deletesth = $dbh->prepare("delete from $table where id=?");

    $this->uuid = isset($_COOKIE[$cookiename])? $_COOKIE[$cookiename] : false;
    
    // If we got a uuid and it's not expired, get the data. Otherwise, blank out $uuid so we generate a new one.
    if ($this->uuid) {
      if($this->selectsth->execute(array($this->uuid, time(), $cookiename))) {;
        $dataarr = $this->selectsth->fetch(PDO::FETCH_NUM);
        if ($dataarr) {
          $this->data = unserialize($dataarr[0]);
          $this->isnew = false;
        } else {
          $this->isnew = true;
        }
      } else { // it's expired
        $this->isnew = true;
      }
    } 

    // If it's new (or just expired; same thing) set the cookie and add blank data to the db
    if ($this->isnew) {
      $hostIPsuffix = substr($_SERVER['SERVER_ADDR'], -7); # get (at least) the last two triples of the IP
      $this->uuid = uniqid($hostIPsuffix, true);
      setcookie($cookiename, $this->uuid, 0, $cargs['path'], $cargs['domain'], false, true);
      $expires = time() + $this->cargs['expires_in_seconds'];    
      $this->insertsth->execute(array($this->uuid, $cookiename, $expires, serialize(array())));
      
      $ip = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : '';
      $referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '';
      $url = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI'] : '';
      
      # Is it a feed?
      
      if (preg_match('/method=atom/', $url)) {
        $action = 'atomfeed';
      } else {
        $action = 'initiate';
      }
      
    } 
        
  } // end of __construct
  
  public function delete($var) {
    if (isset($this->data[$var])) {
      unset ($this->data[$var]);
    }
  }
  
  public function is_set($var) {
    return isset($this->data[$var]);
  }
  
  public function set($var, $val) {
    $this->data[$var] = $val;
  }

  public function get($var) {
    if (isset($this->data[$var])) {
      return $this->data[$var];      
    } else {
      return NULL;
    }
  }

  public function write() {
    $sdata = serialize($this->data);
    $expires = time() + $this->cargs['expires_in_seconds'];
    $this->updatesth->execute(array($sdata, $expires, $this->uuid, self::$cookiename));
  }
  
  public function kill() {
    // delete cookie vb
    setcookie(self::$cookiename, false, time() - 3600, $this->cargs['path'], $this->cargs['domain'], false, true);
   // echo "Set " . self::$cookiename . " to a time in the past";
    // delete data
    $this->deletesth->execute(array($this->uuid));
    $this->data = array();
    $this->uuid = false;

  }


  public function __destruct() {
    $this->write();
  }
  
}

?>
