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
    * @covers Solr::tokenizeInput - two terms
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
    * @covers Solr::tokenizeInput - quoted phrase
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
    * @covers Solr::tokenizeInput - fuzzy phrase
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
    * @covers Solr::tokenizeInput - unquoted fuzzy term = 1 term/token
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
    * @covers Solr::tokenizeInput - mixed input with quoted fuzzy phrase = 3 terms/tokens
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
    * @covers Solr::tokenizeInput - mixed input with boolean operator keeps operator as a standalone token
    */
    public function testTokenizesBooleanOperators()
    {
        $tokens = $this->solr->tokenizeInput('table AND "chair leg"~2');

        $this->assertSame(
            ['table', 'AND', '"chair leg"~2'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput - multiple quoted fuzzy phrases = 2 phrases
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
    * @covers Solr::tokenizeInput - unquoted wildcard term with spaces = 3 terms/tokens (wildcard operator will keep with the last token)
    */
    public function testSplitUnquotedWildcardsPhraseWithSpaces()
    {
        $tokens = $this->solr->tokenizeInput('a b c*');

        $this->assertSame(
            ['a', 'b', 'c*'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizerRemovesLowercaseBooleanWords()
    {
        $tokens = $this->solr->tokenizeInput('poetry and nature');

        $this->assertSame(
            ['poetry', 'nature'],
            $tokens
        );
    }

    /**
    * @covers Solr::tokenizeInput
    */
    public function testTokenizerDoesNotSplitBooleanPhrase()
    {
        $tokens = $this->solr->tokenizeInput('"poetry AND nature"');

        $this->assertSame(
            ['"poetry AND nature"'],
            $tokens
        );

        $tokens = $this->solr->tokenizeInput('poetry AND nature');

        $this->assertSame(
            ['poetry', 'AND', 'nature'],
            $tokens
        );
    
    }


}
?>
