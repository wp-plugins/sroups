<?php
/* 
 * Please see Sroups License file
 */
  // Define path to application directory
  defined('APP_PATH')
          || define('APP_PATH', realpath(dirname(__FILE__) . '/..'));

  /**
   * Common definitions
   */
  define('DIRECTORY_DELIMITER', '_');
  
  require_once(APP_PATH . '/SOS/Protocol/DataService.php');

  class Bootstrap
  {
    private $_debug = false;
    
    private $_useQueryString = false;

    static $_baseUrl = "";

    public function __construct($autoload = false) {
      if($autoload) {
        $this->_initAutoload();
      }
      $this->_initRoute();
      $this->useQueryString(false); // default value of using query strings is false
    }

    protected function _initAutoload() {
      spl_autoload_register(array('Bootstrap', 'autoload'));
    }

    /**
     * autoload class which should be registered with spl_autoload_register function
     * @param String $classname
     */
    public static function autoload($classname) {
      self::_securityCheck($classname);
      $arr = explode(DIRECTORY_DELIMITER, $classname);
      $classname = str_replace(DIRECTORY_DELIMITER, DIRECTORY_SEPARATOR, $classname);
      require_once( APP_PATH . DIRECTORY_SEPARATOR . $classname . '.php');
    }

    /**
     * Make sure that filename does not contain exploits
     *
     * @param  string $filename
     * @return void
     * @throws SOS_Exception
     */
    protected static function _securityCheck($filename)
    {
        if (preg_match('/[^a-z0-9\\/\\\\_.-]/i', $filename)) {
            require_once 'SOS/Exception.php';
            throw new SOS_Exception('Security check: Illegal character in filename');
        }
    }

    /**
     * initialize the rest route and forwards the request to
     * an appropriate controller (protocol)
     *
     * the route schema will be
     * hostname/:controller/:guid/:scope
     * for example: localhost/people/@self/@friends
     */
    protected function _initRoute() {
      
    }

    public function dispatch() {
      $dispatcher = new SOS_Protocol_DataService();
      $dispatcher->useQuerystring($this->_useQueryString);
      return $dispatcher->handle();
    }

    public function info() {
      $dispatcher = new SOS_Protocol_DataService();
      echo 'base url is: ' . $dispatcher->getBaseUrl();
      echo '<br />';
      echo 'request uri is: ' . $dispatcher->getRequestUri();
      echo '<br />';
      echo 'app uri is: ' . $dispatcher->getAppUri();
      echo '<br />';
      echo 'querystring is: ' . $dispatcher->getQueryString();
      echo '<br />';
      foreach ($dispatcher->getParam() as $i => $val) {
        echo 'parameter ' . $i . ' : ' . $val;
        echo '<br />';
      }
      
    }

    public function setDebug($boolean) {
      $this->_debug = $boolean;
      return $this;
    }

    public function isDebug() {
      return $this->_debug;
    }

    public function useQueryString($_useQueryString) {
      $this->_useQueryString = $_useQueryString;
    }

    public static function setBaseUrl($baseUrl) {
      self::$_baseUrl = $baseUrl;
    }

    public static function getBaseUrl() {
      return self::$_baseUrl;
    }

  }
