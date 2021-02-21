<?php

session_start();

require __DIR__.'/vendor/autoload.php';

$app = new Slim\Slim();

require_once("routes/user-admin.php");
require_once("routes/forgot-pass.php");
require_once("routes/categories.php");
require_once("routes/products.php");

$app->run();