<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'].'/modules/incident_rest_api.php';
require_once $_ENV['HOME_PATH'].'/modules/compliance_score/incident_rest_api.php';

switch($route_function_trigger_params){
    case 'list':
        allowedRequestTypes("GET");
        GetIncidentHandler("list");   
        break;
    case 'subcategory':
        allowedRequestTypes("GET");
        GetIncidentHandler("subcategory");   
        break;
    case 'analyse':
        allowedRequestTypes("GET");
        GetIncidentHandler("analyse");   
        break;
    case 'resolve':
        allowedRequestTypes("GET");
        GetIncidentHandler("resolve");   
        break;
    case 'investigate':
        allowedRequestTypes("GET");
        GetIncidentHandler("investigate");   
        break;
    case 'report':
        allowedRequestTypes("GET");
        GetIncidentHandler("report");   
        break;
    case 'initiate':
        allowedRequestTypes("POST");
        GetIncidentHandler("initiate");   
        break;
    case 'save-incident-analyze-security':
        allowedRequestTypes("POST");
        GetIncidentHandler("save-incident-analyze-security");   
        break;
    case 'save-incident-analyze-privacy':
        allowedRequestTypes("POST");
        GetIncidentHandler("save-incident-analyze-privacy");   
        break;
    case 'save-incident-resolve-security':
        allowedRequestTypes("POST");
        GetIncidentHandler("save-incident-resolve-security");   
        break;
    case 'save-incident-resolve-privacy':
        allowedRequestTypes("POST");
        GetIncidentHandler("save-incident-resolve-privacy");   
        break;

    case 'save-incident-investigate-security':
        allowedRequestTypes("POST");
        GetIncidentHandler("save-incident-investigate-security");   
        break;
    case 'save-incident-investigate-privacy':
        allowedRequestTypes("POST");
        GetIncidentHandler("save-incident-investigate-privacy");   
        break;
    default:
        http_response_code(404); 
        echo json_encode(["message"=>"404 Not Found"]);
        exit();
}

?>