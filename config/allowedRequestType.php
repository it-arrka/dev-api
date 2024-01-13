<?php

function allowedRequestTypes($requestType){
    if ($_SERVER['REQUEST_METHOD'] != $requestType) {
        // Only POST requests are allowed
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" =>"Method not allowed"]);
        exit();
    }
}

?>