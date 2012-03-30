<?php

require_once 'Pager/Pager.php';
require_once 'CatalogConnection.php';
require_once 'services/Record/FilterFormat.php';
require_once 'services/Search/SearchStructure.php';
require_once 'sys/VFSession.php';
require_once 'sys/VFUser.php';
require_once "feedcreator/include/feedcreator.class.php";
require_once 'File/MARC.php';
require_once 'services/Tags/Tags.php';
require_once 'services/Tags/TagLine.php';
require_once 'services/Record/RecordUtils.php';

/**
  * A more-or-less abstract class for dealing with sets of tagged items. Should be overridden.
  *
**/

class TaggedItemsDisplay
{
  
  protected $ss; // SearchStructure
  protected $solr; // Solr object
  protected $tags;
  protected $session;
  protected $user;
  protected $rutils;

  public $tag; // for selected items, the UUID. Otherwise the tag
  public $tagdisplay;
  public $linkBase;
  public $searchDescription = "The search description";
  public $pageTitle = 'Selected Items';
  
  function __construct() {
    global $configArray;
    $this->tags = Tags::singleton();
    $this->session = VFSession::singleton();
    $this->user = VFUser::singleton();
    $class = $configArray['Index']['engine'];
    $this->solr = new $class($configArray['Index']['url']);
    $this->tag = $this->session->uuid;
    $this->tagdisplay = "Selected Items";
    $this->linkBase = '/Tags/'. get_class($this);
    $this->searchDescription = "in the Selected Items set";
    $this->urlargs = array();
    $this->rutils = new RecordUtils;
    $this->pageTitle = "Selected Items";
    
    if (isset($_REQUEST['tag'])) {
      $this->urlargs[] = array('tag', $_REQUEST['tag']);
    }
  }
  
  
  
  function launch() {
    global $interface;
    global $configArray;
    $numitems = $this->tags->numItems($this->tag);
 
    if ($numitems == 0) {
      $interface->setPageTitle($this->pageTitle);
      $interface->assign('tid', $this);
      $interface->setTemplate('list-none.tpl');
      $interface->display('layout.tpl');
      return;
    }

    $this->doAllTheWork();
    $interface->assign('subpage', 'Search/list-list.tpl'); // Based on Search/list-list.tpl
    $interface->setTemplate('list.tpl'); // based on Search/list.tpl    
    $interface->display('layout.tpl');
  }
  
  
  function doAllTheWork() {
    global $interface;
    global $configArray;
 
    $numitems = $this->tags->numItems($this->tag);
    $interface->setPageTitle($this->pageTitle);
    $interface->assign('tid', $this);
    $interface->assign('tagobj', $this->tags);
    $interface->assign('tempcount', $this->tags->numTempItems());
    $this->ss = new SearchStructure();
    $this->ss->addTag($this->tag);
    
    # Set the sort
    if (isset($_REQUEST['sort'])) {
        $this->ss->sort  = $_REQUEST['sort'];
    }
    
    // Figure out the pagination.
    
    $start = 0;
    if (isset($_REQUEST['page']) && $_REQUEST['page'] > 0) {
      $start = ($_REQUEST['page'] - 1) * $configArray['Site']['itemsPerPage'];
    }
    
    $results = $this->solr->simplesearch($this->ss, $start);
    
     // This should be pulled out into...I don't know. Maybe _process in Solr? 
    
    foreach ($results['record'] as $num => $record) {
      $id_list[] = $record['id'];
      $marcRecord = $this->rutils->getMarcRecord($record);
      $results['record'][$num]['marc'] = $marcRecord;
      $results['record'][$num]['title'] = $this->rutils->getFullTitle($marcRecord);
      $results['record'][$num]['googleLinks'] = implode(",", $this->rutils->getLinkNums($marcRecord));
    }

    $interface->assign('recordSet', $results['record']);
    
    $interface->assign('url', $this->url());
    $interface->assign('urlbase', $this->url(array(), true));
    
    $this->session->set('lastsearch', 'tags');
    $this->session->set('lasttagsearch', array('url' => $_SERVER["REQUEST_URI"], 'description' => $this->tagdisplay));
    
    $interface->assign('tempcount', $this->tags->numTempItems());
    
    //Pagination
    $paginatorOptions = $this->paginatorOptions($results['RecordCount']);
    $interface->assign('recordStart', $paginatorOptions['recordStart']);
    $interface->assign('recordCount',$results['RecordCount']);
    $interface->assign('recordEnd', $paginatorOptions['recordEnd']);
    $pager =& Pager::factory($paginatorOptions);
    $interface->assign('pager', $pager);
    
    // Holdings
    
    $interface->assign('resultHoldings', $this->rutils->getStatuses($results));    
    
    // List of tags
    
    $interface->assign('taglist', $this->tags->tagsAndCounts());
  }
  
  
  function url($args = array(), $prepareForMore=false) {
    if (count($this->urlargs) > 0) {
      foreach ($this->urlargs as $kv) {
        $args[] = $kv;
      }
    }
    if (count($args) == 0) {
      if ($prepareForMore) {
        return $this->linkBase . '?'; 
      } else {
        return $this->linkBase;
      } 
    }
    
    $pairs = array();
    foreach ($args as $kv) {
      $pairs[] = implode('=', array($kv[0], rawurlencode($kv[1])));
    }
    $pairstring = implode('&', $pairs);
    $url =  implode('?', array($this->linkBase, $pairstring));
    if ($prepareForMore) {
      return $url . '&';
    } else {
      return $url;
    } 
  }

  
  function paginatorOptions($recordCount) {
    global $configArray;
    
    $page = 1;
    if (isset($_REQUEST['page']) && $_REQUEST['page'] > 1 ) {
        $page = $_REQUEST['page'];
    }
    $limit = $configArray['Site']['itemsPerPage'];
    $recordStart = (($page-1)*$limit)+1;
    
    if (($page*$limit) > $recordCount) {
        $recordEnd = $recordCount;
    } else {
        $recordEnd = $page*$limit;
    }
    
    // Process Paging
    
    $link = $this->url(array(), true) . 'page=%d';
    // $link = $this->linkBase .  '?page=%d';
    $options = array('totalItems' => $recordCount,
                     'mode' => 'sliding',
                     'path' => '',
                     'fileName' => $link,
                     'delta' => 5,
                     'perPage' => $limit,
                     'nextImg' => 'Next &raquo;',
                     'prevImg' => '&laquo; Prev',
                     'separator' => '',
                     'spacesBeforeSeparator' => 0,
                     'spacesAfterSeparator' => 0,
                     'append' => false,
                     'clearIfVoid' => true,
                     'urlVar' => 'page',
                     'curPageSpanPre' => '<span>',
                     'curPageSpanPost' => '</span>', 
                     'curPage' => $page,
                     'recordStart' => $recordStart,
                     'recordEnd' => $recordEnd);

    return $options;    
  }
  

}



?>