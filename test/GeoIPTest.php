<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/GeoIP.php';

class GeoIPTest extends TestCase {
  /**
  * @covers GeoIP
  */
  public function test_ip_to_iso(): void {
    $configArray = parse_ini_file('conf/config.ini', true);
    $geoip = new GeoIP($configArray['GeoIP']['path']);
    # 216.160.83.56 is in the test DB as US
    # TODO: find an example in the test DB outside the US?
    $this->assertEquals('US', $geoip->ip_to_iso('216.160.83.56'));
    $this->assertEquals('XX', $geoip->ip_to_iso('no such ip'));
  }
}

?>
