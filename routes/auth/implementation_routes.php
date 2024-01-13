<?php

require_once $_ENV['HOME_PATH'] . '/modules/implementation_rest_api.php';

switch ($route_function_trigger_params) {

    case 'get_compliance_score_for_implementation_tracker':
        allowedRequestTypes("GET");
        GetImplementationHandler("get_compliance_score_for_implementation_tracker");
        break;

    case "overall_activity_read_for_reassign":
        allowedRequestTypes("POST");
        GetImplementationHandler("overall_activity_read_for_reassign");
        break;

    case "load_notice_data_per_txn":
        allowedRequestTypes("POST");
        GetImplementationHandler("load_notice_data_per_txn");
        break;

    case "email_by_role_for_assign":
        allowedRequestTypes("POST");
        GetImplementationHandler("email_by_role_for_assign");
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>