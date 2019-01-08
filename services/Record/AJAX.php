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

global $configArray;

class AJAX extends Action {

    function AJAX()
    {
    }
    
    function launch()
    {
        header ('Content-type: text/xml');
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past

        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?' . ">\n";
        $xmlResponse .= "<AJAXResponse>\n";
        if (is_callable(array($this, $_GET['method']))) {
            $xmlResponse .= $this->$_GET['method']();
        } else {
            $xmlResponse .= '<Error>Invalid Method</Error>';
        }
        $xmlResponse .= '</AJAXResponse>';
        
        echo $xmlResponse;        
    }

    function GetSFXData()
    {
        global $configArray;

        $url = $configArray['SFX']['api'];

        $client = new HTTP_Request2();
        $client->setMethod('GET');
        $client->setURL($url . '&rft:issn=' . $_GET['issn']);
        $result = $client->send();
        $data = $client->getResponseBody();
        
        $xsl = new XSLTProcessor();
        $style = new DOMDocument;
        $style->load('xsl/sfx-menu.xsl');
        $xsl->importStyleSheet($style);

        $xml = new DOMDocument;
        $xml->loadXML($data);

        return $xsl->transformToXML($xml);
    }

    function IsLoggedIn()
    {
        require_once 'services/MyResearch/lib/User.php';

        if(UserAccount::isLoggedIn()) {
        // if (isset($_COOKIE['userinfo'])) {
            return "<result>True</result>";
        } else {
            return "<result>False</result>";
        }
    }

    // Saves a Record to User's Account
    function SaveRecord()
    {
        require_once 'services/Record/Save.php';

        if (isset($_COOKIE['userinfo'])) {
            $saveService = new Save();
            $result = $saveService->saveRecord($_GET['id']);
            if (!PEAR::isError($result)) {
                return "<result>Done</result>";
            } else {
                return "<result>Error</result>";
            }
        } else {
            return "<result>Unauthorized</result>";
        }
    }

    function GetSaveStatus()
    {
        require_once 'services/MyResearch/lib/User.php';
        require_once 'services/MyResearch/lib/Resource.php';

        // check if user is logged in
        $user = UserAccount::isLoggedIn();
        if (!$user) {
            return "<result>Unauthorized</result>";
        }

        // Check if resource is saved to favorites
        $resource = new Resource();
        $resource->record_id = $_GET['id'];
        if ($resource->find(true)) {
            if ($user->hasResource($resource)) {
                return '<result>Saved</result>';
            } else {
                return '<result>Not Saved</result>';
            }
        } else {
            return '<result>Not Saved</result>';
        }
    }

    // Email Record
    function SendEmail()
    {
        require_once 'services/Record/Email.php';

        $emailService = new Email();
        $result = $emailService->sendEmail($_GET['to'], $_GET['from'], $_GET['message']);

        if (PEAR::isError($result)) {
            return '<result>Error</result>';
        } else {
            return '<result>Done</result>';
        }
    }

    // SMS Record
    function SendSMS()
    {
        require_once 'services/Record/SMS.php';
        $result = SMS::sendSMS();
        
        if (PEAR::isError($result)) {
            return '<result>Error</result>';
        } else {
            return '<result>Done</result>';
        }
    }

    function GetTags()
    {
        require_once 'services/MyResearch/lib/Resource.php';

        $return = "<result>\n";

        $resource = new Resource();
        $resource->record_id = $_GET['id'];
        if ($resource->find(true)) {
            $tagList = $resource->getTags();
            foreach ($tagList as $tag) {
                $return .= "  <Tag>" . $tag->tag . "</Tag>\n";
            }
        }
        
        $return .= '</result>';
        return $return;
    }

    function SaveTag()
    {
        require_once 'services/MyResearch/lib/Resource.php';
    
        if (isset($_COOKIE['userinfo'])) {
            $user = unserialize($_COOKIE['userinfo']);
        } else {
            return "<result>Unauthorized</result>";
        }

        $resource = new Resource();
        $resource->record_id = $_GET['id'];
        if (!$resource->find(true)) {
            $resource->insert();
        }
        
        preg_match_all('/"[^"]*"|[^ ]+/', $_REQUEST['tag'], $words);
        foreach ($words[0] as $tag) {
            $tag = str_replace('"', '', $tag);
            $resource->addTag($tag, $user);
        }

        return '<result>Done</result>';
    }
    
    function SaveComment()
    {
        require_once 'services/MyResearch/lib/Resource.php';
        
        if (isset($_COOKIE['userinfo'])) {
            $user = unserialize($_COOKIE['userinfo']);
        } else {
            return "<result>Unauthorized</result>";
        }

        $resource = new Resource();
        $resource->record_id = $_GET['id'];
        if (!$resource->find(true)) {
            $resource->insert();
        }
        $resource->addComment($_REQUEST['comment'], $user);

        return '<result>Done</result>';
    }
    
    function GetComments()
    {
        require_once 'services/MyResearch/lib/Resource.php';
        require_once 'services/MyResearch/lib/Comments.php';

        $resource = new Resource();
        $resource->record_id = $_GET['id'];
        if ($resource->find(true)) {
            $commentList = $resource->getComments();
        }

        $output = "<result>\n" .
                  "  <CommentList>\n";
        foreach ($commentList as $comment) {
            $output .= "    <Comment by=\"" . $comment->fullname . "\" on=\"" . $comment->created . "\">$comment->comment</Comment>\n";
        }
        $output .= "  </CommentList>\n" .
                   '</result>';
                   
        return $output;
    }
    
}
?>