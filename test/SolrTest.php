<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Solr.php';
require_once 'sys/SolrConnection.php';

class SolrTest extends TestCase
{
  /**
  * @covers Solr::exactmatcherify
  Trim whitespace
  Downcase
  Remove anything not a Unicode letter, Unicode number, * or ?
  */
  public function test_exactmatcherify(): void
  {
    $solr = new Solr('', '');
    $this->assertEquals('', $solr->exactmatcherify(''));
    $this->assertEquals('abc', $solr->exactmatcherify("\t\t\n ABC\r"));
    $this->assertEquals('twowords', $solr->exactmatcherify('two words'));
    $this->assertEquals('*?', $solr->exactmatcherify('!@#$%^&*()-=_+,.<>/?'));
    $this->assertEquals('日本', $solr->exactmatcherify('日本'));
  }
  /**
    * @covers Solr::quoteFilterValue
  */
  public function test_quoteFilterValue_escapes_internal_quotes(): void
   {
      $solr = new Solr('', '');

      // Test normal value
      $result = $solr->quoteFilterValue('Smith, John');
      $this->assertEquals('"Smith, John"', $result);

      // Test value with quotes
      $result = $solr->quoteFilterValue('"Kao gu yu wen wu" bian ji bu');
      $this->assertEquals('"\\"Kao gu yu wen wu\\" bian ji bu"', $result);

      // Test value with backslash
      $result = $solr->quoteFilterValue('\Kao gu yu wen wu bian ji bu');
      $this->assertEquals('"\\\\Kao gu yu wen wu bian ji bu"', $result);

      // Test date range (should not be quoted)
      $result = $solr->quoteFilterValue('[1900 TO 2000]');
      $this->assertEquals('[1900 TO 2000]', $result);
   }

  /**
    * @covers Solr::quoteFilterValue
  */
  public function testUnwrapQuotedWildcard(): void
   {
    $solr = new Solr('', '');

    $this->assertSame('table*', $solr->unwrapQuotedWildcard('"table"*'));
    $this->assertSame('machine learning*', $solr->unwrapQuotedWildcard('"machine learning"*'));
    $this->assertNull($solr->unwrapQuotedWildcard('"table"'));
    $this->assertNull($solr->unwrapQuotedWildcard('table*'));
    $this->assertNull($solr->unwrapQuotedWildcard('"*"'));
 }
}

?>
