<?php
/* 
 * Please see Sroups License file
 */

  abstract class SOS_Request_Abstract
  {
    /**
     * @param SOS_Protocol_Abstract
     */
    protected $_protocol = null;

    protected $_guid  = null;
    
    protected $_scope = null;

    public function  __construct(SOS_Protocol_Abstract $protocol) {
      $this->setProtocol($protocol);
    }

    /**
     *
     * @param SOS_Protocol_Abstract $protocol
     * @return SOS_Request_Abstract
     */
    public function setProtocol($protocol) {
      $this->_protocol = $protocol;
      return $this;
    }

    /**
     *
     * @return SOS_Protocol_Abstract
     */
    public function getProtocol() {
      return $this->_protocol;
    }

    /**
     * @return array
     */
    public abstract function getAvailableScopes();

    public abstract function execute();
  }
