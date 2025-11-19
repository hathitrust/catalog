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

require_once 'Smarty/Smarty.class.php';

// Smarty Extension class
class UInterface extends Smarty
{
    function __construct()
    {
        global $configArray;
        $local = $configArray['Site']['local'];
        $theme = $configArray['Site']['theme'];


        $this->template_dir  = "$local/interface/themes/$theme";

	# Set up the space for compiled files
        $comp = "$local/interface/compile/$theme";

        if (!is_dir($comp)) {
          mkdir($comp, 0777);
          chmod($comp, 0777);
        }

        $this->compile_dir   = $comp;
        $this->cache_dir     = "$local/interface/cache";
        $this->plugins_dir   = array('plugins', "$local/interface/plugins");
        $this->caching       = false;
        $this->debug         = true;
        $this->compile_check = true;

        unset($local);

        $this->register_function('translate', 'translate');
        $this->register_function('char', 'char');

        $this->assign('site', $configArray['Site']);
        $this->assign('path', $configArray['Site']['path']);
        $this->assign('url', $configArray['Site']['url']);

        $this->assign('fullPath', $_SERVER['REQUEST_URI']);
        $this->assign('fullPath_esc', preg_replace('/&/', '&amp;', $_SERVER['REQUEST_URI']));

        $this->assign('openUrlLink', $configArray['OpenURL']['url']);
        $this->assign('supportEmail', $configArray['Site']['email']);
        $this->assign('regular_url', $configArray['Site']['regular_url']);
        $this->assign('ht_url', $configArray['Site']['ht_url']);
        $this->assign('home_url', $configArray['Site']['home_url']);

        if ( array_key_exists('HTstatus', $_COOKIE) ) {
            $status = $_COOKIE['HTstatus'];
            $this->assign('ht_status', json_decode($_COOKIE['HTstatus'], TRUE));
        }

        if (isset($configArray['LDAP'])) {
            $this->assign('authMethod', 'LDAP');
        }

        # Look for firebird manifest first under
        # $FIREBIRD_HOME/dist/manifest.json (if $FIREBIRD_HOME env var is
        # defined); otherwise fall back to trying to find it from the server
        # DOCUMENT_ROOT, otherwise give up

        $BABEL_ROOT = str_replace('catalog', 'babel',
          dirname($_SERVER['DOCUMENT_ROOT']));
        $FIREBIRD_HOME = getenv('FIREBIRD_HOME');
        if(!$FIREBIRD_HOME) { $FIREBIRD_HOME = $BABEL_ROOT . '/firebird-common'; }
        $firebird_manifest_filename = $FIREBIRD_HOME . '/dist/manifest.json';

        if (file_exists(($firebird_manifest_filename))) {
            $this->assign('firebird_manifest', json_decode(file_get_contents($firebird_manifest_filename), true));
        }
    }

    function setTemplate($tpl)
    {
        $this->assign('pageTemplate', $tpl);
    }
    function setPageTitle($title)
    {
        $this->assign('pageTitle', $title);
    }
}

function translate($params)
{
    global $translator;
    if (is_array($params)) {
        return $translator->translate($params['text']);
    } else {
        return $translator->translate($params);
    }
}

function char($params)
{
    extract($params);
    return chr($int);
}

?>
