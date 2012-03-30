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
require_once 'services/MyResearch/lib/Search.php';

global $configArray;

// Configures DB_DataObject library
define('DB_DATAOBJECT_NO_OVERLOAD', 0);
$options =& PEAR::getStaticProperty('DB_DataObject', 'options');
$options = $configArray['Database'];

$user = new User;
$user->username = $_COOKIE['user']; // USE COOKIE
if($user->find(true) < 1) {
    PEAR::RaisError("Unknown User.");
}

class SaveSearch extends Action
{
    function SaveSearch()
    {
    }
    
    function launch()
    {
    
        global $user;
        global $configArray;
        
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
        //header("Location: {$configArray['Site']['path']}/Record/{$_GET['id']}");
    }
}

?>