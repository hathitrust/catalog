<?php

require_once 'vendor/autoload.php';

class Pager_LoggingPager extends Pager_Sliding
{

  function _renderLink($altText, $linkText)
     {
         if ($this->_httpMethod == 'GET') {
             if ($this->_append) {
                 $href = '?' . $this->_http_build_query_wrapper($this->_linkData);
             } else {
                 $href = str_replace('%d', $this->_linkData[$this->_urlVar], $this->_fileName);
             }
             $onclick = '';
             if (array_key_exists($this->_urlVar, $this->_linkData)) {
                 $onclick = str_replace('%d', $this->_linkData[$this->_urlVar], $this->_onclick);
             }
             // return sprintf('<a  href="%s"%s%s%s%s title="%s">%s</a>',
             //                htmlentities($this->_url . $href, ENT_COMPAT, 'UTF-8'),
             //                empty($this->_classString) ? '' : ' '.$this->_classString,
             //                empty($this->_attributes)  ? '' : ' '.$this->_attributes,

             //                empty($this->_accesskey)   ? '' : ' accesskey="'.$this->_linkData[$this->_urlVar].'"',
             //                empty($onclick)            ? '' : ' onclick="'.$onclick.'"',
             //                $altText,
             //                $linkText
             // );

             return sprintf('<a  href="%s"%s%s%s%s>%s</a>',
                            htmlentities($this->_url . $href, ENT_COMPAT, 'UTF-8'),
                            empty($this->_classString) ? '' : ' '.$this->_classString,
                            empty($this->_attributes)  ? '' : ' '.$this->_attributes,

                            empty($this->_accesskey)   ? '' : ' accesskey="'.$this->_linkData[$this->_urlVar].'"',
                            empty($onclick)            ? '' : ' onclick="'.$onclick.'"',
                            $linkText
             );

         } elseif ($this->_httpMethod == 'POST') {
             $href = $this->_url;
             if (!empty($_GET)) {
                 $href .= '?' . $this->_http_build_query_wrapper($_GET);
             }
             return sprintf("<a href='javascript:void(0)' onclick='%s'%s%s%s title='%s'>%s</a>",
                            $this->_generateFormOnClick($href, $this->_linkData),
                            empty($this->_classString) ? '' : ' '.$this->_classString,
                            empty($this->_attributes)  ? '' : ' '.$this->_attributes,
                            empty($this->_accesskey)   ? '' : ' accesskey=\''.$this->_linkData[$this->_urlVar].'\'',
                            $altText,
                            $linkText
             );
         }
         return '';
     }

 function getPageLinksArray($url = '')
 {
     //legacy setting... the preferred way to set an option now
     //is adding it to the constuctor
     if (!empty($url)) {
         $this->_path = $url;
     }
     
     //If there's only one page, don't display links
     if ($this->_clearIfVoid && ($this->_totalPages < 2)) {
         return '';
     }

     $links = array();
     if ($this->_totalPages > (2 * $this->_delta + 1)) {
         if ($this->_expanded) {
             if (($this->_totalPages - $this->_delta) <= $this->_currentPage) {
                 $expansion_before = $this->_currentPage - ($this->_totalPages - $this->_delta);
             } else {
                 $expansion_before = 0;
             }
             for ($i = $this->_currentPage - $this->_delta - $expansion_before; $expansion_before; $expansion_before--, $i++) {
                 $print_separator_flag = ($i != $this->_currentPage + $this->_delta); // && ($i != $this->_totalPages - 1)
                 
                 $this->range[$i] = false;
                 $this->_linkData[$this->_urlVar] = $i;
                 $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), str_replace('%d', $i, $this->_altPage));
                 // $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), $i);
                        // . $this->_spacesBefore
                        // . ($print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
             }
         }

         $expansion_after = 0;
         for ($i = $this->_currentPage - $this->_delta; ($i <= $this->_currentPage + $this->_delta) && ($i <= $this->_totalPages); $i++) {
             if ($i < 1) {
                 ++$expansion_after;
                 continue;
             }

             // check when to print separator
             $print_separator_flag = (($i != $this->_currentPage + $this->_delta) && ($i != $this->_totalPages));

             if ($i == $this->_currentPage) {
                 $this->range[$i] = true;
                 $links[] = $this->_curPageSpanPre . $i . $this->_curPageSpanPost;
             } else {
                 $this->range[$i] = false;
                 $this->_linkData[$this->_urlVar] = $i;
                 $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), str_replace('%d', $i, $this->_altPage));
                 // $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), $i);
             }
             // $links[] = $this->_spacesBefore
            //        . ($print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
         }

         if ($this->_expanded && $expansion_after) {
             // $links[] = $this->_separator . $this->_spacesAfter;
             for ($i = $this->_currentPage + $this->_delta +1; $expansion_after; $expansion_after--, $i++) {
                 $print_separator_flag = ($expansion_after != 1);
                 $this->range[$i] = false;
                 $this->_linkData[$this->_urlVar] = $i;
                 $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), str_replace('%d', $i, $this->_altPage));
                 // $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), $i);
                   // . $this->_spacesBefore
                   // . ($print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
             }
         }

     } else {
         //if $this->_totalPages <= (2*Delta+1) show them all
         for ($i=1; $i<=$this->_totalPages; $i++) {
             if ($i != $this->_currentPage) {
                 $this->range[$i] = false;
                 $this->_linkData[$this->_urlVar] = $i;
                 // $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), $i);
                 $links[] = $this->_renderLink(str_replace('%d', $i, $this->_altPage), str_replace('%d', $i, $this->_altPage));
             } else {
                 $this->range[$i] = true;
                 $links[] = $this->_curPageSpanPre . $i . $this->_curPageSpanPost;
             }
             // $links[] = $this->_spacesBefore
             //       . (($i != $this->_totalPages) ? $this->_separator.$this->_spacesAfter : '');
         }
     }
     return $links;
 }
}