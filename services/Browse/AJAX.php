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

class AJAX extends Action {

    private $db;

    function __construct()
    {
        global $configArray;
        
        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $this->db = new $class($configArray['Index']['url']);
        if ($configArray['System']['debug']) {
            $this->db->debug = true;
        }
        $this->db->raw = true;

    }

    function launch()
    {
        header ('Content-type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?' . ">\n";
        echo "<AJAXResponse>\n";
        if (is_callable(array($this, $_GET['method']))) {
            $this->$_GET['method']();
        } else {
            echo '<Error>Invalid Method</Error>';
        }
        echo '</AJAXResponse>';
    }

    function GetOptions()
    {
        $q = ($_GET['query']) ? $_GET['query'] : 'collection:Catalog';
        
        if ($_GET['field']) {
            $result = $this->db->search($q, null, null, 0, null,
                                        array('limit' => $limit,
                                              'field' => $_GET['field']),
                                        null, 'score');
        } else {
            $result = $this->db->search($q);
        }

        echo strstr($result, "\n");
    }

    function GetAlphabet()
    {
        $q = ($_GET['query']) ? $_GET['query'] : 'collection:Catalog';

        $result = $this->db->search($q, null, null, 0, null,
                                    array('limit' => $limit,
                                          'field' => $_GET['field'],
                                          'sort' => 'false'),
                                    null, 'score');

        echo strstr($result, "\n");
    }
    function GetSubjects()
    {
        $q = ($_GET['query']) ? $_GET['query'] : 'collection:Catalog';

        $result = $this->db->search($q, null, null, 0, null,
                                    array('limit' => $limit,
                                          'field' => $_GET['field']),
                                    null, 'score');

        echo strstr($result, "\n");
    }

}
?>