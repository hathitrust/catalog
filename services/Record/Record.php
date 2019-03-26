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

require_once 'File/MARCFLAT.php';

require_once 'sys/Language.php';
require_once 'sys/MergedItemSet.php';

require_once 'services/Record/FilterFormat.php';
require_once 'sys/VFSession.php';
require_once 'services/Record/RecordUtils.php';


class Record extends Action
{
    public $id;
    public $mergedItemData;
    
    /**
     * marc record in File_Marc object
     */
    public $marcRecord;
    
    public $record;

    public $isbn;

    public $cacheId;

    public $db;
    
    public $inTemp = false;
    public $inSubset = false;
    public $isFavorite = false;
    //tbw
    
    function __construct()
    {
        global $configArray;
        global $interface;
        
        //$interface->caching = 1;


        if (isset($_REQUEST['id'])) {
          $this->id = sprintf('%09d', $_GET['id']);
        } elseif (isset($_REQUEST['mergesearch'])) {

          $m = new MergedItemSet(explode('|', $_REQUEST['mergesearch']));

          $this->mergedItemData = $m->data();
          $this->id = $m->firstRecordID();
          $itemdata = $this->mergedItemData[1]['items'];
          $interface->assign('mergedItems', $itemdata);
          
          # Only one? From the OCLC? Just redirect to it and exit
  
          if ((count($itemdata) == 1) && $_REQUEST['fromoclc'] == 1) {
            header("Location: " . $itemdata[0]['itemURL']);
            exit;
          }
                    
          
        }
        
        $interface->assign('id', $this->id);          
        
        
        // Define Default Tab
        $tab = (isset($_GET['action'])) ? $_GET['action'] : 'Holdings';
        $interface->assign('tab', $tab);

        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $url = $configArray['Index']['url'];
        $this->db = new $class($url);
        if ($configArray['System']['debug']) {
            $this->db->debug = true;
        }
        $session = VFSession::instance();
        $interface->assign('inst', $session->get('inst'));
        $interface->assign('lastsearch', $session->get('lastsearch'));
        $interface->assign('lasttagsearch', $session->get('lasttagsearch'));


        if (isset($_REQUEST['oob']) && $_REQUEST['oob'] == 'showrecordnav') {
          $interface->assign('showrecordnav', true);
        }

        // Retrieve Full Marc Record
	try {
          if (!($record = $this->db->getRecord($this->id))) {
            PEAR::raiseError(new PEAR_Error('Record Does Not Exist'));
          }
	} catch (Exception $e) {
	  if (isset($_REQUEST['mergesearch'])) {
    	    header('Location: https://www.hathitrust.org/temporary-outage');
	    exit();
	  }
	}


        $this->record = $record;
 
        
        $interface->assign('record', $record);
        $interface->assign('recordFormat', $record['format']);
        if (isset($record['language']))  $interface->assign('recordLanguage', $record['language']);

        // Process MARC Data
        $ru = new RecordUtils();
        $marc = $ru->getMarcRecord($record);
        $this->marcRecord = $marc;
        $interface->assign('marc', $marc);
        $links = $ru->getLinkNums($this->marcRecord); 
        $interface->assign('googleLinks', implode(",", $links));



        // Define External Content Provider
        if ($this->marcRecord->getField('020')) {
            $interface->assign('hasReviews', true);
            if (strstr($configArray['Content']['reviews'], 'Syndetics')) {
                $interface->assign('hasExcerpt', true);
            }
        }

        //$interface->assign('inst', 'all');		// make sure inst is set
        // Retrieve User Search History in order to get last serach
        //if (isset($_COOKIE['search'])) {
        //    $sHistory = unserialize($_COOKIE['search']);
        //    $lastSearch = $sHistory[count($sHistory) - 1];
        //    $lastSearch = preg_replace('/module=Search&action=Home&/', '', $lastSearch);
        //    $interface->assign('lastsearch', $lastSearch);
        //    // get inst from last search
        //    foreach (explode('&', $lastSearch) as $param) {
        //      list($p, $v) = explode('=', $param);
        //      if ($p == 'inst') { 
        //        $interface->assign('inst', $v);
        //        break;
        //      }
        //   }
        //}

        // Retrieve result id list, build next and previous record links
        if (isset($_COOKIE['resultids'])) {
          $previous = '';
          $next = '';
          $currentResults = unserialize($_COOKIE['resultids']);
          $recIndex = 0;
          foreach ($currentResults as $index => $result) {
            $id = $result[1];
            if ($id == $this->id) {
              $recIndex = $index;
              break;
            }  
          }
          $recordNum = $currentResults[$recIndex][2];
          $recordCount = $currentResults[$recIndex][3];
          if (isset($currentResults[$recIndex-1][0]))
            $interface->assign('previous', $currentResults[$recIndex-1][0]);
          if (isset($currentResults[$recIndex+1][0])) 
            $interface->assign('next', $currentResults[$recIndex+1][0]);
          $interface->assign('current', "Showing record $recordNum of $recordCount");
        }

        // Retrieve tags associated with the record
        $limit = 5;
        
        $this->cacheId = 'Record|' . $this->id . '|' . get_class($this);
        
        if (!$interface->is_cached($this->cacheId)) {
            // Find Similar Records
            $similar = $this->db->getMoreLikeThis($record, $this->id);
            $interface->assign('similarRecords', $similar['record']);
            // Find Other Editions
            $editions = $this->getEditions();
            if (!PEAR::isError($editions)) {
                $interface->assign('editions', $editions);
            }
        }

        // Define CoINs Identifier
        $interface->assign('coinsID', $configArray['COinS']['identifier']);
        
        // Set Proxy URL
        $interface->assign('proxy', $configArray['EZproxy']['host']);

        // get lc subjects and other subjects
        $lc_subjects = array();
        $other_subjects = array();
        foreach ($this->marcRecord->getFields('600|610|630|650|651|655',1) as $field) {
          $type = $field->getIndicator(2);
          $subject = array();
          foreach ($field->getSubfields() as $subfield) {
            if ($subfield->getCode() >= 'a' ) $subject[] = $subfield->getData();
          }
          $type == '0' ? $lc_subjects[] = $subject : $other_subjects[] = $subject;
        }
        $interface->assign('lc_subjects', $lc_subjects);
        $interface->assign('other_subjects', $other_subjects);

        // Set legacy catalog record  URL
        if (isset($configArray['Catalog']['recordURL']))
          $interface->assign('recordURL', $configArray['Catalog']['recordURL']);
          
        // Set flag for MDL records (035 subfield a starting with sdr-mdl)
        $mdl = 0;
        if ($f035List = $this->marcRecord->getFields('035')) {
          foreach ($f035List as $field) {
            if ($suba = $field->getSubfield('a')) {
              $suba_data = $suba->getData();
              if (preg_match('/^sdr-mdl/i', $suba_data)) $mdl = 1;
            }
          }
        }
        $interface->assign('mdl', $mdl);
          
    }
    
    function getEditions()
    {
        if ($this->isbn) {
	  //            return $this->getXISBN($this->isbn);
        } else if (isset($this->record['issn'])) {
	  //            return $this->getXISSN($this->record['issn'][0]);
        }
        
        return null;
    }
    
    private function getXISBN($isbn)
    {
        global $configArray;
    
        // Build URL
        $url = 'http://xisbn.worldcat.org/webservices/xid/isbn/' . $isbn .
               '?method=getEditions&format=csv';
        if (isset($configArray['WorldCat']['id'])) {
            $url .= '&ai=' . $configArray['WorldCat']['id'];
        }

        // Print Debug code
        if ($configArray['System']['debug']) {
            echo "<pre>XISBN: $url</pre>";
        }

        // Fetch results
        $fp = fopen($url, "r");
        $query = '';
        while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
            if ($query != '') {
                $query .= ' OR isbn:' . $data[0];
            } else {
                $query = 'isbn:' . $data[0];
            }
        }
        
        if ($query) {
            // Filter out current record
            $query .= ' NOT id:' . $this->id;        
            $args = array();
            $args[] = array('q', $query);
            $args[] = array('limit', 5);
            
            $result = $this->db->solrSearch($args, 'select'); 
            if (!PEAR::isError($result)) {
                if (isset($result['record'])) {
                    if (isset($result['record']['id'])) {
                        $result['record'] = array($result['record']);
                    }
                    return $result['record'];
                } else {
                    return null;
                }
            } else {
                return $result;
            }
        } else {
            return null;
        }
    }

    private function getXISSN($issn)
    {
        global $configArray;
    
        // Build URL
        $url = 'http://xissn.worldcat.org/webservices/xid/issn/' . urlencode($issn) .
               //'?method=getEditions&format=csv';
               '?method=getEditions&format=xml';
        if (isset($configArray['WorldCat']['id'])) {
            $url .= '&ai=' . $configArray['WorldCat']['id'];
        }

        // Print Debug code
        if ($configArray['System']['debug']) {
            echo "<pre>XISSN: $url</pre>";
        }

        // Fetch results
        $query = '';
        $data = file_get_contents($url);
        $unxml = new XML_Unserializer();
        $unxml->unserialize($data);
        $data = $unxml->getUnserializedData($data);
        if (isset($data['group']['issn'])) {
            if (is_array($data['group']['issn'])) {
                foreach ($data['group']['issn'] as $issn) {
                    if ($query != '') {
                        $query .= ' OR issn:' . $issn;
                    } else {
                        $query = 'issn:' . $issn;
                    }
                }
            } else {
                $query = 'issn:' . $data['group']['issn'];
            }
        }
        
        if ($query) {
            // Filter out current record
            $query .= ' NOT id:' . $this->id;        
            $args = array();
            $args[] = array('q', $query);
            $args[] = array('limit', 5);
            $result = $this->db->solrSearch($args, 'select'); 
        
            if (!PEAR::isError($result)) {
                if (isset($result['record'])) {
                    if (isset($result['record']['id'])) {
                        $result['record'] = array($result['record']);
                    }
                    return $result['record'];
                } else {
                    return null;
                }
            } else {
                return $result;
            }
        } else {
            return null;
        }
    }

    function getLinkNums($record) {
      // ISBN 
      $links = array();
      // oclc number
      if ($f035List = $record->getFields('035')) {
        foreach ($f035List as $field) {
          if ($suba = $field->getSubfield('a')) {
            $suba_data = $suba->getData();
            if (preg_match('/^(\(oclc\)|\(ocolc\)|ocm|ocn).*?(\d+)/i', $suba_data, $oclc_num)) {
              $links[] = 'OCLC:' . $oclc_num[2];
            }
          }
        }
      }
      if ($isbnField = $record->getField('020')) {
        if ($isbnField = $isbnField->getSubfield('a')) {
          $this->isbn = trim($isbnField->getData());
          if ($pos = strpos($this->isbn, ' ')) {
            $this->isbn = substr($this->isbn, 0, $pos);
          }
          $links[] = "ISBN:" . $this->isbn;
        }
      }
      return $links;
    }

}
?>
