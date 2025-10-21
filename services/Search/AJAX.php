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

class AJAX extends Action {

    function launch()
    {
        header('Content-type: text/xml');
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        echo '<?xml version="1.0" encoding="UTF-8"?' . ">\n";
        echo "<AJAXResponse>\n";
        if (array_key_exists('method', $_GET)) {
          $method = $_GET['method'];
        } else {
          $method = $_POST['method'];
        }
        if (is_callable(array($this, $method))) {
            $this->$method();
        } else {
            echo '<Error>Invalid Method</Error>';
        }
        echo '</AJAXResponse>';
    }
    
    function IsLoggedIn()
    {
        require_once 'services/MyResearch/lib/User.php';

        if (isset($_COOKIE['userinfo'])) {
            echo "<result>True</result>";
            return;
        } else {
            echo "<result>False</result>";
            return;
        }
    }
    
    /**
     * Get Item Statuses
     *
     * This is responsible for printing the holdings information for a
     * collection of records in XML format.
     *
     * @access  public
     * @author  Chris Delis <cedelis@uillinois.edu>
     */
    function getItemStatuses()
    {
        global $configArray;

        require_once 'CatalogConnection.php';

        // Try to find a copy that is available
        $catalog = new CatalogConnection($configArray['Catalog']['driver']);
        
        $result = $catalog->getStatuses($_GET['id']);
        
        foreach ($result as $record) {
            $available = false;
            $location = '';
            foreach ($record as $info) {
                // Find an available copy
                if ($info['availability']) {
                    $available = true;
                }

                // Has multiple locations?
                if ($location != 'Multiple Locations') {
                    if ($location != '') {
                        if ($info['location'] != $location) {
                            $location = 'Multiple Locations';
                        } else {
                            $location = htmlentities($info['location']);
                        }
                    } else {
                        $location = htmlentities($info['location']);
                    }
                }
            }
            
            echo ' <item id="' . $record[0]['id'] . '">';
            if ($available) {
                echo '  <availability>true</availability>';
            } else {
                echo '  <availability>false</availability>';
            }
            echo '  <location>' . $location . '</location>';
            if (array_key_exists(0, $record)) {
              echo '  <reserve>' . $record[0]['reserve'] . '</reserve>';
              echo '  <callnumber>' . $record[0]['callnumber'] . '</callnumber>';
            }
            echo ' </item>';
        }

    }
    
    function GetSuggestion()
    {
        global $configArray;
        echo "HELLO!";
        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $db = new $class($configArray['Index']['url']);

        $query = 'title:"' . $_GET['phrase'] . '*"';
        $result = $db->query($query, 0, 10);

        $resultList = '';
        if (isset($result['record'])) {
            foreach ($result['record'] as $record) {
                if (strlen($record['title']) > 40) {
                    $resultList .= htmlentities(substr($record['title'], 0, 40)) . ' ...|';
                } else {
                    $resultList .= htmlentities($record['title']) . '|';
                }
            }
            echo '<result>' . $resultList . '</result>';
        }
    }
    
    // Saves a Record to User's Account
    function SaveRecord()
    {
        global $configArray;

        require_once 'services/MyResearch/lib/User.php';
        require_once 'services/MyResearch/lib/Resource.php';

        if (isset($_COOKIE['userinfo'])) {
            $user = unserialize($_COOKIE['userinfo']);
        } else {
            echo "<result>Unauthorized</result>";
            return;
        }

        $resource = new Resource();
        $resource->record_id = $_GET['id'];
        if (!$resource->find(true)) {
            $resource->insert();
        }

        preg_match_all('/"[^"]*"|[^ ]+/', $_GET['tags'], $tagArray);
        $user->addResource($resource, $tagArray[0], $_GET['notes']);
        echo "<result>Done</result>";
    }
    
    // Saves a search to User's Account
    function SaveSearch()
    {
        require_once 'services/MyResearch/lib/User.php';
        require_once 'services/MyResearch/lib/Search.php';
        
        //check if user is logged in
        if (UserAccount::isLoggedIn()) {
            $user = unserialize($_COOKIE['userinfo']);
        } else {
            echo "<result>Please Log in.</result>";
            return;
        }

        $lookfor = $_GET['lookfor'];
        $limitto = urldecode($_GET['limit']);
        $type = $_GET['type'];
        
        $search = new Search();
        $search->user_id = $user->id;
        $search->limitto = $limitto;
        $search->lookfor = $lookfor;
        $search->type = $type;
        if(!$search->find()) {
            $search = new Search();
            $search->user_id = $user->id;
            $search->lookfor = $lookfor;
            $search->limitto = $limitto;
            $search->type = $type;
            $search->created = date('Y-m-d');
            
            $search->insert();
        }
        echo "<result>Done</result>";
    }
    
    // Email Search Results
    function SendEmail()
    {
        require_once 'services/Search/Email.php';

        $emailService = new Email();
        $result = $emailService->sendEmail($_GET['url'], $_GET['to'], $_GET['from'], $_GET['message']);

        if (PEAR::isError($result)) {
            echo '<result>Error</result>';
        } else {
            echo '<result>Done</result>';
        }
    }
    
    function GetSaveStatus()
    {
        require_once 'services/MyResearch/lib/User.php';
        require_once 'services/MyResearch/lib/Resource.php';

        // check if user is logged in
        if (UserAccount::isLoggedIn()) {
            $user = unserialize($_COOKIE['userinfo']);
        } else {
            echo "<result>Unauthorized</result>";
            return;
        }

        // Check if resource is saved to favorites
        $resource = new Resource();
        $resource->record_id = $_GET['id'];
        if ($resource->find(true)) {
            if ($user->hasResource($resource)) {
                echo '<result>Saved</result>';
            } else {
                echo '<result>Not Saved</result>';
            }
        } else {
            echo '<result>Not Saved</result>';
        }
    }
    
    /**
     * Get Save Statuses
     *
     * This is responsible for printing the save status for a collection of
     * records in XML format.
     *
     * @access  public
     * @author  Chris Delis <cedelis@uillinois.edu>
     */
    function GetSaveStatuses()
    {
        require_once 'services/MyResearch/lib/User.php';
        require_once 'services/MyResearch/lib/Resource.php';

        // check if user is logged in
        if (UserAccount::isLoggedIn()) {
            $user = unserialize($_COOKIE['userinfo']);
        } else {
            echo "<result>Unauthorized</result>";
            return;
        }

        for ($i=0; ; $i++) {
            if (! isset($_GET['id' . $i])) break;
            $id = $_GET['id' . $i];
            echo '<item id="' . $id . '">';

            // Check if resource is saved to favorites
            $resource = new Resource();
            $resource->record_id = $id;
            if ($resource->find(true)) {
                $data = $user->getSavedData($id);
                if ($data) {
                    echo '<result>';
                    foreach ($data as $list) {
                        echo '{"id":"' . $list->id . '","title":"' . $list->title . '"}';
                    }
                    echo '</result>';
                } else {
                    echo '<result>False</result>';
                }
            } else {
                echo '<result>False</result>';
            }

            echo '</item>';
        }
    }
    
    function GetSavedData()
    {
        require_once 'services/MyResearch/lib/User.php';
        require_once 'services/MyResearch/lib/Resource.php';

        echo "<result>\n";

        $user = unserialize($_COOKIE['userinfo']);
        
        $saved = $user->getSavedData($_GET['id']);
        if ($saved->notes) {
            echo "  <Notes>$saved->notes</Notes>\n";
        }

        $myTagList = $user->getTags($_GET['id']);
        if (count($myTagList)) {
            foreach ($myTagList as $tag) {
                echo "  <Tag>" . $tag->tag . "</Tag>\n";
            }
        }

        echo '</result>';
    }

    function GetNarrowOptions()
    {
        global $configArray;
        global $interface;
        global $translator;

        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $db = new $class($configArray['Index']['url']);

        $filter = explode('|', $_POST['filter']);

        $limit = (isset($_POST['optionLimit'])) ? $_POST['optionLimit'] : 50;

        // Get fields from facets.ini configuration file
        $facets = parse_ini_file('conf/facets.ini');
        $interface->assign('facetConfig', $facets);

        // Define URL for links
        $interface->assign('fullUrl', $_POST['link']);

        $fields = $facets;
        
        $db->raw = true;
        $result = $db->search($_POST['query'], $filter, null, 0, null,
                               array('limit' => $limit,
                                     'field' => array_keys($fields)),
                               null, 'score', HTTP_REQUEST2::METHOD_GET);
                               
        $options = array('parseAttributes' => true);
        $unxml = new XML_Unserializer($options);
        $result = $unxml->unserialize($result);
        $data = $unxml->getUnSerializedData();


        // Ditch newspapers and such from Out of Scope

        // error_log("Starting facet cleanup");
        foreach($data['Facets']['Cluster'] as $index => $hlb2 ) {
          if ($hlb2['name'] == 'hlb_both') {
            // error_log("Found hlb");
            $newitems = array();
            foreach ($hlb2['item'] as $item) {
              if (! preg_match('/^(Out of|U.S. National and Regional News)/', $item['_content'])) {
                $newitems[] = $item;
              } else {
                // error_log("Got a match on " . $item['_content']);
              }
            }
            $data['Facets']['Cluster'][$index]['item'] = $newitems;
          }

	  $class = $configArray['Index']['engine'];
	  $db = new $class($configArray['Index']['url']);
	  if ($db->hathiOnly) {
	    $formatkey = -1;
	    if ($hlb2['name'] == 'format') {
	      foreach ($hlb2['item'] as $i => $item) {
		if ($item['_content'] == 'Electronic Resource') {
		  $formatkey = $i;
		  break;
		}
	      }
	      if ($formatkey >= 0) {
		unset($data['Facets']['Cluster'][$index]['item'][$formatkey]); 
	      }
	    }
	  }
	}

	$interface->assign('facets', $data['Facets']['Cluster']);
        echo '<result>';
        $interface->display('Search/facets.tpl');
        echo '</result>';
    }
}

function ar2xml($ar)
{
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->formatOutput = true;
    foreach ($ar as $facet => $value) {
        $element = $doc->createElement($facet);
        foreach ($value as $term => $cnt) {
            $child = $doc->createElement('term', $term);
            $child->setAttribute('count', $cnt);
            $element->appendChild($child);
        }
        $doc->appendChild($element);
    }

    return strstr($doc->saveXML(), "\n");
}

?>
