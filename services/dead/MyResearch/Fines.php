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

class Fines extends MyResearch
{
  function launch()
  {
    global $configArray;
    global $interface;
    global $db;

    $user = VFUser::singleton();
       
    // Get My Transactions
    if ($this->catalog->status and isset($user->patron)) {
      $result = $this->catalog->getMyFines($user->patron);
      if (!PEAR::isError($result)) {
        if (count($result)) {
          $transList = array();
          foreach ($result as $data) {
            $record = $this->db->getRecord($data['id']);
            $barcode = $data['barcode'];
            $date = $data['date'];
            //$fdate = substr($date,4,2) . '/' .  substr($date,6,2) . '/' . substr($date,0,4);
            $fdate = $this->formatDate($date);
            $transList[] = array(
                'id' => $data['id'],
                'isbn' => isset($record['isbn'])? $record['isbn'] : NULL,
                'issn' => isset($record['issn'])? $record['issn'] : NULL,
                'author' => isset($record['author'])? $record['author'] : NULL,
                'title' => $record['title'],
                'title_sort' => $record['titleSort'],
                'format' => $record['format'],
                'call_num' => $data['call_num'],
                'description' => $data['description'],
                'barcode' => $data['barcode'],
                'date' => $fdate,
                'fine' => $data['fine'],
                'fine_description' => $data['fine_description'],
                'status' => $data['status']
               );
          }
echo "assigning transList";
          $interface->assign('transList', $transList);
        } else {
          $interface->assign('transList', 'You do not have any fines');
        }
      }
    }
        
    $interface->setTemplate('fines.tpl');
    $interface->setPageTitle('My Fines');
    $interface->display('layout.tpl');
  }
}

function formatNumber($params)
{
    extract($params);
    if ($record[$fieldName]) {
        $number = substr($record[$fieldName], 0, -2) . '.' . substr($record[$fieldName], -2);
    } else {
        $number = 0;
    }
    return money_format('%.2n', $number);
}

?>
