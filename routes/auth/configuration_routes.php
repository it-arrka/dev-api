<?php
require_once $_ENV['HOME_PATH'] . '/modules/configuration_rest_api.php';
switch ($route_function_trigger_params) {

    // Action Module Config 
    case 'action-config-generate-tid':
        allowedRequestTypes("GET");
        GetConfigHandler("action-config-generate-tid");
        break;

    case 'config-action-data-check':
        allowedRequestTypes("GET");
        GetConfigHandler("config-action-data-check");
        break;

    case 'get-default-config-define-action':
        allowedRequestTypes("GET");
        GetConfigHandler("get-default-config-define-action");
        break;

    case 'action-change-data-save':
        allowedRequestTypes("POST");
        GetConfigHandler("action-change-data-save");
        break;

    case 'action-config-final-submit':
        allowedRequestTypes("POST");
        GetConfigHandler("action-config-final-submit");
        break;

    case 'role-by-name':
        allowedRequestTypes("POST");
        GetConfigHandler("role-by-name");
        break;

    // Asset Register
    case 'asset-register-generate-tid':
        allowedRequestTypes("GET");
        GetConfigHandler("asset-register-generate-tid");
        break;

    case 'load-asset-config-data':
        allowedRequestTypes("GET");
        GetConfigHandler("load-asset-config-data");
        break;

    case 'check-if-data-exist-in-asset-config':
        allowedRequestTypes("GET");
        GetConfigHandler("check-if-data-exist-in-asset-config");
        break;

    case 'save-asset-config':
        allowedRequestTypes("POST");
        GetConfigHandler("save-asset-config");
        break;

    case 'asset-save-config-final-submit':
        allowedRequestTypes("POST");
        GetConfigHandler("asset-save-config-final-submit");
        break;



    // Change Management Config
    case 'change-manage-generate-tid':
        allowedRequestTypes("GET");
        GetConfigHandler("change-manage-generate-tid");
        break;

    case 'change-config-check-all-data-filled':
        allowedRequestTypes("GET");
        GetConfigHandler("change-config-check-all-data-filled");
        break;

    case 'get-default-config-change':
        allowedRequestTypes("GET");
        GetConfigHandler("get-default-config-change");
        break;

    case 'change-no-of-min-approver-cab':
        allowedRequestTypes("POST");
        GetConfigHandler("change-no-of-min-approver-cab");
        break;

    case 'change-config-final-submit':
        allowedRequestTypes("POST");
        GetConfigHandler("change-config-final-submit");
        break;


    // DSRR Config
    case 'dsrr-config-generate-tid':
        allowedRequestTypes("GET");
        GetConfigHandler("dsrr-config-generate-tid");
        break;

    case 'get-default-config':
        allowedRequestTypes("GET");
        GetConfigHandler("get-default-config");
        break;

    case 'dssr-config-data-save':
        allowedRequestTypes("POST");
        GetConfigHandler("dssr-config-data-save");
        break;

    case 'dssr-config-check-all-data-filled':
        allowedRequestTypes("POST");
        GetConfigHandler("dssr-config-check-all-data-filled");
        break;

    case 'dssr-config-final-submit':
        allowedRequestTypes("POST");
        GetConfigHandler("dssr-config-final-submit");
        break;


    // Breach Config
    case 'breach-config-generate-tid':
        allowedRequestTypes("GET");
        GetConfigHandler("breach-config-generate-tid");
        break;

    case 'company-category-master-write':
        allowedRequestTypes("POST");
        GetConfigHandler("company-category-master-write");
        break;

    case 'company-tatmaster-write':
        allowedRequestTypes("POST");
        GetConfigHandler("company-tatmaster-write");
        break;

    case 'company-iamaster-write':
        allowedRequestTypes("POST");
        GetConfigHandler("company-iamaster-write");
        break;






    // KPI Config
    case 'kpi-config-generate-tid':
        allowedRequestTypes("GET");
        GetConfigHandler("kpi-config-generate-tid");
        break;

    case 'arrka-kpimaster-read':
        allowedRequestTypes("GET");
        GetConfigHandler("arrka-kpimaster-read");
        break;

    case 'cisoconfig-data-save':
        allowedRequestTypes("POST");
        GetConfigHandler("cisoconfig-data-save");
        break;

    case 'incident-screen-1-final-submit':
        allowedRequestTypes("POST");
        GetConfigHandler("incident-screen-1-final-submit");
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>