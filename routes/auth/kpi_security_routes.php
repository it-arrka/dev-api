<?php
require_once $_ENV['HOME_PATH'] . '/modules/kpi_security_rest_api.php';
switch ($route_function_trigger_params) {

    case 'get-kpi-for-kpi':
        allowedRequestTypes("POST");
        GetKpiSecurityHandler("get-kpi-for-kpi");
        break;

    case 'get-kpi-reference-kpi':
        allowedRequestTypes("POST");
        GetKpiSecurityHandler("get-kpi-reference-kpi");
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}


?>