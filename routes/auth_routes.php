<?php

//set header to avoid CORS issue in web Browser
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: x-requested-with, Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit;
}

//load common modules
require_once dirname(__DIR__) . "/config/load_common_modules.php";

// Get the requested URL path by $_SERVER['REQUEST_URI']
// Remove the "/routes/" prefix to get the remaining path
$parse_uri = parse_url($_SERVER['REQUEST_URI']);
$routeRemainingPath = substr($parse_uri['path'], strlen('/routes/'));
// Split the remaining path into parts based on "/" delimiter
$route_parts = explode('/', $routeRemainingPath);
// Determine the action or page based on the first part of the URL
$route_part_1 = "";
$route_part_2 = "";
$route_function_trigger_params = "";
if (isset($route_parts[1])) {
    $route_part_1 = $route_parts[1];
}
if (isset($route_parts[2])) {
    $route_part_2 = $route_parts[2];
}
if (isset($route_parts[3])) {
    $route_function_trigger_params = $route_parts[3];
}

if ($route_part_1 == "" || $route_part_2 == "" || $route_function_trigger_params == "") {
    http_response_code(404);
    echo json_encode(["message" => "404 Not Found"]);
    exit();
}

//Validate Token just right here
//This function will handle the Auth Header with Token
// false Parameters is to return success response. In this case we only want to return the unauthorized message
//If authorised, Lets move on to the routes
$accessTokenValidationHandler = AccessTokenValidationHandler(false);

$GLOBALS['email'] = $accessTokenValidationHandler['data']['email'];
$GLOBALS['role'] = $accessTokenValidationHandler['data']['role'];
$GLOBALS['companycode'] = $accessTokenValidationHandler['data']['companycode'];
$GLOBALS['law'] = $accessTokenValidationHandler['data']['law'];
$GLOBALS['custcode'] = get_custcode_from_email($GLOBALS['email']);
$GLOBALS['access_token'] = $accessTokenValidationHandler['data']['access_token'];

//put a switch case
switch ($route_part_2) {
    case "pageauth":
        commonSuccessResponse(200, ['message' => 'success']);
        break;
    case "company":
        require_once 'auth/company_routes.php';
        break;
    case "law":
        require_once 'auth/law_routes.php';
        break;
    case "user":
        require_once 'auth/user_routes.php';
        break;
    case "governance":
        require_once 'auth/governance_routes.php';
        break;
    case "incident":
        require_once 'auth/incident_routes.php';
        break;
    case "maturity":
        require_once 'auth/maturity_routes.php';
        break;
    case "action":
        require_once 'auth/action_routes.php';
        break;
    case "ropa":
        require_once 'auth/ropa_routes.php';
        break;
    case "scheduler":
        require_once 'auth/scheduler_routes.php';
        break;
    case "policy":
        require_once 'auth/policy_routes.php';
        break;
    case "pdam":
        require_once 'auth/pdam_routes.php';
        break;
    case "risk_register":
        require_once 'auth/risk_register_routes.php';
        break;
    case "asset":
        require_once 'auth/asset_routes.php';
        break;
    case "implementation":
        require_once 'auth/implementation_routes.php';
        break;
    case "kpi_privacy":
        require_once 'auth/kpi_privacy_routes.php';
        break;
    case "kpi_security":
        require_once 'auth/kpi_security_routes.php';
        break;
    case "configuration":
        require_once 'auth/configuration_routes.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>