<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/modules/maturity_rest_api.php';
require_once $_ENV['HOME_PATH'].'/modules/maturity_rest_api_old.php';
require_once $_ENV['HOME_PATH'].'/modules/acf/acf_common_api.php';
require_once $_ENV['HOME_PATH'].'/modules/actions/action_rest_api.php';
require_once $_ENV['HOME_PATH'].'/common/upload_api.php';

switch($route_function_trigger_params){
    case 'assessmentList':
        allowedRequestTypes("GET");
        GetMaturityHandler("assessmentList");   
        break;
    case 'questionList':
        allowedRequestTypes("GET");
        GetMaturityHandler("questionList");   
        break;
    case 'report':
        allowedRequestTypes("GET");
        GetMaturityHandler("report");   
        break;
    case 'initiate':
        allowedRequestTypes("POST");
        GetMaturityHandler("initiate");   
        break;
    case 'initiateAssessment':
        allowedRequestTypes("POST");
        GetMaturityHandler("initiateAssessment");   
        break;
    case 'tempSaveResponse':
        allowedRequestTypes("POST");
        GetMaturityHandler("tempSaveResponse");   
        break;
    case 'saveAssessment':
        allowedRequestTypes("POST");
        GetMaturityHandler("saveAssessment");   
        break;
    case 'roleUserListByLaw':
        allowedRequestTypes("POST");
        GetMaturityHandler("roleUserListByLaw");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>