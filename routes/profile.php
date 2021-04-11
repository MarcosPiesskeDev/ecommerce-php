<?php

use HcodeEcom\modules\person\repositories\PersonRepository;
use HcodeEcom\modules\user\repositories\UserRepository;
use HcodeEcom\pages\Page;

require __DIR__.'/../vendor/autoload.php';


$app->get("/profile", function(){
    
    $sUser = UserRepository::getUserFromSession();
    $personRepo = new PersonRepository();

    $person = $personRepo->getPersonById($sUser->getIdPerson());

    $personData = [
        "person" => [
                "id" => $person->getId(),
                "name" => $person->getName(),
                "email" => $person->getEmail(),
                "nPhone" => $person->getNPhone(),
                "dateRegister" => $person->getDateRegister(),
            ],
        ];

    
    $userData =[ 
        "user" => [
            "id" => $sUser->getId(),
            "idPerson" => $sUser->getIdPerson(),
            "username" => $sUser->getUsername(),
            "password" => $sUser->getPassword(),
            "isAdmin" => $sUser->getIsAdmin(),
            "dateRegister" => $sUser->getDateRegister(),
        ],
    ];

    $page = new Page();

    $page->setTpl("profile", [
        "user" => $userData['user'],
        "person" => $personData['person'],
        "profileMsg" => UserRepository::getSuccessChangeData(),
        "profileError" => UserRepository::getErrorChangeData()
    ]);

});

$app->post("/profile", function(){
    
    $userRepo = new UserRepository();
    $sUser = UserRepository::getUserFromSession();
    $personRepo = new PersonRepository();
    $person = $personRepo->getPersonById($sUser->getIdPerson());

    if(empty($_POST['name']) || $_POST['name'] === ''){
        UserRepository::setErrorChangeData("You must to pass a name here");
        header('Location: /profile');
        exit();
    }

    if(empty($_POST['email']) || $_POST['email'] === ''){
        UserRepository::setErrorChangeData("You must to pass a valid e-mail here");
        header('Location: /profile');
        exit();
    }

    if ($_POST['email'] !== $person->getEmail()){
        if($userRepo->emailExists($_POST['email']) === true){
            UserRepository::setErrorChangeData("This e-mails is already in use. Try other valid e-mail");
            header('Location: /profile');
            exit();
        }
    }

    $decryptedUserPass = openssl_decrypt($sUser->getPassword(), 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));

    $userRepo->updateUserAndPersonById($sUser->getId(), [
        'name' => $_POST['name'],
        'username' => $sUser->getUsername(),
        'password' => $decryptedUserPass,
        'email' => $_POST['email'],
        'n_phone' => $person->getNPhone(),
        'is_admin' => $sUser->getIsAdmin()
    ]);
    UserRepository::setSuccessChangeData("Data changed with success!");
    header('Location: /profile');
    exit();
});