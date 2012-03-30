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

class Reserves extends Action {
    
    function launch()
    {
        global $interface;
        global $configArray;

        $catalog = new CatalogConnection($configArray['Catalog']['driver']);
        if (!$catalog->status) {
            PEAR::raiseError(new PEAR_Error('Cannot Load Catalog Driver'));
        }

        if (isset($_GET['module']) && isset($_GET['action'])) {
            // Must have atleast Action and Module set to continue
            $interface->setPageTitle('Reserves Search Results');
            $interface->assign('subpage', 'Search/list-list.tpl');
            $interface->setTemplate('reserves-list.tpl');

            $result = $catalog->findReserves($_GET['course'], $_GET['inst'], $_GET['dept']);

            $query = '';
            foreach ($result as $record) {
                if ($query != '') {
                    $query .= ' OR ';
                }
                $query .= 'id:' . $record['BIB_ID'];
            }

            // Define Page to Display
            $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
            $limit = 20;

            // Setup Search Engine Connection
            $class = $configArray['Index']['engine'];
            $db = new $class($configArray['Index']['url']);
            if ($configArray['System']['debug']) {
                $db->debug = true;
            }
            $result = $db->search($query);

            $interface->assign('recordSet', $result['record']);
            $interface->assign('recordCount', $result['RecordCount']);
            $interface->assign('recordStart', (($page-1)*$limit)+1);
            if ($result['RecordCount'] < $limit) {
                $interface->assign('recordEnd', $result['RecordCount']);
            } else if (($page*$limit) > $result['RecordCount']) {
                $interface->assign('recordEnd', $result['RecordCount']);
            } else {
                $interface->assign('recordEnd', $page*$limit);
            }

            $link = (strstr($_SERVER['REQUEST_URI'], 'page=')) ?
                        str_replace('page=' . $_GET['page'], '', $_SERVER['REQUEST_URI']) . 'page=%d' :
                        $_SERVER['REQUEST_URI'] . '&page=%d';
            $link = strstr($link, '/Search');
            $options = array('totalItems' => $result['RecordCount'],
                             'mode' => 'sliding',
                             'path' => $configArray['Site']['url'],
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
            $interface->setPageTitle('Reserves Search');
            if ($catalog->status) {
                $startDate = getSemesterStartDate();
                $interface->assign('deptList', $catalog->getDepartments($startDate));
                $interface->assign('instList', $catalog->getInstructors($startDate));
                $interface->assign('courseList', $catalog->getCourses($startDate));
            }
            $interface->setTemplate('reserves.tpl');
        }
        $interface->display('layout.tpl');
    }
    
}

function getSemesterStartDate()
{
    $year = date('Y');
    if (date('n') < 5) {
        $month = '01';
        $day = '01';
    } elseif (date('n') < 9) {
        $month = '05';
        $day = '15';
    } else {
        $month = '09';
        $day = '01';
    }

    return "$year-$month-$day";
}
?>