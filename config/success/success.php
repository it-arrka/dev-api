<?php 

function commonSuccessResponse($successCode,$data,$customMessage= ""){
    $successMessages = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content'
    ];

    //Set Header
    header('Content-Type: application/json');
    // Check if the provided error code exists in the mapping
    if (array_key_exists($successCode, $successMessages)) {
        // Set the appropriate HTTP response code
        http_response_code($successCode);

        // Prepare the response message
        $response = [
            'message' => $customMessage,
            'data' => $data
        ];

        // Send the response as JSON
        echo json_encode($response);
    } else {
        // If an invalid error code is provided, return a generic error response
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'An unexpected error occurred.']);
        exit();
    }
}

?>