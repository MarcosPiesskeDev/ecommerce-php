<?php

session_start();

use HcodeEcom\db\MethodsDb\MethodsDb;

require __DIR__.'/vendor/autoload.php';

$app = new \Slim\Slim();

$app->get("/", function(){
    $conn = new MethodsDb();
    
    $results = $conn->select("SELECT * FROM person");

    echo json_encode($results);

});

$app->run();