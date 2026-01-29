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
require_once 'vendor/autoload.php';
require_once 'services/Search/SearchStructure.php';
require_once 'services/Record/FilterFormat.php';
require_once 'lib/LCCallNumberNormalizer.php';
require_once "sys/Normalize.php";

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

    // Add the pagination

    $args[] = array('start', $start);
    $args[] = array('rows', $limit);


    // Spell checking? Also used as a way to determine we came from simple search for logging
    if ($ss->checkSpelling) {
      $args = array_merge($args, $this->spellcheckComponents($ss));
    }

    // print_r("----- Solr query ------: " . json_encode($args, JSON_UNESCAPED_UNICODE));

    // $raw is always false, so rawSolrSearch is never used
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

      // Filter out facet values that match the hidden pattern defined on config.ini.
      // e.g. skip hlbgeneral = "hlb_both:^U\.S\. National and" to hide "U.S. National..." facets
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
  // TODO: Remove this function that is never used because the field type is not defined in conf/dismaxsearchspecs.yaml
  // TODO: Check by function used by this that could be removed too.
  function dismaxSearchArguments($ss) {
    $rv = array();
    // Should just be on "lookfor" and "type"
    $tvb = isset($ss->search[0]) ? $ss->search[0] : array('all', '*:*');
    $type = $tvb[0];
    // $value is the search string
    $value = $tvb[1];

    // If search is empty/whitespace-only, default to *:* (match-all)
    if (!preg_match('/\S/', $value)) {
      $value = '*:*';
    }
    // Get the yaml file
    $allspecs = yaml_parse_file('conf/dismaxsearchspecs.yaml');

    // If the type isn't set, back up to normal arguments
    // Lianet's notes: $type is extracted from conf/dismaxsearchspecs.yaml so the function always return the args in searchArguments

    if (!isset($allspecs[$type])) {
      $args =  $this->searchArguments($ss);
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

    // Lianet's notes: conf/searchspecs is the config used to build the Solr query.
    // the specs looks like {"callnoletters":{"callnoletters":[["emstartswith",1]]}, ...}
    $specs = yaml_parse_file('conf/searchspecs.yaml');
    $query = '';

    foreach ($ss->search as $tvb) { // Type, Value (keywords), Boolean AND or OR

      // $tvb looks like: ['title', 'nature, and history', 'AND']
      $type = $tvb[0];

      // TODO: Refactoring to call build_and_or_onephrase only once per search term, not once per type
      $values = $this->build_and_or_onephrase($tvb[1]);
      // $values looks like:
      /**
      "onephrase":nature, and history,
      "and":"nature, AND and AND history",
      "or":"nature, OR and OR history",
      "asis":"nature, and history",
      "compressed":"nature,andhistory",
      "exactmatcher":"natureandhistory",
      "emstartswith":"natureandhistory*"}"
      */

      $bool = isset($tvb[2]) ? $tvb[2] : false;
      // Build the component for this type
      // Some components are created by build_and_or_onephrase
      // Other components are extracted from the specs file
      // e.g.
      if (isset($specs[$type]) && $values) {
        // $comp looks like: "(issn:(DS753\\ H827) OR isbn:(DS753\\ H827) OR lccn:(\"DS753 H827\") OR lccn:(DS753\\ H827) OR ctrlnum:(ds753h827) OR rptnum:(\"DS753 H827\") OR rptnum:(DS753\\ H827) OR rptnum:(ds753h827) OR oclc_search:(\"DS753 H827\") OR oclc_search:(ds753h827) OR sdrnum:(\"DS753 H827\") OR sdrnum:(ds753h827) OR ht_id:(\"DS753 H827\") OR ht_id:(ds753h827) OR ht_id:(DS753\\ H827) OR id:(\"DS753 H827\"))"
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
    // Check if the query has content, otherwise use *:* to match all
    if (preg_match('/\S/', $query)) {
      $searchComponents[] = array('q', $query);
    }
    else {
      $searchComponents[] = array('q', '*:*');
    }
    return $searchComponents;
  }


  /** Quote a filter value, skipping it if it starts with a '[' (and hence is assumed
   * to be a range). Detect date range
   * Respect range queries ([ TO ])
   * Always quotes values
   * Use lucene_escape_fq because stricter escaped is required
   **/

  function quoteFilterValue($v) {
    if (preg_match('/^\[/', $v)) {
      return $v;
    }
    else {
      // Escape internal quotes before wrapping
      // input: He said "hello, the output: He said \"hello
      // $escaped = str_replace('"', '\\"', $v);
      // String ready to Solr "He said \"hello"
      // examples:
      // publishDateRange:"1870\-1879"
      // NOT ht_rightscode:"tombstone"
      // NOT deleted:"true"
      return '"' . $this->lucene_escape_fq($v) . '"';
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

      # Add fultext filter if ht_availability is set to "Full text"
      
      if ($index == "ht_availability" and $oval == 'Full text') {
        $ft = $this->fulltext_filter_base();
        $ft = $this->fulltext_filter_add_jan1_rollover($ft);
        $ft = $this->fulltext_filter_add_etas_or_resource_sharing($ft);
        $rv[] = $ft;
      }
      else { // otherwise, just do it like normal
        $rv[] = implode(':', array($index, $val));
      }
    }
    // print_r("Filter Creator : " . json_encode($rv, JSON_UNESCAPED_UNICODE));
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

  function fulltext_filter_add_etas_or_resource_sharing($current_ft_filter) {
    global $htstatus;
    if ($htstatus->emergency_access || (isset($htstatus->r['resourceSharing']) && $htstatus->r['resourceSharing'])) {
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
    * Escapes the characters must be escaped inside quoted Lucene text
    * " → \"
    * \ → \\
    *
    * Assumes:
    * - Input is NOT wrapped in quotes
    * - Output will be wrapped by the caller
    * This function must never add quotes.
 */
  public function escapeLuceneLiteral(string $input): string
    {
        // Escape only what Lucene requires inside a quoted phrase
        return preg_replace('/(["\\\\])/', '\\\\$1', $input);
    }

   /**
     * Escape a term.
     *
     * A term is a single word.
     * All characters that have a special meaning in a Solr query are escaped.
     * It must never introduce quotes or phrases.
    * Lucene's Standard Query Parser special characters are:
    * + - && || ! ( ) { } [ ] ^ " ~ * ? : \ /
    * Escape Lucene special characters for a literal term (NO operators)
     *
     * @see https://solr.apache.org/guide/the-standard-query-parser.html#escaping-special-characters
     *
     * @param string $input
     *
     * @return string
     */
   public function escapeTerm(string $input): string
    {

        // Escape backslash FIRST to avoid double-escaping
        $s = str_replace('\\', '\\\\', $input);

        // Use regex to catch all specials, including spaces, in one go
        // The characters are: + - && || ! ( ) { } [ ] ^ " ~ * ? : /
        // Note: && and || are handled as single chars & and | here
        $pattern = '/([\+\-\!\(\)\{\}\[\]\^\"\~\*\?\:\/\&\|])/';

        return preg_replace($pattern, '\\\\$1', $s);
   }

   /**
     * Escape a phrase.
     *
     * A phrase is a group of words.
     * Special characters will be escaped and the phrase will be surrounded by
     * double quotes to group the input into a single phrase.
     * Checking by double quotes can be removed because the input is always a phrase without quotes.
     * Escapes " and \. Never escape booleans, commas, or operators inside a quoted phrase.
     * Do mind that you cannot build a complete query first and then pass it to
     * this method, the whole query will be escaped. You need to escape only the
     * 'content' of your query.
     * This function was inspired by Solarium: https://github.com/solariumphp/solarium/blob/d6a55d2c1cbaaa78edfbfd8f9b090978a6b6fd83/src/Core/Query/Helper.php#L93
     * @param string $input
     *
     * @return string
   */
   public function escapePhrase(string $input): string
    {
        // Remove surrounding quotes if any
        if (strlen($input) >= 2 && $input[0] === '"' && substr($input, -1) === '"') {
            $input = substr($input, 1, -1);
        }

    return '"' . $this->escapeLuceneLiteral($input) . '"';
    }

  /**
    * Escape a boolean expression for Lucene boolean search. Use for: term1 AND term2 OR "phrase here"
    * Use for: dramatic AND literature
    * Operators AND, OR are preserved, everything else is escaped as literal (Terms). No accidental field injection
  */
  public function escapeBoolean(string $expr): string {
    // Split using AND, OR as delimiters (case-insensitive)

    $tokens = explode(' ', $expr);
    $out = [];
    foreach ($tokens as $t) {
        if ($t === 'AND' || $t === 'OR') {
            $out[] = $t;
        } else {
            $out[] = $this->escapeTerm($t);
        }
    }

    return implode(' ', $out);
  }

 /**
 * Escape a prefix query for Lucene prefix search. Use for: prefix*
 * Prevents *table
 * Escape only what Lucene requires inside a quoted phrase
 * Preserves prefix semantics
 * @return string The escaped prefix query
 */
 public function escapePrefix(string $prefix): string {

    $base = substr($prefix, 0, -1);
    return $this->escapeLuceneLiteral($base) . '*';
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

      // is_numeric($field) is true if we've got an un-hashed array, used for grouping
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

        $val = $valweight[0]; // e.g. onephrase, and, or, emstartswith, compressed, exactmatcher, lcnormalized, stdnum
        $weight = $valweight[1]; // e.g. Null, 100, 60


        // lcnormalized and stdnum are not fields generated by build_and_or_onephrase,
        // they will be added here and they are already normalized
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
            // Extract standard number from asis input
            // Strips leading 0s. Captures digits, dashes, dots: 978-0-123-45678-9
            // e.g.  0000978-0-12-345678-9 → 978-0-12-345678-9
            if (preg_match('/^\s*0*([\d\-\.]+[xX]?).*$/', $values['asis'], $match)) {
              $stdnum = $match[1];
              $stdnum = Normalize::stdnum($stdnum);
              $values[$val] = $stdnum;
            }
          }
        }

        if (!isset($values[$val]) || ($values[$val] == "")) {
          continue;
        }

        // Lianet's notes: Escapes based on semantic role. It is safe embedding in field:value syntax
        switch ($val) {

            case 'lcnormalized':
                $escaped_value = $values[$val];
                break;
            case 'stdnum':
                // 978-0-123-45678-9
                $escaped_value = $values[$val];
                break;
            case 'onephrase':
                // "\"dramatic literature, comprehending critical\""
                $escaped_value = $this->escapePhrase($values[$val]);
                break;
            case 'and':
            case 'or':
                // and -- dramatic AND literature, AND comprehending AND critical
                // or -- dramatic OR literature, OR comprehending OR critical
                // \"dramatic AND literature\, AND comprehending AND critical\"
                // See how $values[$var] looks war AND and AND peace AND war AND and AND rest

                $escaped_value = $this->escapeBoolean($values[$val]);
                break;
            case 'emstartswith':
                // dramaticliteraturecomprehendingcritical*
                $escaped_value = $this->escapePrefix($values[$val]);
                break;
            case 'compressed':
                // dramaticliteraturecomprehendingcritical (no spaces)
                $escaped_value = $this->escapeTerm($values[$val]);
                break;
            default:
                // exactmatcher - dramaticliteraturecomprehendingcritical
                $escaped_value = $this->escapeTerm($values[$val]);
        }

        if ($escaped_value === '""' || $escaped_value === '') {
            continue;
        }

        $sstring = $field . ':(' . $escaped_value . ')';
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
   * Turn solr output into a record structure (which should probably be its own class...)
   *
   * @param string $result The XML returned by solr
   * @param string $xslfile The path of the XSL file to use to convert the data
   * @return array A structure representing the returned data after transformation via $xslfile
   */


  function _process($result, $xslfile = 'xsl/solr-convert.xsl') {
    global $configArray;

    if (is_string($result) && preg_match('/^<html/', $result)) {
      // Detect if Solr returns an error page
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
  // TODO: refactor tokenizer to a single-pass parser instead of regex
  public function tokenizeInput($input) {
    // Tokenize on spaces and quotes
    //preg_match_all('/"[^"]*"|[^ ]+/', $input, $words);
    // /"[^"]*"[~[0-9]+]* --> to capture fuzzy searches like "hello world"~5 - matches a double-quoted string followed by ~ and a number
    // "[^"]*" --> to capture exact phrases like "hello world" - matches a double-quoted string
    // [^ ]+ --> to capture single words like hello - matches sequences of non-space characters
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
    }

    return $fixedwords;
  }


  /**
  * Remove leading wildcards from input
  * Ensure wildcards are not at beginning of input
  * Before using this function you should check if there is Use this wildcards to remove, otherwise it
  * will remove always the first character
  * Performance guard, not a security guard. Prevent expensive queries (*table, ?table)

  * @param string $input User's input string
  * @return  string               Input string without leading wildcards
  * @access  public
  */
  public function remove_first_character($input) {
    return substr($input, 1);
    }

  /**
  * Remove parentheses from input
  * Use this function if you want to remove parentheses from input
  * It is used if there is unbalanced parentheses in the input
  * Prevents Solr parser errors. Deletes all parentheses instead of fixing structure

  * @param string $input User's input string
    * @return  string               Input string without parentheses
    * @access  public
  */
  public function remove_parentheses($input) {
    return str_replace(array('(', ')'), '', $input);
    }

  /**
  * Remove wrapping double quotes from a string, if present.
  *
  * Examples:
  *  - '"table"'        → table
  *  - ' "table" '      → table
  *  - '"table name"'   → table name
  *  - 'table "name"'   → table "name"
  *  - '"table"name"'  → table"name
  *
  * @param string $s
  * @return string
  */
  public function remove_quotes(string $s): string {
   $s = trim($s);

   if (mb_strlen($s) >= 2 && $s[0] === '"' && substr($s, -1) === '"') {
    return substr($s, 1, -1);
   }

   return $s;
  }

  /**
    * Remove invalid caret (^) usage from input
    * Ensure ^ is used properly - Prevent invalid syntax as table^, table^abc
    * Use this function if there is invalid caret usage in the input
    * @param string $input User's input string
    * @return  string               Input string without invalid caret usage
    * @access  public
  */
  public function remove_invalid_caret_usage($input) {
    return str_replace('^', '', $input);
  }

  /**
  * If input matches the pattern: "phrase"*,
  * return phrase* (quotes removed, wildcard preserved).
  * Otherwise return null.

    * @param string $input User's input string
    * @return  string|null          Unwrapped quoted wildcard or null
    * @access  public
  */
  public function unwrapQuotedWildcard(string $input): ?string {
    // Match: optional whitespace + "..." + * + optional whitespace
    // ^\s* --> leading whitespace
    // " --> opening quote
    // ([^"]+) --> capture group for any characters except quotes (the phrase)
    // " --> closing quote
    // \* --> literal asterisk
    // \s*$ --> trailing whitespace
    if (preg_match('/^\s*"([^"]+)"\*\s*$/u', $input, $matches)) {
        return $matches[1] . '*';
    }

    return null;
  }

  /**
   * Input Validater
   *
   * Validate the user input for Solr queries.
   * This function is effective if:
   * It is used before building the query
   * It runs before escaping
   * It rejects invalid syntax instead of trying to fix it
   * Escaping is done after validation
   * This validator:
   * - Rejects empty input or garbage-only input
   * - Rejects meaningless single-character input (~, \)
   * - Rejects leading wildcards (*, ?)
    * - Validates balanced parentheses and quotes
    * - Validates boost syntax (^number)
    * - Validates fuzzy operators (~N)
    * - Validates fielded queries (field:value)
    * - Rejects empty boolean groups
    *
    * @param string $input Raw user input
    * @return array{valid: bool, error?: string} Validation result
   * @access  public
   */
  // TODO: Add the rule: Reject fuzzy operators like "~2"
  // Lianet's notes: Verify if this function could be used to validate the Solr query
  public function validateInput($input) {

    // 1. Normalize + trim
    $trimmed = trim($input);

    //print("Input --- : " . json_encode($trimmed, JSON_UNESCAPED_UNICODE));

    // 2. Empty input
    if ($trimmed === '') {
     return ['valid' => false, 'error' => 'Empty query'];
    }

    // 3. Strip garbage-only input ~~//^&$ (no letters or numbers)
    if ($trimmed !== '' && !preg_match('/[\p{L}\p{N}]/u', $trimmed)) {
        return ['valid' => false, 'error' => 'Invalid garbage-only query'];
    }

    // 4. Reject meaningless single-character input (~ or \)
    if (mb_strlen($trimmed) === 1 && preg_match('/^[~\\\\]$/', $trimmed)) {
      return ['valid' => false, 'error' => 'Invalid single-character query'];
    }

    // 5. No leading wildcard
    // Ensure wildcards are not at beginning of input
    // Performance guard, not a security guard. Prevent expensive queries (*table, ?table)
    if ($trimmed[0] === '*' || $trimmed[0] === '?') {
      return ['valid' => false, 'error' => 'Leading wildcard not allowed'];
    }

    // 6. Balanced parentheses
    // Ensure all parens match - parentheses balancing
    // Prevents Solr parser errors. Deletes all parentheses instead of fixing structure
    if (substr_count($trimmed, '(') !== substr_count($trimmed, ')')) {
      return ['valid' => false, 'error' => 'Unbalanced parentheses'];
    }
    // 7. Balanced quotes
    if (substr_count($trimmed, '"') % 2 !== 0) {
        return ['valid' => false, 'error' => 'Unbalanced quotes'];
    }

    // 8. Valid boost syntax (^number or ^number.number)
    // Ensure ^ is used properly - Prevent invalid syntax as table^, table^abc
    // Rejects invalid boosts (^, ^abc)
    if (preg_match_all('/\^([^\s]+)/', $trimmed, $matches)) {
        foreach ($matches[1] as $boost) {
            if (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $boost)) {
                return ['valid' => false, 'error' => 'Invalid boost syntax'];
            }
        }
    }

    // 9. Reject multiple boosts on the same term (table^2^3)
    if (preg_match('/\^[0-9]+(\.[0-9]+)?\s*\^/', $trimmed)) {
     return ['valid' => false, 'error' => 'Multiple boosts on same term'];
    }

    // 10. Reject dangling boost operator like table^
    if (preg_match('/\^\s*(\)|$)/', $trimmed)) {
        return ['valid' => false, 'error' => 'Dangling boost operator'];
    }

    // ---------------------
    // 11. Fuzzy operator validation
    // ---------------------

    // Rules:
    // - ~ must be followed by a non-negative integer
    // - ~ cannot be doubled (~~)
    // - ~ must not be followed by letters
    // - ~ must not appear inside field: without a term
    // - standalone ~2 IS allowed (syntactically valid Lucene)

    // 11.1 Reject repeated fuzzy operators like table~~2
    if (preg_match('/~~+/', $trimmed)) {
        return ['valid' => false, 'error' => 'Repeated fuzzy operator'];
    }

    // 11.2 Reject fuzzy operators with non-numeric distance (table~abc, "foo"~x)
    if (preg_match('/~(?!\d+\b)/', $trimmed)) {
        return ['valid' => false, 'error' => 'Invalid fuzzy syntax'];
    }

    // 11.3 Reject fielded fuzzy with no term: title:~2
    if (preg_match('/\b[\w\-]+:\s*~\d+\b/', $trimmed)) {
        return ['valid' => false, 'error' => 'Fuzzy operator without term'];
    }

    // ---------------------
    // 12. Field validation
    // ---------------------

    // 12.1 Reject empty field groups like title:( )
    if (preg_match('/\b[\w\-]+:\(\s*\)/', $trimmed)) {
     return ['valid' => false, 'error' => 'Empty field group'];
    }

    // 12.2 Reject empty field values like title:
    if (preg_match('/\b[\w\-]+:\s*(\)|$)/', $trimmed)) {
     return ['valid' => false, 'error' => 'Empty field value'];
    }

    // 12.3. Fielded query validation (field:value)
    // Rejects malformed field queries (title:, :table, title::table)
    if (preg_match_all('/(\b[\w\-]+):/', $trimmed, $fields)) {
        foreach ($fields[1] as $field) {
            // Reject empty field names (shouldn't happen due to regex)
            if ($field === '') {
                return ['valid' => false, 'error' => 'Empty field name'];
            }
        }
    }

    // 13. Reject dangling colons
    if (preg_match('/(^|[^\\w]):|::/', $trimmed)) {
        return ['valid' => false, 'error' => 'Malformed field:value syntax'];
    }

    // 14. Reject empty boolean groups inside parentheses
    // Rejects empty boolean groups ((AND), (OR NOT))
    if (preg_match('/\((\s*(AND|OR|NOT)\s*)+\)/i', $trimmed)) {
        return ['valid' => false, 'error' => 'Empty boolean group'];
    }

    // 15. Reject fielded queries ending with boolean operators
    if (preg_match('/\b[\w\-]+:\([^)]*(AND|OR|NOT)\s*\)/i', $trimmed)) {
     return ['valid' => false, 'error' => 'Incomplete boolean expression in field'];
    }

 return ['valid' => true];

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
  * Converts any invalid Lucene input into a safe, terms-only query.
  * - Removes all Lucene syntax
  * - Preserves letters and numbers only
  * - Used only when intent is recoverable but syntax is unsafe
  */
  public function sanitizeToTerms(string $input): string
  {
    // 1. Normalize whitespace
    $s = trim($input);

    // 2. Replace all non-letter/non-number with space
    // \p{L} = any Unicode letter
    // \p{N} = any Unicode number
    $s = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $s);

    // 3. Collapse multiple spaces
    $s = preg_replace('/\s+/u', ' ', $s);

    // 4. Trim again
    $s = trim($s);

    return $s;
  }

  /**
   * Build AND, OR, and Phrase queries
   *
   * Given a lookfor string, clean it up, tokenize it, and
   * return a structure that includes AND, OR, and Phrase
   * queries. lookfor could be single or multi-word
   *
   * @param string $lookfor User's search string
   * @return  array   $values     Includes 'and', 'or', and 'onephrase' elements
   * @access  public
   */
  // Lianet's notes: Check if is necessary to remove illegal characters
  // TODO: Refactoring this function to avoid the different output
  public function build_and_or_onephrase($lookfor = null) {
    $values = array();

    //$illegal = array('.', '{', '}', '/', '!', ':', ';', '[', ']', '(', ')', '+ ', '&', '- ');
    //$lookfor = trim(str_replace($illegal, '', $lookfor));


    // Replace fancy quotes
    $lookfor = str_replace(array('“', '”'), '"', $lookfor);


    // If it looks like "..."*, pull out the quotes
    $unwrapped = $this->unwrapQuotedWildcard($lookfor);
    if ($unwrapped !== null) {
        $lookfor = $unwrapped;
    }

    // Handles multiple issue in the query that make it invalid
    // Each pass fix one issue
    // $maxPasses is to prevent infinity loops
    // Loop continue until query is valid or sanitized
    $maxPasses = 3;
    $pass = 0;

    do {

        $validation = $this->validateInput($lookfor);

        //print("Validation Result --- : " . json_encode($validation, JSON_UNESCAPED_UNICODE));


        if ($validation['valid']) {
            break;
        }

        // Considering the logic of updating the user input query as the application is doing now

        switch ($validation['error']) {

            // -------------------------
            // HARD FALLBACK (*:*)
            // -------------------------
            case 'Empty query':
            case 'Invalid garbage-only query':
            case 'Invalid single-character query':
                 return false;

            // -------------------------
            // STRUCTURAL FIXES
            // -------------------------

            case 'Leading wildcard not allowed':
                 $lookfor = $this->remove_first_character($lookfor);
                 break;
            case 'Unbalanced parentheses':
                 $lookfor = $this->remove_parentheses($lookfor);
                 break;
            case 'Unbalanced quotes':
                 $lookfor = $this->remove_quotes($lookfor);
                 break;

            case 'Invalid boost syntax':
            case 'Multiple boosts on same term':
            case 'Dangling boost operator':
                 $lookfor = $this->remove_invalid_caret_usage($lookfor);
                 break;

            // -------------------------
            // SANITIZE TO TERMS-ONLY
            // -------------------------
            case 'Repeated fuzzy operator':
            case 'Invalid fuzzy syntax':
            case 'Fuzzy operator without term':
            case 'Empty field group':
            case 'Empty field value':
            case 'Empty field name':
            case 'Malformed field:value syntax':
            case 'Dangling colon':
                 $lookfor = $this->sanitizeToTerms($lookfor);
                 break;

            // -------------------------
            // SAFETY NET
            // -------------------------
            default:
               $lookfor = $this->sanitizeToTerms($lookfor);
               break;
        }
        // print_r("SANITIZE Input string: " . $lookfor);
        // print_r("Count pass" . $pass);
        $pass++;
    } while ($pass < $maxPasses);

    if (!preg_match('/\S/', $lookfor)) {
      return false;
    }

    // Tokenize Input
    $tokenized = $this->tokenizeInput($lookfor);

    // Phrase search - "dramatic literature, comprehending critical"
    $values['onephrase'] = preg_replace('/"/', '', implode(' ', $tokenized));
    // AND search - dramatic AND literature, AND comprehending AND critical
    $values['and'] = implode(' AND ', $tokenized);
    // OR search - dramatic OR literature, OR comprehending OR critical
    $values['or'] = implode(' OR ', $tokenized);
    // As-is search - dramatic literature, comprehending critical
    $values['asis'] = $lookfor;
    // Compressed search - dramaticliterature,comprehendingcritical
    $values['compressed'] = preg_replace('/\s/', '', $lookfor);
    // Exactmatcher search - dramaticliteraturecomprehendingcritical
    $values['exactmatcher'] = $this->exactmatcherify($lookfor);
    // Exactmatcher startswith search - dramaticliteraturecomprehendingcritical*
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
    $raw = $this->rawSolrSearch($args, $action); // This is the Solr output
    if (!PEAR::isError($raw)) {
      $processed = $this->_process($raw);

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
    // Ensure a non-NULL return from non-edismax cases
    // return $action;
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
    // Don't get deleted record stubs -- these are present in solr to support
    // reporting on deleted records via OAI
    $filter_query = "NOT deleted:true";
    $result = $this->solrSearch(array(array('q', $query), array('fq',$filter_query)));
//    $result = $this->solrSearch(array(array('q', $query)));
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
  

  /**
   * Strict escape for filter, facets and ranges values and explicit field:value fragments.

   * Use for all fq values and any time you construct field:value with user data.
   * This function:
   * - Normalizes Unicode to NFC form
   * - Removes control characters
   * - Escapes backslash FIRST (critical ordering)
   * - Escapes multi-char tokens (&&, ||)
   * - Escapes all Lucene special characters: + - ! ( ) { } [ ] ^ " ~ * ? : /
   *
   * @param string $s Raw user input or value to escape
   * @return string Safely escaped value for use in Solr fq or field:value
   */
   public function lucene_escape_fq(string $s): string {
    // Normalize Unicode to composed form (NFC)
    if (function_exists('normalizer_normalize')) {
        $s = normalizer_normalize($s, Normalizer::FORM_C) ?: $s;
    }

    // Remove control characters (0x00-0x1F, 0x7F)
    $s = preg_replace('/[\x00-\x1F\x7F]/u', '', $s);

    // Escape backslash FIRST to avoid double-escaping
    $s = str_replace('\\', '\\\\', $s);

    // Use regex to catch all specials, including spaces, in one go
    // The characters are: + - && || ! ( ) { } [ ] ^ " ~ * ? : /
    // Note: && and || are handled as single chars & and | here
    $pattern = '/([\+\-\!\(\)\{\}\[\]\^\"\~\*\?\:\/\&\|])/';

    // '\\\\$1' → runtime '\\$1' → Lucene '\<char>'
    return preg_replace($pattern, '\\\\$1', $s);
   }

  function getMoreLikeThis($record, $id, $max = 5) {
    global $configArray;

    if ($configArray['System']['debug']) {
      $this->debug = true;
    }

    if ((!isset($record['title'])) || ($record['title'] == '')) {
      return null;
    }

    $query = '(title:("' . $this->lucene_escape_fq($this->mltesc($record['title'][0])) . '")^75';
    if (isset($record['shorttitle'])) {
      $query .= ' OR title:("' . $this->lucene_escape_fq($this->mltesc($record['title'][0])) . '")^100';
    }

    if (isset($record['fulltopic'])) {
      foreach ($record['fulltopic'] as $topic) {
        $query .= ' OR fulltopic:("' . $this->lucene_escape_fq($this->mltesc($topic)) . '")^300';
      }
    }

    if (isset($record['language'])) {
      foreach ($record['language'] as $language) {
        $query .= ' OR language:("' . $this->lucene_escape_fq($this->mltesc($language)) . '")^30';
      }
    }

    if (isset($record['author'])) {
      foreach ($record['author'] as $author) {
        $query .= ' OR author:("' . $this->lucene_escape_fq($this->mltesc($author)) . '")^75';
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
