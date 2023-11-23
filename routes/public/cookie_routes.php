<?php 

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params



switch($route_function_trigger_params){
    case 'setCookies':
        include $_ENV['HOME_PATH'].'/cookie/setCookie.php'; 
        break;
    case 'getCookies':
        include $_ENV['HOME_PATH'].'/cookie/getCookie.php'; 
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}


?>