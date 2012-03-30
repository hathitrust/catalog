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

class History extends Action {
    
    function launch()
    {
        global $interface;
        
        $interface->setPageTitle('Search History');
        
        // Retrive search history from cookie
        if (isset($_COOKIE['search'])) {
            $search = unserialize($_COOKIE['search']);

            // build an array of search phrase, type and format
            $links = array();
            foreach($search as $value) {
                $value = urldecode($value);
                $var = explode('&', $value);
                foreach($var as $v) {
                    $url = str_replace('"', '%22', $value);
                    $urlArray = explode('=', $v);
                    if ($urlArray[0] == 'lookfor') {
                        $links[$url]['phrase'] = $urlArray[1];
                    } elseif ($urlArray[0] == 'type') {
                        $links[$url]['type'] = $urlArray[1];
                    } elseif ($urlArray[0] == 'format[]') {
                        if ($links[$url]['format']) {
                            $links[$url]['format'] = $links[$url]['format'] .
                                                     ', ' . $urlArray[1];
                        } else {
                            $links[$url]['format'] = $links[$url]['format'] .
                                                     $urlArray[1];
                        }
                    }
                }
            }

            $interface->assign('links', array_reverse($links));
        }

        $interface->setTemplate('history.tpl');
        $interface->display('layout.tpl');
    }
}

?>