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

// require_once 'XML/Unserializer.php';
// require_once 'XML/Serializer.php';
// This is necessary for unserialize

require_once 'services/MyResearch/lib/User.php';


class UserAccount
{
  
    
    //Checks whether the user is logged in
    function isLoggedIn()
    {
        // # Is the cookie already set?
        // if (isset($_COOKIE['userinfo'])) {
        //     return unserialize($_COOKIE['userinfo']);
        // } 
        // 
        # Do we have a session? If so, already logged in
        global $user;
        if ($user) {
          return $user;
        }
        $user = UserAccount::login_from_session();
        if ($user) {
          return $user;
        } else {
          setcookie(session_name(), false, time() - 600, '/', '.lib.umich.edu', false, true);
          return false;
        }
        
    }


    // Log the user in if we have a session
    // Retrun the user object on success, false on failure
    
    function login_from_session() {
      // Session stuff
      $session_name = 'vufind_login';
      $session_save_path = '/n1/vufind/tmp/';

      session_name($session_name);
      session_set_cookie_params(0, '/', '.lib.umich.edu', false, true);
      session_save_path($session_save_path);


      if (! @session_start()) {
        // error_log("SESSION DIDN'T START!");
        return false;
      }

      // if (isset($_SESSION['user'])) {
      //   $user = $_SESSION['user'];
      //   echo "SESSION from session object";
      //   return $user;
      // }

      # Otherwise, get it from our fake session file
      $oobfile = $session_save_path . 'oob_' . session_id();
      //echo "Trying to read " . $oobfile;
      @$oobsession = fopen($oobfile,'r');
      $uname = false;

      if (!$oobsession) {
        // error_log("Couldn't get out-of-band session");
        return false;
      } else {
        // error_log("Reading from file $oobfile");
      }
        
      $oobline = fread($oobsession, 1000);
      //echo "\nOOB data is " .  $oobline;
      $oobdata = explode("\t", $oobline);
      $uname = $oobdata[0];
      $ip = $oobdata[1];
      $user = new User();
      $user->username = $uname;
      $user->cat_username = $uname;
      $user->password = $uname;
      $user->cat_password = $uname;
      $user->ip = $ip;
      $_SESSION['user'] = $user;
      //echo "User from oob";
      // $result = setcookie('userinfo', serialize($user), time()+60*60, '/');
      // if (!$result) {
      //     $user = false; // Blocked cookies
      // }      
 
      // error_log("Got user " . $user->username);
      return $user;
    }
    
 

    // function login($username, $password=null, $returnTo = null)
    // {
    //     global $configArray;
    // 
    //     switch($configArray['Authentication']['method']) {
    //         case 'COSIGN':
    //             return UserAccount::loginCosign($username);
    //             break;
    //         case 'LDAP':
    //             return UserAccount::loginLDAP($username, $password);
    //             break;
    //         case 'ILS':
    //             return UserAccount::loginILS($username, $password);
    //             break;
    //         case 'SIP2':
    //             return UserAccount::loginSIP2($username, $password);
    //             break;
    //         case 'DB':
    //         default:
    //             return UserAccount::loginDB($username, $password);
    //             break;
    //     }
    // }
    // 
    // // This Method needs to be cleaned up !
    // function loginLDAP($username, $password)
    // {
    //     global $configArray;
    // 
    //     if ($username != '' && $password != '') {
    //         // Attempt LDAP Authentication
    //         $conn = @ldap_connect($configArray['LDAP']['host'], $configArray['LDAP']['port']);
    //         if ($conn) {
    //             @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    //             @ldap_start_tls($conn);
    //             $rdn = $configArray['LDAP']['uid'] . '=' . $username . ',' . $configArray['LDAP']['basedn'];
    //             $search = ldap_search($conn, $configArray['LDAP']['basedn'], "uid=$username");
    //             $info = ldap_get_entries($conn, $search);
    // 
    //             if ($info['count']) {
    //                 @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    //                 @ldap_start_tls($conn);
    // 
    //                 // Fetch User Information
    //                 $result = @ldap_bind($conn, $info[0]['dn'], $password);
    //             }
    //             
    //             if ($result) {
    //                 // Set user info cookie
    //                 $search = ldap_search($conn, $configArray['LDAP']['basedn'], "uid=$username");
    //                 $info = ldap_get_entries($conn, $search);
    //                 $user = UserAccount::processLDAPUser($info);
    // 
    //                 // Set login cookie for 1 hour
    //                 $user->password = $password; // Need this for Metalib
    //                 $result = setcookie('userinfo', serialize($user), time()+60*60, '/');
    //                 if (!$result) {
    //                     $user = new PEAR_Error('Cookies are blocked, please check your browser');
    //                 }
    //             } else {
    //                 $user = new PEAR_Error('Invalid Login, Please try again.');
    //             }
    //             @ldap_close($conn);
    //         } else {
    //             $user = new PEAR_Error('Unable to connect to LDAP server');
    //         }
    //     } else {
    //         $user = new PEAR_Error('Invalid Login, Please try again.');
    //     }
    // 
    //     return $user;
    // }
    // 
    // /**
    //  * SIP2 Authentication
    //  *
    //  * @param   string  $username       The account username
    //  * @param   string  $password       The account password
    //  * @access  public
    //  * @author  Bob Wicksall <bwicksall@pls-net.org>
    //  */
    // function loginSIP2($username, $password)
    // {
    //     require_once 'sys/SIP2.php';
    //     
    //     global $configArray;
    // 
    //     if (isset($_POST['username']) && isset($_POST['password'])) {
    //         $username = $_POST['username'];
    //         $password = $_POST['password'];
    //         if ($username != '' && $password != '') {
    //             // Attempt SIP2 Authentication
    // 
    //             $mysip = new sip2;
    //             $mysip->hostname = $configArray['SIP2']['host'];
    //             $mysip->port = $configArray['SIP2']['port'];
    // 
    //             if ($mysip->connect()) {
    //                 //send selfcheck status message
    //                 $in = $mysip->msgSCStatus();
    //                 $msg_result = $mysip->get_message($in);
    // 
    //                 // Make sure the response is 98 as expected
    //                 if (preg_match("/^98/", $msg_result)) {
    //                     $result = $mysip->parseACSStatusResponse($msg_result);
    // 
    //                     //  Use result to populate SIP2 setings
    //                     $mysip->AO = $result['variable']['AO'][0]; /* set AO to value returned */
    //                     $mysip->AN = $result['variable']['AN'][0]; /* set AN to value returned */
    // 
    //                     $mysip->patron = $username;
    //                     $mysip->patronpwd = $password;
    // 
    //                     $in = $mysip->msgPatronStatusRequest();
    //                     $msg_result = $mysip->get_message($in);
    // 
    //                     // Make sure the response is 24 as expected
    //                     if (preg_match("/^24/", $msg_result)) {
    //                         $result = $mysip->parsePatronStatusResponse( $msg_result );
    // 
    //                         if (($result['variable']['BL'][0] == 'Y') and ($result['variable']['CQ'][0] == 'Y')) {
    //                             // Success!!!
    //                             $user = UserAccount::processSIP2User($result, $username, $password);
    // 
    //                             // Set login cookie for 1 hour
    //                             $user->password = $password; // Need this for Metalib
    //                             $result = setcookie('userinfo', serialize($user), time()+60*60, '/');
    //                             if (!$result) {
    //                                 $user = new PEAR_Error('Cookies are blocked, please check your browser');
    //                             }
    //                         } else {
    //                             $user = new PEAR_Error('Invalid Login, Please try again.');
    //                         }
    //                     } else {
    //                         $user = new PEAR_Error('Bad response from PatronStatusRequest');
    //                     }
    //                 } else {
    //                     $user = new PEAR_Error('Bad response from SC status message');
    //                 }
    //                 $mysip->disconnect();
    // 
    //             } else {
    //                 $user = new PEAR_Error('Unable to connect to SIP2 server');
    //             }
    //         } else {
    //             $user = new PEAR_Error('Invalid Login, Please try again.');
    //         }
    //     } else {
    //         $user = new PEAR_Error('Login Information Cannot be Blank.');
    //     }
    // 
    //    return $user;
    // }
    // 
    // function loginCosign($username, $returnTo)
    // {
    //   
    // }
    // 
    // function loginILS($username, $password)
    // {
    //     global $configArray;
    // 
    //     if (($username != '') && ($password != '')) {
    //         // Connect to Database
    //         $catalog = new CatalogConnection($configArray['Catalog']['driver']);
    // 
    //         if ($catalog->status) {
    //             if ($patron = $catalog->patronLogin($username, $password)) {
    //                 // Set login cookie for 1 hour
    //                 if (!setcookie('userinfo', serialize($patron), time()+60*60, $configArray['Site']['path'])) {
    //                     $user = new PEAR_Error('Cookies are blocked, please check your browser');
    //                 }
    //             } else {
    //                 $user = new PEAR_Error('Invalid Login, Please try again.');
    //             }
    //         } else {
    //             $user = new PEAR_Error('Error: Cannot connect to ILS');
    //         }
    //     } else {
    //         $user = new PEAR_Error('Login Information Cannot be Blank.');
    //     }
    // 
    //     return $user;
    // }
    // 
    // 
    // function loginDB($username, $password)
    // {
    //     global $configArray;
    // 
    //     if (($username != '') && ($password != '')) {
    //         $user = new User();
    //         $user->username = $_POST['username'];
    //         $user->password = $_POST['password'];
    //         if ($user->find(true)) {
    //             // Set login cookie for 1 hour
    //             if (!setcookie('userinfo', serialize($user), time()+60*60, $configArray['Site']['path'])) {
    //                 $user = new PEAR_Error('Cookies are blocked, please check your browser');
    //             }
    //         } else {
    //             $user = new PEAR_Error('Invalid Login, Please try again.');
    //         }
    //     } else {
    //         $user = new PEAR_Error('Login Information Cannot be Blank.');
    //     }
    // 
    //    return $user;
    // }
    // 
    // private function processLDAPUser($info)
    // {
    //     require_once "services/MyResearch/lib/User.php";
    //     
    //     $user = new User();
    //     $user->username = $info[0]['uid'][0];
    //     if ($user->find(true)) {
    //         $insert = false;
    //     } else {
    //         $insert = true;
    //     }
    // 
    //     $user->firstname = $info[0]['givenname'][0];
    //     $user->lastname = $info[0]['sn'][0];
    //     $user->email = $info[0]['mail'][0];
    //     $user->major = (isset($info[0]['studentmajor'])) ? $info[0]['studentmajor'][0] : 'null';
    //     $user->college = (isset($info[0]['studentcollege'])) ? $info[0]['studentcollege'][0] : $info[0]['departmentname'][0];
    // 
    //     if ($insert) {
    //         $user->created = date('Y-m-d');
    //         $user->insert();
    //     } else {
    //         $user->update();
    //     }
    // 
    //     return $user;
    // }
    // 
    // 
    // /**
    //  * Process SIP2 User Account
    //  *
    //  * @param   array   $info           An array of user information
    //  * @param   array   $username       The user's ILS username
    //  * @param   array   $password       The user's ILS password
    //  * @access  public
    //  * @author  Bob Wicksall <bwicksall@pls-net.org>
    //  */
    // private function processSIP2User($info, $username, $password)
    // {
    //     require_once "services/MyResearch/lib/User.php";
    // 
    //     $user = new User();
    //     $user->username = $info['variable']['AA'][0];
    //     if ($user->find(true)) {
    //         $insert = false;
    //     } else {
    //         $insert = true;
    //     }
    // 
    //     // This could potentially be different depending on the ILS.  Name could be Bob Wicksall or Wicksall, Bob.
    //     // This is currently assuming Wicksall, Bob
    //     $user->firstname = trim(substr($info['variable']['AE'][0], 1 + strripos($info['variable']['AE'][0], ',')));
    //     $user->lastname = trim(substr($info['variable']['AE'][0], 0, strripos($info['variable']['AE'][0], ',')));
    //     // I'm inserting the sip username and password since the ILS is the source.
    //     // Should revisit this.
    //     $user->cat_username = $username;
    //     $user->cat_password = $password;
    //     $user->email = 'email';
    //     $user->major = 'null';
    //     $user->college = 'null';
    // 
    //     if ($insert) {
    //         $user->created = date('Y-m-d');
    //         $user->insert();
    //     } else {
    //         $user->update();
    //     }
    // 
    //     return $user;
    // }

}
?>