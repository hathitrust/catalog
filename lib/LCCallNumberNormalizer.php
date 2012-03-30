<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LCCallNumberNormalizer
 *
 * @author dueberb
 */
class LCCallNumberNormalizer {

    protected static $instance = null;

    protected static $MINNUM = 2;

    protected static $JOIN = "";
    protected static $TOPALPHA = "@@@@";
    protected static $TOPSPACES = "@@@@@@@@@@";
    protected static $TOPSPACE = "@";
    protected static $TOPDIGIT = "0";
    protected static $TOPDIGITS = "0000000000000000";
    protected static $BOTTOMSPACES = "~~~~~~~~~~~~~~";
    protected static $BOTTOMSPACE = "~";
    protected static $BOTTOMDIGIT = "9";
    protected static $BOTTOMDIGITS = "999999999999999999999";
    protected static $lcpattern =
      "/^ \\s*
      (?:VIDEO-D)? (?:DVD-ROM)? (?:CD-ROM)? (?:TAPE-C)? \\s*
      ([A-Z]{1,3}) \\s*
      (?: (\\d{1,4}) (?:\\s*?\\.\\s*?(\\d{1,3}))? )? \\s*
      (?: \\.? \\s* ([A-Z]) \\s* (\\d{1,3} | \\Z)? )? \\s*
      (?: \\.? \\s* ([A-Z]) \\s* (\\d{1,3} | \\Z)? )? \\s*
      (?: \\.? \\s* ([A-Z]) \\s* (\\d{1,3} | \\Z)? )? (\\s\\s*.+?)? \\s*$/ix";

    protected function __construct() {

    }

    public static function singleton() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    // Does it match?

    function match($str) {
      if (preg_match(self::$lcpattern, $str)) {
        return true;
      } else {
        return false;
      }
    }

    function normalize($str, $nomatch="_INPUT_") {
      if ($nomatch == "_INPUT_") {
        $nomatch = $str;
      }
      try {
        return $this->_normalize($str);
      } catch (InvalidArgumentException $e) {
        return $nomatch;
      }
    }

    function normalizeFullLength($str, $nomatch="_INPUT_") {
      if ($nomatch == "_INPUT_") {
        $nomatch = $str;
      }
      try {
        return $this->_normalize($str,false, true);
      } catch (InvalidArgumentException $e) {
        return $nomatch;
      }

    }

    function rangeStart($str) {
        return $this->normalize($str);
    }

    function rangeEnd($str, $nomatch="_INPUT_") {
      if ($nomatch == "_INPUT_") {
        $nomatch = $str;
      }
      try {
        return $this->normalize($str) . $BOTTOMSPACE;
      } catch (InvalidArgumentException $e) {
        return $nomatch;
      }
    }

    function rangeEndPadded($str, $nomatch="_INPUT_") {
      if ($nomatch == "_INPUT_") {
        $nomatch = $str;
      }
      try {
        return $this->_normalize($str, true, true);
      } catch (InvalidArgumentException $e) {
        return $nomatch;
      }
    }


    function _normalize($str, $rangeEnd=false, $padded=false) {
      $str = strtoupper($str);
      $str = trim($str);
      preg_match(self::$lcpattern, $str, $m);
      if (!$m) {
        throw new InvalidArgumentException("Doesn't match");
      }
      
      $alpha   = isset($m[1]) && preg_match('/\S/', $m[1])? $m[1] : null;
      $num     = isset($m[2]) && preg_match('/\S/', $m[2])? $m[2] : null;
      $dec     = isset($m[3]) && preg_match('/\S/', $m[3])? $m[3] : null;
      $c1alpha = isset($m[4]) && preg_match('/\S/', $m[4])? $m[4] : null;
      $c1num   = isset($m[5]) && preg_match('/\S/', $m[5])? $m[5] : null;
      $c2alpha = isset($m[6]) && preg_match('/\S/', $m[6])? $m[6] : null;
      $c2num   = isset($m[7]) && preg_match('/\S/', $m[7])? $m[7] : null;
      $c3alpha = isset($m[8]) && preg_match('/\S/', $m[8])? $m[8] : null;
      $c3num   = isset($m[9]) && preg_match('/\S/', $m[9])? $m[9] : null;
      $extra   = isset($m[10]) && preg_match('/\S/', $m[10])? $m[10] : null;



      // If we don't have at least an alpha and a num, throw it out
      if (!isset($alpha) || !isset($num) || !preg_match('/\S/', $num)) {
        throw new InvalidArgumentException("Need leading alpha and number");
      }

      // Keep the originals around
      $origs = array_slice($m, 1);

      //We have some records that aren't LoC Call Numbers, but start like them,
      //only with more than three digits in the decimal. Ditch them

      if (isset($dec) && strlen($dec) > 3) {
        throw new InvalidArgumentException("No more than 3 decimal places");
      }

      // Create a normalized version of the "extra" with a leading space

      $enorm = isset($extra)? $extra : "";
      $enorm = preg_replace('/[^A-Z0-9]/', '', $enorm);
      if (strlen($enorm) > 0) {
        $enorm = " " . $enorm;
      }

      // Pad the number out to four digits

      $orignum = $num;
      $bottomnum = $num;
      if (!isset($bottomnum)) {
        $bottomnum = substr(self::$BOTTOMDIGITS, 0, 4);
      } else {
        $bottomnum = sprintf("%04d", $bottomnum);
      }
      $num = !isset($num) || $num == "" ? substr(self::$TOPDIGITS, 0, 4) : sprintf("%04d", $num);
      $topnorm = array();

      $topnorm[] = $alpha . substr(self::$TOPALPHA, 0, 3 - strlen($alpha));
      $topnorm[] = $num;
      $topnorm[] = !isset($dec)? substr(self::$TOPDIGITS, 0, 3) : $dec . substr(self::$TOPDIGITS, 0, 3 - strlen($dec));
      $topnorm[] = !isset($c1alpha)? self::$TOPSPACE : $c1alpha;
      $topnorm[] = !isset($c1num)? substr(self::$TOPDIGITS, 0, 3) : $c1num . substr(self::$TOPDIGITS, 0, 3 - strlen($c1num));
      $topnorm[] = !isset($c2alpha)? self::$TOPSPACE : $c2alpha;
      $topnorm[] = !isset($c2num)? substr(self::$TOPDIGITS, 0, 3) : $c2num . substr(self::$TOPDIGITS, 0, 3 - strlen($c2num));
      $topnorm[] = !isset($c3alpha)? self::$TOPSPACE : $c3alpha;
      $topnorm[] = !isset($c3num)? substr(self::$TOPDIGITS, 0, 3) : $c3num . substr(self::$TOPDIGITS, 0, 3 - strlen($c3num));
      $topnorm[] = $enorm;

      // If we want a normalized, padded top, just return it
      // If it's already full length, start==end, padded==unpadded
      // just return it

      if (($padded && !$rangeEnd) || isset($extra)) {
        $rv = implode(self::$JOIN, $topnorm);
//        echo "Returning $rv: padded/rangeEnd are $padded and $rangeEnd\n";
        return $rv;
      }

      // Now for the bottom of the range

      $bottomnorm = array();
      $bottomnorm[] = $alpha . substr(self::$TOPALPHA, 0, 3 - strlen($alpha));
      $bottomnorm[] = $bottomnum;
      $bottomnorm[] = !isset($dec)? substr(self::$BOTTOMDIGITS, 0, 3) : $dec . substr(self::$BOTTOMDIGITS, 0, 3 - strlen($dec));
      $bottomnorm[] = !isset($c1alpha)? self::$BOTTOMSPACE : $c1alpha;
      $bottomnorm[] = !isset($c1num)? substr(self::$BOTTOMDIGITS, 0, 3) : $c1num . substr(self::$BOTTOMDIGITS, 0, 3 - strlen($c1num));
      $bottomnorm[] = !isset($c2alpha)? self::$BOTTOMSPACE : $c2alpha;
      $bottomnorm[] = !isset($c2num)? substr(self::$BOTTOMDIGITS, 0, 3) : $c2num . substr(self::$BOTTOMDIGITS, 0, 3 - strlen($c2num));
      $bottomnorm[] = !isset($c3alpha)? self::$BOTTOMSPACE : $c3alpha;
      $bottomnorm[] = !isset($c3num)? substr(self::$BOTTOMDIGITS, 0, 3) : $c3num . substr(self::$BOTTOMDIGITS, 0, 3 - strlen($c3num));
      $bottomnorm[] = $enorm;

      // Remove 'extra'
      array_pop($topnorm);
      if ($rangeEnd) {
        array_pop($bottomnorm);
      }

      for ($i = 8; $i >=1; $i--) {
        $end = array_pop($topnorm);
        if (isset($origs[$i])) {
          if ($rangeEnd) {
            $end = implode(self::$JOIN, array_slice($bottomnorm, $i));
          } elseif ($i > 1) {
            $end = $origs[$i];
          }
          $rv = implode(self::$JOIN, $topnorm) . self::$JOIN . $end;
          return $rv;
        }
      }
      return "Something went horribly wrong";
    }



}
?>
