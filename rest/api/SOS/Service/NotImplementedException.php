<?php
/* 
 * Please see Sroups License file
 */

  class SOS_Service_NotImplementedException extends Exception
  {
    public function  __construct($message = "") {
      parent::__construct($message);
      header("HTTP/1.1 501 Not Implemented");
    }
  }
