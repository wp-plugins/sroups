<?php
/* 
 * Please see Sroups License file
 */
  class SOS_Request_UnsupportedException extends Exception
  {
    public function  __construct($message = "") {
      parent::__construct($message);
      header("HTTP/1.1 405 METHOD NOT ALLOWED");
    }
  }
