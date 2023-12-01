<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/modules/login_rest_api.php';
require_once $_ENV['HOME_PATH'].'/modules/signup_rest_api.php';
require_once $_ENV['HOME_PATH'].'/modules/mail/mail_rest_api.php';


switch($route_function_trigger_params){
    case 'login':
        UserLoginHandler("login");   
        break;
    case 'forgotPassword':
        UserLoginHandler("forgotPassword");   
        break;
    case 'forgotPasswordLinkVerify':
        UserLoginHandler("forgotPasswordLinkVerify");   
        break;
    case 'forgotPasswordSetPassword':
        UserLoginHandler("forgotPasswordSetPassword");   
        break;
    case 'promocodeDetails':
        UserSignupHandler('promocodeDetails');   
        break;
    case 'signupLawlist':
        UserSignupHandler('signupLawlist');   
        break;
    case 'signup':
        UserSignupHandler('signup');   
        break;
    case 'signupVerifyLink':
        UserSignupHandler('signupVerifyLink');   
        break;
    case 'signupSetPassword':
        UserSignupHandler('signupSetPassword');   
        break;
    case 'signupEmailVerified':
        UserSignupHandler('signupEmailVerified');   
        break;
    case 'logout':
        UserLogoutHandler();   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>