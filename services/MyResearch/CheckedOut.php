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

class CheckedOut extends MyResearch
{
    function launch()
    {
        global $configArray;
        global $interface;
        
        $interface->assign('pageTitle', 'Checked out items');
        
        $user = VFUser::singleton();

        $renew_message = array(); 
        if (isset($_POST['submit']) && isset($_POST['item_barcodes'])) {
          $item_barcodes = $_POST['item_barcodes'];
          $num_renewd = 0;
          $num_errors = 0;
          error_log(print_r($_POST, true));
          foreach ($item_barcodes as $item_barcode) {
// error_log("barcode is $item_barcode");
            $result = $this->catalog->renewItem($item_barcode, $user->patron->id);
            if (PEAR::isError($result)) {
              $num_errors++;
              $renew_message["$item_barcode"] = "Not renewed: $result";
            } else {
              $num_renewd++;
              $renew_message["$item_barcode"] = "Renewed: $result";
            }
          }
// error_log(print_r($renew_message,true));
        }

        // Add Sorting
        $sortby = "duedate_sort_a";	// default
        if (isset($_GET['sort'])) $sortby = $_GET['sort'];
        switch ($sortby) {
          case 'duedate_sort_a':
            $sort_term = 'duedate_sort';
            $sort_direction = 'a';
            break;
          case 'duedate_sort_d':
            $sort_term = 'duedate_sort';
            $sort_direction = 'd';
            break;
          case 'title_sort':
            $sort_term = 'title_sort';
            $sort_direction = 'a';
            break;
          case 'author':
            $sort_term = 'author_sort';
            $sort_direction = 'a';
            break;
          default:
            $sort_term = 'duedate_sort';
            $sort_direction = 'a';
            break;
        }
        $interface->assign('sort', $sortby);

        // Get My Transactions
        //if ($this->catalog->status) {
        if ($this->catalog->status and isset($user->patron)) {
          $result = $this->catalog->getMyTransactions($user->patron);
          if (!PEAR::isError($result)) {
            $transList = array();
            foreach ($result as $data) {
              $record = $this->db->getRecord($data['id']);
              if (isset($record)) {
                $id = $data['id'];
                $isbn = isset($record['isbn'])? $record['isbn'] : NULL;
                $issn = isset($record['issn'])? $record['issn'] : NULL;
                $author = isset($record['author'])? $record['author'] : NULL;
                $author_sort = isset($record['author'])? $record['author'][0] : NULL;
                $title = $record['title'];
                $title_sort = $record['titleSort'];
                $format = $record['format'];
              } else {
                $id = NULL; 
                $isbn = NULL;
                $issn = NULL;
                $title = $data['title'];
                $title_sort = $data['title'];
                $format = $data['format'];
                $author = isset($data['author']) ? $data['author'] : NULL;
                $author_sort = isset($data['author']) ? $data['author'] : NULL;
              }
              $barcode = $data['barcode'];
              $message = '';
              if (isset($renew_message["$barcode"])) $message =  $renew_message["$barcode"];
              $dd = $data['duedate'];
              $fd = $this->formatDate($dd);
              $transList[] = array(
                  'id' => $id,
                  'isbn' => $isbn,
                  'issn' => $issn,
                  'author' => $author,
                  'author_sort' => $author_sort,
                  'title' => $title,
                  'title_sort' => $title_sort,
                  'format' => $format,
                  'call_num' => $data['call_num'],
                  'location' => $data['location'],
                  'description' => $data['description'],
                  'barcode' => $data['barcode'],
                  'status' => $data['status'],
                  'renew_message' => $message,
                  'duedate_sort' => $data['duedate'],
                  'duedate' => $fd
                 );
            }
            if ($sort_direction == 'd') 
              usort($transList, create_function('$a,$b', "return strnatcasecmp(\$b['$sort_term'], \$a['$sort_term']);"));
            else
              usort($transList, create_function('$a,$b', "return strnatcasecmp(\$a['$sort_term'], \$b['$sort_term']);"));
            $interface->assign('transList', $transList);
          }
        }
        
        $interface->setTemplate('checkedout.tpl');

        $interface->display('layout.tpl');
    }
}

?>
