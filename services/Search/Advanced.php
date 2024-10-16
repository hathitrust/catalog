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

  public $facetDir;
    
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

        $interface->assign('fqor_language', array());
        $interface->assign('fqor_format', array());

        $interface->assign('ss', $this->getService());

        // Fill values when present
        $ss = $this->getService();
        if (count($ss->search) >= 1) {
            foreach($ss->search as $index => $value) {
                $suffix = $index + 1;
                $interface->assign("type{$suffix}", $value[0]);
                $interface->assign("lookfor{$suffix}", $value[1]);
                if (isset($value[2]) && $value[2]) {
                    $interface->assign("bool{$suffix}", $value[2]);
                }
            }
        }
        $interface->assign('ft', $ss->ftonly);
        
        $filters = $ss->activeInbandFilters();
        foreach($filters as $filter) {
            $key = $filter[0];
            $value = $filter[1];
            // print "<pre>"; print_r($value); print "</pre>";
            if ( $key == 'publishDateTrie' ) {
                if(is_array($value)) {
                    $value = implode(' TO ', $value);
                }
                if (preg_match('/^\[\s*\"?(.*?)\"?\s+TO\s+\"?(.*?)\"?\s*\].*$/', $value, $matcher)) {
                    $start = $matcher[1];
                    $end   = $matcher[2];
                    if ($start == '*') {
                        $interface->assign('endDate', $end);
                        $interface->assign('dateRangeInput', 'before');
                    }
                    if ($end == '*') {
                        $interface->assign('startDate', $start);
                        $interface->assign('dateRangeInput', 'after');
                    }
                    if ($start == $end) {
                        $interface->assign('date', $start);
                        $interface->assign('dateRangeInput', 'in');
                    } else if ( $start != '*' && $end != '*' ) {
                        $interface->assign('startDate', $start);
                        $interface->assign('endDate', $end);
                        $interface->assign('dateRangeInput', 'between');
                    }
                }
            } else {
                $interface->assign("fqor_{$key}", $value);
            }
        }
        
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

        // and now the locations!
        $locationslist = array();
        $locationshandle = fopen($this->facetDir . '/locations.txt', 'r');
        while (!feof($locationshandle)) {
          $locationslist[] = stream_get_line($locationshandle, 1000000, "\n");
        }
        fclose($locationshandle);

        $interface->assign('formatList', $formatlist);
        $interface->assign('languageList', $languages);
        $interface->assign('locationsList', $locationslist);

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
