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

class Records extends Action
{
    private $db;

    function launch()
    {
        global $configArray;
        global $interface;

        if (isset($_GET['util'])) {
            // Setup Search Engine Connection
            $class = $configArray['Index']['engine'];
            $url = $configArray['Index']['url'];
            $this->db = new $class($url);
            if ($configArray['System']['debug']) {
                $this->db->debug = true;
            }

        
            $this->$_GET['util']();
        } else {
            $interface->setTemplate('records.tpl');
            $interface->display('layout-admin.tpl');
        }
    }

    function editRecord()
    {
        global $interface;

        $record = $this->db->getRecord($_GET['id']);
        $interface->assign('record', $record);

        $interface->setTemplate('record-edit.tpl');
        $interface->display('layout-admin.tpl');
    }

    function deleteRecord()
    {
        global $interface;
        
        $this->db->deleteRecord($_GET['id']);
        $this->db->commit();
        //$this->db->optimize();
        
        $interface->setTemplate('records.tpl');
        $interface->display('layout-admin.tpl');
    }

    function deleteSuppressed()
    {
        global $interface;
        global $configArray;

        ini_set('memory_limit', '50M');
        ini_set('max_execution_time', '3600');

        // Make ILS Connection
        try {
            $catalog = new CatalogConnection($configArray['Catalog']['driver']);
        } catch (PDOException $e) {
            // What should we do with this error?
            if ($configArray['System']['debug']) {
                echo '<pre>';
                echo 'DEBUG: ' . $e->getMessage();
                echo '</pre>';
            }
        }

        /*
        // Display Progress Page
        $interface->display('loading.tpl');
        ob_flush();
        flush();
        */

        // Get Suppressed Records and Delete from index
        $deletes = array();
        if ($catalog->status) {
            $result = $catalog->getSuppressedRecords();
            if (!PEAR::isError($result)) {
                $status = $this->db->deleteRecords($result);

                /*
                // Update Loading Page
                $message = "Loading Result List";
                echo '<Script language="JavaScript" type="text/javascript">' .
                     "if (document.getElementById) document.getElementById('statusLabel').innerHTML = '$message';\n" .
                     "if (document.all) document.all['statusLabel'].innerHTML = '$message';\n" .
                     "if (document.layers) document.layers['statusLabel'].innerHTML = '$message';\n" .
                     '</script>';
                ob_flush();
                flush();
                */
                
                $this->db->commit();
                
                /*
                // Update Loading Page
                $message = "Loading Result List";
                echo '<Script language="JavaScript" type="text/javascript">' .
                     "if (document.getElementById) document.getElementById('statusLabel').innerHTML = '$message';\n" .
                     "if (document.all) document.all['statusLabel'].innerHTML = '$message';\n" .
                     "if (document.layers) document.layers['statusLabel'].innerHTML = '$message';\n" .
                     '</script>';
                ob_flush();
                flush();
                */

                $this->db->optimize();
            }
        } else {
            PEAR::raiseError(new PEAR_Error('Cannot connect to ILS'));
        }

        $interface->assign('resultList', $deletes);

        $interface->setTemplate('grid.tpl');
        $interface->display('layout-admin.tpl');
    }
}

?>