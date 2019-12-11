<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.css_link.php
 * Type:     function
 * Name:     timestamp
 * Purpose:  outputs a random magic answer
 * -------------------------------------------------------------
 */
function smarty_function_css_link($params, &$smarty)
{
  $base_filename = preg_replace('#^/([^/]+)/#', '/${1}/web/', $params['href']);
  $root = dirname($_SERVER['DOCUMENT_ROOT']);
  $filename = FALSE;
  if ( is_dir("$root/common") ) {
    $filename = "$root/${base_filename}";
  } elseif ( is_dir("$root/../babel/common") ) {
    $filename = "$root/../babel/${base_filename}";
  }
  $modtime = ( $filename === FALSE ) ? time() : filemtime($filename);
  return "<link rel=\"stylesheet\" type=\"text/css\" href=\"${params['href']}?_=${modtime}\" />";
}
?>