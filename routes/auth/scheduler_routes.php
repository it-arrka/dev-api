<?php

require_once $_ENV['HOME_PATH'] . '/modules/scheduler_rest_api.php';

switch ($route_function_trigger_params) {
    case 'add-activity':
        allowedRequestTypes("POST");
        GetSchedulerHandler("add-activity");
        break;
    case 'report':
        allowedRequestTypes("GET");
        GetSchedulerHandler("report");
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>