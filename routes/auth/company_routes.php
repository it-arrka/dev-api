<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/modules/company_rest_api.php';

switch($route_function_trigger_params){
    case 'addressList':
        allowedRequestTypes("GET");
        GetCompanyHandler("addressList");   
        break;
    case 'addressListFull':
        allowedRequestTypes("GET");
        GetCompanyHandler("addressListFull");   
        break;
    case 'departmentList':
        allowedRequestTypes("GET");
        GetCompanyHandler("departmentList");   
        break;
    case 'companyList':
        allowedRequestTypes("GET");
        GetCompanyHandler("companyList");   
        break;
    case 'companyLogo':
        allowedRequestTypes("GET");
        GetCompanyHandler("companyLogo");   
        break;
    case 'changecompany':
        allowedRequestTypes("POST");
        GetCompanyHandler("changecompany");   
        break;
    case 'get-all-roles':
        allowedRequestTypes("GET");
        GetCompanyHandler("get-all-roles");   
        break;

    case 'get-emails-from-role':
        allowedRequestTypes("GET");
        GetCompanyHandler("get-emails-from-role");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>