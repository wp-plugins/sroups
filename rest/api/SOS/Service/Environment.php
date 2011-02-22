<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  interface SOS_Service_Environment
  {
    /**
     * returns the container name
     * @param SOS_Request_Guid $guid
     * @return SOS_Model_Environment
     */
    public function getEnvironment(SOS_Request_Guid $guid);

    /**
     * @return array
     */
    public function supportsField($field);

    /**
     * returns the domain name ex: orkut.com
     * @return String
     */
    public function getDomain();

    public function getSupportedFields();
    
  }
