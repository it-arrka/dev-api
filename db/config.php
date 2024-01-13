<?php

try{
    $db_name = $platform=$_ENV['DB_NAME'];
    $session  = Cassandra::cluster()
    ->withDefaultPageSize(100000)
    ->build()
    ->connect($db_name);
    global $session;
}catch(Exception $e){
    // Error in connection with db cluster
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" =>"Error in connection with db"]);
    exit();
}

?>