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

  /**
    * Helper function to test phrase + fuzzy escaping
    * @covers Solr::escapePhrase
  */
  public function testPhraseWithFuzzyIsPreserved(): void
  {

    $solr = new Solr('', '');

    $this->assertEquals(
        '"machine learning\\"~3"',
        $solr->escapePhrase('machine learning"~3')
    );
  }

  /**
    * Helper function to test phrase + fuzzy escaping
    * @covers Solr::escapePhrase
  */
  public function testPhraseWithCharactersToEscape(): void
  {
    // Applying escapePhrase() to already-quoted input must produce exactly one quoted phrase.
    $solr = new Solr('', '');

    $input = '"nature, and history"';

    $escapedOnce = $solr->escapePhrase($input);
    $escapedTwice = $solr->escapePhrase($escapedOnce);

    $this->assertSame(
        '"nature, and history"',
        $escapedOnce,
        'escapePhrase should preserve a single quoted phrase'
    );

    $this->assertSame(
        $escapedOnce,
        $escapedTwice,
        'escapePhrase must be idempotent'
    );

    $input = '"nature, "and" history \ test"';

    $escapedPhrase = $solr->escapePhrase($input);

    $this->assertSame(
        '"nature, \"and\" history \\\\ test"',
        $escapedPhrase,
        'escapePhrase should preserve a single quoted phrase and escape to avoid syntax error'
    );


  }

  /**
    * Helper function to test boolean operators within phrases preserved
    * @covers Solr::escapeBoolean
  */
  public function testBooleanEscaping(): void
  {
    // Boolean operators must remain unescaped; operands must never introduce Solr syntax errors.
    $solr = new Solr('', '');
    $this->assertEquals(
        'dramatic AND literature',
        $solr->escapeBoolean('dramatic AND literature')
    );

    $input = 'nature, AND and AND history';

    $escaped = $solr->escapeBoolean($input);

    // Ensure no accidental escaping of operators
    $this->assertStringContainsString(' AND ', $escaped);

    $this->assertSame(
        'nature, AND and AND history',
        $escaped,
        'Boolean operators must be preserved and operands safely escaped'
    );

    // Ensure no quote imbalance
    $this->assertSame(
        0,
        substr_count($escaped, '"') % 2,
        'Escaped boolean expression must have balanced quotes'
    );

    // Hard safety check: no raw special chars that break Solr
    $this->assertDoesNotMatchRegularExpression(
        '/(?<!\\\\)[\+\!\(\)\{\}\[\]\^~\*\?:\/]/',
        $escaped,
        'No unescaped Solr special characters allowed in boolean operands'
    );

  }

  /**
  * Helper function to test prefix preserved, not escaped away
    * @covers Solr::escapePrefix
  */
  public function testPrefixQuery() {

    $solr = new Solr('', '');
    $this->assertEquals(
        'machine*',
        $solr->escapePrefix('machine*')
    );
  }

  /**
  * Helper function to test prefix preserved, not escaped away
    * @covers Solr::escapeTerm
  */
  public function testFieldInjectionBlocked() {

    $solr = new Solr('', '');
    $escaped = $solr->escapeTerm('title:evil');
    $this->assertEquals('title\\:evil', $escaped);
  }

}
?>