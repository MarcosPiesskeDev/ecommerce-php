<?php

require __DIR__.'/../vendor/autoload.php';

use HcodeEcom\modules\user\repositories\UserRepository;
use HcodeEcom\pages\PageAdmin;

$app->get("/admin/forgot", function(){

    $page = new PageAdmin([
       "header" => false,
       "footer" => false
    ]);
 
    $page->setTpl("forgot");
 });
 
 $app->post("/admin/forgot", function(){
    UserRepository::getUserByEmailToRecoverPass($_POST['email']);
    header("Location: /admin/forgot/sent");
    exit();
 
 });
 
 $app->get("/admin/forgot/sent", function(){
    $page = new PageAdmin([
       "header"=>false,
       "footer"=>false
    ]);
 
    $page->setTpl("forgot-sent");
 });
 
 $app->get("/admin/forgot/reset", function(){
    
    $userRepo = new UserRepository();
 
    $forgot = $userRepo->validForgotDecrypt($_GET['code']);
    $page = new PageAdmin([
       "header"=>false,
       "footer"=>false
    ]);
 
    $page->setTpl('forgot-reset', [
       "name"   => $forgot['name'],
       "code"   => $_GET['code']
    ]);
 });
 
 $app->post("/admin/forgot/reset", function(){
    $userRepo = new UserRepository();
 
    $forgot = $userRepo->validForgotDecrypt($_POST['code']);
 
    $userRepo->setDateToForgotPassword($forgot['id']);
 
    $userRepo->setForgotPassword($forgot['id_user'], $_POST['password']);
 
    $page = new PageAdmin([
       "header"=>false,
       "footer"=>false
    ]);
 
    $page->setTpl("forgot-reset-success");
 
 });