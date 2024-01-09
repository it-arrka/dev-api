<?php

require_once $_ENV['HOME_PATH'] . '/modules/asset_rest_api.php';

switch ($route_function_trigger_params) {

    case 'load_asset_config_data':
        allowedRequestTypes("GET");
        GetAssetHandler("load_asset_config_data");
        break;

    case 'asset_sub_cat':
        allowedRequestTypes("GET");
        GetAssetHandler("asset_sub_cat");
        break;

    case 'asset_data_register':
        allowedRequestTypes("POST");
        GetAssetHandler("asset_data_register");
        break;

    case 'asset_view_data':
        allowedRequestTypes("GET");
        GetAssetHandler("asset_view_data");
        break;

    case 'asset_del_id':
        allowedRequestTypes("POST");
        GetAssetHandler("asset_del_id");
        break;

    case 'data_row_save':
        allowedRequestTypes("POST");
        GetAssetHandler("data_row_save");
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "404 Not Found"]);
        exit();
}

?>