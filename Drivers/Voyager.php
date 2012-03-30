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
require_once 'Interface.php';

class Voyager implements DriverInterface
{
    private $db;
    private $dbName;
    private $config;
    
    function __construct()
    {
        // Load Configuration for this Module
        $this->config = parse_ini_file('conf/Voyager.ini', true);
        
        // Define Database Name
        $this->dbName = $this->config['Catalog']['database'];
        
        $tns = '(DESCRIPTION=' .
                 '(ADDRESS_LIST=' .
                   '(ADDRESS=' .
                     '(PROTOCOL=TCP)' . 
                     '(HOST=' . $this->config['Catalog']['host'] . ')' .
                     '(PORT=' . $this->config['Catalog']['port'] . ')' .
                   ')' .
                 ')' . 
                 '(CONNECT_DATA=' .
                   '(SERVICE_NAME=' . $this->config['Catalog']['service'] . ')' .
                 ')' . 
               ')';
        try {
            $this->db = new PDO("oci:dbname=$tns",
                                $this->config['Catalog']['user'],
                                $this->config['Catalog']['password']);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getStatus($id)
    {
        $holding = array();
    
        // Build SQL Statement
        $sql = "select ITEM.ON_RESERVE, ITEM_STATUS_DESC as status, LOCATION.LOCATION_DISPLAY_NAME as location, MFHD_MASTER.DISPLAY_CALL_NO as callnumber " .
               "from $this->dbName.BIB_ITEM, $this->dbName.ITEM, $this->dbName.ITEM_STATUS_TYPE, $this->dbName.ITEM_STATUS, $this->dbName.LOCATION, $this->dbName.MFHD_ITEM, $this->dbName.MFHD_MASTER " .
               "where BIB_ITEM.BIB_ID = '$id' " .
               "and BIB_ITEM.ITEM_ID = ITEM.ITEM_ID " .
               "and ITEM.ITEM_ID = ITEM_STATUS.ITEM_ID " .
               "and ITEM_STATUS.ITEM_STATUS = ITEM_STATUS_TYPE.ITEM_STATUS_TYPE " .
               "and LOCATION.LOCATION_ID = ITEM.PERM_LOCATION " .
               "and MFHD_ITEM.ITEM_ID = ITEM.ITEM_ID " .
               "and MFHD_MASTER.MFHD_ID = MFHD_ITEM.MFHD_ID";

        // Execute SQL
        try {
            $holding = array();
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        // Build Holdings Array
        while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
            switch ($row['STATUS']) {
                case 'Not Charged':
                    $available = true;
                    break;
                default:
                    $available = false;
                    break;
            }

            $holding[] = array('id' => $id,
                               'availability' => $available,
                               'status' => $row['STATUS'],
                               'location' => htmlentities($row['LOCATION']),
                               'reserve' => $row['ON_RESERVE'],
                               'callnumber' => $row['CALLNUMBER']);
        }

        return $holding;
    }

    public function getStatuses($idList)
    {
        $status = array();
        foreach ($idList as $id) {
            $status[] = $this->getStatus($id);
        }
        return $status;
    }

    public function getHolding($id)
    {
        // Build SQL Statement
        $sql = "select ITEM_BARCODE.ITEM_BARCODE, ITEM.ITEM_ID, MFHD_DATA.RECORD_SEGMENT, MFHD_ITEM.ITEM_ENUM, ITEM.ON_RESERVE, ITEM.ITEM_SEQUENCE_NUMBER, ITEM_STATUS_DESC as status, LOCATION.LOCATION_DISPLAY_NAME as location, MFHD_MASTER.DISPLAY_CALL_NO as callnumber, CIRC_TRANSACTIONS.CURRENT_DUE_DATE as duedate " .
               "from $this->dbName.BIB_ITEM, $this->dbName.ITEM, $this->dbName.ITEM_STATUS_TYPE, $this->dbName.ITEM_STATUS, $this->dbName.LOCATION, $this->dbName.MFHD_ITEM, $this->dbName.MFHD_MASTER, $this->dbName.MFHD_DATA, $this->dbName.CIRC_TRANSACTIONS, $this->dbName.ITEM_BARCODE " .
               "where BIB_ITEM.BIB_ID = '$id' " .
               "and BIB_ITEM.ITEM_ID = ITEM.ITEM_ID " .
               "and ITEM.ITEM_ID = ITEM_STATUS.ITEM_ID " .
               "and ITEM_STATUS.ITEM_STATUS = ITEM_STATUS_TYPE.ITEM_STATUS_TYPE " .
               "and ITEM_BARCODE.ITEM_ID (+)= ITEM.ITEM_ID " .
               "and LOCATION.LOCATION_ID = ITEM.PERM_LOCATION " . 
               "and CIRC_TRANSACTIONS.ITEM_ID (+)= ITEM.ITEM_ID " .
               "and MFHD_ITEM.ITEM_ID = ITEM.ITEM_ID " . 
               "and MFHD_MASTER.MFHD_ID = MFHD_ITEM.MFHD_ID " .
               "and MFHD_DATA.MFHD_ID = MFHD_ITEM.MFHD_ID " .
               "and MFHD_MASTER.SUPPRESS_IN_OPAC='N' " .
               "order by ITEM.ITEM_SEQUENCE_NUMBER";
        
        // Execute SQL
        try {
            $holding = array();
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
        
        // Build Holdings Array
        $i = 0;
        while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
            // Determine Copy Number
            $number = ($row['ITEM_ENUM']) ? $row['ITEM_ENUM'] : $row['ITEM_SEQUENCE_NUMBER'];

            // Concat wrapped rows
            if (isset($data[$row['ITEM_ID']]["$number"])) {
                $data[$row['ITEM_ID']]["$number"]['RECORD_SEGMENT'] .= $row['RECORD_SEGMENT'];
            } else {
                $data[$row['ITEM_ID']]["$number"] = $row;
            }
        }
        
        foreach($data as $item) {
            foreach($item as $number => $row) {
            switch ($row['STATUS']) {
                case 'Not Charged':
                    $available = true;
                    break;
                default:
                    $available = false;
                    break;
            }

            $holding[$i] = array('id' => $id,
                                 'availability' => $available,
                                 'status' => $row['STATUS'],
                                 'location' => htmlentities($row['LOCATION']),
                                 'reserve' => $row['ON_RESERVE'],
                                 'callnumber' => $row['CALLNUMBER'],
                                 'duedate' => $row['DUEDATE'],
                                 'number' => $number,
                                 'barcode' => $row['ITEM_BARCODE']);

            // Parse Holding Record
            if ($row['RECORD_SEGMENT']) {
                require_once 'File/MARC.php';
                $marc = new File_MARC(str_replace(array("\n", "\r"), '', $row['RECORD_SEGMENT']), File_MARC::SOURCE_STRING);
                if ($record = $marc->next()) {
                    // Get Notes
                    if ($field = $record->getField('852')) {
                        if ($subfield = $field->getSubfield('z')) {
                            $holding[$i]['notes'] = $subfield->getData();
                        }
                    }

                    // Get Summary
                    if ($field = $record->getField('866')) {
                        if ($subfield = $field->getSubfield('a')) {
                            $holding[$i]['summary'] = $subfield->getData();
                        }
                    }
                }
            }
            
            $i++;
            }
        }

        return $holding;
    }
    
    /*
    public function getHoldings($idList)
    {
        $sql = "select ITEM.ON_RESERVE, ITEM.ITEM_SEQUENCE_NUMBER, ITEM_STATUS_DESC as status, LOCATION.LOCATION_DISPLAY_NAME as location, MFHD_MASTER.DISPLAY_CALL_NO as callnumber, CIRC_TRANSACTIONS.CURRENT_DUE_DATE as duedate " .
               "from $this->dbName.BIB_ITEM, $this->dbName.ITEM, $this->dbName.ITEM_STATUS_TYPE, $this->dbName.ITEM_STATUS, $this->dbName.LOCATION, $this->dbName.MFHD_ITEM, $this->dbName.MFHD_MASTER, $this->dbName.CIRC_TRANSACTIONS " .
               "where BIB_ITEM.ITEM_ID = ITEM.ITEM_ID " .
               "and ITEM.ITEM_ID = ITEM_STATUS.ITEM_ID " .
               "and ITEM_STATUS.ITEM_STATUS = ITEM_STATUS_TYPE.ITEM_STATUS_TYPE " .
               "and LOCATION.LOCATION_ID = ITEM.PERM_LOCATION " .
               "and CIRC_TRANSACTIONS.ITEM_ID (+)= ITEM.ITEM_ID " .
               "and MFHD_ITEM.ITEM_ID = ITEM.ITEM_ID " .
               "and MFHD_MASTER.MFHD_ID = MFHD_ITEM.MFHD_ID " .
               "and (";
        for ($i=0; $i<count($idList); $i++) {
            if ($i > 0) {
                $sql .= ' OR ';
            }
            $sql .= "BIB_ITEM.BIB_ID = '$idList[$i]'";
        }
        $sql .= ')';
        echo $sql;

        try {
            $holding = array();
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                switch ($row['STATUS']) {
                    case 'Not Charged':
                    case 'Cataloging Review':
                    case 'Circulation Review':
                        $available = true;
                        break;
                    default:
                        $available = false;
                        break;
                }

                $holding[] = array('availability' => $available,
                                   'status' => $row['STATUS'],
                                   'location' => $row['LOCATION'],
                                   'reserve' => $row['ON_RESERVE'],
                                   'callnumber' => $row['CALLNUMBER'],
                                   'duedate' => $row['DUEDATE'],
                                   'number' => $row['ITEM_SEQUENCE_NUMBER']);
            }
            return $holding;
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }
    */

    public function getPurchaseHistory($id)
    {
        $sql = "select SERIAL_ISSUES.ENUMCHRON " .
               "from $this->dbName.SERIAL_ISSUES, $this->dbName.COMPONENT, $this->dbName.ISSUES_RECEIVED, $this->dbName.SUBSCRIPTION, $this->dbName.LINE_ITEM " .
               "where SERIAL_ISSUES.COMPONENT_ID = COMPONENT.COMPONENT_ID " .
               "and ISSUES_RECEIVED.ISSUE_ID = SERIAL_ISSUES.ISSUE_ID " .
               "and ISSUES_RECEIVED.COMPONENT_ID = COMPONENT.COMPONENT_ID " .
               "and COMPONENT.SUBSCRIPTION_ID = SUBSCRIPTION.SUBSCRIPTION_ID " .
               "and SUBSCRIPTION.LINE_ITEM_ID = LINE_ITEM.LINE_ITEM_ID " .
               "and SERIAL_ISSUES.RECEIVED = 1 " .
               "and ISSUES_RECEIVED.OPAC_SUPPRESSED = 1 " .
               "and LINE_ITEM.BIB_ID = '$id' " .
               "order by SERIAL_ISSUES.ISSUE_ID DESC";
        try {
            $data = array();
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = array('issue' => $row['ENUMCHRON']);
            }
            return $data;
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }

    public function patronLogin($barcode, $lname)
    {
        $sql = "SELECT PATRON.PATRON_ID FROM $this->dbName.PATRON, $this->dbName.PATRON_BARCODE " .
               "WHERE PATRON.PATRON_ID = PATRON_BARCODE.PATRON_ID AND " .
               "PATRON.LAST_NAME = '$lname' AND PATRON_BARCODE.PATRON_BARCODE = '$barcode'";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            $row = $sqlStmt->fetch(PDO::FETCH_ASSOC);
            if (isset($row['PATRON_ID']) && ($row['PATRON_ID'] != '')) {
                return array('id' => $row['PATRON_ID']);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }
    
    public function getMyTransactions($patron)
    {
        $transList = array();

        $sql = "SELECT CHARGE_DUE_DATE as DUEDATE, BIB_ITEM.BIB_ID " .
               "FROM $this->dbName.CIRC_TRANSACTIONS, $this->dbName.BIB_ITEM " .
               "WHERE BIB_ITEM.ITEM_ID = CIRC_TRANSACTIONS.ITEM_ID " .
               "AND CIRC_TRANSACTIONS.PATRON_ID = '" . $patron['id'] . "'";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $transList[] = array('duedate' => $row['DUEDATE'],
                                     'id' => $row['BIB_ID']);
            }
            return $transList;
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }

    public function getMyFines($patron)
    {
        $fineList = array();

        $sql = "SELECT unique FINE_FEE_TYPE.FINE_FEE_DESC, FINE_FEE.FINE_FEE_AMOUNT, FINE_FEE.FINE_FEE_BALANCE, FINE_FEE.ORIG_CHARGE_DATE, FINE_FEE.DUE_DATE, BIB_ITEM.BIB_ID " .
               "FROM (($this->dbName.FINE_FEE INNER JOIN $this->dbName.FINE_FEE_TYPE ON FINE_FEE.FINE_FEE_TYPE = FINE_FEE_TYPE.FINE_FEE_TYPE) INNER JOIN $this->dbName.PATRON ON FINE_FEE.PATRON_ID = PATRON.PATRON_ID) INNER JOIN $this->dbName.BIB_ITEM ON FINE_FEE.ITEM_ID = BIB_ITEM.ITEM_ID ".
               "WHERE PATRON.PATRON_ID='" . $patron['id'] . "'";

        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $fineList[] = array('amount' => $row['FINE_FEE_AMOUNT'],
                                    'fine' => $row['FINE_FEE_DESC'],
                                    'balance' => $row['FINE_FEE_BALANCE'],
                                    'checkout' => $row['ORIG_CHARGE_DATE'],
                                    'duedate' => $row['DUE_DATE'],
                                    'id' => $row['BIB_ID']);
            }
            return $fineList;
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }

    public function getMyHolds($patron)
    {
        $holdList = array();

        $sql = "SELECT HOLD_RECALL.BIB_ID, HOLD_RECALL.PICKUP_LOCATION, HOLD_RECALL.HOLD_RECALL_TYPE, HOLD_RECALL.EXPIRE_DATE, HOLD_RECALL.CREATE_DATE " .
               "FROM $this->dbName.HOLD_RECALL " .
               "WHERE HOLD_RECALL.PATRON_ID = '" . $patron['id'] . "'";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $holdList[] = array('type' => $row['HOLD_RECALL_TYPE'],
                                    'id' => $row['BIB_ID'],
                                    'location' => $row['PICKUP_LOCATION'],
                                    'expire' => $row['EXPIRE_DATE'],
                                    'create' => $row['CREATE_DATE']);
            }
            return $holdList;
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }

    public function getMyProfile($patron)
    {
        $sql = "SELECT PATRON.LAST_NAME, PATRON.FIRST_NAME, PATRON.HISTORICAL_CHARGES, PATRON_ADDRESS.ADDRESS_LINE1, PATRON_ADDRESS.ADDRESS_LINE2, PATRON_ADDRESS.ZIP_POSTAL, PATRON_PHONE.PHONE_NUMBER, PATRON_GROUP.PATRON_GROUP_NAME " .
               "FROM $this->dbName.PATRON, $this->dbName.PATRON_ADDRESS, $this->dbName.PATRON_PHONE, $this->dbName.PATRON_BARCODE, $this->dbName.PATRON_GROUP " .
               "WHERE PATRON.PATRON_ID = PATRON_ADDRESS.PATRON_ID " .
               "AND PATRON_ADDRESS.ADDRESS_ID = PATRON_PHONE.ADDRESS_ID " .
               "AND PATRON.PATRON_ID = PATRON_BARCODE.PATRON_ID " .
               "AND PATRON_BARCODE.PATRON_GROUP_ID = PATRON_GROUP.PATRON_GROUP_ID " .
               "AND PATRON.PATRON_ID = '" . $patron['id'] . "'";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            $row = $sqlStmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $patron = array('firstname' => $row['FIRST_NAME'],
                                'lastname' => $row['LAST_NAME'],
                                'address1' => $row['ADDRESS_LINE1'],
                                'address2' => $row['ADDRESS2_LINE'],
                                'zip' => $row['ZIP_POSTAL'],
                                'phone' => $row['PHONE_NUMBER'],
                                'group' => htmlentities($row['PATRON_GROUP_NAME']));
                return $patron;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }

    public function getHoldLink($recordId)
    {
        // Open Record Page
        $client = new HTTP_Request(null, array('useBrackets' => false));
    	$client->setURL($this->config['Catalog']['pwebrecon'] . '?BBID=' . $recordId);
        $result = $client->sendRequest();
        if (!PEAR::isError($result)) {
            // Get HTML Page
            $body = str_replace("\n", '', $client->getResponseBody());
            $body = str_replace('<A', "\n<A", $body);  // Not sure why this is needed, but it solved the problem

            // Parse out SEQ and PID
            //preg_match('/<a href="(.*)">Place Hold/i', $body, $temp);
            //$link = $this->config['Catalog']['pwebrecon'] . strrchr(trim($temp[1]), '?');
            preg_match('/\?PAGE=REQUESTBIB&SEQ=(.*)&PID=(.*)"/', $body, $temp);
            
            // Establish Place Hold Link
            if ($temp[1] && $temp[2]) {
                $link = $this->config['Catalog']['pwebrecon'] . '?PAGE=REQUESTBIB' .
                        '&SEQ=' . $temp[1] . '&PID=' . $temp[2];
                return $link;
            } else {
                return new PEAR_Error('Cannot process "Place Hold"');
            }
        } else {
            return $result;
        }
    }

    public function getNewItems($page, $limit, $daysOld, $departmentId = null)
    {
        $items = array();

        // Prevent unnecessary load on voyager
        if ($daysOld > 30) {
            $daysOld = 30;
        }

        $enddate = date('d-m-Y', strtotime('now'));
        $startdate = date('d-m-Y', strtotime("-$daysOld day"));

        $sql = "select count(distinct LINE_ITEM.BIB_ID) as count " .
               "from $this->dbName.LINE_ITEM, $this->dbName.LINE_ITEM_COPY_STATUS, $this->dbName.LINE_ITEM_FUNDS, $this->dbName.FUND " .
               "where LINE_ITEM.LINE_ITEM_ID = LINE_ITEM_COPY_STATUS.LINE_ITEM_ID " .
               "and LINE_ITEM_COPY_STATUS.COPY_ID = LINE_ITEM_FUNDS.COPY_ID " .
               "and LINE_ITEM_FUNDS.FUND_ID = FUND.FUND_ID ";
        if ($departmentId) {
            $sql .= "and FUND.FUND_NAME = '$departmentId' ";
        }
        $sql .= "and LINE_ITEM.CREATE_DATE >= to_date('$startdate', 'dd-mm-yyyy') " .
               "and LINE_ITEM.CREATE_DATE < to_date('$enddate', 'dd-mm-yyyy')";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            $row = $sqlStmt->fetch(PDO::FETCH_ASSOC);
            $items['count'] = $row['COUNT'];
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        $page = ($page) ? $page : 1;
        $limit = ($limit) ? $limit : 20;
        $startRow = (($page-1)*$limit)+1;
        $endRow = ($page*$limit);
        /*
        $sql = "select * from " .
               "(select a.*, rownum rnum from " .
               "(select LINE_ITEM.BIB_ID, BIB_TEXT.TITLE, FUND.FUND_NAME, LINE_ITEM.CREATE_DATE, LINE_ITEM_STATUS.LINE_ITEM_STATUS_DESC " .
               "from $this->dbName.BIB_TEXT, $this->dbName.LINE_ITEM, $this->dbName.LINE_ITEM_COPY_STATUS, $this->dbName.LINE_ITEM_STATUS, $this->dbName.LINE_ITEM_FUNDS, $this->dbName.FUND " .
               "where BIB_TEXT.BIB_ID = LINE_ITEM.BIB_ID " .
               "and LINE_ITEM.LINE_ITEM_ID = LINE_ITEM_COPY_STATUS.LINE_ITEM_ID " .
               "and LINE_ITEM_COPY_STATUS.COPY_ID = LINE_ITEM_FUNDS.COPY_ID " .
               "and LINE_ITEM_STATUS.LINE_ITEM_STATUS = LINE_ITEM_COPY_STATUS.LINE_ITEM_STATUS " .
               "and LINE_ITEM_FUNDS.FUND_ID = FUND.FUND_ID ";
        */
        $sql = "select * from " .
               "(select a.*, rownum rnum from " .
               "(select LINE_ITEM.BIB_ID, LINE_ITEM.CREATE_DATE " .
               "from $this->dbName.LINE_ITEM, $this->dbName.LINE_ITEM_COPY_STATUS, $this->dbName.LINE_ITEM_STATUS, $this->dbName.LINE_ITEM_FUNDS, $this->dbName.FUND " .
               "where LINE_ITEM.LINE_ITEM_ID = LINE_ITEM_COPY_STATUS.LINE_ITEM_ID " .
               "and LINE_ITEM_COPY_STATUS.COPY_ID = LINE_ITEM_FUNDS.COPY_ID " .
               "and LINE_ITEM_STATUS.LINE_ITEM_STATUS = LINE_ITEM_COPY_STATUS.LINE_ITEM_STATUS " .
               "and LINE_ITEM_FUNDS.FUND_ID = FUND.FUND_ID ";
        if ($departmentId) {
            $sql .= "and FUND.FUND_NAME = '$departmentId' ";
        }
        $sql .= "and LINE_ITEM.CREATE_DATE >= to_date('$startdate', 'dd-mm-yyyy') " .
               "and LINE_ITEM.CREATE_DATE < to_date('$enddate', 'dd-mm-yyyy') " .
               "group by LINE_ITEM.BIB_ID, LINE_ITEM.CREATE_DATE " .
               "order by LINE_ITEM.CREATE_DATE desc) a " .
               "where rownum <= $endRow) " .
               "where rnum >= $startRow";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $items['results'][]['id'] = $row['BIB_ID'];
            }
            return $items;
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }
    }
    
    function getFunds()
    {
        $list = array();

        $sql = "select distinct * from " .
               "(select initcap(lower(FUND.FUND_NAME)) as name from NOVADB.FUND) " .
               "order by name";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $list[] = $row['NAME'];
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        return $list;
    }

    function getDepartments($startDate = null)
    {
        $deptList = array();
        
        $sql = "select DEPARTMENT.DEPARTMENT_ID, DEPARTMENT.DEPARTMENT_NAME " .
               "from $this->dbName.RESERVE_LIST, $this->dbName.RESERVE_LIST_COURSES, $this->dbName.DEPARTMENT " .
               "where " .
               "RESERVE_LIST.RESERVE_LIST_ID = RESERVE_LIST_COURSES.RESERVE_LIST_ID and " .
               "RESERVE_LIST_COURSES.DEPARTMENT_ID = DEPARTMENT.DEPARTMENT_ID ";
        if ($startDate) {
            $sql .= "and RESERVE_LIST.EXPIRE_DATE <= to_date('$startDate', 'YYYY-mm-dd') ";
        }
        $sql .= "group by DEPARTMENT.DEPARTMENT_ID, DEPARTMENT_NAME " .
                "order by DEPARTMENT_NAME";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $deptList[$row['DEPARTMENT_ID']] = $row['DEPARTMENT_NAME'];
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        return $deptList;
    }
    
    function getInstructors($startDate)
    {
        $deptList = array();

        $sql = "select INSTRUCTOR.INSTRUCTOR_ID, INSTRUCTOR.LAST_NAME || ', ' || INSTRUCTOR.FIRST_NAME as NAME " .
               "from $this->dbName.RESERVE_LIST, $this->dbName.RESERVE_LIST_COURSES, $this->dbName.INSTRUCTOR " .
               //"where RESERVE_LIST.EXPIRE_DATE <= to_date('$startDate', 'YYYY-mm-dd') and " .
               "where RESERVE_LIST.RESERVE_LIST_ID = RESERVE_LIST_COURSES.RESERVE_LIST_ID and " .
               "RESERVE_LIST_COURSES.INSTRUCTOR_ID = INSTRUCTOR.INSTRUCTOR_ID " .
               "group by INSTRUCTOR.INSTRUCTOR_ID, LAST_NAME, FIRST_NAME " .
               "order by LAST_NAME";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $deptList[$row['INSTRUCTOR_ID']] = $row['NAME'];
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        return $deptList;
    }
    
    function getCourses($startDate)
    {
        $deptList = array();

        $sql = "select COURSE.COURSE_NUMBER || ': ' || COURSE.COURSE_NAME as NAME, COURSE.COURSE_ID " .
               "from $this->dbName.RESERVE_LIST, $this->dbName.RESERVE_LIST_COURSES, $this->dbName.COURSE " .
               //"where RESERVE_LIST.EXPIRE_DATE <= to_date('$startDate', 'YYYY-mm-dd') and " .
               "where RESERVE_LIST.RESERVE_LIST_ID = RESERVE_LIST_COURSES.RESERVE_LIST_ID and " .
               "RESERVE_LIST_COURSES.COURSE_ID = COURSE.COURSE_ID " .
               "group by COURSE.COURSE_ID, COURSE_NUMBER, COURSE_NAME " .
               "order by COURSE_NUMBER";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $deptList[$row['COURSE_ID']] = $row['NAME'];
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        return $deptList;
    }

    function findReserves($course, $inst, $dept)
    {
        $recordList = array();

        $sql = "select MFHD_MASTER.DISPLAY_CALL_NO, BIB_TEXT.BIB_ID, BIB_TEXT.AUTHOR, BIB_TEXT.TITLE, BIB_TEXT.PUBLISHER, BIB_TEXT.PUBLISHER_DATE " .
               "from $this->dbName.MFHD_ITEM, $this->dbName.MFHD_MASTER, $this->dbName.BIB_TEXT, $this->dbName.BIB_ITEM, $this->dbName.RESERVE_LIST_COURSES, $this->dbName.RESERVE_LIST_ITEMS " .
               "where RESERVE_LIST_ITEMS.RESERVE_LIST_ID = RESERVE_LIST_COURSES.RESERVE_LIST_ID and " .
               "RESERVE_LIST_ITEMS.ITEM_ID = BIB_ITEM.ITEM_ID and " .
               "BIB_ITEM.BIB_ID = BIB_TEXT.BIB_ID and " .
               "MFHD_ITEM.ITEM_ID = BIB_ITEM.ITEM_ID and " .
               "MFHD_MASTER.MFHD_ID = MFHD_ITEM.MFHD_ID";
        if ($course != '') {
            $sql .= " and RESERVE_LIST_COURSES.COURSE_ID = $course";
        }
        if ($inst != '') {
            $sql .= " and RESERVE_LIST_COURSES.INSTRUCTOR_ID = $inst";
        }
        if ($dept != '') {
            $sql .= " and RESERVE_LIST_COURSES.DEPARTMENT_ID = $dept";
        }
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $recordList[] = $row;
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        return $recordList;
    }

    /*
    function findReserves($course, $inst, $dept)
    {
        $recordList = array();

        $reserve_subset = "";

        if ($course != '') {
            //$sql .= " and RESERVE_LIST_COURSES.COURSE_ID = $course";
            if ($reserve_subset !== "")
            {$reserve_subset .= " AND ";}
            $reserve_subset .= "RESERVE_LIST_COURSES.COURSE_ID = $course" ;
        }
        if ($inst != '') {
            //$sql .= " and RESERVE_LIST_COURSES.INSTRUCTOR_ID = $inst";
            if ($reserve_subset !== "")
            {$reserve_subset .= " AND ";}
            $reserve_subset .= "RESERVE_LIST_COURSES.INSTRUCTOR_ID = $inst" ;
        }
        if ($dept != '') {
            //$sql .= " and RESERVE_LIST_COURSES.DEPARTMENT_ID = $dept";
            if ($reserve_subset !== "")
            {$reserve_subset .= " AND ";}
            $reserve_subset .= "RESERVE_LIST_COURSES.DEPARTMENT_ID = $dept" ;
        }

        $reserve_subset = "(".$reserve_subset.")";
        $sql = " select MFHD_MASTER.DISPLAY_CALL_NO, BIB_TEXT.BIB_ID, BIB_TEXT.AUTHOR, BIB_TEXT.TITLE, " .
               " BIB_TEXT.PUBLISHER, BIB_TEXT.PUBLISHER_DATE FROM $this->dbName.BIB_TEXT, $this->dbName.MFHD_MASTER where " .
               " bib_text.bib_id = (select bib_mfhd.bib_id from bib_mfhd where bib_mfhd.mfhd_id = mfhd_master.mfhd_id) " .
               " and " .
               "  mfhd_master.mfhd_id in ( ".
               "  ((select distinct eitem.mfhd_id from $this->dbName.eitem where " .
               "    eitem.eitem_id in " .
               "    (select distinct reserve_list_eitems.eitem_id from " .
               "     $this->dbName.reserve_list_eitems where reserve_list_eitems.reserve_list_id in " .
               "     (select distinct reserve_list_courses.reserve_list_id from " .
               "      $this->dbName.reserve_list_courses where " .
               "      $reserve_subset )) )) union " .
               "  ((select distinct mfhd_item.mfhd_id from $this->dbName.mfhd_item where " .
               "    mfhd_item.item_id in " .
               "    (select distinct reserve_list_items.item_id from " .
               "    $this->dbName.reserve_list_items where reserve_list_items.reserve_list_id in " .
               "    (select distinct reserve_list_courses.reserve_list_id from " .
               "      $this->dbName.reserve_list_courses where $reserve_subset )) )) " .
               "  ) ";

        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $recordList[] = $row;
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        return $recordList;
    }
    */
    
    function getSuppressedRecords()
    {
        $list = array();

        $sql = "select BIB_MASTER.BIB_ID " .
               "from $this->dbName.BIB_MASTER " .
               "where BIB_MASTER.SUPPRESS_IN_OPAC='Y'";
        try {
            $sqlStmt = $this->db->prepare($sql);
            $sqlStmt->execute();
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $list[] = $row['BIB_ID'];
            }
        } catch (PDOException $e) {
            return new PEAR_Error($e->getMessage());
        }

        return $list;
    }
}

?>
