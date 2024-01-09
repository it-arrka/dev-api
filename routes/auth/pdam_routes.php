<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/modules/pdam_rest_api.php';

switch($route_function_trigger_params){
    case 'get-type-of-data':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-type-of-data");   
        break;
    case 'get-product-services':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-product-services");   
        break;

    case 'add-product-services':
        allowedRequestTypes("POST");
        GetPDAMHandler("add-product-services");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>