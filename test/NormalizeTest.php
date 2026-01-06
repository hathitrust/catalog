<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Normalize.php';

class NormalizeTest extends TestCase
{
  /**
  * @covers Normalize::lucene_escape
  */
  public function test_lucene_escape(): void
  {
    $norm = new Normalize('', '');
    $this->assertEquals('', $norm->lucene_escape(''));
    // Escapes the following characters and sequences with a backslash
    // + - && || ! ( ) { } [ ] ^ " ~ * ? : \
    // Supposedly Lucene 4 has / as a regex delimiter but I'm not sure if we
    // need to support that. The current implementation makes no such accommodation.
    $this->assertEquals(
      '\+ \- \&& \|| \! \( \) \{ \} \[ \] \^ \" \~ \* \? \:',
      $norm->lucene_escape('+ - && || ! ( ) { } [ ] ^ " ~ * ? :')
    );
    // Single backslash, have to escape it but it is one character '/'.
    // The expected output is two characters: '\\'.
    $this->assertEquals('\\\\', $norm->lucene_escape('\\'));
  }

  /**
  * @covers Normalize::trimlower
  */
  public function test_trimlower(): void
  {
    $norm = new Normalize('', '');
    // Trim, downcase
    $this->assertEquals('abc', $norm->trimlower('  ABC  '));
  }

  /**
  * @covers Normalize::stdnum
  */
  public function test_stdnum(): void
  {
    $norm = new Normalize('', '');
    // Trim, downcase
    // Strip leading space, dash, period
    // Leave first maximal sequence of digits, dash, period.
    // Then strip out the non-digits from the sequence
    $this->assertEquals('123456789', $norm->stdnum('  -.123.456-789  '));
    // Removing leading zeroes (the default)
    $this->assertEquals('123456789', $norm->stdnum('  -.000123.456-789  '));
    // With `leaveLeadZeros` flag
    $this->assertEquals('000123456789', $norm->stdnum('  -.000123.456-789  ', true));
  }

  /**
  * @covers Normalize::exactmatcher
  */
  public function test_exactmatcher(): void
  {
    $norm = new Normalize('', '');
    // Trim, downcase, and throw out anything non-alphanumeric
    $this->assertEquals('1a', $norm->exactmatcher('  !@#$1-A&*>  '));
  }

  /**
  * @covers Normalize::numeric
  */
  public function test_numeric(): void
  {
    $norm = new Normalize('', '');
    // Remove maximum non-numeric prefix
    $this->assertEquals('12345abcde', $norm->numeric('a12345abcde'));
    $this->assertEquals('12345abcde', $norm->numeric('a $!&#$12345abcde'));
    // Remove leading zeroes
    $this->assertEquals('12345abcde', $norm->numeric('a000012345abcde'));
    // Downcase
    $this->assertEquals('12345abcde', $norm->numeric('A12345ABCDE'));
    // Trim input
    $this->assertEquals('12345abcde', $norm->numeric('  a12345abcde  '));
  }

  /**
  * @covers Normalize::isbnlongify
  */
  public function test_isbnlongify(): void
  {
    $norm = new Normalize('', '');
    // Normal cases
    $this->assertEquals('9780060210298', $norm->isbnlongify('006021029X'));
    $this->assertEquals('9780060210298', $norm->isbnlongify('0-06-021029-x'));
    // With 10 as ISBN-13 check digit
    $this->assertEquals('9783631304310', $norm->isbnlongify('3631304315'));

    // Already ISBN-13
    $this->assertEquals('9780060210298', $norm->isbnlongify('9780060210298'));

    // Edge cases
    $this->assertEquals('not an ISBN', $norm->isbnlongify('not an ISBN'));
    $this->assertEquals('', $norm->isbnlongify(''));
  }

  /**
  * @covers Normalize::lccnnormalize
  */
  public function test_lccnnormalize(): void
  {
    $norm = new Normalize('', '');
    // Examples from https://www.loc.gov/marc/lccn-namespace.html#syntax
    $this->assertEquals('n78890351', $norm->lccnnormalize('n78-890351'));
    $this->assertEquals('n78089035', $norm->lccnnormalize('n78-89035'));
    $this->assertEquals('n78890351', $norm->lccnnormalize('n 78890351 '));
    $this->assertEquals('85000002', $norm->lccnnormalize(' 85000002 '));
    $this->assertEquals('85000002', $norm->lccnnormalize('85-2 s'));
    $this->assertEquals('2001000002', $norm->lccnnormalize('2001-000002'));
    $this->assertEquals('75425165', $norm->lccnnormalize('75-425165//r75'));
    $this->assertEquals('79139101', $norm->lccnnormalize(' 79139101 /AC/r932'));
  }

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
