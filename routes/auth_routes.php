<?php 

//set header to avoid CORS issue in Browser
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // Only POST requests are allowed
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" =>"Method not allowed"]);
    exit();
}

//load common modules
require_once dirname(__DIR__)."/config/load_common_modules.php";

// Get the requested URL path by $_SERVER['REQUEST_URI']
// Remove the "/routes/" prefix to get the remaining path
$routeRemainingPath = substr($_SERVER['REQUEST_URI'], strlen('/routes/'));
// Split the remaining path into parts based on "/" delimiter
$route_parts = explode('/', $routeRemainingPath);
// Determine the action or page based on the first part of the URL
$route_part_1=""; $route_part_2=""; $route_function_trigger_params=""; $const_api_path="";
if(isset($route_parts[1])){ $route_part_1=$route_parts[1]; }
if(isset($route_parts[2])){ $route_part_2=$route_parts[2]; }
if(isset($route_parts[3])){ $route_function_trigger_params=$route_parts[3]; }

if($route_part_1=="" || $route_part_2=="" || $route_function_trigger_params==""){ 
    http_response_code(404); 
    echo json_encode(["message"=>"404 Not Found"]);
    exit(); 
}

//Validate Token just right here
//This function will handle the Auth Header with Token
// false Parameters is to return success response. In this case we only want to return the unauthorized message
//If authorised, Lets move on to the routes
access_token_handler(false);

//put a switch case
switch($route_part_2){
    case "notification":
        $const_api_path = dirname(__DIR__).'/modules';
        require_once 'auth/activity_routes.php';
        break;
    case "governance":
        require_once 'auth/signup_routes.php';
        break;
    default:
         http_response_code(404); 
         echo json_encode(["message"=>"404 Not Found"]);
         exit();
}

?>