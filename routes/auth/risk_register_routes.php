<?php
require_once $_ENV['HOME_PATH'].'/modules/risk_register_rest_api.php';

switch($route_function_trigger_params){
    case 'get-policy-version-status':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("get-policy-version-status");   
        break;
        
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>