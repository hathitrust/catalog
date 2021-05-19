<?php

class Normalize
{

  private static $memoize = array();

  //============================================
  // NORMALIZATION FUNCTIONS
  //============================================


  static function lucene_escape($str) {
    $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
    $replace = '\\\$1';
    return preg_replace($pattern, $replace, $str);
  }


  static function trimlower($str) {
    return trim(strtolower($str));
  }

     // <!-- Simple type to normalize isbn/issn -->
     //  <fieldType name="stdnum" class="solr.TextField" sortMissingLast="true" omitNorms="true" >
     //    <analyzer>
     //      <tokenizer class="solr.KeywordTokenizerFactory"/>
     //      <filter class="solr.LowerCaseFilterFactory"/>
     //      <filter class="solr.TrimFilterFactory"/>
     //      <!--   pattern="^\s*0*([\d\-\.]+[xX]?).*$" replacement="$1"  -->
     //      <!--   pattern="^[\s0\-\.]+([\d\-\.]+[xX]?).*$" replacement="$1" -->
     //      <filter class="solr.PatternReplaceFilterFactory"
     //           pattern="^[\s0\-\.]*([\d\.\-]+x?).*$" replacement="$1"
     //      />
     //      <filter class="solr.PatternReplaceFilterFactory"
     //           pattern="[\-\.]" replacement=""  replace="all"
     //      />
     //    </analyzer>
     //  </fieldType>


  static function stdnum($str, $leaveLeadZeros=false) {
    $str = trim(strtolower($str));
    if ($leaveLeadZeros) {
     $str = preg_replace('/^[\s\-\.]*([\d\.\-]+x?).*$/', '$1', $str);
    } else {
      $str = preg_replace('/^[\s0\-\.]*([\d\.\-]+x?).*$/', '$1', $str);
    }
    return preg_replace('/[\-\.]/', '', $str);

  }

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

  static function exactmatcher($str) {
    return preg_replace('/[^\p{L}\p{N}]/', '', trim(strtolower($str)));
  }

  // <fieldType name="numeric" class="solr.TextField" sortMissingLast="true" omitNorms="true" >
  //   <analyzer>
  //     <tokenizer class="solr.KeywordTokenizerFactory"/> 
  //     <filter class="solr.LowerCaseFilterFactory"/>
  //     <filter class="solr.TrimFilterFactory"/>
  //     <filter class="solr.PatternReplaceFilterFactory"
  //          pattern="[^0-9]*([0-9]+)[^0-9]*" replacement="$1"
  //     />
  //     <filter class="solr.PatternReplaceFilterFactory"
  //          pattern="^0*(.*)" replacement="$1"
  //     />
  //   </analyzer>
  // </fieldType>

  static function numeric($str) {
    $str = preg_replace('/^[^0-9]*?([0-9]+)/', '$1', trim(strtolower($str)));
    return preg_replace('/^0+/', '', $str);
  }


  static function isbnlongify($str) {
    // Ditch any dashes or dots
    $str = preg_replace('/[\-\.]/', '', $str);
    if (!preg_match('/^.*\b(\d{9})[\dXx](?:\Z|\D).*$/', $str, $match)) {
      return $str;
    }
    $longisbn = '978' . $match[1];
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
      $sum += $longisbn[$i] + (2 * $longisbn[$i] * ($i % 2));
    }
    $top = $sum + (10 - ($sum % 10));
    $check = $top - $sum;
    if ($check == 10) {
      return $longisbn . '0';
    } else {
      return $longisbn . $check;
    }

  }

  // Normalization pattern from http://www.loc.gov/marc/lccn-namespace.html#syntax

  static function lccnnormalize($val) {
    // First, ditch the spaces
    $val = preg_replace('/\s/', '', $val);

    // Lose any trailing slash-plus-characters
    if (preg_match('/^(.*?)\//', $val, $match)) {
      $val = $match[1];
    }

    // if there's a hyphen, remove it and right-zero-pad the remaining digits to six chars
    if (preg_match('/^(\w+)-(\d+)/', $val, $match)) {
      $prefix = $match[1];
      $digits = $match[2];
      $digits = sprintf('%06d', $digits);
      return $prefix . $digits;
    } else {
      return $val;
    }
  }



}



?>