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

require_once 'services/MyResearch/lib/User.php';
require_once 'services/MyResearch/lib/Resource.php';
require_once 'sys/VFUser.php';

class MyResearch extends Action
{
    var $db;
    var $catalog;

    function loginAndReturnURL() {
      $authspecs = AuthSpecs::singleton();
      $returnto = rawurlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
      return $authspecs['RedirectAuth']['loginURLBase'] . $returnto;
    }

    function __construct()
    {
        global $interface;
        global $configArray;
        
        // Don't cache this stuff!!!
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');        
        
        $user = VFUser::singleton();
        
// global $user;
// $user = UserAccount::isLoggedin();

        if (!$user) {
           header("Location: " . self::loginAndReturnURL());
           exit();
        } else {
            // error_log("no need to log in");
        }
        
        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $this->db = new $class($configArray['Index']['url']);
        if ($configArray['System']['debug']) {
            $this->db->debug = true;
        }

        // Connect to Database
        $this->catalog = new CatalogConnection($configArray['Catalog']['driver']);
        $interface->assign('user', $user);
      }

      function formatDate($dd) {
        switch (strlen($dd)) {
          case 13:
            return date("m/d/Y g:ia", strtotime($dd));
          case 8:
            return date("m/d/Y", strtotime($dd));
          default:
            return $dd;
        }
      }

}

?>
