<?php
/* 
 * Please see Oyun Studyosu License file
 */
  require_once(APP_PATH . '/SOS/Service/Environment.php');
  require_once(APP_PATH . '/SOS/Model/Environment.php');
  require_once(APP_PATH . '/SOS/Exception.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  class SOS_Service_EnvironmentImpl implements SOS_Service_Environment
  {

    /**
     * Singleton instance
     * @var SOS_Service_EnvironmentImpl
     */
    protected static $_instance;

    public static function getInstance() {
      if(SOS_Service_EnvironmentImpl::$_instance == null) {
        SOS_Service_EnvironmentImpl::$_instance = new SOS_Service_EnvironmentImpl();
      }
      return SOS_Service_EnvironmentImpl::$_instance;
    }

    /**
     * Private constructor prevents the class being instantiated
     * Use the static getInstance method instead
     */
    private function  __construct() {}

    public function getEnvironment(SOS_Request_Guid $guid) {
      $e = new SOS_Model_Environment();
      $e->setDomain(SOS_Factory::getDomain());
      $e->setFields(array('people', 'mediaItems', 'environment'));
      return $e;
    }
  }
