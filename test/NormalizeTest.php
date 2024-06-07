<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Normalize.php';

class NormalizeTest extends TestCase
{
  /**
  * @covers Normalize::normalize_issn
  */
  public function test_normalize_issn(): void
  {
    $norm = new Normalize('', '');
    $this->assertEquals('1234-5678', $norm->normalize_issn('12345678'));
    $this->assertEquals('1234-5678', $norm->normalize_issn('  1234 5678  '));
    $this->assertEquals('1234-5678', $norm->normalize_issn('qwerty  1234!@#$%^&*()_+=5678  uiop'));
    $this->assertEquals('1234567899999', $norm->normalize_issn('1234567899999'));
    $this->assertEquals('1234-567x', $norm->normalize_issn('1234567X'));
    $this->assertEquals('1234-567x', $norm->normalize_issn('1234567x'));
    $this->assertEquals('0000-0001', $norm->normalize_issn('1'));
    $this->assertEquals('0000-0000', $norm->normalize_issn(''));
  }
}

?>
