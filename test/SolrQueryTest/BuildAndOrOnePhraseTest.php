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
    public function testRejectsSingleTilde(): void
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
    public function testRejectsSingleBackslash(): void
    {
        $this->assertFalse(
            $this->solr->build_and_or_onephrase('\\'),
            'Single backslash should be rejected'
        );
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * table~2 --> accepted fuzzy search, then create the query
    */
    public function testAllowsFuzzyTerm(): void
    {
        $result = $this->solr->build_and_or_onephrase('table~2');

        $this->assertIsArray($result);
        $this->assertEquals('"table~2"', $result['onephrase']);
        $this->assertEquals('table~2', $result['asis']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * "table"~2 --> accepted fuzzy search, then create the query
    */
    public function testAllowsQuotedFuzzyPhrase(): void
    {
        $result = $this->solr->build_and_or_onephrase('"table"~2');

        $this->assertIsArray($result);
        $this->assertEquals('"table~2"', $result['onephrase']);
        $this->assertEquals('"table"~2', $result['asis']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * "table" --> accepted one word query, then create the query
    */
    public function testAllowsNormalTerm(): void
    {
        $result = $this->solr->build_and_or_onephrase('table');

        $this->assertIsArray($result);
        $this->assertEquals('"table"', $result['onephrase']);
        $this->assertEquals('table', $result['asis']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    * * --> accepted wildcard search, then create the query
    */
    public function testAllowsWildcardTerm(): void
    {
        $result = $this->solr->build_and_or_onephrase('table*');

        $this->assertIsArray($result);
        $this->assertEquals('"table*"', $result['onephrase']);
        $this->assertEquals('table*', $result['asis']);
    }

    /* ============================================================
     * unwrapQuotedWildcard()
     * ============================================================
    */

    /**
    * @covers Solr::build_and_or_onephrase
    */
    public function testUnwrapQuotedWildcard(): void
    {
        #$this->assertSame(
        #    'table*',
        #    $this->solr->unwrapQuotedWildcard('"table"*')
        #);

        $result = $this->solr->build_and_or_onephrase('"table"*');

        $this->assertIsArray($result);
        $this->assertEquals('"table*"', $result['onephrase']);
        $this->assertEquals('table*', $result['asis']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    */
    public function testUnwrapQuotedWildcardWithWhitespace(): void
    {
        #$this->assertSame(
        #    'table*',
        #    $this->solr->unwrapQuotedWildcard('  "table"*  ')
        #);
        $result = $this->solr->build_and_or_onephrase('  "table"*  ');

        $this->assertIsArray($result);
        $this->assertEquals('"table*"', $result['onephrase']);
        $this->assertEquals('table*', $result['asis']);
    }

    /* ============================================================
     * validateInput()
     * ============================================================
    */

    /**
    * @covers Solr::build_and_or_onephrase
    */
    public function testRejectsEmptyInput(): void
    {

        $result = $this->solr->build_and_or_onephrase('   ');

        $this->assertIsArray($result);
        $this->assertEquals('""', $result['onephrase']);
        $this->assertEquals('', $result['asis']);

        #$result = $this->solr->build_and_or_onephrase('   ');

        #$this->assertFalse($result['valid']);
        #$this->assertSame('Empty query', $result['error']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    */
    public function testRejectsGarbageOnlyInput(): void
    {

        // TODO: Add the rule of removing trailing garbage only characters
        #$this->assertFalse(
        #    $this->solr->build_and_or_onephrase('~~~///'),
        #    'Invalid garbage-only query should be rejected'
        #);

        $result = $this->solr->build_and_or_onephrase('~~~///');

        $this->assertIsArray($result);
        $this->assertEquals('"~~~"', $result['onephrase']);
        $this->assertEquals('~~~', $result['asis']);
        $this->assertEquals('', $result['exactmatcher']);

        #$result = $this->solr->validateInput('~~~///');

        #$this->assertFalse($result['valid']);
        #$this->assertSame('Invalid garbage-only query', $result['error']);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    */
    public function testRejectsInvalidSingleCharacter(): void
    {
        $this->assertFalse(
            $this->solr->build_and_or_onephrase('~'),
            'Invalid single-character query'
        );

        $this->assertFalse(
            $this->solr->build_and_or_onephrase('\\'),
            'Invalid single-character query'
        );

        #$this->assertFalse(
        #    $this->solr->validateInput('~')['valid']
        #);

        #$this->assertFalse(
        #    $this->solr->validateInput('\\')['valid']
        #);
    }

    /**
    * @covers Solr::build_and_or_onephrase
    */
    // TODO: Fix fuzzy term validation to reject invalid fuzzy terms

    public function testRejectsInvalidFuzzyTerms(): void
    {
        $invalid = [
            '~2',
            'table~~2',
            'table~abc',
            'table~2~',
            '"table"~abc',
        ];

        foreach ($invalid as $input) {
            $this->assertFalse(
            $this->solr->build_and_or_onephrase($input),
            'Invalid single-character query'
        );
            #$result = $this->solr->validateInput($input);
            #$this->assertFalse(
            #    $result['valid'],
            #    "Expected invalid fuzzy query: {$input}"
            #);
        }
    }


}
?>
