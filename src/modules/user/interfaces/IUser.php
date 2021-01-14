<?php

namespace HcodeEcom\modules\interfaces;

require __DIR__.'../../../../../vendor/autoload.php';

interface IUser{

    public function login(string $username, string $password);

}