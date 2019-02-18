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

require_once 'services/MyResearch/MyResearch.php';
require_once 'sys/VFUser.php';
require_once 'sys/ActivityLog.php';

class Holds extends MyResearch
{
    function launch()
    {
        global $configArray;
        global $interface;
        
        $user = VFUser::singleton();

        if (isset($_POST['submit']) && isset($_POST['hold_rec_key'])) {
          $hold_rec_key = $_POST['hold_rec_key'];
          $num_removed = 0;
          error_log(print_r($_POST, true));
          foreach ($hold_rec_key as $hold_key) {
            $result = $this->catalog->removeHold($hold_key);
            if (PEAR::isError($result)) {
              $message = $result;
            } else {
              $num_removed++;
            }
          }
          if ($num_removed == 1)  $message = "Removed $num_removed hold";
          else $message = "Removed $num_removed holds";
          $alog = ActivityLog::singleton();
          $alog->log('removehold', $num_removed);
          $interface->assign('message', $message);
        }

        // Get My Transactions
        if ($this->catalog->status and isset($user->patron)) {
          $result = $this->catalog->getMyHolds($user->patron);
          if (!PEAR::isError($result)) {
            if (count($result['H'])) {
              // Setup Search Engine Connection
              $class = $configArray['Index']['engine'];
              $db = new $class($configArray['Index']['url']);
              if ($configArray['System']['debug']) {
                  $db->debug = true;
              }

              $recordList = array();
              foreach ($result['H'] as $row) {
                $record = $db->getRecord($row['id']);
                $record['description'] = $row['description'];
                $record['type'] = $row['type'];
                $record['call_num'] = $row['call_num'];
                $record['createdate'] = $row['create'];
                $record['expiredate'] = $row['expire'];
                $record['status'] = $row['status'];
                $record['pickup_loc'] = $row['pickup_loc'];
                $record['hold_rec_key'] = $row['hold_rec_key'];
                $recordList[] = $record;
              }
              $interface->assign('recordList', $recordList);
            } else {
                $interface->assign('recordList', 'You do not have any holds');
            }
          }
        }

        $interface->setTemplate('holds.tpl');
        $interface->setPageTitle('My Holds');
        $interface->display('layout.tpl');
    }
    
}

?>
