<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $const_api_path.'/notice_rest_api.php';

switch($route_function_trigger_params){
    case 'getActivity':
        $output=get_activity();   
        echo json_encode($output);     
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>