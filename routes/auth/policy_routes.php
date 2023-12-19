<?php

require_once $_ENV['HOME_PATH'].'/modules/policy_rest_api.php';

switch($route_function_trigger_params){
    case 'get-policy-version-status':
        allowedRequestTypes("GET");
        GetPolicyHandler("get-policy-version-status");   
        break;
    case 'get-policy-domains':
        allowedRequestTypes("GET");
        GetPolicyHandler("get-policy-domains");   
        break;

    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>