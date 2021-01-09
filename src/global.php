<?php

use Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__."/..", null);
$dotenv->load();
