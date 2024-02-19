<?php
function GetConfigHandler($funcCallType)
{
    try {
        switch ($funcCallType) {

            // Action Config
            case "action-config-generate-tid":
                if (isset($GLOBALS['companycode'])) {
                    $output = action_config_generate_tid($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "config-action-data-check":
                if (isset($GLOBALS['companycode'])) {
                    $output = config_action_data_check($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "get-default-config-define-action":
                if (isset($GLOBALS['companycode'])) {
                    $output = getDefaultConfigDefineAction($_GET['tid'], $GLOBALS['companycode']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "action-change-data-save":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['change_data_save'], $json['change_txn_id'], $json['form_type'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'])) {


                    $output = action_change_data_save($json['change_data_save'], $json['change_txn_id'], $json['form_type'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "action-config-final-submit":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['txn_id'], $GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = action_config_final_submit($json['txn_id'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "role-by-name":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['role'], $GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = role_by_name($json['role'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;



            // Asset Register Config
            case "asset-register-generate-tid":
                if (isset($GLOBALS['companycode'])) {
                    $output = asset_register_generate_tid($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "load-asset-config-data":
                if (isset($_GET['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = load_asset_config_data($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "check-if-data-exist-in-asset-config":
                if (isset($_GET['config_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = check_if_data_exist_in_asset_config($_GET['config_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "save-asset-config":

                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['save_asset_config']) && isset($json['save_asset_type']) && isset($json['txn_id_asset']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = save_asset_config($json['save_asset_config'], $json['save_asset_type'], $json['txn_id_asset'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;
            case "asset-save-config-final-submit":

                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = asset_save_config_final_submit($json['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            // Change Managemnt Config 
            case "change-manage-generate-tid":
                if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = change_manage_generate_tid($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "change-config-check-all-data-filled":
                if (isset($_GET['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = change_config_check_all_data_filled($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;
            case "get-default-config-change":
                if (isset($_GET['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_default_config_change($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "change-no-of-min-approver-cab":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);
                if (isset($json['no_of_min_approver']) && isset($json['txn_id_incident']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = change_no_of_min_approver_cab($json['no_of_min_approver'], $json['txn_id_incident'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "change-config-final-submit":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = change_config_final_submit($json['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            // DSRR Config 

            case "dsrr-config-generate-tid":
                if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = dsrr_config_generate_tid($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "get-default-config":
                if (isset($_GET['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_default_config($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "dssr-config-data-save":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['dssr_txn_id']) && isset($json['dssr_data_save']) && isset($json['form_type']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = dssr_config_data_save($json['dssr_txn_id'], $json['dssr_data_save'], $json['form_type'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "dssr-config-check-all-data-filled":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = dssr_config_check_all_data_filled($json['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "dssr-config-final-submit":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = dssr_config_final_submit($json['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            // Breach Config 
            case "breach-config-generate-tid":
                if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = breach_config_generate_tid($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "company-category-master-write":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['companycategorymaster_arr']) && isset($json['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = company_category_master_write($json['companycategorymaster_arr'], $json['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "company-tatmaster-write":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['companytatmaster_arr']) && isset($json['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = company_tatmaster_write($json['companytatmaster_arr'], $json['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "company-iamaster-write":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['companyiamaster_arr']) && isset($json['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = company_iamaster_write($json['companyiamaster_arr'], $json['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;





            // Kpi Config 
            case "kpi-config-generate-tid":
                if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = kpi_config_generate_tid($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "arrka-kpimaster-read":
                if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = arrka_kpimaster_read($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;


            case "cisoconfig-data-save":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($json['ciso_txn_id']) && isset($json['cisoconfig_data_save']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = cisoconfig_data_save($json['ciso_txn_id'], $json['cisoconfig_data_save'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "incident-screen-1-final-submit":
                $jsonString = file_get_contents('php://input');
                $json = json_decode($jsonString, true);

                if (isset($_POST['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = incident_screen_1_final_submit($_POST['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;
            default:
                catchErrorHandler(400, ["message" => E_INV_REQ, "error" => ""]);
                break;
        }
    } catch (Exception $e) {
        catchErrorHandler($output['code'], ["message" => "", "error" => $e->getMessage()]);
    }
}




// Action Config

function action_config_generate_tid($companycode, $email, $role)
{
    try {
        global $session;
        $createNew = false;
        $result_version = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "def_action")));
        if ($result_version->count() > 0) {
            $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "progress", "def_action")));
            if ($result->count() > 0) {
                $link = (string) $result[0]['wcvid'];
                $arr_return = ["code" => 200, "success" => true, "data" => $link];
                return $arr_return;
            } else {
                $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "active", "def_action")));
                if ($result->count() > 0) {
                    $link = (string) $result[0]['wcvid'];
                    $arr_return = ["code" => 200, "success" => true, "data" => $link];
                    return $arr_return;
                } else {
                    $createNew = true;
                }
            }
        } else {
            $createNew = true;
        }

        if ($createNew) {
            $version_no = (string) $result_version->count();
            $version_name = "Version" . (string) $result_version->count();
            $txn_id = new \Cassandra\Uuid();
            $query_insert = $session->prepare('INSERT INTO workflowconfigversions(wcvid,createdate,effectivedate,custemail,status,wcvcompanycode,wcvversionname,wcvversionno,wcvworkflowname,form_status,screen_status,active_status,wvtype,cab_approver_num)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        $txn_id,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $email,
                        "1",
                        $companycode,
                        $version_name,
                        $version_no,
                        "def_action",
                        "0",
                        "0",
                        "progress",
                        "start",
                        "1"
                    )
                )
            );
            $link = (string) $txn_id;
            $arr_return = ["code" => 200, "success" => true, "data" => $link];
            return $arr_return;
        }
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}
function config_action_data_check($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $res_txn = $session->execute($session->prepare("SELECT wcvid,wvtype,form_status,screen_status,cab_approver_num,active_status FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? AND status=? AND wcvid=? ALLOW FILTERING"), array('arguments' => array($companycode, "def_action", "1", new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => $res_txn[0]];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function getDefaultConfigDefineAction($tid, $companycode)
{
    try {
        global $session;
        $arr = [];
        $arr['cab'] = [];
        $arr['validator'] = [];
        $result = $session->execute($session->prepare("SELECT * FROM defineactioncompanycab WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['cab'][] = $row;
        }
        $result = $session->execute($session->prepare("SELECT * FROM defineactioncompanyvalidator WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['validator'][] = $row;
        }

        $arr_return = ["code" => 200, "success" => true, "data" => $arr];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function action_change_data_save($data, $tid, $form, $companycode, $email, $role)
{
    try {
        global $session;

        switch ($form) {
            //Form-2
            case 'form-2':

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanypriority WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanypriority WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $priorityno = count($data) - $key;
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanypriority(id,companycode,createdate,effectivedate,filleremail,fillerrole,priorityclass,priorityno,sorting_order,status,workflowname,transactionid)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['priorityclass'],
                                (string) $priorityno,
                                $key,
                                "1",
                                "change",
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));

                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;


                break;
            //Form-3
            case 'form-3':

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanycategory WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanycategory WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanycategory(id,companycode,createdate,effectivedate,filleremail,fillerrole,categoryname,sorting_order,status,workflowname,transactionid)
                VALUES(?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['categoryname'],
                                $key,
                                "1",
                                "change",
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("3", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;


            //Form-4
            case 'form-4':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanybiclass WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanybiclass WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $priorityno = count($data) - $key;
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanybiclass(id,companycode,createdate,effectivedate,filleremail,fillerrole,biclassname,biclassno,sorting_order,status,workflowname,transactionid)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['biclassname'],
                                (string) $priorityno,
                                $key,
                                "1",
                                "change",
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;

            //Form 2A for saving DPIA approver

            case 'form-2A':

                //Validating data
                foreach ($data as $key_val => $value_val) {
                    foreach ($value_val as $key_chk => $value_chk) {
                        if ($value_chk == '') {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Values cannot be null for business impact"];
                            exit();
                        }
                    }
                }

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanybiclass WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanybiclass WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $priorityno = count($data) - $key;
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanybiclass(id,companycode,createdate,effectivedate,filleremail,fillerrole,biclassname,biclassno,sorting_order,status,workflowname,transactionid)
          VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['biclassname'],
                                (string) $priorityno,
                                $key,
                                "1",
                                "dpia",
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;

            //Form-5
            case 'form-5':

                //Validating data
                foreach ($data as $key_val => $value_val) {
                    foreach ($value_val as $key_chk => $value_chk) {
                        if ($value_chk == '') {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Values cannot be null in approval matrix"];
                            exit();
                        }
                    }
                }

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanyapprovalmatrix WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanyapprovalmatrix WHERE id=?"), array('arguments' => array($row['id'])));
                }

                //Saving the data
                foreach ($data as $key => $value) {

                    //Approver name
                    $count++;
                    $approver2aname = get_name_by_email($value['approver2aname']);

                    //security
                    $secuiryapprovername = get_name_by_email($value['secuiryapprovername']);

                    //cab
                    $approver2cname = get_name_by_email($value['approver2cname']);

                    //implementor
                    $implementorapprovername = get_name_by_email($value['implementorapprovername']);


                    $result_bi = $session->execute(
                        $session->prepare("SELECT biclassno FROM changecompanybiclass WHERE companycode=? AND transactionid=? AND status=? AND biclassname=? ALLOW FILTERING"),
                        array(
                            'arguments' => array(
                                $companycode,
                                $tid,
                                "1",
                                $value['biclassname']
                            )
                        )
                    );

                    $biclassno = $result_bi[0]['biclassno'];

                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanyapprovalmatrix(id,companycode,createdate,effectivedate,filleremail,fillerrole,biclassname,biclassno,
                 approver2arole,approver2aname,approver2aemail,approver2crole,approver2cname,approver2cemail,categoryname,secuiryapprovername,secuiryapproveremail,secuiryapproverrole,sorting_order,status,workflowname,transactionid,implementorapproverrole,implementorapprovername,implementorapproveremail,sla)
                 VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['biclassname'],
                                $biclassno,
                                $value['approver2arole'],
                                $approver2aname,
                                $value['approver2aname'],
                                $value['approver2crole'],
                                $approver2cname,
                                $value['approver2cname'],
                                $value['categoryname'],
                                $secuiryapprovername,
                                $value['secuiryapprovername'],
                                $value['secuiryapproverrole'],
                                $key,
                                "1",
                                "change",
                                $tid,
                                $value['implementorapproverrole'],
                                $implementorapprovername,
                                $value['implementorapprovername'],
                                $value['sla']
                            )
                        )
                    );


                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;

            //Form 3A for DPIA
            case 'form-3A':
                //Validating data
                // foreach ($data as $key_val => $value_val) {
                //
                // }

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanycab WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanycab WHERE id=?"), array('arguments' => array($row['id'])));
                }

                //Saving the data
                foreach ($data as $key => $value) {
                    foreach ($value as $key_chk => $value_chk) {
                        if ($value_chk == '') {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Values cannot be null in approval matrix"];
                            exit();
                        }
                    }
                    //Approver name
                    // $count++;
                    $cabname = get_name_by_email($value['cabname']);



                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();
                    $columns = [
                        "id",
                        "companycode",
                        "createdate",
                        "effectivedate",
                        "filleremail",
                        "fillerrole",
                        "cabemail",
                        "cabname",
                        "cabrole",
                        "
           sorting_order",
                        "status",
                        "workflowname",
                        "transactionid"
                    ];

                    $columns_data = [
                        $uuid,
                        $companycode,
                        $timestamp,
                        $timestamp,
                        $email,
                        $role,
                        $value['cabname'],
                        $cabname,
                        $value['cabrole'],
                        $key,
                        "1",
                        "dpia",
                        $tid
                    ];
                    $data_for_crud = [
                        "action" => "insert", //read/insert/update/delete
                        "table_name" => "changecompanycab", //provide actual table name or dummy table name thats been in JSON/arr file
                        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
                        "isCondition" => true,
                        "condition_columns" => [],
                        "columns_data" => $columns_data,
                        "isAllowFiltering" => false
                    ];
                    $output = table_crud_actions($data_for_crud);
                    if (!$output['success']) {
                        echo $output['msg'];
                        exit();
                    }


                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;
            //Form 3A for TRA
            case 'form-3T':

                //Validating data
                // foreach ($data as $key_val => $value_val) {
                //
                // }

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM tracompanycab WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM tracompanycab WHERE id=?"), array('arguments' => array($row['id'])));
                }

                //Saving the data
                foreach ($data as $key => $value) {
                    foreach ($value as $key_chk => $value_chk) {
                        if ($value_chk == '') {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Values cannot be null in approval matrix"];
                            exit();
                        }
                    }
                    //Approver name
                    // $count++;
                    $cabname = get_name_by_email($value['cabname']);



                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();
                    $columns = [
                        "id",
                        "companycode",
                        "createdate",
                        "effectivedate",
                        "filleremail",
                        "fillerrole",
                        "cabemail",
                        "cabname",
                        "cabrole",
                        "sorting_order",
                        "status",
                        "workflowname",
                        "transactionid"
                    ];

                    $columns_data = [
                        $uuid,
                        $companycode,
                        $timestamp,
                        $timestamp,
                        $email,
                        $role,
                        $value['cabname'],
                        $cabname,
                        $value['cabrole'],
                        $key,
                        "1",
                        "tra_2",
                        $tid
                    ];
                    $data_for_crud = [
                        "action" => "insert", //read/insert/update/delete
                        "table_name" => "tracompanycab", //provide actual table name or dummy table name thats been in JSON/arr file
                        "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
                        "isCondition" => true,
                        "condition_columns" => [],
                        "columns_data" => $columns_data,
                        "isAllowFiltering" => false
                    ];
                    $output = table_crud_actions($data_for_crud);
                    if (!$output['success']) {
                        echo $output['msg'];
                        exit();
                    }


                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;
            case 'form-3DA':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM defineactioncompanycab WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM defineactioncompanycab WHERE id=?"), array('arguments' => array($row['id'])));
                }


                //Saving the data
                foreach ($data as $key => $value) {

                    foreach ($value as $key_chk => $value_chk) {
                        if ($value_chk == '') {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Values cannot be null in approval matrix"];
                            exit();
                        }
                    }

                    //Approver name
                    // $count++;
                    $cabname = get_name_by_email($value['cabname']);
                    $result_cab = $session->execute($session->prepare("SELECT id FROM defineactioncompanycab WHERE companycode=? AND transactionid=? AND status=? AND cabname=? AND cabrole=? AND cabemail =? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1", $cabname, $value['cabrole'], $value['cabname'])));


                    if ($result_cab->count() == 0) {

                        $uuid = new \Cassandra\Uuid();
                        $timestamp = new \Cassandra\Timestamp();
                        $columns = [
                            "id",
                            "companycode",
                            "createdate",
                            "effectivedate",
                            "filleremail",
                            "fillerrole",
                            "cabemail",
                            "cabname",
                            "cabrole",
                            "sorting_order",
                            "status",
                            "workflowname",
                            "transactionid"
                        ];

                        $columns_data = [
                            $uuid,
                            $companycode,
                            $timestamp,
                            $timestamp,
                            $email,
                            $role,
                            $value['cabname'],
                            $cabname,
                            $value['cabrole'],
                            $key,
                            "1",
                            "def_action",
                            $tid
                        ];
                        $data_for_crud = [
                            "action" => "insert", //read/insert/update/delete
                            "table_name" => "defineactioncompanycab", //provide actual table name or dummy table name thats been in JSON/arr file
                            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
                            "isCondition" => true,
                            "condition_columns" => [],
                            "columns_data" => $columns_data,
                            "isAllowFiltering" => false
                        ];
                        $output = table_crud_actions($data_for_crud);
                        if (!$output['success']) {
                            $arr_return = ["code" => 200, "success" => true, "data" => $output['msg']];
                            return $arr_return;
                        }
                    }
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;
            case 'form-3DAV':

                //Validating data
                // foreach ($data as $key_val => $value_val) {
                //
                // }

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM defineactioncompanyvalidator WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM defineactioncompanyvalidator WHERE id=?"), array('arguments' => array($row['id'])));
                }

                //Saving the data
                foreach ($data as $key => $value) {
                    foreach ($value as $key_chk => $value_chk) {
                        if ($value_chk == '') {
                            echo "Values cannot be null in approval matrix";
                            exit();
                        }
                    }
                    //Approver name
                    // $count++;
                    $cabname = get_name_by_email($value['cabname']);
                    $result_cab = $session->execute($session->prepare("SELECT id FROM defineactioncompanyvalidator WHERE companycode=? AND transactionid=? AND status=? AND cabname=? AND cabrole=? AND cabemail =? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1", $cabname, $value['cabrole'], $value['cabname'])));
                    if ($result_cab->count() == 0) {


                        $uuid = new \Cassandra\Uuid();
                        $timestamp = new \Cassandra\Timestamp();
                        $columns = [
                            "id",
                            "companycode",
                            "createdate",
                            "effectivedate",
                            "filleremail",
                            "fillerrole",
                            "cabemail",
                            "cabname",
                            "cabrole",
                            "sorting_order",
                            "status",
                            "workflowname",
                            "transactionid"
                        ];

                        $columns_data = [
                            $uuid,
                            $companycode,
                            $timestamp,
                            $timestamp,
                            $email,
                            $role,
                            $value['cabname'],
                            $cabname,
                            $value['cabrole'],
                            $key,
                            "1",
                            "def_action",
                            $tid
                        ];
                        $data_for_crud = [
                            "action" => "insert", //read/insert/update/delete
                            "table_name" => "defineactioncompanyvalidator", //provide actual table name or dummy table name thats been in JSON/arr file
                            "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
                            "isCondition" => true,
                            "condition_columns" => [],
                            "columns_data" => $columns_data,
                            "isAllowFiltering" => false
                        ];
                        $output = table_crud_actions($data_for_crud);
                        if (!$output['success']) {

                            $arr_return = ["code" => 200, "success" => true, "data" => $output['msg']];
                            return $arr_return;
                        }

                    }
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;
            //Form-6
            case 'form-6':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanyimplementationmatrix WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanyimplementationmatrix WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Validating data
                foreach ($data as $key_val => $value_val) {
                    foreach ($value_val as $key_chk => $value_chk) {
                        if ($value_chk == '') {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Values cannot be null in approval matrix"];
                            exit();
                        }
                    }
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanyimplementationmatrix(id,companycode,createdate,effectivedate,filleremail,fillerrole,implementationfailure,implementationsuccess,
                 sorting_order,status,workflowname,transactionid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['implementationfailure'],
                                $value['implementationsuccess'],
                                $key,
                                "1",
                                "change",
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;
            case 'form-6T':
                //Validating data
                foreach ($data as $key_val => $value_val) {
                    foreach ($value_val as $key_chk => $value_chk) {
                        if ($value_chk == '') {

                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Values cannot be null in approval matrix"];
                            exit();
                        }
                    }
                }

                $threshold_high = $data[0]['threshold'];
                $accept_mitigate_high = $data[0]['accept_mitigate'];
                $threshold_medium = $data[1]['threshold'];
                $accept_mitigate_medium = $data[1]['accept_mitigate'];
                $threshold_low = $data[2]['threshold'];
                $accept_mitigate_low = $data[2]['accept_mitigate'];

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM trariskacceptance WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM trariskacceptance WHERE id=?"), array('arguments' => array($row['id'])));
                }

                //Saving the data

                $uuid = new \Cassandra\Uuid();
                $timestamp = new \Cassandra\Timestamp();

                $query_insert = $session->prepare('INSERT INTO trariskacceptance(id,companycode,createdate,effectivedate,filleremail,fillerrole,accepted_high,accepted_medium,accepted_low,
                   status,workflowname,transactionid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
                $session->execute(
                    $query_insert,
                    array(
                        'arguments' => array(
                            $uuid,
                            $companycode,
                            $timestamp,
                            $timestamp,
                            $email,
                            $role,
                            $accept_mitigate_high,
                            $accept_mitigate_medium,
                            $accept_mitigate_low,
                            "1",
                            "tra_2",
                            $tid
                        )
                    )
                );

                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;


            //Form-7
            case 'form-7':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanycab WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanycab WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {

                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanycab(id,companycode,createdate,effectivedate,filleremail,fillerrole,cabemail,cabname,cabrole,
                 sorting_order,status,workflowname,transactionid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['cabemail'],
                                $value['cabname'],
                                $value['cabrole'],
                                $key,
                                "1",
                                "change",
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;
            //Form-8
            case 'form-8':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM changecompanysubcategory WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM changecompanysubcategory WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {

                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    $query_insert = $session->prepare('INSERT INTO changecompanysubcategory(id,companycode,createdate,effectivedate,filleremail,fillerrole,categoryname,subcategoryname,sorting_order,status,workflowname,transactionid)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $role,
                                $value['categoryname'],
                                $value['subcategoryname'],
                                $key,
                                "1",
                                "change",
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;

                break;

        }



    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function role_by_name($role, $companycode, $email, $global_role)
{
    try {
        global $session;
        $arr = [];
        $result_role = $session->execute($session->prepare("SELECT rtccustemail FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtcrole=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $role)));
        foreach ($result_role as $row) {
            array_push($arr, $row['rtccustemail']);
        }
        sort($arr);
        $email_arr = array_unique($arr);
        $final_arr = [];
        foreach ($email_arr as $key => $value) {
            $result_er = $session->execute($session->prepare("SELECT custuserpasswd,custfname,custlname FROM customer WHERE custemailaddress=?"), array('arguments' => array($value)));
            if ($result_er[0]['custuserpasswd'] == '') {
                unset($email_arr[$key]);
            } else {
                $final_arr[$value] = ["name" => $result_er[0]['custfname'] . " " . $result_er[0]['custlname']];
            }
        }

        $arr_return = ["code" => 200, "success" => true, "data" => $final_arr];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


// Asset Register Config Function

function asset_register_generate_tid($companycode, $email, $role)
{
    try {
        global $session;
        $result_version = $session->execute($session->prepare("SELECT wcvversionno FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "asset")));
        if ($result_version->count() > 0) {
            $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "progress", "asset")));
            if ($result->count() > 0) {
                $res = (string) $result[0]['wcvid'];
                $arr_return = ["code" => 200, "success" => true, "data" => $res];
                return $arr_return;
            } else {
                $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "active", "asset")));

                $res = (string) $result[0]['wcvid'];
                $arr_return = ["code" => 200, "success" => true, "data" => $res];
                return $arr_return;
            }
        } else {
            $version_no = (string) $result_version->count();
            $version_name = "Version" . (string) $result_version->count();
            $txn_id = new \Cassandra\Uuid();
            $query_insert = $session->prepare('INSERT INTO workflowconfigversions(wcvid,createdate,effectivedate,custcode,custemail,status,wcvcompanycode,wcvversionname,wcvversionno,wcvworkflowname,form_status,screen_status,active_status,wvtype)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        $txn_id,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $_SESSION['customer_id'],
                        $email,
                        "1",
                        $companycode,
                        $version_name,
                        $version_no,
                        "asset",
                        "0",
                        "0",
                        "progress",
                        "start"
                    )
                )
            );
            $arr_return = ["code" => 200, "success" => true, "data" => $txn_id];
            return $arr_return;
        }
    } catch (\Throwable $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function load_asset_config_data($config_tid, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = [];
        $arr_type = ["confidentiality", "integrity", "availability"];
        $arr_default = ["High", "Medium", "Low"];

        foreach ($arr_type as $type) {
            $result = $session->execute($session->prepare('SELECT category FROM asset_config_data WHERE companycode=? AND transactionid=? AND status=? AND type=?'), array('arguments' => array($companycode, $config_tid, "1", $type)));
            if ($result->count() > 0) {
                $arr_data = [];
                foreach ($result as $row) {
                    array_push($arr_data, $row['category']);
                }
                $arr[$type] = $arr_data;
            } else {
                $arr[$type] = $arr_default;
            }
        }
        $arr_return = ["code" => 200, "success" => true, "data" => $arr];
        return $arr_return;
    } catch (\Throwable $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function check_if_data_exist_in_asset_config($config_tid, $companycode, $email, $role)
{
    try {
        global $session;
        $arr_final = [];
        $type_arr = ["confidentiality", "integrity", "availability"];
        $type_form = [];
        foreach ($type_arr as $type) {
            $result_val = $session->execute($session->prepare('SELECT sorting_order,category FROM asset_config_data WHERE companycode=? AND transactionid=? AND status=? AND type=?'), array('arguments' => array($companycode, $config_tid, "1", $type)));
            if ($result_val->count() == 0) {
                array_push($type_form, ucwords($type));
            }
        }
        if (count($type_form) > 0) {
            $msg = implode(", ", $type_form) . " form is not filled yet. Fill the form first";
            $arr_final = ["success" => false, "msg" => $msg];
        } else {
            $msg = "success";
            $arr_final = ["success" => true, "msg" => $msg];
        }
        $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


function save_asset_config($data, $type, $config_tid, $companycode, $email, $role)
{
    try {
        global $session;

        if (count($data) == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Data should not be empty"];
            exit();
        }

        if ($type == "confidentiality" || $type == "integrity" || $type == "availability") {

        } else {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid type"];
            exit();
        }


        $result = $session->execute($session->prepare('SELECT sorting_order,category FROM asset_config_data WHERE companycode=? AND transactionid=? AND status=? AND type=?'), array('arguments' => array($companycode, $config_tid, "1", $type)));
        foreach ($result as $row) {
            $session->execute(
                $session->prepare('DELETE FROM asset_config_data WHERE companycode=? AND transactionid=? AND status=? AND type=? AND sorting_order=? AND category=?'),
                array(
                    'arguments' => array(
                        $companycode,
                        $config_tid,
                        "1",
                        $type,
                        $row['sorting_order'],
                        $row['category']
                    )
                )
            );
        }

        foreach ($data as $key => $value) {
            $id = new \Cassandra\Uuid();
            $timestamp = new \Cassandra\Timestamp();
            $columns = [
                "companycode",
                "transactionid",
                "status",
                "type",
                "sorting_order",
                "category",
                "createdate",
                "effectivedate",
                "filleremail",
                "fillerrole"
            ];
            $columns_data = [
                $companycode,
                $config_tid,
                "1",
                $type,
                $key,
                $value,
                $timestamp,
                $timestamp,
                $email,
                $role
            ];
            $data_for_insert = [
                "action" => "insert", //read/insert/update/delete
                "table_name" => "asset_config_data", //provide actual table name or dummy table name thats been in JSON/arr file
                "columns" => $columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
                "isCondition" => false,
                "condition_columns" => "",
                "columns_data" => $columns_data,
                "isAllowFiltering" => false
            ];

            $table_insert = table_crud_actions_check($data_for_insert);

            // $arr_return = ["code" => 200, "success" => true, "data" => $table_insert];
            // return $arr_return;


            if (!$table_insert['success']) {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $table_insert['msg']];
                exit();
            }
        }
        $arr_return = ["code" => 200, "success" => true, "data" => "Success"];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function asset_save_config_final_submit($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $type_arr = ["confidentiality", "integrity", "availability"];
        $check_if_data_exist_in_asset_config = check_if_data_exist_in_asset_config($tid, $companycode, $email, $role);
        if (!$check_if_data_exist_in_asset_config['success']) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $check_if_data_exist_in_asset_config['msg']];
            exit();
        }

        $result = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Error Occured. Try Again!!"];
            exit();
        }

        $result_1 = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "asset")));
        foreach ($result_1 as $row_1) {
            $session->execute($session->prepare("UPDATE workflowconfigversions SET active_status=? WHERE wcvid=?"), array('arguments' => array("inactive", $row_1['wcvid'])));
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=?,screen_status=?,active_status=?,wvtype=? WHERE wcvid=?"), array('arguments' => array("0", "1", "active", "edited", new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => "Success"];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}



//  Change Management Functions 

function change_manage_generate_tid($companycode, $email, $role)
{
    try {
        global $session;
        $create_new = 0;
        $result_version = $session->execute($session->prepare("SELECT wcvversionno FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "change")));
        if ($result_version->count() > 0) {
            $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "progress", "change")));
            if ($result->count() > 0) {
                $arr_return = ["code" => 200, "success" => true, "data" => (string) $result[0]['wcvid']];
                return $arr_return;
            } else {
                $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "active", "change")));
                if ($result->count() > 0) {
                    $arr_return = ["code" => 200, "success" => true, "data" => (string) $result[0]['wcvid']];
                    return $arr_return;

                } else {
                    if ($result->count() == 0) {
                        $create_new = 1;
                    }
                }
            }
        } else {
            $create_new = 1;
        }

        if ($create_new == 1) {
            $version_no = (string) $result_version->count();
            $version_name = "NewVersion" . (string) $result_version->count();
            $txn_id = new \Cassandra\Uuid();
            $query_insert = $session->prepare('INSERT INTO workflowconfigversions(wcvid,createdate,effectivedate,custemail,status,wcvcompanycode,wcvversionname,wcvversionno,wcvworkflowname,form_status,screen_status,active_status,wvtype)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        $txn_id,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $email,
                        "1",
                        $companycode,
                        $version_name,
                        $version_no,
                        "change",
                        "0",
                        "0",
                        "progress",
                        "start"
                    )
                )
            );
            $arr_return = ["code" => 200, "success" => true, "data" => $txn_id];
            return $arr_return;

        }
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function change_config_check_all_data_filled($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $output = 1;
        $form_arr = [];
        $cat_arr = [];
        $bi_arr = [];
        $app_arr = [];

        //Transaction validation
        $result = $session->execute($session->prepare("SELECT cab_approver_num FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            $output = 0;
            array_push($form_arr, "Invalid configuration");
        } else {
            if ($result[0]['cab_approver_num'] == '') {
                $output = 0;
                array_push($form_arr, "Minimum approver for CAB");
            }
        }

        //Category
        $result_cat = $session->execute($session->prepare("SELECT id,categoryname FROM changecompanycategory WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result_cat->count() == 0) {
            $output = 0;
            array_push($form_arr, "Category Type");
        }
        //category array
        foreach ($result_cat as $row_cat) {
            array_push($cat_arr, $row_cat['categoryname']);
        }

        //biclassname
        $result_bi = $session->execute($session->prepare("SELECT id,biclassname FROM changecompanybiclass WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result_bi->count() == 0) {
            $output = 0;
            array_push($form_arr, "Business Impact Category");
        }
        //biclassname array
        foreach ($result_bi as $row_bi) {
            array_push($bi_arr, $row_bi['biclassname']);
        }

        //Priority
        $result = $session->execute($session->prepare("SELECT id FROM changecompanypriority WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result->count() == 0) {
            $output = 0;
            array_push($form_arr, "Priority");
        }

        //Sub category
        $result = $session->execute($session->prepare("SELECT id FROM changecompanysubcategory WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result->count() == 0) {
            $output = 0;
            array_push($form_arr, "Sub-Category Type");
        }

        //Cab
        $result = $session->execute($session->prepare("SELECT id FROM changecompanycab WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result->count() == 0) {
            $output = 0;
            array_push($form_arr, "CAB");
        }

        $result = $session->execute($session->prepare("SELECT id FROM changecompanyimplementationmatrix WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result->count() == 0) {
            $output = 0;
            array_push($form_arr, "Implementation Phase");
        }

        $result = $session->execute($session->prepare("SELECT id FROM changecompanyapprovalmatrix WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result->count() == 0) {
            $output = 0;
            array_push($form_arr, "Approval Matrix");
        } else {
            foreach ($cat_arr as $categoryname) {
                foreach ($bi_arr as $biclassname) {
                    $result_ap = $session->execute(
                        $session->prepare("SELECT id FROM changecompanyapprovalmatrix WHERE companycode=? AND transactionid=? AND status=? AND categoryname=? AND biclassname=? ALLOW FILTERING"),
                        array(
                            'arguments' => array(
                                $companycode,
                                $tid,
                                "1",
                                $categoryname,
                                $biclassname
                            )
                        )
                    );
                    if ($result_ap->count() == 0) {
                        $output = 0;
                        array_push($app_arr, $categoryname . " - " . $biclassname);
                    }
                }
            }
        }
        $arr_return = ["code" => 200, "success" => true, "data" => ["form" => $form_arr, "approval" => $app_arr]];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function get_default_config_change($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = [];
        $arr['priority'] = [];
        $arr['category'] = [];
        $arr['biclass'] = [];
        $arr['approvalmetrix'] = [];
        $arr['impapprovalmetrix'] = [];
        $arr['cab'] = [];
        $arr['subcategory'] = [];
        $arr['default_config'] = [];

        $result = $session->execute($session->prepare("SELECT * FROM changecompanypriority WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['priority'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM changecompanycategory WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['category'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM changecompanybiclass WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['biclass'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM changecompanyapprovalmatrix WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['approvalmetrix'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM changecompanyimplementationmatrix WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['impapprovalmetrix'][] = $row;
        }


        $result = $session->execute($session->prepare("SELECT * FROM changecompanycab WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['cab'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM changecompanysubcategory WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['subcategory'][] = $row;
        }

        //Sorting
        foreach ($arr as $key => $value) {
            $temp_price_for_ques = array_column($arr[$key], 'sorting_order');
            array_multisort($temp_price_for_ques, SORT_ASC, $arr[$key]);
        }

        $result = $session->execute($session->prepare("SELECT * FROM companyworkflowmaster WHERE cwmcompanycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['default_config'] = $row;
        }


        $arr_return = ["code" => 200, "success" => true, "data" => $arr];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function change_no_of_min_approver_cab($cab_count, $tid, $companycode, $email, $role)
{
    try {
        global $session;
        $result = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "This transaction doesn't exist. Try Again!"];
            exit();

        }
        $result_cab = $session->execute($session->prepare("SELECT id FROM changecompanycab WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        $total_cab_count = $result_cab->count();
        if ($cab_count == '' || (int) $cab_count == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Save the CAB form first and then select minimum number of approver."];
            exit();
        }
        if ((int) $cab_count > $total_cab_count) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "This option is not acceptable. Select other option."];
            exit();
        }

        $session->execute($session->prepare("UPDATE workflowconfigversions SET cab_approver_num=? WHERE wcvid=?"), array('arguments' => array($cab_count, new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => "success"];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


function change_config_final_submit($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $result = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Error Occured"];
            exit();
        }

        //Check if all data filled
        $checkalldatafilled = checkalldatafilled($tid, $companycode);
        if ($checkalldatafilled['output'] == 0) {
            $form_arr = $checkalldatafilled['form'];

            if (isset($checkalldatafilled['approval'])) {
                $approval = $checkalldatafilled['approval'];
            } else {
                $approval = null;
            }

            $temp_msg = "";
            if (count($form_arr) > 0) {
                $temp_msg .= implode(",", $form_arr) . " has not been submitted yet. ";
            }

            if (count($approval) > 0) {
                $temp_msg .= "Please fill Approval Matrix first. " . implode(",", $approval) . " combination in Approval Matrix form is not available.";
            }

            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $temp_msg];
            exit();

        }

        if ($checkalldatafilled['output'] == 2) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $checkalldatafilled['msg']];
            exit();
        }

        $result_1 = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "change")));
        foreach ($result_1 as $row_1) {
            $session->execute($session->prepare("UPDATE workflowconfigversions SET active_status=? WHERE wcvid=?"), array('arguments' => array("inactive", $row_1['wcvid'])));
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=?,screen_status=?,active_status=?,wvtype=? WHERE wcvid=?"), array('arguments' => array("0", "1", "active", "edited", new \Cassandra\Uuid($tid))));


        $arr_return = ["code" => 200, "success" => true, "data" => "success"];
        return $arr_return;


    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


// DSRR Config Function
function dsrr_config_generate_tid($companycode, $email, $role)
{
    try {
        global $session;
        $result_version = $session->execute($session->prepare("SELECT wcvversionno FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "dssr")));
        if ($result_version->count() > 0) {
            $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "progress", "dssr")));
            if ($result->count() > 0) {
                $arr_return = ["code" => 200, "success" => true, "data" => (string) $result[0]['wcvid']];
                return $arr_return;
            } else {
                $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "active", "dssr")));
                $arr_return = ["code" => 200, "success" => true, "data" => (string) $result[0]['wcvid']];
                return $arr_return;
            }
        } else {
            $version_no = (string) $result_version->count();
            $version_name = "Version" . (string) $result_version->count();
            $txn_id = new \Cassandra\Uuid();
            $query_insert = $session->prepare('INSERT INTO workflowconfigversions(wcvid,createdate,effectivedate,custemail,status,wcvcompanycode,wcvversionname,wcvversionno,wcvworkflowname,form_status,screen_status,active_status,wvtype)
          VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        $txn_id,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $email,
                        "1",
                        $companycode,
                        $version_name,
                        $version_no,
                        "dssr",
                        "0",
                        "0",
                        "progress",
                        "start"
                    )
                )
            );
            $arr_return = ["code" => 200, "success" => true, "data" => $txn_id];
            return $arr_return;
        }
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function get_default_config($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = array();

        $result = $session->execute($session->prepare("SELECT * FROM companyrightmaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['right'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM companyrulemaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['rule'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM dsrrcompanytatmaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['tat'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM companyformmaster WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $docid = explode("|", $row['docid']);
            array_shift($docid);
            $docname = explode("|", $row['docname']);
            array_shift($docname);
            $row['docid'] = $docid;
            $row['docname'] = $docname;
            $arr['form'][] = $row;
        }

        //covers-3
        $result = $session->execute($session->prepare("SELECT * FROM companyconfigmaster WHERE ccmcompanycode=? AND transactionid=? AND status=? AND ccmworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1", "dssr")));
        foreach ($result as $row) {
            $arr['normal_tree'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM dsrrcompanyidentifier WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['identifier'][] = $row;
        }


        $result = $session->execute($session->prepare("SELECT * FROM dsrrcompanypdelements WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['pdelement'][] = $row;
        }

        $result = $session->execute($session->prepare("SELECT * FROM dsrrcompanydecisionmaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['decision'][] = $row;
        }

        foreach ($arr as $key => $value) {
            $temp_price_for_ques = array_column($arr[$key], 'sorting_order');
            array_multisort($temp_price_for_ques, SORT_ASC, $arr[$key]);
        }

        $result = $session->execute($session->prepare("SELECT * FROM companyworkflowmaster WHERE cwmcompanycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $arr['default_config'] = $row;
        }

        $arr_return = ["code" => 200, "success" => true, "data" => $arr];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function dssr_config_data_save($tid, $data, $form, $companycode, $email, $role)
{
    try {
        global $session;

        switch ($form) {
            //Form-9
            case 'form-9-NA':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM dsrrcompanydecisionmaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM dsrrcompanydecisionmaster WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();
                    $query_insert = $session->prepare('INSERT INTO dsrrcompanydecisionmaster(id,companycode,createdate,effectivedate,filleremail,decisiontype,validation,yesno,status,workflowname,sorting_order,transactionid)
              VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $value['decisiontype'],
                                $value['validation'],
                                $value['yesno'],
                                "1",
                                "dssr",
                                $key,
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("8", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;
            //Form-8
            case 'form-8':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM dsrrcompanypdelements WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM dsrrcompanypdelements WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();
                    $query_insert = $session->prepare('INSERT INTO dsrrcompanypdelements(id,companycode,createdate,effectivedate,filleremail,pdassets,pdcategory,status,workflowname,sorting_order,transactionid)
              VALUES(?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $value['pdassets'],
                                $value['pdcategory'],
                                "1",
                                "dssr",
                                $key,
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("8", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;
            //Form-7
            case 'form-7':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM dsrrcompanyidentifier WHERE companycode=? 
                AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));

                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM dsrrcompanyidentifier WHERE id=?"), array('arguments' => array($row['id'])));
                }

                //Saving the data
                foreach ($data as $key => $value) {
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();
                    $query_insert = $session->prepare('INSERT INTO dsrrcompanyidentifier(id,companycode,createdate,effectivedate,filleremail,pdcountry,pdidentifiertype,status,workflowname,sorting_order,transactionid)
              VALUES(?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $value['pdcountry'],
                                $value['pdidentifiertype'],
                                "1",
                                "dssr",
                                $key,
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("7", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;
            //Form-6
            case 'form-6':
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Error Occured. Try Again."];
                break;
            //Form-5
            case 'form-5':
                $result = $session->execute($session->prepare("SELECT ccmid FROM companyconfigmaster WHERE ccmcompanycode=? AND transactionid=? AND status=? AND ccmworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1", "dssr")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM companyconfigmaster WHERE ccmid=?"), array('arguments' => array($row['ccmid'])));
                }
                foreach ($data as $key => $value) {
                    $query_insert = $session->prepare('INSERT INTO companyconfigmaster(ccmid,ccmcompanycode,ccmcustemail,ccmdept,ccmemail,ccmindexno,ccmmemberclass,
              ccmname,ccmphone,ccmrole,ccmteamcategory,ccmteamtitle,ccmworkflowname,createdate,effectivedate,status,transactionid,sorting_order
            )
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                new \Cassandra\Uuid(),
                                $companycode,
                                $email,
                                $value['ccmdept'],
                                $value['ccmcustemail'],
                                (string) $value['categoryno'],
                                "",
                                $value['ccmname'],
                                $value['ccmphone'],
                                $value['ccmrole'],
                                $value['ccmteamcategory'],
                                $value['ccmteamtitle'],
                                "dssr",
                                new \Cassandra\Timestamp(),
                                new \Cassandra\Timestamp(),
                                "1",
                                $tid,
                                $key
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("5", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;
            //Form-4
            case 'form-4':

                //Checking the data
                foreach ($data as $key_c => $value_c) {
                    $left_s = (int) $value_c['tat'];
                    $right_s = (int) $value_c['tat2'] + (int) $value_c['resolutiontat'] + (int) $value_c['escalationatat'];
                    if ($right_s > $left_s) {
                        return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Sum of Response TAT, Resolution TAT & Escalation TAT must be less than or equal to TAT as per law."];
                        exit();
                    }
                }

                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM dsrrcompanytatmaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM dsrrcompanytatmaster WHERE id=?"), array('arguments' => array($row['id'])));
                }


                //Saving the data
                foreach ($data as $key => $value) {
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();

                    if ($value['dh'] == 'd') {
                        $value['tat'] = (string) ((int) $value['tat'] * 24);
                        $value['tat2'] = (string) ((int) $value['tat2'] * 24);
                        $value['resolutiontat'] = (string) ((int) $value['resolutiontat'] * 24);
                        $value['escalationatat'] = (string) ((int) $value['escalationatat'] * 24);
                    }

                    $query_insert = $session->prepare('INSERT INTO dsrrcompanytatmaster(id,companycode,createdate,effectivedate,filleremail,noticetype,tat,status,workflowname,sorting_order,transactionid,escalationatat,tat2,resolutiontat)
              VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $value['noticetype'],
                                $value['tat'],
                                "1",
                                "dssr",
                                $key,
                                $tid,
                                $value['escalationatat'],
                                $value['tat2'],
                                $value['resolutiontat']
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("4", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;

            //Form-3
            case 'form-3':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM companyrulemaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM companyrulemaster WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                foreach ($data as $key => $value) {
                    $uuid = new \Cassandra\Uuid();
                    $timestamp = new \Cassandra\Timestamp();
                    $query_insert = $session->prepare('INSERT INTO companyrulemaster(id,companycode,createdate,effectivedate,filleremail,law,pdmap,reviewreq,right,rule,status,workflowname,sorting_order,transactionid)
              VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                    $session->execute(
                        $query_insert,
                        array(
                            'arguments' => array(
                                $uuid,
                                $companycode,
                                $timestamp,
                                $timestamp,
                                $email,
                                $value['law'],
                                $value['pdmap'],
                                $value['reviewreq'],
                                $value['right'],
                                $value['rule'],
                                "1",
                                "dssr",
                                $key,
                                $tid
                            )
                        )
                    );
                }
                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("3", new \Cassandra\Uuid($tid))));
                $arr_return = ["code" => 200, "success" => true, "data" => "success"];
                return $arr_return;
                break;

            //Form-2
            case 'form-2':
                //For previous data
                $result = $session->execute($session->prepare("SELECT id FROM companyrightmaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
                foreach ($result as $row) {
                    $session->execute($session->prepare("DELETE FROM companyrightmaster WHERE id=?"), array('arguments' => array($row['id'])));
                }
                //Saving the data
                $arr_chk = array();
                $arr_ret = array();
                foreach ($data as $key => $value) {
                    if ($value['rightlaw'] !== '') {
                        if (in_array($value['rightlaw'], $arr_chk)) {
                        } else {
                            $arr_ret_temp = array();
                            $uuid = new \Cassandra\Uuid();
                            $timestamp = new \Cassandra\Timestamp();
                            $query_insert = $session->prepare('INSERT INTO companyrightmaster(id,companycode,createdate,effectivedate,fillercustcode,filleremail,rightlaw,status,workflowname,sorting_order,transactionid)
              VALUES(?,?,?,?,?,?,?,?,?,?,?)');
                            $session->execute($query_insert, array('arguments' => array($uuid, $companycode, $timestamp, $timestamp, $_SESSION['customer_id'], $email, $value['rightlaw'], "1", "dssr", $key, $tid)));

                            //Push for validation
                            array_push($arr_chk, $value['rightlaw']);
                            //Fetch rights for each law
                            $result_right = $session->execute($session->prepare("SELECT right FROM dsrrrightmaster WHERE countrylaw=? AND status=? ALLOW FILTERING"), array('arguments' => array($value['rightlaw'], "1")));
                            foreach ($result_right as $row_right) {
                                array_push($arr_ret_temp, $row_right['right']);
                            }
                            $arr_ret[$value['rightlaw']] = $arr_ret_temp;
                        }
                        //form master

                        $uuid = new \Cassandra\Uuid();
                        $timestamp = new \Cassandra\Timestamp();
                        $query_insert = $session->prepare('INSERT INTO companyformmaster(id,companycode,createdate,effectivedate,filleremail,form,formtype,right,status,workflowname,sorting_order,transactionid,docid,docname,rightlaw)
              VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                        $session->execute(
                            $query_insert,
                            array(
                                'arguments' => array(
                                    $uuid,
                                    $companycode,
                                    $timestamp,
                                    $timestamp,
                                    $email,
                                    $value['form'],
                                    $value['formtype'],
                                    $value['right'],
                                    "1",
                                    "dssr",
                                    (int) $form_index,
                                    $tid,
                                    $docid,
                                    $docname,
                                    $value['rightlaw']
                                )
                            )
                        );
                    }
                }

                $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));
                // echo "success|" . json_encode($arr_ret);
                $arr_return = ["code" => 200, "success" => true, "data" => $arr_ret];
                return $arr_return;
                break;

        }

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function dssr_config_check_all_data_filled($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $output = 1;
        $form_arr = [];
        $cat_arr = [];
        $bi_arr = [];
        $app_arr = [];

        //Transaction validation
        $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            $output = 0;
            array_push($form_arr, "Invalid configuration");
        }

        //Country/Law exists or not
        $result_right = $session->execute($session->prepare("SELECT * FROM companyrightmaster WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result_right->count() == 0) {
            $output = 0;
            array_push($form_arr, "Country/Law");
        }
        //right array
        foreach ($result_right as $row_cat) {
            array_push($cat_arr, $row_cat['rightlaw']);
        }


        //response team
        $result_bi = $session->execute($session->prepare("SELECT ccmemail,ccmrole,ccmteamtitle,ccmphone FROM companyconfigmaster WHERE ccmcompanycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result_bi->count() == 0) {
            $output = 0;
            array_push($form_arr, "Response Team details");
        }
        //biclassname array
        foreach ($result_bi as $row_bi) {
            if ($row_bi['ccmemail'] == '' || $row_bi['ccmrole'] == '' || $row_bi['ccmphone'] == '') {
                $output = 0;
            }

        }
        //array_push($form_arr,"Response Team details");
        $arr_return = ["code" => 200, "success" => true, "data" => $form_arr];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function dssr_config_final_submit($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $result = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Error Occured. Try Again!!"];
            exit();
        }

        $result_1 = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "dssr")));
        foreach ($result_1 as $row_1) {
            $session->execute($session->prepare("UPDATE workflowconfigversions SET active_status=? WHERE wcvid=?"), array('arguments' => array("inactive", $row_1['wcvid'])));
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=?,screen_status=?,active_status=?,wvtype=? WHERE wcvid=?"), array('arguments' => array("0", "1", "active", "edited", new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => "success"];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


// Breach Config Funtions 
function breach_config_generate_tid($companycode, $email, $role)
{
    try {
        global $session;
        $result_version = $session->execute($session->prepare("SELECT wcvversionno FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "incident")));
        if ($result_version->count() > 0) {
            $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "progress", "incident")));
            if ($result->count() > 0) {
                $arr_return = ["code" => 200, "success" => true, "data" => (string) $result[0]['wcvid']];
                return $arr_return;
            } else {
                $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "active", "incident")));
                $arr_return = ["code" => 200, "success" => true, "data" => (string) $result[0]['wcvid']];
                return $arr_return;
            }
        } else {
            $version_no = (string) $result_version->count();
            $version_name = "Version" . (string) $result_version->count();
            $txn_id = new \Cassandra\Uuid();
            $query_insert = $session->prepare('INSERT INTO workflowconfigversions(wcvid,createdate,effectivedate,custemail,status,wcvcompanycode,wcvversionname,wcvversionno,wcvworkflowname,form_status,screen_status,active_status,wvtype)
          VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        $txn_id,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $email,
                        "1",
                        $companycode,
                        $version_name,
                        $version_no,
                        "incident",
                        "0",
                        "0",
                        "progress",
                        "start"
                    )
                )
            );
            $arr_return = ["code" => 200, "success" => true, "data" => $txn_id];
            return $arr_return;
        }
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function company_category_master_write($companycategorymaster_arr, $tid, $companycode, $email, $role)
{
    try {
        global $session;

        $result = $session->execute($session->prepare("SELECT ccatid FROM companycategorymaster WHERE ccatcompanycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $session->execute($session->prepare("DELETE FROM companycategorymaster WHERE ccatid=?"), array('arguments' => array($row['ccatid'])));
        }

        $count = 1;
        foreach ($companycategorymaster_arr as $key => $value) {
            $query_insert = $session->prepare('INSERT INTO companycategorymaster(ccatid,ccatcategoryname,ccatcategorynumber,ccatcompanycode,
              ccatcustemail,ccatworkflowname,createdate,effectivedate,status,transactionid,sorting_order
            )
            VALUES(?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        new \Cassandra\Uuid(),
                        $key,
                        $value,
                        $companycode,
                        $email,
                        "incident",
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        "1",
                        $tid,
                        $count
                    )
                )
            );
            $count++;
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("2", new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => "success"];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function company_tatmaster_write($companytatmaster_arr, $tid, $companycode, $email, $role)
{
    try {
        global $session;

        //validation
        foreach ($companytatmaster_arr as $value_val) {
            //tat=$value[0] esc=$value[1]
            if ($value_val[0] < $value_val[1]) {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Escalation Deadline should be less than TAT Deadline."];
                exit();
            }
        }

        $result = $session->execute($session->prepare("SELECT ctmid FROM companytatmaster WHERE ctmcompanycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $session->execute($session->prepare("DELETE FROM companytatmaster WHERE ctmid=?"), array('arguments' => array($row['ctmid'])));
        }
        $count = 1;
        foreach ($companytatmaster_arr as $key => $value) {
            $query_insert = $session->prepare('INSERT INTO companytatmaster(ctmid,createdate,ctmcategorybasis,ctmcategoryname,ctmcategorynumber,
              ctmcompanycode,ctmcustemail,ctmesc1deadline,ctmtatdeadline,ctmworkflowname,effectivedate,status,transactionid,sorting_order
            )
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        new \Cassandra\Uuid(),
                        new \Cassandra\Timestamp(),
                        "",
                        $key,
                        (string) $value[2],
                        $companycode,
                        $email,
                        $value[1],
                        $value[0],
                        "incident",
                        new \Cassandra\Timestamp(),
                        "1",
                        $tid,
                        $count
                    )
                )
            );
            $count++;
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("3", new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => "success"];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function company_iamaster_write($companyiamaster_arr, $tid, $companycode, $email, $role)
{
    try {
        global $session;

        $result = $session->execute($session->prepare("SELECT ciamid FROM companyiamaster WHERE ciamcompanycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        foreach ($result as $row) {
            $session->execute($session->prepare("DELETE FROM companyiamaster WHERE ciamid=?"), array('arguments' => array($row['ciamid'])));
        }

        foreach ($companyiamaster_arr as $key => $value_n) {
            $count = 1;
            foreach ($value_n as $value) {
                $query_insert = $session->prepare('INSERT INTO companyiamaster(ciamid,ciamcompanycode,ciamcustemail,ciamiatitle,ciamnocategory,
                ciamnocategorynumber,ciamworkflowname,ciamyescategory,ciamyescategorynumber,createdate,effectivedate,status,transactionid,sorting_order,ciamtype
              )
              VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                $session->execute(
                    $query_insert,
                    array(
                        'arguments' => array(
                            new \Cassandra\Uuid(),
                            $companycode,
                            $email,
                            $value[0],
                            $value[3],
                            $value[4],
                            "incident",
                            $value[1],
                            $value[2],
                            new \Cassandra\Timestamp(),
                            new \Cassandra\Timestamp(),
                            "1",
                            $tid,
                            $count,
                            $key
                        )
                    )
                );
                $count++;
            }
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=? WHERE wcvid=?"), array('arguments' => array("4", new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => "success"];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

// KPI Config Functions
function kpi_config_generate_tid($companycode, $email, $role)
{
    try {
        global $session;
        $createNew = false;
        $result_version = $session->execute($session->prepare("SELECT wcvversionno FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "kpi")));
        if ($result_version->count() > 0) {
            $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "progress", "kpi")));
            if ($result->count() > 0) {
                $res = (string) $result[0]['wcvid'];
                $arr_return = ["code" => 200, "success" => true, "data" => $res];
                return $arr_return;
            } else {
                $result = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "active", "kpi")));
                if ($result->count() > 0) {
                    $res = (string) $result[0]['wcvid'];
                    $arr_return = ["code" => 200, "success" => true, "data" => $res];
                    return $arr_return;
                } else {
                    $createNew = true;
                }
            }
        } else {
            $createNew = true;
        }

        if ($createNew) {
            $version_no = (string) $result_version->count();
            $version_name = "Version" . (string) $result_version->count();
            $txn_id = new \Cassandra\Uuid();
            $query_insert = $session->prepare('INSERT INTO workflowconfigversions(wcvid,createdate,effectivedate,custemail,status,wcvcompanycode,wcvversionname,wcvversionno,wcvworkflowname,form_status,screen_status,active_status,wvtype)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert,
                array(
                    'arguments' => array(
                        $txn_id,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        // $_SESSION['customer_id'],
                        $email,
                        "1",
                        $companycode,
                        $version_name,
                        $version_no,
                        "kpi",
                        "0",
                        "0",
                        "progress",
                        "start"
                    )
                )
            );

            $arr_return = ["code" => 200, "success" => true, "data" => $txn_id];
            return $arr_return;
        }

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function arrka_kpimaster_read($companycode, $email, $role)
{
    try {
        global $session;
        $arr = array();

        $result = $session->execute($session->prepare("SELECT * FROM arrkakpimaster"));
        foreach ($result as $row) {
            $arr[] = $row;
        }
        $arr_return = ["code" => 200, "success" => true, "data" => $arr];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


function cisoconfig_data_save($tid, $data_n, $companycode, $email, $role)
{
    try {
        global $session;

        $result = $session->execute($session->prepare("SELECT * FROM companykpimaster WHERE transactionid=? AND status=? AND companycode=? ALLOW FILTERING"), array('arguments' => array($tid, "1", $companycode)));
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $session->execute($session->prepare("DELETE FROM companykpimaster WHERE id=?"), array('arguments' => array($row['id'])));
            }
        }

        //validation
        foreach ($data_n as $key_val => $data_val) {
            if ($data_val['area'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Area can't be blank"];
                exit();
            }
            if ($data_val['parameter'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Parameter can't be blank"];
                exit();
            }
            if ($data_val['kpi'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "KPI details can't be blank"];
                exit();
            }
            if ($data_val['input_parameter_1'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Input Parameter 1  can't be blank"];
                exit();
            }
            if ($data_val['input_parameter_2'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Input Parameter 2  can't be blank"];
                exit();
            }
            if ($data_val['green_threshold'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Green threshold  can't be blank"];
                exit();
            }
            // if($data_val['amber_threshold'] ==''){echo "Amber threshold cant be blank"; exit();}
            if ($data_val['red_threshold'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Red threshold  can't be blank"];
                exit();
            }
            if ($data_val['target'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Target  can't be blank"];
                exit();
            }
            if ($data_val['frequency'] == '') {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Frequency  can't be blank"];
                exit();
            }
        }

        foreach ($data_n as $key => $data) {
            $new_uuid = new \Cassandra\uuid();
            $timestamp = new \Cassandra\Timestamp();
            $session->execute(
                $session->prepare("INSERT INTO companykpimaster (id,status,createdate,effectivedate,filleremail,companycode,transactionid,sorting_order,area,parameter,kpi,input_parameter_1,input_parameter_2,green_threshold,amber_threshold,red_threshold,target,frequency,role,type,kpireference)
         VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"),
                array(
                    'arguments' => array(
                        $new_uuid,
                        "1",
                        $timestamp,
                        $timestamp,
                        $email,
                        $companycode,
                        $tid,
                        $key,
                        $data['area'],
                        $data['parameter'],
                        $data['kpi'],
                        $data['input_parameter_1'],
                        $data['input_parameter_2'],
                        $data['green_threshold'],
                        $data['amber_threshold'],
                        $data['red_threshold'],
                        $data['target'],
                        $data['frequency'],
                        $role,
                        $data['type'],
                        $data['kpireference']
                    )
                )
            );
        }


        $arr_return = ["code" => 200, "success" => true, "data" => ""];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


function incident_screen_1_final_submit($tid, $companycode, $role, $email)
{
    try {
        global $session;
        $result_st = $session->execute($session->prepare("SELECT * FROM companykpimaster WHERE status=? AND companycode=? ALLOW FILTERING"), array('arguments' => array("1", $companycode)));
        if ($result_st->count() == 0) {
            echo "Please, save data first";
        }
        $result = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            echo "Error Occured. Try Again!!";
            exit();
        }

        $result_1 = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "kpi")));
        foreach ($result_1 as $row_1) {
            $session->execute($session->prepare("UPDATE workflowconfigversions SET active_status=? WHERE wcvid=?"), array('arguments' => array("inactive", $row_1['wcvid'])));
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=?,screen_status=?,active_status=?,wvtype=? WHERE wcvid=?"), array('arguments' => array("0", "1", "active", "edited", new \Cassandra\Uuid($tid))));


        $arr_return = ["code" => 200, "success" => true, "data" => "success"];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


function action_config_final_submit($tid, $companycode, $role, $email)
{
    try {
        global $session;
        $arr_return = [];
        $continue = 1;
        // //Check if all data filled
        $checkalldatafilled_action = checkalldatafilled($tid, $companycode);

        if ($checkalldatafilled_action['output'] == 0) {

            $form_arr = $checkalldatafilled_action['form'];

            $temp_msg = "";
            if (count($form_arr) > 0) {
                $temp_msg .= implode(",", $form_arr) . " has not been submitted yet. ";
                $continue = 0;
            }

        }

        if ($checkalldatafilled_action['output'] == 2) {
            $temp_msg .= $checkalldatafilled_action['msg'];
            $continue = 0;
        }

        if ($continue == 0) {
            $arr_return = ["code" => 200, "success" => true, "data" => $temp_msg];
            return $arr_return;
            exit();
        }
        $result = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvid=?"), array('arguments' => array(new \Cassandra\Uuid($tid))));
        if ($result->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Error Occured. Try Again!"];

            exit();
        }

        $result_1 = $session->execute($session->prepare("SELECT * FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? ALLOW FILTERING"), array('arguments' => array($companycode, "def_action")));
        foreach ($result_1 as $row_1) {
            $session->execute($session->prepare("UPDATE workflowconfigversions SET active_status=? WHERE wcvid=?"), array('arguments' => array("inactive", $row_1['wcvid'])));
        }
        $session->execute($session->prepare("UPDATE workflowconfigversions SET form_status=?,screen_status=?,active_status=?,wvtype=? WHERE wcvid=?"), array('arguments' => array("0", "1", "active", "edited", new \Cassandra\Uuid($tid))));

        $arr_return = ["code" => 200, "success" => true, "data" => "Success"];

        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


// Helper Function
function get_name_by_email($email)
{
    try {
        global $session;
        if (strtoupper($email) == 'AUTO') {
            return "Auto";
            exit();
        }
        if ($email == '') {
            $email = ' ';
        }
        $result_name = $session->execute($session->prepare("SELECT custfname,custlname FROM customer WHERE custemailaddress=?"), array('arguments' => array($email)));
        return $result_name[0]['custfname'] . " " . $result_name[0]['custlname'];
    } catch (\Exception $e) {
        return "";
    }
}

function checkalldatafilled($tid, $companycode)
{
    try {
        global $session;
        $output = 1;
        $form_arr = [];
        $cab_arr = [];
        $app_arr = [];
        //check validator
        $result_applaw = $session->execute($session->prepare("SELECT * FROM defineactioncompanyvalidator WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result_applaw->count() == 0) {
            $output = 0;
            array_push($form_arr, "Validator");
        }
        foreach ($result_applaw as $row_applaw) {
            array_push($cab_arr, $row_applaw['cabemail']);
        }

        //check reviwer
        $result_approver = $session->execute($session->prepare("SELECT * FROM defineactioncompanycab WHERE companycode=? AND transactionid=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result_approver->count() == 0) {
            $output = 0;
            array_push($form_arr, "Reviewer");
        }
        foreach ($result_approver as $row_approver) {
            array_push($app_arr, $row_approver['cabemail']);
        }

        return ["output" => $output, "msg" => "success", "form" => $form_arr];

    } catch (\Exception $e) {
        return ["output" => 2, "msg" => "Error Occured. Try Again!", "form" => $form_arr, "approval" => $app_arr];

    }
}



function table_crud_actions_check($data)
{
    try {
        global $session;


        // $loadTableJSON = file_get_contents('new-api/table-set.json');
        // $loadTableArray=json_decode($loadTableJSON,true);

        $arr_return = [];

        //Allowed actions on table
        $action_allowed = ["read", "insert", "update", "delete"];
        //Action validation
        if (!isset($data['action'])) {
            $arr_return = ["success" => false, "msg" => "No Action"];
            return $arr_return;
            exit();
        }
        if (!in_array($data['action'], $action_allowed)) {
            $arr_return = ["success" => false, "msg" => "Invalid Action"];
            return $arr_return;
            exit();
        }
        $table_action = $data['action'];

        //table_name validation
        if (!isset($data['table_name'])) {
            $arr_return = ["success" => false, "msg" => "No Table Name"];
            return $arr_return;
            exit();
        }
        $table_name = $data['table_name'];
        // if (isset($loadTableArray[$table_name])) { $table_name=$data['table_name']; }

        //Query create
        $query_initiate = "";
        switch ($table_action) {
            case 'read':
                $query_1 = "SELECT ";
                $result = [];

                //For column
                if (!isset($data['columns'])) {
                    $arr_return = ["success" => false, "msg" => "No Table Name"];
                    return $arr_return;
                    exit();
                }
                $column = $data['columns'];
                if (count($column) == 0) {
                    $arr_return = ["success" => false, "msg" => "No Columns"];
                    return $arr_return;
                    exit();
                }

                if (count($column) == 1) {
                    if ($column[0] == 'all') {
                        $query_1 .= "* ";
                    }
                } else {
                    $col_query = implode(",", $column);
                    $query_1 .= $col_query . " ";
                }

                $query_1 .= "FROM " . $table_name . " ";
                //For Where condition
                if (isset($data['isCondition'])) {
                    if ($data['isCondition']) {
                        $query_1 .= "WHERE ";

                        if (!isset($data['condition_columns']) || !isset($data['columns_data'])) {
                            $arr_return = ["success" => false, "msg" => "No Conditions"];
                            return $arr_return;
                            exit();
                        }

                        $conditon_column = $data['condition_columns'];
                        $conditon_column_data = $data['columns_data'];
                        if (count($conditon_column) == 0 || count($conditon_column_data) == 0) {
                            $arr_return = ["success" => false, "msg" => "No Conditions"];
                            return $arr_return;
                            exit();
                        }
                        if (count($conditon_column) != count($conditon_column_data)) {
                            $arr_return = ["success" => false, "msg" => "Conditions column & data don't match"];
                            return $arr_return;
                            exit();
                        }
                        $col_query = implode("=?, ", $conditon_column) . " =?";
                        $query_1 .= $col_query . " ";

                        //Is Allow FILTERING true
                        if (isset($data['isAllowFiltering'])) {
                            if ($data['isAllowFiltering']) {
                                $query_1 .= "ALLOW FILTERING";
                            }
                        }
                        $result = $session->execute($session->prepare($query_1), array('arguments' => $conditon_column_data));

                        $arr_value = [];
                        foreach ($result as $row) {
                            $arr_value[] = $row;
                        }
                        $arr_return = ["success" => true, "msg" => "Data fetched successfully", "data" => $arr_value];
                        return $arr_return;
                    } else {
                        $result = $session->execute($query_1);
                        $arr_value = [];
                        foreach ($result as $row) {
                            $arr_value[] = $row;
                        }
                        $arr_return = ["success" => true, "msg" => "Data fetched successfully", "data" => $arr_value];
                        return $arr_return;
                    }
                } else {
                    $result = $session->execute($query_1);
                    $arr_value = [];
                    foreach ($result as $row) {
                        $arr_value[] = $row;
                    }
                    $arr_return = ["success" => true, "msg" => "Data fetched successfully", "data" => $arr_value];
                    return $arr_return;
                }

                break;

            case 'insert':
                $query_1 = "INSERT INTO " . $table_name . " (";
                $result = [];
                //For column
                if (!isset($data['columns'])) {
                    $arr_return = ["success" => false, "msg" => "No Table Name"];
                    return $arr_return;
                    exit();
                }
                $column = $data['columns'];
                if (count($column) == 0) {
                    $arr_return = ["success" => false, "msg" => "No Columns"];
                    return $arr_return;
                    exit();
                }

                $col_query = implode(",", $column);
                $tertiary_operator = [];
                for ($i = 0; $i < count($column); $i++) {
                    array_push($tertiary_operator, "?");
                }
                $tertiary_operator_query = implode(",", $tertiary_operator);

                $query_1 .= $col_query . ") VALUES(" . $tertiary_operator_query . ")";

                $conditon_column_data = $data['columns_data'];

                if (count($column) == 0 || count($conditon_column_data) == 0) {
                    $arr_return = ["success" => false, "msg" => "No Column"];
                    return $arr_return;
                    exit();
                }
                if (count($column) != count($conditon_column_data)) {
                    $arr_return = ["success" => false, "msg" => "Column & data don't match"];
                    return $arr_return;
                    exit();
                }

                $result = $session->execute($session->prepare($query_1), array('arguments' => $conditon_column_data));

                $arr_value = ['insert' => true];
                $arr_return = ["success" => true, "msg" => "Data insert successfully", "data" => $arr_value];
                return $arr_return;
                break;


            case 'update':
                $query_1 = "UPDATE " . $table_name . " SET ";
                $result = [];

                //For column
                if (!isset($data['columns'])) {
                    $arr_return = ["success" => false, "msg" => "No Columns"];
                    return $arr_return;
                    exit();
                }
                $column = $data['columns'];
                $conditon_column_data = $data['columns_data'];
                if (count($column) == 1) {
                    $arr_return = ["success" => false, "msg" => "Should be more than 1 column."];
                    return $arr_return;
                    exit();
                }
                if (count($column) == 0 || count($conditon_column_data) == 0) {
                    $arr_return = ["success" => false, "msg" => "No Column"];
                    return $arr_return;
                    exit();
                }
                if (count($column) != count($conditon_column_data)) {
                    $arr_return = ["success" => false, "msg" => "Column & data don't match"];
                    return $arr_return;
                    exit();
                }

                // find Last column i.e primary key
                $primary_key = $column[count($column) - 1];
                unset($column[count($column) - 1]);

                $col_query = implode("=?,", $column) . "=?";
                $query_1 .= $col_query . " WHERE " . $primary_key . "=?";

                $result = $session->execute($session->prepare($query_1), array('arguments' => $conditon_column_data));

                $arr_value = [];
                foreach ($result as $row) {
                    $arr_value[] = $row;
                }
                $arr_return = ["success" => true, "msg" => "Data updated successfully", "data" => $arr_value];
                return $arr_return;
                break;

            case 'delete':
                $query_1 = "DELETE FROM " . $table_name . " WHERE ";
                $result = [];

                //For column
                if (!isset($data['columns'])) {
                    $arr_return = ["success" => false, "msg" => "No Columns"];
                    return $arr_return;
                    exit();
                }
                $column = $data['columns'];
                $conditon_column_data = $data['columns_data'];
                if (count($column) > 1) {
                    $arr_return = ["success" => false, "msg" => "Should not be more than 1 column."];
                    return $arr_return;
                    exit();
                }
                if (count($column) == 0 || count($conditon_column_data) == 0) {
                    $arr_return = ["success" => false, "msg" => "No Column"];
                    return $arr_return;
                    exit();
                }
                if (count($column) != count($conditon_column_data)) {
                    $arr_return = ["success" => false, "msg" => "Column & data don't match"];
                    return $arr_return;
                    exit();
                }

                // find Last column i.e primary key
                $primary_key = $column[0];

                $query_1 .= $primary_key . "=?";

                $result = $session->execute($session->prepare($query_1), array('arguments' => $conditon_column_data));

                $arr_value = [];
                foreach ($result as $row) {
                    $arr_value[] = $row;
                }
                $arr_return = ["success" => true, "msg" => "Data deleted successfully", "data" => $arr_value];
                return $arr_return;
                break;

            default:
                $arr_return = ["success" => false, "msg" => "Invalid Request"];
                break;
        }

    } catch (\Exception $e) {
        $err = (string) $e;
        $arr_return = ["success" => false, "msg" => $err];
        return $arr_return;
    }
}

?>