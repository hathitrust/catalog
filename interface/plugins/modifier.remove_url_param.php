<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     getvalue
 * Purpose:  Prints the subfield data of a MARC_Field object
 * -------------------------------------------------------------
 */
function smarty_modifier_remove_url_param($url, $paramname) {
    $pat = "/(?:&amp;|\?){$paramname}=[^&]+/";
    $s = preg_replace($pat, "", $url);
    return $s;
}
?>