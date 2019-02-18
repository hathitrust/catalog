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

require_once 'services/MyResearch/lib/Tags.php';

class Tag extends Action {
    
    function launch()
    {
        global $interface;

        if (isset($_GET['findby'])) {
            $interface->assign('findby', $_GET['findby']);
            $tagList = array();
            $tag = new Tags();
            $sql = "SELECT tags.tag, COUNT(resource_tags.id) as cnt FROM tags, resource_tags WHERE tags.id = resource_tags.tag_id GROUP BY tags.tag";
            switch ($_GET['findby']) {
                case 'alphabetical':
                    $sql .= " ORDER BY tags.tag, cnt DESC";
                    break;
                case 'popularity':
                    $sql .= " ORDER BY cnt DESC, tags.tag";
                    break;
                case 'recent':
                    $sql .= " ORDER BY resource_tags.posted, cnt DESC, tags.tag";
                    break;
            }
            $sql .= " LIMIT 50";
            $tag->query($sql);
            if ($tag->N) {
                while ($tag->fetch()) {
                    $tagList[] = clone($tag);
                }
            }

            $interface->assign('tagList', $tagList);

        }

        $interface->setPageTitle('Browse the Collection');
        $interface->setTemplate('tag.tpl');
        $interface->display('layout.tpl');
    }
}

?>