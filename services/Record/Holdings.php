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

require_once 'Record.php';
require_once 'services/Record/RecordUtils.php';

class Holdings extends Record
{
    function launch()
    {
        global $interface;
        global $configArray;
        global $user;

        // Do not cache holdings page
        $interface->caching = 0;
        $titleTitle = preg_replace('/\p{P}+$/', '', $this->record['title'][0]);

        $interface->setPageTitle('Catalog Record: ' . $titleTitle);
        $ru = new RecordUtils();

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

        // Get Holdings Data
        $result['record'][] = $this->record;
        $holdings = $ru->getStatuses($result);
        $interface->assign('holdings', $holdings[$this->id]);

        // Get Acquisitions Data
        $result = $catalog->getPurchaseHistory($this->id);
        if (PEAR::isError($result)) {
            PEAR::raiseError($result);
        }
        $interface->assign('history', $result);

        if (isset($configArray['Catalog']['showBooking']))
          $interface->assign('showBooking', $configArray['Catalog']['showBooking']);

        $interface->assign('subTemplate', 'view-holdings.tpl');
        $interface->setTemplate('view.tpl');

        // Display Page
        $interface->display('layout.tpl');
    }

}

?>
