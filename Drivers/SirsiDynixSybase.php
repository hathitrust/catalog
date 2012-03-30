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

require_once 'Interface.php';

class SirsiDynixSybase implements DriverInterface
{
    private $db;

    function __construct()
    {
        // Load Configuration for this Module
        $configArray = parse_ini_file('conf/SirsiDynixSybase.ini', true);

        // Connect to database
        $this->db = sybase_pconnect($configArray['Catalog']['server_port'],
				    $configArray['Catalog']['username'],
				    $configArray['Catalog']['password']);


        // Select the databse
        sybase_select_db($configArray['Catalog']['database']);
    }

    public function getHolding($id)
    {
        // Query holding information based on 001 id
        $sql = "select distinct item.item# as NUMBER, item_status.descr as STATUS, " .
               "item.copy_reconstructed as ITEM_SEQUENCE_NUMBER, item.location as LOCATION, item.call_reconstructed as CALLNUMBER, " .
               "convert(varchar(12),dateadd(dd,item.due_date,'jan 1 1970'),7) as DUEDATE " .
               "from bib, item, item_status " .
               "where bib.bib# = $id " .
               "and bib.bib# = item.bib# " .
               "and item.item_status = item_status.item_status ";
        try {
            $holding = array();
            $sqlStmt = sybase_query($sql);
            while ($row = sybase_fetch_assoc($sqlStmt)) {
                $holding[] = array('status' => $row['STATUS'],
                                   'location' => $row['LOCATION'],
                                   //'reserve' => $row['ON_RESERVE'],
                                   'callnumber' => $row['CALLNUMBER'],
                                   'duedate' => $row['DUEDATE'],
                                   'number' => $row['ITEM_SEQUENCE_NUMBER']);
            }
            return $holding;
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }
    
    public function getHoldings($idList)
    {
        foreach ($idList as $id) {
            $holdings[] = $this->getHolding($id);
        }
        return $holdings;
    }

    public function getHoldingRecords($id)
    {
        return array();
    }

    public function getPurchaseHistory($id)
    {
        return array();
    }

}

?>