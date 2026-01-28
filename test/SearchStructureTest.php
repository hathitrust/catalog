<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'services/Search/SearchStructure.php';

$configArray = parse_ini_file('conf/config.ini', true);
$configArray['Site']['local'] = '/app';

class SearchStructureTest extends TestCase
{
  /**
  * @covers SearchStructure::searchtermsForDisplay
  * @runInSeparateProcess
  */
  public function test_searchtermsForDisplay(): void
  {
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $_REQUEST['lookfor'] = ['journal'];
    $ss = new SearchStructure;
    $this->assertEquals(['All Fields: <span class="query-term">journal</span>'], $ss->searchtermsForDisplay());
    $_REQUEST['lookfor'] = ['<b>'];
    $ss = new SearchStructure;
    $this->assertEquals(['All Fields: <span class="query-term">&lt;b&gt;</span>'], $ss->searchtermsForDisplay());

    # Check the function fix_unbalanced_quotes
    $this->assertEquals('norfolk', $ss->fix_unbalanced_quotes('norfolk')); // 0 quotes, balanced:
    $this->assertEquals('norfolk', $ss->fix_unbalanced_quotes('"norfolk')); // 1 quote, unbalanced:
    $this->assertEquals('"norfolk"', $ss->fix_unbalanced_quotes('"norfolk"')); // 2 quotes, balanced:
    $this->assertEquals('"norfolk"', $ss->fix_unbalanced_quotes('"norfolk""')); // 3 quotes, unbalanced:

   }
}

?>
