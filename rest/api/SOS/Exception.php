<?php
/* 
 * Please see Oyun Studyosu License file
 */

  class SOS_Exception extends Exception
  {
    public function  __construct($message = "") {
      parent::__construct($message);
      header("HTTP/1.1 404 Not Found");
    }
  }