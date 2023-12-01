<?php

require_once $_ENV['HOME_PATH'].'/modules/actions/action_rest_api.php';

switch($route_function_trigger_params){
    case 'save-management-response':
        allowedRequestTypes("POST");
        GetActionHandler("save-management-response");   
        break;
    case 'save-temp-define-action':
        allowedRequestTypes("POST");
        GetActionHandler("save-temp-define-action");   
        break;
    case 'save-define-action':
        allowedRequestTypes("POST");
        GetActionHandler("save-define-action");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>