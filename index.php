<?php

session_start();

require __DIR__.'/vendor/autoload.php';

use HcodeEcom\pages\Page;
use HcodeEcom\pages\PageAdmin;

$app = new Slim\Slim();

$app->get("/", function(){
   $page = new Page();

   $page->setTpl("index");

});

$app->get("/admin", function(){
   $page = new PageAdmin();

   $page->setTpl("index");

});

$app->run();