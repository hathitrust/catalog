<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     highlight
 * Purpose:  Adds a span tag with class "highlight" around a
 *           specific phrase for highlighting
 * -------------------------------------------------------------
 */
function smarty_modifier_highlight($haystack, $needle) {
    if (is_array($needle)) {
        foreach ($needle as $phrase) {
            if ($phrase != '') {
                $phrase = preg_quote($phrase);
                $phrase = str_replace('/', '\/', $phrase);
                $haystack = preg_replace("/($phrase)/i", '<span class="highlight">$1</span>', $haystack);
            }
        }
    } elseif ($needle != '') {
        $needle = preg_quote($needle);
        $needle = str_replace('/', '\/', $needle);
        $haystack = preg_replace("/($needle)/i", '<span class="highlight">$1</span>', $haystack);
    }
    
    return $haystack;
}
?>