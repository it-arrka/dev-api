<?php

//This values comes from routes.php .. $const_api_path/$route_function_trigger_params
require_once $_ENV['HOME_PATH'] . '/modules/pdam_rest_api.php';

switch ($route_function_trigger_params) {

    case 'get-type-of-data':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-type-of-data");
        break;

    case 'get-product-services':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-product-services");
        break;

    case 'add-product-services':
        allowedRequestTypes("POST");
        GetPDAMHandler("add-product-services");
        break;

    case 'get-dashboard-data':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-dashboard-data");
        break;

    case 'get-pending-with-for-pdam':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-pending-with-for-pdam");
        break;

    case 'load-client-department':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-client-department");
        break;

    case 'load-user-from-client':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-user-from-client");
        break;

    case 'load-channel-name':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-channel-name");
        break;

    case 'section-2-channel-dept-validation':
        allowedRequestTypes("POST");
        GetPDAMHandler("section-2-channel-dept-validation");
        break;

    case 'section-2-data-validation':
        allowedRequestTypes("POST");
        GetPDAMHandler("section-2-data-validation");
        break;

    case 'section-2-submit':
        allowedRequestTypes("POST");
        GetPDAMHandler("section-2-submit");
        break;

    case 'load-client-role':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-client-role");
        break;

    case 'section-1-submit':
        allowedRequestTypes("POST");
        GetPDAMHandler("section-1-submit");
        break;

    case 'get-pd-element-list':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-pd-element-list");
        break;

    case 'get-pd-element-list-by-category':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-pd-element-list-by-category");
        break;

    case 'save-section-3-form-1-row':
        allowedRequestTypes("POST");
        GetPDAMHandler("save-section-3-form-1-row");
        break;

    case 'load-section-3-form-1-data':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-section-3-form-1-data");
        break;

    case 'delete-section-3-form-1-row':
        allowedRequestTypes("POST");
        GetPDAMHandler("delete-section-3-form-1-row");
        break;

    case 'next-phase-update-section-3':
        allowedRequestTypes("POST");
        GetPDAMHandler("next-phase-update-section-3");
        break;

    case 'get-purpose-list-from-master':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-purpose-list-from-master");
        break;

    case 'load-section-3-form-2-data':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-section-3-form-2-data");
        break;

    case 'load-section-3-form-3-data':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-section-3-form-3-data");
        break;

    case 'save-section-3-form-3-row':
        allowedRequestTypes("POST");
        GetPDAMHandler("save-section-3-form-3-row");
        break;

    case 'save-section-3-form-4-row-modal':
        allowedRequestTypes("POST");
        GetPDAMHandler("save-section-3-form-4-row-modal");
        break;

    case 'load-section-3-form-4-pre-data-specific':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-section-3-form-4-pre-data-specific");
        break;

    case 'save-section-3-form-2-row':
        allowedRequestTypes("POST");
        GetPDAMHandler("save-section-3-form-2-row");
        break;

    case 'check-if-asset-storage-mapping-is-done':
        allowedRequestTypes("GET");
        GetPDAMHandler("check-if-asset-storage-mapping-is-done");
        break;

    case 'get-asset-list':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-asset-list");
        break;

    case 'get-pd-category-list-for-client':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-pd-category-list-for-client");
        break;

    case 'load-section-3-form-1-data-for-edit':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-section-3-form-1-data-for-edit");
        break;

    case 'update-pdam-section-form-1':
        allowedRequestTypes("POST");
        GetPDAMHandler("update-pdam-section-form-1");
        break;

    case 'get-pd-details-by-pd-category':
        allowedRequestTypes("GET");
        GetPDAMHandler("get-pd-details-by-pd-category");
        break;

    case 'pdam-section-3-new-pd-element':
        allowedRequestTypes("POST");
        GetPDAMHandler("pdam-section-3-new-pd-element");
        break;

    case 'load-section-3-form-1-data-asset-grouped':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-section-3-form-1-data-asset-grouped");
        break;

    case 'load-section-3-from-1-data-for-report':
        allowedRequestTypes("GET");
        GetPDAMHandler("load-section-3-from-1-data-for-report");
        break;

    case 'pdam-section-department-process':
        allowedRequestTypes("POST");
        GetPDAMHandler("pdam-section-department-process");
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>