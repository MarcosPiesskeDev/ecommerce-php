<?php

use HcodeEcom\modules\person\models\Person;
use HcodeEcom\modules\person\repositories\PersonRepository;
use HcodeEcom\modules\user\repositories\UserRepository;

function checkLogin($inAdmin = true)
{
    return UserRepository::checkUserLogin($inAdmin);
}

function getUsernamePerson()
{
    $user = UserRepository::getUserFromSession();
    $personRepo = new PersonRepository();

    $person = $personRepo->getPersonById($user->getIdPerson());
    
    return $person->getName();
}