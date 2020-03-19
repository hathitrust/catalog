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

# Set the timezone
ini_set('date.timezone', 'America/Detroit');

/** CORE APPLICATION CONTOLLER **/

// Require System Libraries
require_once 'PEAR.php';

require_once 'sys/Interface.php';
require_once 'sys/User.php';
require_once 'sys/Translator.php';
require_once 'sys/VFSession.php';
require_once 'sys/VFUser.php';
require_once 'sys/HTStatus.php';
require_once 'sys/ActivityLog.php';
require_once 'services/Record/RecordUtils.php';
require_once 'sys/mobile_device_detect.php';
require_once 'services/Search/SearchStructure.php';
require_once 'sys/SolrConnection.php';
require_once 'sys/Solr.php';

// Set up for autoload
function sample_autoloader($class) {
  require str_replace('_', '/', $class) . '.php';
}
spl_autoload_register('sample_autoloader');

// Sets global error handler for PEAR errors
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handlePEARError');


//######################################
// Configuration and utility objects
//######################################



$configArray = parse_ini_file('conf/config.ini', true);

## Now munge it based on the hostname

$hn =  $_SERVER['HTTP_HOST'];

if (isset($configArray[$hn])) {
  foreach ($configArray[$hn] as $key => $val) {
    $configArray['Site'][$key] = $val;
  }
}

if (isset($configArray[$hn], $configArray[$hn]['extraFilters'])) {
  $configArray['extraFilters'] = array('ht' => $configArray[$hn]['extraFilters']);
}

#######################################
# Mobile detection
#######################################
# IF we're hitting the top page,
#  AND it's a hathitrust.org
#  AND there's a mobile site configured
#  AND we're not already *at* the mobile site
#  AND it's a mobile device
# THEN redirect to the mobile site

$sname = $_SERVER["HTTP_HOST"];
if ( 0 && (strlen($_SERVER['REQUEST_URI']) <=1) &&
     (preg_match('/\.hathitrust\.org$/', $sname)) &&
     (isset($configArray['Site']['mobile_machine']))  &&
     ($sname != $configArray['Site']['mobile_machine']) &&
     mobile_device_detect()
   ) {
     header("Location: //" . $configArray['Site']['mobile_machine'] . "/");
     exit();
}

##############################################
# Use mobile theme and stuff?
##############################################


if (($sname == $configArray['Site']['mobile_machine']) || (isset($_REQUEST['force_mobile']))) {
  $configArray['Site']['url'] = '//' . $configArray['Site']['mobile_machine'];
  $configArray['Site']['theme'] = $configArray['Site']['mobile_theme'];
}


$session = VFSession::instance();
$alog = ActivityLog::singleton();
$user = VFUser::singleton();

# Set up the interface

$authspecs = AuthSpecs::singleton();
$interface = new UInterface();
$interface->assign('machine', $_SERVER['SERVER_ADDR']);
$interface->assign('session', $session);
$interface->assign('regular_url', isset($configArray['Site']['regular_url']) ?
                                  $configArray['Site']['regular_url'] :
                                  $configArray['Site']['url']);

# Should we log?
if (isset($_REQUEST['donotlog'])) {
  ActivityLog::$donotlog = true;
}

#####################################
# Are we USA or non-USA?
#######################################

if (!$session->is_set('country')) {
  $ip  = $_SERVER['REMOTE_ADDR'];
  $country = geoip_country_code3_by_name($ip);
  $session->set('country', $country);

  if ($country == 'USA') {
    $session->set('inUSA', true);
  } else {
    $session->set('inUSA', false);
  }
}

if (isset($_REQUEST['intl'])) {
  if ($_REQUEST['intl'] == 'true') {
    $session->set('inUSA', false);
  } else {
    $session->set('inUSA', true);
  }
}


//################################
//     HTStatus
//
//  Fake institution_code with etas=myinstcode in URL
//###############################

$htstatus = new HTStatus();
if (isset($_REQUEST['etas'])) {
  $htstatus->fakefill($_REQUEST['etas']);
}



// if ($_SERVER['REMOTE_ADDR'] == '141.211.43.192') {
//   $session->set('inUSA', true);
// }

//######################################
// Check system availability
//######################################

if (!$configArray['System']['available']) {
    $interface->display('unavailable.tpl');
    exit();
}


//######################################
// Set correct Solr URL
//######################################
if (isset($configArray['Index']['urlFile'])) {
  $configSolrURL = parse_ini_file("conf/" . $configArray['Index']['urlFile']);
  $configArray['Index']['url'] = $configSolrURL['url'];
}

//#####################################
// Load up ht HT namespace map
//#####################################

$HT_NAMESPACES = $eval = eval(file_get_contents($configArray['Site']['facetDir'] . '/ht_namespaces.php'));
$HT_COLLECTIONS = eval(file_get_contents($configArray['Site']['facetDir'] . '/ht_collections.php'));
$interface->assign('ht_namespace_map', $HT_NAMESPACES);


//######################################
// Language translation
//######################################
if (isset($_POST['mylang'])) {
    $language = $_POST['mylang'];
    setcookie('language', $language, null, '/');
} else {
    $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : $configArray['Site']['language'];
}
$translator = new I18N_Translator('lang', $language);
setlocale(LC_MONETARY, $configArray['Site']['locale']);


//
// //######################################
// // Set up default institution and stick in session
// //######################################
//
// $instConfig = parse_ini_file('conf/instList.ini', true);
// $instList = $instConfig['inst'];
// $instForIP = $instConfig['instForIP'];
// $interface->assign('instList', $instList);
// $ip = $_SERVER['REMOTE_ADDR'];
//
// // if inst is set in _REQUEST, add to session
// if (isset($_REQUEST['inst'])) {
//   $inst = $_REQUEST['inst'];
//   if ($inst != '' and !isset($instList[$inst])) {
//     error_log("invalid inst in _REQUEST: $inst");
//     $inst = '';
//   }
//   $session->set("inst", $inst);
// }
// // if inst hasn't already been set, set it based on ip address.  Default is 'all'
// if (!$session->is_set('inst')) {
//   $inst = 'all';  // default value
//   foreach ($instForIP as $ip_pattern => $ip_inst) {
//     if  (preg_match("/^$ip_pattern/", $ip)) {
//       $inst = $ip_inst;
//       //      error_log("ip match for inst, $ip -> $$ip_pattern -> $ip_inst");
//       break;
//     }
//   }
//   $session->set("inst", $inst);
// }
//
//
//
//

//###################################################
// Avoid XSS attacks by disallowing the '>' character
//###################################################
//
// There's gotta be a better way and a better place to do this.

foreach ($_REQUEST as $key => $val) {
  unset($_REQUEST[$key]);
  $key = preg_replace('/>/', '', $key);
  if (is_array($val)) {
    foreach ($val as $index => $v) {
      $val[$index] = preg_replace('/>/', '', $v);
    }
  } else {
    $val = preg_replace('/>/', '', $val);
  }
  $_REQUEST[$key] = $val;
}


//######################
// Project UNICORN / HT Full text only
//#########################
/*
  Change in plans: two variables

  setft    sets the session variable and controls the checkbox
  ft       affects the current search


*/

if (isset($_REQUEST['ft']) && $_REQUEST['ft'] == 'ft') {
  $interface->assign('is_fullview', true);
  $htftonly = true;
} else {
  $_REQUEST['ft'] = '';
  $interface->assign('is_fullview', false);
  $htftonly = false;
}


if (isset($_REQUEST['setft'])) {
  $session->set("htftonly", $htftonly);
}


if ($session->get('htftonly')) {
  $interface->assign('check_ft_checkbox', true);
}


//============================
// Project Unicorn: searchtype and lookfor
//
// Map the unicorn searchtype to our 'type'
// and note that it's a unicorn search

if (isset($_REQUEST['searchtype'])) {
  $_REQUEST['type'] = $_REQUEST['searchtype'];
  $interface->assign('searchtype', $_REQUEST['searchtype']);
}

if (isset($_REQUEST['type'])) {
  $t = $_REQUEST['type'];
  $t = is_array($t) ? $t : array($t);
  if (count($t) == 1) {
    $_REQUEST['searchtype'] = $t[0];
    $interface->assign('searchtype', $_REQUEST['searchtype']);
  } else {
  }
} else {
}


$ss = new SearchStructure();
if (count($ss->search) == 1) {
    $interface->assign('lookfor', $ss->search[0][1]);
    $interface->assign('type', $ss->search[0][0]);
}




//######################################
// Determine module and action
//######################################
$module = 'Search';
$module = (isset($_GET['module'])) ? $_GET['module'] : $module;
$action = (isset($_GET['action'])) ? $_GET['action'] : 'Home';




//######################################
// Add basics to the interface
//######################################

$interface->assign('configArray', $configArray);
$interface->assign('userLang', $language);
if ($session->is_set('inst')) {
  $interface->assign('inst', $session->get('inst'));
}
$interface->assign('path', $configArray['Site']['url']);
$interface->assign('module', $module);
$interface->assign('action', $action);
$interface->assign('user', $user);
$interface->assign('uuid', $session->uuid);
$interface->assign('ru', new RecordUtils());

$interface->assign('unicorn_root', $configArray['Site']['unicorn_root']);
$interface->assign('handle_prefix', $configArray['Site']['handle_prefix']);

//######################################
// Authentication
//######################################

$interface->assign('loginURLBase', $authspecs['RedirectAuth']['loginURLBase'] );

if ($user) {
  $interface->assign('uniqname', $user->username);
  if (is_null($user->patron) || !isset($user->patron)) {
    $interface->assign('username', $user->username);
  } else {
    $interface->assign('username', implode(' ', array($user->patron->firstname, $user->patron->lastname)));
  }
} else {
  $interface->assign('loginURL', $authspecs['RedirectAuth']['loginURLBase'] . rawurlencode(curPageURL()));
}



//######################################
// Call the appropriate action
//######################################

if (is_readable("services/$module/$action.php")) {
    require_once "services/$module/$action.php";
    if (class_exists($action)) {
        $service = new $action();
        $service->launch();
    } else {
        PEAR::raiseError(new PEAR_Error('Unknown Action'));
    }
} else {
    PEAR::RaiseError(new PEAR_Error("Cannot Load Action: module=$module, action=$action"));
}


//######################################
// Utility functions
//######################################

// Process any errors that are thrown
function handlePEARError($error, $method = null) {
    $interface = new UInterface();
    $interface->assign('error', $error);
    header('HTTP/1.1 404 Not Found');
    $interface->display('error.tpl');
    foreach ($error->backtrace as $trace) {
        echo '[' . $trace['line'] . '] ' . $trace['file'] . '<br>';
    }
    exit();
}

function curPageURL() {
 $pageURL = 'https';
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}


?>
