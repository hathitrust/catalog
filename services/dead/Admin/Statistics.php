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

class Statistics extends Action
{
    function launch()
    {
        global $configArray;
        global $interface;

        // Load SOLR Statistics
        $url = $configArray['Index']['url'];
        $solr = new Solr($url, 'stats');
        if ($configArray['System']['debug']) {
            $solr->debug = true;
        }

        // All Statistics
        $solr->raw = true;
        $result = $solr->search('*:*', null, 0, 0, null,
                                array('field' => array('ipaddress', 'browser')),
                                null, null, 'GET');
        $options = array('parseAttributes' => true,
                         'attributesArray' => 'attributes');
    	$unxml = new XML_Unserializer($options);
    	$result = $unxml->unserialize($result);
    	if (!PEAR::isError($result)) {
            $data = $unxml->getUnserializedData();
            foreach ($data['Facets']['Cluster'] as $cluster) {
                if ($cluster['attributes']['name'] == 'ipaddress') {
                    $interface->assign('ipList', $cluster['item']);
                } else if ($cluster['attributes']['name'] == 'browser') {
                    $interface->assign('browserList', $cluster['item']);
                }
            }
    	}

        
        // Search Statistics
        $solr->raw = true;
        $result = $solr->search('phrase:[* TO *]', null, 0, 0, null,
                                array('field' => array('noresults', 'phrase')),
                                null, null, 'GET');
        $options = array('parseAttributes' => true,
                         'attributesArray' => 'attributes');
    	$unxml = new XML_Unserializer($options);
    	$result = $unxml->unserialize($result);
    	if (!PEAR::isError($result)) {
            $data = $unxml->getUnserializedData();
            $interface->assign('searchCount', $data['RecordCount']);
            $interface->assign('nohitCount', $data['Facets']['Cluster'][0]['item']['attributes']['count']);
            $interface->assign('termList', $data['Facets']['Cluster'][1]['item']);
    	}

        // Record View Statistics
        $solr->raw = true;
        $result = $solr->search('recordId:[* TO *]', null, 0, 0, null,
                                array('field' => array('recordId')),
                                null, null, 'GET');
        $options = array('parseAttributes' => true,
                         'attributesArray' => 'attributes');
    	$unxml = new XML_Unserializer($options);
    	$result = $unxml->unserialize($result);
    	if (!PEAR::isError($result)) {
            $data = $unxml->getUnserializedData();
            $interface->assign('recordViews', $data['RecordCount']);
            $interface->assign('recordList', $data['Facets']['Cluster']['item']);
    	}

        $interface->setTemplate('statistics.tpl');
        $interface->display('layout-admin.tpl');
    }
}

?>