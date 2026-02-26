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

require_once 'vendor/autoload.php';

use Smarty\Smarty;

# Smarty Extension class
class UInterface extends Smarty
{
    function __construct()
    {
        parent::__construct();
        global $configArray;
        $local = $configArray['Site']['local'];
        $theme = $configArray['Site']['theme'];

        # set a single directory where the config file are stored.
        $this->setTemplateDir("$local/interface/themes/$theme");

	    # Set up the space for compiled files
        $comp = "$local/interface/compile/$theme";

        # The compile dir and cache dir need to be writable for the user running the PHP script.
        if (!is_dir($comp)) {
          mkdir($comp, 0777);
          chmod($comp, 0777);
        }

        // set another path to store compiled templates
        $this->setCompileDir($comp);

        // set another path to store caches of templates to speed up the loading of templates
        $this->setCacheDir("$local/interface/cache");
        # Register legacy plugin files from the custom directory
        $this->registerLegacyPlugins("$local/interface/plugins");
        $this->setCaching(Smarty::CACHING_OFF);
        $this->setDebugging(false);
        $this->setCompileCheck(Smarty::COMPILECHECK_ON);


        unset($local);

        // Register custom functions 
        // These are used in the templates as {translate text="Back to Record"} to output the translated text
        // registerPlugin documentation: https://www.smarty.net/docs/en/api.register.plugin.tpl
        $this->registerPlugin('function', 'translate', 'translate');
        $this->registerPlugin('function', 'char', 'char');

        // Register PHP functions that used to be invoked via Smarty's @modifier syntax.
        // These are used in the templates as {$var|json_encode="value"} to output the JSON-encoded value of $var, 
        // or {$array|count} to output the count of items in $array.
        // modifier are used to transform the output of a variable, so they are registered as 'modifier' plugins.
        $this->registerPlugin('modifier', 'json_encode', 'json_encode');
        $this->registerPlugin('modifier', 'count', 'count');


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

    /**
     * Registers legacy plugins from a specified directory.
     *
     * This method looks for PHP files in the given directory that match the naming convention
     * for Smarty plugins (function.{name}.php or modifier.{name}.php). It then includes these files
     * and registers the corresponding functions as Smarty plugins.
     * 
     * Smarty 5 requires to register each plugin or load an extension instead of pointing it at a directory.
     * 
     *
     * @param string $directory The directory to search for legacy plugin files.
     */

    private function registerLegacyPlugins(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php') as $file) {
            require_once $file;
            $basename = basename($file);
            if (!preg_match('/^(function|modifier)\\.([^\\.]+)\\.php$/', $basename, $matches)) {
                continue;
            }
            $type = $matches[1];
            $name = $matches[2];
            $callback = 'smarty_' . $type . '_' . $name;
            if (!function_exists($callback)) {
                continue;
            }
            $this->registerPlugin($type, $name, $callback);
        }
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
