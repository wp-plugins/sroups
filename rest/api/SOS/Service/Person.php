<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  interface SOS_Service_Person
  {
    /**
     *
     * @param SOS_Request_Guid $guid
     * @return SOS_Model_Person
     */
    public function getPerson(SOS_Request_Guid $guid);
  }
