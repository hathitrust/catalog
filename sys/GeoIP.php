<?php

require_once 'vendor/geoip/geoip2.phar';
use GeoIp2\Database\Reader;

class GeoIP {
  public static $geoip_file;

  function __construct($geoip_file) {
    self::$geoip_file = $geoip_file;
  }

  # Returns a two-character code.
  # United States is "US"
  function ip_to_iso($ip) {
    $reader = new Reader(self::$geoip_file);
    try {
      $record = $reader->country($ip);
      $country = $record->country->isoCode;
    } catch (Exception $e) {
      $country = 'XX';
    }
    $reader->close();
    return $country;
  }
}

?>