<?php
/* 
 * Please see Oyun Studyosu License file
 */
  require_once(APP_PATH . '/SOS/Model/Abstract.php');

  class SOS_Model_Environment extends SOS_Model_Abstract
  {
    const FIELD_FIELDS = 'fields';
    const FIELD_DOMAIN = 'domain';

    public function  __construct() {}

    public function setFields($fields) {
      $this->setField(self::FIELD_FIELDS, $fields);
    }

    public function setDomain($domain) {
      $this->setField(self::FIELD_DOMAIN, $domain);
    }

  }
