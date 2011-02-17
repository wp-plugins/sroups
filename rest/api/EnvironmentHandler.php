<?php
/* 
 * Please see oyunstudyosu license file
 */

/**
 * Description of EnvironmentHandler
 *
 * @author suleymanmelikoglu [at] oyunstudyosu.com
 */
class EnvironmentHandler implements SOS_Service_Environment {
    
    public function getEnvironment(SOS_Request_Guid $guid) {
      $data = new SOS_Model_Environment_Data();
      $data->setContainerName("My Container");
      return $data;
    }

    public function supportsField($field) {
      $fields = array('people', 'mediaItems');
    }

    public function getDomain() {
      return "example.com";
    }

    public function getSupportedFields() {
      return array('people', 'mediaItems');
    }
}

