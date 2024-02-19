<?php
include 'rest_common_api.php';
function GetPDAMHandler($funcCallType)
{
  try {

    switch ($funcCallType) {
      case "get-type-of-data":
        if (isset($GLOBALS['companycode'])) {
          $output = get_type_of_data($GLOBALS['companycode']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-product-services":
        if (isset($GLOBALS['companycode'])) {
          $output = get_product_services($GLOBALS['companycode']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      //Write API
      case "add-product-services":
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = add_new_product_and_services($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "initiate":
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = pdam_initiate_submit($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $GLOBALS['alw'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-dashboard-data":
        $page = 1;
        $limit = 10;
        $day = "ALL";
        if (isset($_GET['page'])) {
          $page = (int) $_GET['page'];
        }
        if (isset($_GET["limit"])) {
          $limit = (int) $_GET["limit"];
        }
        if (isset($_GET["day"])) {
          $day = $_GET["day"];
        }

        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = get_dashboard_data($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $limit, $page, $day);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-pending-with-for-pdam":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($_GET['config_tid']) && isset($_GET['section_1_tid'])) {
          $output = get_pending_with_for_pdam($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $_GET['config_tid'], $_GET['section_1_tid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "load-client-department":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = load_client_department($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'load-user-from-client':
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = load_user_from_client($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'load-channel-name':
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = load_channel_name($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'section-2-channel-dept-validation':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($json['channel_dept_val']) && isset($json['classname'])) {
          $output = section_2_channel_dept_validation($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'section-2-data-validation':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($json['channel_name']) && isset($json['dept_name']) && isset($json['owner_name']) && isset($json['section_1_tid']) && isset($json['config_tid'])) {
          $output = section_2_data_validation($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'section-2-submit':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($GLOBALS['custcode'])) {
          $output = section_2_submit($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $json['data_final'], $json['config_tid'], $json['section_1_tid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'load-client-role':
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = load_client_role($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'section-1-submit':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($GLOBALS['custcode']) && isset($json)) {
          $output = section_1_submit($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $json['pdam_name'], $json['type_sel'], $json['product_sel'], $json['owner'], $json['pns_sel'], $json['txn_id_incident']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case 'get-pd-element-list':
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($_GET['typeofdata']) && isset($_GET['section_2_tid']) && isset($_GET['section_1_tid']) && isset($_GET['config_tid']) && isset($_GET['application_name'])) {
          $output = get_pd_element_list($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $_GET['typeofdata'], $_GET['section_2_tid'], $_GET['section_1_tid'], $_GET['config_tid'], $_GET['application_name']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'get-pd-element-list-by-category':
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($_GET['typeofdata']) && isset($_GET['section_2_tid']) && isset($_GET['pdcategory']) && isset($_GET['application_name'])) {
          $output = get_pd_element_list_by_category($_GET['typeofdata'], $_GET['pdcategory'], $GLOBALS['companycode'], $_GET['section_2_tid'], $_GET['application_name']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'save-section-3-form-1-row':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (
          isset(
          $GLOBALS['companycode'],
          $GLOBALS['email'],
          $GLOBALS['role'],
          $json['data'],
          $json['config_tid'],
          $json['section_1_tid'],
          $json['section_2_tid'],
          $GLOBALS['custcode'],
          $json['unique_id'],
          $json['req_type']
        )
        ) {
          $output = save_section_3_form_1_row(
            $json['data'],
            $json['config_tid'],
            $json['section_1_tid'],
            $json['section_2_tid'],
            $GLOBALS['companycode'],
            $GLOBALS['email'],
            $GLOBALS['role'],
            $GLOBALS['custcode'],
            $json['unique_id'],
            $json['req_type']
          );

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'load-section-3-form-1-data':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']
        )
        ) {
          $output = load_section_3_form_1_data($GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'delete-section-3-form-1-row':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (
          isset(
          $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $json['del_id']
        )
        ) {
          $output = delete_section_3_form_1_row($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $json['del_id']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'next-phase-update-section-3':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (
          isset(
          $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $json['form_id'], $json['data'], $json['data_type']
        )
        ) {
          $output = next_phase_update_section_3($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $json['form_id'], $json['data'], $json['data_type']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'get-purpose-list-from-master':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['typeofdata'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']
        )
        ) {
          $output = get_purpose_list_from_master($GLOBALS['companycode'], $_GET['typeofdata'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'load-section-3-form-2-data':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']
        )
        ) {
          $output = load_section_3_form_2_data($GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'load-section-3-form-3-data':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['data_type']
        )
        ) {
          $output = load_section_3_form_3_data($GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['data_type'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'save-section-3-form-3-row':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (
          isset(
          $json['data'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']
        )
        ) {
          $output = save_section_3_form_3_row(
            $json['data'],
            $json['config_tid'],
            $json['section_1_tid'],
            $json['section_2_tid'],
            $GLOBALS['companycode'],
            $GLOBALS['email'],
            $GLOBALS['role'],
            $GLOBALS['custcode']
          );

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'save-section-3-form-4-row-modal':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (
          isset(
          $json['data'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']
        )
        ) {
          $output = save_section_3_form_4_row_modal(
            $json['data'],
            $json['config_tid'],
            $json['section_1_tid'],
            $json['section_2_tid'],
            $GLOBALS['companycode'],
            $GLOBALS['email'],
            $GLOBALS['role'],
            $GLOBALS['custcode']
          );

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'load-section-3-form-4-pre-data-specific':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['typeofdata'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']
        )
        ) {
          $output = load_section_3_form_4_pre_data_specific($GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['typeofdata'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'save-section-3-form-2-row':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (
          isset(
          $json['data'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']
        )
        ) {
          $output = save_section_3_form_2_row(
            $json['data'],
            $json['config_tid'],
            $json['section_1_tid'],
            $json['section_2_tid'],
            $GLOBALS['companycode'],
            $GLOBALS['email'],
            $GLOBALS['role'],
            $GLOBALS['custcode']
          );

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'check-if-asset-storage-mapping-is-done':
        if (
          isset(
          $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['typeofdata'], $_GET['channel_name']
        )
        ) {
          $output = check_if_asset_storage_mapping_is_done($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['typeofdata'], $_GET['channel_name']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'get-asset-list':
        if (
          isset(
          $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']
        )
        ) {
          $output = asset_name_read_with_dept($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'get-pd-category-list-for-client':
        if (
          isset(
          $_GET['typeofdata'], $GLOBALS['companycode']
        )
        ) {
          $output = get_pd_category_list_for_client($_GET['typeofdata'], $GLOBALS['companycode']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'load-section-3-form-1-data-for-edit':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']
        )
        ) {
          $output = load_section_3_form_1_data_for_edit($GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'update-pdam-section-form-1':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }

        if (
          isset(
          $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $json['data'], $json['config_tid'], $json['section_1_tid'], $json['section_2_tid'], $json['typeofdata'], $json['channel_name']
        )
        ) {
          $output = update_pdam_section_form_1(
            $GLOBALS['companycode'],
            $GLOBALS['email'],
            $GLOBALS['role'],
            $GLOBALS['custcode'],
            $json['data'],
            $json['config_tid'],
            $json['section_1_tid'],
            $json['section_2_tid'],
            $json['typeofdata'],
            $json['channel_name']
          );

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'get-pd-details-by-pd-category':
        if (
          isset(
          $_GET['pdcategory'], $_GET['typeofdata']
        )
        ) {
          $output = pd_details_by_pd_category($_GET['pdcategory'], $_GET['typeofdata']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'pdam-section-3-new-pd-element':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $name = get_name_from_email($GLOBALS['email']);
        if (
          isset(
          $json['new_pdelement'], $json['new_pdcategory'], $json['new_pdsupercategory'], $json['new_pdclassification'], $json['new_typeofdata_afm'], $GLOBALS['email'], $name, $GLOBALS['role'], $GLOBALS['custcode'], $GLOBALS['companycode']
        )
        ) {
          $output = pdam_section_3_new_pd_element(
            $json['new_pdelement'],
            $json['new_pdcategory'],
            $json['new_pdsupercategory'],
            $json['new_pdclassification'],
            $json['new_typeofdata_afm'],
            $GLOBALS['email'],
            $name,
            $GLOBALS['role'],
            $GLOBALS['custcode'],
            $GLOBALS['companycode']
          );

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'load-section-3-form-1-data-asset-grouped':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['fetch_data_type']
        )
        ) {
          $output = load_section_3_form_1_data_asset_grouped($GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid'], $_GET['fetch_data_type']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'load-section-3-from-1-data-for-report':
        if (
          isset(
          $GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']
        )
        ) {
          $output = load_section_3_form_1_data_for_report($GLOBALS['companycode'], $_GET['config_tid'], $_GET['section_1_tid'], $_GET['section_2_tid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      case 'pdam-section-department-process':
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $name = get_name_from_email($GLOBALS['email']);
        // echo($GLOBALS['custcode']);
        // echo('name');
        if (
          isset(
          $json['department_name'], $GLOBALS['companycode'], $GLOBALS['email'], $name, $GLOBALS['role'], $GLOBALS['custcode']
        )
        ) {
          $output = pdam_section_department_process(
            $json['department_name'],
            $GLOBALS['companycode'],
            $GLOBALS['email'],
            $name,
            $GLOBALS['role'],
            $GLOBALS['custcode']
          );

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            break;
          } else {
            catchErrorHandler(
              $output['code'],
              [
                "message" => $output['message'],
                "error" => $output['error']
              ]
            );
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }

      default:
        catchErrorHandler(400, ["message" => E_INV_REQ, "error" => ""]);
        break;
    }
  } catch (Exception $e) {
    catchErrorHandler($output['code'], ["message" => "", "error" => $e->getMessage()]);
  }
}

//add_new_product_and_service-------------------------------------
function add_new_product_and_services($companycode, $email, $role, $data)
{
  try {
    global $session;

    if (!isset($data['product'])) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Missing Product"];
      exit();
    }

    $product = escape_input($data['product']);

    if ($product == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Product/Service"];
      exit();
    }

    $columns = [
      "companycode",
      "status",
      "createdate",
      "id",
      "effectivedate",
      "filleremail",
      "fillerrole",
      "product"
    ];
    $columns_data = [
      $companycode,
      "1",
      new \Cassandra\Timestamp(),
      "product",
      new \Cassandra\Timestamp(),
      $email,
      $role,
      $product
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "product_and_services", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    return $table_insert;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function pdam_initiate_submit($companycode, $email, $role, $custcode, $activeLaw, $data)
{
  try {
    global $session;
    $required_keys = [
      "pdam_name",
      "type_of_data",
      "owner",
      "product_and_service",
      "product"
    ];

    //check if array is valid
    if (!checkKeysExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    //check value incoming
    if (!checkValueExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => implode(", ", $required_keys_val) . " value is mandatory"];
      exit();
    }

    $type_sel = escape_input($data['type_of_data']);
    $product_sel = escape_input($data['product']);
    $pdam_name = escape_input($data['pdam_name']);
    $owner = escape_input($data['owner']);
    $pns_sel = escape_input($data['product_and_service']);

    $config_tid = get_active_config_txn_id($companycode, "pdam");
    if ($config_tid == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid configuration"];
      exit();
    }

    $section_1_tid = new \Cassandra\Uuid();
    $timestamp = new \Cassandra\Timestamp();
    $columns = [
      "companycode",
      "status",
      "createdate",
      "effectivedate",
      "section_1_tid",
      "config_tid",
      "typeofdata",
      "product",
      "pdam_name",
      "owner",
      "product_and_services"
    ];
    $columns_data = [
      $companycode,
      "1",
      $timestamp,
      $timestamp,
      (string) $section_1_tid,
      $config_tid,
      $type_sel,
      $product_sel,
      $pdam_name,
      $owner,
      $pns_sel
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "pdam_section_1", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    if (!$table_insert['success']) {
      return $table_insert;
      exit();
    }
    //Create notice

    $email_role_array = module_assign_email_role_list("PG075", "create", $companycode);
    foreach ($email_role_array as $em_role) {
      $notice_link = "pdam_section_2.php?tid=" . $config_tid . "&s1_tid=" . (string) $section_1_tid;
      $notice_output = notice_write("PDAM201", $companycode, $email, $role, $notice_link, $em_role['email'], $em_role['role'], $pdam_name, (string) $section_1_tid);
    }
    update_landing_module($companycode, $email, $role, $custcode, $activeLaw, "8");

    $arr_return = ["code" => 200, "success" => true, "data" => ""];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

//get APIs
function get_type_of_data($companycode)
{
  try {
    global $session;
    $arr = ["Customer", "Employee", "Vendor"];
    $result = $session->execute($session->prepare("SELECT type_of_data FROM pdam_type_of_data WHERE companycode=?  AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    foreach ($result as $row) {
      array_push($arr, $row['type_of_data']);
    }
    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_product_services($companycode)
{
  try {
    global $session;
    $arr = [];
    $result = $session->execute($session->prepare("SELECT product FROM product_and_services WHERE companycode=? AND status=? AND id=?"), array('arguments' => array($companycode, "1", "product")));
    foreach ($result as $row) {
      array_push($arr, $row['product']);
    }
    sort($arr);
    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_dashboard_data($companycode, $email, $role, $limit, $page, $day)
{
  try {
    global $session;

    //timestamp
    $timestamp = 0;
    if (strtoupper($day) != "ALL") {
      $last_day = (int) $day;
      if ($last_day < 1) {
        $last_day = 1;
      }
      $timestamp = strtotime("-" . $last_day . " days");
    }

    //validate limit and page
    if ($limit < 1) {
      $limit = 1;
    }
    if ($page < 1) {
      $page = 1;
    }
    $page = $page - 1;
    $arr = [];
    $total_index = 0;
    // $arr = array();

    $arr_txn = [];
    $arr_txn_final = [];
    // result_txn
    $result_txn = $session->execute($session->prepare("SELECT section_1_tid,createdate FROM pdam_section_1 WHERE companycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    foreach ($result_txn as $row_txn) {
      $createdate_str = (string) $row_txn['createdate'];
      $arr_txn[(string) $row_txn['section_1_tid'] . "|new"] = (int) $createdate_str;
    }

    $res_txn = $session->execute($session->prepare('SELECT invcusttranscode,createdate FROM transcodeinvcust WHERE invcompanycode=? ALLOW FILTERING'), array('arguments' => array($companycode)));
    foreach ($res_txn as $r_txn) {
      $createdate_str = (string) $r_txn['createdate'];
      $arr_txn[(string) $r_txn['invcusttranscode'] . "|old"] = (int) $createdate_str;
    }
    arsort($arr_txn);

    //divide array and find specific chunks
    $array_chunk = array_chunk($arr_txn, $limit, true);
    $total_index = count($array_chunk);
    $arr_final_txn = $array_chunk[$page];

    foreach ($arr_final_txn as $key_id => $value) {
      $id_arr = explode("|", $key_id);
      if ($id_arr[1] == 'new') {
        //pdam_section_1
        //NEW Pdam
        $result = $session->execute($session->prepare("SELECT pdam_name,product,owner,typeofdata,product_and_services,config_tid,section_1_tid,createdate,modifydate FROM pdam_section_1 WHERE section_1_tid=? ALLOW FILTERING"), array('arguments' => array($id_arr[0])));
        foreach ($result as $row) {
          $createdate_str = (string) $row['createdate'];
          if ($createdate_str == "") {
            $row['createdate'] = "-";
          } else {
            $row['createdate'] = date("d-m-Y", (int) $createdate_str / 1000);
          }
          $modifydate_str = (string) $row['modifydate'];
          if ($modifydate_str == "") {
            $row['modifydate'] = "-";
          } else {
            $row['modifydate'] = date("d-m-Y", (int) $modifydate_str / 1000);
          }
          $row['status'] = "Section3";
          $report_status = 0;
          $pending_with_flag = 0;
          $result_s2 = $session->execute($session->prepare("SELECT channel_name,form_status FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $row['config_tid'], $row['section_1_tid'])));
          if ($result_s2->count() > 0) {
            $row['status'] = "Section-3";
            foreach ($result_s2 as $row_s2) {
              if ($row_s2['form_status'] == '6') {
                $report_status = 1;
              } else {
                $pending_with_flag = 1;
              }
              if ($report_status == 1 && $pending_with_flag == 1) {
                break;
              }
            }
          } else {
            $pending_with_flag = 1;
          }
          $row['report_status'] = $report_status;
          $row['pending_with_flag'] = $pending_with_flag;
          $row['pdam_version'] = "new";
          $arr[] = $row;
        }
      } else {

        //Old PDAM
        $res = $session->execute($session->prepare('SELECT * FROM transcodeinvcust WHERE invcusttranscode=?'), array('arguments' => array(new \Cassandra\Uuid($id_arr[0]))));
        foreach ($res as $row) {
          if ($row['version'] == '') {
            $row['version'] = '0';
          }
          $createdate_str = (string) $row['createdate'];
          if ($createdate_str == "") {
            $createdate = "-";
          } else {
            $createdate = date("d-m-Y", (int) $createdate_str / 1000);
          }
          $modifydate_str = (string) $row['modifydate'];
          if ($modifydate_str == "") {
            $modifydate = "-";
          } else {
            $modifydate = date("d-m-Y", (int) $modifydate_str / 1000);
          }
          if ($row['txn_name'] == "") {
            $row['txn_name'] = 'NA';
          }
          $arr[] = array(
            "pdam_version" => "old",
            "txn_id" => (string) $row['invcusttranscode'],
            "status" => $row['status'],
            "typeofdata" => $row['typeofdata'],
            "txn_name" => $row['txn_name'],
            "txn_type" => $row['txn_type'],
            "version" => $row['version'],
            "createdate" => $createdate,
            "modifydate" => $modifydate,
            "sorting" => (int) $createdate_str
          );
        }

      }
    }

    $arr_final = [
      "limit" => $limit,
      "day" => $day,
      "page" => $page + 1,
      "pagination" => $total_index,
      "data" => $arr
    ];

    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr_final];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_pending_with_for_pdam($companycode, $email, $role, $config_tid, $section_1_tid)
{
  try {
    global $session;
    $arr = [];

    if ($config_tid == "" || $section_1_tid == "") {
      $arr_return = ["success" => false, "message" => "Invalid transaction", "data" => ""];
      return $arr_return;
      exit();
    }

    $result_tid = $session->execute($session->prepare("SELECT typeofdata,pdam_name FROM pdam_section_1 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $config_tid, $section_1_tid)));
    if ($result_tid->count() == 0) {
      $arr_return = ["success" => false, "message" => "Invalid transaction", "data" => ""];
      return $arr_return;
      exit();
    }
    $typeofdata = $result_tid[0]['typeofdata'];
    $pdam_name = $result_tid[0]['pdam_name'];

    $result = $session->execute($session->prepare("SELECT section_2_tid,channel_name FROM pdam_section_2 WHERE companycode=? AND status=? AND typeofdata=? AND config_tid=? AND section_1_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $typeofdata, $config_tid, $section_1_tid)));
    if ($result->count() > 0) {
      foreach ($result as $row) {
        $section_2_tid = $row['section_2_tid'];
        $channel_name = $row['channel_name'];
        $pending_with = pending_email_roles_for_notice($section_2_tid, $companycode);
        if (count($pending_with) > 0) {
          $arr[] = [
            "channel_name" => $channel_name,
            "section_2_status" => 1,
            "pending_with" => $pending_with
          ];
        }
      }
    } else {
      $pending_with = pending_email_roles_for_notice($section_1_tid, $companycode);
      if (count($pending_with) > 0) {
        $arr[] = [
          "channel_name" => $pdam_name,
          "section_2_status" => 0,
          "pending_with" => $pending_with
        ];
      }
    }

    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_client_department($companycode, $email, $role)
{
  try {
    global $session;
    $arr_d = [];
    $result = $session->execute($session->prepare("SELECT locationdepartment FROM locationinscope WHERE companycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    foreach ($result as $row_d) {
      $dept = explode("|", $row_d['locationdepartment']);
      foreach ($dept as $det) {
        $dep_t = explode(",", $det);
        if ($dep_t[0] !== "") {
          array_push($arr_d, $dep_t[0]);
        }
      }
    }

    $arr_unique = array_unique($arr_d);
    sort($arr_unique);
    $arr_return = ["code" => 200, "success" => true, "message" => "Data fetched successfully", "data" => $arr_unique];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_user_from_client($companycode, $email, $role)
{
  try {
    global $session;
    $arr = [];
    //Main company
    $result = $session->execute($session->prepare("SELECT custemailaddress,custuserpasswd,custfname,custlname FROM customer WHERE custcompanycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    foreach ($result as $row) {
      if ($row['custuserpasswd'] != "") {
        $result_sr = $session->execute($session->prepare("SELECT onboardstatus FROM statusrecord WHERE custemail=?"), array('arguments' => array($row['custemailaddress'])));
        if ($result_sr->count() > 0) {
          if ((string) $result_sr[0]['onboardstatus'] != "0") {

            $result_role = $session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtccustemail=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $row['custemailaddress'])));
            if ($result_role->count() > 0) {
              $name = $row['custfname'] . " " . $row['custlname'];
              $arr[$row['custemailaddress']] = $name;
            }
          }
        }
      }
    }

    // Assigned company
    $result_as = $session->execute($session->prepare("SELECT custemail,onboardstatus FROM custassignedcompany WHERE assignedcompany=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    foreach ($result_as as $row_as) {
      if ((string) $row_as['onboardstatus'] != "0") {
        $result_ex = $session->execute($session->prepare("SELECT custuserpasswd,custfname,custlname FROM customer WHERE custemailaddress=?"), array('arguments' => array($row_as['custemail'])));
        if ($result_ex->count() > 0) {
          if ($result_ex[0]['custuserpasswd'] != "") {
            $result_role = $session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtccustemail=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $row_as['custemail'])));
            if ($result_role->count() > 0) {
              $name = $result_ex[0]['custfname'] . " " . $result_ex[0]['custlname'];
              $arr[$row_as['custemail']] = $name;
            }
          }
        }
      }
    }

    asort($arr);
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_channel_name($companycode, $email, $role)
{
  try {
    global $session;
    $arr_d = [];
    $result = $session->execute($session->prepare("SELECT channel FROM pdam_channel_client WHERE companycode=?"), array('arguments' => array($companycode)));
    foreach ($result as $row_d) {
      array_push($arr_d, $row_d['channel']);
    }

    $result_alt = $session->execute($session->prepare("SELECT channel FROM pdam_channel_master WHERE id=?"), array('arguments' => array("channel")));
    foreach ($result_alt as $row_m) {
      array_push($arr_d, $row_m['channel']);
    }

    $arr_unique_m = array_unique($arr_d);
    sort($arr_unique_m);
    $arr_return = ["code" => 200, "success" => true, "message" => "Data fetched successful", "data" => $arr_unique_m];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function section_2_channel_dept_validation($companycode, $email, $role, $data)
{
  try {
    global $session;

    if ($data['classname'] == "channel") {
      $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => "data_found"];
      $result = $session->execute($session->prepare("SELECT channel FROM pdam_channel_master WHERE id=? AND channel=?"), array('arguments' => array("channel", $data['channel_dept_val'])));
      if ($result->count() == 0) {
        $result = $session->execute($session->prepare("SELECT channel FROM pdam_channel_client WHERE companycode=? AND channel=?"), array('arguments' => array($companycode, $data['channel_dept_val'])));
        if ($result->count() == 0) {
          $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => "data_not_found"];
        }
      }
    }

    if ($data['classname'] == "dept") {
      $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => "data_not_found"];
      $arr_dept = client_department_read($companycode);
      $dept_arr = [];
      if ($arr_dept['success']) {
        $dept_arr = $arr_dept['data'];
      }

      if (in_array($data['channel_dept_val'], $dept_arr)) {
        $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => "data_found"];
      }
    }

    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function section_2_data_validation($companycode, $email, $role, $data)
{
  try {
    global $session;
    //Data validation
    $config_tid = escape_input($data['config_tid']);
    $section_1_tid = escape_input($data['section_1_tid']);
    $channel_name = escape_input($data['channel_name']);
    $dept_name = escape_input($data['dept_name']);
    $owner_name = escape_input($data['owner_name']);

    if ($channel_name == '') {
      $arr_return = ["success" => false, "message" => "Channel name cannot be empty"];
      return $arr_return;
      exit();
    }
    if ($dept_name == '') {
      $arr_return = ["success" => false, "message" => "Channe owner cannot be empty"];
      return $arr_return;
      exit();
    }
    if ($owner_name == '') {
      $arr_return = ["success" => false, "message" => "Owner name cannot be empty"];
      return $arr_return;
      exit();
    }

    $result_check = $session->execute(
      $session->prepare("SELECT companycode FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND channel_name=? AND team_name=? AND owner_email=? ALLOW FILTERING"),
      array(
        'arguments' => array(
          $companycode,
          "1",
          $config_tid,
          $section_1_tid,
          $channel_name,
          $dept_name,
          $owner_name
        )
      )
    );
    if ($result_check->count() > 0) {
      $arr_return = ["success" => false, "message" => "Some combination already exist"];
      return $arr_return;
      exit();
    }
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => 'Success'];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function section_2_submit($companycode, $email, $role, $custcode, $data, $config_tid, $section_1_tid)
{
  try {
    global $session;
    //Data validation
    $config_tid = escape_input($config_tid);
    $section_1_tid = escape_input($section_1_tid);
    foreach ($data as $key__v => $value__v) {
      foreach ($value__v as $key_v => $value_v) {
        $data[$key__v][$key_v] = escape_input($value_v);
        if ($value__v == "") {
          $arr_return = ["success" => false, "message" => "Please fill all fields first"];
          return $arr_return;
          exit();
        }
      }
    }

    $existing_comb = [];
    foreach ($data as $value_s2_val) {
      $channel_name = $value_s2_val['channel_name'];
      $owner_email = $value_s2_val['owner_email'];
      $team_name = $value_s2_val['team_name'];
      $result_check = $session->execute(
        $session->prepare("SELECT companycode FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND channel_name=? AND team_name=? AND owner_email=? ALLOW FILTERING"),
        array(
          'arguments' => array(
            $companycode,
            "1",
            $config_tid,
            $section_1_tid,
            $channel_name,
            $team_name,
            $owner_email
          )
        )
      );
      if ($result_check->count() > 0) {
        $arr_return = ["code" => 500, "success" => false, "message" => "Some combination already exist", "error" => ""];
        return $arr_return;
        exit();
      }
    }

    $result_val = $session->execute($session->prepare("SELECT typeofdata,product_and_services FROM pdam_section_1 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $config_tid, $section_1_tid)));
    if ($result_val->count() == 0) {
      $arr_return = ["success" => false, "message" => "Invalid transaction details"];
      return $arr_return;
      exit();
    }

    $typeofdata = $result_val[0]['typeofdata'];
    $product_and_services = $result_val[0]['product_and_services'];

    foreach ($data as $value_s2) {

      $channel_name = $value_s2['channel_name'];
      $team_name = $value_s2['team_name'];

      $cd_data['classname'] = "channel";
      $cd_data['channel_dept_val'] = $channel_name;

      //validate dept and team for add new
      $channel_name_val = section_2_channel_dept_validation($companycode, $email, $role, $cd_data);
      if ($channel_name_val['data'] == 'data_not_found') {
        $add_new_channel = add_new_channel($companycode, $channel_name, $email, "Name", $role);
      }

      $team_name_val = section_2_channel_dept_validation($companycode, $email, $role, $cd_data);
      if ($team_name_val['data'] == 'data_not_found') {
        $pdam_section_department_process = pdam_section_department_process($team_name, $companycode, $email, "Name", $role, $custcode);
      }

      $owner_email = $value_s2['owner_email'];
      $owner_name = get_name_from_email($owner_email);
      $section_2_tid = new \Cassandra\Uuid();
      $timestamp = new \Cassandra\Timestamp();

      $columns = [
        "companycode",
        "status",
        "createdate",
        "effectivedate",
        "approve_status",
        "typeofdata",
        "channel_name",
        "owner_email",
        "owner_name",
        "team_name",
        "section_1_tid",
        "section_2_tid",
        "config_tid",
        "form_status"
      ];
      $columns_data = [
        $companycode,
        "1",
        $timestamp,
        $timestamp,
        "",
        $typeofdata,
        $channel_name,
        $owner_email,
        $owner_name,
        $team_name,
        $section_1_tid,
        (string) $section_2_tid,
        $config_tid,
        "1"
      ];
      $data_for_insert = [
        "action" => "insert", //read/insert/update/delete
        "table_name" => "pdam_section_2", //provide actual table name or dummy table name thats been in JSON/arr file
        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      $table_insert = table_crud_actions($data_for_insert);

      //Find random role
      $owner_role = "NA";
      $result_role = $session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtccustemail=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $owner_email)));
      if ($result_role->count() > 0) {
        $owner_role = $result_role[0]['rtcrole'];
      }

      //notice_write
      $notice_link = "pdam_section_3.php?tid=" . $config_tid . "&s1_tid=" . $section_1_tid . "&s2_tid=" . (string) $section_2_tid;
      $notice_output = notice_write("PDAM202", $companycode, $email, $role, $notice_link, $owner_email, $owner_role, $channel_name, (string) $section_2_tid, $product_and_services);

    }

    //Update Notice
    $notice_update = notice_update_all($section_1_tid, $companycode, $email, $role, "PDAM201");

    $arr_return = ["code" => 200, "success" => true, "message" => "Data Inserted Successfully", "data" => "Succcess"];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_client_role($companycode, $email, $role)
{
  try {
    global $session;
    $arr = [];
    $result = $session->execute($session->prepare("SELECT rolename FROM rolematrix WHERE rolemodule=? ALLOW FILTERING"), array('arguments' => array("normal")));
    foreach ($result as $row) {
      array_push($arr, $row['rolename']);
    }
    sort($arr);
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function section_1_submit($companycode, $email, $role, $custcode, $pdam_name, $type_sel, $product_sel, $owner, $pns_sel, $config_tid)
{
  try {
    global $session;
    //Data validation
    $pdam_name = escape_input($pdam_name);
    $type_sel = escape_input($type_sel);
    $owner = escape_input($owner);
    $pns_sel = escape_input($pns_sel);
    $product_sel = escape_input($product_sel);
    $config_tid = escape_input($config_tid);

    if ($pdam_name == "") {
      $arr_return = ["success" => false, "message" => "PDAM name should not be empty"];
      return $arr_return;
      exit();
    }
    if ($type_sel == "") {
      $arr_return = ["success" => false, "message" => "Please select type of data"];
      return $arr_return;
      exit();
    }
    if ($product_sel == "") {
      $arr_return = ["success" => false, "message" => "Please select Product/Service/Department"];
      return $arr_return;
      exit();
    }
    if ($owner == "") {
      $arr_return = ["success" => false, "message" => "Please select owner"];
      return $arr_return;
      exit();
    }
    if ($pns_sel == "") {
      $arr_return = ["success" => false, "message" => "Please select Product and Department"];
      return $arr_return;
      exit();
    }
    if ($config_tid == "") {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }

    $cd_data['classname'] = "dept";
    $cd_data['channel_dept_val'] = $product_sel;

    $team_name_val = section_2_channel_dept_validation($companycode, $email, $role, $cd_data);
    if ($team_name_val['data'] == 'data_not_found') {
      $pdam_section_department_process = pdam_section_department_process($product_sel, $companycode, $email, "Name", $role, $custcode);
    }

    $section_1_tid = new \Cassandra\Uuid();
    $timestamp = new \Cassandra\Timestamp();
    $columns = [
      "companycode",
      "status",
      "createdate",
      "effectivedate",
      "section_1_tid",
      "config_tid",
      "typeofdata",
      "product",
      "pdam_name",
      "owner",
      "product_and_services"
    ];
    $columns_data = [
      $companycode,
      "1",
      $timestamp,
      $timestamp,
      (string) $section_1_tid,
      $config_tid,
      $type_sel,
      $product_sel,
      $pdam_name,
      $owner,
      $pns_sel
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "pdam_section_1", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    if ($table_insert['success']) {
      //Create notice

      $email_role_array = module_assign_email_role_list("PG075", "create", $companycode);
      foreach ($email_role_array as $em_role) {
        $notice_link = "pdam_section_2.php?tid=" . $config_tid . "&s1_tid=" . (string) $section_1_tid;
        $notice_output = notice_write("PDAM201", $companycode, $email, $role, $notice_link, $em_role['email'], $em_role['role'], $pdam_name, (string) $section_1_tid);
      }
      // $output = update_landing_module($companycode, $GLOBALS['activeLaw'], "8");

      $arr_return = ["code" => 200, "success" => true, "data" => "Section 1 Data Inserted Successfully"];

    } else {
      $arr_return = ["success" => false, "message" => "Error Occured"];
    }
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_pd_element_list($companycode, $email, $role, $typeofdata, $section_2_tid, $section_1_tid, $config_tid, $application_name, $donot_allow_reloading = true)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $typeofdata_master = $typeofdata;
    $insert_suggested = true;

    $typeofdata_master = ($typeofdata == 'Customer' || $typeofdata == 'Employee') ? $typeofdata : 'Customer';

    $result = $session->execute($session->prepare("SELECT pdcategory FROM pd_master WHERE type=? ALLOW FILTERING"), array('arguments' => array($typeofdata_master)));
    foreach ($result as $row) {
      if ($row['pdcategory'] != "") {
        $result_checked = $session->execute($session->prepare("SELECT id FROM pdam_section_3_form_1_temp WHERE section_2_tid=? AND pdcategory=? AND application_name_id=? ALLOW FILTERING"), array('arguments' => array($section_2_tid, $row['pdcategory'], $application_name)));
        if ($result_checked->count() > 0) {
          $row['checked'] = true;
          $insert_suggested = false;
        }
        $arr[$row['pdcategory']] = $row;
      }
    }

    $result_client = $session->execute($session->prepare("SELECT pdcategory FROM pdam_pd_element WHERE companycode=? AND status=? AND typeofdata=?"), array('arguments' => array($companycode, "1", $typeofdata)));
    foreach ($result_client as $row_client) {
      if ($row_client['pdcategory'] != "") {
        $result_checked = $session->execute($session->prepare("SELECT id FROM pdam_section_3_form_1_temp WHERE section_2_tid=? AND pdcategory=? AND application_name_id=? ALLOW FILTERING"), array('arguments' => array($section_2_tid, $row_client['pdcategory'], $application_name)));
        if ($result_checked->count() > 0) {
          $row_client['checked'] = true;
          $insert_suggested = false;
        }
        $arr[$row_client['pdcategory']] = $row_client;
      }
    }

    //Get all the suggested PDelements
    if ($insert_suggested && $donot_allow_reloading) {

      $res_s2id = $session->execute(
        $session->prepare("SELECT channel_name FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND section_2_tid=? ALLOW FILTERING"),
        array('arguments' => array($companycode, "1", $config_tid, $section_1_tid, $section_2_tid))
      );
      if ($res_s2id->count() == 0) {
        $arr_return = ["success" => false, "message" => "Invalid Request", "data" => ""];
        return $arr_return;
        exit();
      }
      $channel_name = $res_s2id[0]['channel_name'];

      //Get all the suggested data
      $get_all_suggested_pdelements = get_all_suggested_pdelements($companycode, $typeofdata, $application_name);

      if (!$get_all_suggested_pdelements['success']) {
        return $get_all_suggested_pdelements;
        exit();
      }

      $suggested_pdelements = $get_all_suggested_pdelements['data'];
      //Insert suggested_pdelements then reload this function. To avoid deadlock/infinite loop just pass a donot_allow_reloading=false flag in this function

      foreach ($suggested_pdelements as $value_element) {

        $check_if_pdelements_exist = check_if_pdelements_exist($value_element['pdcategory'], $value_element['pdelements']);

        if ($check_if_pdelements_exist) {
          $id = new \Cassandra\Uuid();
          $timestamp = new \Cassandra\Timestamp();
          $section_header = "Collection Channel Application Name";
          $section_header_id = "FORM-1";
          $columns = [
            "id",
            "companycode",
            "status",
            "createdate",
            "effectivedate",
            "typeofdata",
            "channel_name",
            "application_name",
            "pdelements",
            "pdcategory",
            "pdsupercategory",
            "pdclassification",
            "section_header",
            "section_header_id",
            "config_tid",
            "section_1_tid",
            "section_2_tid",
            "alsoknownas",
            "application_name_id",
            "modeof_inflow"
          ];
          $columns_data = [
            (string) $id,
            $companycode,
            "1",
            $timestamp,
            $timestamp,
            $typeofdata,
            $channel_name,
            $value_element['application_name'],
            $value_element['pdelements'],
            $value_element['pdcategory'],
            $value_element['pdsupercategory'],
            $value_element['pdclassification'],
            $section_header,
            $section_header_id,
            $config_tid,
            $section_1_tid,
            $section_2_tid,
            $value_element['alsoknownas'],
            $value_element['application_name_id'],
            $value_element['modeof_inflow']
          ];
          $data_for_insert = [
            "action" => "insert", //read/insert/update/delete
            "table_name" => "pdam_section_3_form_1_temp", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);
          if (!$table_insert['success']) {
            return $table_insert;
            exit();
          }

          //Insert into client and vendor table
          $res_ven = $session->execute(
            $session->prepare("SELECT * FROM pdam_client_vendor_details WHERE companycode=? AND status=? AND refid=? ALLOW FILTERING"),
            array('arguments' => array($companycode, "1", $value_element['id']))
          );


          foreach ($res_ven as $row_ven) {
            $idNew = new \Cassandra\Uuid();
            $columns = [
              "companycode",
              "status",
              "createdate",
              "effectivedate",
              "client_vendor_name",
              "client_vendor_id",
              "client_vendor_type",
              "config_tid",
              "section_1_tid",
              "section_2_id",
              "id",
              "modeof_inflow",
              "refid"
            ];
            $columns_data = [
              $companycode,
              "1",
              new \Cassandra\timestamp(),
              new \Cassandra\timestamp(),
              "Name",
              $row_ven['client_vendor_id'],
              $row_ven['client_vendor_type'],
              $config_tid,
              $section_1_tid,
              $section_2_tid,
              (string) $idNew,
              $value_element['modeof_inflow'],
              (string) $id
            ];
            $data_for_insert = [
              "action" => "insert", //read/insert/update/delete
              "table_name" => "pdam_client_vendor_details", //provide actual table name or dummy table name thats been in JSON/arr file
              "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
              "isCondition" => false,
              "condition_columns" => "",
              "columns_data" => $columns_data,
              "isAllowFiltering" => false
            ];
            $table_insert = table_crud_actions($data_for_insert);
            if (!$table_insert['success']) {
              return $table_insert;
              exit();
            }
          }
        }

      }
      $get_pd_element_list_from_master = get_pd_element_list($companycode, $email, $role, $typeofdata, $section_2_tid, $section_1_tid, $config_tid, $application_name, false);
      return $get_pd_element_list_from_master;
    } else {
      ksort($arr);
      $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
      return $arr_return;
    }
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_pd_element_list_by_category($typeofdata, $pdcategory, $companycode, $section_2_tid, $application_name)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $typeofdata_master = $typeofdata;
    if ($typeofdata == 'Customer' || $typeofdata == 'Employee') {
    } else {
      $typeofdata_master = 'Customer';
    }
    if ($pdcategory == "") {
      $pdcategory = " ";
    }
    $result = $session->execute($session->prepare("SELECT pdelements,pdcategory,pdsupercategory,pdclassification,modeof_inflow,id,alsoknownas FROM pd_master WHERE type=? AND pdcategory=? ALLOW FILTERING"), array('arguments' => array($typeofdata_master, $pdcategory)));
    foreach ($result as $row) {
      if ($row['pdelements'] != "") {
        foreach ($row as $key => $value) {
          if ($value == "") {
            $row[$key] = "";
          }
        }
        $result_checked = $session->execute($session->prepare("SELECT id FROM pdam_section_3_form_1_temp WHERE section_2_tid=? AND pdcategory=? AND pdelements=? AND application_name_id=? ALLOW FILTERING"), array('arguments' => array($section_2_tid, $pdcategory, $row['alsoknownas'], $application_name)));
        if ($result_checked->count() > 0) {
          $row['id'] = $result_checked[0]['id'];
          $row['checked'] = true;
        } else {
          $row['id'] = (string) $row['id'];
          $row['checked'] = false;
        }
        $arr[] = $row;
      }
    }

    $result_client = $session->execute($session->prepare("SELECT pdelements,pdcategory,pdsupercategory,pdclassification,id FROM pdam_pd_element WHERE companycode=? AND status=? AND typeofdata=? AND pdcategory=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $typeofdata, $pdcategory)));
    foreach ($result_client as $row_client) {
      $row_client['alsoknownas'] = $row_client['pdelements'];
      if ($row_client['pdelements'] != "") {
        $row_client['modeof_inflow'] = "";
        foreach ($row_client as $key => $value) {
          if ($value == "") {
            $row_client[$key] = "";
          }
        }
        $result_checked = $session->execute($session->prepare("SELECT id FROM pdam_section_3_form_1_temp WHERE section_2_tid=? AND pdcategory=? AND pdelements=? AND application_name_id=? ALLOW FILTERING"), array('arguments' => array($section_2_tid, $pdcategory, $row_client['alsoknownas'], $application_name)));
        if ($result_checked->count() > 0) {
          $row_client['id'] = $result_checked[0]['id'];
          $row_client['checked'] = true;
        } else {
          $row_client['id'] = (string) $row_client['id'];
          $row_client['checked'] = false;
        }
        $arr[] = $row_client;
      }
    }

    array_multisort(array_column($arr, 'alsoknownas'), SORT_ASC, $arr);

    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}
function get_all_suggested_pdelements($companycode, $typeofdata, $application_name_id)
{
  try {
    global $session;
    $arr = [];
    // pdam_section_2
    $result_section_2 = $session->execute($session->prepare("SELECT section_1_tid,section_2_tid,form_status,typeofdata,channel_name FROM pdam_section_2 WHERE companycode=? AND status=? AND typeofdata=?"), array('arguments' => array($companycode, "1", $typeofdata)));
    foreach ($result_section_2 as $row_section_2) {
      if ($row_section_2['form_status'] == '6') {
        //get data where form_status = 6, means completed 
        $section_1_tid = $row_section_2['section_1_tid'];
        $typeofdata = $row_section_2['typeofdata'];
        $channel_name = $row_section_2['channel_name'];
        $section_2_tid = $row_section_2['section_2_tid'];
        //get pdelements from assets
        if ($typeofdata != "" && $channel_name != "") {
          $result_form_1 = $session->execute(
            $session->prepare("SELECT * FROM pdam_section_3_form_1 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"),
            array(
              'arguments' => array(
                $companycode,
                "1",
                $section_2_tid,
                $typeofdata,
                $channel_name
              )
            )
          );
          foreach ($result_form_1 as $row_form_1) {
            if ($row_form_1['alsoknownas'] == '' || $row_form_1['pdcategory'] == "" || $row_form_1['pdelements'] == "") {
            } else {
              if ($row_form_1['application_name_id'] == $application_name_id) {

                //check against pd_master first or pd_element


                $arr[$row_form_1['pdelements']] = $row_form_1;
              }
            }
          }
        }
      }
    }
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function check_if_pdelements_exist($pdcategory, $pdelement)
{
  try {
    global $session;
    $result = $session->execute($session->prepare("SELECT pdcategory FROM pd_master WHERE pdcategory=? AND alsoknownas=? ALLOW FILTERING"), array('arguments' => array($pdcategory, $pdelement)));
    if ($result->count() == 0) {

      $result = $session->execute($session->prepare("SELECT pdcategory FROM pdam_pd_element WHERE pdcategory=? AND pdelements=? ALLOW FILTERING"), array('arguments' => array($pdcategory, $pdelement)));
      if ($result->count() == 0) {
        return false;
      } else {
        return true;
      }
    } else {
      return true;
    }
  } catch (\Exception $e) {
    return false;
  }
}

function save_section_3_form_1_row($data, $config_tid, $section_1_tid, $section_2_tid, $companycode, $email, $role, $custcode, $unique_id, $req_type)
{
  try {
    global $session;
    //Data validation
    $config_tid = escape_input($config_tid);
    $section_1_tid = escape_input($section_1_tid);
    $section_2_tid = escape_input($section_2_tid);
    $application_name = escape_input($data['application_name']);
    $application_name_id = escape_input($data['application_name_id']);
    $pdelements = escape_input($data['pdelements']);
    $pdcategory = escape_input($data['pdcategory']);
    $pdsupercategory = escape_input($data['pdsupercategory']);
    $pdclassification = escape_input($data['pdclassification']);
    $modeof_inflow = escape_input($data['modeof_inflow']);
    $alsoknownas = escape_input($data['alsoknownas']);
    $unique_id = (string) $unique_id;

    if ($req_type == "remove") {
      $result_del = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_1_temp WHERE id=? ALLOW FILTERING"), array('arguments' => array((string) $unique_id)));
      if ($result_del->count() > 0) {
        $session->execute(
          $session->prepare("DELETE FROM pdam_section_3_form_1_temp WHERE companycode=? AND section_2_tid=? AND status=? AND typeofdata=? AND channel_name=? AND application_name=? AND createdate=? AND id=?"),
          array(
            'arguments' => array(
              $result_del[0]['companycode'],
              $result_del[0]['section_2_tid'],
              $result_del[0]['status'],
              $result_del[0]['typeofdata'],
              $result_del[0]['channel_name'],
              $result_del[0]['application_name'],
              $result_del[0]['createdate'],
              $result_del[0]['id']
            )
          )
        );
        $arr_return = ["code" => 200, "success" => true, "message" => "Deleted successfully"];
        return $arr_return;
        exit();
      } else {
        $arr_return = ["code" => 500, "success" => false, "message" => "Invalid ID"];
        return $arr_return;
        exit();
      }
    }

    if ($application_name == "") {
      $arr_return = ["code" => 500, "success" => false, "message" => "Application name cannot be empty"];
      return $arr_return;
      exit();
    }

    if ($pdelements == "") {
      $arr_return = ["code" => 500, "success" => false, "message" => "PD element cannot be empty"];
      return $arr_return;
      exit();
    }
    //tid validation
    $result_val = $session->execute($session->prepare("SELECT typeofdata,channel_name FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND section_2_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $config_tid, $section_1_tid, $section_2_tid)));
    if ($result_val->count() == 0) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Invalid transaction details"];
      return $arr_return;
      exit();
    }

    $typeofdata = $result_val[0]['typeofdata'];
    $channel_name = $result_val[0]['channel_name'];
    $timestamp = new \Cassandra\Timestamp();
    $section_header = "Collection Channel Application Name";
    $section_header_id = "FORM-1";
    $id = new \Cassandra\Uuid();

    $result_asset_tx = $session->execute($session->prepare("SELECT assetname FROM transasscust WHERE transasscust=?"), array('arguments' => array(new \Cassandra\Uuid($application_name_id))));
    if ($result_asset_tx->count() == 0) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Application name is not valid"];
      return $arr_return;
      exit();
    }

    $application_name = $result_asset_tx[0]['assetname'];

    $result_id = $session->execute($session->prepare("SELECT id,createdate FROM pdam_section_3_form_1_temp WHERE id=? ALLOW FILTERING"), array('arguments' => array((string) $unique_id)));
    if ($result_id->count() > 0) {
      $id = $result_id[0]['id'];
      $timestamp = $result_id[0]['createdate'];
    }

    $columns = [
      "id",
      "companycode",
      "status",
      "createdate",
      "effectivedate",
      "typeofdata",
      "channel_name",
      "application_name",
      "pdelements",
      "pdcategory",
      "pdsupercategory",
      "pdclassification",
      "modeof_inflow",
      "section_header",
      "section_header_id",
      "config_tid",
      "section_1_tid",
      "section_2_tid",
      "alsoknownas",
      "application_name_id"
    ];
    $columns_data = [
      (string) $id,
      $companycode,
      "1",
      $timestamp,
      $timestamp,
      $typeofdata,
      $channel_name,
      $application_name,
      $pdelements,
      $pdcategory,
      $pdsupercategory,
      $pdclassification,
      $modeof_inflow,
      $section_header,
      $section_header_id,
      $config_tid,
      $section_1_tid,
      $section_2_tid,
      $alsoknownas,
      $application_name_id
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "pdam_section_3_form_1_temp", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    if ($table_insert['success']) {
      $arr_return = ["code" => 200, "success" => true, "message" => "Data Inserted Successfully", "data" => (string) $id];
    } else {
      $arr_return = ["code" => 500, "success" => false, "message" => $table_insert['msg']];
    }

    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_section_3_form_1_data($companycode, $config_tid, $section_1_tid, $section_2_tid)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $arr_asset = [];

    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];

    $result = $session->execute($session->prepare("SELECT id,application_name,application_name_id,pdelements,pdcategory,pdsupercategory,pdclassification,modeof_inflow,alsoknownas FROM pdam_section_3_form_1_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    foreach ($result as $row) {
      $row['temp'] = 1;
      $arr[] = $row;
      $arr_asset[$row['application_name']] = ["saved_st" => 0, "id" => "NA", "storage_type" => "", "location" => "", "retention_period" => "", "temp" => 1];
    }

    foreach ($arr_asset as $key_asset => $value) {
      $key_asset = (string) $key_asset;
      $result_form_3 = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_3 WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $key_asset)));
      if ($result_form_3->count() > 0) {
        $row = $result_form_3[0];
        $retention_period = (int) $row['retention_period'];
        $row['retention_period_year'] = floor($retention_period / 12);
        $row['retention_period_month'] = $retention_period % 12;
        $row['saved_st'] = 1;
        $row['temp'] = 0;
        $arr_asset[$key_asset] = $row;
      } else {
        $result_form_3_sv = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_3_temp WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $key_asset)));
        if ($result_form_3_sv->count() > 0) {
          $row_sv = $result_form_3_sv[0];
          $retention_period = (int) $row_sv['retention_period'];
          $row_sv['retention_period_year'] = floor($retention_period / 12);
          $row_sv['retention_period_month'] = $retention_period % 12;
          $row_sv['saved_st'] = 1;
          $row_sv['temp'] = 1;
          $arr_asset[$key_asset] = $row_sv;
        } else {
          $result_asset = $session->execute($session->prepare("SELECT * FROM transasscust_pdam WHERE companycode=? AND status=? AND assetname=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $key_asset)));
          if ($result_asset->count() > 0) {
            $row = $result_asset[0];
            $row['saved_st'] = 0;
            $row['id'] = "NA";
            $row['temp'] = 1;
            $retention_period = (int) $row['retention_period'];
            $row['retention_period_year'] = floor($retention_period / 12);
            $row['retention_period_month'] = $retention_period % 12;
            $arr_asset[$key_asset] = $row;
          }
        }
      }
    }
    $arr_final = ["form_data" => $arr, "asset_data" => $arr_asset];

    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr_final];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_section_2_data($section_2_tid)
{
  try {
    global $session;
    $arr = [];
    $result = $session->execute($session->prepare("SELECT * FROM pdam_section_2 WHERE section_2_tid=? ALLOW FILTERING"), array('arguments' => array($section_2_tid)));
    if ($result->count() > 0) {
      foreach ($result as $row) {
        $arr = $row;
      }
      $arr_return = ["success" => true, "message" => "Data fetched successful", "data" => $arr];
    } else {
      $arr_return = ["success" => false, "message" => "No Data Found", "data" => $arr];
    }
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function delete_section_3_form_1_row($companycode, $email, $role, $custcode, $config_tid, $section_1_tid, $section_2_tid, $id)
{
  try {
    global $session;
    $result = $session->execute($session->prepare("SELECT companycode,typeofdata,channel_name,createdate,application_name FROM pdam_section_3_form_1_temp WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND section_2_tid=? AND id=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $config_tid, $section_1_tid, $section_2_tid, $id)));
    if ($result->count() == 0) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Invalid request", "error" => "No data found"];
      return $arr_return;
      exit();
    }
    $session->execute(
      $session->prepare("DELETE FROM pdam_section_3_form_1_temp WHERE companycode=? AND section_2_tid=? AND status=? AND typeofdata=? AND channel_name=? AND application_name=? AND createdate=? AND id=?"),
      array(
        'arguments' => array(
          $companycode,
          $section_2_tid,
          "1",
          $result[0]['typeofdata'],
          $result[0]['channel_name'],
          $result[0]['application_name'],
          $result[0]['createdate'],
          $id
        )
      )
    );
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => "Successfully Deleted"];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function next_phase_update_section_3($companycode, $email, $role, $custcode, $config_tid, $section_1_tid, $section_2_tid, $form_id, $data, $data_type)
{
  try {
    global $session;
    $arr_return = [];
    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];
    $createdate = $get_section_2_data['data']['createdate'];
    $timestamp = new \Cassandra\Timestamp();

    //check if any data has been entered first
    switch ($form_id) {
      case '1':
        $result_form_temp = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_1_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
        if ($result_form_temp->count() == 0) {
          $result_form = $session->execute($session->prepare("SELECT companycode FROM pdam_section_3_form_1 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
          if ($result_form->count() == 0) {
            $arr_return = ["success" => false, "message" => "No data found under this transaction. Please fill data first"];
            return $arr_return;
            exit();
          }
        }

        foreach ($result_form_temp as $row_val) {
          if ($row_val['modeof_inflow'] == "") {
            $arr_return = ["success" => false, "message" => "Mode of in flow cannot be empty."];
            return $arr_return;
            exit();
          }
        }


        foreach ($result_form_temp as $row_form) {
          if ($row_form["application_name"] == "") {
            $row_form["application_name"] = " ";
          }
          $result_asset_tx = $session->execute($session->prepare("SELECT transasscust FROM transasscust WHERE assetname=? ALLOW FILTERING"), array('arguments' => array($row_form["application_name"])));
          foreach ($result_asset_tx as $row_asset_tx) {
            $session->execute($session->prepare("UPDATE transasscust SET transpriv=? WHERE transasscust=?"), array('arguments' => array("Yes", $row_asset_tx['transasscust'])));
          }
          $columns = [
            "companycode",
            "section_2_tid",
            "status",
            "typeofdata",
            "channel_name",
            "application_name",
            "createdate",
            "id",
            "config_tid",
            "effectivedate",
            "modeof_inflow",
            "pdcategory",
            "pdclassification",
            "pdelements",
            "pdsupercategory",
            "section_1_tid",
            "section_header",
            "section_header_id",
            "alsoknownas",
            "application_name_id"
          ];
          $columns_data = [
            $row_form["companycode"],
            $row_form["section_2_tid"],
            $row_form["status"],
            $row_form["typeofdata"],
            $row_form["channel_name"],
            $row_form["application_name"],
            $timestamp,
            $row_form["id"],
            $row_form["config_tid"],
            $timestamp,
            $row_form["modeof_inflow"],
            $row_form["pdcategory"],
            $row_form["pdclassification"],
            $row_form["pdelements"],
            $row_form["pdsupercategory"],
            $row_form["section_1_tid"],
            $row_form["section_header"],
            $row_form["section_header_id"],
            $row_form["alsoknownas"],
            $row_form["application_name_id"]
          ];
          $data_for_insert = [
            "action" => "insert", //read/insert/update/delete
            "table_name" => "pdam_section_3_form_1", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);

          //Delete from temp
          $session->execute(
            $session->prepare("DELETE FROM pdam_section_3_form_1_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? AND application_name=? AND createdate=? AND id=?"),
            array(
              'arguments' => array(
                $companycode,
                "1",
                $section_2_tid,
                $typeofdata,
                $channel_name,
                $row_form['application_name'],
                $row_form['createdate'],
                $row_form['id']
              )
            )
          );
        }

        break;
      case '2':
        $save_section_3_form_2_row = save_section_3_form_2_row($data, $config_tid, $section_1_tid, $section_2_tid, $companycode, $email, $role, $custcode);
        if (!$save_section_3_form_2_row['success']) {
          return $save_section_3_form_2_row;
          exit();
        }

        $result_form_temp = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_2_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
        foreach ($result_form_temp as $row_form) {
          $columns = [
            "companycode",
            "section_2_tid",
            "status",
            "typeofdata",
            "channel_name",
            "createdate",
            "id",
            "config_tid",
            "effectivedate",
            "formwithdocsid",
            "objective",
            "purpose",
            "section_1_tid",
            "section_header",
            "section_header_id",
            "url"
          ];
          $columns_data = [
            $row_form["companycode"],
            $row_form["section_2_tid"],
            $row_form["status"],
            $row_form["typeofdata"],
            $row_form["channel_name"],
            $row_form["createdate"],
            $row_form["id"],
            $row_form["config_tid"],
            $row_form["effectivedate"],
            $row_form["formwithdocsid"],
            $row_form["objective"],
            $row_form["purpose"],
            $row_form["section_1_tid"],
            $row_form["section_header"],
            $row_form["section_header_id"],
            $row_form["url"]
          ];
          $data_for_insert = [
            "action" => "insert", //read/insert/update/delete
            "table_name" => "pdam_section_3_form_2", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);

          //Delete from temp
          $session->execute(
            $session->prepare("DELETE FROM pdam_section_3_form_2_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? AND createdate=? AND id=?"),
            array(
              'arguments' => array(
                $companycode,
                "1",
                $section_2_tid,
                $typeofdata,
                $channel_name,
                $row_form['createdate'],
                $row_form['id']
              )
            )
          );
        }

        break;
      case '3':
        // case '4':
        $result_form_temp = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_3_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
        if ($result_form_temp->count() == 0) {
          $result_form = $session->execute($session->prepare("SELECT companycode FROM pdam_section_3_form_3 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
          if ($result_form->count() == 0) {
            $arr_return = ["success" => false, "message" => "No data found under this transaction. Please fill data first"];
            return $arr_return;
            exit();
          }
        }

        foreach ($result_form_temp as $row_form) {
          $columns = [
            "companycode",
            "section_2_tid",
            "status",
            "typeofdata",
            "channel_name",
            "createdate",
            "id",
            "application_name",
            "config_tid",
            "effectivedate",
            "location",
            "retention_period",
            "section_1_tid",
            "section_header",
            "section_header_id",
            "storage_type",
            "primary_database",
            "primary_database_id",
            "primary_database_location",
            "data_type",
            "not_applicable",
          ];
          $columns_data = [
            $row_form["companycode"],
            $row_form["section_2_tid"],
            $row_form["status"],
            $row_form["typeofdata"],
            $row_form["channel_name"],
            $row_form["createdate"],
            $row_form["id"],
            $row_form["application_name"],
            $row_form["config_tid"],
            $row_form["effectivedate"],
            $row_form["location"],
            $row_form["retention_period"],
            $row_form["section_1_tid"],
            $row_form["section_header"],
            $row_form["section_header_id"],
            $row_form["storage_type"],
            $row_form["primary_database"],
            $row_form["primary_database_id"],
            $row_form["primary_database_location"],
            $row_form["data_type"],
            $row_form["not_applicable"],
          ];
          $data_for_insert = [
            "action" => "insert", //read/insert/update/delete
            "table_name" => "pdam_section_3_form_3", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);

          //Delete from temp
          $session->execute(
            $session->prepare("DELETE FROM pdam_section_3_form_3_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? AND createdate=? AND id=?"),
            array(
              'arguments' => array(
                $companycode,
                "1",
                $section_2_tid,
                $typeofdata,
                $channel_name,
                $row_form['createdate'],
                $row_form['id']
              )
            )
          );
        }


        break;
      case '5':
        $result_form_temp = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_4_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
        if ($result_form_temp->count() == 0) {
          $result_form = $session->execute($session->prepare("SELECT companycode FROM pdam_section_3_form_4 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
          if ($result_form->count() == 0) {
            $arr_return = ["success" => false, "message" => "No data found under this transaction. Please fill data first"];
            return $arr_return;
            exit();
          }
        }

        foreach ($result_form_temp as $row_form) {
          $columns = [
            "companycode",
            "section_2_tid",
            "status",
            "typeofdata",
            "channel_name",
            "createdate",
            "id",
            "application_name",
            "config_tid",
            "effectivedate",
            "location",
            "objective",
            "pdcategory",
            "pdelements",
            "pdsupercategory",
            "purpose",
            "retention_period",
            "section_1_tid",
            "section_header",
            "section_header_id",
            "shared_via",
            "storage_type",
            "team_name",
            "team_type",
            "team_input_type",
            "team_name_id",
            "shared_via_id"
          ];
          $columns_data = [
            $row_form["companycode"],
            $row_form["section_2_tid"],
            $row_form["status"],
            $row_form["typeofdata"],
            $row_form["channel_name"],
            new \Cassandra\Timestamp(),
            $row_form["id"],
            $row_form["application_name"],
            $row_form["config_tid"],
            new \Cassandra\Timestamp(),
            $row_form["location"],
            $row_form["objective"],
            $row_form["pdcategory"],
            $row_form["pdelements"],
            $row_form["pdsupercategory"],
            $row_form["purpose"],
            $row_form["retention_period"],
            $row_form["section_1_tid"],
            $row_form["section_header"],
            $row_form["section_header_id"],
            $row_form["shared_via"],
            $row_form["storage_type"],
            $row_form["team_name"],
            $row_form["team_type"],
            $row_form["team_input_type"],
            $row_form["team_name_id"],
            $row_form["shared_via_id"],
          ];
          $data_for_insert = [
            "action" => "insert", //read/insert/update/delete
            "table_name" => "pdam_section_3_form_4", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);

          //Delete from temp
          $session->execute(
            $session->prepare("DELETE FROM pdam_section_3_form_4_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? AND createdate=? AND id=?"),
            array(
              'arguments' => array(
                $companycode,
                "1",
                $section_2_tid,
                $typeofdata,
                $channel_name,
                $row_form['createdate'],
                $row_form['id']
              )
            )
          );
        }
        break;
      case '69':
        $result_form_temp = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_5_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
        if ($result_form_temp->count() == 0) {
          $result_form = $session->execute($session->prepare("SELECT companycode FROM pdam_section_3_form_5 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
          if ($result_form->count() == 0) {
            $arr_return = ["success" => false, "message" => "No data found under this transaction. Please fill data first"];
            return $arr_return;
            exit();
          }
        }

        foreach ($result_form_temp as $row_form) {
          $columns = [
            "companycode",
            "section_2_tid",
            "status",
            "typeofdata",
            "channel_name",
            "createdate",
            "id",
            "application_name",
            "config_tid",
            "effectivedate",
            "location",
            "objective",
            "purpose",
            "section_1_tid",
            "section_header",
            "section_header_id",
            "team_name",
            "type"
          ];
          $columns_data = [
            $row_form["companycode"],
            $row_form["section_2_tid"],
            $row_form["status"],
            $row_form["typeofdata"],
            $row_form["channel_name"],
            $row_form["createdate"],
            $row_form["id"],
            $row_form["application_name"],
            $row_form["config_tid"],
            $row_form["effectivedate"],
            $row_form["location"],
            $row_form["objective"],
            $row_form["purpose"],
            $row_form["section_1_tid"],
            $row_form["section_header"],
            $row_form["section_header_id"],
            $row_form["team_name"],
            $row_form["type"]
          ];
          $data_for_insert = [
            "action" => "insert", //read/insert/update/delete
            "table_name" => "pdam_section_3_form_5", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);

          //Delete from temp
          $session->execute(
            $session->prepare("DELETE FROM pdam_section_3_form_5_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? AND createdate=? AND id=?"),
            array(
              'arguments' => array(
                $companycode,
                "1",
                $section_2_tid,
                $typeofdata,
                $channel_name,
                $row_form['createdate'],
                $row_form['id']
              )
            )
          );
        }

        break;
      default:
        $arr_return = ["success" => false, "message" => "Invalid transaction"];
        return $arr_return;
        exit();
        break;
    }

    if ($form_id == "3a") {
      $form_id = 4;
    }
    $form_status = (int) $form_id + 1;

    //Update form_status
    $session->execute(
      $session->prepare("UPDATE pdam_section_2 SET form_status=?, modifydate=? WHERE companycode=? AND typeofdata=? AND status=? AND createdate=? AND section_2_tid=?"),
      array(
        'arguments' => array(
          (string) $form_status,
          new \Cassandra\Timestamp(),
          $companycode,
          $typeofdata,
          "1",
          $createdate,
          $section_2_tid
        )
      )
    );

    if ($form_status == 6) {
      $notice_update = notice_update($section_2_tid, $companycode, $email, $role, "PDAM202");
      $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => "redirect"];
    } else {
      $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => "next"];
    }

    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function save_section_3_form_2_row($data, $config_tid, $section_1_tid, $section_2_tid, $companycode, $email, $role, $custcode)
{
  try {
    global $session;
    //Data validation
    $config_tid = escape_input($config_tid);
    $section_1_tid = escape_input($section_1_tid);
    $section_2_tid = escape_input($section_2_tid);
    $formwithdocsid = escape_input($data['formwithdocsid']);
    $url = escape_input($data['url']);
    $objective = escape_input($data['objective']);
    $purpose = $data['purpose'];

    if ($objective == '' || count($purpose) == 0) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Please fill all fields first", "error" => "Please fill all fields first"];
      return $arr_return;
      exit();
    }

    //tid validation
    $result_val = $session->execute($session->prepare("SELECT typeofdata,channel_name FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND section_2_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $config_tid, $section_1_tid, $section_2_tid)));
    if ($result_val->count() == 0) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Invalid transaction details", "error" => "Invalid transaction details"];
      return $arr_return;
      exit();
    }

    $typeofdata = $result_val[0]['typeofdata'];
    $channel_name = $result_val[0]['channel_name'];
    $timestamp = new \Cassandra\Timestamp();
    $section_header = "Form-2";
    $section_header_id = "FORM-2";
    $id = new \Cassandra\Uuid();

    $result_id = $session->execute($session->prepare("SELECT id,createdate FROM pdam_section_3_form_2_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    if ($result_id->count() > 0) {
      $id = $result_id[0]['id'];
      $timestamp = $result_id[0]['createdate'];
    }

    $columns = [
      "id",
      "companycode",
      "status",
      "createdate",
      "effectivedate",
      "typeofdata",
      "channel_name",
      "formwithdocsid",
      "url",
      "objective",
      "purpose",
      "section_header",
      "section_header_id",
      "config_tid",
      "section_1_tid",
      "section_2_tid"
    ];
    $columns_data = [
      (string) $id,
      $companycode,
      "1",
      $timestamp,
      $timestamp,
      $typeofdata,
      $channel_name,
      $formwithdocsid,
      $url,
      $objective,
      "form_2_purpose",
      $section_header,
      $section_header_id,
      $config_tid,
      $section_1_tid,
      $section_2_tid
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "pdam_section_3_form_2_temp", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    if (!$table_insert['success']) {
      $arr_return = ["code" => 500, "success" => false, "message" => $table_insert['msg'], "error" => $table_insert['msg']];
      return $arr_return;
      exit();
    }

    //Insert purpose
    $result_del = $session->execute($session->prepare("SELECT id,createdate FROM pdam_section_3_form_2_purpose WHERE refid=? AND companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($section_2_tid, $companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    foreach ($result_del as $row_del) {
      $session->execute(
        $session->prepare("DELETE FROM pdam_section_3_form_2_purpose WHERE refid=? AND companycode=? AND status=? AND createdate=? AND section_2_tid=? AND id=? AND typeofdata=? AND channel_name=?"),
        array(
          'arguments' => array(
            $section_2_tid,
            $companycode,
            "1",
            $row_del['createdate'],
            $section_2_tid,
            $row_del['id'],
            $typeofdata,
            $channel_name
          )
        )
      );
    }


    foreach ($purpose as $purpose_value) {
      $id_purpose = new \Cassandra\Uuid();
      $columns = [
        "id",
        "companycode",
        "status",
        "createdate",
        "effectivedate",
        "typeofdata",
        "channel_name",
        "purpose",
        "section_header",
        "section_header_id",
        "config_tid",
        "section_1_tid",
        "section_2_tid",
        "refid"
      ];
      $columns_data = [
        (string) $id_purpose,
        $companycode,
        "1",
        $timestamp,
        $timestamp,
        $typeofdata,
        $channel_name,
        $purpose_value,
        $section_header,
        $section_header_id,
        $config_tid,
        $section_1_tid,
        $section_2_tid,
        $section_2_tid
      ];
      $data_for_insert = [
        "action" => "insert", //read/insert/update/delete
        "table_name" => "pdam_section_3_form_2_purpose", //provide actual table name or dummy table name thats been in JSON/arr file
        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      $table_insert = table_crud_actions($data_for_insert);
      if (!$table_insert['success']) {
        $arr_return = ["code" => 500, "success" => false, "message" => $table_insert['msg'], "error" => $table_insert['msg']];
        return $arr_return;
        exit();
      }
    }
    $arr_return = ["code" => 200, "success" => true, "message" => "Data Inserted Successfully", "data" => "Success"];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_purpose_list_from_master($companycode, $typeofdata, $config_tid, $section_1_tid, $section_2_tid)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];

    $typeofdata_master = $typeofdata;
    if ($typeofdata == 'Customer' || $typeofdata == 'Employee') {
    } else {
      $typeofdata_master = 'Customer';
    }


    $result = $session->execute($session->prepare("SELECT purpose,mapping_id,question FROM purpose_master WHERE type=? AND status=? AND usability=? AND ques_type=? ALLOW FILTERING"), array('arguments' => array($typeofdata_master, "1", "External", "Question")));
    foreach ($result as $row) {
      $arr[$row['mapping_id']] = ["question" => $row['question'], "purpose" => $row['purpose']];
    }

    $result_pdam = $session->execute($session->prepare("SELECT purpose,id FROM pdam_purpose WHERE companycode=? AND typeofdata=? AND status=? AND usability=? ALLOW FILTERING"), array('arguments' => array($companycode, $typeofdata, "1", "External")));
    foreach ($result_pdam as $row_pdam) {
      $arr[$row_pdam['id']] = ["question" => $row_pdam['purpose'], "purpose" => $row_pdam['purpose']];
    }

    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_section_3_form_2_data($companycode, $config_tid, $section_1_tid, $section_2_tid)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $arr_purpose = [];
    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];

    $result = $session->execute($session->prepare("SELECT formwithdocsid,url,objective FROM pdam_section_3_form_2 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    if ($result->count() == 0) {
      $result = $session->execute($session->prepare("SELECT formwithdocsid,url,objective FROM pdam_section_3_form_2_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
      foreach ($result as $row) {
        $arr = $row;
      }
    } else {
      foreach ($result as $row) {
        $arr = $row;
      }
    }

    //pdam_section_3_form_2_purpose_temp
    $result_purpose = $session->execute($session->prepare("SELECT purpose FROM pdam_section_3_form_2_purpose WHERE refid=? AND companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($section_2_tid, $companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    foreach ($result_purpose as $row_purpose) {
      $result_pdpr = $session->execute($session->prepare("SELECT question,purpose,mappedto FROM purpose_master WHERE mapping_id=? ALLOW FILTERING"), array('arguments' => array($row_purpose['purpose'])));
      // print_r($result_pdpr);
      // die('dead');
      if ($result_pdpr->count() > 0) {
        $arr_purpose[$row_purpose['purpose']] = $result_pdpr[0]['purpose'];
      } else {
        $result_pdpr = $session->execute($session->prepare("SELECT purpose FROM pdam_purpose WHERE id=? ALLOW FILTERING"), array('arguments' => array($row_purpose['purpose'])));
        if ($result_pdpr->count() > 0) {
          $arr_purpose[$row_purpose['purpose']] = $result_pdpr[0]['purpose'];
        }
      }
    }
    asort($arr_purpose);

    $arr_final = ["form_data" => $arr, "purpose_data" => $arr_purpose];
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr_final];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_section_3_form_3_data($companycode, $config_tid, $section_1_tid, $section_2_tid, $data_type, $email, $role, $custcode)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $arr_asset = [];

    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];

    $result_sv = $session->execute($session->prepare("SELECT id,application_name,pdelements,pdcategory,pdsupercategory,pdclassification,modeof_inflow FROM pdam_section_3_form_1 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    foreach ($result_sv as $row_sv) {
      $arr_asset[$row_sv['application_name']] = ["saved_st" => 0, "id" => "NA", "storage_type" => "", "location" => "", "retention_period" => "", "temp" => 1];
    }

    $secondary = false;
    $asset_final = "";

    foreach ($arr_asset as $key_asset => $value) {
      $key_asset = (string) $key_asset;
      //find first element which for primary is not mapped
      $result_form_3_sv_saved = $session->execute($session->prepare("SELECT id,storage_type,location,retention_period FROM pdam_section_3_form_3 WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? AND data_type=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $key_asset, $data_type)));
      if ($result_form_3_sv_saved->count() == 0) {
        $result_form_3_sv = $session->execute($session->prepare("SELECT id,storage_type,location,retention_period FROM pdam_section_3_form_3_temp WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? AND data_type=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $key_asset, $data_type)));
        if ($result_form_3_sv->count() == 0) {
          $asset_final = $key_asset;
          break;
        }
      }

      $result_form_3_sec_saved = $session->execute($session->prepare("SELECT id,storage_type,location,retention_period FROM pdam_section_3_form_3 WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? AND data_type=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $key_asset, "secondary")));
      if ($result_form_3_sec_saved->count() == 0) {
        $result_form_3_sec = $session->execute($session->prepare("SELECT id,storage_type,location,retention_period FROM pdam_section_3_form_3_temp WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? AND data_type=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $key_asset, "secondary")));
        if ($result_form_3_sec->count() == 0) {
          $secondary = true;
          $asset_final = $key_asset;
          break;
        }
      }
    }

    $asset_list = [];
    $asset_list_inp = asset_name_read_with_dept($companycode, $email, $role, $custcode);
    $asset_list = [];
    if ($asset_list_inp['success']) {
      $asset_list = $asset_list_inp['data'];
    }

    if ($asset_final == "") {
      $arr_return = ["success" => true, "message" => "all_complete", "data" => ""];
    } else {
      $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => ['form_data' => $asset_final, "asset_list" => $asset_list, "secondary" => $secondary]];
    }
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function save_section_3_form_3_row($data, $config_tid, $section_1_tid, $section_2_tid, $companycode, $email, $role, $custcode)
{
  try {
    global $session;
    //Data validation
    $config_tid = escape_input($config_tid);
    $section_1_tid = escape_input($section_1_tid);
    $section_2_tid = escape_input($section_2_tid);

    $asset_name = escape_input($data['asset_name']);
    $primary_server_location = escape_input($data['primary_server_location']);
    $primary_database_id = escape_input($data['primary_database']);
    $primary_database_location = escape_input($data['primary_database_location']);
    $storage_type = escape_input($data['storage_type']);
    $retention_period_year = (int) $data['retention_period_year'];
    $retention_period_month = (int) $data['retention_period_month'];


    $result_asset_tx = $session->execute($session->prepare("SELECT assetname FROM transasscust WHERE transasscust=?"), array('arguments' => array(new \Cassandra\Uuid($primary_database_id))));
    if ($result_asset_tx->count() == 0) {
      $arr_return = ["success" => false, "message" => "Primary Database is not valid"];
      return $arr_return;
      exit();
    }
    $primary_database = $result_asset_tx[0]['assetname'];

    $retention_period = ($retention_period_year * 12) + $retention_period_month;

    //tid validation
    $result_val = $session->execute($session->prepare("SELECT typeofdata,channel_name FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND section_2_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $config_tid, $section_1_tid, $section_2_tid)));
    if ($result_val->count() == 0) {
      $arr_return = ["success" => false, "message" => "Invalid transaction details"];
      return $arr_return;
      exit();
    }

    $typeofdata = $result_val[0]['typeofdata'];
    $channel_name = $result_val[0]['channel_name'];
    $timestamp = new \Cassandra\Timestamp();
    $section_header = "The collected data is stored in";
    $section_header_id = "FORM-3";
    $id = new \Cassandra\Uuid();

    $columns = [
      "id",
      "companycode",
      "status",
      "createdate",
      "effectivedate",
      "typeofdata",
      "channel_name",
      "application_name",
      "storage_type",
      "location",
      "retention_period",
      "section_header",
      "section_header_id",
      "config_tid",
      "section_1_tid",
      "section_2_tid",
      "primary_database",
      "primary_database_id",
      "primary_database_location",
      "data_type"
    ];
    $columns_data = [
      (string) $id,
      $companycode,
      "1",
      $timestamp,
      $timestamp,
      $typeofdata,
      $channel_name,
      $asset_name,
      $storage_type,
      $primary_server_location,
      (string) $retention_period,
      $section_header,
      $section_header_id,
      $config_tid,
      $section_1_tid,
      $section_2_tid,
      $primary_database,
      $primary_database_id,
      $primary_database_location,
      "primary"
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "pdam_section_3_form_3_temp", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    if (!$table_insert['success']) {
      $arr_return = ["success" => false, "message" => $table_insert['msg']];
    }

    //Insert into asset register
    $add_asset_to_register = add_asset_to_register($companycode, $custcode, $asset_name, $storage_type, $primary_server_location, (string) $retention_period);
    if (!$add_asset_to_register['success']) {
      return $add_asset_to_register;
    } else {
      $arr_return = ["code" => 200, "success" => true, "message" => "Data Inserted Successfully", "data" => (string) $id];
    }
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function add_asset_to_register($companycode, $custcode, $application_name, $storage_type, $location, $retention_period)
{
  try {
    global $session;
    $result_as = $session->execute($session->prepare("SELECT assetname,modulename,transasscust FROM transasscust WHERE transasscompanycode=? AND status=? AND assetname=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $application_name)));
    $timestamp = new \Cassandra\Timestamp();
    if ($result_as->count() > 0) {
      $transasscust = $result_as[0]['transasscust'];
    } else {
      $transasscust = new \Cassandra\Uuid();
      $columns = [
        "transasscust",
        "assetname",
        "createdate",
        "effectivedate",
        "modulename",
        "status",
        "transasscompanycode",
        "transasscustcode"
      ];
      $columns_data = [
        $transasscust,
        $application_name,
        $timestamp,
        $timestamp,
        "PDAM",
        "1",
        $companycode,
        $custcode
      ];
      $data_for_insert = [
        "action" => "insert", //read/insert/update/delete
        "table_name" => "transasscust", //provide actual table name or dummy table name thats been in JSON/arr file
        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      $table_insert = table_crud_actions($data_for_insert);
    }

    $result_asset = $session->execute($session->prepare("SELECT assetname FROM transasscust_pdam WHERE companycode=? AND status=? AND assetname=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $application_name)));
    if ($result_asset->count() > 0) {
      $arr_return = ["success" => true, "message" => "Already Exist"];
      return $arr_return;
      exit();
    } else {
      $columns = [
        "companycode",
        "status",
        "createdate",
        "effectivedate",
        "assetname",
        "storage_type",
        "location",
        "retention_period",
        "refid"
      ];
      $columns_data = [
        $companycode,
        "1",
        $timestamp,
        $timestamp,
        $application_name,
        $storage_type,
        $location,
        $retention_period,
        (string) $transasscust
      ];
      $data_for_insert = [
        "action" => "insert", //read/insert/update/delete
        "table_name" => "transasscust_pdam", //provide actual table name or dummy table name thats been in JSON/arr file
        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      $table_insert = table_crud_actions($data_for_insert);
    }
    ;
    $arr_return = ["code" => 200, "success" => true, "message" => "Data Inserted Successfully", "data" => "Succcess"];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function save_section_3_form_4_row_modal($data, $config_tid, $section_1_tid, $section_2_tid, $companycode, $email, $role, $custcode)
{
  try {
    global $session;
    //Data validation
    $config_tid = escape_input($config_tid);
    $section_1_tid = escape_input($section_1_tid);
    $section_2_tid = escape_input($section_2_tid);

    $asset_name = escape_input($data['asset_name']);
    $primary_server_location = escape_input($data['secondary_server_location']);
    $primary_database_id = escape_input($data['secondary_database']);
    $primary_database_location = escape_input($data['secondary_database_location']);
    $storage_type = escape_input($data['storage_type']);
    $na_opt = escape_input($data['na_opt']);
    $retention_period_year = (int) $data['retention_period_year'];
    $retention_period_month = (int) $data['retention_period_month'];

    if ($na_opt == "on") {
      $primary_server_location = "";
      $primary_database = "";
      $primary_database_location = "";
      $storage_type = "";
      $retention_period_year = 0;
      $retention_period_month = 0;
    } else {
      $result_asset_tx = $session->execute($session->prepare("SELECT assetname FROM transasscust WHERE transasscust=?"), array('arguments' => array(new \Cassandra\Uuid($primary_database_id))));
      if ($result_asset_tx->count() == 0) {
        $arr_return = ["success" => false, "message" => "Secondary Database is not valid"];
        return $arr_return;
        exit();
      }
      $primary_database = $result_asset_tx[0]['assetname'];
    }

    $retention_period = ($retention_period_year * 12) + $retention_period_month;

    //tid validation
    $result_val = $session->execute($session->prepare("SELECT typeofdata,channel_name FROM pdam_section_2 WHERE companycode=? AND status=? AND config_tid=? AND section_1_tid=? AND section_2_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $config_tid, $section_1_tid, $section_2_tid)));
    if ($result_val->count() == 0) {
      $arr_return = ["success" => false, "message" => "Invalid transaction details"];
      return $arr_return;
      exit();
    }

    $typeofdata = $result_val[0]['typeofdata'];
    $channel_name = $result_val[0]['channel_name'];
    $timestamp = new \Cassandra\Timestamp();
    $section_header = "The collected data is stored in";
    $section_header_id = "FORM-4";
    $id = new \Cassandra\Uuid();

    $columns = [
      "id",
      "companycode",
      "status",
      "createdate",
      "effectivedate",
      "typeofdata",
      "channel_name",
      "application_name",
      "storage_type",
      "location",
      "retention_period",
      "section_header",
      "section_header_id",
      "config_tid",
      "section_1_tid",
      "section_2_tid",
      "primary_database",
      "primary_database_id",
      "primary_database_location",
      "data_type",
      "not_applicable"
    ];
    $columns_data = [
      (string) $id,
      $companycode,
      "1",
      $timestamp,
      $timestamp,
      $typeofdata,
      $channel_name,
      $asset_name,
      $storage_type,
      $primary_server_location,
      (string) $retention_period,
      $section_header,
      $section_header_id,
      $config_tid,
      $section_1_tid,
      $section_2_tid,
      $primary_database,
      $primary_database_id,
      $primary_database_location,
      "secondary",
      $na_opt
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "pdam_section_3_form_3_temp", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    if (!$table_insert['success']) {
      $arr_return = ["success" => false, "message" => $table_insert['msg']];
    }

    //Insert into asset register
    $arr_return = ["code" => 200, "success" => true, "message" => "Data Inserted Successfully", "data" => (string) $id];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_section_3_form_4_pre_data_specific($companycode, $config_tid, $section_1_tid, $section_2_tid, $typeofdata, $email, $role, $custcode)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $arr_purpose = [];

    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];
    $typeofdata_master = $typeofdata;
    if ($typeofdata == 'Customer' || $typeofdata == 'Employee') {
    } else {
      $typeofdata_master = 'Customer';
    }

    $result_purpose = $session->execute($session->prepare("SELECT purpose FROM pdam_section_3_form_2_purpose WHERE refid=? AND companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($section_2_tid, $companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    $mapped_to_arr = [];
    foreach ($result_purpose as $row_purpose) {
      $result_pdpr = $session->execute($session->prepare("SELECT question,mappedto FROM purpose_master WHERE mapping_id=? ALLOW FILTERING"), array('arguments' => array($row_purpose['purpose'])));
      if ($result_pdpr->count() > 0) {
        foreach ($result_pdpr as $value_pdpd) {
          $mapped_to_delimeter = explode(",", $value_pdpd['mappedto']);
          $mapped_to_arr = array_merge($mapped_to_arr, $mapped_to_delimeter);
        }
      } else {
        $result_pdam_2 = $session->execute($session->prepare("SELECT purpose,id FROM pdam_purpose WHERE companycode=? AND typeofdata=? AND status=? AND usability=? AND id=? ALLOW FILTERING"), array('arguments' => array($companycode, $typeofdata, "1", "External", $row_purpose['purpose'])));
        if ($result_pdam_2->count() > 0) {
          array_push($mapped_to_arr, $row_purpose['purpose']);
        }
      }
    }

    array_unique($mapped_to_arr);
    foreach ($mapped_to_arr as $mapped_to) {
      $result_pdpr = $session->execute($session->prepare("SELECT question,purpose,mapping_id FROM purpose_master WHERE mapping_id=? ALLOW FILTERING"), array('arguments' => array($mapped_to)));
      if ($result_pdpr->count() > 0) {
        $arr_purpose[$result_pdpr[0]['mapping_id']] = ["question" => $result_pdpr[0]['question'], "purpose" => $result_pdpr[0]['purpose']];
      } else {
        $result_pdam_2_alt = $session->execute($session->prepare("SELECT purpose,id FROM pdam_purpose WHERE companycode=? AND typeofdata=? AND status=? AND usability=? AND id=? ALLOW FILTERING"), array('arguments' => array($companycode, $typeofdata, "1", "External", $mapped_to)));
        if ($result_pdam_2_alt->count() > 0) {
          $arr_purpose[$result_pdam_2_alt[0]['id']] = ["question" => $result_pdam_2_alt[0]['purpose'], "purpose" => $result_pdam_2_alt[0]['purpose']];
        }
      }
    }

    $result_pdam = $session->execute($session->prepare("SELECT purpose,id FROM pdam_purpose WHERE companycode=? AND typeofdata=? AND status=? AND usability=? ALLOW FILTERING"), array('arguments' => array($companycode, $typeofdata, "1", "Internal")));
    foreach ($result_pdam as $row_pdam) {
      $arr_purpose[$row_pdam['id']] = ["question" => $row_pdam['purpose'], "purpose" => $row_pdam['purpose']];
    }

    $result = $session->execute($session->prepare("SELECT application_name FROM pdam_section_3_form_1 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    foreach ($result as $row) {
      $result_pd_sv = $session->execute($session->prepare("SELECT application_name FROM pdam_section_3_form_4_temp WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $row['application_name'])));
      if ($result_pd_sv->count() == 0) {
        array_push($arr, $row['application_name']);
      }
    }
    $asset_list_inp = asset_name_read_with_dept($companycode, $email, $role, $custcode);
    $asset_list = [];
    if ($asset_list_inp['success']) {
      $asset_list = $asset_list_inp['data'];
    }

    array_multisort(array_column($arr_purpose, 'question'), SORT_ASC, $arr_purpose);

    $arr_final = ["form_data" => $arr, "purpose_data" => $arr_purpose, "asset_list" => $asset_list];
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr_final];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function check_if_asset_storage_mapping_is_done($companycode, $email, $role, $custcode, $config_tid, $section_1_tid, $section_2_tid, $typeofdata, $channel_name)
{
  try {
    global $session;
    $arr_1 = [];
    $arr_2 = [];
    $result_1 = $session->execute(
      $session->prepare("SELECT application_name,application_name_id FROM pdam_section_3_form_1 WHERE companycode=? AND section_2_tid=? AND status=? AND typeofdata=? AND channel_name=?"),
      array(
        'arguments' => array(
          $companycode,
          $section_2_tid,
          "1",
          $typeofdata,
          $channel_name
        )
      )
    );

    foreach ($result_1 as $row_1) {
      $arr_1[$row_1['application_name']] = $row_1;
    }

    $result_2 = $session->execute(
      $session->prepare("SELECT application_name FROM pdam_section_3_form_3 WHERE companycode=? AND section_2_tid=? AND status=? AND typeofdata=? AND channel_name=?"),
      array(
        'arguments' => array(
          $companycode,
          $section_2_tid,
          "1",
          $typeofdata,
          $channel_name
        )
      )
    );

    foreach ($result_2 as $row_2) {
      $arr_2[$row_2['application_name']] = $row_2;
    }

    $status = "";

    //Find difference
    if (count($arr_1) > count($arr_2)) {
      $status = "form_3";
    } elseif (count($arr_1) < count($arr_2)) {
      $status = "form_1";
    } else {
      $status = "done";
    }

    $final_arr = [
      "form_1_asset" => $arr_1,
      "form_3_asset" => $arr_2,
      "status" => $status
    ];


    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $final_arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_pd_category_list_for_client($typeofdata, $companycode)
{
  try {
    global $session;
    $arr = [];
    $typeofdata_master = $typeofdata;

    if ($typeofdata == 'Customer' || $typeofdata == 'Employee') {
    } else {
      $typeofdata_master = 'Customer';
    }
    $result = $session->execute($session->prepare("SELECT pdcategory FROM pd_master WHERE type=? ALLOW FILTERING"), array('arguments' => array($typeofdata_master)));
    foreach ($result as $row) {
      if ($row['pdcategory'] != "") {
        $arr[$row['pdcategory']] = $row;
      }
    }

    $result_client = $session->execute($session->prepare("SELECT pdcategory FROM pdam_pd_element WHERE companycode=? AND status=? AND typeofdata=?"), array('arguments' => array($companycode, "1", $typeofdata)));
    foreach ($result_client as $row_client) {
      if ($row_client['pdcategory'] != "") {
        $arr[$row_client['pdcategory']] = $row_client;
      }
    }
    ksort($arr);

    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_section_3_form_1_data_for_edit($companycode, $config_tid, $section_1_tid, $section_2_tid)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $arr_asset = [];

    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "message" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];

    $result = $session->execute($session->prepare("SELECT id,application_name,application_name_id,pdelements,pdcategory,pdsupercategory,pdclassification,modeof_inflow,alsoknownas FROM pdam_section_3_form_1 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    foreach ($result as $row) {
      $row['temp'] = 1;
      $arr[] = $row;
      $arr_asset[$row['application_name']] = ["saved_st" => 0, "id" => "NA", "storage_type" => "", "location" => "", "retention_period" => "", "temp" => 1];
    }

    foreach ($arr_asset as $key_asset => $value) {
      $key_asset = (string) $key_asset;
      $result_form_3 = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_3 WHERE companycode=? AND status=? AND section_2_tid=? AND application_name=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid, $key_asset)));
      if ($result_form_3->count() > 0) {
        $row = $result_form_3[0];
        $retention_period = (int) $row['retention_period'];
        $row['retention_period_year'] = floor($retention_period / 12);
        $row['retention_period_month'] = $retention_period % 12;
        $row['saved_st'] = 1;
        $row['temp'] = 0;
        $arr_asset[$key_asset] = $row;
      }
    }
    $arr_final = ["form_data" => $arr, "asset_data" => $arr_asset];
    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr_final];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function update_pdam_section_form_1($companycode, $email, $role, $custcode, $data, $config_tid, $section_1_tid, $section_2_tid, $typeofdata, $channel_name)
{
  try {
    global $session;
    //Data validation
    $config_tid = escape_input($config_tid);
    $section_1_tid = escape_input($section_1_tid);
    $section_2_tid = escape_input($section_2_tid);
    $typeofdata = escape_input($typeofdata);
    $channel_name = escape_input($channel_name);

    if ($config_tid == "" || $section_1_tid == "" || $section_2_tid == "" || $typeofdata == "" || $channel_name == "") {
      $arr_return = ["success" => false, "message" => "Bad Request"];
      return $arr_return;
      exit();
    }

    $section_header = "Collection Channel Application Name";
    $section_header_id = "FORM-1";

    foreach ($data as $key => $value) {
      $id = escape_input($value['id']);
      $type = escape_input($value['type']);
      $application_name_id = escape_input($value['application_name_id']);
      $pdcategory = escape_input($value['pdcategory']);
      $pdelement = escape_input($value['pdelement']);
      $pdsupercategory = escape_input($value['pdsupercategory']);
      $pdclassification = escape_input($value['pdclassification']);
      $modeof_inflow = escape_input($value['modeof_inflow']);
      $alsoknownas = escape_input($value['alsoknownas']);

      $result_asset_tx = $session->execute($session->prepare("SELECT assetname FROM transasscust WHERE transasscust=?"), array('arguments' => array(new \Cassandra\Uuid($application_name_id))));
      if ($result_asset_tx->count() == 0) {
        $arr_return = ["success" => false, "message" => "Application name is not valid"];
        return $arr_return;
        exit();
      }
      $application_name = $result_asset_tx[0]['assetname'];

      if ($type == "new") {
        $id = (string) new \Cassandra\Uuid();
        $createdate = new \Cassandra\Timestamp();
        $effectivedate = new \Cassandra\Timestamp();
        $modifydate = new \Cassandra\Timestamp();
      } else {
        $result_createdate = $session->execute($session->prepare("SELECT createdate,effectivedate,channel_name,typeofdata,application_name FROM pdam_section_3_form_1 WHERE companycode=? AND section_2_tid=? AND status=? AND id=? ALLOW FILTERING"), array('arguments' => array($companycode, $section_2_tid, '1', $id)));
        if ($result_createdate->count() == 0) {
          $arr_return = ["success" => false, "message" => "Invalid Choice"];
          return $arr_return;
          exit();
        }
        $createdate = $result_createdate[0]['createdate'];
        $effectivedate = $result_createdate[0]['effectivedate'];
        $modifydate = new \Cassandra\Timestamp();
        $channel_name = $result_createdate[0]['channel_name'];
        $typeofdata = $result_createdate[0]['typeofdata'];
        $application_name_ext = $result_createdate[0]['application_name'];
      }

      $columns = [
        "id",
        "companycode",
        "status",
        "createdate",
        "effectivedate",
        "modifydate",
        "typeofdata",
        "channel_name",
        "application_name",
        "pdelements",
        "pdcategory",
        "pdsupercategory",
        "pdclassification",
        "modeof_inflow",
        "section_header",
        "section_header_id",
        "config_tid",
        "section_1_tid",
        "section_2_tid",
        "alsoknownas",
        "application_name_id"
      ];
      $columns_data = [
        $id,
        $companycode,
        "1",
        $createdate,
        $effectivedate,
        $modifydate,
        $typeofdata,
        $channel_name,
        $application_name,
        $pdelement,
        $pdcategory,
        $pdsupercategory,
        $pdclassification,
        $modeof_inflow,
        $section_header,
        $section_header_id,
        $config_tid,
        $section_1_tid,
        $section_2_tid,
        $alsoknownas,
        $application_name_id
      ];
      $data_for_insert = [
        "action" => "insert", //read/insert/update/delete
        "table_name" => "pdam_section_3_form_1", //provide actual table name or dummy table name thats been in JSON/arr file
        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      $table_insert = table_crud_actions($data_for_insert);
      if (!$table_insert['success']) {
        $arr_return = ["success" => false, "message" => $table_insert['msg']];
        exit();
      }

      if ($application_name != $application_name_ext) {
        //Delete the old one
        $session->execute(
          $session->prepare("DELETE FROM pdam_section_3_form_1 WHERE companycode=? AND section_2_tid=? AND status=? AND typeofdata=? AND channel_name=? AND application_name=? AND createdate=? AND id=?"),
          array(
            'arguments' => array(
              $companycode,
              $section_2_tid,
              "1",
              $typeofdata,
              $channel_name,
              $application_name_ext,
              $createdate,
              $id
            )
          )
        );
      }
    }

    $arr_return = ["code" => 200, "success" => true, "message" => "Data Inserted Successfully", "data" => ""];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function pd_details_by_pd_category($pdcategory, $typeofdata)
{
  try {
    global $session;
    $arr_pds = [];
    $arr_pdc = [];
    if ($pdcategory == "" || $typeofdata == "") {
      $arr_return = ["success" => false, "message" => "Error Occured", "data" => ""];
      return $arr_return;
      exit();
    }
    $result = $session->execute($session->prepare("SELECT pdsupercategory,pdclassification FROM pd_master WHERE pdcategory=? AND type=? ALLOW FILTERING"), array('arguments' => array($pdcategory, $typeofdata)));
    if ($result->count() == 0) {
      $result = $session->execute($session->prepare("SELECT pdsupercategory,pdclassification FROM pd_master WHERE pdcategory=? AND type=? ALLOW FILTERING"), array('arguments' => array($pdcategory, "Customer")));
    }
    foreach ($result as $row) {
      array_push($arr_pds, $row['pdsupercategory']);
      array_push($arr_pdc, $row['pdclassification']);
    }
    $arr_pds = array_unique($arr_pds);
    $arr_pdc = array_unique($arr_pdc);
    sort($arr_pds);
    sort($arr_pdc);

    $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => ["pdsupercategory" => $arr_pds, "pdclassification" => $arr_pdc]];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function pdam_section_3_new_pd_element($new_pdelement, $new_pdcategory, $new_pdsupercategory, $new_pdclassification, $new_typeofdata_afm, $email, $name, $role, $custcode, $companycode)
{
  try {
    global $session;
    $arr_return = [];
    $result_master = $session->execute(
      $session->prepare("SELECT pdelements FROM pd_master WHERE pdelements=? AND type =? AND status = ? ALLOW FILTERING"),
      array('arguments' => array($new_pdelement, $new_typeofdata_afm, "1"))
    );
    if ($result_master->count() > 0) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Already Exist", "error" => "Already Exist"];
      return $arr_return;
      exit();
    }
    $result = $session->execute(
      $session->prepare("SELECT pdelements FROM pdam_pd_element WHERE companycode=? AND pdelements=? AND typeofdata =? AND status=? ALLOW FILTERING"),
      array('arguments' => array($companycode, $new_pdelement, $new_typeofdata_afm, "1"))
    );
    if ($result->count() > 0) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Already Exist", "error" => "Already Exist"];
      return $arr_return;
      exit();
    }

    //Add to support master
    $support_data = [
      "pdelements" => $new_pdelement,
      "pdcategory" => $new_pdcategory,
      "pdsupercategory" => $new_pdsupercategory,
      "pdclassification" => $new_pdclassification,
      "typeofdate" => $new_typeofdata_afm
    ];
    // insert into support_master_update
    $id_new = new \Cassandra\Uuid();
    $columns = [
      "requestor_companycode",
      "master_table",
      "createdate",
      "effectivedate",
      "id",
      "data_to_add",
      "mail_flag",
      "master_name",
      "requestor_email",
      "requestor_name",
      "status"
    ];
    $columns_data = [
      $companycode,
      "pd_master",
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      (string) $id_new,
      json_encode($support_data),
      "0",
      "PD Elements",
      $email,
      $role,
      "1"
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "support_master_update", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);
    //Send mail to help@arrka.com


    $id = new \Cassandra\Uuid();
    $columns = [
      "id",
      "companycode",
      "createdate",
      "effectivedate",
      "pdcategory",
      "pdclassification",
      "pdelements",
      "pdsupercategory",
      "typeofdata",
      "status",
      "filleremail",
      "fillerrole",
      "support_notice_flag"
    ];

    $columns_data = [
      (string) $id,
      $companycode,
      new \Cassandra\timestamp(),
      new \Cassandra\timestamp(),
      $new_pdcategory,
      $new_pdclassification,
      $new_pdelement,
      $new_pdsupercategory,
      $new_typeofdata_afm,
      "1",
      $email,
      $role,
      "0"
    ];

    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "pdam_pd_element", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];

    $table_insert = table_crud_actions($data_for_insert);

    if (!$table_insert['success']) {
      $arr_return = ["code" => 500, "success" => false, "message" => "Error Occured", "data" => $table_insert['msg'], "error" => "Error Occured"];
    } else {
      $arr_to_send = ["pdelements" => $new_pdelement, "pdcategory" => $new_pdcategory, "pdsupercategory" => $new_pdsupercategory, "pdclassification" => $new_pdclassification, "typeofdata" => $new_typeofdata_afm];
      $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr_to_send];
    }
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_section_3_form_1_data_asset_grouped($companycode, $config_tid, $section_1_tid, $section_2_tid, $fetch_data_type)
{
  try {
    global $session;
    $arr_asset = [];

    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "msg" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];
    if ($fetch_data_type == "temp") {
      $result = $session->execute($session->prepare("SELECT application_name FROM pdam_section_3_form_1_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    } else {
      $result = $session->execute($session->prepare("SELECT application_name FROM pdam_section_3_form_1 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
      if ($result->count() == 0) {
        $result = $session->execute($session->prepare("SELECT application_name FROM pdam_section_3_form_1_temp WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
      }
    }
    foreach ($result as $row) {
      array_push($arr_asset, $row['application_name']);
    }
    $arr_asset = array_unique($arr_asset);
    sort($arr_asset);

    $arr_return = ["code" => 200, "success" => true, "msg" => "Success", "data" => $arr_asset];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function load_section_3_form_1_data_for_report($companycode, $config_tid, $section_1_tid, $section_2_tid)
{
  try {
    global $session;
    $arr_return = [];
    $arr = [];
    $arr_asset = [];

    $get_section_2_data = get_section_2_data($section_2_tid);
    if (!$get_section_2_data['success']) {
      $arr_return = ["success" => false, "msg" => "Invalid transaction"];
      return $arr_return;
      exit();
    }
    $channel_name = $get_section_2_data['data']['channel_name'];
    $typeofdata = $get_section_2_data['data']['typeofdata'];

    $result_sv = $session->execute($session->prepare("SELECT id,application_name,pdelements,pdcategory,pdsupercategory,pdclassification,modeof_inflow FROM pdam_section_3_form_1 WHERE companycode=? AND status=? AND section_2_tid=? AND typeofdata=? AND channel_name=?"), array('arguments' => array($companycode, "1", $section_2_tid, $typeofdata, $channel_name)));
    foreach ($result_sv as $row_sv) {
      $row_sv['temp'] = 0;
      $arr[] = $row_sv;
    }

    $result_form_3 = $session->execute($session->prepare("SELECT * FROM pdam_section_3_form_3 WHERE companycode=? AND status=? AND section_2_tid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $section_2_tid)));
    foreach ($result_form_3 as $row_f3) {
      $row_f3['data_type_show'] = ucwords($row_f3['data_type']);
      $arr_asset[] = $row_f3;
    }

    $arr_final = ["form_data" => $arr, "asset_data" => $arr_asset];
    $arr_return = ["code" => 200, "success" => true, "msg" => "Success", "data" => $arr_final];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function pdam_section_department_process($department_name, $companycode, $email, $name, $role, $custcode)
{
  try {
    global $session;
    $arr_return = [];
    //check if the dept is already in the client dept list
    //Return message that department already exist
    $client_department_read = client_department_read($companycode);
    if (!$client_department_read['success']) {
      return $client_department_read;
      exit();
    }

    $client_department_list = $client_department_read['data'];
    if (in_array($department_name, $client_department_list)) {
      $arr_return = ["success" => false, "msg" => "Department already exist"];
      return $arr_return;
      exit();
    }

    //Check if the data is from master or new entry
    //if not in master then send mail to help@arrka.com
    $result_val = $session->execute($session->prepare("SELECT deptname FROM departments WHERE deptname=? ALLOW FILTERING"), array('arguments' => array($department_name)));
    if ($result_val->count() == 0) {
      // insert into support_master_update
      $id_new = new \Cassandra\Uuid();
      $columns = [
        "requestor_companycode",
        "master_table",
        "createdate",
        "effectivedate",
        "id",
        "data_to_add",
        "mail_flag",
        "master_name",
        "requestor_email",
        "requestor_name",
        "status",
      ];
      $columns_data = [
        $companycode,
        "departments",
        new \Cassandra\Timestamp(),
        new \Cassandra\Timestamp(),
        (string) $id_new,
        $department_name,
        "0",
        "Department",
        $email,
        $role,
        "1"
      ];
      $data_for_insert = [
        "action" => "insert", //read/insert/update/delete
        "table_name" => "support_master_update", //provide actual table name or dummy table name thats been in JSON/arr file
        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      $table_insert = table_crud_actions($data_for_insert);
      //Send mail to help@arrka.com

    }

    $id = new \Cassandra\Uuid();
    $columns = [
      "id",
      "locationdepartment",
      "createdate",
      "effectivedate",
      "companycode"
    ];
    $columns_data = [
      $id,
      $department_name,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      $companycode
    ];
    $data_for_insert = [
      "action" => "insert", //read/insert/update/delete
      "table_name" => "locationinscope", //provide actual table name or dummy table name thats been in JSON/arr file
      "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);

    if ($table_insert['success']) {
      $arr_return = ["code" => 200, "success" => true, "msg" => "New Department Added", "data" => $department_name];
    } else {
      $arr_return = ["code" => 500, "success" => false, "msg" => "Error Occured"];
    }
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}


//HELPER_FUNCTION
function client_department_read($companycode)
{
  try {
    global $session;
    $arr_d = [];
    $result = $session->execute($session->prepare("SELECT locationdepartment FROM locationinscope WHERE companycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    foreach ($result as $row_d) {
      $dept = explode("|", $row_d['locationdepartment']);
      foreach ($dept as $det) {
        $dep_t = explode(",", $det);
        if ($dep_t[0] !== "") {
          array_push($arr_d, $dep_t[0]);
        }
      }
    }

    $arr_unique = array_unique($arr_d);
    sort($arr_unique);
    $arr_return = ["code" => 200, "success" => true, "message" => "Data fetched successful", "data" => $arr_unique];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}


// $arr_return = ["code" => 200, "success" => true, "message" => "Success", "data" => $arr];
//     return $arr_return;
//   } catch (Exception $e) {
//     return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
//   }

?>