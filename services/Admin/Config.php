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

class Config extends Action
{
    function launch()
    {
        global $configArray;
        global $interface;

        if (isset($_POST['submit'])) {
            switch ($_GET['file']) {
                case 'stopwords.txt':
                    $this->processStopWords();
                    break;
                case 'synonyms.txt':
                    $this->processSynonyms();
                    break;
                case 'protwords.txt':
                    $this->processProtWords();
                    break;
                case 'elevate.xml':
                    $this->processElevate();
                    break;
                case 'config.ini':
                    $this->processConfig();
                    break;
            }
        }

        if (isset($_GET['file'])) {
            switch ($_GET['file']) {
                case 'stopwords.txt':
                    $this->showStopWords();
                    break;
                case 'synonyms.txt':
                    $this->showSynonyms();
                    break;
                case 'protwords.txt':
                    $this->showProtWords();
                    break;
                case 'elevate.xml':
                    $this->showElevate();
                    break;
                case 'config.ini':
                default:
                    $this->showConfig();
                    break;
            }
        } else {
            $interface->setTemplate('config.tpl');
        }
        $interface->display('layout-admin.tpl');
    }
    
    function showStopWords()
    {
        global $interface;
        global $configArray;

        $stopwords = file_get_contents($configArray['Index']['local'] . '/conf/stopwords.txt');
        $interface->assign('stopwords', $stopwords);
        $interface->setTemplate('config-stopwords.tpl');
    }
    
    function processStopWords()
    {
        global $configArray;

        file_put_contents($configArray['Index']['local'] . '/conf/stopwords.txt',
                          $_POST['stopwords']);
    }

    function showSynonyms()
    {
        global $interface;
        global $configArray;

        $synonyms = file_get_contents($configArray['Index']['local'] . '/conf/synonyms.txt');
        $interface->assign('synonyms', $synonyms);
        $interface->setTemplate('config-synonyms.tpl');
    }

    function processSynonyms()
    {
        global $configArray;

        file_put_contents($configArray['Index']['local'] . '/conf/synonyms.txt',
                          $_POST['synonyms']);
    }

    function showProtWords()
    {
        global $interface;
        global $configArray;

        $protwords = file_get_contents($configArray['Index']['local'] . '/conf/protwords.txt');
        $interface->assign('protwords', $protwords);
        $interface->setTemplate('config-protwords.tpl');
    }

    function processProtWords()
    {
        global $configArray;

        file_put_contents($configArray['Index']['local'] . '/conf/protwords.txt',
                          $_POST['protwords']);
    }

    function showElevate()
    {
        global $interface;
        global $configArray;

        $elevate = file_get_contents($configArray['Index']['local'] . '/conf/elevate.xml');
        $interface->assign('elevate', $elevate);
        $interface->setTemplate('config-elevate.tpl');
    }

    function processElevate()
    {
        global $configArray;

        $doc = new DOM_Document();
        $xml = $doc->saveXML();

        file_put_contents($configArray['Index']['local'] . '/conf/elevate.xml',
                          $xml);
    }

    function showConfig()
    {
        global $interface;
        global $configArray;

        $list = array();
        $themesDir = $configArray['Site']['local'] . '/interface/themes';
        echo $themesDir;
        if (is_dir($themesDir)) {
            if ($dh = opendir($themesDir)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file, 0, 1) != '.') {
                        $list[] = $file;
                    }
                }
            }
            closedir($dh);
        }
        $interface->assign('themeList', $list);

        $interface->setTemplate('config-config.tpl');
        $interface->assign('config', $configArray);
    }

    function processConfig()
    {
        global $configArray;
        
        $configArray['Site']['path'] = $_POST['path'];
        $configArray['Site']['url'] = $_POST['url'];
        $configArray['Site']['local'] = $_POST['local'];
        $configArray['Site']['title'] = $_POST['title'];
        $configArray['Site']['email'] = $_POST['email'];
        $configArray['Site']['language'] = $_POST['language'];

        $configArray['Index']['engine'] = $_POST['engine'];
        $configArray['Index']['url'] = $_POST['engineurl'];

        $configArray['Catalog']['catalog'] = $_POST['ils'];

        $configArray['Database']['database'] = $_POST['dbusername'] . ':' . $_POST['dbpassword'] . '@' . $_POST['dbhost'] . '/' . $_POST['dbname'];

        $configArray['Mail']['host'] = $_POST['mailhost'];
        $configArray['Mail']['port'] = $_POST['mailport'];

        $configArray['BookCovers']['provider'] = $_POST['bookcover_provider'];
        $configArray['BookCovers']['id'] = $_POST['bookcover_id'];

        $configArray['BookReviews']['provider'] = $_POST['bookreview_provider'];
        $configArray['BookReviews']['id'] = $_POST['bookreview_id'];

        $configArray['LDAP']['host'] = $_POST['ldaphost'];
        $configArray['LDAP']['port'] = $_POST['ldapport'];
        $configArray['LDAP']['basedn'] = $_POST['ldapbasedn'];
        $configArray['LDAP']['uid'] = $_POST['ldapuid'];

        $configArray['COinS']['identifier'] = $_POST['coinsID'];

        $configArray['OAI']['identifier'] = $_POST['oaiID'];

        $configArray['OpenURL']['url'] = $_POST['openurl'];
        
        $fileData = '';
        foreach ($configArray as $name => $section) {
            $fileData .= "[$name]\n";
            foreach ($section as $field => $value) {
                $fileData .= "$field = \"$value\"\n";
            }
        }
        file_put_contents('conf/config.ini', $fileData);
    }
}

?>
