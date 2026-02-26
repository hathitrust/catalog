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
        'machine learning\\"~3',
        $solr->escapePhrase('machine learning"~3')
    );
  }

  /**
    * Helper function to test phrase + fuzzy escaping
    * @covers Solr::escapePhrase
  */
  public function testPhraseWithCharactersToEscape(): void
  {
    // Applying escapePhrase() to already-quoted input must produce one unquoted phrase.
    // The function that build the query will add the quotes back when needed, 
    // so we should not end up with double quotes or unbalanced quotes.
    $solr = new Solr('', '');

    $input = '"nature, and history"';

    $escapedOnce = $solr->escapePhrase($input);
    $escapedTwice = $solr->escapePhrase($escapedOnce);

    $this->assertSame(
        'nature, and history',
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
        'nature, \"and\" history \\\\ test',
        $escapedPhrase,
        'escapePhrase should preserve a single quoted phrase and escape to avoid syntax error'
    );


  }

  /**
    * Helper function to test boolean operators within phrases preserved
    * @covers Solr::escapePhrase
  */
  public function testBooleanEscaping(): void
  {
    // Boolean operators must remain unescaped; operands must never introduce Solr syntax errors.
    $solr = new Solr('', '');

    $input = 'dramatic AND literature';

    $this->assertEquals(
        $input,
        $solr->escapePhrase('dramatic AND literature')
    );

    $escaped = $solr->escapePhrase($input);

    // Ensure no accidental escaping of operators
    $this->assertStringContainsString(' AND ', $escaped);

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
    * @covers Solr::escapeTerm
  */
  public function testFieldInjectionBlocked() {

    $solr = new Solr('', '');
    $escaped = $solr->escapeTerm('title:evil');
    $this->assertEquals('title\\:evil', $escaped);
  }

  /**
    * @covers Solr::buildEscapedParts
  */
  public function testBuildEscapedPartsEdgeCases(): void
  {
    $solr = new Solr('', '');

    // buildEscapedParts is a private method, so we need to use reflection to test it directly. 
    $this->assertSame([], $this->invokeBuildEscapedParts($solr, []));

    $tokens = [
      ['type' => 'unknown', 'value' => 'ignored'], // Should be ignored since type is unknown
      ['type' => 'operator', 'value' => 'not'],
      ['type' => 'term_fuzzy', 'value' => '~2'],
    ];

    $this->assertSame(['NOT', '~2'], $this->invokeBuildEscapedParts($solr, $tokens));
  }

  /**
    * @covers Solr::buildEscapedParts
  */
  public function testBuildEscapedPartsSpecialCharacterCombinations(): void
  {
    $solr = new Solr('', '');

    $tokens = [
      ['type' => 'term', 'value' => 'C++'],
      ['type' => 'operator', 'value' => 'and'],
      ['type' => 'term_fuzzy', 'value' => 'http://exa-mple.com~4'],
      ['type' => 'operator', 'value' => 'or'],
      ['type' => 'term_wildcard', 'value' => 'title:hist*ry?'],
      ['type' => 'phrase', 'value' => ['text' => 'he said "hello" \\ goodbye', 'slop' => null]],
      ['type' => 'phrase_slop', 'value' => ['text' => 'climate "change" \\ policy', 'slop' => 2]],
    ];

    $expected = [
      'C\\+\\+', // + must be escaped in term
      'AND', // Operator should be uppercased, not escaped
      'http\\:\\/\\/exa\\-mple.com~4', // : and / must be escaped in term, ~ is preserved for fuzzy
      'OR', // Operator should be uppercased, not escaped
      'title\\:hist*ry?', // : must be escaped in term, * and ? are preserved for wildcard
      '"he said \\"hello\\" \\\\ goodbye"', // Quotes inside phrase must be escaped, backslash must be escaped, whole thing is quoted
      '"climate \\"change\\" \\\\ policy"~2', // Quotes inside phrase must be escaped, backslash must be escaped, whole thing is quoted, ~2 is preserved for slop
    ];

    $this->assertSame($expected, $this->invokeBuildEscapedParts($solr, $tokens));
  }

  /**
    * @covers Solr::buildEscapedParts
  */
  public function testBuildEscapedPartsRealWorldQueryExample(): void
  {
    $solr = new Solr('', '');
    $query = '"machine learning"~3 AND C++ OR title:history/* NOT library?';

    $tokens = $solr->classifyTokens($solr->tokenizeInput($query));
    $escapedParts = $this->invokeBuildEscapedParts($solr, $tokens);

    $this->assertSame(
      [
        '"machine learning"~3',
        'AND',
        'C\\+\\+',
        'OR',
        'title\\:history\\/*',
        'NOT',
        'library?',
      ],
      $escapedParts
    );
  }

  /**
    * @covers Solr::escapeTermKeepWildcardOperators
    * Preserve * and ? for wildcard queries, but escape other special characters
  */
  public function testEscapeTermKeepWildcardOperatorsPreservesWildcards(): void
  {
    $solr = new Solr('', '');
  
    $this->assertSame(
      'title\\:hist*ry?',
      $solr->escapeTermKeepWildcardOperators('title:hist*ry?')
    );
    $this->assertSame(
      'a\\+b*c?',
      $solr->escapeTermKeepWildcardOperators('a+b*c?')
    );
  }

  /**
    * @covers Solr::escapeTermKeepWildcardOperators
      * Ensure that all special characters except * and ? are escaped, even when they appear together
      *  This is important to prevent syntax errors in wildcard queries that also contain other special characters 
  */
  public function testEscapeTermKeepWildcardOperatorsEscapesOtherSpecialChars(): void
  {
    $solr = new Solr('', '');

    $this->assertSame(
      'C\\:\\\\path\\\\file*?.txt',
      $solr->escapeTermKeepWildcardOperators('C:\\path\\file*?.txt')
    );
    $this->assertSame(
      'a\\&\\&b\\|\\|c\\!*?\\~\\/d',
      $solr->escapeTermKeepWildcardOperators('a&&b||c!*?~/d')
    );
  }

  /**
    * @covers Solr::flattenTokens
  */
  public function testFlattenTokensMixedTokenTypes(): void
  {
    $solr = new Solr('', '');

    $tokens = [
      ['type' => 'phrase_slop', 'value' => ['text' => 'machine learning', 'slop' => 3]],
      ['type' => 'operator', 'value' => 'AND'],
      ['type' => 'term', 'value' => 'C++'],
      ['type' => 'operator', 'value' => 'or'],
      ['type' => 'phrase', 'value' => ['text' => 'climate change', 'slop' => null]],
      ['type' => 'term_wildcard', 'value' => 'title:hist*ry?'],
      ['type' => 'term_fuzzy', 'value' => 'library~2'],
    ];

    $this->assertSame(
      '"machine learning"~3 AND C++ or "climate change" title:hist*ry? library~2',
      $this->invokeFlattenTokens($solr, $tokens)
    );
  }

  /**
    * @covers Solr::flattenTokens
  */
  public function testFlattenTokensEmptyInput(): void
  {
    $solr = new Solr('', '');

    $this->assertSame('', $this->invokeFlattenTokens($solr, []));
  }

  private function invokeBuildEscapedParts(Solr $solr, array $tokens): array
  { 
    // Use reflection to access the private method buildEscapedParts
    $reflection = new ReflectionClass($solr);
    $method = $reflection->getMethod('buildEscapedParts');

    return $method->invoke($solr, $tokens);
  }

  private function invokeFlattenTokens(Solr $solr, array $tokens): string
  {
    $reflection = new ReflectionClass($solr);
    $method = $reflection->getMethod('flattenTokens');

    return $method->invoke($solr, $tokens);
  }

}
?>
