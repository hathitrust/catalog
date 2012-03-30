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

/**
 * I18N_Translator
 *
 * The I18N_Translator class handles language translations via an Array that is
 * stored in an INI file. There is 1 ini file per language and upon construction
 * of the class, the appropriate language file is loaded. The class offers
 * functionality to manage the files as well, such as creating new language
 * files and adding/deleting of existing translations. Upon destruction, the
 * file is saved.
 *
 * @author      Andrew S. Nagy <andrew.nagy@villanova.edu>
 * @package     I18N_Translator
 * @category    I18N
 */
class I18N_Translator
{
    /**
     * Language translation files path
     *
     * @var     string
     * @access  public
     */
    var $path;

    /**
     * The specified language.
     *
     * @var     string
     * @access  public
     */
    var $langCode;

    /**
     * An array of the translated text
     *
     * @var     array
     * @access  public
     */
    var $words = array();

    /**
     * An array of the languages available
     *
     * @var     array
     * @access  public
     */
    var $languages = array();

    /**
     * Constructor
     *
     * @param   string $langCode    The ISO 639-1 Language Code
     * @access  public
     */
    function __construct($path, $langCode)
    {
        global $configArray;

        $this->path = $path;
        $this->langCode = $langCode;

        // Define available languages
        $this->getLanguages();


        // Load file in specified path
        if ($dh = opendir($path)) {
            $file = $path . '/' . $langCode . '.ini';
            if ($this->langCode != '' && is_file($file)) {
                $this->words = parse_ini_file($file);
            } else {
                return new PEAR_Error("Unknown language file");
            }
        } else {
            return new PEAR_Error("Cannot open $path for reading");
        }
        // check for supplemental translate files
        if (isset($configArray['translate_files'])) {
          foreach ($configArray['translate_files'] as $file_type => $file_name) {
            $file = "conf/" . $file_name;
            //echo "reading $file";
            $word_supplement = parse_ini_file($file);
            $this->words = array_merge($this->words, $word_supplement);
//location_desc = location_desc.ini
          }
        }
    }

    /**
     * Translate the phrase
     *
     * @param   string $phrase      The phrase to translate
     * @access  public
     * @note    Can be called statically if 2nd parameter is defined and load
     *          method is called before
     */
    function translate($phrase)
    {
        if (isset($this->words[$phrase])) {
            return $this->words[$phrase];
        } else {
            return $phrase;
        }
    }

    /**
     * Add new language to the scope
     *
     * @param   string $langCode    The ISO 639 Language Code
     * @access  public
     * @static
     * @note    Cannot be called statically
     */
    function addLanguage($langCode)
    {
        $this->languages[] = $langCode;

        $this->languages = array_unique($this->languages);

        // Define Destructor
        register_shutdown_function(array($this, 'save'));
    }

    /**
     * Remove a language from the scope
     *
     * @param   string $langCode    The ISO 639 Language Code
     * @access  public
     * @static
     * @note    Cannot be called statically
     */
    function removeLanguage($langCode)
    {
        $key = array_search($langCode);
        unset($this->languages[$key]);

        // Define Destructor
        register_shutdown_function(array($this, 'save'));
    }

    function getLanguages()
    {
        if (is_file($this->path)) {
            $dir = dirname($this->path);
        } else {
            $dir = $this->path;
        }

        if (is_dir($dir)) {
           if ($dh = opendir($dir)) {
               while (($file = readdir($dh)) !== false) {
                   if ($pos = strpos($file, '.ini')) {
                       $this->languages[] = substr($file, 0, $pos);
                   }
               }
           }
           closedir($dh);
        } else {
            return new PEAR_Error('Invalid Path');
        }

        return $this->languages;
    }

    /**
     * Define a translation
     *
     * @param   string $phrase      The phrase that has been translated
     * @param   string $translation The phrase translation
     * @param   string $langCode    The language in which the phrase was
     *                              translated.  This is specified by the
     *                              ISO 639-1 Language Code
     * @access  public
     * @note    Can be called statically if 3rd parameter is defined and load
     *          method is called before hand
    function setTranslation($phrase, $translation, $langCode = null)
    {
        if ($langCode != null) {
            $GLOBALS['I18N_Translator_Text_' . $langCode][$phrase] = $translation;
            $this->languages[] = $langCode;
        } else {
            $GLOBALS['I18N_Translator_Text_' . $this->langCode][$phrase] = $translation;
        }

        $this->languages = array_unique($this->languages);

        // Define Destructor
        register_shutdown_function(array($this, 'save'));
    }
     */

    /*
    function removeTranslation($phrase)
    {
        foreach ($this->getLanguages() as $lang) {
            print_r(array_keys($GLOBALS['I18N_Translator_Text_' . $lang]), $phrase);
            unset($GLOBALS['I18N_Translator_Text_' . $lang][$phrase]);
        }

        // Define Destructor
        register_shutdown_function(array($this, 'save'));
    }
    */

    /**
     * Unset the translation data from scope
     *
     * @access  public
     * @note    Cannot be called statically
    function unload()
    {
        if (count($languages)) {
            foreach($languages as $lang) {
                unset($GLOBALS['I18N_Translator_Text_' . $lang]);
            }
        }
    }
     */

    /**
     * Defines the path to the translation files
     *
     * @param   string $path        The path to the translation files.
     * @access  public
     * @note    Cannot be called statically
     */
    function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Saves the translation data to php include files
     *
     * @param   string $path        The file to load or directory containing
     *                              translation files to load.
     * @access  public
     * @static
     * @note    Should only be called statically
     * @todo    Do not overright entire file, similiar to DB_Dataobject's
     *          createTables.php
     */
    function save()
    {
        if ($this->path != null) {
            if ($fp = @fopen($this->path . '/' . $this->langCode . '.ini', 'w+')) {
                foreach($this->words as $phrase => $translation) {
                    fwrite($fp, $phrase . ' = ' . $translation . "\n");
                }
            } else {
                return new PEAR_Error("Cannot save file in $this->path");
            }
        } else {
            return new PEAR_Error("A path to save the file was not specified");
        }
    }
}
?>
