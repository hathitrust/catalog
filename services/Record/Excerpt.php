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

require_once 'HTTP/Request.php';

require_once 'Record.php';

class Excerpt extends Record
{
    function launch()
    {
        global $interface;
        global $configArray;

        if (!$interface->is_cached($this->cacheId)) {
            $interface->setPageTitle('Excerpt: ' . $this->record['title'][0]);

            // Fetch from provider
            if (isset($configArray['BookReviews'])) {
                switch($configArray['BookReviews']['provider']) {
                    case 'Syndetics':
                        $result = $this->getSyndeticsExcerpt();
                        break;
                    case 'Amazon':
                    default:
                        $result = $this->getAmazonExcerpt();
                        break;
                }
            }

            if ($result) {
                if (!PEAR::isError($result)) {
                    $interface->assign('reviews', $result);
                //} else {
                //    PEAR::raiseError($result);
                }
            }

            $interface->assign('subTemplate', 'view-excerpt.tpl');
            $interface->setTemplate('view.tpl');
        }

        // Display Page
        $interface->display('layout.tpl', $this->cacheId);
    }

    /**
     * getAmazonExcerpt
     *
     * This method is responsible for connecting to Amazon AWS and abstracting
     * customer reviews for the specific ISBN
     *
     * @return  array       Returns array with review data, otherwise a
     *                      PEAR_Error.
     * @access  public
     * @author  Andrew Nagy <andrew.nagy@villanova.edu>
     */
    function getAmazonExcerpt()
    {
        global $configArray;
        return null;
    }

    /**
     * getSyndeticsExcerpt
     *
     * This method is responsible for connecting to Syndetics and abstracting
     * reviews from only 1 provider.
     *
     * It first queries the master url for the ISBN entry seeking a review url.
     * If a review url is found, the script will then use http request to
     * retrieve the script. The script will then parse the review according to
     * US MARC (i believe). It will provide a link to the url master html page
     * for more information.
     * Configuration:  Sources are processed in order - refer to $sourceList.
     * If your library prefers one reviewer over another change the order.
     * If your library does not like a reviewer, remove it.  If there are more
     * syndetics reviewers add another entry.
     *
     * @return  array       Returns array with review data, otherwise a
     *                      PEAR_Error.
     * @access  public
     * @author  Joel Timothy Norman <joel.t.norman@wmich.edu>
     * @author  Andrew Nagy <andrew.nagy@villanova.edu>
     */
    function getSyndeticsExcerpt()
    {
        global $configArray;

        //list of syndetic revies
        $sourceList = array('DBCHAPTER' => array('title' => 'First Chapter or Excerpt',
                                                'file' => 'DBCHAPTER.XML'));
                            
        //first request url
        $url = 'http://syndetics.com/index.aspx?isbn=' . $this->isbn . '/' .
               'index.xml&client=' . $configArray['BookReviews']['id'] .
               '&type=rw12,hw7';

        //find out if there are any reviews
        $client = new HTTP_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);
        if (PEAR::isError($http = $client->sendRequest())) {
			return $http;
        }

        // Test XML Response
        if (!($xmldoc = @DOMDocument::loadXML($client->getResponseBody()))) {
            return new PEAR_Error('Invalid XML');
        }

        $review = array();
        $i = 0;
        foreach ($sourceList as $source => $sourceInfo) {
            $nodes = $xmldoc->getElementsByTagName($source);
            if ($nodes->length) {
                // Load reviews
                $url = 'http://syndetics.com/index.aspx?isbn=' . $this->isbn . '/' .
                       $sourceInfo['file'] . '&client=' . $configArray['BookReviews']['id'] .
                       '&type=rw12,hw7';
                $client->setURL($url);
                if (PEAR::isError($http = $client->sendRequest())) {
                    return $http;
                }

                // Test XML Response
                if (!($xmldoc2 = @DOMDocument::loadXML($client->getResponseBody()))) {
                    return new PEAR_Error('Invalid XML');
                }

                $nodes = $xmldoc2->GetElementsbyTagName("Fld520"); //review child
                $outdoc = new DOMDocument;
                $node = $outdoc->importNode($nodes->item(0), true);
                $outdoc->appendChild($node);
                $review[$i]['Content'] = $outdoc->saveXML();

                $nodes = $xmldoc->GetElementsbyTagName("Fld997");  //copyright child
                $review[$i]['Copyright'] = $nodes->item(0)->textContent;

                if ($review[$i]['Copyright']) {  //stop duplicate copyrights
                    $location = strripos($review[0]['Content'], $review[0]['Copyright']);
                    if ($location > 0) {
                        $review[$i]['Content'] = substr($review[0]['Content'], 0, $location);
                    }
                }

                $review[$i]['Source'] = $sourceInfo['title'];  //changes the xml to actual title
                $review[$i]['ISBN'] = $this->isbn; //show more link
                $review[$i]['username'] = $configArray['BookReviews']['id'];
                
                $i++;
            }
        }

        return $review;
    }

}

?>
