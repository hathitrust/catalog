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

class Cite extends Record
{
    function launch()
    {
        global $interface;
        
        // Create Title
        $titleField = $this->marcRecord->getField('245');
        $titleA = $titleField->getSubfield('a');
        $title = trim(cleanTitle($titleA->getData()));
        if ($titleB = $titleField->getSubfield('b')) {
            $title .= ': ' . cleanTitle($titleB->getData());
        }
        $title = trim($title);
        
        // Add period to titles not ending in punctuation
        if (!((substr($title, -1) == '?') || (substr($title, -1) == '!'))) {
            $title .= '.';
        }
        $interface->assign('apatitle', $title);
        $interface->assign('mlatitle', title_case($title));

        // Build author list
        $authorList = array();
        if ($authorField = $this->marcRecord->getField('100')) {
            $authorA = $authorField->getSubfield('a');
            $authorList[] = $authorA->getData();
        }
        if ($authorField = $this->marcRecord->getField('700')) {
            $authorA = $authorField->getSubfield('a');
            $authorList[] = $authorA->getData();
        }
        $authorList = array_unique($authorList);

        // Create Author List for APA style
        $i = 0;
        $authorStr = '';
        foreach($authorList as $author) {
            $author = abbreviateName($author);
            if (($i == count($authorList)) && ($i > 0)) { // Last
                $authorStr .= ', &amp; ' . cleanTitle($author) . '.';
            } elseif ($i > 0) {
                $authorStr .= ', ' . cleanTitle($author) . '.';
            } else {
                $authorStr .= cleanTitle($author) . '.';
            }
            $i++;
        }
        $interface->assign('apaAuthorList', trim($authorStr));

        // Create Author List for MLA style
        $i = 0;
        $authorStr = '';
        if (count($authorList) > 4) {
            $authorStr = cleanTitle($author) . ', et al.';
        } else {
            foreach($authorList as $author) {
                if (($i+1 == count($authorList)) && ($i > 0)) { // Last
                    $authorStr .= ', and ' . reverse(cleanTitle($author));
                } elseif ($i > 0) {
                    $authorStr .= ', ' . reverse(cleanTitle($author));
                } else { // First
                    $authorStr .= cleanTitle($author);
                }
                $i++;
            }
        }
        $interface->assign('mlaAuthorList', trim($authorStr));

        // Setup Publisher information
        $publisher = "";
        if ($publisherField = $this->marcRecord->getField('260')) {
            if ($publisherB = $publisherField->getSubfield('b')) {
                $publisherA = $publisherField->getSubfield('a');
                if ($publisherA && $publisherB) {
                  $publisher = trim(cleanTitle($publisherA->getData())) . ': ' .
                               trim($publisherB->getData());
                }
            } else {
                $publisherA = $publisherField->getSubfield('a');
                if ($publisherA) {
                  $publisher = trim($publisherA->getData());
                } elseif ($publisherB = $publisherField->getSubfield('b')) {
                  $publisher = trim($publisherB->getData());
                }
            }
        }
        $interface->assign('publisher', cleanTitle($publisher));
        

        if (isset($_GET['lightbox'])) {
            // Use for lightbox
            $interface->assign('title', $_GET['message']);
            return $interface->fetch('Record/cite.tpl');
            //$html = file_get_contents('http://www.worldcat.org/oclc/4670293?page=citation');
            //return transform($html, 'services/Record/xsl/worldcat-cite.xsl');
        } else {
            // Display Page
            $interface->setPageTitle('Record Citations');
            $interface->assign('subTemplate', 'cite.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl', 'RecordCite' . $_GET['id']);
        }
    }
}

function abbreviateName($name)
{
    if (!(isset($name) && preg_match('/\S/', $name))) {
      return '';
    }
    $parts = explode(', ', $name);
    $fnameParts = array();
    if (isset($parts[1])) {
      $fnameParts = explode(' ' , $parts[1]);
    }
    $name = $parts[0] . ', ' . substr($fnameParts[0], 0, 1);
    array_shift($fnameParts);
    $name .= '. ' . implode(' ', $fnameParts);
    return trim($name);
}

function cleanTitle($title)
{
    $title = trim($title);
    if ((substr($title, -1) == '.') ||
        (substr($title, -1) == ',') ||
        (substr($title, -1) == ':') ||
        (substr($title, -1) == ';') ||
        (substr($title, -1) == '/')) {
        $title = substr($title, 0, -1);
    }
    return $title;
}

function reverse($str)
{
    if (preg_match('/,/', $str)) {
      $arr = explode(', ', $str);
      return $arr[1] . ' ' . $arr[0];
    } else {
      return $str;
    }
}


function title_case($str)
{
    $exceptions = array('a', 'an', 'but', 'by', 'for', 'it', 'of', 'the', 'to');

    $words = split(' ', $str);
    $newwords = array();
    foreach ($words as $word) {
        if (!in_array($word, $exceptions)) {
            $word = ucfirst($word);
        }
        array_push($newwords, $word);
    }

    return ucfirst(join(' ', $newwords));
}

function transform($xml, $xslFile)
{
    $style = new DOMDocument;
    $style->load($xslFile);
    $xsl = new XSLTProcessor();
    $xsl->importStyleSheet($style);
    $doc = new DOMDocument;
    if ($doc->loadXML($xml)) {
        return $xsl->transformToXML($xml);
    }
}

?>