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

/*
 * @todo    Convert this to an AJAX approach to allow for client side access to
 *          images.  Also investigate local caching approach.  What about using
 *          Squid?
 */

// Retrieve values from configuration file
$configArray = parse_ini_file('conf/config.ini', true);

if (!count($_GET)) {
    header('Content-type: image/gif');
    echo readfile('images/noCover2.gif');
    exit();
}

if (isset($_GET['isn'])) {
    $localFile = 'images/covers/' . $_GET['size'] . '/' . $_GET['isn'] . '.jpg';
    if (is_readable($localFile)) {
        // Load local cache if available
        header('Content-type: image/jpeg');
        echo readfile($localFile);
    } else {
        // Fetch from provider
        if (isset($configArray['Content']['coverimages'])) {
            $providers = explode(',', $configArray['Content']['coverimages']);
            $success = 0;
            foreach ($providers as $provider) {
                $provider = explode(':', $provider);
                if (!(isset($provider[0]) && isset($provider[1]))) {
                  continue;
                }
                $func = $provider[0];
                $key = $provider[1];
                if ($func($key)) {
                    $success = 1;
                    break;
                }
            }
            if (!$success) {
                header('Content-type: image/gif');
                echo readfile('images/noCover2.gif');
            }
        } else {
            header('Content-type: image/gif');
            echo readfile('images/noCover2.gif');
        }
    }
} else {
    header('Content-type: image/gif');
    echo readfile('images/noCover2.gif');
}

function syndetics($id)
{
    global $configArray;
    global $localFile;

    switch ($_GET['size']) {
        case 'small':
            header('Content-type: image/gif');
            $size = 'SC.GIF';
            break;
        case 'medium':
            header('Content-type: image/gif');
            $size = 'MC.GIF';
            break;
        case 'large':
            header('Content-type: image/jpeg');
            $size = 'LC.JPG';
            break;
    }

    $url = 'http://www.syndetics.com/index.aspx?type=xw12&isbn=' . $_GET['isn'] .
           '/' . $size . '&client=' . $id;
    if ($image = file_get_contents($url)) {
        if ($image != file_get_contents('images/covers/syndetics_1x1.gif')) {
            file_put_contents($localFile, $image);
            readfile($url);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function librarything($id)
{
    global $configArray;
    global $localFile;

    $url = 'http://covers.librarything.com/devkey/' . $id . '/' . $_GET['size'] . '/isbn/' . $_GET['isn'];
    if ($image = file_get_contents($url)) {
        if ($image != file_get_contents('images/covers/1x1.gif')) {
            file_put_contents($localFile, $image);
            header('Content-type: image/jpeg');
            readfile($url);
            return true;
        }
    }
    return false;
}

function google()
{
    require_once 'HTTP/Request.php';

    global $configArray;

    if (is_callable('json_decode')) {
        $url = 'http://books.google.com/books?jscmd=viewapi&' .
               'bibkeys=ISBN:' . $_GET['isn'] . '&callback=addTheCover';
        $client = new HTTP_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);
        $result = $client->sendRequest();
        if (!PEAR::isError($result)) {
            $json = $client->getResponseBody();
            // strip off addthecover(
            $json = substr($json, 31);
            // strip off );
            $json = substr($json, 0, -3);
            // convert \x26 to &
            $json = str_replace("\\x26", "&", $json);
            if ($json = json_decode($json, true)) {
                //if ($image = file_get_contents($json['ISBN:' . $_GET['isn']]['thumbnail_url'])) {
                if (isset($json['thumbnail_url']) && $image = file_get_contents($json['thumbnail_url'])) {
                    header('Content-type: image/jpeg');
                    readfile($json['thumbnail_url']);
                    return true;
                }
            }
        }
    }
    return false;
}

function amazon($id)
{
    require_once 'HTTP/Request.php';
    require_once 'XML/Unserializer.php';

    global $configArray;

    $url = 'http://webservices.amazon.com/onca/xml?' .
           'Service=AWSECommerceService&' .
           'SubscriptionId=' . $id . '&' .
           'Operation=ItemLookup&' .
           'ResponseGroup=Images&' .
           'ItemId=' . $_GET['isn'];

    $client = new HTTP_Request();
    $client->setMethod(HTTP_REQUEST_METHOD_GET);
    $client->setURL($url);
    $result = $client->sendRequest();
    if (!PEAR::isError($result)) {
        $unxml = new XML_Unserializer();
        $unxml->unserialize($client->getResponseBody());
        $data = $unxml->getUnserializedData();
        if (!$data['Items']['Item']['ASIN']) {
            $data['Items']['Item'] = $data['Items']['Item'][0];
        }
        if (isset($data['Items']['Item'])) {
            switch ($_GET['size']) {
                case 'small':
                    if ($fp = fopen($data['Items']['Item']['SmallImage']['URL'], 'r')) {
                        $image = $data['Items']['Item']['SmallImage']['URL'];
                        fclose($fp);
                    }
                    break;
                case 'medium':
                    if ($fp = fopen($data['Items']['Item']['MediumImage']['URL'], 'r')) {
                        $image = $data['Items']['Item']['MediumImage']['URL'];
                        fclose($fp);
                    }
                    break;
                case 'large':
                    if ($fp = fopen($data['Items']['Item']['LargeImage']['URL'], 'r')) {
                        $image = $data['Items']['Item']['LargeImage']['URL'];
                        fclose($fp);
                    }
                    break;
            }
            if ($image) {
                header('Content-type: image/jpeg');
                readfile($image);
                return true;
            }
        }
    }

    return false;
}
?>
