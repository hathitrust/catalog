<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Solr.php';
require_once 'sys/SolrConnection.php';


class BuildAndOrOnePhraseTest extends TestCase
{
    private $solr;

    /**
    * @covers Solr::build_and_or_onephrase
    * All these test is to ensure single-character operator-only query input is blocked
    * fuzzy and wildcard queries still work
    * Return False if we want to let the user know that the input query change
    */

    protected function setUp(): void
    {
        // Instantiate the class
        $this->solr = new Solr('', '');
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * ~ --> reject, then return False
    */
    public function testRejectsSingleTilde()
    {
        $this->assertFalse(
            $this->solr->build_and_or_onephrase('~'),
            'Single tilde should be rejected'
        );
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * \\ --> reject, then return False
    */
    public function testRejectsSingleBackslash()
    {
        $this->assertFalse(
            $this->solr->build_and_or_onephrase('\\'),
            'Single backslash should be rejected'
        );
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * table~2 --> that is a fuzzy search, then create the query
    * table~2 is a phrase.
    */
    public function testAllowsFuzzyTerm()
    {
        $result = $this->solr->build_and_or_onephrase('table~2');

        $this->assertIsArray($result);
        $this->assertEquals('table~2', $result['onephrase']);
        $this->assertEquals('table~2', $result['asis']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * "table chair"~2 --> that is a PhraseQuery with slop, not a FuzzyQuery
    * Match documents where "table" and "chair" appear within 2 positions of each other.
    * "table chair" is a phrase.
    */
    public function testAllowsQuotedFuzzyPhrase()
    {
        $result = $this->solr->build_and_or_onephrase('"table chair"~2');
        $this->assertIsArray($result);
        $this->assertEquals('"table chair"~2', $result['onephrase']);
        $this->assertEquals('"table chair"~2', $result['asis']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * "table" --> accepted one word query, then create the query
    */
    public function testAllowsNormalTerm()
    {
        $result = $this->solr->build_and_or_onephrase('table');

        $this->assertIsArray($result);
        $this->assertEquals('table', $result['onephrase']);
        $this->assertEquals('table', $result['asis']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * * --> accepted wildcard search, then create the query
    */
    public function testAllowsWildcardTerm()
    {
        $result = $this->solr->build_and_or_onephrase('table*');

        $this->assertIsArray($result);
        $this->assertEquals('table*', $result['onephrase']);
        $this->assertEquals('table*', $result['asis']);
        $this->assertEquals('table*', $result['emstartswith']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    */
    public function testMultipleValidationErrorsAreHandledIteratively(): void
    {

    // Two errors:
    // 1) Unbalanced parentheses
    // 2) Dangling boost operator (^)
    $input = '(title:(war^peace'; // Query builder will treats it as a literar string, title\:warpeace.

    $result = $this->solr->build_and_or_onephrase($input);

    $this->assertIsArray($result);

    // Parentheses should be removed
    $this->assertStringNotContainsString('(', $result['asis']);
    $this->assertStringNotContainsString(')', $result['asis']);

    // Invalid boost should be removed
    $this->assertStringNotContainsString('^', $result['asis']);

    // Core terms must survive
    $this->assertStringContainsString('war', $result['asis']);
    $this->assertStringContainsString('peace', $result['asis']);

    // Final result must now be valid
    $validation = $this->solr->validateInput($result['asis']);
    $this->assertTrue($validation['valid'], 'Final query should be valid after iterative fixes');
    }

}
?>