<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/GeoIP.php';

class GeoIPTest extends TestCase {
  /**
  * @covers GeoIP::ip_to_iso
  Trim whitespace
  Downcase
  Remove anything not a Unicode letter, Unicode number, * or ?
  */
  public function test_ip_to_iso(): void {
    $geoip = new GeoIP;
    # 216.160.83.56 is in the test DB as US
    # TODO: find an example in the test DB outside the US?
    $this->assertEquals('US', $geoip->ip_to_iso('216.160.83.56'));
    $this->assertEquals('XX', $geoip->ip_to_iso('no such ip'));
  }
}

?>
