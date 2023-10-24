<?php

require_once $_ENV['HOME_PATH'].'/modules/user_rest_api.php';
require_once $_ENV['HOME_PATH'].'/modules/activity_rest_api.php';

switch($route_function_trigger_params){
    case 'userRoles':
        allowedRequestTypes("GET");
        GetUserHandler("userRoles");   
        break;
    case 'activity':
        allowedRequestTypes("GET");
        GetActivityHandler("activity");   
        break;
    case 'activityAll':
        allowedRequestTypes("GET");
        GetActivityHandler("activityAll");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found ".$_GET['limit']]);
        exit();
}

?>