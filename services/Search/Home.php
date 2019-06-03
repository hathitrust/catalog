<?php
/**
 *
 * Copyright (C) Villanova University 2007.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

require_once 'Action.php';

require_once 'sys/LoggingPager.php';
require_once 'Pager/Pager.php';
require_once 'services/Record/FilterFormat.php';

require_once 'services/Search/SearchStructure.php';

require_once 'sys/VFSession.php';
require_once 'sys/VFUser.php';

require_once "feedcreator/include/feedcreator.class.php";

require_once 'File/MARC.php';


require_once 'services/Record/RecordUtils.php';

class Home extends Action {

    private $db;
    private $query;
    private $filterQuery;
    private $searchStructure;
    private $ss;
    private $session;


    function setup() {
        global $configArray;
        global $interface;

        $this->ss = new SearchStructure;
        $this->session = VFSession::instance();

         // Setup Search Engine Connection
         $class = $configArray['Index']['engine'];
         $this->db = new $class($configArray['Index']['url']);
         if ($configArray['System']['debug']) {
             $this->db->debug = true;
         }

    }

    function launch()
    {
        global $interface;
        global $configArray;

        $this->setup();

        // module and action are set in the rewrite rules; they'll still be here even
        // if we change to using POST from the browser. If we've got them, do the search

        if (isset($_GET['module']) && isset($_GET['action'])) {
          // Must have atleast Action and Module set to continue
          // If we're using dismax or edismax, it's not an advanced search
          if (isset($_REQUEST['adv'])) {
            $interface->setPageTitle('Catalog Advanced Search Results');
            $interface->assign('adv', preg_replace('/\/Home\?/', '/Advanced?', $_SERVER['REQUEST_URI']));
          } else {
            $interface->setPageTitle('Catalog Search Results');
          }
          $this->search();
        } else {
            // Otherwie, display the home page
            $interface->setPageTitle('Search Home');
	          $interface->assign('isTheHomePage', true);
            $interface->assign('searchTemplate', 'search.tpl');
            $interface->setTemplate('home.tpl');
            $interface->display('layout.tpl');

        }
    }


    // The main search function

    function search()
    {
        global $interface;
        global $configArray;


        //******************************************************
        //    SET UP BASIC DISPLAY VARIABLES
        //******************************************************

        $interface->assign('proxy', $configArray['EZproxy']['host']);

        // The sort option, if set

        if (isset($this->ss->sort)) {
            $interface->assign('sort', $this->ss->sort);
        }

        $interface->assign('uuid', $this->session->uuid);

        # Add the whole damn session, too

        $interface->assign('session', $this->session);

        // If it was a simple search (lookfor is not an array)
        // go ahead and put them in the interface. Otherwise,
        // leave it blank.
        //
        // We need to figure out how/where to display the
        // actual search, ala Blacklight, though, too.

        if (count($this->ss->search) == 1) {
            $interface->assign('lookfor', $this->ss->search[0][1]);
            $interface->assign('type', $this->ss->search[0][0]);
        }

        // The action
        $interface->assign('action', $_GET['action']);

        // The Query URL
        $interface->assign('searchcomps', $this->ss->asURL());

        $this->session->set('lastsearch', '/Search/Home?' . $this->ss->asURL());
        $interface->assign('thissearch', $this->session->get("lastsearch"));
        // The existing facets
        $interface->assign('currentFacets', $this->ss->currentFacetsStructure());
        #print_r($this->ss);

        // The search terms, for display
        $interface->assign('searchterms', implode(' ', $this->ss->searchtermsForDisplay()));

        //******************************************************
        //      SIMILAR SEARCH TERMS (FOR TOP OF AUTHOR SEARCH)
        //******************************************************


        // Get similar search terms
        $type = isset($_REQUEST['type'])? $_REQUEST['type'] : false;



        //******************************************************
        //    WHAT PAGE ARE WE ON? HOW MANY ITEMS SHOULD WE RETRIEVE?
        //******************************************************

        $page = 1;
        if (isset($_REQUEST['page']) && $_REQUEST['page'] > 1 ) {
            $page = $_REQUEST['page'];
        }

        if (isset($_REQUEST['pagesize'])) {
            $pagesize = $_REQUEST['pagesize'];
	    $interface->assign('pagesize', $pagesize);
        }

        $limit = isset($_REQUEST['pagesize']) ? $_REQUEST['pagesize'] : $configArray['Site']['itemsPerPage'];


        // Max of 100
	if ($limit > 100) {
	  $limit = 100;
	}

        //******************************************************
        //     ACTUALLY DO THE SEARCH
        //******************************************************


        $result = $this->newprocessSearch($page, $limit);
        if (PEAR::isError($result)) {
            PEAR::raiseError($result->getMessage());
        }


        //******************************************************
        //     GET SPELLING RESULTS
        //******************************************************

        if (isset($result['SpellcheckSuggestion']) && $result['RecordCount'] == 0) {
          $interface->assign('newPhrase', $result['SpellcheckSuggestion']);
        }

        //******************************************************
        //     NO RESULTS? Just return
        //******************************************************

        if (count($result['record']) == 0) {
            $interface->assign('ss', $this->ss);

            if ($this->ss->ftonly) {
              $this->ss->setFTOnly(false);
              $allitems_results = $this->newprocessSearch(1, 0);
              $interface->assign('allitems_count', $allitems_results['RecordCount']);
              $interface->assign('allitems_url', $this->ss->asFullURL());
              $this->ss->setFTOnly(true);
            }

            $interface->setTemplate('list-none.tpl');
            $interface->display('layout.tpl');

            return;
        }


        //******************************************************
        //    TURN IT INTO AN ARRAY IF WE ONLY GOT ONE ITEM
        //******************************************************


        //if ($result['RecordCount'] == 1) {
        //    $result['record'] = array($result['record']);
        //}





        //******************************************************
        //    SET UP DISPLAY
        //******************************************************

        $interface->assign('sitepath', $configArray['Site']['path']);
        $interface->assign('subpage', 'Search/list-list.tpl');
        $interface->setTemplate('list.tpl');
        $interface->assign('atom', 1);
        $interface->assign('ss', $this->ss);

        //*****************************************************
        // Record Count / URLs for this tab and other tab (Project UNICORN)
        //*****************************************************

        if ($this->ss->ftonly) {
          $interface->assign('fullview_count', $result['RecordCount']);
          $interface->assign('fullview_url', $this->ss->asFullURL());

          // temporarily munch this->ss to get the url and count for the allitems tab
          $this->ss->setFTOnly(false);
          $allitems_results = $this->newprocessSearch(1, 0);
          $interface->assign('allitems_count', $allitems_results['RecordCount']);
          $interface->assign('allitems_url', $this->ss->asFullURL());
          $this->ss->setFTOnly(true);

        } else {
          $interface->assign('allitems_count', $result['RecordCount']);
          $interface->assign('allitems_url', $this->ss->asFullURL());

          $this->ss->setFTOnly(true);
          $fullview_results = $this->newprocessSearch(1, 0);
          $interface->assign('fullview_count', $fullview_results['RecordCount']);
          $interface->assign('fullview_url', $this->ss->asFullURL());
          $this->ss->setFTOnly(false);

        }





        //******************************************************
        //    DEAL WITH PAGINATION
        //******************************************************


        $recordCount =  $result['RecordCount'];
        $recordStart = (($page-1)*$limit)+1;

        $interface->assign('recordStart', $recordStart);
        $interface->assign('recordCount', $recordCount);

        if (($page*$limit) > $recordCount) {
            $recordEnd = $recordCount;
        } else {
            $recordEnd = $page*$limit;
        }
        $interface->assign('recordEnd', $recordEnd);


        // Process Paging
        $link = 'Search/Home?' . $this->ss->asURL() . '&pagesize='. $limit . '&page=%d';
        $rlink = '/' . $link; 	// rlink used to build record-level paging urls
        $options = array('totalItems' => $result['RecordCount'],
                         'mode' => 'loggingPager',
                         'path' =>  $configArray['Site']['fullurl'],
                         'fileName' => $link,
                         'delta' => 4,
                         'perPage' => $limit,
                         'nextImg' => 'Next Page <i aria-hidden="true" class="icomoon icomoon-arrow-right"></i>',
                         'prevImg' => '<i aria-hidden="true" class="icomoon icomoon-arrow-left"></i> Previous Page',
                         'separator' => '',
                         'spacesBeforeSeparator' => 0,
                         'spacesAfterSeparator' => 0,
                         'append' => false,
                         'clearIfVoid' => true,
                         'urlVar' => 'page',
                         'curPageSpanPre' => '<span><strong><span class="offscreen">Results page (current) </span>',
                         'curPageSpanPost' => '</strong></span>',
                         'altPage' => '<span class="offscreen">Results page</span> ');


        $pager =& Pager::factory($options);

        $interface->assign('pager', $pager);



        //******************************************************
        //    SAVE SEARCH IN COOKIE
        //******************************************************


        $qargs = explode("&", $_SERVER['QUERY_STRING']);
        $ignore = array('module' => true, 'action' => true, 'submit' => true);
        foreach ($qargs as $keyval) {
            $kv = explode('=', $keyval);
            if (isset($ignore[$kv[0]])) {
                unset($qargs[$keyval]);
            }
        }

        $newQS = implode('&', $qargs);

       // Store Search in Cookie
        $sHistory = array();
        if (isset($_COOKIE['search'])) {
            $sHistory = unserialize($_COOKIE['search']);
        }
        $sHistory[] = $newQS;
        // Ensure cookie doesn't exceed the 4k limit
        while (strlen(urlencode(serialize($sHistory))) > 2048) {
            array_shift($sHistory);
        }
        $lastsearch = array_pop($sHistory);
        setcookie('search', serialize(array($lastsearch)), null, '/');


        //******************************************************
        //    SAVE CURRENT RESULT IDs FOR NEXT/PREV BUTTONS
        //******************************************************

        // Get records ids in array to use for results cookie used for record paging
        $resultIDs = array();
        $recordNum = $recordStart;
        if ($page != 1) {
          //$prevLink = str_replace("%d", $page-1, $link) . sprintf('&rec=%d', $recordNum-1);
          $prevLink = str_replace("%d", $page-1, $rlink) . sprintf('&rec=%d', $limit);
          $resultIDs[] = array($prevLink, '', $recordNum-1, $recordCount);
        }
        foreach ($result['record'] as $record) {
          $id = $record['id'];
          $resultIDs[] = array("/Record/" . $id, $id, $recordNum, $recordCount);
          $recordNum++;
        }
        if ($recordEnd < $recordCount) {
          //$nextLink = str_replace("%d", $page+1, $link) . sprintf('&rec=%d', $recordNum);
          $nextLink = str_replace("%d", $page+1, $rlink) . sprintf('&rec=%d', 1);
          $resultIDs[] = array($nextLink, '', $recordNum, $recordCount);
        }
        $cookielength = strlen(urlencode(serialize($resultIDs)));
        if ($cookielength > 4096) {
          error_log("can't write resultIDs cookie, length is $cookielength");
         } else {
          $cookieRC = setcookie('resultids', serialize($resultIDs), null, '/');
        }
        if (isset($_REQUEST['rec'])) {
          $newOffset = $_REQUEST['rec'];
          $recPath = $resultIDs[$newOffset][0];
          header("Location: {$configArray['Site']['path']}{$recPath}");
          exit();
        }


        //******************************************************
        //   GET HOLDINGS DATA AND TAGGED INFO
        //******************************************************

        // Get Holdings Data, summarize at the location level
        $ru = new RecordUtils();
        foreach ($result['record'] as $num => $record) {
          $id_list[] = $record['id'];
          $marcRecord = $ru->getMarcRecord($record);
          $result['record'][$num]['marc'] = $marcRecord;
          $result['record'][$num]['title'] = $ru->getFullTitle($marcRecord);
          $result['record'][$num]['googleLinks'] = implode(",", $ru->getLinkNums($marcRecord));
          $url_list[$record['id']] = $ru->getURLs($marcRecord);
          $id = $record['id'];


        }



        $interface->assign('recordSet', $result['record']);
#        $interface->assign('resultHoldings', $ru->getStatuses($result));

        //******************************************************
        //  COINS
        //******************************************************
        $interface->assign('coinsID', $configArray['COinS']['identifier']);

        //******************************************************
        //   SCORE
        //******************************************************

        // Want to put the scores in the output? Gods, but I hate having to do 'isset' all the time
        if (isset($configArray['Site']['showscores']) && $configArray['Site']['showscores']) {
            $interface->assign('showscores', true);
        }

        //******************************************************
        //   DISPLAY
        //******************************************************

        $this->getFacetCounts();
        $interface->display('layout.tpl');
    }



//===========================================
// newprocessSearch(page, limit) -- process the search
//   and return the results object
//===========================================

    function newprocessSearch($page = 1, $limit = 20)
    {


        // If we've got a simple (one-type) clause, do a simple search

        return $this->db->simpleSearch($this->ss,
                                       ($page-1)*$limit, $limit
                                      );
    }



    function getFacetCounts() {
        global $configArray;
        global $interface;

        $rawcounts = $this->db->facetlist($this->ss, $this->ss->facetFields(), 'count', 0, 30);
        $counts = array();
        foreach ($rawcounts['values'] as $index => $valcountpairs) {
            $counts[$index] = array();
            $i = 0;
            foreach ($valcountpairs as $vc) {
                if ($this->ss->hasFilter($index, $vc[0])) {
                    continue;
                }
                $i++;
                $logargs = "addfacet|$index|" . $vc[0] . "|$i";
                $url = $this->ss->asURLPlusFilter($index, $vc[0]);
                $url = preg_replace('/&page=\d+/', '', $url);
                $counts[$index][] = array('cluster' => $index,
                                             'value' => $vc[0],
                                             'count' => $vc[1],
                                             'url' => $url,
                                             'logargs' => $logargs);
            }
        }
        $interface->assign('counts', $counts);
        $interface->assign('facetConfig', $this->ss->facetConfig);
        $interface->assign('indexes', $this->ss->facetFields());
        // $interface->display('Search/facet_snippet.tpl');
    }

    function getService() {
      return $this->ss;
    }


}

?>
