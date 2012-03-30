<?php

require_once 'services/MyResearch/MyResearch.php';
require_once 'services/Tags/TaggedItemsDisplay.php';
require_once 'services/Tags/Tags.php';

class Favorites extends MyResearch 
{

  
  public $tid;
  public $extraTag;
  public $tags;
  
  function __construct() {
    global $interface;
    parent::__construct();
    $this->tid = new TaggedItemsDisplay;
    $this->tags = Tags::singleton();
    $interface->assign("favoritesPage", true);
  }




  function launch() {
    global $interface;
    global $configArray;
    
    $this->extraTag = isset($_REQUEST['tag'])? $_REQUEST['tag'] : false;
    
    if ($this->extraTag) {
      $this->tid->tag = $this->extraTag;
      $this->tid->tagdisplay = "<span class=\"favorites\">Favorites</span> tagged '" . $this->extraTag . "'";
      $this->tid->pageTitle = "Favorites tagged " . $this->extraTag;
    } else {
      $this->tid->pageTitle = "Favorites";
      $this->tid->tag = Tags::$favoriteTag;
      $this->tid->tagdisplay = "<span class=\"favorites\">Favorites</span>";
    }
    
    // $this->tid->searchDescription = "marked as " . $this->tid->tagdisplay;
    $this->tid->searchDescription = '';
    $this->tid->linkBase = '/MyResearch/Favorites';
    $numitems = $this->tags->numItems($this->tid->tag);

    if ($numitems == 0) {
      $interface->assign('tid', $this->tid);
      $interface->assign('tagobj', $this->tags);
      $interface->setTemplate('favorites-none.tpl'); // based on Search/list.tpl         
      $interface->display('layout.tpl');
      return;
    }

    $this->tid->doAllTheWork();
    $interface->assign('subpage', 'Search/list-list.tpl'); // Based on Search/list-list.tpl
    $interface->setTemplate('favorites.tpl'); // based on Search/list.tpl    
    $interface->display('layout.tpl');
  }

}




?>