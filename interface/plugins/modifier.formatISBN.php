<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     FormatISBN
 * Purpose:  Formats an ISBN number
 * -------------------------------------------------------------
 */
function smarty_modifier_formatISBN($isbn) {
    if ($pos = strpos(trim($isbn), ' ')) {
        return substr($isbn, 0, $pos);
    } else {
        return $isbn;
    }
}
?>