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
 
require_once 'Record.php';

require_once 'services/MyResearch/lib/Comments.php';

class UserComments extends Record
{
    function launch()
    {
        global $interface;
        global $user;
        
        // Process Delete Comment
        if ((isset($_GET['delete'])) && (is_object($user))) {
            $comment = Comments::staticGet('id', $_GET['delete']);
            if ($user->id == $comment->user_id) {
                $comment->delete();
            }
        }

        //if (!$interface->is_cached($this->cacheId)) {
            $interface->assign('user', $user);
        
            $interface->setPageTitle('Comments: ' . $this->record['title'][0]);

            $resource = new Resource();
            $resource->record_id = $_GET['id'];
            if ($resource->find(true)) {
                $commentList = $resource->getComments();
                $interface->assign('commentList', $commentList);
            }

            $interface->assign('subTemplate', 'view-comments.tpl');
            $interface->setTemplate('view.tpl');
        //}

        // Display Page
        $interface->display('layout.tpl', $cacheId);
    }
}

?>
