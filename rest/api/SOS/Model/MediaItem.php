<?php
/* 
 * Please see Oyun Studyosu License file
 */
  require_once(APP_PATH . '/SOS/Model/Abstract.php');

  class SOS_Model_MediaItem extends SOS_Model_Abstract
  {
    const FIELD_ID            = 'id';
    const FIELD_URL           = 'url';
    const FIELD_TITLE         = 'title';
    const FIELD_DESCRIPTION   = 'description';
    const FIELD_THUMBNAILURL  = 'thumbnailUrl';

    public function  __construct() {}

    public function setId($id) {
      $this->setField(self::FIELD_ID, $id);
    }

    public function setUrl($url) {
      $this->setField(self::FIELD_URL, $url);
    }

    public function setTitle($title) {
      $this->setField(self::FIELD_TITLE, $title);
    }

    public function setDescription($description) {
      $this->setField(self::FIELD_DESCRIPTION, $description);
    }

    public function setThumbnailUrl($thumbnailUrl) {
      $this->setField(self::FIELD_THUMBNAILURL, $thumbnailUrl);
    }

  }
