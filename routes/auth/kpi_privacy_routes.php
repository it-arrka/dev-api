<?php
require_once $_ENV['HOME_PATH'] . '/modules/kpi_privacy_rest_api.php';
switch ($route_function_trigger_params) {

    case 'get-area-for-kpi':
        allowedRequestTypes("GET");
        GetKpiPrivacyHandler("get-area-for-kpi");
        break;
    case 'get-selected-area-activity-value':
        allowedRequestTypes("POST");
        GetKpiPrivacyHandler("get-selected-area-activity-value");
        break;
    case 'modify-kpi':
        allowedRequestTypes("GET");
        GetKpiPrivacyHandler("modify-kpi");
        break;

    case 'email-by-role':
        allowedRequestTypes("GET");
        GetKpiPrivacyHandler("email-by-role");
        break;

    case 'companykpimaster-modify':
        allowedRequestTypes("POST");
        GetKpiPrivacyHandler("companykpimaster-modify");
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>