<?php
//This file needs to be included in all APIs
//This may be different for Production setup and different for development

// Define a custom error handling function

require_once 'error_codes.php';
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $response = [
        'code' => $errno,
        'error' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'message' => ''
    ];
    http_response_code(500); // Set an appropriate HTTP status code
    header('Content-Type: application/json');
    $msg="[ErNo: $errno] [ErMsg: $errstr] [ErFile: $errfile] [ErLine: $errline]";
    //write into logs
    write_log(LOG_ERR,$msg,$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER']);
    echo json_encode($response);
    exit();
}

function catchErrorHandler($errorCode,$customErrorMessageArr) {
    $errorMessages = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable'
    ];

    //Set Header
    header('Content-Type: application/json');

    // Check if the provided error code exists in the mapping
    if (array_key_exists($errorCode, $errorMessages)) {
        // Set the appropriate HTTP response code
        http_response_code($errorCode);

        // Prepare the response message
        $response = [
            'message' => $customErrorMessageArr['message'],
            'error' => $customErrorMessageArr['error']
        ];

        // Send the response as JSON
        echo json_encode($response);
        exit();
    } else {
        // If an invalid error code is provided, return a generic error response
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'An unexpected error occurred.']);
        exit();
    }
}

// Set the custom error handler
set_error_handler("customErrorHandler");


?>
