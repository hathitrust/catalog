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

class LCC extends Action {
    
    function launch()
    {
        global $interface;
        global $configArray;

        $defaultList = array(
              'A' => 'General Works',
              'B' => 'Philosophy, Psychology, and Religion',
              'C' => 'Historical Sciences',
              'D' => 'World History',
              'E' => 'United States History',
              'F' => 'General American History',
              'G' => 'Geography, Anthropology, and Recreation',
              'H' => 'Social Science',
              'J' => 'Political Science',
              'K' => 'Law',
              'L' => 'Education',
              'M' => 'Music',
              'N' => 'Fine Arts',
              'P' => 'Language and Literature',
              'Q' => 'Science',
              'R' => 'Medicine',
              'S' => 'Agriculture',
              'T' => 'Technology',
              'U' => 'Military Science',
              'V' => 'Naval Science',
              'Z' => 'Library Science');
        $interface->assign('defaultList', $defaultList);

        $interface->setPageTitle('Browse the Collection');
        $interface->setTemplate('lcc.tpl');
        $interface->display('layout.tpl');
    }
}

?>