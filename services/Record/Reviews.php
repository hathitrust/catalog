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

class Reviews extends Record
{
    function launch()
    {
        global $interface;
        global $configArray;

        if (!$interface->is_cached($this->cacheId)) {
            $interface->setPageTitle('Reviews: ' . $this->record['title'][0]);

            // Fetch from provider
            if (isset($configArray['Content']['reviews'])) {
                $providers = explode(',', $configArray['Content']['reviews']);
                foreach ($providers as $provider) {
                    $provider = explode(':', trim($provider));
                    $func = strtolower($provider[0]);
                    $key = $provider[1];
                    $reviews[$func] = $this->$func($key);
                }
            }

            if ($reviews) {
                if (!PEAR::isError($reviews)) {
                    $interface->assign('reviews', $reviews);
                }
            }

            $interface->assign('subTemplate', 'view-reviews.tpl');
            $interface->setTemplate('view.tpl');
        }

        // Display Page
        $interface->display('layout.tpl', $this->cacheId);
    }

    /**
     * Amazon Reviews
     *
     * This method is responsible for connecting to Amazon AWS and abstracting
     * customer reviews for the specific ISBN
     *
     * @return  array       Returns array with review data, otherwise a
     *                      PEAR_Error.
     * @access  public
     * @author  Andrew Nagy <andrew.nagy@villanova.edu>
     */
    function amazon($id)
    {
        $url = 'http://webservices.amazon.com/onca/xml' .
               '?Service=AWSECommerceService&' .
               'SubscriptionId=' . $id . '&' .
               'Operation=ItemLookup&' .
               'ResponseGroup=Reviews&' .
               'ItemId=' . $this->isbn;
        $client = new HTTP_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);
        $result = $client->sendRequest();
        if (!PEAR::isError($result)) {
        	$unxml = new XML_Unserializer();
        	$result = $unxml->unserialize($client->getResponseBody());
        	if (!PEAR::isError($result)) {
                $data = $unxml->getUnserializedData();
                if ($data['Items']['Item']['CustomerReviews']['Review']['ASIN']) {
                    $data['Items']['Item']['CustomerReviews']['Review'] = array($data['Items']['Item']['CustomerReviews']['Review']);
                }
                $i = 0;
                $result = array();
                foreach ($data['Items']['Item']['CustomerReviews']['Review'] as $review) {
                    $customer = $this->getAmazonCustomer($id, $review['CustomerId']);
                    if (!PEAR::isError($customer)) {
                        $result[$i]['Source'] = $customer;
                    }
                    $result[$i]['Rating'] = $review['Rating'];
                    $result[$i]['Summary'] = $review['Summary'];
                    $result[$i]['Content'] = $review['Content'];
                    $i++;
                }
                return $result;
            } else {
                return $result;
            }
        } else {
            return $result;
        }
    }

    /**
     * Amazon Editorial
     *
     * This method is responsible for connecting to Amazon AWS and abstracting
     * editorial reviews for the specific ISBN
     *
     * @return  array       Returns array with review data, otherwise a
     *                      PEAR_Error.
     * @access  public
     * @author  Andrew Nagy <andrew.nagy@villanova.edu>
     */
    function amazoneditorial($id)
    {
        $url = 'http://webservices.amazon.com/onca/xml' .
               '?Service=AWSECommerceService&' .
               'SubscriptionId=' . $id . '&' .
               'Operation=ItemLookup&' .
               'ResponseGroup=EditorialReview&' .
               'ItemId=' . $this->isbn;
        $client = new HTTP_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);
        $result = $client->sendRequest();
        if (!PEAR::isError($result)) {
        	$unxml = new XML_Unserializer();
        	$result = $unxml->unserialize($client->getResponseBody());
        	if (!PEAR::isError($result)) {
                $data = $unxml->getUnserializedData();
                if (isset($data['Items']['Item']['EditorialReviews']['EditorialReview']['Source'])) {
                    $data['Items']['Item']['EditorialReviews']['EditorialReview'] = array($data['Items']['Item']['EditorialReviews']['EditorialReview']);
                }
                
                // Filter out product description
                for ($i=0; $i<=count($data['Items']['Item']['EditorialReviews']['EditorialReview']); $i++) {
                    if ($data['Items']['Item']['EditorialReviews']['EditorialReview'][$i]['Source'] == 'Product Description') {
                        unset($data['Items']['Item']['EditorialReviews']['EditorialReview'][$i]);
                    }
                }
                
                return $data['Items']['Item']['EditorialReviews']['EditorialReview'];
            } else {
                return $result;
            }
        } else {
            return $result;
        }
    }

    
    /**
     * getSyndeticsReviews
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
    function syndetics($id)
    {
        //list of syndetic revies
        $sourceList = array('CHREVIEW' => array('title' => 'Choice Review',
                                                'file' => 'CHREVIEW.XML'),
                            'BLREVIEW' => array('title' => 'Booklist Review',
                                                'file' => 'BLREVIEW.XML'),
                            'PWREVIEW' => array('title' => "Publisher's Weekly Review",
                                                'file' => 'PWREVIEW.XML'),
                            'SLJREVIEW' => array('title' => 'School Library Journal Review',
                                                'file' => 'SLJREVIEW.XML'),
                            'HBREVIEW' => array('title' => 'Horn Book Review',
                                                'file' => 'HBREVIEW.XML'),
                            'KIREVIEW' => array('title' => 'Kirkus Book Review',
                                                'file' => 'KIREVIEW.XML'),
                            'CRITICASEREVIEW' => array('title' => 'Criti Case Review',
                                                'file' => 'CRITICASEREVIEW.XML'));
                            
        //first request url
        $url = 'http://syndetics.com/index.aspx?isbn=' . $this->isbn . '/' .
               'index.xml&client=' . $id . '&type=rw12,hw7';

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
                       $sourceInfo['file'] . '&client=' . $id . '&type=rw12,hw7';
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
                $review[$i]['Content'] = preg_replace('/<[^>]*>/', '', $outdoc->saveXML());

                $nodes = $xmldoc2->GetElementsbyTagName("Fld997");  //copyright child
                $outdoc = new DOMDocument;
                $node = $outdoc->importNode($nodes->item(0), true);
                $outdoc->appendChild($node);
                $review[$i]['Copyright'] = $outdoc->saveXML();

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
    
    private function getAmazonCustomer($id, $customerId)
    {
        $url = 'http://webservices.amazon.com/onca/xml' .
               '?Service=AWSECommerceService&' .
               'SubscriptionId=' . $id . '&' .
               'Operation=CustomerContentLookup&' .
               'ResponseGroup=CustomerInfo&' .
               'CustomerId=' . $customerId;
        $client = new HTTP_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);
        $result = $client->sendRequest();
        if (!PEAR::isError($result)) {
        	$unxml = new XML_Unserializer();
        	$result = $unxml->unserialize($client->getResponseBody());
        	if (!PEAR::isError($result)) {
                $data = $unxml->getUnserializedData();
                if (isset($data['Customers']['Customer']['Name'])) {
                    return $data['Customers']['Customer']['Name'];
                } elseif (isset($data['Customers']['Customer']['Nickname'])) {
                    return $data['Customers']['Customer']['Nickname'];
                } else {
                    return 'Anonymous';
                }
            } else {
                return $result;
            }
        } else {
            return $result;
        }

    }

}

?>
