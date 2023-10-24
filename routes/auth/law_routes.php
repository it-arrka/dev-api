<?php

require_once $_ENV['HOME_PATH'].'/modules/law_rest_api.php';

switch($route_function_trigger_params){
    case 'lawList':
        allowedRequestTypes("GET");
        GetLawHandler("lawList");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>