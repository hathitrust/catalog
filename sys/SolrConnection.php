<?php

require_once 'HTTP/Request2.php';

class SolrConnection
{

  public $client;
  public $request;
  public $base_url;
  public $url;
  public $method;

  // I want to memoize this in a class variable, but I can't set it with a
  // runtime expression. So I'm cheating by getting an array and only ever
  // using the first element of it.
  public static $base_url_container = [];

  /**
   * SolrRequest constructor.
   * @param array $args optional key=>value or key=>[v1, v2, ...] array
   *                    with post parameters.
   * @param string $handler the name of the solr requestHandler
   */
  function __construct($args = [], $method = "POST", $handler = "select") {
    self::$base_url_container[0] = isset(self::$base_url_container[0]) ? self::$base_url_container[0] :  self::solr_url();
    $this->base_url = self::$base_url_container[0];
    $this->url = $this->base_url . $handler;

    $this->method = $method;

    $this->setup_basic_request_object($this->url);
    $this->add($args);
  }

  // There are different mechanisms for adding data to GET and POST
  // requests, so I guess I'll have to switch on them.
  public function add($args) {
    foreach ($args as $kvpair) {
      $key = $kvpair[0];
      $value = $kvpair[1];
      $vstr = is_array($value) ? "[" . join(", ", $value) . "]" : $value;
      $this->request->addPostParameter(array($key => $value));
    }
  }

  public function _send($args = []) {
    $this->add($args);
    $resp = $this->request->send();

    #TODO: do this better
    if ($resp->getStatus() > 200) {
      $msg = $resp->getBody();
      throw new Exception("Problem talking to Solr: $msg");
    }
    else {
      $body = $resp->getBody();
      $this->setup_basic_request_object();
      return $body;
    }
  }


  public function send($args = []) {
    $raw = $this->_send($args);
    return json_decode($raw, true);
  }

  // TODO: Detete it - Nothing uses it
  public function send_for_obj($args = []) {
    $raw = $this->_send($args);
    return json_decode($raw, false);
  }



  public static function solr_url() {
    $solrstuff = parse_ini_file("conf/solrURL.ini");
    return $solrstuff["full_url"];
  }

  public function setup_basic_request_object($url = null) {
    $url = isset($url) ? $url :  $this->url;
    $this->request = new HTTP_Request2($url, HTTP_Request2::METHOD_POST, array('use_brackets' => false));
    $this->request->setHeader('Content-Type', 'application/x-www-form-urlencoded');
    $this->add([['wt', 'json'], ['json.nl', 'arrarr']]);
  }

}

?>
