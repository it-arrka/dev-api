<?php 

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/token/token_generation.php';


switch($route_function_trigger_params){
    case 'accessToken':
        AccessTokenValidationHandler(true);   
        break;
    case 'refreshToken':
        RefreshTokenHandler();   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}


?>