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

require_once "Action.php";

require_once 'services/MyResearch/Login.php';

require_once 'Mail/RFC822.php';


class Account extends Action
{
    function __construct()
    {
      // Don't cache this stuff!!!
      header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
      header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
      header('Pragma: no-cache');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');        
    }

    function launch()
    {
        global $interface;

        if (isset($_POST['submit'])) {
            $result = $this->processInput();
            if (PEAR::isError($result)) {
                $interface->assign('message', $result->getMessage());
                $interface->assign('formVars', $_POST);
                $interface->setTemplate('account.tpl');
                $interface->display('layout.tpl');
            } else {
                Login::launch();
            }
        } else {
            $interface->setTemplate('account.tpl');
            $interface->display('layout.tpl');
        }
    }
    
    function processInput()
    {
        // Validate Input
        if (trim($_POST['username']) == '') {
            return new PEAR_Error('Username cannot be blank');
        }
        if (trim($_POST['password']) == '') {
            return new PEAR_Error('Password cannot be blank');
        }
        if ($_POST['password'] != $_POST['password2']) {
            return new PEAR_Error('Password do not match');
        }
        if (!Mail_RFC822::isValidInetAddress($_POST['email'])) {
            return new PEAR_Error('email address is invalid');
        }

        // Create Account
        $user = new User();
        $user->username = $_POST['username'];
        if (!$user->find()) {
            $user->email = $_POST['email'];
            if (!$user->find()) {
                $user->password = $_POST['password'];
                $user->firstname = $_POST['firstname'];
                $user->lastname = $_POST['lastname'];
                $user->created = date('Y-m-d h:i:s');
                $user->insert();
            } else {
                return new PEAR_Error('That email address is already used');
            }
        } else {
            return new PEAR_Error('That username is already taken');
        }
        
        return true;
    }
}

?>