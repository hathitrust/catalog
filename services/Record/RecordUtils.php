<?php


class RecordUtils 
{
  
  private $catalog;
  
  # Which 245 subfields go in the long title?
  private $longTitleSubfields = array(
    'a' => true,
    'b' => true,
    'd' => true,
    'e' => true,
    'f' => true,
    'g' => true,
    'k' => true,
    'n' => true,
    'p' => true
    );  
  
  private $ns_google_prefix = array(
    'chi' => 'CHI',
    'coo' => 'CORNELL',
    'hvd' => 'HARVARD',
    'ien' => 'NWU',
    'inu' => 'IND',
    'mdp' => 'UOM',
    'njp' => 'PRNC',
    'nnc1' => 'COLUMBIA',
    'nyp' => 'NYPL',
    'pst' => 'PSU',
    'pur1' => 'PURD',
    'uc1' => 'UCAL',
    'ucm' => 'UCM',
    'umn' => 'MINN',
    'uva' => 'UVA',
    'wu' => 'WISC'
  );

  function __construct() {
    global $configArray;
  }
  
   function getMarcRecord($record) 
  {
    $rawmarc = trim($record['fullrecord']);
    if (substr($rawmarc,0,1) == '<') {
      return $this->getMarcXMLRecord($rawmarc);
    } else {
      return $this->getMarcBinaryRecord($rawmarc);
    }
  }

   function getMarcXMLRecord($rawmarc) 
  {
    $marc = new File_MARCXML($rawmarc, File_MARC::SOURCE_STRING);
    if ($marcRecord = $marc->next()) {
      return $marcRecord;
    } else {
      echo "Oops. Couldn't get marc record\n\n";
      return false;
   }
  }


   function getMarcBinaryRecord($rawmarc) 
  {
    $rawmarc = preg_replace('/#31;/', "\x1F", $rawmarc);
    $rawmarc = preg_replace('/#30;/', "\x1E", $rawmarc);
    $marc = new File_MARC($rawmarc, File_MARC::SOURCE_STRING);
    if ($marcRecord = $marc->next()) {
      return $marcRecord;
    } else {
      echo "Oops. Couldn't get marc record\n\n";
      return false;
   }
  }


   function getURLs($marcRecord) {
    global $configArray;
    global $session; 
    global $user; 
    $inst = $session->get('inst');
    $proxy = $configArray['EZproxy']['host'];	// default
    if (isset($user) and isset($user->patron)
        and ($user->patron->campus == 'UMFL') or ($inst == 'flint')) 
      $proxy = $configArray['EZproxy']['flint'];
    $urls = array();
    foreach ($marcRecord->getfields('856') as $field) {
      $url = array("link" => '', "description" => '', "note" => '', "status" => 'Available Online');
      if ($field->getSubfield('u')) $url_link = $field->getSubfield('u')->getData(); 
      else continue; 
      // check for proxy in url
      if (preg_match('/proxy/', $url_link) == 0 ) $url_link = $proxy . "/login?url=" . $url_link;
      $url['link'] = $url_link;
      if ($field->getSubfield('3')) { 
        $url['description'] = $field->getSubfield('3')->getData(); 
      }
      if ($field->getSubfield('z')) {
        $url['note'] = $field->getSubfield('z')->getData(); 
      }
      $urls[] = $url;
    }
    return $urls;
  }

   function getFullTitle($marcRecord) {
    $titles = array();
    foreach ($marcRecord->getfields('245') as $field) {
      $title = '';
      foreach ($field->getSubfields() as $subcode => $subfield) {
        if ($subcode >= 'a' and $subcode <= 'z') {
          $title .= $subfield->getData() . ' ';
        }
      }
      if (strlen($title) > 1) {
        $titles[] = $title;        
      }
    }
    return $titles;
  }
  
  
  /**
    * Get the "long" title (subfields abdefgknp, as in edit_doc_777 in Aleph)
    *
  **/
  

  
  function getLongTitles($marcRecord) {
    $titles = array();
    foreach ($marcRecord->getfields('245') as $field) {
      $title = array();
      foreach ($field->getSubfields() as $subcode => $subfield) {
        if (isset($this->longTitleSubfields[$subcode])) {
          $title[] = $subfield->getData();
        }
      }
      $titlestring = implode(' ', $title);
      $titlestring = preg_replace('/\s+:/', ':', $titlestring);
      $titlestring = preg_replace('/[^\w\d\.\]\)\}]+$/', '', $titlestring);
      $titles[] = $titlestring;
    }
    return $titles;
  }
  
  
  function getFirstTitle($marcRecord) {
    $titles = $this->getFullTitle($marcRecord);
    return $titles[0];
  }

  function getGooglePrefix($ns, $id) {
    if (!isset( $this->ns_google_prefix[$ns])) return '';
    $google_prefix = $this->ns_google_prefix[$ns];
    if ($google_prefix == 'UCAL') return $this->getGooglePrefixUCAL($id);
    return $google_prefix;
  }
 
  function getGooglePrefixUCAL($id) {
    $id_len = strlen($id);
    if ($id_len == 11 and substr($id, 0, 1) == 'L') return 'UCLA';
    if ($id_len == 10) return 'UCB';
    if ($id_len == 14) {
      switch (substr($id, 1, 4)) {
        case '1822':
          return 'UCSD';
        case '1970':
          return 'UCI';
        case '1378':
          return 'UCSF';
        case '2106':
          return 'UCSC';
        case '1205':
          return 'UCSB';
        case '1175':
          return 'UCD';
        case '1158':
          return 'UCLA';
        case '1210':
          return 'UCR';
      }
    }
    return 'UCAL';
  }

   function getLinkNums($marc) {
    $links = array();
    // hathi id (974 subfield u)
    if ($f974List = $marc->getFields('974')) {
      foreach ($f974List as $field) {
        if ($subu = $field->getSubfield('u')->getData()) {
          list($ns, $id) = explode(".", $subu, 2);
          $id = strtoupper($id);
          if ($google_prefix = $this->getGooglePrefix($ns, $id)) {
            $links[] = implode(":", array($google_prefix, $id));
            break;
          }
        }
      }
    }
    // oclc number
    if ($f035List = $marc->getFields('035')) {
      foreach ($f035List as $field) {
        if ($suba = $field->getSubfield('a')) {
          $suba_data = $suba->getData();
          if (preg_match('/^(\(oclc\)|\(ocolc\)|ocm|ocn).*?(\d+)/i', $suba_data, $oclc_num)) {
            $links[] = 'OCLC:' . $oclc_num[2];
          }
        }
      }
    }
    // ISBN 
    if ($isbnField = $marc->getField('020')) {
      if ($isbnField = $isbnField->getSubfield('a')) {
        $isbn = trim($isbnField->getData());
        if ($pos = strpos($isbn, ' ')) {
          $isbn = substr($isbn, 0, $pos);
        }
        $links[] = "ISBN:" . $isbn;
      }
    }
#    echo "links: " . implode("<br>", $links);
    return $links;
  }
  
  
  function getStatuses($result) {
    global $configArray;
    
    $session = VFSession::singleton();
    
    $ids = array();
    $url_list = array();
    if (!isset($this->catalog)) {
      $this->catalog = new CatalogConnection($configArray['Catalog']['driver']);    
    }
    foreach ($result['record'] as $num => $record) {
      $ids[] = $record['id'];
      $marcRecord = $this->getMarcRecord($record);
      $url_list[$record['id']] = $this->getURLs($marcRecord);
    }  
  
    //$holdingList = $this->catalog->getStatuses($ids);
    $holdingList = $this->catalog->getStatuses($result);
    
    
    $usft  = array('pd', 'pdus' ,'world', 'umall', 'opb');
    $intlft = array('pd', 'world', 'umall', 'opb');
    
    if ($session->get('inUSA')) {
      $ft = $usft;
    } else {
      $ft = $intlft;
    }
    
    foreach ($holdingList as $id => $h) {
      if ( isset($holdingList[$id]["NA"])) return $holdingList;
      foreach ($h as $locindex => $location) {
        if ($location['sub_library'] == 'HATHI') {
          foreach ($location['item_info'] as $index => $volume) {          
            if (in_array($volume['rights'], $ft) || preg_match('/^cc/', $volume['rights'])) {
              $holdingList[$id][$locindex]['item_info'][$index]['isHTFT'] = true;
            } else {
              $holdingList[$id][$locindex]['item_info'][$index]['isHTSO'] = true;
              $holdingList[$id][$locindex]['item_info'][$index]['status'] = "Search only (no full text)";              
            }
          }
        }
      }
    }
    
    
    
    if (PEAR::isError($holdingList)) {
        PEAR::raiseError($holdingList);
    }
  
    foreach ($url_list as $id => $urls) {
      // If there are urls for this id, make sure we have an ELEC copy 
      // for it (the ELEC copy will be first if it exists).
      // Insert it as the first entry if it doesn't exist
      if (count($urls)) {
        if (!isset($holdingList[$id][0]) or $holdingList[$id][0]['sub_library'] != 'ELEC') {
          array_unshift($holdingList[$id], array('location' => 'Electronic Resources', 'sub_library' => 'ELEC'));
        }
        foreach ($urls as $url) {
          $holdingList[$id][0]['item_info'][] = $url;
        } 
        if (count($urls) == 1) {
          $holdingList[$id][0]['link'] = $urls[0]['link'];
          $holdingList[$id][0]['description'] = $urls[0]['description'];
          $holdingList[$id][0]['note'] = $urls[0]['note'];
          $holdingList[$id][0]['status'] = $urls[0]['status'];
        } else {
          $holdingList[$id][0]['status'] = 'See holdings';
        }
      }
    }
    return $holdingList;
  }

  function getOptouts($result) {
    global $configArray;
    if (!isset($this->catalog)) {
      $this->catalog = new CatalogConnection($configArray['Catalog']['driver']);    
    }
  
    $holdings = $this->catalog->getStatus($result);
    if (PEAR::isError($holdings)) {
        PEAR::raiseError($holdings);
    }
    
    $id = $result['record'][0]['id'];
    $optoutList = array();
    foreach ($holdings[$id] as $hold) { 
      if ($hold['sub_library'] == 'HATHI') continue;
      if (!isset($hold['item_info'])) continue;
      foreach ($hold['item_info'] as $item) {
        //echo "barcode: " . print_r( $item );
        if ($item['opt_out']) {
          $opt_item['barcode'] = $item['barcode'];
          $opt_item['description'] = $item['description'];
          $optoutList[] = $opt_item;
        }
      }
    }
    return $optoutList;
  }
  
}

?>
