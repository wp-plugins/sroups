<?php

/*
 * This is sample person handler file
 */
require_once(APP_PATH . '/SOS/Service/Person.php');
require_once(APP_PATH . '/SOS/Model/Person.php');
require_once(APP_PATH . '/SOS/Request/Guid.php');

class PersonHandler implements SOS_Service_Person
{

    /**
     * Method used to instantiate a new SOS person model and populate with data
     * 
     * @return SOS_Model_Person
     */
    public function getPerson() {
        // create a new SOS person model instance.
        $person = new SOS_Model_Person();

        // set the unique identifier for the person. probably this will be the
        // primary key field value (e.g. email address) for your users table.
        $person->setId("1234567890");

        // set the person's age. following is an example script which calculates
        // the age regarding to the date of birth.
        // http://snipplr.com/view/1357/calculate-age/
        $person->setAge("30");

        // set the display name that is shown in the game screen.
        $person->setDisplayName("OS");

        // set the persons gender. use SOS_Model_Person::GENDER_FEMALE or
        // SOS_Model_Person::GENDER_MALE.
        $person->setGender(SOS_Model_Person::GENDER_MALE);

        // set whether the user is a member of your community or not. by default,
        // it's set to false. you can allow/deny anonymous access to your sroups
        // from your control panel on srou.ps.
        $person->setAnonymous(false);

        // set the profile url (on your website) for the person.
        $person->setProfileUrl("http://www.example.com/profile/example");

        // set the real name of the person. it will be shown to other users
        // when they want to see the person's profile badge in the game.
        $person->setName("Example Name");

        // set the thumbnail version of the profile picture of the person.
        $person->setThumbnailUrl("http://www.example.com/profile/example/pic/1.jpg");

        // set the medium size version of the profile picture of the person.
        $person->setMediumUrl("http://www.example.com/profile/example/pic/1.jpg");

        // set the original version of the profile picture of the person.
        $person->setLargeUrl("http://www.example.com/profile/example/pic/1.jpg");

        // set the type of the person. possible values are:
        // SOS_Model_Person::USER_TYPE_ANONYMOUS
        // SOS_Model_Person::USER_TYPE_NORMAL
        // SOS_Model_Person::USER_TYPE_MODERATOR
        // SOS_Model_Person::USER_TYPE_ADMINISTRATOR
        $person->setUserType(SOS_Model_Person::USER_TYPE_NORMAL);

        return $person;
    }

}
