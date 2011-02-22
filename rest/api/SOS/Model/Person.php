<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Model/Abstract.php');

  class SOS_Model_Person extends SOS_Model_Abstract
  {
    const FIELD_ID = 'id';
    const FIELD_DISPLAY_NAME = 'displayName';
    const FIELD_GENDER = 'gender';
    const FIELD_AGE = 'age';
    const FIELD_ANONYMOUS = 'anonymous';
    const FIELD_PROFILE_URL = 'profileUrl';
    const FIELD_NAME = 'name';
    const FIELD_SR_THUMBNAIL_URL = 'srThumbnailUrl';
    const FIELD_SR_LARGE_URL = 'srLargeUrl';
    const FIELD_SR_MEDIUM_URL = 'srMediumUrl';
    const FIELD_USER_TYPE = 'srUserType';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const USER_TYPE_NORMAL = "normal";
    const USER_TYPE_ADMINISTRATOR = "administrator";
    const USER_TYPE_MODERATOR = "moderator";
    const USER_TYPE_ANONYMOUS = "anonymous";

    public function  __construct() {}

    public function setId($id) {
      $this->setField(self::FIELD_ID, $id);
    }

    public function setDisplayName($name) {
      $this->setField(self::FIELD_DISPLAY_NAME, $name);
    }

    public function setName($name) {
        $this->setField(self::FIELD_NAME, $name);
    }

    public function setGender($gender) {
      $this->setField(self::FIELD_GENDER, $gender);
    }

    public function setAge($age) {
      $this->setField(self::FIELD_AGE, $age);
    }

    public function setAnonymous($isAnonymous = false) {
      $this->setField(self::FIELD_ANONYMOUS, $isAnonymous);
    }

    public function setProfileUrl($profileUrl) {
        $this->setField(self::FIELD_PROFILE_URL, $profileUrl);
    }

    public function setThumbnailUrl($url) {
        $this->setField(self::FIELD_SR_THUMBNAIL_URL, $url);
    }

    public function setMediumUrl($url) {
        $this->setField(self::FIELD_SR_MEDIUM_URL, $url);
    }

    public function setLargeUrl($url) {
        $this->setField(self::FIELD_SR_LARGE_URL, $url);
    }

    /**
     * Allowed user types are
     * administrator, moderator, normal or anonymous
     * @param string $type
     */
    public function setUserType($type) {
        $this->setField(self::FIELD_USER_TYPE, $type);
    }

  }
