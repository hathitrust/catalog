<?php

require_once 'vendor/geoip/geoip2.phar';
use GeoIp2\Database\Reader;

class GeoIP {
  # Returns a two-character code.
  # United States is "US"
  function ip_to_iso($ip) {
    $reader = new Reader('/htapps/babel/geoip/GeoIP2-Country.mmdb');
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