<?php

require_once $_ENV['HOME_PATH'].'/modules/ropa_rest_api.php';

switch($route_function_trigger_params){
    case 'save-controller-contact':
        allowedRequestTypes("POST");
        GetRopaHandler("save-controller-contact");   
        break;
    case 'save-controller-data':
        allowedRequestTypes("POST");
        GetRopaHandler("save-controller-data");   
        break;
    case 'save-processor-contact':
        allowedRequestTypes("POST");
        GetRopaHandler("save-processor-contact");   
        break;
    case 'save-processor-data':
        allowedRequestTypes("POST");
        GetRopaHandler("save-processor-data");   
        break;
    case 'get-controller-contact':
        allowedRequestTypes("GET");
        GetRopaHandler("get-controller-contact");   
        break;
    case 'get-controller-data':
        allowedRequestTypes("GET");
        GetRopaHandler("get-controller-data");   
        break;
    case 'get-controller-specific-data':
        allowedRequestTypes("GET");
        GetRopaHandler("get-controller-specific-data");   
        break;
    case 'get-processor-contact':
        allowedRequestTypes("GET");
        GetRopaHandler("get-processor-contact");   
        break;
    case 'get-processor-data':
        allowedRequestTypes("GET");
        GetRopaHandler("get-processor-data");   
        break;
    case 'get-processor-specific-data':
        allowedRequestTypes("GET");
        GetRopaHandler("get-processor-specific-data");   
        break;
    case 'get-ropa-init-data':
        allowedRequestTypes("GET");
        GetRopaHandler("get-ropa-init-data");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>