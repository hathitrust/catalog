<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Solr.php';
require_once 'sys/SolrConnection.php';

final class IsPhraseTest extends TestCase
{
    private $solr;

    protected function setUp(): void
    {
        // Instantiate the class
        $this->solr = new Solr('', '');
    }

    /**
    * @covers Solr::classifyTokens - two terms
    */
    public function testClassifySimpleWords()
    {
        $tokens = $this->solr->classifyTokens(['table', 'chair']);

        $this->assertSame(
            [
                ['type' => 'term', 'value' => 'table'],
                ['type' => 'term', 'value' => 'chair']
            ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens 
    */
    public function testClassifyQuotedPhrase()
    {   
        $tokens = $this->solr->classifyTokens(['"table chair"']);
        $this->assertSame(
            [
                ['type' => 'phrase', 'value' => ['text' => 'table chair', 'slop' => null]]
            ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens 
    */
    public function testClassifyFuzzyPhrase()
    {
        $tokens = $this->solr->classifyTokens(['"table chair"~2']);

        $this->assertSame(
            [
                ['type' => 'phrase_slop', 'value' => ['text' => 'table chair', 'slop' => '2']]
            ],
            $tokens,
            'Fuzzy phrase must be a single token'
        );
    }

    /**
    * @covers Solr::classifyTokens 
    */
    public function testClassifyUnquotedFuzzyTerm()
    {
        $tokens = $this->solr->classifyTokens(['table~2']);

        $this->assertSame(
            [
                ['type' => 'term_fuzzy', 'value' => 'table~2']
                ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens 
    */
    public function testClassifyMixedInput()
    {
        $tokens = $this->solr->classifyTokens(['table', '"chair leg"~3', 'desk']);

        $this->assertSame(
            [
                ['type' => 'term', 'value' => 'table'],
                ['type' => 'phrase_slop', 'value' => ['text' => 'chair leg', 'slop' => '3']],
                ['type' => 'term', 'value' => 'desk']
            ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens - boolean operators are classified as separate operator tokens
    */
    public function testClassifyBooleanOperators()
    {
        $tokens = $this->solr->classifyTokens(['table', 'AND', '"chair leg"~2']);

        $this->assertSame(
            [
                ['type' => 'term', 'value' => 'table'],
                ['type' => 'operator', 'value' => 'AND'],
                ['type' => 'phrase_slop', 'value' => ['text' => 'chair leg', 'slop' => '2']]
            ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens - multiple fuzzy phrases should be classified correctly
    */
    public function testClassifyMultipleFuzzyPhrases()
    {
        $tokens = $this->solr->classifyTokens(['"table chair"~2', '"wood table"~1']);

        $this->assertSame(
            [
                ['type' => 'phrase_slop', 'value' => ['text' => 'table chair', 'slop' => '2']],
                ['type' => 'phrase_slop', 'value' => ['text' => 'wood table', 'slop' => '1']]
            ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens - unquoted wildcard term with spaces should be classified as separate terms (wildcard operator will keep with the last token)
    */
    public function testClassifyUnquotedWildcardsPhraseWithSpaces()
    {
        $tokens = $this->solr->classifyTokens(['a', 'b', 'c*']);
        $this->assertSame(
            [
                ['type' => 'term', 'value' => 'a'],
                ['type' => 'term', 'value' => 'b'],
                ['type' => 'term_wildcard', 'value' => 'c*']
            ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens 
    */
    public function testClassifyRemovesLowercaseBooleanWords()
    {
        $tokens = $this->solr->classifyTokens(['poetry', 'nature']);
        $this->assertSame(
            [
                ['type' => 'term', 'value' => 'poetry'],
                ['type' => 'term', 'value' => 'nature']
            ],
            $tokens
        );
    }

    /**
    * @covers Solr::classifyTokens - boolean operators are preserved in phrases but split when standalone
    */
    public function testClassifyDoesNotSplitBooleanPhrase()
    {
        $tokens = $this->solr->classifyTokens(['"poetry AND nature"']);
        $this->assertSame(
            [
                ['type' => 'phrase', 'value' => ['text' => 'poetry AND nature', 'slop' => null]]
            ],
            $tokens
        );

        $tokens = $this->solr->classifyTokens(['poetry', 'AND', 'nature']);


        $this->assertSame(
            [
                ['type' => 'term', 'value' => 'poetry'],
                ['type' => 'operator', 'value' => 'AND'],
                ['type' => 'term', 'value' => 'nature']
            ],
            $tokens
        );
    
    }


}
?>
