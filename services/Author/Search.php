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
require_once 'Pager/Pager.php';
require_once 'services/Search/SearchStructure.php';

class Search extends Action
{
    private $ss;
    private $db;
    private $limit;
    
    function launch($field='authorStr')
    {
        global $configArray;
        global $interface;

        $interface->caching = false;
        $interface->assign('lookfor', $_REQUEST['lookfor'][0]);

        // Get the SS and DB
        $this->ss = new SearchStructure();
        $interface->assign('searchcomps', $this->ss->asURL());
        
        $class = $configArray['Index']['engine'];
        
        $this->db = new $class($configArray['Index']['url']);

        // Define Page to Display
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $this->limit = $configArray['Site']['itemsPerPage'];
        $skip = ($page - 1) * $this->limit;

        
        
        //sort alphabetically by default
        $bsort = isset($_GET['bsort'])? $_GET['bsort'] : 'count';
        $interface->assign('sort', $bsort);

        # Get the search results  (total => numFound, values=>((value1, count1), (value2, count2), ...)),
        
        $ss = new SearchStructure(true);
        $ss->search[] = array('authorSuggest', $_REQUEST['lookfor'][0]);
        $list = $this->db->facetlist($ss, $field, $bsort, $skip, $this->limit);
        $interface->assign('values', $list['values'][$field]);
        $interface->assign('start', $skip + 1);
        $interface->assign('end', $skip + $this->limit);
        $interface->assign('total', $list['total']);   
        
        $interface->assign('type', 'author');
             

        $link = '/Author/Search?' . $this->ss->asURL() . "&bsort=$bsort&page=%d";
        $options = array('totalItems' => $list['total'],
                         'mode' => 'sliding',
                         'path' => '',
                         'fileName' => $link,
                         'delta' => 5,
                         'perPage' => $this->limit,
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

        $interface->setPageTitle('Author Browse');
        $interface->setTemplate('list.tpl');
        $interface->display('layout.tpl');
    }

}
?>