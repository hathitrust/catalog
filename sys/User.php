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
    
 


}
?>