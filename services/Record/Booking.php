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
 
require_once 'CatalogConnection.php';

require_once 'Action.php';

class Booking extends Action
{
    var $catalog;

    function launch()
    {
        global $configArray;

        if (isset($_GET['full_item_key'])) $full_item_key = $_GET['full_item_key'];
        if (isset($_POST['full_item_key'])) $full_item_key = $_POST['full_item_key'];
        $record_id = $_GET['id'];

        try {
            $this->catalog = new CatalogConnection($configArray['Catalog']['driver']);
        } catch (PDOException $e) {
            // What should we do with this error?
            if ($configArray['System']['debug']) {
                echo '<pre>';
                echo 'DEBUG: ' . $e->getMessage();
                echo '</pre>';
            }
        }

        // Check How to Process Hold
        if (method_exists($this->catalog->driver, 'placeBooking')) {
            $this->placeBooking();
        } elseif (method_exists($this->catalog->driver, 'getBookingLink')) {
            // Redirect user to Place Hold screen on ILS OPAC
            $link = $this->catalog->getBookingLink($_GET['full_item_key']);
            if (!PEAR::isError($link)) {
                header('Location:' . $link);
            } else {
                PEAR::raiseError($link);
            }
        } else {
            PEAR::raiseError(new PEAR_Error('Cannot Process Place Booking - ILS Not Supported'));
        }
    }
    
    function placeBooking()
    {
        global $interface;
        global $configArray;
        
        // not functional at umich

        $interface->setTemplate('booking.tpl');

        $interface->display('layout.tpl');
    }
}

?>
