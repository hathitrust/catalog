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
function smarty_function_firebird_manifest($params, $template)
{
  $manifest = $template->getTemplateVars('firebird_manifest');  // Smarty 4 method

  if ( $manifest ) {
    $output['stylesheet'] = '/common/firebird/dist/' . $manifest['index.css']['file'];
    $output['script'] = '/common/firebird/dist/' . $manifest['index.html']['file'];
  }
  return json_encode($output);
}
