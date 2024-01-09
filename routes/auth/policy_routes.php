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
    case 'get-policy-domain-data':
        allowedRequestTypes("GET");
        GetPolicyHandler("get-policy-domain-data");  
        break;
   
    case 'discard-domain-policy-changes':
        allowedRequestTypes("POST");
        GetPolicyHandler("discard-domain-policy-changes");  
        break;
    case 'delete-policy-domains':
        allowedRequestTypes("POST");
        GetPolicyHandler("delete-policy-domains");  
        break;  
    
    case 'save-policy-acf-data':
        allowedRequestTypes("POST");
        GetPolicyHandler("save-policy-acf-data");  
        break;  

    case 'check-if-all-domains-are-saved':
        allowedRequestTypes("POST");
        GetPolicyHandler("check-if-all-domains-are-saved");  
        break;

    case 'discard-policy-overall-changes':
        allowedRequestTypes("POST");
        GetPolicyHandler("discard-policy-overall-changes");  
        // break;  
        
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>