<?php

require_once 'Pager/Sliding.php';

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
             return sprintf('<a  href="%s"%s%s%s%s title="%s">%s</a>',
                            htmlentities($this->_url . $href, ENT_COMPAT, 'UTF-8'),
                            empty($this->_classString) ? '' : ' '.$this->_classString,
                            empty($this->_attributes)  ? '' : ' '.$this->_attributes,

                            empty($this->_accesskey)   ? '' : ' accesskey="'.$this->_linkData[$this->_urlVar].'"',
                            empty($onclick)            ? '' : ' onclick="'.$onclick.'"',
                            $altText,
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
}