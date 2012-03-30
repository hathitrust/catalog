<?php

function arrayify($item) {
  if (!is_array($item)) {
    return array($item);
  } else {
    return $item;
  }
}

require_once 'sys/VFSession.php';
require_once 'services/Tags/Tags.php';

class TaggedItem 
{
  public $id;
  public $title;
  public $description;
  protected $_tags;
  
  function __construct($id, $title="", $description="", $tags=array()) {
    $this->id = $id;
    $this->title = $title;
    $this->description = $description;
    
    $tags = arrayify($tags);
    
    foreach ($tags as $tag) {
      $this->_tags[$tag] = true;
    }
  }
  
  function numTags(){
    return count($this->tags());
  }
  
  function tags() {
    return array_keys($this->_tags);
  }
  
  // all the tags except for uuid and mirlyn-favorites
  function displayTags() {
    $session = VFSession::singleton();
    $tags = array();
    foreach ($this->tags() as $tag) {
      if ($tag != $session->uuid && $tag != Tags::$favoriteTag && preg_match('/\S/', $tag)) {
        $tags[] = $tag;
      }
    }
    sort($tags, SORT_STRING);
    return $tags;
    
  }
  
  function hasTag($tag) {
    return isset($this->_tags[$tag]);
  }
  
  function tag($tags) {
    $tags = arrayify($tags);
    
    foreach ($tags as $tag) {
      if (!preg_match('/\S/', $tag)) {
        continue;
      }      
      $this->_tags[$tag] = true;
    }
  }
  
  function untag($tags) {
    $tags = arrayify($tags);
    
    foreach ($tags as $tag) {
      if (isset($this->_tags[$tag])) {
        unset($this->_tags[$tag]);
      }
    }
    
  }
  
  /**
    * Build a url from the id. 
    * @param boolean absolute Return the full path if true
    *
  **/
  
  function url($absolute=false) {
    global $configArray;
    if ($absolute) {
      $url = $configArray['Site']['url'] . '/Record/' . $this->id;
    } else {
      $url = '/Record/' . $this->id;      
    }
    return $url;
  }
  
  // Return all the tags EXCEPT the temptag id'd by the session
  // and the subset id'd by the keyword $uuid_subset
  function permanentTags() {
    if (count(array_keys($this->_tags)) == 0) {
      return null;
    }
    $session = VFSession::singleton();
    $t = array();
    foreach ($this->tags() as $tag) {
      if ($tag != $session->uuid && $tag != $session->uuid . '_subset') {
        $t[] = $tag;     
      }
    }
    return array_sort($t);
  }
}
?>