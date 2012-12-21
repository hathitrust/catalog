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

class Holdings extends Record
{
  private $ru;
    function launch()
    {
        global $interface;
        global $configArray;
        global $user;

        // Do not cache holdings page
        $interface->caching = 0;
        $titleTitle = preg_replace('/\p{P}+$/', '', $this->record['title'][0]);

        $interface->setPageTitle('Catalog Record: ' . $titleTitle);

        $interface->assign('subTemplate', 'view-holdings.tpl');
        $interface->setTemplate('view.tpl');

        // Display Page
        $interface->display('layout.tpl');
    }

}

?>
