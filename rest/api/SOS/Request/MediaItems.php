<?php
/* 
 * Please see Oyun Studyosu License file
 */
  require_once(APP_PATH . '/SOS/Factory.php');
  require_once(APP_PATH . '/SOS/Request/Abstract.php');
  require_once(APP_PATH . '/SOS/Protocol/Abstract.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');
  require_once(APP_PATH . '/SOS/Request/InvalidException.php');
  require_once(APP_PATH . '/SOS/Service/NotImplementedException.php');
  

  class SOS_Request_MediaItems extends SOS_Request_Abstract
  {
    public function  __construct(SOS_Protocol_Abstract $protocol) {
      parent::__construct($protocol);

      $this->_guid =
              new SOS_Request_Guid(
                      $this->getProtocol()->getGuidParam(),
                      array(SOS_Request_Guid::COMMUNITY));

      $scope = $this->getProtocol()->getScopeParam();
      if($scope) {
        if(!is_string($scope)) {
          throw new SOS_Request_InvalidException("Invalid Scope");
        }

        // check if it begins with "@"
        if(strpos("@", $scope) != 1) {
          throw new SOS_Request_InvalidException("Invalid Scope");
        }

        // check if it is a valid scope
        if(!in_array( strtolower($scope), $this->getAvailableScopes())) {
          throw new SOS_Service_NotImplementedException("Scope:" . $scope . " is not supported!");
        }

        $this->_scope = $scope;
      }

    }

    /**
     *
     * @return array
     */
    public function getAvailableScopes() {
      return array("@self");
    }

    /**
     * Returns the person object as stdObject
     * if $json passed true, returns JSON string
     * @param boolean $json
     * @return String jsonObject
     */
    public function execute($json = true) {
      $mediaItemService = SOS_Factory::getMediaItemService();
      $mediaItems = $mediaItemService->getMediaItems($this->_guid);

      $arr = array();
      foreach($mediaItems as $m)
        array_push($arr, $m->getObject());

      return json_encode( $arr );
    }
  }
