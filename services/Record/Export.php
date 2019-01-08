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

class Export extends Record
{
    // public $itemtype = array(
    //      'Conference' => 'CONF',
    //      'Journal' => 'BOOK',
    //      'Book' => "BOOK",
    //      'Computer File' => 'ELEC',
    //      'Manuscript' => 'BOOK',
    //      'Visual Material' => 'ART',
    //      'Statistics' => 'DATA',
    //      'Video (DVD)' => 'VIDEO',
    //      'Video (VHS)' => 'VIDEO',
    //      'Maps-Atlas' => 'BOOK',
    //      'Map' => 'MAP',
    //      'Music' => 'SOUND',
    //      'Directories' => 'CTLG',
    //      'Audio' => 'SOUND',
    //      'Motion Picture' => 'MPCT',
    //      'Dictionaries' => 'BOOK',
    //      'Musical Score' => 'MUSIC',
    //      'Audio (music)' => 'SOUND',
    //      'Audio CD' => 'SOUND',
    //      'Audio (spoken word)' => 'SOUND',
    //      'Audio LP' => 'SOUND',
    //      'Newspaper' => 'NEWS',
    // );
    function launch()
    {
        global $configArray;
        global $interface;

        switch ($_GET['style']) {
            case 'endnote':
                header('Content-type: application/x-inst-for-Scientific-info');
                $this->endnoteRIS();
                break;
            case 'refworks':
                $url = 'http://www.refworks.com/express/expressimport.asp';
                $client = new HTTP_Request2($url, array('allowRedirects' => 'true'));
                $client->setMethod('GET');
                //$client->addQueryString('vendor', $configArray['Site']['title']);
                $client->addQueryString('vendor', 'VuFind');
                $client->addQueryString('filter', 'MARC Format');
                $client->addQueryString('encoding', '65001');
                $client->addQueryString('url', $configArray['Site']['url'] . '/Record/' . $this->id . '/Export%3Fstyle=MARC');

                $result = $client->send();
                echo $client->getResponseBody();
                break;
            case 'MARC':
                $this->displayMARC();
                break;
            case 'REF':
                $this->refworksTaggedMarc();
                break;
            case 'RDF':
            default:
                $this->displayXSL('record-rdf.xsl');
                break;
        }
    }
    
    function displayMARC()
    {
        echo $this->record['fullrecord'];
        exit();
    }
    
    function refworksTaggedMarc() {
      $m = $this->marcRecord;
      header('Content-type: text/plain; charset=UTF-8');

      echo 'LEADER ', $m->getLeader(), "\n";
      
      foreach ($m->getFields() as $tag => $val) {
        echo $tag;
        if ($val instanceof File_MARC_Control_FIELD) {
          echo '    ', $val->getData(), "\n";
        } else {
          echo ' ', $val->getIndicator(1),  $val->getIndicator(2), ' ';
          $subs = array();
          foreach ($val->getSubFields() as $code=>$subdata) {
            $line = '';
            if ($code != 'a') {
              $line = '|' . $code;
            }
            $subs[] = $line . $subdata->getData();
          }
          echo implode(' ', $subs), "\n";
        }        
      }
    }
    
    
    function endnoteRIS() {
        global $configArray;
        $m = $this->marcRecord;
        $lines = array();
        $specs = Horde_Yaml::load(file_get_contents('conf/risexport.yaml'));
        foreach ($specs as $ristag => $tagspec) {
            foreach ($tagspec as $tuple) {
                $line = $ristag . '  - ';
                
                // Take care of special cases
                $tag = $tuple[0];
                
                if  (preg_match('/^\d+$/', $tag)) {
                    $tag = sprintf('%03d', $tag);
                }
                
                if ($tag == 'ID') {
                    array_push($lines, $line . $this->id);
                    continue;
                }
                if ($tag == 'RECORDURL') {
                    array_push($lines, $line . $configArray['Site']['url'] . 'Record/' . $this->id);
                    continue;
                }
                if ($tag == 'TYPE') {
                    foreach ($m->getFields('970') as $dfield) {
                        foreach ($dfield->getSubfields() as $sub) {
                            // if (isset($this->itemtype[$sub->getData()])) {
                            //     array_push($lines, $line . $this->itemtype[$sub->getData()]);
                            // }
                            array_push($lines, $line . $sub->getData());
                        }
                    }
                    continue;
                }
                
                # if it's a control tag...
                
                if ($tag < 10 && preg_match('/^\d+$/', $tag)) {
                    foreach ($m->getFields($tag, true) as $cfield) {
                        $data = $cfield->getData();
                        if (isset($tuple[1])) {
                            $start = $tuple[1] - 1;
                            $length = $tuple[2];
                            $data = substr($data, $start, $length);
                        }
                        array_push($lines, $line .  $data);
                    }
                    continue;
                }
                
                # Otherwise, data
                $join = isset($tuple[2])? $tuple[2] : '';
                $match = $tuple[1];
                $alwaysmatch = isset($match)? false : true;
                
                $realtag = substr($tag, 0, 3);
                
                $ind1 = substr($tag, 3, 1);
                $ind2 = substr($tag, 4, 1);
                                
                foreach ($m->getFields($realtag, true) as $dfield) {
                    if (! (preg_match("/$ind1/", $dfield->getIndicator(1)) &&
                           preg_match("/$ind2/", $dfield->getIndicator(2)))) {
                               continue;
                    }
                    $str = array();
                    foreach ($dfield->getSubfields() as $sub) {
                        if ($alwaysmatch || strspn($sub->getCode(), $match)) {
                           array_push($str, $sub->getData());
                        }
                    }
                    if (count($str)) {
                        array_push($lines, $line . implode($join, $str));
                    }
                }
            }
        }
        array_push($lines, 'ER');
        
        echo implode("\n", $lines), "\n";
    }
    
        
    function displayXSL($file)
    {
        header('Content-type: text/xml');
        
        $style = new DOMDocument;
        $style->load('services/Record/xsl/' . $file);

        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet($style);

        $xml = new DOMDocument;
        $xml->loadXML(trim($this->marcRecord->toXML()));
        echo $xsl->transformToXML($xml);
        exit();
    }
}
?>