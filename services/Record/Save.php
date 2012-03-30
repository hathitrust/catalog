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

require_once 'services/MyResearch/lib/Resource.php';
require_once 'services/MyResearch/lib/User.php';

class Save extends Action
{
    private $user;

    function __construct()
    {
        if (isset($_COOKIE['userinfo'])) {
            $this->user = unserialize($_COOKIE['userinfo']);
        }
    }

    function launch()
    {
        global $interface;
        global $configArray;

        // Check if user is logged in
        if (!$this->user) {
            $interface->assign('recordId', $_GET['id']);
            $interface->assign('followupModule', 'Record');
            $interface->assign('followupAction', 'Save');
            if (isset($_GET['lightbox'])) {
                $interface->assign('title', $_GET['message']);
                $interface->assign('message', 'You must be logged in first');
                return $interface->fetch('AJAX/login.tpl');
            } else {
                $interface->assign('title', $this->details['title']);
                $interface->setPageTitle('You must be logged in first');
                $interface->assign('subTemplate', 'login.tpl');
                $interface->setTemplate('view-alt.tpl');
                $interface->display('layout.tpl', 'RecordSave' . $_GET['id']);
            }
            exit();
        }

        // Get details if they exist
        if ($this->user->hasResource($_GET['id'])) {
            $myTagList = $this->user->getTags($_GET['id']);
            if (count($myTagList)) {
                $newTagList = array();
                foreach($myTagList as $myTag) {
                    $tagStr .= "$myTag->tag ";
                }
                $interface->assign('tags', $tagStr);
            }
            $saved = $this->user->getSavedData($_GET['id']);
            $interface->assign('notes', $saved->notes);
        }

        // Display Page
        $interface->assign('id', $_GET['id']);
        if (isset($_GET['lightbox'])) {
            $interface->assign('title', $_GET['message']);
            return $interface->fetch('Record/save.tpl');
        } else {
            $interface->assign('title', $this->details['title']);
            $interface->setPageTitle('Save this Record');
            $interface->assign('subTemplate', 'save.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl', 'RecordSave' . $_GET['id']);
        }
    }
    
    function saveRecord()
    {
        if ($this->user) {
            $resource = new Resource();
            $resource->record_id = $_GET['id'];
            if (!$resource->find(true)) {
                $resource->insert();
            }

            preg_match_all('/"[^"]*"|[^ ]+/', $_GET['tags'], $tagArray);
            $this->user->addResource($resource, $tagArray[0], $_GET['notes']);
        }
    }

}
?>