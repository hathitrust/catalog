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

require_once 'services/MyResearch/MyResearch.php';

class MyList extends MyResearch
{

    function launch()
    {
        global $configArray;
        global $interface;
        global $user;

        // Load List
        if (isset($_GET['id'])) {
            $list = User_list::staticGet('id', $_GET['id']);
            $interface->assign('list', $list);

            // Delete Resource
            if (isset($_GET['delete'])) {
                $resource = Resource::staticGet('record_id', $_GET['delete']);
                $list->removeResource($resource);
            }

            // Build List
            foreach ($list->getResources() as $resource) {
                if ($resource->record_id != '') {
                    $data = $user->getSavedData($resource->record_id);
                    $record = $this->db->getRecord($resource->record_id);
                    $resourceList[] = array('id' => $resource->record_id,
                                            'title' => $record['title'],
                                            'isbn' => $record['isbn'],
                                            'author' => $record['author'],
                                            'format' => $record['format'],
                                            'tags' => $user->getTags($resource->record_id),
                                            'notes' => $data->notes);
                }
            }
            $interface->assign('resourceList', $resourceList);
        }

        // Display Page
        $interface->assign('title', $list->title);
        if (isset($_GET['lightbox'])) {
            return $interface->fetch('MyResearch/list-lightbox.tpl');
        } else {
            $interface->setPageTitle('My Lists');
            $interface->assign('subTemplate', 'list.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl');
        }
    }
    
}

?>