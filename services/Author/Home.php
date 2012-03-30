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

require_once 'HTTP/Request.php';
require_once 'Pager/Pager.php';

class Home extends Action
{
    private $db;
    private $lang;

    function launch()
    {
        global $configArray;
        global $interface;
        global $user;
        
        $interface->caching = false;
        
        if (!isset($_GET['author'])) {
            PEAR::raiseError(new PEAR_Error('Unknown Author'));
        } else {
            $interface->assign('author', $_GET['author']);
        }

        // What language should we use?
        $this->lang = $configArray['Site']['language'];
        
        // Retrieve User Search History
        if (isset($_COOKIE['search'])) {
            $sHistory = unserialize($_COOKIE['search']);
            $lastSearch = $sHistory[count($sHistory) - 1];
            $interface->assign('lastsearch', $lastSearch);
        }
        
        if (!$interface->is_cached('layout.tpl|Author' . $_GET['author'])) {
            // Clean up author string
            $author = $_GET['author'];
            if (substr($author, strlen($author) - 1, 1) == ",") {
                $author = substr($author, 0, strlen($author) - 1);
            }
            $author = explode(',', $author);
            $interface->assign('author', $author);
            
            // Create First Name
            $fname = '';
            if (isset($author[1])) {
                $fname = $author[1];
                if (isset($author[2])) {
                    // Remove punctuation
                  if ((strlen($author[2]) > 2) && (substr($author[2], -1) == '.')) {
                      $author[2] = substr($author[2], 0, -1);
                  }
                  $fname = $author[2] . ' ' . $fname;
               } 
            }
           
            // Remove dates
            $fname = preg_replace('/[0-9]+-[0-9]*/', '', $fname);            
            
            // Build Author name to display.
            if (substr($fname, -3, 1) == ' ') {
                // Keep period after initial
                $authorName = $fname . ' ';
            } else {
                // No initial so strip any punctuation from the end
                if ((substr(trim($fname), -1) == ',') ||
                    (substr(trim($fname), -1) == '.')) {
                    $authorName = substr(trim($fname), 0, -1) . ' ';
                } else {
                    $authorName = $fname . ' ';
                }
            }
            $authorName .= $author[0];
            $interface->assign('authorName', $authorName);
            
            // Pull External Author Content
            if (!isset($_GET['page']) || ($_GET['page'] == 1)) {
                //$authorInfo = $this->getWikipedia($_GET['author'], $configArray['Site']['language']);
                $authorInfo = $this->getWikipedia($authorName, $configArray['Site']['language']);
                if (!PEAR::isError($authorInfo)) {
                    $interface->assign('info', $authorInfo);
                }
            }
        }

        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $this->db = new $class($configArray['Index']['url']);
        if ($configArray['System']['debug']) {
            $this->db->debug = true;
        }

        // Define Page to Display
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $limit = 20;

        // Get Subjects by this Author
        $query = 'author:"' . $_GET['author'] . '"^100 OR ' .
                 'author2:"' . $_GET['author'] . '"';
        $result = $this->db->search($query, null, ($page-1)*$limit, $limit, null,
                                    array('field' => array('topicStr'),
                                          'limit' => '10'),
                                    null, 'score', HTTP_REQUEST_METHOD_GET);
        if (isset($result['Facets']['Cluster']['item'])) {
            $interface->assign('topics', $result['Facets']['Cluster']['item']);
        }

        // Hack for issue with XML_Serializer
        if (isset($result['record']['id'])) {
            $result['record'] = array($result['record']);
        }
        
        $interface->assign('recordSet', $result['record']);
        $interface->assign('recordCount', $result['RecordCount']);
        $interface->assign('recordStart', (($page-1)*$limit)+1);
        if ($result['RecordCount'] < $limit) {
            $interface->assign('recordEnd', $result['RecordCount']);
        } else if (($page*$limit) > $result['RecordCount']) {
            $interface->assign('recordEnd', $result['RecordCount']);
        } else {
            $interface->assign('recordEnd', $page*$limit);
        }


        $link = (strstr($_SERVER['REQUEST_URI'], 'page=')) ? str_replace('page=' . $_GET['page'], '', $_SERVER['REQUEST_URI']) . 'page=%d' : $_SERVER['REQUEST_URI'] . '&page=%d';
        $options = array('totalItems' => $result['RecordCount'],
                         'mode' => 'sliding',
                         'path' => '',
                         'fileName' => $link,
                         'delta' => 5,
                         'perPage' => 20,
                         'nextImg' => 'Next &raquo;',
                         'prevImg' => '&laquo; Prev',
                         'separator' => '',
                         'spacesBeforeSeparator' => 0,
                         'spacesAfterSeparator' => 0,
                         'append' => false,
                         'clearIfVoid' => true,
                         'urlVar' => 'page',
                         'curPageSpanPre' => '<span>',
                         'curPageSpanPost' => '</span>');
        $pager =& Pager::factory($options);
        $interface->assign('pager', $pager);

        $interface->setTemplate('home.tpl');
        $interface->display('layout.tpl', 'Author' . $_GET['author']);
    }
    

    /**
     * getWikipedia
     *
     * This method is responsible for connecting to Wikipedia via the REST API
     * and pulling the content for the relevant author.
     *
     * @param   string  $lang   The language code of the language to use
     * @return  null
     * @access  public
     * @author  Andrew Nagy <andrew.nagy@villanova.edu>
     */
    public function getWikipedia($author, $lang = null)
    {
        if ($lang) {
            $this->lang = $lang;
        }
    
        $url = "http://$this->lang.wikipedia.org/w/api.php" .
               '?action=query&prop=revisions&rvprop=content&format=php' .
               '&list=allpages&titles=' . urlencode($author);
        $client = new HTTP_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);
        $result = $client->sendRequest();
        if (PEAR::isError($result)) {
            return $result;
        }

        $info = $this->parseWikipedia(unserialize($client->getResponseBody()));
        if (!PEAR::isError($info)) {
            return $info;
        }
    }
    
    /**
     * parseWikipedia
     *
     * This method is responsible for parsing the output from the Wikipedia
     * REST API.
     *
     * @param   string  $lang   The language code of the language to use
     * @return  null
     * @access  public
     * @author  Rushikesh Katikar <rushikesh.katikar@gmail.com>
     */
    private function parseWikipedia($body)
    {
        global $configArray;

        // Check if data exists or not
        if(isset($body['query']['pages']['-1'])) {
            return new PEAR_Error('No page found');
        }

        $body = array_shift($body['query']['pages']);
        $info['name'] = $body['title'];

        $body = array_shift($body['revisions']);
        $body = explode("\n", $body['*']);

        // Process redirection
        if (stristr($body[0], '#REDIRECT')) {
            preg_match('/\[\[(.*)\]\]/', $body[0], $matches);
            return $this->getWikipedia($matches[1]);
        }

        // Loop through content
        $done = 0;
        while(!$done) {
            if($body[0] == '') {
                array_shift($body);
                continue;
            }
            switch(substr($body[0], 0, 2)){
                case '[[' :
                case ']]' :
                case '{{' :
                case '}}' :
                case '| ' :
                case ' |' :
                case '  ' :
                    if ((strstr($body[0], 'image_caption')) ||
                        (strstr($body[0], 'caption '))) {
                        $stpos = strpos($body[0], ' = ') + strlen(' = ');
                        $caption = substr($body[0], $stpos);
                    }
                    if ((stristr($body[0], 'image_name')) ||
                        (stristr($body[0], 'image ')) ||
                        (stristr($body[0], 'image=')) ||
                        (stristr($body[0], 'image: '))) {
                        $stpos = strpos($body[0], '= ') + strlen('= ');
                        if ($stpos) {
                            $body[0] = substr($body[0], $stpos);
                            $endpos = stripos($body[0], '.jpg') + strlen('.jpg');
                            if (!$endpos) {
                                $endpos = stripos($body[0], '.gif') + strlen('.gif');
                            }
                            if ($endpos) {
                                $image = substr($body[0], 0, $endpos);
                            }
                        }
                    }
                    array_shift($body);
                    break;
                default :
                    $done = 1;
                    break;
            }

        }
        
        // Get Image Information
        if ($image) {
            $url = "http://$this->lang.wikipedia.org/w/api.php" .
                   '?prop=imageinfo&action=query&iiprop=url&iiurlwidth=150&format=php' .
                   '&titles=Image:' . str_replace(' ', '_', $image);
            $client = new HTTP_Request();
            $client->setMethod(HTTP_REQUEST_METHOD_GET);
            $client->setURL($url);
            $result = $client->sendRequest();
            if (PEAR::isError($result)) {
                return $result;
            }

            if ($response = $client->getResponseBody()) {
                if ($imageinfo = unserialize($response)) {
                    //$image = $imageinfo['query']['pages']['-1']['imageinfo'][0]['url'];
                    // Hack for wikipedia api
                    preg_match('/\"http:\/\/(.*)\"/', $response, $matches);
                    $image = 'http://' . substr($matches[1], 0, strpos($matches[1], '"'));
                }
            }
        }

        // Retrieve Page Content
        $desc = "";
        $done = 0;
        while (!$done && count($body)) {
            if (substr($body[0], 0, 1) == '=') {
                $done = 1;
            } else {
                $desc .= $body[0];
                array_shift($body);
            }
        }

        // Convert wikipedia links
        $pattern = array();
        $replacement = array();
        $pattern[] = '/(\x5b\x5b)([^\x5d|]*)(\x5d\x5d)/';
        $replacement[] = '<a href="' . $configArray['Site']['url'] . '/Search/Home?lookfor=%22$2%22">$2</a>';
        $pattern[] = '/(\x5b\x5b)([^\x5d]*)\x7c([^\x5d]*)(\x5d\x5d)/';
        $replacement[] = '<a href="' . $configArray['Site']['url'] . '/Search/Home?lookfor=%22$2%22">$3</a>';

        // Removes citation
        $pattern[] = '/({{)[^}]*(}})/';
        $replacement[] = "";

        // Formatting
        $pattern[] = "/'''([^']*)'''/";
        $replacement[] = '<strong>$1</strong>';

        $desc = preg_replace($pattern, $replacement, $desc);

        $info['image'] = $image;
        $info['altimage'] = $caption;
        $info['description'] = $desc;

        return $info;
    }

}

?>