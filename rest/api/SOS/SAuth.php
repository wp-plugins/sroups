<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/SAuth/Exception.php');

  class SOS_Sauth
  {
    private $originalUri;
    private $timestamp;
    private $sig;

    /**
     * $sig is coming from GET parameter
     *
     * @param String $originalUri
     * @param int $timestamp
     * @param String $sig
     */
    public function  __construct($originalUri = null, $timestamp = null,
            $sig = null )
    {
      $this->originalUri = $originalUri;
      $this->timestamp = $timestamp;
      $this->sig = $sig;
    }

    public function setOriginalUri($originalUri) {
      $this->originalUri = $originalUri;
    }

    public function setTimestamp($timestamp) {
      $this->timestamp = $timestamp;
    }

    public function setSig($sig) {
      $this->sig = $sig;
    }

    public function authenticate() {
      // check if any of the required fields are empty
      if( empty($this->originalUri) || empty($this->sig) ) {
       throw new SOS_SAuth_Exception("Authentication Failed!");
      }

      if( md5($this->originalUri . SOS_Factory::getConsumerSecret() . 
              $this->timestamp) != $this->sig ) {
       throw new SOS_SAuth_Exception("SAuth Signature Failed");
      } else {
        //if( ((time() - $this->timestamp) > 60) || (time() < $this->timestamp) ) {
        if( ((time() - $this->timestamp) > 60) ) {
         throw new SOS_SAuth_Exception("Timestamp check failed");
        }
      }

      // SAuth OK
      return true;
    }

    /**
     * Signs the response headers
     * @param String $response | JSON object
     */
    public static function signResponse($response, $originalURI) {
      $timestamp = time();

      $sauth = new stdClass();
      $sauth->ts = $timestamp;
      $sauth->sig = "";
      $sauth->originalUri = $originalURI;

      $response->sauth = $sauth;

      $sauth->sig = md5( $originalURI .
                          SOS_Factory::getConsumerSecret() . $timestamp );
      
      return json_encode($response);
    }


  }
