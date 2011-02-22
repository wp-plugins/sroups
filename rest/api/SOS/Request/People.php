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

  class SOS_Request_People extends SOS_Request_Abstract
  {

    public function  __construct(SOS_Protocol_Abstract $protocol) {
      parent::__construct($protocol);

      $this->_guid =
              new SOS_Request_Guid(
                      $this->getProtocol()->getGuidParam(),
                      array(SOS_Request_Guid::ME, 
                            SOS_Request_Guid::OWNER,
                            SOS_Request_Guid::USERID)); 

      $scope = $this->getProtocol()->getScopeParam();
      if($scope) {
        if(!is_string($scope)) {
          throw new SOS_Request_InvalidException("Invalid Scope, Scope parameter must be a string");
        }

        // check if it begins with "@"
        if(strpos("@", $scope)) {
          throw new SOS_Request_InvalidException("Invalid Scope " . $scope . ", Scope parameter must begin with @");
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
      $personService = SOS_Factory::getPersonService();
      $person = $personService->getPerson($this->_guid);

      return $person->getJSONObject();
    }

  }
