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
require_once 'sys/VFUser.php';

class ILL extends MyResearch
{
  function launch()
  {
    global $configArray;
    global $interface;
    global $db;

    $user = VFUser::singleton();
       
    $interface->setTemplate('ill.tpl');
    $interface->setPageTitle('My Interlibrary Loan Transactions');
    $interface->display('layout.tpl');
  }
}

?>
