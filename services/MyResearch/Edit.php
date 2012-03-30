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

require_once "Action.php";

require_once 'Home.php';

class Edit extends Action
{
    function __construct()
    {
    }

    function launch($msg = null)
    {
        global $interface;
        global $configArray;

        $user = unserialize($_COOKIE['userinfo']);

        // Save Data
        if (isset($_POST['submit'])) {
            $resource = Resource::staticGet('record_id', $_GET['id']);
            preg_match_all('/"[^"]*"|[^ ]+/', $_POST['tags'], $tagArray);
            $user->addResource($resource, $tagArray[0], $_POST['notes']);
            Home::launch();
            exit();
        }

        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $db = new $class($configArray['Index']['url']);
        if ($configArray['System']['debug']) {
            $db->debug = true;
        }

        // Get Record Information
        $details = $db->search('id:' . $_GET['id']);
        $interface->assign('record', $details['record']);
        
        // Retrieve saved information about record
        $myTagList = $user->getTags($_GET['id']);
        if (count($myTagList)) {
            $newTagList = array();
            foreach($myTagList as $myTag) {
                if (strstr($myTag->tag, ' ')) {
                    $tagStr .= "\"$myTag->tag\" ";
                } else {
                    $tagStr .= "$myTag->tag ";
                }
            }
            $interface->assign('myTagList', $tagStr);
        }
        $saved = $user->getSavedData($_GET['id']);
        $interface->assign('savedData', $saved);

        $interface->setTemplate('edit.tpl');
        $interface->display('layout.tpl');
    }
}

?>