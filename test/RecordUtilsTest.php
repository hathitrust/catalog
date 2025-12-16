<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'services/Record/RecordUtils.php';
require_once 'sys/HTStatus.php';


$HT_COLLECTIONS =  eval(file_get_contents('__DIR__/../derived_data/ht_collections.php'));
$htstatus = new HTStatus();
$htstatus->fakefill('umich');

class RecordUtilsTest extends TestCase
{
  /**
  * @covers RecordUtils::ht_link_data_from_json
  * @runInSeparateProcess
  */
  public function test_ht_link_data_from_json(): void {
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $sample_json = array(
      'rights' => 'pd',
      'htid' => 'mdp.001',
      'collection_code' => 'miu',
      'enumcron' => 'v.1',
      'heldby' => array('umich')
    );
    $utils = new RecordUtils();
    $data = $utils->ht_link_data_from_json($sample_json);
    // No role and no activated role
    $this->assertEquals(false, $data['has_activated_role']);
    $this->assertEquals(false, $data['is_resource_sharing']);
  }

  /**
  * @covers RecordUtils::ht_link_data_from_json
  * @runInSeparateProcess
  */
  public function test_ht_link_data_from_json_rs(): void {
    global $htstatus;
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $sample_json = array(
      'rights' => 'ic',
      'htid' => 'mdp.001',
      'collection_code' => 'miu',
      'enumcron' => 'v.1',
      'heldby' => array('umich')
    );
    $utils = new RecordUtils();
    $htstatus->r = array('resourceSharing' => 1);
    $data = $utils->ht_link_data_from_json($sample_json);
    // This is a RS scenario
    $this->assertEquals(true, $data['is_resource_sharing']);
  }

  /**
  * @covers RecordUtils::ht_link_data_from_json
  * @runInSeparateProcess
  */
  public function test_ht_link_data_from_json_no_rs_supp(): void {
    global $htstatus;
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $sample_json = array(
      'rights' => 'supp',
      'htid' => 'mdp.001',
      'collection_code' => 'miu',
      'enumcron' => 'v.1',
      'heldby' => array('umich')
    );
    $utils = new RecordUtils();
    $htstatus->r = array('resourceSharing' => 1);
    $data = $utils->ht_link_data_from_json($sample_json);
    // Not RS because the rights are 'supp'
    $this->assertEquals(false, $data['is_resource_sharing']);
  }

  /**
  * @covers RecordUtils::ht_link_data_from_json
  * @runInSeparateProcess
  */
  public function test_ht_link_data_from_json_no_rs_not_held(): void {
    global $htstatus;
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $sample_json = array(
      'rights' => 'ic',
      'htid' => 'mdp.001',
      'collection_code' => 'miu',
      'enumcron' => 'v.1',
      'heldby' => array('yale')
    );
    $utils = new RecordUtils();
    $htstatus->r = array('resourceSharing' => 1);
    $data = $utils->ht_link_data_from_json($sample_json);
    // Not RS because not held by user's institution
    $this->assertEquals(false, $data['is_resource_sharing']);
  }
  
  /**
  * @covers RecordUtils::ht_link_data_from_json
  * @runInSeparateProcess
  */
  public function test_ht_link_data_from_json_no_rs_fullview(): void {
    global $htstatus;
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $sample_json = array(
      'rights' => 'pd',
      'htid' => 'mdp.001',
      'collection_code' => 'miu',
      'enumcron' => 'v.1',
      'heldby' => array('umich')
    );
    $utils = new RecordUtils();
    $htstatus->r = array('resourceSharing' => 1);
    $data = $utils->ht_link_data_from_json($sample_json);
    // Not RS because rights are pd
    $this->assertEquals(false, $data['is_resource_sharing']);
  }

  /**
  * @covers RecordUtils::ht_link_data_from_json
  * @runInSeparateProcess
  */
  public function test_ht_link_data_from_json_activated_role(): void {
    global $htstatus;
    # Setup for VFSession.php and DSession.php
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $sample_json = array(
      'rights' => 'ic',
      'htid' => 'mdp.001',
      'collection_code' => 'miu',
      'enumcron' => 'v.1',
      'heldby' => array('umich')
    );
    $utils = new RecordUtils();
    $htstatus->r = array('resourceSharing' => 1);
    $htstatus->has_activated_role = true;
    $data = $utils->ht_link_data_from_json($sample_json);
    $this->assertEquals(true, $data['has_activated_role']);
    $this->assertEquals('resourceSharing', $data['role_name']);
  }

  /**
  * @covers RecordUtils::is_fullview
  */
  public function test_is_fullview(): void {
    $utils = new RecordUtils();
    $examples = [
      // Rights     fv us?   fv non-us?
      ['pd',        true,    true],
      ['ic',        false,   false],
      ['op',        false,   false],
      ['orph',      false,   false],
      ['und',       false,   false],
      // umall is obsolete, test is included to demonstrate behavior
      ['umall',     false,   false],
      ['ic-world',  true,    true],
      ['nobody',    false,   false],
      ['pdus',      true,    false],
      ['cc-by-3.0', true,    true],
      // skip the rest of the CC 3.0 rights...
      ['orphcand',  false,   false],
      ['cc-zero',   true,    true],
      ['und-world', true,    true],
      ['icus',      false,   true],
      ['cc-by-4.0', true,    true],
      // skip the rest of the CC 4.0 rights...
      ['pd-pvt',    false,   false],
      ['supp',      false,   false],
      // Oddball cases
      ['???',                false,   false],
      [['newly_open', 'pd'], true,    true],
      [['pd', 'ic'],         true,    true]
    ];
    foreach ($examples as $example) {
      $this->assertEquals($example[1], $utils->is_fullview($example[0], true));
      $this->assertEquals($example[2], $utils->is_fullview($example[0], false));
    }
  }

  /**
  * @covers RecordUtils::is_open_to_no_one
  */
  public function test_is_open_to_no_one(): void {
    $utils = new RecordUtils();
    $examples = [
      // Rights           open to no one?
      ['pd',              false],
      ['ic',              false],
      ['pd-pvt',          true],
      ['supp',            true],
      ['nobody',          true],
      [['pd', 'pd-pvt'],  true],
      [['pd', 'supp'],    true],
      [['pd', 'nobody'],  true]
    ];
    foreach ($examples as $example) {
      $this->assertEquals($example[1], $utils->is_open_to_no_one($example[0]));
    }
  }
}

?>
