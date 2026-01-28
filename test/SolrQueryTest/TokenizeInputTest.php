<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Solr.php';
require_once 'sys/SolrConnection.php';

final class TokenizeInputTest extends TestCase
{
    private $solr;

    protected function setUp(): void
    {
        // Instantiate the class
        $this->solr = new Solr('', '');
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizesSimpleWords()
    {
        $tokens = $this->solr->tokenizeInput('table chair');

        $this->assertSame(
            ['table', 'chair'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizesQuotedPhrase()
    {
        $tokens = $this->solr->tokenizeInput('"table chair"');

        $this->assertSame(
            ['"table chair"'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizesFuzzyPhrase()
    {
        $tokens = $this->solr->tokenizeInput('"table chair"~2');

        $this->assertSame(
            ['"table chair"~2'],
            $tokens,
            'Fuzzy phrase must be a single token'
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizesUnquotedFuzzyTerm()
    {
        $tokens = $this->solr->tokenizeInput('table~2');

        $this->assertSame(
            ['table~2'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizesMixedInput()
    {
        $tokens = $this->solr->tokenizeInput('table "chair leg"~3 desk');

        $this->assertSame(
            ['table', '"chair leg"~3', 'desk'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizesBooleanOperators()
    {
        $tokens = $this->solr->tokenizeInput('table AND "chair leg"~2');

        $this->assertSame(
            ['table AND "chair leg"~2'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizesMultipleFuzzyPhrases()
    {
        $tokens = $this->solr->tokenizeInput('"table chair"~2 "wood table"~1');

        $this->assertSame(
            ['"table chair"~2', '"wood table"~1'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testDoesNotSplitQuotedPhraseWithSpaces()
    {
        $tokens = $this->solr->tokenizeInput('"a b c"~4');

        $this->assertSame(
            ['"a b c"~4'],
            $tokens
        );
    }
}
?>