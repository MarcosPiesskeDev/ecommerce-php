<?php

session_start();

require __DIR__.'/vendor/autoload.php';

use HcodeEcom\pages\Page;

$app = new Slim\Slim();

$app->get("/", function(){
   $page = new Page();

   $page->setTpl("index");

});

$app->run();