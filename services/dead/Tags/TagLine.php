<?php

class TagLine {
  
  static function initialize() {
    global $interface;
    
    $tags = Tags::singleton();
    $session = VFSession::instance();
    $interface->assign('tempcount', $tags->numTempItems());
    $interface->assign('uuid', $session->uuid);
  }
  
}



?>