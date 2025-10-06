<?php
/**
 * 
 * A simple subclass of Home.php that allows a little more customization about what goes out 
 * the door. Designed to be a more flexible replacement for AJAX.php.
 *
*/


require_once 'services/Search/Home.php';
require_once 'services/Search/SearchStructure.php';

class Snippet extends Home 
{

    /**
      * launch() -- setup and dispatch
      *
      * Sets up the solr object, determines the method to run, and runs it. If the method is
      * invalid, return a 403 Forbidden
     */
         
    function launch()
    {
        global $configArray;
        // Set up Solr object
         $class = $configArray['Index']['engine'];
         $this->db = new $class($configArray['Index']['url']);
         
         $this->ss = new SearchStructure();
         
         $method = isset($_REQUEST['method'])? $_REQUEST['method'] : null;
         if ($method && is_callable(array($this, $method))) {
             $this->$method();
         } else {
             header("HTTP/1.0 403 Forbidden");
             echo "Method $method not recognized.";
         }
         
    }
    
    /**
      *  getfacetCounts() -- Get facet stuff based on the current search and return as 
      *  an HTML snippet
      *
     */

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
                 $logargs = array();
                 $logargs[] = array('lc', 'addfacet');
                 $logargs[] = array('lv1', $index);
                 $logargs[] = array('lv2', $vc[0]);
                 $logargs[] = array('lv3', $i);
                 $url = $this->ss->asURLPlusFilter($index, $vc[0], $logargs);
                 $counts[$index][] = array('cluster' => $index, 
                                              'value' => $vc[0], 
                                              'count' => $vc[1],
                                              'url' => $url);
             }
         }
         $interface->assign('counts', $counts);
         $interface->assign('facetConfig', $this->ss->facetConfig);
         $interface->assign('indexes', $this->ss->facetFields());
         $interface->display('Search/facet_snippet.tpl');
     }
    
    
}
?>
