<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Solr.php';
require_once 'sys/SolrConnection.php';

/**
 * Dedicated test file for Solr escaping edge cases
 * Tests escape order, production failure cases, and all special characters
 * @covers Solr::lucene_escape
 */
class SolrEscapingTest extends TestCase
{
  /**
   * Test escaping order: backslash first, then multi-char, then single-char
   * This is CRITICAL - wrong order causes double-escaping bugs
   */
  public function test_escape_order(): void
  {
    $solr = new Solr('', '');
    // If we have "&&" and it contains "\", backslash must be escaped first
    $input = '\\&&';
    $result = $solr->lucene_escape($input);
    // Should be: \\ (escaped backslash) + \&\& (escaped &&)
    $this->assertEquals('\\\\\\&\\&', $result);
  }

  /**
   * Test all 19 special characters individually
   * @covers Solr::lucene_escape
   */
  public function test_all_special_chars(): void
  {
    $solr = new Solr('', '');

    $specials = [
      ['\\', '\\\\'],
      ['&&', '\\&\\&'],
      ['||', '\\|\\|'],
      ['+', '\\+'],
      ['-', '\\-'],
      ['!', '\\!'],
      ['(', '\\('],
      [')', '\\)'],
      ['{', '\\{'],
      ['}', '\\}'],
      ['[', '\\['],
      [']', '\\]'],
      ['^', '\\^'],
      ['"', '\\"'],
      ['~', '\\~'],
      ['*', '\\*'],
      ['?', '\\?'],
      [':', '\\:'],
      ['/', '\\/']
    ];

    foreach ($specials as [$input, $expected]) {
      $this->assertEquals($expected, $solr->lucene_escape($input),
                          "Failed to escape: $input");
    }
  }
}
?>