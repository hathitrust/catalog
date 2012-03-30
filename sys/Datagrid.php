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

require_once 'Structures/DataGrid.php';

// Datagrid Extension Class
class DataGrid extends Structures_DataGrid {
    function DataGrid($limit = null, $page = 1)
    {
        parent::Structures_DataGrid($limit, $page);

        // Define DataGrid Color Attributes
        $this->renderer->setTableEvenRowAttributes(array('class' => 'evenrow'));
        $this->renderer->setTableOddRowAttributes(array('class' => 'oddrow'));

        // Define DataGrid Table Attributes
        $this->renderer->setTableAttribute('cellspacing', '0');
        $this->renderer->setTableAttribute('cellpadding', '2');
        $this->renderer->setTableAttribute('class', 'datagrid');
        $this->renderer->sortIconASC = '&uarr;';
        $this->renderer->sortIconDESC = '&darr;';
    }
}

?>