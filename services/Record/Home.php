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

//require_once 'sys/SolrStats.php';

class Home extends Record
{
    function launch()
    {
        global $configArray;
        
        // if ($configArray['Statistics']['enabled']) {
        //     // Setup Statistics Index Connection
        //     $solrStats = new SolrStats($configArray['Statistics']['solr']);
        //     if ($configArray['System']['debug']) {
        //         $solrStats->debug = true;
        //     }
        // 
        //     // Save Record View
        //     $solrStats->saveRecordView($this->id);
        //     unset($solrStats);
        // }

        // Execute Default Tab
        require_once 'Holdings.php';
        $h = new Holdings();
	$h->launch();
    }
    try {
    $phar = new Phar('/app/vendor/geoip/geoip2.phar');
    echo $phar->getVersion();
} catch (PharException $e) {
    // Handle error, e.g. archive not found or invalid
    echo $e->getMessage();
}
}

?>