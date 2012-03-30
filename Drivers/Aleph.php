<?php
/**
 *
 * Copyright (C) UB/FU Berlin
 *
 * last update: 7.11.2007
 * tested with X-Server Aleph 18.1.
 *
 * TODO: login, course information, getNewItems, duedate in holdings, https connection to x-server, ...
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
require_once 'Interface.php';
require_once 'sys/VFUser.php';

class Aleph implements DriverInterface
{
    private $db;
    private $dbName;

    function __construct()
    {
        // Load Configuration for this Module
        $configArray = parse_ini_file('conf/Aleph.ini', true);
        
        $this->host = $configArray['Catalog']['host'];
        $this->groupByHol = false;
        if (isset($configArray['Catalog']['groupByHol'])) $this->groupByHol = $configArray['Catalog']['groupByHol'];
        $this->bib = $configArray['Catalog']['bib'];
        $this->useradm = $configArray['Catalog']['useradm'];
        $this->admlib = $configArray['Catalog']['admlib'];
        $this->loanlib = $configArray['Catalog']['loanlib'];
        $this->wwwuser = $configArray['Catalog']['wwwuser'];
        $this->wwwpasswd = $configArray['Catalog']['wwwpasswd'];
        $this->sublibadm = $configArray['sublibadm'];
#        $this->logo = $configArray['logo'];

    }
    
    public function getStatus($id)
    {
        $group_by_hol = 'N';
        if ($this->groupByHol) $group_by_hol = 'Y';
        $request = "http://$this->host/cgi-bin/getHoldings.pl?id=$id&item=Y&group_by_hol=$group_by_hol";
        // getHoldings: locally developed script running on the Aleph server.   
        // id parameter is a comma-separated list of bib keys.  
        $response = @file($request);	// suppress errors
        if (!$response) { 		// no response, generate dummy holdings array
          $holdings = array();
          foreach (explode(",", $id) as $key) {
            $holdings{$key}{"NA"}{"location"} = "Holdings/circ info temporarily unavailable";
          }
          return $holdings;
        }
        $answer = implode('', $response);
        $holdings = json_decode($answer, true);
        return $holdings;
    }
    
    public function getStatuses($idList)
    {
       return $this->getStatus(implode(",", $idList));
    }

    public function getHolding($id)
    {
        return $this->getStatus($id);
    }

    public function getHoldings($idList)
    {
        foreach ($idList as $id) {
            $holdings[] = $this->getStatus($id);
        }
        return $holdings;
    }

    public function getPurchaseHistory($id)
    {
        return array();
    }
    
    public function patronLogin($username, $lname)
    {
      // error_log("Called patronLogin with $username/$lname");
        $request = "http://$this->host/X?op=bor-auth&library=$this->useradm&bor_id=$username&verification=$lname";
        $requestfile = file($request);
        if (! $requestfile) {
          error_log ("error from xserver call for $username");
          return NULL;
        } 
        if (substr($requestfile[0],0,5) != '<?xml') {
          error_log ("non-xml file from xserver call for $username");
          return NULL;
        }
        $xmlfile = implode('', $requestfile);
        $xml = simplexml_load_string($xmlfile);
        if ($xml->error != '') {
            $patron->errorMsg = $xml->error;
            error_log("patronLogin error: " . $xml->error);
            return NULL;
        } else {
            $nameArray = explode(",", $xml->{'z303'}->{'z303-name'}, 2);
            $patron->firstname = "$nameArray[1]";
            $patron->lastname = "$nameArray[0]";
            $email_addr = $xml->{'z304'}->{'z304-email-address'};
            $home_lib = $xml->{'z303'}->{'z303-home-library'};
            if (($home_lib == '') || ($this->sublibadm["$home_lib"] == '')) {
                $patron->college = $this->useradm;
            } else {
                $patron->college = $this->sublibadm["$home_lib"];
            }
            $patron->username = "$username";
            $patron->password = "$lname";
            $patron->email = "$email_addr";
            $patron->id = (string) $xml->{'z303'}->{'z303-id'};
            $patron->bor_status = (string) $xml->{'z305'}->{'z305-bor-status'};
            $patron->campus = (string) $xml->{'z303'}->{'z303-profile-id'};
        }
        return $patron;
    }
    
    public function getMyTransactions($user)
    {
        $transList = array();

        $request = "http://$this->host/X?op=bor-info&library=$user->college&loans=Y&hold=N&cash=N&bor_id=$user->username&verification=$user->password";
        $xmlfile = implode('', file($request));
        //error_log($xmlfile, 3, '/tmp/login.xml');
        $xml = simplexml_load_string($xmlfile);
        $max = substr_count($xmlfile, "<item-l>");
        $today = date("Ymd");
        for($i=0;$i < $max ; $i++){
            $status = array();
            $id = str_pad($xml->{'item-l'}[$i]->z13->{'z13-doc-number'}, 9, '0', STR_PAD_LEFT);
            $isbn = $xml->{'item-l'}[$i]->z13->{'z13-isbn-issn'};
            $author = $xml->{'item-l'}[$i]->z13->{'z13-author'};
            $title = $xml->{'item-l'}[$i]->z13->{'z13-title'};
            //$format = $xml->{'item-l'}[$i]->z13->{'z13-user-defined-4'};
            //if (($format == '') || ($this->format["$format"] == '')) {
            //    $format = "Book";
            //} else {
            //    $format = $this->format["$format"];
            //}
            $duedate = $xml->{'item-l'}[$i]->z36->{'z36-due-date'};
            $duetime = $xml->{'item-l'}[$i]->z36->{'z36-due-hour'};
            $duedate .= " " . $duetime;
            $recallDuedate = $xml->{'item-l'}[$i]->z36->{'z36-recall-due-date'};
            if ($recallDuedate > "00000000") { 
              $duedate = $recallDuedate; 
              $status[] = 'Recalled';
            }
            $barcode = $xml->{'item-l'}[$i]->z30->{'z30-barcode'};
            $location = $xml->{'item-l'}[$i]->z30->{'z30-sub-library'};
            $call_num = $xml->{'item-l'}[$i]->z30->{'z30-call-no'};
            $description = $xml->{'item-l'}[$i]->z30->{'z30-description'};
            $format[] = $xml->{'item-l'}[$i]->z30->{'z30-material'} . '';
            if ($duedate < $today) $status[] = 'Overdue';
            $transList[] = array('duedate' => $duedate,
                                 'isbn' => $isbn,
                                 'status' => implode(", ", $status),
                                 'author' => $author,
                                 'title' => $title,
                                 'barcode' => $barcode,
                                 'location' => $location,
                                 'call_num' => $call_num,
                                 'description' => $description,
                                 'format' => $format,
                                 'id' => $id,
                                 'num' => $i+1);
        }
     
        return $transList;
    }
    
    public function getMyHolds($patron)
    {
        $list = array();
        $list["B"] = array();
        $list["H"] = array();
        $request = "http://$this->host/X?op=bor-info&library=$patron->college&loans=N&hold=Y&cash=N&bor_id=$patron->username&verification=$patron->password";
        $xmlfile = implode('', file($request));
        $xml = simplexml_load_string($xmlfile);
        $max = substr_count($xmlfile, "<item-h>");
        for($i=0;$i < $max ; $i++){
            $author = (string) $xml->{'item-h'}[$i]->z13->{'z13-author'};
            $title = (string) $xml->{'item-h'}[$i]->z13->{'z13-title'};
            $doc_number = str_pad($xml->{'item-h'}[$i]->z13->{'z13-doc-number'}, 9, '0', STR_PAD_LEFT);
            $request_type = (string) $xml->{'item-h'}[$i]->z37->{'z37-request-type'};
            if ($request_type == 'H') {
              $create = $xml->{'item-h'}[$i]->z37->{'z37-request-date'};
              $expire = $xml->{'item-h'}[$i]->z37->{'z37-end-request-date'};
              $booking_start = '';
              $booking_end = '';
            }
            if ($request_type == 'B') {
              $create = $xml->{'item-h'}[$i]->z37->{'z37-open-date'};
              $expire = '';
              $booking_start = $xml->{'item-h'}[$i]->z37->{'z37-booking-start-date'} . ' ' .
                               $xml->{'item-h'}[$i]->z37->{'z37-booking-start-hour'}; 
              $booking_end = $xml->{'item-h'}[$i]->z37->{'z37-booking-end-date'} . ' ' .
                             $xml->{'item-h'}[$i]->z37->{'z37-booking-end-hour'}; 
            }
            $recall_type = (string) $xml->{'item-h'}[$i]->z37->{'z37-recall-type'};
            $pickup_loc = (string) $xml->{'item-h'}[$i]->z37->{'z37-pickup-location'};
            $status = (string) $xml->{'item-h'}[$i]->z37->{'z37-status'};
            $hold_rec_key = $xml->{'item-h'}[$i]->z37->{'z37-doc-number'} . 
              $xml->{'item-h'}[$i]->z37->{'z37-item-sequence'} . 
              $xml->{'item-h'}[$i]->z37->{'z37-sequence'}; 
            $location = (string) $xml->{'item-h'}[$i]->z30->{'z30-sub-library'};
            $barcode = (string) $xml->{'item-h'}[$i]->z30->{'z30-barcode'};
            $call_num = (string) $xml->{'item-h'}[$i]->z30->{'z30-call-no'};
            $description = (string) $xml->{'item-h'}[$i]->z30->{'z30-description'};
            $list[$request_type][] = array('type' => $request_type . '-' . $recall_type,
                                'status' => $status,
                                'id' => $doc_number,
                                'hold_rec_key' => $hold_rec_key,
                                'barcode' => $barcode,
                                'pickup_loc' => $pickup_loc,
                                'location' => $location,
                                'call_num' => $call_num,
                                'description' => $description,
                                'expire' => $expire,
                                'create' => $create,
                                'booking_start' => $booking_start,
                                'booking_end' => $booking_end,
                                );
        }
        return $list;
    }

    public function getMyFines($patron)
    {
        $transList = array();
        $request = "http://$this->host/X?op=bor-info&library=$patron->college&loans=N&hold=N&cash=O&bor_id=$patron->username&verification=$patron->password";
        $xmlfile = implode('', file($request));
        $xml = simplexml_load_string($xmlfile);
        $max = substr_count($xmlfile, "<fine>");
        $balance = ltrim($xml->balance, '0');
        if (($length = strlen($balance)) > 2) {
            $result->balance = substr($balance, 0, $length - 2) . '.' . substr($balance, -2);
        }
        for($i=0;$i < $max ; $i++){
            $title = $xml->fine[$i]->z13->{'z13-title'};
            $id = str_pad($xml->fine[$i]->z13->{'z13-doc-number'}, 9, '0', STR_PAD_LEFT);
            $status = $xml->fine[$i]->z31->{'z31-status'};
            $date = $xml->fine[$i]->z31->{'z31-date'};
            $fine = $xml->fine[$i]->z31->{'z31-net-sum'};
            $fine_description = $xml->fine[$i]->z31->{'z31-description'};
            $barcode = $xml->fine[$i]->z30->{'z30-barcode'};
            $location = $xml->fine[$i]->z30->{'z30-sub-library'};
            $call_num = $xml->fine[$i]->z30->{'z30-call-no'};
            $description = $xml->fine[$i]->z30->{'z30-description'};
            $library = $xml->fine[$i]->z31->{'z31-payment-target'};
            $transList[] = array('title' => $title,
                                 'id' => $id,
                                 'status' => $status,
                                 'date' => $date,
                                 'fine' => $fine,
                                 'fine_description' => $fine_description,
                                 'description' => $description,
                                 'barcode' => $barcode,
                                 'location' => $location,
                                 'call_num' => $call_num,
                                 'library' => $library);
        }
        error_log("getMyFines: " . print_r($transList,1));
        return $transList;
    }
    
    public function getMyCounts($user)
    {
        $tagmatch = "cbscindeloreisz";
        $transList = array();
        $request = "http://$this->host/X?op=bor-info&library=$user->college&bor_id=$user->username&verification=";
        $answer = file($request);
        foreach($answer as $line){
            // transform the misspelled xml-tags:
            if(preg_match("|^<[$tagmatch]|i", $line) || preg_match("|^</[$tagmatch]|i", $line)) {
                $line = preg_replace("/-/i", "_", $line);
            }
            $xmlfile = $xmlfile . $line;
        }
        $xml = simplexml_load_string($xmlfile);
        $balance = ltrim($xml->balance, '0');
        if (($length = strlen($balance)) > 2) {
            $result->balance = substr($balance, 0, $length - 2) . '.' . substr($balance, -2);
        }
        $max = substr_count($xmlfile, "<item_l>");
        $result->loanCount = $max;
        $max = substr_count($xmlfile, "<item_h>");
        $result->holdCount = $max;
        return $result;
    }

    public function placeHold($barcode, $patronID, $comment, $type, $pickupLoc, $not_needed_after_date)
    {
     //$request = "http://$this->host/X?op=hold-req&item_barcode=$barcode&bor_id=$patronID&library=$this->useradm";
     $notNeededAfter = date_format($not_needed_after_date, "Ymd");
     $request = "http://$this->host/cgi-bin/placeHold?&item_barcode=$barcode&bor_id=$patronID&library=$this->useradm&pickup_loc=$pickupLoc&not_needed_after=$notNeededAfter";
     $xmlfile = implode('', file($request));
     $xml = simplexml_load_string($xmlfile);
     $error = $xml->{'error'};
     if ($error) {
       return new PEAR_Error($error);
     }
     $result = "OK"; 
     return $result;
    }

    public function removeHold($recordId)
    {
      $request = "http://$this->host/X?op=hold-req-cancel&rec_key=$recordId&library=$this->useradm";
      $xmlfile = implode('', file($request));
      $xml = simplexml_load_string($xmlfile);
      $error = $xml->{'error'};
     if ($error) {
       return new PEAR_Error($error);
     }
     $result = "hold removed"; 
     return $result;
    }

    public function renewItem($recordId, $patronId)
    {
      $request = "http://$this->host/X?op=renew&item_barcode=$recordId&bor_id=$patronId&library=$this->useradm";
      $xmlfile = implode('', file($request));
      $xml = simplexml_load_string($xmlfile);
      $error = $xml->{'error'};
      if ($error) {
        return new PEAR_Error($error);
      }
      $error = $xml->{'error-code-1'};
      if ($error) {
        $error_msg = $xml->{'error-text-1'};
        return new PEAR_Error( $error_msg );
      }
      $result = "item renewed"; 
      return $result;
     }

    public function getNewItems($page, $limit, $startdate, $enddate, $department = null)
    {
        $items = array();
        return $items;
    }
    
    function getDepartments()
    {
        $deptList = array();
        return $deptList;
    }
    
    function getInstructors()
    {
        $deptList = array();
        return $deptList;
    }
    
    function getCourses()
    {
        $deptList = array();
        return $deptList;
    }

    function findReserves($course, $inst, $dept)
    {
        $recordList = array();
        return $recordList;
    }

    function getMyProfile($user)
    {
        $request = "http://$this->host/X?op=bor-info&library=$user->college&loans=N&hold=N&cash=N&bor_id=$user->username&verification=$user->password";
        $xmlfile = implode('', file($request));
        $xml = simplexml_load_string($xmlfile);
        if ($xml->error != '') {
            return null;
        } else {
            //$username = $xml->{'z303'}->{'z303-id'};
            $nameArray = explode(",", $xml->{'z303'}->{'z303-name'}, 2);
            $patron = array(
                            'firstname' => $nameArray[1],
                            'lastname' => $nameArray[0],
                            'email' => $xml->{'z304'}->{'z304-email-address'},
                            'address1' => $xml->{'z304'}->{'z304-address-1'},
                            'address2' => $xml->{'z304'}->{'z304-address-2'},
                            'zip' => $xml->{'z304'}->{'z304-zip'},
                            'phone' => $xml->{'z304'}->{'z304-telephone'},
                            'id' => $xml->{'z303'}->{'z303-id'},
                           );
        }
        return $patron;
    }
    
    public function getShelfItems($user)
    {
        $request = "http://$this->host/cgi-bin/getShelfItems.pl?user_id=$user->id";
        $response = @file($request);	// suppress errors
        if (!$response) { 		// no response, ???
          return;
        }
        $answer = implode('', $response);
        $items = json_decode($answer, true);
        return $items;
    }

    public function getBookingLink($recordId)
    {
        $adm_doc_number = substr($recordId, 0, 9);
        $adm_item_sequence = substr($recordId, 9, 6);
        $url = "http://$this->host/F/?func=booking-req-form-itm&adm_library=$this->admlib&adm_doc_number=$adm_doc_number&adm_item_sequence=$adm_item_sequence&exact_item=N";
        return $url;
    }

    
}

?>
