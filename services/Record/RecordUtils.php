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

   function getLinkNums($marc) {
    // ISBN 
    $links = array();
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
    if ($isbnField = $marc->getField('020')) {
      if ($isbnField = $isbnField->getSubfield('a')) {
        $isbn = trim($isbnField->getData());
        if ($pos = strpos($isbn, ' ')) {
          $isbn = substr($isbn, 0, $pos);
        }
        $links[] = "ISBN:" . $isbn;
      }
    }
    return $links;
  }
  
  
  function getStatuses($result) {
    global $configArray;
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
  
    $holdingList = $this->catalog->getStatuses($ids);
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
  
}

?>
