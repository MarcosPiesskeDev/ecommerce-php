<?php

require __DIR__.'/../vendor/autoload.php';

use HcodeEcom\modules\person\models\Person;
use HcodeEcom\modules\product\repository\ProductRepository;
use HcodeEcom\modules\user\models\User;
use HcodeEcom\modules\user\repositories\UserRepository;
use HcodeEcom\pages\Page;
use HcodeEcom\pages\PageAdmin;

$app = new Slim\Slim();

$app->get("/", function(){
   $productRepo = new ProductRepository();
   
   $products = $productRepo->getAllProducts();
   $page = new Page();
   $page->setTpl("index", [
      'products'=>$products
   ]);
});


$app->get("/admin", function(){

   UserRepository::verifyLogin();
   
   $page = new PageAdmin();

   $page->setTpl("index");

});

$app->get("/admin/login", function(){
  
   $page = new PageAdmin([
      "header" => false,
      "footer" => false
   ]);

   $page->setTpl("login");
});

$app->post('/admin/login', function(){
   $userRepo = new UserRepository();

   $userRepo->login($_POST['login'], $_POST['password']);

   header('Location: /admin');

   exit();
});

$app->get('/admin/logout', function(){
   UserRepository::logout();

   header("Location: /admin/login");
   exit();
});

$app->get("/admin/users", function(){
   UserRepository::verifyLogin();
   $userRepo = new UserRepository();
   $users = $userRepo->getAllUsers();
   $page = new PageAdmin();
   $page->setTpl('users', [
      "users" => $users
   ]);
});

$app->get("/admin/users/create", function(){
   UserRepository::verifyLogin();
   $page = new PageAdmin();
   $page->setTpl('users-create');
});


$app->get("/admin/users/:idUser/delete", function($idUser){
   UserRepository::verifyLogin();
   $userRepo = new UserRepository();

   $userRepo->deleteUserAndPersonById($idUser);
   header("Location: /admin/users");
   exit();
});

$app->get("/admin/users/:idUser", function($idUser){
   UserRepository::verifyLogin();
   $userRepo = new UserRepository();
   $page = new PageAdmin();

   $user = $userRepo->getUserAndPersonById((int)$idUser);

   $page->setTpl('users-update', [
      "user"=>$user
   ]);
});

$app->post("/admin/users/create", function(){
   UserRepository::verifyLogin();
   $page = new PageAdmin();
   $userRepo = new UserRepository();

   $user = new User();
   $person = new Person();

   $passEncrypted = openssl_encrypt($_POST['password'], 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));

   $_POST['is_admin'] = (isset($_POST['is_admin'])) ? 0 : 1;

   $person->setName($_POST['name']);
   $person->setEmail($_POST['email']);
   $person->setNPhone($_POST['n_phone']);
   $user->setUsername($_POST['username']);
   $user->setPassword($passEncrypted);
   $user->setIsAdmin($_POST['is_admin']);

   $userRepo->createUserAndPerson($user, $person);

   $page->setTpl('users-create');

   header("Location: /admin/users");
   exit();
});

$app->post("/admin/users/:idUser", function($idUser){
   UserRepository::verifyLogin();
   $page = new PageAdmin();

   $userRepo = new UserRepository();

   $_POST['is_admin'] = (isset($_POST['is_admin'])) ? 0 : 1;

   $user = $userRepo->getUserAndPersonById($idUser);

   $user['name']     = $_POST['name'];
   $user['email']    = $_POST['email'];
   $user['n_phone']  = $_POST['n_phone'];
   $user['username'] = $_POST['username'];
   $user['password'] = $_POST['password'];
   $user['is_admin'] = $_POST['is_admin'];

   $userRepo->updateUserAndPersonById($idUser, $user);

   $page->setTpl('users-update');

   header("Location: /admin/users");
   exit();
});

$app->get("/login", function(){
   
   $page = new Page();
   $page->setTpl("login", [
      'error' => UserRepository::getErrorLogin(),
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