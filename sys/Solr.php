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
 **/

// require_once 'XML/Unserializer.php';
// require_once 'XML/Serializer.php';
require_once 'HTTP/Request2.php';
require_once 'services/Search/SearchStructure.php';
require_once 'services/Record/FilterFormat.php';
require_once 'lib/LCCallNumberNormalizer.php';
require_once 'sys/ActivityLog.php';

/**
 * Solr HTTP Interface
 *
 * @version     $Revision: 1.13 $
 * @author      Andrew S. Nagy <andrew.nagy@villanova.edu>
 * @access      public
 */
class Solr
{
  /**
   * A boolean value detemrining whether to print debug information
   * @var bool
   */
  public $debug = false;

  /**
   * Whether to Serialize to a PHP Array or not.
   * @var bool
   */
  public $raw = false;

  /**
   * The HTTP_Request2 object used for REST transactions
   * @var object HTTP_Request2
   */
  public $solr_connection;

  /**
   * The host to connect to
   * @var string
   */
  public $host;

  public $fields = '*,score';


  /**
   * A place to store log data
   **/

  public $alogdata = array();


  /**
   * Constructor
   *
   * Sets up the Solr Client
   *
   * @param string $host The URL for the local Solr Server
   * @param string $index The solr index directory (default 'catalog')
   * @access  public
   */
  function __construct($host, $index = 'catalog') {
    global $configArray;
    $this->host = $host . '/' . $index;
    $this->solr_connection = new SolrConnection();

    if (isset($configArray['HathiTrust']) &&
      isset($configArray['HathiTrust']['onlyHathi']) &&
      $configArray['HathiTrust']['onlyHathi']) {
      $this->hathiOnly = true;
    }
    else {
      $this->hathiOnly = false;
    }

  }

  /**
   * Execute a simple search
   *
   * @param SearchStructure $ss The search structure
   * @param int $start The first record to get
   * @param int $limit The number of records to get
   * @param bool $raw = false    return the raw solr query (without _process-ing)?
   * @return array record structure
   */

  function simplesearch($ss, $start = 0, $limit = null, $raw = false) {

    global $configArray;
    if (!isset($limit)) {
      $limit = isset($_REQUEST['pagesize']) ? $_REQUEST['pagesize'] : $configArray['Site']['itemsPerPage'];
    }


    // The initial query
    if ($ss->use_dismax) {
      $ss->action = 'edismax';
      $args = $this->dismaxSearchArguments($ss);
    }
    else {
      $args = $this->searchArguments($ss);
    }

    $action = $ss->action;

    // Set up logging for later if it's a simple (dismax) search
    if ($ss->use_dismax && isset($ss->search[0])) {
      if (isset($ss->originalHash, $ss->originalHash['origin'])) {
        $this->alogdata['logaction'] = 'externalsearch';
        $this->alogdata['data3'] = $ss->originalHash['origin'];
        $this->alogdata['logit'] = true;
      }
      else {
        $this->alogdata['logaction'] = 'simplesearch';
        $this->alogdata['data3'] = '';
      }
      $this->alogdata['data1'] = $ss->search[0][0]; // the index
      $this->alogdata['data2'] = $ss->search[0][1]; // the terms searched
    }


    // Add the pagination

    $args[] = array('start', $start);
    $args[] = array('rows', $limit);


    // Spell checking? Also used as a way to determine we came from simple search for logging
    if ($ss->checkSpelling) {
      $args = array_merge($args, $this->spellcheckComponents($ss));
    }

    if (isset($ss->originalHash['checkspelling']) && count($ss->activeInbandFilters()) == 0) {
      $this->alogdata['logit'] = true;
    }


    if ($raw) {
      return $this->rawSolrSearch($args, $action);
    }

    // Otherwise...
    $rv = $this->solrSearch($args, $action);
    return $rv;
  }


  /**
   * Do a simplesearch, but return a RecordSet
   *
   *
   * @param SearchStructure $ss The search structure
   * @param int $start The first record to get
   * @param int $limit The number of records to get
   * @return RecordSet
   **/

  function getRecordSet($ss, $start = 0, $limit = null) {
    $rs = new RecordSet;

    $rs->add_records($this->simplesearch($ss, $start, $limit));
    return $rs;
  }


  /**
   * Get a list of facet values suitable for browsing
   *
   * @param SearchStructure|nil $ss The search structure to use when filtering the facets. Leave as nil to ignore
   * @param string $fields A field to get values for
   * @param integer $skip How many values to skip, for pagination purposes
   * @param integer $limit How many values to return
   * @return array An array of the form:
   *       (total => numFound, values=>(index1 => ((value1, count1), (value2, count2)), ...)),
   **/


  function facetlist($ss, $fields, $sort = 'index', $skip = 0, $limit = 20) {
    // Start by getting the ones to filter out,by regexp
    global $configArray;
    if (isset($configArray['hideFacetValues'])) {
      $hide = array();
      foreach ($configArray['hideFacetValues'] as $kvstring) {
        $kv = explode(':', $kvstring);
        if (!isset($hide[$kv[0]])) {
          $hide[$kv[0]] = array();
        }
        $hide[$kv[0]][] = $kv[1];
      }
    }


    $args = array();
    $searchcomps = $ss->use_dismax ? $this->dismaxSearchArguments($ss) : $this->standardSearchComponents($ss);
    if (isset($ss) and $ss) {
      $args = array_merge($args,
        $searchcomps,
        $this->filterComponents($ss));
    }
    else {
      $args[] = array('q', '*:*');
    }

    if (!is_array($fields)) {
      $fields = array($fields);
    }


    $args[] = array('rows', 0);
    $args[] = array('facet', 'true');
    $option['facet.enum.cache.minDf'] = 50;
    $args[] = array('facet.mincount', 1);
    $args[] = array('facet.offset', $skip);
    $args[] = array('facet.limit', $limit);
    // Add the fields
    $args[] = array('facet.field', $fields);

    $args[] = array('facet.sort', $sort);

    // Get the results back as json
    $args[] = array('wt', 'json');
    // ... in a sane format
    $args[] = array('json.nl', 'arrarr');

    // Get the data.
    $body = $this->rawSolrSearch($args, $ss->action);

    $rv = array();
    $rv['total'] = $body['response']['numFound'];
    $rv['values'] = array();

    // Turn it into the exposed return value

    foreach ($fields as $field) {
      $values = $body['facet_counts']['facet_fields'][$field];
      $rv['values'][$field] = array();

      // skip the hidden ones
      foreach ($values as $valcnt) {
        if (isset($hide, $hide[$field])) {
          foreach ($hide[$field] as $regexp) {
            if (preg_match('/' . $regexp . '/', $valcnt[0])) {
              continue(2);
            }
          }
        }
        // ..otherwise, if it's not hidden...
        $rv['values'][$field][] = $valcnt;
      }
    }
    return $rv;
  }


  /**
   * Build up a set of key=value arguments for sending to Solr based on the passed SearchStructure
   *
   * @param SearchStructure $ss A filled-in search structure
   * @return array An array of (key,value) duples to send to Solr
   */

  function searchArguments($ss) {
    $ss->action = 'standard';
    // print_r($this->standardSearchComponents($ss));
    return array_merge($this->standardSearchComponents($ss),
      $this->filterComponents($ss),
      $this->sortComponents($ss));
  }


  /**
   * Build up a set of search arguments for a dismax request
   *
   * @param SearchStructure $ss A fille-in search structure
   * @return array An array of (key,value) duples for sending to Solr
   **/

  function dismaxSearchArguments($ss) {
    $rv = array();
    // Should just be on "lookfor" and "type"
    $tvb = isset($ss->search[0]) ? $ss->search[0] : array('all', '*:*');
    $type = $tvb[0];
    $value = $tvb[1];

    if (!preg_match('/\S/', $value)) {
      $value = '*:*';
    }
    // Get the yaml file
    $allspecs = yaml_parse_file('conf/dismaxsearchspecs.yaml');

    // If the type isn't set, back up to normal arguments

    if (!isset($allspecs[$type])) {
      $args =  $this->searchArguments($ss);
      // print_r($args);
      return $args;
    }


    $spec = $allspecs[$type];
    $parsed = array();

    foreach (array('pf', 'qf', 'pf1') as $param) {
      $parsed[$param] = array();
      if (!isset($spec[$param])) {
        continue;
      }
      foreach ($spec[$param] as $fieldboost) {
        $parsed[$param][] = implode("^", $fieldboost);
      }
      $rv[] = array($param, implode(" ", $parsed[$param]));
    }
    $rv[] = array('q', $value);
    $rv[] = array('qt', 'edismax');
    $rv[] = array('mm', $this->mm($spec, $ss));

    return array_merge($rv, $this->filterComponents($ss), $this->sortComponents($ss));
  }


  /**
   * Get the minmatch (mm) param for solr
   **/

  function mm($spec, $ss) {
    return $spec['mm'];
  }

  /**
   * Create key-value argument(s) to specify the search as a Solr Standard Search syntax search
   *
   * @param SearchStructure $ss The Search Structure
   * @return array And array of duples of the form key=value (e.g, q=<text of the query>)
   */

  function standardSearchComponents($ss) {
    // Get a SearchSpecs singleton; we define searchable fields as
    // those listed in the searchspecs file

    $searchComponents = array();

    $specs = yaml_parse_file('conf/searchspecs.yaml');
    $query = '';

    foreach ($ss->search as $tvb) { // Type, Value (keywords), Boolen AND or OR
      $type = $tvb[0];
      $values = $this->build_and_or_onephrase($tvb[1]);
      $bool = isset($tvb[2]) ? $tvb[2] : false;
      if (isset($specs[$type]) && $values) {
        $comp = '(' . $this->__buildQueryString($specs[$type], $values) . ')';
        if ($bool) {
          $comp .= ' ' . $bool . ' ';
        }
        $query .= $comp;
      }
    }

    if (!$ss->noExtraIDs()) {
      if (strlen($query) > 0) {
        $query = '(' . $query . ') OR ';
      }
      $query .= "id:(" . implode(' OR ', $ss->extraIDs()) . ')';
    }

    $ids = $this->tagIDs($ss);
    if (preg_match('/\S/', $query)) {
      $searchComponents[] = array('q', $query);
    }
    else {
      $searchComponents[] = array('q', '*:*');
    }

    return $searchComponents;
  }


  /** Quote a filter value, skipping it if it starts with a '[' (and hence is assumed
   * to be a range)
   **/

  function quoteFilterValue($v) {
    if (preg_match('/^\[/', $v)) {
      return $v;
    }
    else {
      return '"' . $v . '"';
    }
  }

  /**
   * Given a list of filters, replace 'ht_availability' => 'Full text'
   *
   */

  /**
   * Create key-value argument(s) to correctly apply the filters in $ss
   *
   * @param SearchStructure $ss
   * @return array An array of (key,value) duples
   */

  function filterComponents($ss) {
    global $configArray;
    global $htstatus;

    $rv = array();
    $filters = $ss->allActiveFilters();

    $tagfilter = $this->tagFilter($ss);
    if (isset($tagfilter)) {
      $rv[] = $tagfilter;
    }


    foreach ($filters as $indexValue) {
      $index = $indexValue[0];
      $val = $indexValue[1];
      $oval = $val;
      if (is_array($val)) {
        $quoted = array();
        foreach ($val as $v) {
          $quoted[] = $this->quoteFilterValue($v);
        }

        $val = '(' . implode(' OR ', $quoted) . ')';
      }
      else {
        $val = $this->quoteFilterValue($val);
      }

      # Add fultext filter if
      #  * ht_availability is set to "Full text"
      #  * the user is not NFB
      
      if ($index == "ht_availability" and $oval == 'Full text') {
        if (!$htstatus->is_NFB) {
          $ft = $this->fulltext_filter_base();
          $ft = $this->fulltext_filter_add_jan1_rollover($ft);
          $ft = $this->fulltext_filter_add_etas($ft);
          $rv[] = $ft;
        }
      }
      else { // otherwise, just do it like normal
        $rv[] = implode(':', array($index, $val));
      }
    }
    if (count($rv) == 0) {
      return array();
    }
    else {
      return array(array('fq', $rv));
    }
  }

  function fulltext_filter_base() {
    $ft = $this->quoteFilterValue('Full text');
    return "ht_availability:$ft";
  }

  function fulltext_filter_add_etas($current_ft_filter) {
    global $htstatus;
    if ($htstatus->emergency_access) {
      $inst_code = $this->quoteFilterValue($htstatus->institution_code);
      if (isset($htstatus->mapped_institution_code)) {
        $mic = $this->quoteFilterValue($htstatus->mapped_institution_code);
        $inst_code = "($inst_code OR $mic)";
      }
      return "($current_ft_filter OR print_holdings:$inst_code)";
    }
    else {
      return $current_ft_filter;
    }
  }

  # NFB users have no restrictions, so don't restrict it
  function fulltext_filter_add_nfb($ft) {

  }

  function fulltext_filter_add_jan1_rollover($current_ft_filter) {
    // Hack into place a change of the full-text only facet
    // for the temporary newly_open rightscode value
    // but only on or after the date from config.ini

    global $configArray;

    $todays_date = intval(date("YmdH"));
    $copyright_active_date = intval($configArray['IntoCopyright']['date']);

    if ($todays_date >= $copyright_active_date) {
      $newly_open = $this->quoteFilterValue('newly_open');
      return "($current_ft_filter OR ht_rightscode:$newly_open)";
    }
    else {
      return $current_ft_filter;
    }
  }


  /**
   * tagIDs($ss)
   *
   * Get a list of the IDs associated with all the tags
   *
   **/

  function tagIDs($ss) {
    $ids = array();

    $tags = $ss->tags();
    if (count($tags) == 0) {
      return $ids;
    }
    $tagobj = Tags::singleton();
    foreach ($ss->tags() as $tag) {
      $ids = array_merge($ids, $tagobj->idsWithTag($tag));
    }
    return $ids;
  }

  /**
   * Create a simple ID filter based on tags
   * For now, we're kinda assuming only one tag
   **/
  function tagFilter($ss) {
    $ids = $this->tagIDs($ss);
    if (count($ids) == 0) {
      return null;
    }
    return 'id:(' . implode(' OR ', $ids) . ')';
  }

  /**
   * Create key-value args to apply any selected sort option(s)
   *
   * @param SearchStructure $ss
   * @return array An array of (key,value) duples
   */

  function sortComponents($ss) {
    global $configArray;

    $sort = array();
    if (isset($ss->sort, $configArray['SortMapping'], $configArray['SortMapping'][$ss->sort])) {
      $sort[] = array('sort', $configArray['SortMapping'][$ss->sort]);
    }
    return $sort;
  }

  /**
   * Create key=value args for facets
   *
   * @param SearchStructure $ss
   * @return array An array of (key,value) duples
   */

  function facetComponents($ss, $fields, $limit, $sort = 'count') {

    $args = array();
    // Basic facet stuff
    $args[] = array('facet', 'true');
    $option['facet.enum.cache.minDf'] = 50;
    $args[] = array('facet.mincount', 1);
    $args[] = array('facet.limit', $limit);
    // Add the fields
    $args[] = array('facet.field', $fields);
    // Sort by count, desc
    $args[] = array('facet.sort', 'count');
    return $args;
  }

  function spellcheckComponents($ss) {

    # Disable someday

    return array();

    $args = array();
    if (!isset($ss->search[0], $ss->search[0][1])) {
      return $args;
    }
    $args[] = array('spellcheck.onlyMorePopular', 'true');
    $args[] = array('spellcheck.extendedResults', 'false');
    $args[] = array('spellcheck.count', '1');
    $args[] = array('spellcheck.collate', 'true');

    $args[] = array('spellcheck', 'true');
    $args[] = array('spellcheck.q', $ss->search[0][1]);
    $args[] = array('spellcheck.count', 1);
    $args[] = array('spellcheck.collate', 'true');
    return $args;
  }

//========= BEGIN LOWER-LEVEL STUFF ===================//


  function asGetIDsURL($args) {
    $params = array();
    $newargs = array();
    foreach ($args as $kv) {
      $key = $kv[0];
      $val = $kv[1];
      if ($key == 'fl') {
        $val = 'ht_id';
      }
      if ($key == 'rows') {
        $val = '200000';
      }
      if (preg_match('/spell/', $key)) {
        continue;
      }
      $newargs[] = array($key, $val);
    }


    foreach ($newargs as $kv) {
      $k = $kv[0];
      $v = $kv[1];
      if (is_array($v)) {
        foreach ($v as $multival) {
          $params[] = implode('=', array($k, rawurlencode($multival)));
        }
      }
      else {
        $params[] = implode('=', array($k, rawurlencode($v)));
      }
    }

    return $url_base . '?' . implode('&', $params);
  }


  /**
   * __buildQueryString -- internal method to build query string from search parameters
   *
   * @access  private
   * @param array $structure the SearchSpecs-derived structure or substructure defining the search, derived from the yaml file
   * @param array $values the various values in an array with keys 'onephrase', 'and', 'or' (and perhaps others)
   * @param string $joiner the value used to compose substructures (AND or OR)
   * @return  string              A search string suitable for adding to a query URL
   * @throws  object              PEAR Error
   */


  private function __buildQueryString($structure, $values, $joiner = "OR") {
    $clauses = array();
    foreach ($structure as $field => $clausearray) {
      // is_numeric($field) is true iff we've got an un-hashed array, used for grouping
      if (is_numeric($field)) {
        // get the op (AND or OR) and weight from the first item
        $opweight = array_shift($clausearray);
        $op = $opweight[0];
        $weight = $opweight[1];
        $sstring = implode('', array('(', Solr::__buildQueryString($clausearray, $values, $op), ')'));
        if (isset($weight) && $weight > 0) {
          $sstring .= '^' . $weight;
        }
        $clauses[] = $sstring;
        continue;
      }

      // Do we have just a title: [stringtype, weight] set?

      foreach ($clausearray as $valweight) {

        $val = $valweight[0];
        $weight = $valweight[1];
        if (!isset($values[$val])) {
          if ($val == 'lcnormalized') {
            $LC = LCCallNumberNormalizer::singleton();
            $normalized = $LC->normalize($values['asis'], false);
            if ($normalized) {
              $values[$val] = $normalized;
            }
            else {
              continue;
            }
          }

          if ($val == 'stdnum') {
            if (preg_match('/^\s*0*([\d\-\.]+[xX]?).*$/', $values['asis'], $match)) {
              $stdnum = $match[1];
              $stdnum = preg_replace('/[\.\-]/', '', $stdnum);
              $values[$val] = $stdnum;
            }
          }
        }
        if (!isset($values[$val])) {
          continue;
        }
        $sstring = $field . ':(' . $values[$val] . ')';
        if (isset($weight) && $weight > 0) {
          $sstring .= '^' . $weight;
        }
        $clauses[] = $sstring;
      }
    }
    $newq = implode(' ' . $joiner . ' ', $clauses);
    return $newq;
  }


  /**
   * Turn solr output into a record structure (which shouuld probably be its own class...)
   *
   * @param string $result The XML returned by solr
   * @param string $xslfile The path of the XSL file to use to convert the data
   * @return array A structure representing the returned data after transformation via $xslfile
   */


  function _process($result, $xslfile = 'xsl/solr-convert.xsl') {
    global $configArray;

    if (is_string($result) && preg_match('/^<html/', $result)) {
      if (preg_match('/ParseException/', $result)) {
        $errorMsg = "Error+in+search+syntax";
      }
      else {
        $errorMsg = "Unknown+error";
      }
      // header("Location: /Search/Error?error=$errorMsg");
      return;
    }

    $resp = $result['response'];
    $res = array();
    $res['RecordCount'] = $resp['numFound'];
    // $res['record'] = $resp['numFound'] == 1? array($resp['docs']) : $resp['docs'];
    $res['record'] = $resp['docs'];
    if (isset($result['spellcheck'],
      $result['spellcheck']['suggestions'],
      $result['spellcheck']['suggestions'][1],
      $result['spellcheck']['suggestions'][1][1])) {
      // $res['SpellcheckSuggestion'] = $resraw['spellcheck']['suggestions'][1][1];
      $spellcheck = array_pop($result['spellcheck']['suggestions']);
      $res['SpellcheckSuggestion'] = $spellcheck[1];
    }

    $ff = new FilterFormat();
    if ($res['RecordCount'] == 0) {
      return $res;
    }
    foreach ($res['record'] as &$record) {
      $record['baseFormat'] = $record['format'];
      if (!isset($record['availability'])) {
        $record['availability'] = "";
      }
      if (isset($record['format'])) {
        $record['format'] = $ff->filter(array_merge((array)$record['format'], (array)$record['availability']));
      }
      else {
        $record['format'] = $ff->filter((array)$record['availability']);
      }
    }

    // print_r($res);

    return $res;

  }

  /**
   * Input Tokenizer
   *
   * Tokenizes the user input based on spaces and quotes.  Then joins phrases
   * together that have an AND, OR, NOT present.
   *
   * @param string $input User's input string
   * @return  array               Tokenized array
   * @access  public
   */
  public function tokenizeInput($input) {
    // Tokenize on spaces and quotes
    //preg_match_all('/"[^"]*"|[^ ]+/', $input, $words);
    preg_match_all('/"[^"]*"[~[0-9]+]*|"[^"]*"|[^ ]+/', $input, $words);
    $words = $words[0];

    // Join words with AND, OR, NOT
    $newWords = array();
    for ($i = 0; $i < count($words); $i++) {
      if (($words[$i] == 'OR') || ($words[$i] == 'AND') || ($words[$i] == 'NOT')) {
        if (count($newWords)) {
          // $newWords[count($newWords)-1] .= ' ' . $words[$i] . ' ' . strtolower($words[$i+1]);
          $newWords[count($newWords) - 1] .= ' ' . $words[$i] . ' ' . $words[$i + 1];
          $i = $i + 1;
        }
      }
      else {
        // $newWords[] = strtolower($words[$i]);
        $newWords[] = $words[$i];
      }
    }

    # Pull out any trailing + or -
    $fixedwords = array();
    foreach ($newWords as &$word) {
      // $word = preg_replace('/[^a-zA-Z0-9\'\"()]\s*$/', '', $word);
      // if (!preg_match('/\S/', $word)) {
      //   continue;
      // }
      $fixedwords[] = $word;
//          error_log("Token is $word");
    }

    // return $newWords;
    return $fixedwords;
  }

  /**
   * Input Validater
   *
   * Cleanes the input based on the Lucene Syntax rules.
   *
   * @param string $input User's input string
   * @return  string                Fixed input
   * @access  public
   */
  public function validateInput($input) {
    // Ensure wildcards are not at beginning of input
    if ((substr($input, 0, 1) == '*') ||
      (substr($input, 0, 1) == '?')) {
      return substr($input, 1);
    }

    // Ensure all parens match
    $start = preg_match_all('/\(/', $input, $tmp);
    $end = preg_match_all('/\)/', $input, $tmp);
    if ($start != $end) {
      return str_replace(array('(', ')'), '', $input);
    }

    // Ensure ^ is used properly
    $cnt = preg_match_all('/\^/', $input, $tmp);
    $matches = preg_match_all('/.+\^[0-9]/', $input, $tmp);

    if (($cnt) && ($cnt !== $matches)) {
      return str_replace('^', '', $input);
    }

    return $input;
  }

  /**
   * Transform the given string just as the exactmatcher type in our Solr install
   *
   * Unlike the solr exactmatcher, we leave * and ? alone and don't worry about unicode for now
   *
   * @param string $str String to exactmatcher-ify
   * @return string Transformed string
   * @access public
   **/

  // <fieldType name="exactmatcher" class="solr.TextField" omitNorms="true">
  //        <analyzer>
  //          <tokenizer class="solr.KeywordTokenizerFactory"/>
  //          <filter class="schema.UnicodeNormalizationFilterFactory" version="icu4j" composed="false" remove_diacritics="true" remove_modifiers="true" fold="true"/>
  //          <filter class="solr.LowerCaseFilterFactory"/>
  //          <filter class="solr.TrimFilterFactory"/>
  //          <filter class="solr.PatternReplaceFilterFactory"
  //               pattern="[^\p{L}\p{N}]" replacement=""  replace="all"
  //          />
  //        </analyzer>
  //      </fieldType>

  function exactmatcherify($str) {
    $str = strtolower($str);
    $str = trim($str);
    $str = preg_replace('/[^\p{L}\p{N}\*\?]/u', '', $str);
    return $str;
  }


  /**
   * Build AND, OR, and Phrase queries
   *
   * Given a lookfor string, clean it up, tokenize it, and
   * return a structure that includes AND, OR, and Phrase
   * queries.
   *
   * @param string $lookfor User's search string
   * @return  array   $values     Includes 'and', 'or', and 'onephrase' elements
   * @access  public
   */

  public function build_and_or_onephrase($lookfor = null) {
    $values = array();

    $illegal = array('.', '{', '}', '/', '!', ':', ';', '[', ']', '(', ')', '+ ', '&', '- ');
    $lookfor = trim(str_replace($illegal, '', $lookfor));

    // Replace fancy quotes
    $lookfor = str_replace(array('“', '”'), '"', $lookfor);

    // If it looks like "..."*, pull out the quotes

    if (preg_match('/^\s*"(.*)"\*\s*$/', $lookfor, $match)) {
      $em = $match[1];
      $lookfor = $em . '*';
      // $em = $this->exactmatcherify($em) . '*';
      // return array('exactmatcher' => $em, 'emstartswith' => $em, 'asis' => $lookfor);
    }

    // Validate input
    $lookfor = $this->validateInput($lookfor);

    if (!preg_match('/\S/', $lookfor)) {
      return false;
    }

    // Tokenize Input
    $tokenized = $this->tokenizeInput($lookfor);

    $values['onephrase'] = '"' . preg_replace('/"/', '', implode(' ', $tokenized)) . '"';
    $values['and'] = implode(' AND ', $tokenized);
    $values['or'] = implode(' OR ', $tokenized);
    $values['asis'] = $lookfor;
    $values['compressed'] = preg_replace('/\s/', '', $lookfor);
    $values['exactmatcher'] = $this->exactmatcherify($lookfor);
    $values['emstartswith'] = $values['exactmatcher'] . '*';
    return $values;
  }


  /**
   * Construct, perform, and process the search
   *
   * @param array $args An array of duples of the form (key, val) for sending straight to solr. One should presumably be 'q'
   * @param string $action The solr action (either 'select' or one defined in solrconfig.xml)
   * @return mixed $result The standard vufind result object, as returned from _process
   **/

  function solrSearch($args, $action = 'standard') {
    $raw = $this->rawSolrSearch($args, $action);
    if (!PEAR::isError($raw)) {
      $processed = $this->_process($raw);
#          if (isset($this->alogdata['logit']) && $this->alogdata['logit'] && isset($this->alogdata['logaction'])) {
#            $data4 = $processed['RecordCount'];
#            $alog = ActivityLog::singleton();
#            $alog->log($this->alogdata['logaction'],
#                       $this->alogdata['data1'],
#                       $this->alogdata['data2'],
#                       $this->alogdata['data3'],
#                       $data4
#                       );
#          }

      return $processed;
    }
    else {
      return $raw;
    }
  }


  /**
   * Do the low-level talking to Solr via the client using HTTP POST
   *
   * @param array $args An array of duples of the form (key, val) for sending straight to solr. One should presumably be 'q'
   * @param string $action The solr action (either 'select' or one defined in solrconfig.xml)
   * @return string The XML (or JSON or whatever) returned from solr
   **/

  # Hack to get around edismax problem with mm; use dismax in the
  # absense of AND/OR/NOT
  protected function set_proper_action($action, $args) {
    if ($action == 'edismax') {
      $q = '';
      foreach ($args as $kv) {
        if ($kv[0] == 'q') {
          $q = $kv[1];
          break;
        }
      }
      if (!preg_match('/\b(AND|OR|NOT)\b/', $q) && !($q == '*:*')) {
        return 'dismax';
      }
      else {
        return $action;
      }
    }
  }

  // Do we just want the IDs? Spit 'em out!
  public function print_out_list_of_ids($args) {
    if ($this->is_list_of_ids_request()) {
      $j = $this->solr_connection->send();
      header("Content-Type: text/plain");
      foreach ($j['response']['docs'] as $doc) {
        foreach ($doc['ht_id'] as $htid) {
          echo $htid, "\n";
        }
      }
    }
  }

  protected function hack_pf_to_pf1($args) {
    $newargs = array();
    foreach ($args as $k => $v) {
      if ($k == "pf") {
        $newargs["pf1"] = $v;
      }
      else {
        $newargs[$k] = $v;
      }
    }
    return $newargs;
  }

  function rawSolrSearch($args, $action = 'standard') {
    global $interface;


    $action = $this->set_proper_action($action, $args);

    $defaults = [
      ['qt', $action],
      ['fl', $this->fields],
      ['ps', 0]
    ];

    $args = array_merge($defaults, $args);

    $nullsearch = false;

    if ($action == 'edismax') {
      $args = $this->hack_pf_to_pf1($args);
    }

    $this->solr_connection->add($args);

    # Just want a list of IDs? Produce it and die
    if (isset($_REQUEST['htid_list'])) {
      $this->print_out_list_of_ids($args);
      die();
    }

    # Finally, we can deal with the normal case
    return $this->solr_connection->send();
  }



//
//
//
//    PEAR::setErrorHandling(PEAR_ERROR_PRINT);
//
//    $result = $this->solr_connection->send();
//    if (isset($_REQUEST['debug'])) {
//      echo '<div style="text-align: left">';
//      echo "<h1>Solr URL</h1>";
//      echo $this->solr_connection->getBody();
//      echo "</pre>";
//      echo "<hr>";
//      echo "<h1>Request object</h1>";
//      echo "<pre>";
//      print_r($this->solr_connection);
//      echo "</pre>";
//      echo "</div>";
//    }
//
//
//    if (!PEAR::isError($result)) {
//      return $result->getBody();
//    }
//    else {
//      return $result;
////    }
//
//  }

  protected function is_list_of_ids_request() {
    return isset($_REQUEST['htid_list']);
  }

  /**
   * Print out a "get" url for a solr query
   *
   *
   */

  function asGetURL($args) {
    $get_sc = new SolrConnection($args, 'GET');
    return $get_sc->asURL();

    return $this->solr_connection->getURL();
  }


  /**
   * Retrieves a document specified by the ID.
   *
   * @param string $id The document to retrieve from Solr
   * @access  public
   * @return  string              The requested resource
   * @throws  object              PEAR Error
   */
  function getRecord($id) {
    // The default action
    $query = "id:$id OR old_ids:$id";
    $result = $this->solrSearch(array(array('q', $query)));
    if (!PEAR::isError($result) && isset($result['record'][0])) {
      $rec =  $result['record'][0];
      $returned_id = $rec['id'];
      if ($returned_id == $id) {
        return $rec;
      } else {
        $this->old_id_301($returned_id);
      }
    }
    else {
      return null;
    }
  }

  function old_id_301($newid) {
    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: /Record/$newid");
    exit();
  }

  /**
   * NEEDS REWRITING!!!! Get records similiar to given record.
   *
   * @access  public
   * @param array $record An associative array of the record data
   * @param string $id The record id
   * @param integer $max The maximum records to return; Default is 5
   * @return  array       An array of query results in standard vufind record format
   * @throws  object      PEAR Error
   */

  function mltesc($str) {
    return str_replace(array('(', ')','[', ']', '!', '&', ':', ';', '-', '/', '"'), '', $str);
  }
  

  function lucene_escape($str) {
    $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
    $replace = '\\\$1';
    return preg_replace($pattern, $replace, $str);
  }

  function getMoreLikeThis($record, $id, $max = 5) {
    global $configArray;

    if ($configArray['System']['debug']) {
      $this->debug = true;
    }

    if ((!isset($record['title'])) || ($record['title'] == '')) {
      return null;
    }

    $query = '(title:(' . $this->lucene_escape($this->mltesc($record['title'][0])) . ')^75';
    if (isset($record['shorttitle'])) {
      $query .= ' OR title:(' . $this->lucene_escape($this->mltesc($record['title'][0])) . ')^100';
    }

    if (isset($record['fulltopic'])) {
      foreach ($record['fulltopic'] as $topic) {
        $query .= ' OR fulltopic:("' . $this->lucene_escape($this->mltesc($topic)) . '")^300';
      }
    }

    if (isset($record['language'])) {
      foreach ($record['language'] as $language) {
        $query .= ' OR language:("' . $this->lucene_escape($this->mltesc($language)) . '")^30';
      }
    }

    if (isset($record['author'])) {
      foreach ($record['author'] as $author) {
        $query .= ' OR author:("' . $this->lucene_escape($this->mltesc($author)) . '")^75';
      }

    }

    if (isset($record['publishDate'])) {
      $pubdate = $record['publishDate'][0];
      if (preg_match('/^\d+$/', $pubdate)) {
        $start = $pubdate - 5;
        $end = $pubdate + 5;
        $query .= " OR publishDateTrie:[$start TO $end]";
      }
    }


    # Remove the current item
    $query .= ') NOT id:(' . $id . ')';

    $ss = new SearchStructure(true); // create a "blank" ss with just the filter queries

    $args = array_merge(array(array('q', $query)), $this->filterComponents($ss));
    return $this->solrSearch($args);
  }

  /**
   * Get record data based on the provided field and phrase.
   * Used for AJAX suggestions.
   *
   * @access  public
   * @param string $phrase The input phrase
   * @param string $field The field to search on
   * @param int $limit The number of results to return
   * @return  array   An array of query results
   */
  function getSuggestion($phrase, $field, $limit) {
    if (!strlen($phrase)) {
      return null;
    }

    // Ignore illegal characters
    $illegal = array('!', ':', '[', ']');
    $phrase = str_replace($illegal, '', $phrase);
    $phrase = ucfirst($phrase);

    // Process Search
    $query = "$field:($phrase*)";
    $result = $this->search($query, null, null, $limit, null, array('field' => 'title', 'limit' => $limit));

    return $result['Facets']['Cluster']['item'];
  }

}


?>
