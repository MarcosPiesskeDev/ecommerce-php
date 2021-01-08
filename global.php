<?php

use Dotenv\Dotenv;

require 'vendor/autoload.php';

$dotenv = Dotenv::create(__DIR__, null);
$dotenv->load();
