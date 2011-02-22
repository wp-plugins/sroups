<?php
  /*
 * Please see Sroups License file
  */
  require_once(APP_PATH . '/SOS/Request/InvalidException.php');

  /**
   * @todo refactor this file to have a decent dispatcher
   * and implement a routing system
   */
  class SOS_Protocol_Abstract {
    private $_baseUrl = null;
    private $_requestUri = null;
    private $_appUri = null;
    private $_queryString = null;
    private $_useQuerystring = false;

    public function __construct() {
      $this->setRequestUri();
      $this->setBaseUrl();
      $this->setAppUri();
    }

    public function getBaseUrl() {
      /*if(null === $this->_baseUrl) {
        $this->setBaseUrl();
      }*/
      return $this->_baseUrl;
    }

    public function setBaseUrl() {
      $baseUrl = Bootstrap::getBaseUrl();
      if( !empty($baseUrl) ) {

        // exclude host name
        if( strpos($baseUrl, 'http://') !== false || strpos($baseUrl, 'https://') !== false ) {
          $baseUrl = str_replace('http://', "", $baseUrl);
          $baseUrl = str_replace('https://', "", $baseUrl);
          $baseUrl = str_replace($_SERVER['HTTP_HOST'], "", $baseUrl);
        }
        $this->_baseUrl = $baseUrl;
        return $this;
      }

      $this->_baseUrl = rtrim($baseUrl, '/');
      return $this;
    }

    public function getRequestUri() {
      if (empty($this->_requestUri)) {
        $this->setRequestUri();
      }

      return $this->_requestUri;
    }

    public function setRequestUri($requestUri = null) {
      $this->_requestUri = $_SERVER['REQUEST_URI'];
      return $this;
    }

    public function getAppUri() {
      if(null === $this->_appUri) {
        $this->setAppUri();
      }
      return $this->_appUri;
    }

    public function setAppUri() {
      // get rid of the querystring part
      $strpos = strpos($this->getRequestUri(),"?");
      $reqUri = ( $strpos ) ? substr($this->getRequestUri(),0, $strpos) : $this->getRequestUri();
      $this->_appUri = "/" . str_replace($this->getBaseUrl(), "", $reqUri);
      return $this;
    }

    public function getQueryString() {
      if(null === $this->_queryString) {
        $this->setQueryString();
      }
      return $this->_queryString;
    }

    public function setQueryString() {
      $this->_queryString = substr($this->getRequestUri(), strpos($this->getRequestUri(),"?"));
      return $this;
    }

    /**
     * Returns the parameter using given index in the URL Schema
     * for example:
     *    http://hostname/protocol/parameter1/parameter2
     *    $this->getParam(1); returns "parameter1"
     * @param integer $param
     * @return String
     */
    public function getParam($index = null) {
      if(is_string($index) && isset($_GET[$index])) {
        return urlencode($_GET[$index]);
      }

      if($this->_useQuerystring) {
        return $this->getParamUsingQuerystring($index);
      } else {
        return $this->getParamUsingAppUrl($index);
      }
    }

    public function getParamUsingAppUrl($index = null) {
      $url = ltrim($this->getAppUri(),"/");
      $arr = explode(DIRECTORY_SEPARATOR, $url);

      if(null === $index) {
        return $arr;
      }

      if(!is_numeric($index)) {
        throw new SOS_Request_InvalidException("Get Param Requires a numeric parameter");
      }

      return (isset($arr[$index])) ? $arr[$index] : false;
    }

    public function getParamUsingQuerystring($index = null) {
      $arr = array();
      $arr[0] = (isset($_GET['action'])) ? $_GET['action'] : "";
      $arr[1] = (isset($_GET['guid'])) ? $_GET['guid'] : "";
      $arr[2] = (isset($_GET['scope'])) ? $_GET['scope'] : "";

      if(null === $index) {
        return $arr;
      }

      if(!is_numeric($index)) {
        throw new SOS_Request_InvalidException("Get Param Requires a numeric parameter");
      }

      return (isset($arr[$index])) ? $arr[$index] : false;
    }

    public function getParams() {
      return $this->getParam();
    }

    public function getPost($name) {
        if(isset($_POST[$name]))
          return htmlentities($_POST[$name],ENT_QUOTES);
        else
          return null;
    }

    private function _getProtocolHelper($index) {
      $res = $this->getParam($index);
      //if(!$res)
      //  throw new SOS_Request_InvalidException("Invalid URL: " . $this->getRequestUri());
      return $res;
    }

    public function getProtocolParam() {
      return $this->_getProtocolHelper(0);
    }

    public function getGuidParam() {
      return $this->_getProtocolHelper(1);
    }

    public function getScopeParam() {
      return $this->getParam(2);
    }

    public function getAlbumIdParam() {
      return $this->getParam(3);
    }

    public function useQuerystring($_useQuerystring) {
      $this->_useQuerystring = $_useQuerystring;
    }

  }
