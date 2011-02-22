<?php
/* 
 * Please see Sroups License file
 */

  class SOS_Request_InvalidException extends Exception
  {
    public function  __construct($message = "") {
      parent::__construct($message);
      header("HTTP/1.1 400 Bad Request");
    }
  }
