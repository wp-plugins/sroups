<?php

  require_once(APP_PATH . '/SOS/Request/InvalidException.php');
  require_once(APP_PATH . '/SOS/Request/UnsupportedException.php');

  /**
   * Guid represents a user object in the container.
   * The determinator of the user can be an integer userId, or a special string determinator
   * constant values in this class represents the special string determinators
   */
  class SOS_Request_Guid
  {
    const ME        = "@me";
    const OWNER     = "@owner";
    const USERID    = "@userId";
    //const COMMUNITY = "@community";

    private $_type = null;

    private $_userId = null;

    private $_groupId = null;

    /**
     * if $param is integer, than sets it as the userId
     * if that is a string, makes sure that $param is one of the given validTypes
     * @param integer | String $param
     * @param array $validTypes
     */
    public function  __construct($param, array $validTypes = null, $groupId = null) {
      if(is_numeric($param)) {
        $this->setUserId($userId);
        return;
      }
      
      if(empty($validTypes) && empty($param)) {
        // this is a valid request, guid can be optional
      }elseif(is_string($param)) {
        // check if it is a valid type
        if(!in_array(strtolower($param), $validTypes)) {
          throw new SOS_Request_UnsupportedException("Guid: " . $param . " is not supported for this request!");
        }
        $this->setType($param);
      } else {
        throw new SOS_Request_UnsupportedException("Guid: " . $param . " is not valid or not supported by this request!");
      }

      if (null != $groupId && is_numeric($groupId)) {
          $this->setGroupId($groupId);
      }
    }

    public function setType($type) {
      $this->_type = $type;
      return $this;
    }

    public function getType() {
      return $this->_type;
    }

    public function setUserId($userId) {
      $this->_userId = $userId;
      return $this;
    }

    public function getUserId() {
      return $this->_userId;
    }

    public function getGroupId() {
      return $this->_groupId;
    }

    public function setGroupId($groupId) {
      $this->_groupId = $groupId;
    }

  }
