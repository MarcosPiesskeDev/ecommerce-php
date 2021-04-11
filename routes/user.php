<?php

use HcodeEcom\modules\person\models\Person;
use HcodeEcom\modules\product\repository\ProductRepository;
use HcodeEcom\modules\user\models\User;
use HcodeEcom\modules\user\repositories\UserRepository;
use HcodeEcom\pages\Page;

require __DIR__.'/../vendor/autoload.php';

$app->get("/", function(){
    $productRepo = new ProductRepository();
    
    $products = $productRepo->getAllProducts();
    $page = new Page();
    $page->setTpl("index", [
       'products'=>$products
    ]);
 });

$app->get("/login", function(){
   
    $page = new Page();
    $page->setTpl("login", [
       'errorLogin' => UserRepository::getErrorLogin(),
       'errorRegister' => UserRepository::getErrorRegister(),
    ]);
 });
 
 $app->post("/login", function(){
 
    $userRepo = new UserRepository();
 
    try{
       $userRepo->login($_POST['login'], $_POST['password']);
       header('Location: /checkout');
    }catch(Exception $e){
       UserRepository::setErrorLogin($e->getMessage());
       header('Location: /login');
    }
    exit();
 });
 
 $app->get("/logout", function(){
    UserRepository::logout();
 
    header("Location: /login");
    exit();
 });

 $app->post("/register", function(){
    $userRepo = new UserRepository();
    $user = new User();
    $person = new Person();

    if(is_numeric($_POST['usernameRegister'])){
      UserRepository::setErrorRegister("You must pass a not just numeric value in username");
      header('Location: /login');
      exit();
    }

    if($_POST['usernameRegister'] === "" || $_POST['passwordRegister'] === "" || $_POST['name'] === "" || $_POST['email'] === "" ){
      UserRepository::setErrorRegister("Fields cannot be empty");
      header('Location: /login');
      exit();
    }

    if($userRepo->usernameExists($_POST['usernameRegister']) === true){
      UserRepository::setErrorRegister("This username already exists, try use other");
      header('Location: /login');
      exit();
    }

    if($userRepo->emailExists($_POST['email'] === true)){
      UserRepository::setErrorRegister("This email already exists, try use other");
      header('Location: /login');
      exit();
    }

    try{
      $passEncrypted = openssl_encrypt($_POST['passwordRegister'], 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));

       $user->setUsername($_POST['usernameRegister']);
       $user->setPassword($passEncrypted);
       $user->setIsAdmin(1);
       $person->setName($_POST['name']);
       $person->setEmail($_POST['email']);
       $person->setNPhone($_POST['phone']);
       $userRepo->createUserAndPerson($user, $person);
       $userRepo->login($_POST['usernameRegister'], $_POST['passwordRegister']);

    }catch(Throwable $t){
       UserRepository::setErrorRegister($t->getMessage());
       header('Location: /login');
       exit();
    }
    exit();
 });