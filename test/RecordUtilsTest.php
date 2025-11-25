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
}

?>
