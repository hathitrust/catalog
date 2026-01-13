<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Solr.php';
require_once 'sys/SolrConnection.php';

/**
 * Dedicated test file for Solr escaping edge cases
 * Tests escape order, production failure cases, and all special characters
 * @covers Solr::lucene_escape_fq
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
    $result = $solr->lucene_escape_fq($input);
    // Should be: \\ (escaped backslash) + \&\& (escaped &&)
    $this->assertEquals('\\\\\\&\\&', $result);
  }

  /**
   * Test all 19 special characters individually
   * @covers Solr::lucene_escape_fq
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
      $this->assertEquals($expected, $solr->lucene_escape_fq($input),
                          "Failed to escape: $input");
    }
  }

  /**
   * Test production failure cases from error logs
   * @covers Solr::lucene_escape_fq
   */
  public function test_production_failure_cases(): void
  {
    $solr = new Solr('', '');

    $cases = [
      // From actual error logs
      ['\\', '\\\\'],
      ['"\\', '\\"\\\\'], // Quote + backslash
      ['C:\\Program Files', 'C\\:\\\\Program Files'],
      ['foo~bar', 'foo\\~bar'],
      ['{!term}', '\\{\\!term\\}'],
    ];

    foreach ($cases as [$input, $expected]) {
      $this->assertEquals($expected, $solr->lucene_escape_fq($input),
                          "Failed production case: $input");
    }
  }

   /**
   * Test that empty string is preserved
   * @covers Solr::lucene_escape_fq
   */
  public function test_empty_string(): void
  {
    $solr = new Solr('', '');
    $this->assertEquals('', $solr->lucene_escape_fq(''));
  }

   /**
   * Test normal text is not modified
   * @covers Solr::lucene_escape_fq
   */
  public function test_normal_text_unchanged(): void
  {
    $solr = new Solr('', '');
    $this->assertEquals('hello world', $solr->lucene_escape_fq('hello world'));
    $this->assertEquals('abc123', $solr->lucene_escape_fq('abc123'));
  }

}
?>