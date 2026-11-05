<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'sys/Pager.php';

class PagerTest extends TestCase
{
  protected function setUp(): void {
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
  }

  /**
  * @covers Pager::__construct
  * @runInSeparateProcess
  */
  public function test_properties(): void {
    $pager = new Pager(base_url: "example.com", total_records: 1000, records_per_page: 100, current_page: 1);
    $this->assertEquals("example.com", $pager->base_url);
    $this->assertEquals(1000, $pager->total_records);
    $this->assertEquals(100, $pager->records_per_page);
    $this->assertEquals(1, $pager->current_page);
  }

  /**
  * @covers Pager::__construct
  * @runInSeparateProcess
  */
  public function test_properties_with_bogus_values(): void {
    $pager = new Pager(base_url: "example.com", total_records: 1000, records_per_page: -1);
    $this->assertEquals(100, $pager->records_per_page);
    $pager = new Pager(base_url: "example.com", total_records: 1000, records_per_page: 0);
    $this->assertEquals(100, $pager->records_per_page);
    $pager = new Pager(base_url: "example.com", total_records: 1000, records_per_page: 100, current_page: -1);
    $this->assertEquals(1, $pager->current_page);
    $pager = new Pager(base_url: "example.com", total_records: 1000, records_per_page: 100, current_page: 0);
    $this->assertEquals(1, $pager->current_page);
  }

  /**
  * @covers Pager::__construct
  * @runInSeparateProcess
  */
  public function test_default_properties(): void {
    $pager = new Pager(base_url: "example.com", total_records: 1000);
    $this->assertEquals(100, $pager->records_per_page);
    $this->assertEquals(1, $pager->current_page);
  }

  /**
  * @covers Pager::last_page
  * @runInSeparateProcess
  */
  public function test_last_page_with_full_pages(): void {
    $pager = new Pager(base_url: "example.com", total_records: 1000, records_per_page: 100);
    $this->assertEquals(10, $pager->last_page());
  }

  /**
  * @covers Pager::last_page
  * @runInSeparateProcess
  */
  public function test_last_page_with_partial_page(): void {
    $pager = new Pager(base_url: "example.com", total_records: 1050, records_per_page: 100);
    $this->assertEquals(11, $pager->last_page());
  }

  /**
  * @covers Pager::last_page
  * @runInSeparateProcess
  */
  public function test_last_page_with_no_results(): void {
    $pager = new Pager(base_url: "example.com", total_records: 0);
    $this->assertEquals(1, $pager->last_page());
  }

  /**
  * @covers Pager::last_page
  * @runInSeparateProcess
  */
  public function test_last_page_with_no_records_per_page(): void {
    $pager = new Pager(base_url: "example.com", total_records: 1, records_per_page: 0);
    $this->assertEquals(1, $pager->last_page());
  }

  /**
  * @covers Pager::next_url
  * @runInSeparateProcess
  */
  public function test_next_url(): void {
    $_GET['ft'] = '';
    $_GET['lookfor[1]'] = 'journal';
    $_GET['lookfor[2]'] = 'conference';
    $pager = new Pager(base_url: 'catalog?f', total_records: 1000, records_per_page: 100);
    $url = $pager->next_url();
    $this->assertStringContainsString('page=2', $url);
    # URL contains exactly one page parameter
    $this->assertEquals(1, substr_count($url, 'page='));
    # URL contains no explicitly indexed parameters
    $this->assertDoesNotMatchRegularExpression('/%5B\d+%5D/', $url);
    $_GET = [];
  }

  /**
  * @covers Pager::next_url
  * @runInSeparateProcess
  */
  public function test_next_url_at_end(): void {
    $pager = new Pager(base_url: "catalog?", total_records: 1000, records_per_page: 100, current_page: 10);
    $this->assertEquals('', $pager->next_url());
  }

  /**
  * @covers Pager::previous_url
  * @runInSeparateProcess
  */
  public function test_previous_url(): void {
    $_GET['ft'] = '';
    $_GET['lookfor[1]'] = 'journal';
    $_GET['lookfor[2]'] = 'conference';
    $pager = new Pager(base_url: 'catalog?', total_records: 1000, records_per_page: 100, current_page: 10);
    $url = $pager->previous_url();
    $this->assertStringContainsString('page=9', $url);
    # URL contains exactly one page parameter
    $this->assertEquals(1, substr_count($url, 'page='));
    # URL contains no explicitly indexed parameters
    $this->assertDoesNotMatchRegularExpression('/%5B\d+%5D/', $url);
    $_GET = [];
  }

  /**
  * @covers Pager::previous_url
  * @runInSeparateProcess
  */
  public function test_previous_url_at_beginning(): void {
    $pager = new Pager(base_url: 'catalog?', total_records: 1000, records_per_page: 100);
    $this->assertEquals('', $pager->previous_url());
  }
}
