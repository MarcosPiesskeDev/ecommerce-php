<?php

namespace HcodeEcom\modules\user\interfaces;

use HcodeEcom\modules\person\models\Person;
use HcodeEcom\modules\user\models\User;

require __DIR__.'../../../../../vendor/autoload.php';

interface IUser{

    public function login(string $username, string $password);

    public function getAllUsers();

    public function getUserById(int $id);

    public function createUserAndPerson(User $user, Person $person);

    public function getUserAndPersonById(int $id): array;

    public function updateUserAndPersonById(int $idUser, array $data);

    public function deleteUserAndPersonById(int $idUser);
}