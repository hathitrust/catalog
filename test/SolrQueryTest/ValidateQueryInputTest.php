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
    * @covers Solr::validateInput
    */
    public function testRemovesLeadingWildcard(): void
    {
        $this->assertSame(
            'table',
            $this->solr->validateInput('*table')
        );

        $this->assertSame(
            'table',
            $this->solr->validateInput('?table')
        );
    }

    /* ============================================================
     * remove_unbalanced_parentheses()
     * ============================================================
     */

    /**
    * @covers Solr::validateInput
    */
     public function testAllParentheses(): void
    {

        $this->assertSame(
            '(table) (test)',
            $this->solr->validateInput('(table) (test)')
        );
    }

    /**
    * @covers Solr::validateInput
    */
    public function testNestedParentheses(): void
    {

        $this->assertSame(
            '((table) test)',
            $this->solr->validateInput('((table) test)')
        );
    }

    /**
    * @covers Solr::validateInput
    */
    public function testRemovingUnbalanceParentheses(): void
    {

        $this->assertSame(
            'table test',
            $this->solr->validateInput('((table test)')
        );
    }


    /* ============================================================
     * remove_invalid_caret_usage()
     * ============================================================
    */

    /**
    * @covers Solr::validateInput
    */
    public function testRemovesAllCarets(): void
    {
        $this->assertSame(
            'table^2',
            $this->solr->validateInput('table^2')
        );

        $this->assertSame(
            'tableabc',
            $this->solr->validateInput('table^abc')
        );

        $this->assertSame(
            'table^1.5',
            $this->solr->validateInput('table^1.5')
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
            '"data science"~2',
            'table~',
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
    // TODO: Fix fuzzy term validation to reject invalid fuzzy terms
    /**
    *public function testRejectsInvalidFuzzyTerms(): void
    *{
    *    $invalid = [
    *        '~2',
    *        'table~~2',
    *        'table~abc',
    *        'table~2~',
    *        '"table"~abc',
    *    ];

    *    foreach ($invalid as $input) {
    *        $result = $this->solr->validateInput($input);
    *        $this->assertFalse(
    *            $result['valid'],
    *            "Expected invalid fuzzy query: {$input}"
    *        );
    *    }
    *}
    */
    /* ============================================================
    * Fielded query validation (field:value)
    * ============================================================
    */

    /**
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
    * @covers Solr::validateInput
    */
    // TODO: Improve fielded query validation to catch more errors
    /**
    *public function testRejectsInvalidFieldedQueries(): void
    *{
    *    $invalid = [
    *        'title:',
    *        ':table',
    *        'title::table',
    *        'title:table^',
    *        'title:table^abc',
    *        'title:~2',
    *        'title:(table',
    *        'title:table)',
    *        'title:"table',
    *        'title:"table"~',
    *        'title:table~abc',
    *        'title:(table AND)'
    *    ];

    *    foreach ($invalid as $input) {
    *        $result = $this->solr->validateInput($input);
    *        $this->assertFalse(
    *            $result['valid'],
    *            "Expected invalid fielded query: {$input}"
    *        );
    *    }
    *}
    */


}
?>
