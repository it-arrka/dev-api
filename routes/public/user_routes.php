<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/modules/login_rest_api.php';


switch($route_function_trigger_params){
    case 'login':
        UserLoginHandler();   
        break;
    // case 'signup':
    //     UserLoginHandler();   
    //     break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>