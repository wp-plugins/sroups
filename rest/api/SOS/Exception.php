<?php
/* 
 * Please see Sroups License file
 */

  class SOS_Exception extends Exception
  {
    public function  __construct($message = "") {
      parent::__construct($message);
      header("HTTP/1.1 500 Internal Server Error");
    }
  }
