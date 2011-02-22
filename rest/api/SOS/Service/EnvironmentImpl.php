<?php
/* 
 * Please see Sroups License file
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
      return $this->getDelegator()->getEnvironment($guid);
    }

    /**
     *
     * @return SOS_Service_MediaItem
     */
    public function getDelegator() {
      if($this->_delegator == null) {
        throw new SOS_Exception("Missing delegator class");
      }
      return $this->_delegator;
    }

    /**
     * Sets the delegator
     * @return SOS_Service_MediaItem
     */
    public function setDelegator(SOS_Service_Environment $delegator) {
      $this->_delegator = $delegator;
      return $this;
    }

    /**
     *
     * @param String $field
     * @return boolean
     */
    public function supportsField($field) {
      return $this->getDelegator()->supportsField($field);
    }

    /**
     * returns the domain name
     * @return String
     */
    public function getDomain() {
      return $this->getDelegator()->getDomain();
    }

    public function  getSupportedFields() {
      return $this->getDelegator()->getSupportedFields();
    }
    
  }
