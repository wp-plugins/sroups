<?php
/* 
 * Please see Oyun Studyosu License file
 */
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  interface SOS_Service_Environment
  {
    /**
     *
     * @param SOS_Request_Guid $guid
     * @return SOS_Model_Person
     */
    public function getEnvironment(SOS_Request_Guid $guid);
  }
