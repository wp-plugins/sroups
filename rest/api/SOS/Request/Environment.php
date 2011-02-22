<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Factory.php');
  require_once(APP_PATH . '/SOS/Request/Abstract.php');
  require_once(APP_PATH . '/SOS/Protocol/Abstract.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');
  require_once(APP_PATH . '/SOS/Request/InvalidException.php');
  require_once(APP_PATH . '/SOS/Service/NotImplementedException.php');

  class SOS_Request_Environment extends SOS_Request_Abstract
  {
    public function  __construct(SOS_Protocol_Abstract $protocol) {
      parent::__construct($protocol);

      $this->_guid =
                new SOS_Request_Guid(
                        $this->getProtocol()->getGuidParam(),
                        array(SOS_Request_Guid::ME),
                        $this->getProtocol()->getScopeParam());

    }

    /**
     *
     * @return array
     */
    public function getAvailableScopes() {
      return array("@self");
    }

    public function execute($json = true) {
      $environmentService = SOS_Factory::getEnvironmentService();
      $res = $environmentService->getEnvironment($this->_guid);
      
      return $res->getJSONObject();
    }
  }
