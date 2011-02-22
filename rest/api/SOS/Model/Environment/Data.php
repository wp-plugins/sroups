<?php
/* 
 * Please see Sroups license file
 */

/**
 * Description of Data
 *
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
