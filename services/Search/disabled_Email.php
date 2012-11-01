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

require_once 'Mail.php';
require_once 'Mail/RFC822.php';
require_once 'services/Search/SearchStructure.php';

class Email extends Action
{
    function launch()
    {
        global $interface;
        global $configArray;

        if (isset($_POST['submit'])) {
            $result = $this->sendEmail($_POST['url'], $_POST['to'], $_POST['from'], $_POST['message']);
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
            return $interface->fetch('Search/email.tpl');
        } else {
            // $interface->assign('title', $this->details['title']);
            $interface->setPageTitle('Email This Search');
            $interface->assign('subTemplate', 'email.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl', 'SearchEmail' . $_GET['url']);
        }
    }
    
    function sendEmail($url, $to, $from, $message)
    {
        global $configArray;
        
        if (!Mail_RFC822::isValidInetAddress($to)) {
            return new PEAR_Error('Invalid Email Address');
        }
                

        $subject = 'HathiTrust Catalog Search Results';
        $body = "This email was sent from: $from\n" .
                   "------------------------------------------------------------\n\n";
        if ($message != '') {
            $body .= "Message From Sender:\n$message\n\n";
        }
                 
        
        $body .= "  Link: $url\n" .
                 "------------------------------------------------------------\n\n";
                   
        $headers['From']    = $from;
        $headers['To']      = $to;
        $headers['Subject'] = $subject;

        $mail =& Mail::factory('sendmail', array('host' => $configArray['Mail']['host'],
                                             'port' => $configArray['Mail']['port']));
        return $mail->send($to, $headers, $body);
    }
}
?>
