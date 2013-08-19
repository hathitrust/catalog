<?php

/**
  * SearchStructure -- build up a search structure (with components for search strings and their fields, filters, etc)
  * suitable for turning into Solr searches, links to other searches (as per facet links), etc.
  *
  * For ease, we break a search up into three components:
  *
  *  - search: a list of triples of the form [field, string, AND|OR|NULL]
  *
  *  - inbandFilters: a set of filters (facets) specified by the user. This is a hash of the form
  *    inbandFilters[index] = [term1=>term1, term2=>term2, implodedTerm3=>[list,of,or'd,values]].
  *    The "implodedTerm3" is the list of or'd values run through implode('|', sort(@terms)).
  *
  *  - outofbandFIlters are the same as inband, execpt these are specified via configuration and not via
  *    user input (e.g., restrict to a certain library or whatnot), allowing multiple instances to
  *    use the same database, or allowing the sysadmin to silently ignore swaths of the database without
  *    this being revealed in the interface.
 **/

require_once 'sys/VFSession.php';
require_once 'sys/VFUser.php';

class SearchStructure 
{
    public $force_standard = array('callnumber' => true);
    public $ftonly;
    public $use_dismax = false;
    public $sort;
    public $page;
    public $inbandFilters = array();
    public $outofbandFilters = array();
    public $search= array();
    public $facetConfig;
    public $manuallyAddedIDs = array();
    public $onlyTemp = false;
    public $onlyTags = null;
    public $filterByTags = array();
    public $tagList = array();
    public $nolimit = false;
    public $action = 'standard';
    public $checkSpelling = false;
    public $originalHash;
    public $indexDisplayName = array(
      'publishDateTrie' => 'Year', 
      'subject2' => 'Subject',
      'location' => 'Location',
      'ht_rightscode' => "Rights"
    );
    
    
    /**
      * fromHash(hash)
      *
      * Takes in a hash with all the search-structure information it needs and constructs the
      * $ss object. Normally this will just be $_REQUEST and the normal constrctor can be called, but
      * this is here for other uses.
    **/
    
    
    static function fromHash($hash) {
      $c = __CLASS__;
      $obj = new $c(true);
      $obj->_fillFromHash($hash);
      return $obj;
    }
    
    private function _fillFromHash($hash) {
      global $configArray;
      $session = VFSession::singleton();
      
      
      $this->originalHash = $hash;
      
      # Figure out which facet file to use
      $facetfile = 'conf/facets.ini';
      if (isset($configArray['FacetFiles'])) {
        if ($session->is_set('inUSA')) {
          $facetfile = $session->get('inUSA')? $configArray['FacetFiles']['USA'] : $configArray['FacetFiles']['INTL'];
        }
      }
      
      $this->facetConfig = parse_ini_file($facetfile);
      $this->facetLimit = 30;       
      // Get refs to the arrays
      $lookfor = isset($hash['lookfor'])? $hash['lookfor'] : array();
      $type = isset($hash['type'])? $hash['type'] : array();
      $bool = isset($hash['bool'])? $hash['bool']: null;


      $this->ftonly = isset($hash['ft']) && $hash['ft'] == 'ft';
          
      
      $this->page = isset($hash['page'])? $hash['page']: "1";
      

      $this->inbandFilters = array();
      $this->outofbandFilters = array();
      $this->fillInbandFilters($hash);
      $this->fillOOBFilters();
      $this->fillORFilters($hash);
      $this->fillRangeFilters($hash);
      $this->fillManualIDs($hash);
      $this->fillTags($hash);
      $this->fillInstFilter($hash);
      $this->setAction($hash);

      # Set the sort
      if (isset($hash['sort'])) {
          $this->sort  = $hash['sort'];
      }

      # Turn everything into an array
      if (!is_array($lookfor)) {
          $lookfor = array($lookfor);
      }
      if (!is_array($type)) {
          $type = array($type);
      }
      if (!is_array($bool)) {
          $bool = array($bool);
      }



      # Clean up the lookfors
      
      //      foreach ($lookfor as $i => $val) {
      //        $new = $this->convert_smart_quotes($val);
      //        if ($new != $val) {
      //          // echo "Changed '$val' into '$new'\n";
      //          $lookfor[$i] = $new;
      //        }
      //      }

      # Ditch all the blank lookfors
      $ss = array();
      foreach($lookfor as $i => $val) {
          if (preg_match('/\S/', $lookfor[$i])) {
              array_push($ss, array(isset($type[$i])? $type[$i] : 'all', 
                                    $lookfor[$i], 
                                    isset($bool[$i])? $bool[$i] : null
                                    )
                         );
          }
      }
      

      // The last bool need to be nil. Pop it off, change it, and
      // push it back on again.
      if (count($ss)) {
        $last = array_pop($ss);
        unset($last[2]);
        array_push($ss, $last);
      }
      
      // CHECK FOR DISMAX USE
      
      // First, assume we can use dismax
      
      $this->use_dismax = true;
      
      // If we have multiple types, we can't use dismax
      // If our single index is unsupported, we can't use dismax.
      
      if (count($ss) > 1) {
        $this->use_dismax = false;
      } elseif (count($ss) == 1) {

        $index = $ss[0][0];
        if (isset($this->force_standard[$index]) && $this->force_standard[$index]) {
          $this->use_dismax = false;
        }
      }
      $this->search = $ss; 
  }
    
            
    function __construct ($blank = false) 
    {
        global $configArray;
        
        if ($blank) {
            $this->ss = array();
            $this->inbandFilters = array();
            $this->outofbandFilters = array();
            $this->fillOOBFilters();
            return;
        }
        
       $this->_fillFromHash($_REQUEST);
 

    }
    
   /**
     * Turn an array of filter (facet) values into a string normalized by first (a) sorting the values, then (b)
     * imploding them with a '|'
   **/
   
     function filterkey($val) {
       if (!is_array($val)) {
         return $val;
       }
       $key = $val;
       sort($key);
       return implode('|', $key);
     }  

   /**
     * Build a list of OR'd filtes. They must be named filteror-<indexname>
     * Note that if you want to create an OR'd filter manually, you can
     * just call addFiilter with something like ("Bill" OR "Dueber")
   **/
  
    function fillORFilters($hash) {
        foreach (array_keys($hash) as $key) {
            if (!preg_match('/^fqor-(.*)$/', $key, $matches)) {
                continue;
            }
            $index = $matches[1];
            $vals = $hash[$key];
            if (is_array($vals) && count($vals) && strlen($vals[0])) {
              $this->addFilter($index, $vals);
            }
        }
    }

   /**
     * Find inputs of the form fqrange-start-XXX and fqrange-end-XXX
     * and create a range filter.
     *
     * 
    **/
    
    function fillRangeFilters($hash) {
      foreach (array_keys($hash) as $key) {
          if (!preg_match('/^fqrange-(start|end)-(.*?)-(\d+)$/', $key, $matches)) {
              continue;
          }
          // We may have already deleted it...
          if (!isset($hash[$key])) {
            continue;
          }
          $startend = $matches[1];
          $index = $matches[2];
          $seq   = $matches[3];
          $val = $hash[$key];
          if (!preg_match('/\S/', $val)) {
            continue;
          }
                    
          $startkey = implode('-', array("fqrange", "start", $index, $seq));
          $endkey   = implode('-', array("fqrange", "end",   $index, $seq));
          
          $start = '*';
          $end   = '*';
          
          if (isset($hash[$startkey]) && preg_match('/^\s*\d+\s*$/',$hash[$startkey])) {
            $start = $hash[$startkey];
          }
          if (isset($hash[$endkey]) && preg_match('/^\s*\d+\s*$/',$hash[$endkey])) {
            $end = $hash[$endkey];
          }
          unset($hash[$startkey], $hash[$endkey]);
          
          $this->addFilter($index, "[\"$start\" TO \"$end\"]");
        }
      }
 
    
   /**
     *
     * Build up a list of filters based on the $_REQUEST['filter'] values, if any,
     * as well as any extra filters from the extraFilters area of the config file
   **/    
    
    function fillOOBFilters() {
        global $configArray;

        // Add the extra filters from the config file
        if (isset($configArray['extraFilters'])) {
            foreach (array_values($configArray['extraFilters']) as $kvstring) {
                $kv = explode(':', $kvstring);
                $this->addOOBFilter($kv[0], $kv[1]);
            }
        }
        
        if ($this->ftonly) {
          $this->addOOBFilter('ht_availability', 'Full text');
        }

#### HACK  FOR ANGELINA ####

      if (isset($_REQUEST['onlyso'])) {
         $this->addOOBFilter('ht_availability', 'Search only');
      }



     }
    
    function fillInstFilter($hash) {
      global $configArray;
      if (isset($configArray['Switches']['ignoreInst']) && $configArray['Switches']['ignoreInst']) {
        return;
      }

      $session = VFSession::singleton();

      // Add location limit from sublib and collection if present
      $location = '';
      if (isset($hash['sublib']) && 
                $hash['sublib'] != '' &&
                $hash['sublib'] != 'ALL') {
        $location = $hash['sublib'];
      }
      if (isset($hash['sublibColl']) && 
                $hash['sublibColl'] != '' &&
                $hash['sublibColl'] != 'ALL') {
        $location = $hash['sublibColl'];
      }
      //if ($location != '') $this->addOOBFilter('location', $location);
      if ($location != '') $this->addFilter('location', $location);
    }
    
    function fillInbandFilters($hash) {
                
        if (!isset($hash['filter'])) {
            return;
        }
        $request = $hash['filter'];
        
        
        if (!is_array($request)) {
            $request = array($request);
        }

        foreach($request as $filter) {
            $item = explode(':', $filter);
            if (isset($item[1]) && preg_match('/\S/', $item[1])) {
              $this->addFilter($item[0], $item[1]);
            }
        }
        
    }
    
    function setAction($hash) {
      // if (isset($hash['checkspelling']) && !$this->use_dismax) {
      //      $this->action = 'spellCheckCompRH';
            $this->checkSpelling = true;
      //  } 
     }
    
    
     function setFTOnly($ft) {
       $this->ftonly = $ft;
       if ($ft) {
         $this->addOOBFilter('ht_availability', 'Full text');
       } else {
         $this->removeOOBFilter('ht_availability', 'Full text');
       }
     }
    
    
    function actionURLComponents() {
      $aucs = array();
      // if ($this->use_dismax) {
      //   $aucs[] = array('use_dismax', true);
      // }
      
      if ($this->ftonly) {
        $aucs[] = array('ft', 'ft');
      } else {
        $aucs[] = array('ft', '');
      }
      
      return $aucs;
    }
    
    /**
      * Add a tag directive
      *
    **/
    
    function addTag($tag) {
      $this->tagList[$tag] = true;
    }
    /**
      * Hold onto any tag directives
      *
    **/
    
    function fillTags($hash) {
      if (!isset($hash['tag'])) {
        return;
      } else {
        $tags = $hash['tag'];
      }
      if (!is_array($tags)) {
        $tags = array($tags);
      }
      foreach ($tags as $tag) {
        $this->addTag($tag);
      }
    }
    
    function tags() {
      return array_keys($this->tagList);
    }
    
    /**
      * Add any search components of the form id=XXX to manualIDs
    **/
    
    function fillManualIDs($hash) {
      if (!isset($hash['id'])) {
        return;
      }
      $ids = $hash['id'];
      if (!is_array($ids)) {
        $ids= array($ids);
      }
      foreach ($ids as $id) {
        $multiids = preg_split('/[,; ]+/', $id);
        foreach ($multiids as $oneid) {
          $this->addIDs($oneid);          
        }
      }
    }
    
    /**
      * Add a filter, possibly keeping it out-of-band (and hence not displayed to the user)
      * 
      * @param string index       The solr index to filter on. 
      * @param string value       The value or array of values
    **/
    
    function addFilter($index, $value) {
      $this->inbandFilters[$index][$this->filterkey($value)] = $value;
    }
    
    function addOOBFilter($index, $value) {
      $this->outofbandFilters[$index][$this->filterkey($value)] = $value;
    }
    
    function removeFilter($index, $value) {
      $key = $this->filterkey($value);
      unset($this->inbandFilters[$index][$key]);
    }
     
    function removeOOBFilter($index, $value) {
      $key = $this->filterkey($value);
      unset($this->outofbandFilters[$index][$key]);
     }
    
    function hasFilter($index, $value) {
       $key = $this->filterkey($value);
      return isset($this->inbandFilters[$index], $this->inbandFilters[$index][$key]) ||
             isset($this->inbandFilters[$index], $this->inbandFilters[$index][$key]);
    }
    
  /**
    * Create a list of the form 
    *  [
    *    [index, val], [index2, val2], [index2, val3], [index2, [orval1, orval2]], ...
    *  ]
  **/
    
    function _activeFilters($farray) {
      $rv = array();
      foreach (array_keys($farray) as $index) {
        foreach (array_values($farray[$index]) as $v) {
          $rv[] = array($index, $v);
        }
      }
      return $rv;
    }
    
     function activeInbandFilters() {
         return $this->_activeFilters($this->inbandFilters);
     }
     
     function activeOOBFilters() {
       return $this->_activeFilters($this->outofbandFilters);
     }
     
     function allActiveFilters() {
         return array_merge($this->activeInbandFilters(), $this->activeOOBFilters());
     }
     
     
    /** 
      * Deal with extra IDs (for manually-created lists)
      *
    **/
    
    function addIDs($ids) {
      if (!is_array($ids)) {
        $ids = array($ids);
      }
      foreach ($ids as $id) {
        $id = sprintf("%09d", $id);
        $this->manuallyAddedIDs[$id] = true;
      }
    }
    
    function removeIDs($id) {
      if (!is_array($ids)) {
        $ids = array($ids);
      }
      foreach ($ids as $id) {
        $id = sprintf("%09d", $id);
        unset($this->manuallyAddedIDs[$id]);
      }
    }
    
    function extraIDs() {
      return array_keys($this->manuallyAddedIDs);
    }
    
    /**
      * Utilities to easily determine if we have an empty search (or various components)
      *
    **/
    
    function isEmpty() {
      return $this->noSearch() && $this->noExtraIDs() && $this->noInbandFilters();
    }
    
    function noSearch() {
      return count($this->search()) == 0;
    }
    
    function noExtraIDs() {
      return count($this->manuallyAddedIDs) == 0;
    }
    
    function noInbandFilters() {
      return count($this->inbandFilters) == 0;
    }
    
    /**
      * Create URL components that will re-create this search. Basically, this should be everything after
      * /Search/Home?
      *
      * Note that we don't include outofbandFilters in this; whatever is producing those should still produce them
      * without them being explicitly in the URL (hence "out-of-band")
      *
      * @return array A series of tuples of the form (data[], value) where the data options
      *               are type, lookfor, and bool.
    **/    
    
    
    function searchURLComponents() {
        $urlcomps = array();
        foreach ($this->search as $tlb) {
             $urlcomps[] = array('type[]', $tlb[0]);
             $urlcomps[] = array('lookfor[]', $tlb[1]);
             if (isset($tlb[2]) && $tlb[2]) {
                 $urlcomps[] = array('bool[]', $tlb[2]);
             }
         }
        return $urlcomps;
    }
    
    /**
      * Create URL components for the inband filters of this query
      *
      * @return array A series of tuples of the form ('filter[]', index:Value) or
      *         (fqor-indexname[]:value)
      *
     */

    function filterURLComponents() {
        $urlcomps = array();
        foreach ($this->activeInbandFilters() as $indexValue) {
          $index = $indexValue[0];
          $val = $indexValue[1];
          if(is_array($val)) {
            $key = 'fqor-' . $index . '[]';
            foreach ($val as $v) {
              $urlcomps[] = array($key, $v);
            }
          } else {
            $urlcomps[] = array('filter[]', implode(':', array($index, $val)));
          }
        }
        return $urlcomps;
    }
    
    /**
      * Create URL components for the sort options of this query
      *
      * @return array A tuple of the form ('sort', sortspec), or () if no sort is specified
      *
     */
    
    function sortURLComponents() {
        if ($this->sort) {
            return  array(array('sort', $this->sort));
        } else {
            return array();
        }
    }
    
    function pageURLComponents() {
        if ($this->page) {
            return  array(array('page', $this->page));
        } else {
            return array();
        }
    }
    
    function tagURLComponents() {
      $rv = array();
      foreach ($this->tags() as $t) {
        $rv[] = array('tag', $t);
      }
      return $rv;
    }
    

    static function asURLComponent($kvpair) {
        return rawurlencode($kvpair[0]) . '=' . rawurlencode($kvpair[1]);
    }
    
    
    function asFullURL($module = 'Home', $extra = array()) {
      global $configArray;
      $url = $configArray['Site']['url'] . '/Search/' . $module . '?' . $this->asURL($extra);
      return $url;
    }

    function asRecordURL($sysid, $extra=array()) {
      
      $url =  '/Record/' . $sysid;
      if (count($ss->search) <= 1) {
        $url .= '?' . $this->asURL($extra, false);
      } else {
        $url .= '?' .  implode('&', array_map(array($this, "asURLComponent"), $this->actionURLComponents()));
      }
      return $url;
    }


    /**
     *  DEPRECATED. What was I thinking calling this asURL??? Use asURLComponents
     *  Construct a valid URL from the search components, inband filters, and the sort
     *  and return it as a url-encoded string.
     *
     *  @param array $extra  An array of (key, value) duples to add to the URL
     *  @return string The URL for the current search
     */

    function asURL($extra = array(), $includePageComponents=true) {
        return implode('&', array_map(array($this, "asURLComponent"), 
                                      array_merge($this->searchURLComponents(),
                                                  $this->filterURLComponents(),
                                                  $this->sortURLComponents(),
                                                  $this->tagURLComponents(),
                                                  ($includePageComponents ? $this->pageURLComponents() : array()),
                                                  $this->actionURLComponents(),
                                                  $extra)));    
    }
    
    function asURLComponents($extra) {
      return $this->asURL($extra);
    }
    
    /**
     * Construct and return a URL which extends the current search with an additional
     * filter. A simple wrapper for addFilter/removeFilter
     *
     * @param string $index The (actual solr) index for the filter
     * @param string $value The (already quoted and parenthesized, if need be) value
     * @param array $extra  An array of (key, value) duples to add to the URL
     * @return string The URL for the current search with an extra filter
    */
    
    function asURLPlusFilter($index, $value, $extra = array()) {
        if ($this->hasFilter($index, $value)) {
            return $this->asURL($extra);
        }
        
        $this->addFilter($index, $value);
        $url = $this->asURL($extra);
        $this->removeFilter($index,$value);
        return $url;
    }
    

    /**
     * Construct and return a URL which broadens the current search to ignore
     * the given index/value as filter.
     *
     * @param string $index The (actual solr) index for the filter
     * @param string $value The (already quoted and parenthesized, if need be) value
     * @param array $extra  An array of (key, value) duples to add to the URL     
     * @return string The URL for the current search without the given filter
     *
    */
    
    function asURLMinusFilter($index, $value, $extra=array()) {
        if (!$this->hasFilter($index, $value)) {
            return $this->asURL($extra);
        }

        $this->removeFilter($index, $value);
        $url = $this->asURL($extra);
        $this->addFilter($index,$value);
        return $url;
    }
    
    /**
      * Construct a string representing the search string for display
      * @return Array An array of displayable search terms with trailing bools
      *    ['title:Title words AND', 'author: Dueber']
    **/
    
    function searchtermsForDisplay() {
      $s = array();
      if (!count($this->search) && !count($this->tagList)) {
        return array("(no keywords)");
      }
      
      if (count($this->tagList)) {
        $session = VFSession::singleton();
        foreach ($this->tags() as $t) {
          if ($t == $session->uuid) {
            $s[] = "Temporary set";
          } else {
            $s[] = "tag: $t";
          }
        }
        
      }
      foreach ($this->search as $fkb) { # field, keywords, bool
        $index = $fkb[0] == 'all'? 'all fields' : $fkb[0];
        if (isset($this->indexDisplayName[$index])) $index =  $this->indexDisplayName[$index];
        
        $l = $index . ':' . $fkb[1];
        
        if (isset($fkb[2])) { # the boolean operator
          $l .= ' ' . $fkb[2];
        }
        $s[] = $l;
      }
      return $s;
    }

    /**
     * Construct a string representing the current facets as a display string
     *
     **/

    function facetsForDisplay() {
      $s = array();
      foreach ($this->currentFacetsStructure() as $f) {
        $val = $f['value'];
        if (is_array($val)) {
          $val = implode(', ', $val);
        }
        $s[] = implode(':', array($f['indexDisplay'], '(' . $val . ')'));
      }
      return $s;
    }


     
     static function displayStrip($v) {
       if (preg_match('/^\[\s*\"?(.*?)\"?\s+TO\s+\"?(.*?)\"?\s*\].*$/', $v, $matcher)) {
         $start = $matcher[1];
         $end   = $matcher[2];
         if ($start == '*') {
           return "During or before $end";
         }
         if ($end == '*') {
           return "During or after $start";
         }
         if ($start == $end) {
           return "During $start";
         }
         return "Between $start and $end";
       } else {
         return preg_replace('/[\[\]\"]/', '', $v);
       }
       
       
     }
    
    
     /**
       * A structure denoting current inband facets, with addition information about
       * them
       * @return array An array of arrays of the form:
       *
       *  - index => facet solrIndex 
       *  - indexDisplay => display name from facets.ini (or whatever is in config.ini under FacetFiles)
       *  - value => facet value
       *  - removalURL => A url that, when followed, repeats the search without this facet
       *  - logargs => string containing pipe-delimited arguments for $activitylog->log, for inclusion in page
      */
    
     function currentFacetsStructure($extra=array()) {
         $rv = array();
         foreach ($this->activeInbandFilters() as $kv) {
             $valueDisplay = is_array($kv[1])? implode(' OR ', array_map(array($this, 'displayStrip'), $kv[1])) : $this->displayStrip($kv[1]);
             $logargs = implode('|', array('removefacet', $kv[0], $valueDisplay, ''));
             $rv[] = array(
               'index' => $kv[0],
               'value' => $kv[1],
               'valueDisplay' => $valueDisplay,
               'indexDisplay' => $this->facetDisplayName($kv[0]),
               'removalURL' => $this->asURLMinusFilter($kv[0], $kv[1], array_merge($extra)), 
               'logargs' => $logargs
             );
         }
         return $rv;
     }
     
      
    /**
      * The list of facet fields
      * 
      * @return array An array of facet fields (index names)
    **/
     
    function facetFields() {
        return array_keys($this->facetConfig);
    } 
     
    /**
      * Get the display name for a facet, as denoted in facets.ini
      *
      * @param string $facet The solr index to provide a display name for
      * @return string The display name for $facet
    **/
    
    
    
    function facetDisplayName($facet) {
        if (isset($this->indexDisplayName[$facet])) {
          return $this->indexDisplayName[$facet];
        } else {
          return isset($this->facetConfig[$facet])? $this->facetConfig[$facet] : $facet;
        }
    }
    
    
    // function convert_smart_quotes($text) 
    // { 
    //   // From http://axonflux.com/handy-regexes-for-smart-quotes
    //   
    //   
    //   //Quotes: Replace smart double quotes with straight double quotes.
    //   //ANSI version for use with 8-bit regex engines and the Windows code page 1252.
    //   $text = preg_replace('/[\x84\x93\x94]/', '"', $text);
    //  
    //   //Quotes: Replace smart single quotes and apostrophes with straight single quotes.
    //   //ANSI version for use with 8-bit regex engines and the Windows code page 1252.
    //   $text = preg_replace("/[\x82\x91\x92]/", "'", $text);   
    // 
    //   //Quotes: Replace smart double quotes with straight double quotes.
    //   //Unicode version for use with Unicode regex engines.
    //   // $text = mb_ereg_replace('/[\u201C\u201D\u201E\u201F\u2033\u2036]/', '"', $text);
    // 
    //   //Quotes: Replace smart single quotes and apostrophes with straight single quotes.
    //   //Unicode version for use with Unicode regex engines.
    //   // $text = mb_ereg_replace("/[\u2018\u2019\u201A\u201B\u2032\u2035]/", "'", $text);
    // 
    //   
    //   return $text;
    // }
    
    
    // Take from http://www.toao.net/48-replacing-smart-quotes-and-em-dashes-in-mysql
    function convert_smart_quotes($text) {
      // First, replace UTF-8 characters.
      $text = str_replace(
       array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
       array("'", "'", '"', '"', '-', '--', '...'),
       $text);
      // Next, replace their Windows-1252 equivalents.
       $text = str_replace(
       array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
       array("'", "'", '"', '"', '-', '--', '...'),
       $text);
       return $text;
    }
    
}

?>
