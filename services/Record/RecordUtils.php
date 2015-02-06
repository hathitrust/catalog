<?php

require_once 'sys/VFSession.php';

class RecordUtils {
  # Which RDA fields map to with other fields?

  private $rda = array(
      '260' => array('tag' => '264', 'ind1' => false, 'ind2' => '1')
  );


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

  // Get all 974s
  function ht_fields($marc) {
    return $marc->getFields('974');
  }

  // Find fields that we're allowed to display
  function displayable_ht_fields($marc) {
    $ditems = array();
    foreach ($marc->getFields('974') as $f) {
      if ($f->getSubfield('r') && $f->getSubfield('r')->getData() != 'nobody') {
        $ditems[] = $f;
      }
    }
    return $ditems;
  }

  // Return an array of the form:
  // {
  //   rights_code => "whatever",
  //   enumchron => "whatever",
  //   fullview => true|false
  //   original_from => "whatever",
  // }

  function ht_link_data($field) {
    global $HT_COLLECTIONS;
    $rv = array();
    $rc = $field->getSubfield('r')->getData();
    $rv['rights_code'] = $rc;

    $handle = $field->getSubfield('u')->getData();
    $rv['handle'] = $handle;
    
    $collection = $field->getSubfield('c')->getData();
    $collection = strtolower($collection);
    $rv['original_from'] = $HT_COLLECTIONS[$collection]['original_from'];

    $rv['enumchron'] = $field->getSubfield('z') ? $field->getSubfield('z')->getData() : '';
    $rv['is_fullview'] = $this->is_fullview($rv['rights_code']);
    return $rv;
  }

  // Take a rightscode (and, soon, other data) and return viewability
  function is_fullview($rcode, $inUSA = null) {
    if (!isset($inUSA)) {
      $session = VFSession::singleton();
      $inUSA = $session->get('inUSA');
    }    

    // Assume false
    $fv = false;

    // Public domain? return true
    if (preg_match('/^(cc|pd)/', $rcode) || preg_match('/world/', $rcode)) {
      $fv = true;
    }

    //...unless it's US only and we're outside the US
    if ($rcode == 'pdus' && $inUSA == false) {
      $fv = false;
    }

    // ...unless UNLESS it's pd-private or pd-pvt
    if (preg_match('/pd-p/', $rcode)) {
      $fv = false;
    }
    
    // ...or it's ICUS and we're *outside* the USA
    
    if ($rcode == 'icus' && $inUSA  == false) {
      $fv = true;
    }

    // other stuff about logins and such goes here
    return $fv;
  }

  function __construct() {
    global $configArray;
  }

  function getMarcRecord($record) {
    $rawmarc = trim($record['fullrecord']);
    if (substr($rawmarc, 0, 1) == '<') {
      return $this->getMarcXMLRecord($rawmarc);
    } else {
      return $this->getMarcBinaryRecord($rawmarc);
    }
  }

  function getMarcXMLRecord($rawmarc) {
    $marc = new File_MARCXML($rawmarc, File_MARC::SOURCE_STRING);
    if ($marcRecord = $marc->next()) {
      return $marcRecord;
    } else {
      echo "Oops. Couldn't get marc record\n\n";
      return false;
    }
  }

  function getMarcBinaryRecord($rawmarc) {
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

  # Get the given field plus any corresponding RDA field
  # from a marc record given the spec at the top of this file.
  # First, we'll have a little utility

  function indmatch($i1, $i2) {
    return ($i1 === false) || ($i2 === false) || ($i1 == $i2);
  }

  function getRDAFields($marc, $tag, $pcre = false) {
    $base = $marc->getFields($tag, $pcre);

    if (!isset($this->rda[$tag])) {
      return $base;
    }

    # Otherwise...
    $spec = $this->rda[$tag];
    $rdafields = $marc->getFields($spec['tag']);

    if (!$rdafields) {
      return $base;
    }

    if (($spec['ind1'] == false) && ($spec['ind2'] == false)) {
      $rv = $base + $rdafields; # simple union
      if (count($rv) > 0) {
        return $rv;
      } else {
        return false;
      }
    }

    # OK. We got this far. We have rdafields, and we need to deal
    # with the indicators

    foreach ($rdafields as $rf) {
      if (($this->indmatch($spec['ind1'], $rf->getIndicator(1))) &&
              ($this->indmatch($spec['ind2'], $rf->getIndicator(2)))) {
        array_push($base, $rf);
      }
    }

    if (count($base) > 0) {
      return $base;
    } else {
      return false;
    }
  }

  # Just get the first one

  function getRDAField($marc, $tag, $pcre = false) {
    $fields = $this->getRDAFields($marc, $tag, $pcre);
    if ($fields) {
      return $fields[0];
    } else {
      return false;
    }
  }

  function getURLs($marcRecord) {
    global $configArray;
    global $session;
    global $user;
    $inst = $session->get('inst');
    $proxy = $configArray['EZproxy']['host']; // default
    if (isset($user) and isset($user->patron)
            and ($user->patron->campus == 'UMFL') or ($inst == 'flint'))
      $proxy = $configArray['EZproxy']['flint'];
    $urls = array();
    foreach ($marcRecord->getfields('856') as $field) {
      $url = array("link" => '', "description" => '', "note" => '', "status" => 'Available Online');
      if ($field->getSubfield('u'))
        $url_link = $field->getSubfield('u')->getData();
      else
        continue;
      // check for proxy in url
      if (preg_match('/proxy/', $url_link) == 0)
        $url_link = $proxy . "/login?url=" . $url_link;
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
   * */
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
    if (!isset($this->ns_google_prefix[$ns]))
      return '';
    $google_prefix = $this->ns_google_prefix[$ns];
    if ($google_prefix == 'UCAL')
      return $this->getGooglePrefixUCAL($id);
    return $google_prefix;
  }

  function getGooglePrefixUCAL($id) {
    $id_len = strlen($id);
    if ($id_len == 11 and substr($id, 0, 1) == 'L')
      return 'UCLA';
    if ($id_len == 10)
      return 'UCB';
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

}

?>
