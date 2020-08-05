<?php

require_once 'sys/VFSession.php';

class RecordUtils {
  # Which RDA fields map to with other fields?

  private $rda = array(
      '260' => array('tag' => '264', 'ind1' => false, 'ind2' => '1')
  );

  private $memoize = array();


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

static function sortstringFromEnumcron($str) {
  $rv = '';
  preg_match('/\d+/', $str, $match);
  if (isset($match[0])) {
    if (!is_array($match[0])) {
      $match[0]  = array($match[0]);
    }
    foreach ($match[0] as $digits) {
      $rv .= strlen($digits) . $digits . ' ';
    }
  }
  return $rv;
}


function enumsort($a, $b) {
  $sa = $this->sortstringFromEnumcron($a['enumcron']);
  $sb = $this->sortstringFromEnumcron($b['enumcron']);
  if ($sa == $sb) {
      return 0;
  }
  return ($sa < $sb) ? -1 : 1;
}

# Sort items from the json and return them

function items_from_json($record) {
  return $this->items_from_raw_json($record['ht_json']);
}

function items_from_mergeditemset($m) {
  return $this->items_from_many_raw_jsons($m->raw_ht_jsons);
}

function items_from_many_raw_jsons($jsons) {
   $merged_items = array();
   foreach ($jsons as $j) {
     $merged_items = array_merge($merged_items, $this->items_from_raw_json($j));
   }
  usort($merged_items, array($this, 'enumsort'));
  return $merged_items;
}

function items_from_raw_json($json_string) {
  $items = json_decode($json_string, true);
  usort($items, array($this, 'enumsort'));
  return $items;
}
  

  // Return an array of the form:
  // {
  //   rights_code => "whatever",
  //   enumchron => "whatever",
  //   fullview => true|false
  //   original_from => "whatever",
  // }


  function ht_link_data_from_json($e) {
    global $HT_COLLECTIONS;
    global $htstatus;
    $rv = array();

    $rc = $e['rights'];

    $rv['rights_code'] = $rc;
    $rv['handle'] = $e['htid'];
    $collection = $e['collection_code'];
    $rv['original_from'] = $HT_COLLECTIONS[$collection]['original_from'];
    $rv['enumchron'] = $e['enumcron'];
    $rv['is_fullview'] = $this->is_fullview($rv['rights_code']);
    $rv['is_tombstone'] = $rv['rights_code'] == 'nobody';

    $heldby = $e['heldby'];
    $rv['is_emergency_access'] = $htstatus->emergency_access && (!$rv['is_fullview'] && $this->is_held_by_user_institution($heldby));
    $rv['is_NFB'] = $htstatus->is_NFB;
    return $rv;
  }

  function is_held_by_user_institution($print_holdings) {
    global $htstatus;

    return in_array($htstatus->institution_code, $print_holdings)  ||
           in_array($htstatus->mapped_institution_code, $print_holdings) ;
  }


  function record_is_tombstone($rec) {
    $htjson = json_decode($rec['ht_json'], true);
    foreach ($htjson as $item) {
      if ($item['rights'] != 'nobody') {
        return false;
      }
    }
    return true;
  }


  // Take a rightscode (and, soon, other data) and return viewability

  function is_fullview($rcode, $inUSA = null) {

    global $configArray;
    if (!isset($inUSA)) {
      $session = VFSession::instance();
      $inUSA = $session->get('inUSA');
    }

    // Assume false
    $fv = false;

    // Newly into copyright? Return true if after the right date
    // The munged facet is in Solr.php -- don't forget
    // to deal with that, too.

    $todays_date = intval(date("YmdH"));
    $copyright_active_date = intval($configArray['IntoCopyright']['date']);
    if (is_array($rcode) &&
        array_search("newly_open", $rcode) &&
	$todays_date >= $copyright_active_date
	) {
      return true;
      
    } else if (is_array($rcode)) { // ditch the newly_open marker
      $index = array_search("newly_open", $rcode);
      if ($index) {
        unset($rcode[$index]);
      }
      $rcode = $rcode[0];
    }
        

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
    return $links;
  }

}

?>
