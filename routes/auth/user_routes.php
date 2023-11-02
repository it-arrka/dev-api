<?php

require_once $_ENV['HOME_PATH'].'/modules/user_rest_api.php';
require_once $_ENV['HOME_PATH'].'/modules/activity_rest_api.php';

switch($route_function_trigger_params){
    case 'pageCommonData':
        allowedRequestTypes("GET");
        GetUserHandler("pageData");   
        break;
    case 'userRoles':
        allowedRequestTypes("GET");
        GetUserHandler("userRoles");   
        break;
    case 'changeRole':
        allowedRequestTypes("POST");
        GetUserHandler("changeRole");   
        break;
    case 'changeLaw':
        allowedRequestTypes("POST");
        GetUserHandler("changeLaw");   
        break;
    case 'activityInfo':
        allowedRequestTypes("GET");
        GetActivityHandler("activityInfo");   
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
        echo json_encode(["message"=>"404 Not Found "]);
        exit();
}

?>