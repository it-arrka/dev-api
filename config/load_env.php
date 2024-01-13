<?php

try{
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}catch(Exception $e){
    // Error in loading environment file i.e. .env
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" =>"Error in loading .env file : ".$e->getMessage()]);
    exit();
}

// Access environment variables
// $dbHost = $_ENV['DB_HOST'];

?>
