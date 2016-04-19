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

require_once 'CatalogConnection.php';

require_once 'Pager/Pager.php';

class NewItem extends Action {
    
    function launch()
    {
        global $configArray;
        global $interface;

        $catalog = new CatalogConnection($configArray['Catalog']['driver']);
        if (!$catalog->status) {
            PEAR::raiseError(new PEAR_Error('Cannot Load Catalog Driver'));
        }

        if (isset($_GET['module']) && isset($_GET['action'])) {
            // Must have atleast Action and Module set to continue
            $interface->setPageTitle('New Item Search Results');
            $interface->setTemplate('newitem-list.tpl');

            // Fetch New Items
            $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
            $limit = 20;
            $newItems = $catalog->getNewItems($page, $limit, $_GET['range'], $_GET['department']);
            
            // Setup Search Engine Connection
            $class = $configArray['Index']['engine'];
            $db = new $class($configArray['Index']['url']);
            if ($configArray['System']['debug']) {
                $db->debug = true;
            }

            // Query Index for BIB Data
            for ($i=0; $i<count($newItems['results']); $i++) {
                if ($i) {
                    $query .= ' OR ';
                }
                $query .= 'id:' . $newItems['results'][$i]['id'];
            }
            $result = $db->search($query);
            $interface->assign('recordSet', $result['record']);

            // Setup Record Count Display
            $interface->assign('recordCount', $newItems['count']);
            $interface->assign('recordStart', (($page-1)*$limit)+1);
            if ($newItems['count'] < $limit) {
                $interface->assign('recordEnd', $newItems['count']);
            } else if (($page*$limit) > $newItems['count']) {
                $interface->assign('recordEnd', $newItems['count']);
            } else {
                $interface->assign('recordEnd', $page*$limit);
            }

            // Setup Paging
            $link = (strstr($_SERVER['REQUEST_URI'], 'page=')) ?
                        str_replace('page=' . $_GET['page'], '', $_SERVER['REQUEST_URI']) . 'page=%d' :
                        $_SERVER['REQUEST_URI'] . '&page=%d';
            $link = strstr($link, '/Search');
            $options = array('totalItems' => $newItems['count'],
                             'mode' => 'sliding',
                             'path' => '/',
                             'fileName' => $link,
                             'delta' => 5,
                             'perPage' => 20,
                             'nextImg' => 'Next &raquo;',
                             'prevImg' => '&laquo; Prev',
                             'separator' => '',
                             'spacesBeforeSeparator' => 0,
                             'spacesAfterSeparator' => 0,
                             'append' => false,
                             'clearIfVoid' => true,
                             'urlVar' => 'page',
                             'curPageSpanPre' => '<span>',
                             'curPageSpanPost' => '</span>');
            $pager =& Pager::factory($options);
            $interface->assign('pager', $pager);
        } else {
            $interface->setPageTitle('New Item Search');
            $interface->setTemplate('newitem.tpl');
            
            $list = $catalog->getFunds();
            $interface->assign('fundList', $list);
        }
        $interface->display('layout.tpl');
    }
}

?>