<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Service/Person.php');

  class SOS_Service_PersonImpl implements SOS_Service_Person
  {
    /**
     * Delegator pattern
     * @var SOS_Service_Person
     */
    protected $_delegator = null;

    /**
     * Singleton instance
     * @var SOS_Service_PersonImpl
     */
    protected static $_instance = null;

    /**
     * Singleton instance
     * @return SOS_Service_PersonImpl
     */
    public static function getInstance() {
      if(SOS_Service_PersonImpl::$_instance == null) {
        SOS_Service_PersonImpl::$_instance = new SOS_Service_PersonImpl();
      }
      return SOS_Service_PersonImpl::$_instance;
    }

    /**
     * Private constructor prevents the class from being instantiated
     * Only getInstance method should be used for the instantiating the object
     */
    private function  __construct() {}

    public function getDelegator() {
      if($this->_delegator == null) {
        throw new SOS_Exception("Missing delegator class");
      }
      return $this->_delegator;
    }

    public function setDelegator(SOS_Service_Person $delegator) {
      $this->_delegator = $delegator;
      return $this;
    }

    public function getPerson(SOS_Request_Guid $guid) {
      return $this->getDelegator()->getPerson($guid);
    }
  }
