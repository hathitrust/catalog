<?php

require_once 'sys/VFUser.php';
require_once 'sys/VFSession.php';
require_once 'sys/DBH.php';
require_once 'services/Search/SearchStructure.php';
require_once 'services/Tags/TaggedItem.php';
require_once 'services/Record/RecordList.php';
require_once 'services/Record/RecordUtils.php';
require_once 'HTTP/Request2.php';
require_once 'sys/Solr.php';

class Tags
{
  public static $favoriteTag = 'mirlyn-favorite';
  protected $items = array();
  protected $idsTaggedWith = array();
  protected $loggedIn = false;
  protected $dirty = array();
  public    $session;
  protected $user;
  private static $instance = false;
  protected $httpRequest; // An HTTP Request2 object
  protected $_solr;
  
  public function allItems() {
    return $this->items;
  }
  public function singleton() {
    if (!self::$instance) {
      $c = __CLASS__;
      self::$instance = new $c();
    }
    self::$instance->user = VFUser::singleton();
    return self::$instance;
  }

  
  protected function __construct() {
    $this->user    = VFUser::singleton();
    $this->session = VFSession::instance();
    
    // Are we logged in?
    if ($this->user) {
      $this->loggedIn = true;
    } else {
      $this->loggedIn = false;
    }
    
    # Do we already have stuff saved in the session?
    if ($this->session->is_set('tags')) {
      $stags = $this->session->get('tags');
      $this->items = $stags['items'];
      $this->idsTaggedWith = $stags['idsTaggedWith'];
    }
      
    // If we're logged in, we may have to pull from the database.
    // Do so if we have no stags (this is the first time here) or
    // if the user wasn't logged in last time we we here.
      
    if ($this->loggedIn && (!isset($stags) || $stags['loggedIn'] == false)) {
      $this->fillFromDB($this->user->username);
      // $this->fillFromDB('dueberb');
      $this->saveToSession();
    }
  }
  
  
  function solr() {
    global $configArray;
    if (!isset($this->_solr)) {
      $class = $configArray['Index']['engine'];
      $this->_solr = new $class($configArray['Index']['url']);      
    }
    return $this->_solr;
  }
  
  function req() {
    if (!isset($this->httpRequest)) {
      $req = new HTTP_Request2(null, array('useBrackets' => false));
      $req->setMethod(HTTP_REQUEST_METHOD_GET);
      $this->httpRequest = $req;
    }
    return $this->httpRequest;
  }
  

  function saveToSession() {
    $tags = array();
    $tags['items'] = $this->items;
    $tags['idsTaggedWith'] = $this->idsTaggedWith;
    $tags['loggedIn'] = $this->loggedIn;
    $this->session->set('tags', $tags);
  }


  //----------------------------------------------
  // Generic tagging

  function tag($id,  $tags, $desc="") {
    $this->tagLocally($id, $tags, $desc);
    $this->tagInDB($this->items[$id], $tags);
    $this->saveToSession();
  }
  
  function untag($id, $tags) {
    $this->removeDBTags($this->items[$id], $tags);
    $this->removeLocalTags($id,$tags);
    $this->saveToSession();
  }
  
  function delete($id) {
    if (!$this->items[$id]) {
      return;
    }
    $tags = $this->items[$id]->tags();
    $this->removeDBTags($this->items[$id], $tags);
    $this->removeLocalTags($id, $tags);
    $this->saveToSession();
  }


  //----------------------------------------------
  // Dealing with temp items ('Selected')

  function addToTemp ($id) {
    $this->tagLocally($id, $this->session->uuid);
  }

  function removeFromTemp($id) {
    $this->removeLocalTags($id, $this->session->uuid);
  }

  function clearTemp() {
    foreach ($this->tempIDs() as $id) {
      $this->removeFromTemp($id);
    }
    $this->saveToSession();
    
  }

  function numTempItems() {
    return $this->numItems($this->session->uuid);
  }
  
  function inTempItems($id) {
    if (isset($this->items[$id])) {
      $rv =  $this->items[$id]->hasTag($this->session->uuid);
      return $rv;
    } else {
      return false;
    }
  }
  
  function tempIDs() {
    return $this->idsWithTag($this->session->uuid);
  }

  function tempItems() {
    return $this->itemsTaggedWith($this->session->uuid);
  }


  //----------------------------------------------
  // Dealing with favorites  
  
  function addToFavorites($id) {
    $this->tag($id, self::$favoriteTag);
  }

  function addTempToFavorites($tags=array()) {
    $tags = arrayify($tags);
    $tags[] = self::$favoriteTag;
    // Get a list of those that are actually being favorited as we tag them
    $newFavorites = array();
    foreach ($this->tempIDs() as $id) {
      if (!$this->isFavorite($id)) {
        $newFavorites[] = $id;
      }
      $this->tag($id, $tags);
    }
    return $newFavorites;
  }
  
  function isFavorite($id) {
    return isset($this->items[$id]) && $this->items[$id]->hasTag(self::$favoriteTag);
  }
  
  function favoriteIDs() {
    return $this->idsTaggedWith(self::$favoriteTag);
  }
  
  function favoriteItems() {
    return $this->itemsTaggedWith(self::$favoriteTag);
  }
  
  function numFavoriteItems() {
    return $this->numItems(self::$favoriteTag);
  }
  
  function removeFromFavorites($id) {
    // remove all tags but uuid. If no tags, then delete.
    if (!isset($this->items[$id])) {
      return;
    }
    $tags = $this->items[$id]->tags();
    $this->removeDBTags($this->items[$id], $tags);
    $this->removeLocalTags($id, $tags, true); // true tells it to skip tempset
    $this->saveToSession();
    
    
  }
  
  function deleteAll() {
    foreach ($this->ids() as $id) {
      $this->delete($id);
    }
    $this->saveToSession(); 
    
  }

  //----------------------------------------------
  // Local Tagging (only within the session)

  function tagLocally($id, $tags, $desc="") {
    $tags = arrayify($tags);
    
    // Get the item (or a new one)
    
    if (!isset($this->items[$id])) {
      $this->massFetch($id);
      
      # Do we just plain not have it??? Deletion?
      if (!isset($this->items[$id])) {
        return;
      }
      $item = $this->items[$id];
      
      $this->items[$id]->description = $desc;
    }

    // Add the tags to it
    
    
    $this->items[$id]->tag($tags);
    
    
    // Add the item to the inverted index
    foreach ($tags as $tag) {
      if (!preg_match('/\S/', $tag)) {
        continue;
      }
      if (!isset($this->idsTaggedWith[$tag])) {
        $this->idsTaggedWith[$tag] = array();
      }
      $this->idsTaggedWith[$tag][$id] = $this->items[$id];
    }
    
    # Local, so we need to save to session
    $this->saveToSession();
  }


  function removeLocalTags($id, $tags, $skipTemp = false) {
    if (!isset($this->items[$id])) {
      return;
    }
    $tags = arrayify($tags);
    foreach ($tags as $tag) {
      if ($skipTemp && $tag == $this->session->uuid) {
        continue;
      }
      unset($this->idsTaggedWith[$tag][$id]);
      if (isset($this->idsTaggedWith[$tag]) && count($this->idsTaggedWith[$tag] == 0)) {
        // unset($this->idsTaggedWith[$tag]);
      }
      $this->items[$id]->untag($tag);
      if (count($this->items[$id]->tags()) == 0) {
        // unset($this->items[$id]);
      }
    }
    $this->saveToSession(); 
  }
  
  
  //----------------------------------------------
  // Talking to the database
  
  function talkToDB($op, $item, $tags) {
    $tags = arrayify($tags);
    $tagarray = array();
    foreach ($tags as $tag) {
      $tag = trim($tag);
      if (!preg_match('/\S/', $tag)) {
        continue;
      }
      $tagarray[] = $tag;
    }
    $taglist = implode(',',$tagarray);
    $req = $this->req();
    $req->setURL("http://www.lib.umich.edu/mtagger/tags/api/" . $op);
    $req->addQueryString('user', $this->user->username);
    $req->addQueryString('tags', $taglist);
    $req->addQueryString('u', $item->url(true)); // true makes it absolute

    // echo $req->getUrl(), "\n";
    if ($op == 'add') {
      $req->addQueryString('title', $item->title);
    }
    $req->sendRequest();
    // echo "TALK TO DB: " . op . " gave " . $req->getResponseBody();
  }
  
  function tagInDB($item, $tags) {
    $tags = arrayify($tags);
    if (isset($tags[$this->session->uuid])) {
      unset($tags[$this->session->uuid]);
    }
    if (count($tags) > 0) {
      $this->talkToDB('add', $item, $tags);
    }
  }
  
  function removeDBTags($item,$tags) {
    $this->talkToDB('remove', $item, $tags);
  }

  
  /**
    * Get tags and info from the tag db. We need to make sure we dont'
    * overwrite the existing stuff in the session, in case they log
    * in after already saving stuff.
  **/
  
  function fillFromDB($uname) {

    $req = new HTTP_Request2(null, array('useBrackets' => false));
    $req->setMethod(HTTP_REQUEST_METHOD_GET);
    $req->setURL("http://www.lib.umich.edu/mtagger/items/api/full");
    $req->addQueryString('user', $uname);
    $req->addQueryString('tags', self::$favoriteTag);
    // echo "URL: " . $req->getURL();
    
    if (PEAR::isError($req->sendRequest())) {
         return array();
    }
    // echo "REQUEST BODY: " .  $req->getResponseBody();
    
    $mtaggeritems = json_decode($req->getResponseBody(), true);
    $ids = array();
    $taglists = array();
    foreach ($mtaggeritems as $mti) {
      preg_match('/(\d{9})/', $mti['url'], $match);
      if (isset($match[1])) {
        $id = $match[1];
        $ids[] = $id;
        $taglist = array();
        foreach ($mti['tags'] as $mtit) {
          if (preg_match('/\S/', $mtit['name'])) {
            $taglist[] = $mtit['name'];
          }
        }
        $taglists[$id] = $taglist;
      } else {
      }
    }
    $this->massFetch($ids);
    foreach ($ids as $id) {
      $this->tagLocally($id, $taglists[$id]);
    }
  }

  
  //----------------------------------------------
  // Generic tag metadata
  
  function numItems($tags) {
    $tags = arrayify($tags);
    $count = 0;
    foreach ($tags as $tag) {
      if (isset($this->idsTaggedWith[$tag])) {
        $count += count(array_keys($this->idsTaggedWith[$tag]));
      }
    }
    return $count;
  }
  
  function idsTaggedWith($tags = array()) {
    $tags = arrayify($tags);
    $rv = array();
    if (count($tags) == 0) {
      return $rv;
    }
    foreach ($tags as $tag) {
      if (isset($this->idsTaggedWith[$tag])) {
        $rv = array_merge($rv, array_keys($this->idsTaggedWith[$tag]));
      }
    }
    sort($rv, SORT_NUMERIC);
    return $rv;
  }
  
  function item($id) {
    return $this->items[$id];
  }
  
  function itemsTaggedWith($tags=array()) {
    $tags = arrayify($tags);
    $rv = array();
    if (count($tags) == 0) {
      return $rv;
    }
    foreach ($tags as $tag) {
      if (isset($this->idsTaggedWith[$tag])) {
        $rv = array_merge($rv, array_values($this->idsTaggedWith[$tag]));
      }
    }
    return $rv;
    
  }




  function inSubset($id) {
    return isset($this->items[$id]) && $this->items[$id]->hasTag($this->session->uuid . '_subset');
  }


  
  /**
    * Fetch a bunch of items from the db if we don't already have them.
    * Used when pulling stuff down from the database
    * to tag them all as "mirlyn-favorite"
    * Will do nothing if we've already got all the passed IDs
  **/
  
  function massFetch($ids) {
    $ids = arrayify($ids);
    $needids = array();
    foreach ($ids as $id) {
      if (!isset($this->items[$id])) {
        $needids[] = $id;
      }
    }
    if (count($needids) == 0) {
      return;
    }
    
    $ss = new SearchStructure(true);
    $ss->addIDs($needids);
    
    $results = $this->solr()->simplesearch($ss, 0, 100); // limit to 1000 items
    $rutils = new RecordUtils;
    foreach ($results['record'] as $rec) {
      $id = $rec['id'];
      $marc = $rutils->getMarcRecord($rec);
      if (!$marc) {
        echo "Bad rec for id $id\n"; print_r($rec);
      }
      $titles = $rutils->getFullTitle($marc);
      $title = array_shift($titles);      
      $item = new TaggedItem($id, $title);
      $this->items[$id] = $item;
    }
  }
  
  function taglist() {
    $tl = array();
    foreach (array_keys($this->idsTaggedWith) as $tag) {
      if ($tag == $this->session->uuid || $tag == self::$favoriteTag || !preg_match('/\S/', $tag)) {
        continue;
      }
      $tl[] = $tag;
    }
    sort($tl);
    return $tl;
  }
  
  function tagsAndCounts() {
    $tc = array();
    foreach ($this->taglist() as $tag) {
      $tc[] = array('tag'=>$tag, 'count'=>$this->numItems($tag));
    }
    return $tc;
  }
  

  // Get a search structure based on the favorites
  
  function favorites_ss() {
    return $this->union_ss($this->session->uuid);
  }
  
  function ids() {
    $rv = array_keys($this->items);
    sort($rv, SORT_NUMERIC);
    return $rv;
  }
  
  function idsWithTag($tag) {
    if (!isset($this->idsTaggedWith[$tag])) {
      return array();
    } else {
      $rv = array_keys($this->idsTaggedWith[$tag]);
      sort($rv, SORT_NUMERIC);
      return $rv;
    }
  }
  
  function tagCount($tag) {
     // echo "Getting count for tag $tag, user " . $this->user->username . "\n";
     $ids = $this->idsWithTag($tag);
     return count($ids);    
  }
  
  function displayTagsForID($id) {
    return $this->items[$id]->displayTags();
  }
  
  function union_ss($tags) {
    $ss = new SearchStructure(true); // true param means it comes back empty
    $tags = arrayify($tags);
    foreach ($tags as $tag) {
      if (isset($this->idsTaggedWith[$tag])) {
        $ss->addIDs(array_keys($this->idsTaggedWith[$tag]));
      }
    }
    return $ss;
  }
  

  
}


?>
