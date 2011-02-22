<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Model/Interface.php');

  class SOS_Model_Abstract implements SOS_Model_Interface
  {
    /**
     * Array stores the desired fields
     * The values of these fields in this array will be
     * returned from the service
     * @var array
     */
    private $_fields = array();

    /**
     * Sets a new field value of fields array
     * The values of these fields in this array will be
     * returned from the service
     */
    public function setField($key, $value) {
      $this->_fields[$key] = $value;
    }

    public function getField($key) {
      if(isset($this->_fields[$key]))
        return $this->_fields[$key];
      else
        return null;
    }

    /**
     * Returns string in JSON notation
     * @return String
     */
    public function getJSONObject() {
      return json_encode($this->getObject());
    }

    /**
     *
     * @return stdClass
     */
    public function getObject() {
      $res = new stdClass();
      foreach ($this->_fields as $field => $value) {
        $res->$field = $value;
      }
      return $res;
    }

    public function  __toString() {
      $res = "";
      foreach ($this->_fields as $field => $value) {
        $res .= $field . ":" . $value;
      }
      return $res;
    }
  }
