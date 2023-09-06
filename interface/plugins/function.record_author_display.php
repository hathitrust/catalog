<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.record_author_display.php
 * Type:     function
 * Name:     record_author_display
 * Purpose:  Displays a single 1XX or 7XX as an anchor in Record display
 * -------------------------------------------------------------
 */

# Which 700/710/711 subfields do we use for display/search/extra links?
# extra fields are appended to the display value but are not part of the link.
const F_7XX_SUBFIELDS = array(
  '100' => ['display' => 'aqbcd', 'search' => 'aqbcd'],
  '110' => ['display' => 'ab', 'search' => 'ab'],
  '111' => ['display' => 'acdn', 'search' => 'acdn'],
  '700' => ['display' => 'aqbcd', 'search' => 'aqbcd', 'extra' => 'e'],
  '710' => ['display' => 'ab', 'search' => 'ab'],
  '711' => ['display' => 'acdn', 'search' => 'acdn']
);

const TRAILING_COMMA_REGEX = '/,\s*$/';

function smarty_function_record_author_display($params, &$smarty) {
  $marc_field = $params['marc_field'];
  # $url and $inst are template/global variables used here only for URL generation
  $url = $params['url'];
  $inst = $params['inst'];
  $tag = $marc_field->getTag();
  $display_value = _extract_subfields($marc_field, $tag, 'display');
  # Remove trailing comma -- if there's a role it will be re-added
  $display_value = preg_replace(TRAILING_COMMA_REGEX, '', $display_value);
  $search_value = urlencode(_extract_subfields($marc_field, $tag, 'search', TRAILING_COMMA_REGEX));
  $extra_value = _extract_subfields($marc_field, $tag, 'extra');
  if (strlen($extra_value)) {
    $extra_value = ', ' . $extra_value;
  }
  return <<<HTML
<a href="{$url}/Search/Home?lookfor=%22{$search_value}%22&amp;type=author&amp;inst={$inst}">
  {$display_value}</a>{$extra_value}
HTML;
}

# Return a list of subfields as a space-delimited string of values.
# tag = {'100', '100', '111', '700', '710', '711'}
# display_type = {'display', 'search', 'extra'}
# filter is a deletion regex to apply to each subfield value
function _extract_subfields($marc_field, $tag, $display_type, $filter = null) {
  $values = array();
  # e.g., $subfields = ['a', 'b', 'c', 'd']
  $subfields = str_split(F_7XX_SUBFIELDS[$tag][$display_type] ?? '');
  foreach ($subfields as $subfield_code) {
    $subfield = $marc_field->getSubfield($subfield_code);
    if ($subfield) {
      $subfield_value = $subfield->getData();
      if ($subfield_value) {
        if ($filter) {
          $subfield_value = preg_replace($filter, '', $subfield_value);
        }
        $values[] = $subfield_value;
      }
    }
  }
  return implode(' ', $values);
}

?>
