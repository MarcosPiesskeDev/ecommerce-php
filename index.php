<?php

session_start();

require __DIR__.'/vendor/autoload.php';

use HcodeEcom\modules\repositories\UserRepository;
use HcodeEcom\pages\Page;
use HcodeEcom\pages\PageAdmin;

$app = new Slim\Slim();

$app->get("/", function(){
   $page = new Page();

   $page->setTpl("index");

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

$app->run();