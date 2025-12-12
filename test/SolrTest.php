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
   }
}

?>
