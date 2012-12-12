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
 
require_once 'CatalogConnection.php';
require_once 'services/MyResearch/MyResearch.php';
require_once 'Action.php';
require_once 'sys/ActivityLog.php';
require_once 'services/Record/RecordUtils.php';


class Hold extends MyResearch
{
    var $catalog;
    
    //function __construct() {
    //  parent::__construct();  # Will force login
    //}    
    
    function launch()
    {
      global $configArray;
      global $interface;
      
      $message = '';
      $user = VFUser::singleton();

      if (isset($_GET['barcode'])) $barcode = $_GET['barcode'];
      if (isset($_POST['barcode'])) $barcode = $_POST['barcode'];
      $record_id = $_GET['id'];

      if (! isset($user->patron)) {
        $message = "No valid Mirlyn patron record found";
        $interface->assign('message', $message);
        $interface->assign('id', $record_id);
        $interface->setTemplate('hold-fail.tpl');
        $interface->display('layout.tpl');
        error_log("Exit from Hold.php, message is " . $message);
        exit();
      }
      
      $alog = ActivityLog::singleton();

      $class = $configArray['Index']['engine'];
      $db = new $class($configArray['Index']['url']);
      $record = $db->getRecord($record_id);
      if (! $record) {
        PEAR::raiseError(new PEAR_Error('Cannot find record'));
      }
      // get the associated holdings info
      $holding = $this->catalog->getHolding($record_id);
      if (!$holding) {
        PEAR::raiseError(new PEAR_Error('Cannot get holdings for record'));
      }
      $hold = $holding[$record_id];
      $ru = new RecordUtils();
      $marc = $ru->getMarcRecord($record);
      // get some supplemental fields from the marc record
      if ($f260 = $marc->getField('26[04]', true)) {
        if ($subf = $f260->getSubfield('a')) $record['pubPlace'] = $this->cleanStringEnd($subf->getData());
        if ($subf = $f260->getSubfield('b')) $record['publisher'] = $this->cleanStringEnd($subf->getData());
        if ($subf = $f260->getSubfield('c')) $record['pubDate'] = $this->cleanStringEnd($subf->getData());
      }
      if ($f250 = $marc->getField('250')) {
        if ($subf = $f250->getSubfield('a')) $record['edition'] = $this->cleanStringEnd($subf->getData());
      }
//      if (! isset($record['author'])) {	//  no author--look for editors
//        foreach ($marc->getFields('700|710|711',1) as $f7xx) { 
//          if ($subf = $f7xx->getSubfield('a')) $record['author'][] = $this->cleanStringEnd($subf->getData());
//        }
//      }       
      $record['titles'] = $ru->getFullTitle($marc);
      
      // get the item info for the specified barcode
      foreach ($hold as $copy) {
        if (isset($copy['item_info'])) {
          foreach ($copy['item_info'] as $item) {
            if (isset($item['barcode']) && $item['barcode'] == $barcode) {
              //$interface->assign('item', $item);
              $record['item'] = $item;
              break 2;
            }
          }
        }
      }
      $interface->assign('record', $record);
      $ctx_object = $this->getCtxObject($record);
      $interface->assign('ctx_object', $ctx_object);
      $interface->assign('id', $record_id);
      $interface->assign('patron', get_object_vars($user->patron));
 
      // get pickup loc list from ini file
      $pickupLocList = parse_ini_file('conf/pickupLoc.ini');
      $interface->assign('pickupLocList', $pickupLocList);

      if (!isset($_POST['submit'])) { 			// 1st time--get default non_needed_date, and display form
        $alog->log('showgetthis', $record_id);        
        $not_needed_after = date("m/d/Y", strtotime("+2 months"));
        $interface->assign('not_needed_after', $not_needed_after);
        $interface->setTemplate('hold.tpl');
        $interface->display('layout.tpl');
        error_log("Exit from Hold.php, submit not set" );
        exit();
      }

      // submit pressed, validate values
      $alog->log('dogetthis', $record_id);
      
      $errmsg = array();
      if (isset($_POST['pickup_loc']) && $_POST['pickup_loc'] != '') {
        $pickup_loc = $_POST['pickup_loc'];
        $interface->assign('pickup_loc', $pickup_loc);
      } else { $errmsg[] = "select a pickup location"; }
      if (isset($_POST['not_needed_after']) && $_POST['not_needed_after'] != '') {
        $not_needed_after = $_POST['not_needed_after'];
        $interface->assign('not_needed_after', $not_needed_after);
        $date_not_needed = date_create($not_needed_after);
      } else { $errmsg[] = "specify \"Date not needed\""; }
      if ( count($errmsg) > 0 ) {
        $message = "Please " . implode(" and ", $errmsg);
        $interface->assign('message', $message);
        $interface->setTemplate('hold.tpl');
        $interface->display('layout.tpl');
        error_log("Exit from Hold.php, message is " . $message);
        exit();
      }
      // try to place the hold
      $result = $this->catalog->placeHold($barcode, $user->patron->id, '', '', $pickup_loc, $date_not_needed);
      if (!PEAR::isError($result)) {
        $message = "Item has been requested for pickup at $pickupLocList[$pickup_loc]";
        $interface->assign('message', $message);
        $interface->setTemplate('hold-success.tpl');
        $interface->display('layout.tpl');
        error_log("Exit from Hold.php, hold placed" );
        exit();
      } else {
        $message = "Error placing hold: $result";
        $interface->assign('message', $message);
        $interface->setTemplate('hold.tpl');
        $interface->display('layout.tpl');
        error_log("Exit from Hold.php, error placing hold" );
        exit();
      }
    }

    function getCtxObject($record) {
      $pairs = array();
      if (isset($record['oclc'])) $pairs["rfe_dat"] = "<accessionnumber>" . implode(',', $record['oclc']) . "</accessionnumber>"; 
      if (isset($record['issn'])) $pairs["issn"] = implode(',', $record['issn']); 
      if (isset($record['isbn'])) $pairs["isbn"] = implode(',', $record['isbn']); 
      if (isset($record['titles'])) $pairs["title"] = $record['titles'][0];
      if (isset($record['author'])) $pairs["rft.au"] = implode('; ', $record['author']);
      if (isset($record['pubDate'])) $pairs["date"] = $record['publishDate'][0];
      if (isset($record['publisher'])) $pairs["rft.pub"] = $record['publisher'];
      if (isset($record['pubPlace'])) $pairs["rft.place"] = $record['pubPlace'];
      if (isset($record['edition'])) $pairs["rft.edition"] = $record['edition'];
      if (isset($record['callnumber'])) $pairs["callnumber"] = $record['callnumber'][0];
      if (isset($record['item']['description'])) $pairs["rft.issue"] = $record['item']['description'];
      if (isset($record['item']['description'])) $pairs["notes"] = $record['item']['description'];

      return $pairs;
	    }

    function cleanStringEnd($string)
    {
        $string = trim($string);
        if ((substr($string, -1) == '.') ||
            (substr($string, -1) == ',') ||
            (substr($string, -1) == ':') ||
            (substr($string, -1) == ';') ||
            (substr($string, -1) == '/')) {
            $string = substr($string, 0, -1);
        }
        return $string;
    }
}

?>
