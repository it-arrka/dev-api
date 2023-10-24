<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // Only POST requests are allowed
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" =>"Method Not Allowed"]);
    exit();
}

//load common modules
require_once dirname(__DIR__)."/config/load_common_modules.php";

// Get the requested URL path by $_SERVER['REQUEST_URI']
// Remove the "/routes/" prefix to get the remaining path
$parse_uri = parse_url($_SERVER['REQUEST_URI']);
$routeRemainingPath = substr($parse_uri['path'], strlen('/routes/'));
// Split the remaining path into parts based on "/" delimiter
$route_parts = explode('/', $routeRemainingPath);
// Determine the action or page based on the first part of the URL
$route_part_1=""; $route_part_2=""; $route_function_trigger_params="";
if(isset($route_parts[1])){ $route_part_1=$route_parts[1]; }
if(isset($route_parts[2])){ $route_part_2=$route_parts[2]; }
if(isset($route_parts[3])){ $route_function_trigger_params=$route_parts[3]; }

if($route_part_1=="" || $route_part_2=="" || $route_function_trigger_params==""){ 
    http_response_code(404); 
    echo json_encode(["message"=>"404 Not Found"]);
    exit(); 
}

// http_response_code(200); echo json_encode(["message"=>"404 Not Found"]); exit(); 

//put a switch case
switch($route_part_2){
    case "token":
        require_once 'public/token_routes.php';
        break;    
    case "user":
        require_once 'public/user_routes.php';
        break;
    default:
         http_response_code(404); 
         echo json_encode(["message"=>"404 Not Found"]);
         exit();
}

?>