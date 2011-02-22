<?php
/* 
 * Please see Sroups License file
 */

  require_once 'PHPUnit/Framework.php';

  class DataServiceTest extends PHPUnit_Framework_TestCase {

    public function testDataServiceShouldExtendAbstract() {
      $ds = new SOS_Protocol_DataService();
      $this->assertTrue($ds instanceof SOS_Protocol_Abstract);
    }

    public function testBaseUrl() {
      $tBaseUrl = "/sroups.sos"; // @todo fix this
      $ds = new SOS_Protocol_DataService();
      //$this->assertEquals($tBaseUrl, $ds->getBaseUrl());
    }

  }
