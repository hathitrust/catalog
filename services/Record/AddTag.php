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

require_once 'services/MyResearch/lib/User.php';
require_once 'services/MyResearch/lib/Tags.php';
require_once 'services/MyResearch/lib/Resource_tags.php';

class AddTag extends Action {

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
            $interface->assign('followupAction', 'AddTag');
            if (isset($_GET['lightbox'])) {
                $interface->assign('title', $_GET['message']);
                $interface->assign('message', 'You must be logged in first');
                return $interface->fetch('AJAX/login.tpl');
            } else {
                $interface->assign('title', $this->details['title']);
                $interface->setPageTitle('You must be logged in first');
                $interface->assign('subTemplate', 'login.tpl');
                $interface->setTemplate('view-alt.tpl');
                $interface->display('layout.tpl', 'AddTag' . $_GET['id']);
            }
            exit();
        }

        if (isset($_POST['submit'])) {
            $result = $this->save();
        } else {
            return $this->display();
        }
    }
    
    function display()
    {
        global $interface;

        // Display Page
        $interface->assign('id', $_GET['id']);
        if (isset($_GET['lightbox'])) {
            $interface->assign('title', $_GET['message']);
            return $interface->fetch('Record/addtag.tpl');
        } else {
            $interface->assign('title', $this->details['title']);
            $interface->setPageTitle('Add Tag');
            $interface->assign('subTemplate', 'addtag.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl', 'AddTag' . $_GET['id']);
        }
    }
    
    function save()
    {
        //Check if tag is already in the database
        $tag = new tags();
        $tag->tag = $_GET['tag'];
        if ($tag->find(true)) {
            $tagId = $tag->id;
        } else {
            $tagId = $tag->insert();
        }

        // Add tag to record
        $resource_tags = new Resource_tags();
        $resource_tags->record_id = $_GET['id'];
        $resource_tags->tag_id = $tagId;
        $resource_tags->user_id = $this->user->id;
        if (!$resource_tags->find()) {
            $resource_tags->insert();
        }
    }
}

?>