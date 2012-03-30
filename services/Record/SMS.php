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

require_once 'Record.php';

require_once 'Mail.php';
require_once 'sys/ActivityLog.php';
require_once 'sys/VFUser.php';

class SMS extends Record
{
    function launch()
    {
        global $interface;
        
        if (isset($_POST['submit'])) {
            $result = $this->sendSMS();
            if (PEAR::isError($result)) {
                $interface->assign('error', $result->getMessage());
                $interface->display('Record/sms-error.tpl');
            } else {
                $interface->display('Record/sms-sent.tpl');
            }
        } else {
            return $this->display();
        }
    }
    
    function display()
    {
        global $interface;
        
        if (isset($_GET['lightbox'])) {
            // Use for lightbox
            $interface->assign('title', $_GET['message']);
            return $interface->fetch('Record/sms.tpl');
        } else {
            // Display Page
            $interface->setPageTitle('Record Citations');
            $interface->assign('subTemplate', 'sms.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl', 'RecordSMS' . $_GET['id']);
        }
    }
    
    // Email SMS
    function sendSMS()
    {
        global $configArray;
        
        // Log attempt
        
        $alog = ActivityLog::singleton();
        $user = VFUser::singleton();
        

        if (!isset($_GET['provider']) || $_GET['provider'] == '') {
            echo '<result>Error</result>';
            return new PEAR_Error('Unknown Carrier');
        }

        $carriers = array('virgin' => 'vmobl.com',
                          'att' => 'txt.att.net',
                          'verizon' => 'vtext.com',
                          'nextel' => 'messaging.nextel.com',
                          'sprint' => 'messaging.sprintpcs.com',
                          'tmobile' => 'tmomail.net',
                          'alltel' => 'message.alltel.com',
                          'Cricket' => 'mms.mycricket.com');

        // Setup Search Engine Connection
        $class = $configArray['Index']['engine'];
        $db = new $class($configArray['Index']['url']);
        if ($configArray['System']['debug']) {
            $db->debug = true;
        }

        // Load Bib Data
        $result = $db->getRecord($_GET['id']);

        // Get Holdings
        try {
            $catalog = new CatalogConnection($configArray['Catalog']['driver']);
        } catch (PDOException $e) {
            echo '<result>Error</result>';
            return new PEAR_Error('Cannot connect to ILS');
        }
        $holdings = $catalog->getStatus($_GET['id']);
        if (PEAR::isError($holdings)) {
            echo '<result>Error</result>';
            return $holdings;
        }
        
        $phonenumber = $_GET['to'];
        $alog->log('rectext', $user->username, $phonenumber);
        

        $message = $result['title'] . "\n" .
                   "Location: " . $holdings[0]['location'] . "\n" .
                   "Call #: " . $holdings[0]['callnumber'] . "\n" .
                   $configArray['Site']['url'] . '/Record/' . $_GET['id'];

        $to = $_GET['to'] . '@' . $carriers[$_GET['provider']];

        $headers['From']    = $configArray['Site']['email'];
        $headers['To']      = $to;
        $headers['Subject'] = '';

        $mail =& Mail::factory('smtp', array('host' => $configArray['Mail']['host'],
                                             'port' => $configArray['Mail']['port']));
        if (!PEAR::isError($mail)) {
            return $mail->send($to, $headers, $message);
        } else {
            echo '<result>Error</result>';
            return $mail;
        }
    }

}
?>