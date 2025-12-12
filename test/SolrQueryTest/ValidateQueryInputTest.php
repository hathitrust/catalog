<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Solr.php';
require_once 'sys/SolrConnection.php';

final class ValidateQueryInputTest extends TestCase
{
    private $solr;

    protected function setUp(): void
    {
        // Instantiate the class
        $this->solr = new Solr('', '');
    }

    /* ============================================================
     * remove_wildcards_add_beginning()
     * ============================================================
     */

    /**
    * @covers Solr::remove_first_character
    */
    public function testRemovesLeadingWildcard(): void
    {
        $this->assertSame(
            'table',
            $this->solr->remove_first_character('*table')
        );

        $this->assertSame(
            'table',
            $this->solr->remove_first_character('?table')
        );
    }

    /**
    * @covers Solr::remove_first_character
    */
    public function testRemoveWildcardBlindlyRemovesFirstChar(): void
    {
        // This documents current behavior (important!)
        $this->assertSame(
            'able',
            $this->solr->remove_first_character('table')
        );
    }

    /* ============================================================
     * remove_unbalanced_parentheses()
     * ============================================================
     */

    /**
    * @covers Solr::remove_parentheses
    */
     public function testRemovesAllParentheses(): void
    {
        $this->assertSame(
            'table test',
            $this->solr->remove_parentheses('(table) (test)')
        );
    }

    /**
    * @covers Solr::remove_parentheses
    */
    public function testRemovesNestedParentheses(): void
    {
        $this->assertSame(
            'table test',
            $this->solr->remove_parentheses('((table) test)')
        );
    }

    /* ============================================================
     * remove_invalid_caret_usage()
     * ============================================================
    */

    /**
    * @covers Solr::remove_invalid_caret_usage
    */
    public function testRemovesAllCarets(): void
    {
        $this->assertSame(
            'table2',
            $this->solr->remove_invalid_caret_usage('table^2')
        );

        $this->assertSame(
            'tableabc',
            $this->solr->remove_invalid_caret_usage('table^abc')
        );
    }

    /* ============================================================
     * unwrapQuotedWildcard()
     * ============================================================
    */

    /**
    * @covers Solr::unwrapQuotedWildcard
    */
    public function testUnwrapQuotedWildcard(): void
    {
        $this->assertSame(
            'table*',
            $this->solr->unwrapQuotedWildcard('"table"*')
        );
    }

    /**
    * @covers Solr::unwrapQuotedWildcard
    */
    public function testUnwrapQuotedWildcardWithWhitespace(): void
    {
        $this->assertSame(
            'table*',
            $this->solr->unwrapQuotedWildcard('  "table"*  ')
        );
    }

    /**
    * @covers Solr::unwrapQuotedWildcard
    */
    public function testUnwrapQuotedWildcardReturnsNullForInvalid(): void
    {
        $this->assertNull(
            $this->solr->unwrapQuotedWildcard('"table"')
        );

        $this->assertNull(
            $this->solr->unwrapQuotedWildcard('table*')
        );

        $this->assertNull(
            $this->solr->unwrapQuotedWildcard('"table*"')
        );
    }

    /* ============================================================
     * validateInput()
     * ============================================================
    */

    /**
    * @covers Solr::validateInput
    */

    /**
    * @covers Solr::validateInput
    */
    public function testRejectsEmptyInput(): void
    {
        $result = $this->solr->validateInput('   ');

        $this->assertFalse($result['valid']);
        $this->assertSame('Empty query', $result['error']);
    }

    /**
    * @covers Solr::validateInput
    */
    public function testRejectsGarbageOnlyInput(): void
    {
        $result = $this->solr->validateInput('~~~///');

        $this->assertFalse($result['valid']);
        $this->assertSame('Invalid garbage-only query', $result['error']);
    }

    /**
    * @covers Solr::validateInput
    */
    public function testRejectsInvalidSingleCharacter(): void
    {
        $this->assertFalse(
            $this->solr->validateInput('~')['valid']
        );

        $this->assertFalse(
            $this->solr->validateInput('\\')['valid']
        );
    }

    /**
    * @covers Solr::validateInput
    */
    public function testRejectsLeadingWildcard(): void
    {
        $result = $this->solr->validateInput('*table');

        $this->assertFalse($result['valid']);
        $this->assertSame('Leading wildcard not allowed', $result['error']);
    }

    /**
    * @covers Solr::validateInput
    */
    public function testRejectsUnbalancedParentheses(): void
    {
        $result = $this->solr->validateInput('(table');

        $this->assertFalse($result['valid']);
        $this->assertSame('Unbalanced parentheses', $result['error']);
    }

    /**
    * @covers Solr::validateInput
    */
    public function testRejectsInvalidBoostSyntax(): void
    {
        $this->assertFalse(
            $this->solr->validateInput('table^')['valid']
        );

        $this->assertFalse(
            $this->solr->validateInput('table^abc')['valid']
        );
    }

    /**
    * @covers Solr::validateInput
    */
    public function testAcceptsValidBoostSyntax(): void
    {
        $this->assertTrue(
            $this->solr->validateInput('table^2')['valid']
        );

        $this->assertTrue(
            $this->solr->validateInput('table^1.5')['valid']
        );
    }

    /**
    * @covers Solr::validateInput
    */
    public function testAcceptsValidQueries(): void
    {
        $validInputs = [
            'table',
            'table test',
            'table AND test',
            '(table test)',
            'title:"data science"',
            'table~2',
            'table~0.5',
            '"data science"~2'
        ];

        foreach ($validInputs as $input) {
            $this->assertTrue(
                $this->solr->validateInput($input)['valid'],
                "Expected valid input: {$input}"
            );
        }
    }

    /**
    * @covers Solr::validateInput
    */
    public function testRejectsInvalidFuzzyTerms(): void
    {
        $invalid = [
            'table~~2',
            'table~abc',
            'table~2~',
            '"table"~abc',
            'table^2^3',
            'table~'
        ];

        foreach ($invalid as $input) {
            $result = $this->solr->validateInput($input);
            $this->assertFalse(
                $result['valid'],
                "Invalid fuzzy syntax not allowed: {$input}"
            );
        }
    }

    /** ============================================================
    * Fielded query validation (field:value)
    * ============================================================
    * This test is to check if the query input is valid.
    * The Catalog search does not support this field syntax queries
    * but as this query input is valid, we accept it. In general, the result of this query is empty or unexpected

    * @covers Solr::validateInput
    */
    public function testAcceptsValidFieldedQueries(): void
    {
    $valid = [
        'title:table',
        'author:smith',
        'title:"data science"',
        'title:table~2',
        'title:"machine learning"~3',
        'title:table^2',
        'title:table AND author:smith',
    ];

    foreach ($valid as $input) {
        $this->assertTrue(
            $this->solr->validateInput($input)['valid'],
            "Expected valid fielded query: {$input}"
        );
    }
    }

    /**
    * This test is to check if the query input is valid.
    * The Catalog search does not support this field syntax queries
    * When the query input is invalid a set of heuristics are apply to transform
    * the invalid query into a valid one.
    * @covers Solr::validateInput
    */
    public function testRejectsInvalidFieldedQueries(): void
    {
        $invalid = [
            'title:',
            ':table',
            'title::table',
            'title:table^',
            'title:table^abc',
            'title:~2',
            'title:(table',
            'title:table)',
            'title:"table',
            'title:"table"~',
            'title:table~abc',
            'title:(table AND)'
        ];

        foreach ($invalid as $input) {
            $result = $this->solr->validateInput($input);
            $this->assertFalse(
                $result['valid'],
                "Expected invalid fielded query: {$input}"
            );
        }
    }



}
?>