<?php
require_once $_ENV['HOME_PATH'] . '/modules/risk_register_rest_api.php';

switch ($route_function_trigger_params) {

    case 'productid-read-for-cmpany':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("productid-read-for-cmpany");
        break;
    case 'internal-and-external-team-list':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("internal-and-external-team-list");
        break;
    case "comapany-email-dept-read":
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("comapany-email-dept-read");
        break;
    case 'read-asset':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("read-asset");
        break;
    case 'riskarea-from-vularea':
        allowedRequestTypes("POST");
        GetRiskRegisterHandler("riskarea-from-vularea");
        break;
    case 'impact-by-risk-n-vul':
        allowedRequestTypes("POST");
        GetRiskRegisterHandler("impact-by-risk-n-vul");
        break;
    case 'get-email-and-role':
        allowedRequestTypes("POST");
        GetRiskRegisterHandler("get-email-and-role");
        break;
    case 'riskrating-by-pb-n-impact':
        allowedRequestTypes("POST");
        GetRiskRegisterHandler("riskrating-by-pb-n-impact");
        break;
    case 'get-law-list-for-risk':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("get-law-list-for-risk");
        break;

    case 'get-law-detail-by-law-tid':
        allowedRequestTypes("POST");
        GetRiskRegisterHandler("get-law-detail-by-law-tid");
        break;

    case 'get-law-detail-by-law-tid':
        allowedRequestTypes("POST");
        GetRiskRegisterHandler("get-law-detail-by-law-tid");
        break;

    case 'show-define-risk-all':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("show-define-risk-all");
        break;

    case 'vularea-read-from-company':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("vularea-read-from-company");
        break;

    case 'define-risk-data-save':
        allowedRequestTypes("POST");
        GetRiskRegisterHandler("define-risk-data-save");
        break;

    case 'fetch-risk-report':
        allowedRequestTypes("GET");
        GetRiskRegisterHandler("fetch-risk-report");
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>