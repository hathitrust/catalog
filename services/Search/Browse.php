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
require_once 'XML/Unserializer.php';

class Browse extends Action {
    
    function launch()
    {
        global $interface;
        global $configArray;

        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $db = new $class($configArray['Index']['url']);
        if ($configArray['System']['debug']) {
            $db->debug = true;
        }

        $interface->setPageTitle('Browse the Collection');
        $interface->setTemplate('browse.tpl');
            
        // Get Tags for Tag Cloud
        require_once('services/Search/TagCloud.php');
        $data = getTagCloud();
        $interface->assign("tagCloud", $data);
        
        // Get Subject Facets
        $db->raw = true;
        $result = $db->search('collection:Catalog', null, null, 0, null,
                              array('limit' => 10,
                                    'field' => array('topicStr',
                                                     'genreStr',
                                                     'geographicStr',
                                                     'language',
                                                     'authorStr',
                                                     'format')),
                              null, 'score');
    	$unxml = new XML_Unserializer(array('parseAttributes' => true));
    	$result = $unxml->unserialize($result);
    	if (!PEAR::isError($result)) {
            $data = $unxml->getUnserializedData();
        } else {
            PEAR::raiseError($result);
        }
        $interface->assign('subjectList', $data['Facets']['Cluster']);
        
        $interface->display('layout.tpl');
    }
}

?>