<?php
require_once 'services/Tags/TaggedItem.php';
require_once 'services/Tags/Tags.php';
require_once 'sys/ActivityLog.php';


class TagInterface
{
  
  public $tags;
  public $alog;
  /**
    * launch() -- setup and dispatch
    *
    * Sets up the solr object, determines the method to run, and runs it. If the method is
    * invalid, return a 403 Forbidden
   */
       
  function launch()
  {
       $this->tags = Tags::singleton();
       $this->alog = ActivityLog::singleton();
       
       $method = isset($_REQUEST['method'])? $_REQUEST['method'] : null;
       if ($method && is_callable(array($this, $method))) {
           header("Content-Type: application/json; charset=utf-8");
           $this->$method();
       } else {
           header("HTTP/1.0 403 Forbidden");
           echo "Method $method not recognized.";
       }
       
  }
  
  
  function removeFromTemp() {
    if (!isset($_REQUEST['id'])) {
      return $this->returnError('400', "ID required for favorite");
    }
    $id = $_REQUEST['id'];
    if (is_array($id)) {
      return $this->returnError('400', "Only a single ID for favorite");
    }
    

    $this->tags->removeFromTemp($id);
    $this->alog->log('unselect', $id);
    echo json_encode(array('numItems' => $this->tags->numTempItems()));    
  }  
  
  
  function tagAsTemp() {
    if (!isset($_REQUEST['id'])) {
      return $this->returnError('400', "ID required for favorite");
    }
    $id = $_REQUEST['id'];
    if (is_array($id)) {
      return $this->returnError('400', "Only a single ID for favorite");
    }
    

    $this->tags->addToTemp($id);
    $this->alog->log('select', $id);
    echo json_encode(array('numItems' => $this->tags->numTempItems(), 'tag' =>$this->tags->session->uuid));
  }
  
  function tempCount() {
    echo json_encode(array('numItems' => $this->tags->numTempItems()));
  }
  
  function clearTemp() {
    $this->alog->log('clearselected', $this->tags->numTempItems());
    $this->tags->clearTemp();
    echo json_encode(array('numItems' => 0));
  }
  
  function tempIDs() {
    $ids = array();
    foreach ($this->tags->tempIDs() as $id) {
      $ids[$id] = true;
    }
    echo json_encode($ids);
  }

  function favoriteCount() {
    echo json_encode(array('favoriteCount' => $this->tags->tagCount('vufind-favorites')));
  }

  function tempToFavorites() {
    if (isset($_REQUEST['taglist'])) {
      $taglist = $_REQUEST['taglist'];
      $tags = preg_split('/\s*,\s*/', $taglist);
    } else {
      $tags = array();
    }
    $addedIDs = $this->tags->addTempToFavorites($tags);
    $this->alog->log('selectedtofavorites', count($addedIDs),  $this->tags->numTempItems() - count($addedIDs));
    echo json_encode(array('favoriteCount' => $this->tags->numFavoriteItems(), 
                           'attemptedFavorites' => $this->tags->tempIDs(),
                           'newFavorites' => $addedIDs));

  }

  function addToFavorites() {
    $ids = preg_split('/\s*,\s*/', $_REQUEST['ids'], -1, PREG_SPLIT_NO_EMPTY);
    $addedIDs = array();
    foreach ($ids as $id) {
      if (!$this->tags->isFavorite($id)) {
        $this->tags->addToFavorites($id);
        $addedIDs[] = $id;
      }
    }
    if (count($ids) == 1) {
      $this->alog->log('favorite', $ids[0]);
    }
    echo json_encode(array('favoriteCount' => $this->tags->numFavoriteItems(), 
                           'attemptedFavorites' => $ids,
                           'newFavorites' => $addedIDs));
  }
  
  function editFavorite() {
    $oldtags = isset($_REQUEST['oldtags']) && preg_match('/\S/', $_REQUEST['oldtags'])? 
               preg_split('/\s*,\s*/', $_REQUEST['oldtags']) : 
               array();
    $newtags = isset($_REQUEST['newtags']) && preg_match('/\S/', $_REQUEST['newtags'])? 
               preg_split('/\s*,\s*/',$_REQUEST['newtags']) : 
               array();
    
    $id = $_REQUEST['id'];
    $oldtagmap = array();
    foreach ($oldtags as $tag) {
      $oldtagmap[$tag] = true;
    }
    $removedTags = array();
    foreach ($this->tags->displayTagsForID($id) as $tag) {
      if (!isset($oldtagmap[$tag])) {
        $removedTags[] = $tag;
      }
    }
    $this->tags->untag($id, $removedTags);
    $this->tags->tag($id, $newtags);
    $this->alog->log('editfavorite', $id, count($newtags), count($removedTags));
    echo json_encode(array('tags' => array_merge($oldtags, $newtags)));
    
  }
  
  
  function tagsAndCountsSnippet() {
    global $interface;
    $interface->assign('tagobj', $this->tags);
    $interface->display('MyResearch/favorites_taglist.tpl');
  }
  
  function tagIDs() {
    $ids = preg_split('/\s*,\s*/', $_REQUEST['ids'], -1, PREG_SPLIT_NO_EMPTY);
    $tags = preg_split('/\s*,\s*/', $_REQUEST['tags']);
    foreach ($ids as $id) {
      $this->tags->tag($id, $tags);
    }
    $this->alog->log('addextratags', $id, count($tags));
    echo json_encode(array('ids'=>$ids, 'tags'=>$tags));
  }
  
  function untagIDs() {
    $ids = preg_split('/\s*,\s*/', $_REQUEST['ids'], -1, PREG_SPLIT_NO_EMPTY);
    $tags = preg_split('/\s*,\s*/', $taglist);
    foreach ($ids as $id) {
      $this->tags->untag($id, $tags);
    }
    echo json_encode(array('ids'=>$ids, 'tags'=>$tags));
  }
  
  
  
  function removeFromFavorites() {
    $ids = preg_split('/\s*,\s*/', $_REQUEST['ids'], -1, PREG_SPLIT_NO_EMPTY);
    $removedID = array();
    foreach ($ids as $id) {
      $this->tags->removeFromFavorites($id);
    }
    // echo json_encode(array('deletedIDs' => $ids, 'tags' => $this->tags));
    $this->alog->log('unfavorite', $ids[0]);
    echo json_encode(array('favoriteCount' => $this->tags->numFavoriteItems(),'deletedIDs' => $ids));
  }
  
  // for debugging only.
  function clearFavorites() {
    $this->tags->deleteAll();
    $this->tags->saveToSession();
    echo json_encode($this->tags);
  }

  function tagsAndCounts() {
    echo json_encode($this->tags->tagsAndCounts());
  }
}

?>