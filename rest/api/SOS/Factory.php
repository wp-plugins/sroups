<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Service/Person.php');
  require_once(APP_PATH . '/SOS/Service/MediaItem.php');
  require_once(APP_PATH . '/SOS/Service/Environment.php');
  require_once(APP_PATH . '/SOS/Service/PersonImpl.php');
  require_once(APP_PATH . '/SOS/Service/MediaItemImpl.php');
  require_once(APP_PATH . '/SOS/Service/EnvironmentImpl.php');

  class SOS_Factory
  {

    private static $_consumerSecret;

    /**
     * Host Name. i.e. http://www.example.com
     * @var String
     */
    private static $_domain;

    /**
     * full qualified API URL: i.e. http://www.example.com/sroups/api
     * @var String
     */
    private static $_apiUrl;

    /**
     *
     * @return SOS_Service_Person
     */
    public static function getPersonService() {
      return SOS_Service_PersonImpl::getInstance();
    }

    /**
     *
     * @param SOS_Service_Person $personService
     */
    public static function setPersonService(SOS_Service_Person $personService) {
      SOS_Service_PersonImpl::getInstance()->setDelegator($personService);
    }

    /**
     *
     * @return SOS_Service_MediaItem
     */
    public static function getMediaItemService() {

      return SOS_Service_MediaItemImpl::getInstance();
    }

    /**
     *
     * @param SOS_Service_MediaItem $mediaItemService 
     */
    public static function setMediaItemService(SOS_Service_MediaItem $mediaItemService) {
      SOS_Service_MediaItemImpl::getInstance()->setDelegator($mediaItemService);
    }

    /**
     * @return SOS_Service_Environment
     */
    public static function getEnvironmentService() {
      return SOS_Service_EnvironmentImpl::getInstance();
    }

    public static function setEnvironmentService(SOS_Service_Environment $environmentService) {
      SOS_Service_EnvironmentImpl::getInstance()->setDelegator($environmentService);
    }

    public static function getConsumerSecret() {
      return SOS_Factory::$_consumerSecret;
    }

    public static function setConsumerSecret($secret) {
      SOS_Factory::$_consumerSecret = $secret;
    }

    public static function setDomain($domain) {
      SOS_Factory::$_domain = $domain;
    }

    public static function getDomain() {
      return SOS_Factory::$_domain;
    }

  }
