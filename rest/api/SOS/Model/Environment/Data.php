<?php
/* 
 * Please see oyunstudyosu license file
 */

/**
 * Description of Data
 *
 * @author suleymanmelikoglu [at] oyunstudyosu.com
 */
class SOS_Model_Environment_Data {

  /**
   * container name
   * @var String
   */
  public $name;

  public function  __construct($containerName = "") {
      $this->name = $containerName;
  }

  public function setContainerName($containerName) {
    $this->name = $containerName;
  }

  public function getContainerName() {
    return $this->name;
  }

}
