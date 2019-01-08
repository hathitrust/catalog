<?php

require_once 'sys/DBH.php';
require_once 'sys/VFSession.php';

class ActivityLog 
{
  
  private $dbh;
  protected static $config;
  protected static $instance;
  private $sth;
  public $session;
  public static $donotlog = false;
  
  private function __construct($file) {
    self::$config = Horde_Yaml::load(file_get_contents($file));
    if (self::$config['style'] == 'db') {
      $this->dbh = DBH::singleton(self::$config['DBLogging']);
      $table = self::$config['DBLogging']['table'];
      $table = 'activities'; ##### HACK #####
      $this->sth = $this->dbh->prepare("insert into $table (sessionid, action, data1, data2, data3, data4, logday, logdate, logtime) values (?,?,?,?,?,?,?,?,?)");
    }
  }

  public static function singleton($file = false) {
    global $configArray;
    if (!$file) {
      $file = $configArray['Site']['local'] . '/conf/activitylog.yaml';
    }
    
    
    if (!isset(self::$instance)) {
      $c = __CLASS__;
      self::$instance = new $c($file);
    }
    return self::$instance;
  }
  
  public function log($action, $data1='', $data2='', $data3='', $data4='') {
    $session = VFSession::instance();
    
    $this->rawlog($session->uuid, $action, $data1, $data2, $data3, $data4);
  }
  
  public function rawlog($session, $action, $data1='', $data2='', $data3='', $data4='') {
   #### HACK FOR HATHITRUST ###
     return;

    if (self::$donotlog) {
      return;
    }
    $actionNum = self::$config['LCMap'][$action];
    if ($actionNum == 1) {
      return;
    }
    
    // Special case for elink -- parse out the URL
    
    if ($action == 'elink' || $action == 'recelink') {
      preg_match('/.*https?:\/\/(.*?)\//', $data2, $match);
      $machine = $match[1];
      $machinecomps = explode('.', $machine);
      array_pop($machinecomps); # .com, .edu, .org, .net
      $domain = array_pop($machinecomps); #company name?
      
      // If there are still at least two items, pop another one.
      if (count($machinecomps) > 1) {
        $sec = array_pop($machinecomps);
        $domain = "$sec.$domain";
      }
      if (isset($domain) && preg_match('/\S/', $domain)) {
        $data2 = $domain;
      }
    }
    
    $t = time();
    $logday = date('N', $t);
    $logdate = date('Ymd', $t);
    $logtime = date('His', $t);
    if (!$this->sth->execute(array($session, $actionNum, $data1, $data2, $data3, $data4, $logday, $logdate, $logtime))) {
      echo "WHOA!";
      print_r($this->sth->errorInfo() );
    }
  }
  
}
?>