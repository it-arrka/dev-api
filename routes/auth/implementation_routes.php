<?php

require_once $_ENV['HOME_PATH'] . '/modules/implementation_rest_api.php';

switch ($route_function_trigger_params) {

    case 'test_':
        allowedRequestTypes("GET");
        GetImplementationHandler("test_");
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>