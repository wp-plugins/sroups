<?php
/* 
 * This is sample person handler file
 */
  require_once(APP_PATH . '/SOS/Service/Person.php');
  require_once(APP_PATH . '/SOS/Model/Person.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  class PersonHandler implements SOS_Service_Person
  {
    public function getPerson(SOS_Request_Guid $guid) {
      $person = new SOS_Model_Person();
      $person->setId("1234567890");
      $person->setAge("28");
      $person->setDisplayName("suleyman");
      $person->setGender(SOS_Model_Person::GENDER_MALE);
      $person->setAnonymous(true);

      $person->setProfileUrl("http://www.example.com/profile/suleyman");
      $person->setName("Süleyman Melikoğlu");
      $person->setThumbnailUrl("http://www.example.com/profile/suleyman/pic/1.jpg");
      $person->setMediumUrl("http://www.example.com/profile/suleyman/pic/1.jpg");
      $person->setLargeUrl("http://www.example.com/profile/suleyman/pic/1.jpg");
      $person->setUserType(SOS_Model_Person::USER_TYPE_NORMAL);

      return $person;

    }
  }
