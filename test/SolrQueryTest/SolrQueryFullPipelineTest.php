<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'services/Search/SearchStructure.php';
require_once 'sys/Solr.php';

$configArray = parse_ini_file('conf/config.ini', true);
$configArray['Site']['local'] = '/app';

class SolrQueryFullPipelineTest extends TestCase
{
  /**
  * @runInSeparateProcess
  */
  public function test_queryFullPipeline(): void
  {
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';

    // Verify backslash is preserved (not removed or double-escaped)
    $_REQUEST['lookfor'] = ['\\'];
    $_REQUEST['type'] = ['title'];
    $ss = new SearchStructure;

    // Check search field structure
    $this->assertNotEmpty($ss->search);
    $this->assertEquals('title', $ss->search[0][0]);
    $this->assertEquals("\\", $ss->search[0][1]);

    // Check cleaned_up_original_search
    $this->assertNotEmpty($ss->cleaned_up_original_search);
    $this->assertEquals('title', $ss->cleaned_up_original_search[0][0]);
    $this->assertEquals('\\', $ss->cleaned_up_original_search[0][1]);

    // Verify backslash is preserved (not removed or double-escaped)
    $this->assertStringContainsString('\\', $ss->search[0][1]);

    // ---------------test_backslash_through_full_pipeline---------------

    // 1. Start with raw input
    $rawInput = '\\';
    // fwrite(STDOUT, "1. Raw input:     '$rawInput' (len=" . strlen($rawInput) . ")\n");

    // 2. Through SearchStructure
    $afterSS = $ss->search[0][1];
    // fwrite(STDOUT, "2. After SearchStructure: '$afterSS' (len=" . strlen($afterSS) . ")\n");

    // 3. Through Solr (dismax)

    $solr = new Solr('', '');
    $args = $solr->dismaxSearchArguments($ss);
    // args[0][0] is q

    // As \ is an invalid query, Solr should convert it to *:*
    $this->assertEquals('q', $args[0][0], "Solr args field should be 'q'");
    $this->assertEquals('*:*', $args[0][1], "Solr args query should be '*:*' for invalid input");

    $this->assertEquals('smart', $ss->fix_unbalanced_quotes('“smart')); // fancy quote are fixed in SearchStructure:

    $_REQUEST['lookfor'] = ['“smart']; //
    $ss = new SearchStructure;
    // Check that unbalanced fancy quote is fixed
    $this->assertEquals('smart', $ss->search[0][1], "Unbalanced fancy quote are fixed in SearchStructure");

    // dismaxSearchArguments receives the input query with unbalanced fancy quote
    $solr = new Solr('', '');
    $args = $solr->dismaxSearchArguments($ss);

    $this->assertStringContainsString('smart', $args[0][1], "Solr.php receives fixed query without fancy quotes");
    // In $args[0][1] is expected the Solr query
    // Required clauses (order-independent)
    // Test example considering title query
    $expectedClauses = [
            'title_ab:(smart)^25000', // exactmatcher
            'title_a:(smart)^15000', // exactmatcher
            'titleProper:(smart*)^8000', // emstartswith

            'titleProper:(smart)^1200', // onephrase field

            'title_topProper:(smart)^600', // onephrase field

            'series2:(smart)^500' // onephrase field
        ];

    foreach ($expectedClauses as $clause) {
            $this->assertStringContainsString(
                $clause,
                $args[0][1],
                "Missing expected Solr clause: {$clause}\n\nActual query:\n{$args[0][1]}"
            );
        }
  }

}

?>