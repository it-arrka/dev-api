<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/modules/company_rest_api.php';

switch($route_function_trigger_params){
    case 'companyList':
        allowedRequestTypes("GET");
        GetCompanyHandler("companyList");   
        break;
    case 'companyLogo':
        allowedRequestTypes("GET");
        GetCompanyHandler("companyLogo");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>