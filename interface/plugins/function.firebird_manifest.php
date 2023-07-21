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
function smarty_function_firebird_manifest($params, &$smarty)
{
  $manifest = $smarty->get_template_vars('firebird_manifest');
  $output = array(
    'stylesheet' => '//hathitrust-firebird-common.netlify.app/assets/index.css',
    'script' => '//hathitrust-firebird-common.netlify.app/assets/index.js'
  );
  if ( $manifest ) {
    $output['stylesheet'] = '/common/firebird/dist/' . $manifest['index.css']['file'];
    $output['script'] = '/common/firebird/dist/' . $manifest['index.html']['file'];
  }
  return json_encode($output);
}
