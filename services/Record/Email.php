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

require_once 'Record.php';

require_once 'Mail.php';
require_once 'Mail/RFC822.php';
require_once 'services/Record/RecordUtils.php';

class Email extends Record
{
    function launch()
    {
        global $interface;
        global $configArray;

        if (isset($_POST['submit'])) {
            $result = $this->sendEmail($_POST['to'], $_POST['from'], $_POST['message']);
            if (!PEAR::isError($result)) {
                require_once 'Home.php';
                Home::launch();
                exit();
            } else {
                $interface->assign('message', $result->getMessage());
            }
        }
        
        // Display Page
        if (isset($_GET['lightbox'])) {
            $interface->assign('title', $_GET['message']);
            return $interface->fetch('Record/email.tpl');
        } else {
            // $interface->assign('title', $this->details['title']);
            $interface->setPageTitle('Email Record');
            $interface->assign('subTemplate', 'email.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl', 'RecordEmail' . $_GET['id']);
        }
    }
    
    function sendEmail($to, $from, $message)
    {
        global $configArray;
        global $interface;
        
        if (!Mail_RFC822::isValidInetAddress($to)) {
            return new PEAR_Error('Invalid Email Address');
        }
        
        $template = 'Record/email_body.tpl';
        $r = $this->record;
        $result = array();
        $result['record'] = array($r);
        $ru = new RecordUtils;
        $holdings = $ru->getStatuses($result);
        foreach ($holdings[$r['id']] as $loc) {
          if ($loc['callnumber'] == "") {
            continue;
          }
          $callnos[] = $loc['location'] . ": " . $loc['callnumber'];
        }
         
        $subject = "Library Catalog Record: " . $this->record['title'];
        
        
        $message = "This email was sent from: $from\n" .
                   "------------------------------------------------------------\n\n" .
                   "  " . $this->record['title'] . "\n" .
                   "  Link: " . $configArray['Site']['url'] . '/Record/' . $this->id . "\n" .
                   "  Holdings: \n            " .
                   implode("\n            ", $callnos) . "\n" . 
                   "------------------------------------------------------------\n\n" .
                   "Message From Sender:\n$message";
                   
        $headers['From']    = $from;
        $headers['To']      = $to;
        $headers['Subject'] = $subject;

        $mail =& Mail::factory('sendmail', array('host' => $configArray['Mail']['host'],
                                             'port' => $configArray['Mail']['port']));
        return $mail->send($to, $headers, $message);
    }
}
?>
