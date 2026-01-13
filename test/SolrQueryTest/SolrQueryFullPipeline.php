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
    fwrite(STDOUT, "1. Raw input:     '$rawInput' (len=" . strlen($rawInput) . ")\n");

    // 2. Through SearchStructure
    $afterSS = $ss->search[0][1];
    fwrite(STDOUT, "2. After SearchStructure: '$afterSS' (len=" . strlen($afterSS) . ")\n");

    // 3. Through Solr (dismax)

    $solr = new Solr('', '');
    // (titleProper:(*)^8000 OR titleProper:("\")^1200 OR titleProper:(\)^120 OR title_topProper:("\")^600 OR title_topProper:(\)^60 OR title_restProper:("\")^400 OR title_restProper:(\)^40 OR series:("\")^500 OR series:(\)^50 OR series2:("\")^500 OR series2:(\)^50 OR title:(\)^30 OR title_top:(\)^20 OR title_rest:(\)^1))
    $args = $solr->dismaxSearchArguments($ss);
    // args[0][0] is q
    fwrite(STDOUT, "Solr args field: " . print_r($args[0][0], true) . "\n");
    fwrite(STDOUT, "*******************************: \n");
    // args[0][1] is the query string
    fwrite(STDOUT, "Solr args: " . print_r($args[0][1], true) . "\n");
    $afterSolr = $args[0][1];
    fwrite(STDOUT, "3. After Solr dismax: '$afterSolr' (len=" . strlen($afterSolr) . ")\n");

    // 4. Check each stage
    $this->assertEquals(1, strlen($rawInput), "Raw input should be 1 char");
    $this->assertEquals(1, strlen($afterSS), "After SS should still be 1 char");

    // The Solr query might legitimately escape it - that's what we're testing
    fwrite(STDOUT, "4. Hex dump of Solr query: " . bin2hex($afterSolr) . "\n");

  }
   // TODO Ask Moses how to add covers to Home.php?????
  /**
   * Testing that serialization to and from cookie preserves backslashes correctly
   * @runInSeparateProcess
   */
  public function test_cookie_serialization_escaping(): void
  {
    // $original contains one character, PHP source code uses escaping to represent it.
    // After parsing the runtime value is \
    $original = "\\";

    // Simulate what happens with cookie
    // serialization shows 'a:1:{s:7:"lookfor";s:1:"\";}'
    $serialized = serialize(['lookfor' => $original]);
    $unserialized = unserialize($serialized);

    fwrite(STDOUT, "Original: '$original' (len=" . strlen($original) . ")\n");
    fwrite(STDOUT, "Serialized: '$serialized'\n");
    fwrite(STDOUT, "Unserialized: '{$unserialized['lookfor']}' (len=" . strlen($unserialized['lookfor']) . ")\n");

    // Cookie serialization is not altering the value at all
    $this->assertEquals($original, $unserialized['lookfor'],
                       "Cookie serialization should not change the value");
  }


}

?>
