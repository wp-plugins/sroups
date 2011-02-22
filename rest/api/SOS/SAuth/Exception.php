<?php
/* 
 * Please see Sroups License file
 */

  class SOS_SAuth_Exception extends Exception
  {
    public function  __construct($message = "") {
      parent::__construct($message);
      header("HTTP/1.1 401 UNAUTHORIZED");
    }
  }
