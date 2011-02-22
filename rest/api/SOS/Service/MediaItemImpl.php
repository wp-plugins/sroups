<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Service/MediaItem.php');
  require_once(APP_PATH . '/SOS/Exception.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  class SOS_Service_MediaItemImpl implements SOS_Service_MediaItem
  {
    /**
     * Delegator pattern
     * @var SOS_Service_MediaItem
     */
    protected $_delegator;

    /**
     * Singleton instance
     * @var SOS_Service_MediaItemImpl
     */
    protected static $_instance;

    public static function getInstance() {
      if(SOS_Service_MediaItemImpl::$_instance == null) {
        SOS_Service_MediaItemImpl::$_instance = new SOS_Service_MediaItemImpl();
      }
      return SOS_Service_MediaItemImpl::$_instance;
    }

    /**
     * Private constructor prevents the class being instantiated
     * Use the static getInstance method instead
     */
    private function  __construct() {}

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
    public function setDelegator(SOS_Service_MediaItem $delegator) {
      $this->_delegator = $delegator;
      return $this;
    }

    public function getMediaItems(SOS_Request_Guid $guid) {
      return $this->getDelegator()->getMediaItems($guid);
    }

    /**
     *
     * @param SOS_Request_Guid $guid
     * @param SOS_Model_MediaItem $item
     * @return true
     */
    public function addMediaItem(SOS_Request_Guid $guid, SOS_Model_MediaItem $item) {
        return $this->getDelegator()->addMediaItem($guid, $item);
    }

  }
