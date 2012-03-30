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

require_once 'services/Search/Home.php';
require_once 'sys/ActivityLog.php';

class Advanced extends Home {

  public $facetDir = '/n1/vufind/facetlists';
    
    function launch()
    {
        global $interface;
        global $configArray;
        global $instConfig;
        
        
        // Change the facet dir if we have it
        if (isset($configArray['Site']['facetDir'])) {
	  $this->facetDir = $configArray['Site']['facetDir'];
	}
        
        $this->setup();
        
        //Log it
        
        $alog = ActivityLog::singleton();
        $alog->log('advpage');
        
        //Suppress the basic searchbox
        
        $interface->assign('suppress_searchbox', true);
     
        // Set the defaults for the searches
        
        $interface->assign('bool1', 'AND');
        $interface->assign('bool2', 'AND');
        $interface->assign('bool3', 'AND');
                                       
        $interface->assign('type1', 'all');
        $interface->assign('type2', 'author');
        $interface->assign('type3', 'title');
        $interface->assign('type4', 'subject');
        
        $languages = array(); 
        $langhandle = fopen($this->facetDir . '/language.txt', 'r');
        while (!feof($langhandle)) {
            $languages[] = stream_get_line($langhandle, 1000000, "\n");
        }
        fclose($langhandle);


        // ...And the formats
        $formatlist = array();
        $formathandle = fopen($this->facetDir . '/format.txt', 'r');
        while (!feof($formathandle)) {
          $formatlist[] = stream_get_line($formathandle, 1000000, "\n");
        }
        fclose($formathandle);

        // get location/collection info from config file
        $locColl = Horde_Yaml::load(file_get_contents('conf/locColl.yaml'));
        $instLocs = Horde_Yaml::load(file_get_contents('conf/instLocs.yaml'));
        $interface->assign('locColl', $locColl);
        $interface->assign('locCollJSON', json_encode($locColl));
        $interface->assign('instLocsJSON', json_encode($instLocs));

        $interface->assign('formatList', $formatlist);
        $interface->assign('languageList', $languages);

        // following is now done in index.php--tlp
        //if (isset($_GET['inst']) and $_GET['inst'] != '') {
        //   $interface->assign('inst', $_GET['inst']);
        // }

        $interface->setPageTitle('Advanced Search');
        $interface->setTemplate('advanced.tpl');
        $interface->display('layout.tpl');
    }
}
?>
