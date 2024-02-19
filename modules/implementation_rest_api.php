<?php

function GetImplementationHandler($funcCallType)
{
    try {
        switch ($funcCallType) {

            case "get_compliance_score_for_implementation_tracker":
                if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($GLOBALS['custcode'])) {
                    $output = get_compliance_score_for_implementation_tracker($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "overall_activity_read_for_reassign":
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

                if (isset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json['overall_activity_read_for_reassign'])) {
                    $companycode = $GLOBALS['companycode'];
                    $email = $GLOBALS['email'];
                    $role = $GLOBALS['role'];
                    $actionType = $json['overall_activity_read_for_reassign'];

                    if ($actionType == 'ALL_ACTION') {
                        $output = overall_activity_read_for_reassign_action($companycode, $email, $role, $json['view_by_sel'], $json['custom_from_date'], $json['custom_to_date'], $json['fetch_from_db']);
                    } else {
                        $output = overall_activity_read_for_reassign($companycode, $email, $role);
                    }

                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                        break;
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }

            case "load_notice_data_per_txn":
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

                if (isset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json['load_notice_data_per_txn'])) {
                    $companycode = $GLOBALS['companycode'];
                    $email = $GLOBALS['email'];
                    $role = $GLOBALS['role'];
                    $txnId = $json['load_notice_data_per_txn'];

                    $output = load_notice_data_per_txn($companycode, $email, $role, $txnId);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                        break;
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }

            case "email_by_role_for_assign":
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

                if (isset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json['email_by_role_for_assign'], $json['reassign_type'], $json['notice_id'])) {
                    $companycode = $GLOBALS['companycode'];
                    $email = $GLOBALS['email'];
                    $role = $json['email_by_role_for_assign'];
                    $reassign_type = $json['reassign_type'];
                    $notice_id = $json['notice_id'];

                    $output = email_by_role_for_assign($companycode, $email, $role, $reassign_type, $notice_id);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                        break;
                    } else {
                        print_r('check');
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }

            case "reassign_activity_update_reassign":
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

                if (isset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'])) {
                    $companycode = $GLOBALS['companycode'];
                    $email = $GLOBALS['email'];
                    $role = $GLOBALS['role'];

                    $output = reassign_activity_update_reassign($companycode, $email, $role, $jsonString);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                        break;
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
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

function get_compliance_score_for_implementation_tracker($companycode, $email, $role, $custcode)
{
    try {
        global $session;
        $closed = 0;
        $total = 0;
        $result = $session->execute($session->prepare("SELECT validation_status FROM actions_data WHERE companycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
        foreach ($result as $row) {
            $total++;
            if ($row['validation_status'] == 'Closed') {
                $closed++;
            }
        }

        $comp_score = 0;
        if ($total > 0) {
            $comp_score = $closed / $total;
            $comp_score = $comp_score * 100;
            $comp_score = number_format((float) $comp_score, 0, '.', '');
        } else {
            $comp_score = 0;
        }
        $arr_return = ["code" => 200, "success" => true, "data" => $comp_score];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function overall_activity_read_for_reassign_action($companycode, $email, $role, $view_by_sel, $custom_from_date, $custom_to_date, $fetch_from_db)
{
    try {
        global $session;
        $table_name = 'notice';
        $view_by_sel = "";
        if (isset($_POST['view_by_sel'])) {
            $view_by_sel = $_POST['view_by_sel'];
        }
        $custom_from_date = "";
        if (isset($_POST['custom_from_date'])) {
            $custom_from_date = strtotime($_POST['custom_from_date']);
        }
        $custom_to_date = "";
        if (isset($_POST['custom_to_date'])) {
            $custom_to_date = strtotime($_POST['custom_to_date'] . " 11:59:59 PM");
        }
        $fetch_from_db = "current";
        if (isset($_POST['fetch_from_db'])) {
            $fetch_from_db = $_POST['fetch_from_db'];
        }

        if ($view_by_sel == 'last_updated_10' || $view_by_sel == 'all_closed') {
            $notice_alert_status = 'settled';
            if ($fetch_from_db == "archive") {
                $table_name = 'notice_archive';
            }
        } else {
            $notice_alert_status = 'urgent';
        }
        if ($view_by_sel == 'last_updated_10') {
            $index_date = 'modifydate';
        } else {
            $index_date = 'createdate';
        }

        $arr_final = [];
        $arr = [];
        $notice_no_arr = [];
        $result = $session->execute($session->prepare("SELECT notice_no,createdate,transactionid,notice_module_id,reassigntype,modifydate,notice_module_alt FROM " . $table_name . " WHERE companycode=? AND status=? AND notice_alert_status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $notice_alert_status)));
        foreach ($result as $row) {
            $reassigntype = $row['reassigntype'];
            $createdate = (string) $row[$index_date];
            $createdate_int = (int) $createdate / 1000;
            if ($createdate_int > 0) {
                if ($view_by_sel == 'custom') {
                    if ($createdate_int >= $custom_from_date && $createdate_int <= $custom_to_date) {
                        if ($reassigntype == 'individual') {
                            $notice_no_arr["notice_no*|*" . (string) $row['notice_no']] = $createdate_int;
                        } else {
                            $notice_no_arr["txnid*|*" . $row['transactionid'] . "*|*" . $row['notice_module_id']] = $createdate_int;
                        }
                    }
                } else {
                    if ($reassigntype == 'individual') {
                        $notice_no_arr["notice_no*|*" . (string) $row['notice_no']] = $createdate_int;
                    } else {
                        $notice_no_arr["txnid*|*" . $row['transactionid'] . "*|*" . $row['notice_module_id']] = $createdate_int;
                    }
                }
            }
        }

        arsort($notice_no_arr);

        switch ($view_by_sel) {
            case 'newest_10':
                $first_arr = array_slice($notice_no_arr, 0, 10, true);
                break;
            case 'oldest_10':
                $first_arr = array_slice($notice_no_arr, -10, 10, true);
                break;
            case 'last_updated_10':
                $first_arr = array_slice($notice_no_arr, -10, 10, true);
                break;
            case 'custom':
                $first_arr = $notice_no_arr;
                break;
            default:
                $first_arr = $notice_no_arr;
                break;
        }

        $arr_return = ["code" => 200, "success" => true, "data" => $first_arr];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function overall_activity_read_for_reassign($companycode, $email, $role)
{
    global $session;
    try {
        $arr = array();
        $result = $session->execute($session->prepare("SELECT * FROM notice WHERE companycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
        foreach ($result as $row) {
            if ($row['notice_alert_status'] != 'settled') {
                if ($row['notice_to_role'] == "") {
                    $row['notice_to_role'] = "";
                }
                $createdate = (string) $row['createdate'];
                $row['seconds'] = (int) $createdate;

                if ($createdate == '') {
                    $row['assign_date'] = "-";
                } else {
                    $row['assign_date'] = date("d-m-Y", (int) $createdate / 1000);
                }

                $modifydate = (string) ($row['modifydate']);
                if ($modifydate == '') {
                    $row['last_updated_date'] = "-";
                } else {
                    $row['last_updated_date'] = date("d-m-Y", (int) $modifydate / 1000);
                }

                $row['notice_no'] = (string) $row['notice_no'];

                $notice_to_name = "";
                if ($row['notice_to'] != "") {
                    $notice_to_name = get_name_from_email($row['notice_to']);
                }
                $row['notice_to_name'] = $notice_to_name;
                if ($row['notice_module_id'] == '') {
                    $row['notice_module_id'] = " ";
                }

                $result_action = $session->execute($session->prepare("SELECT reassigntype FROM notice_master WHERE notice_module_id=? ALLOW FILTERING"), array('arguments' => array($row['notice_module_id'])));
                if ($result_action->count() == 0) {
                    $result_action = $session->execute($session->prepare("SELECT reassigntype FROM notice_master WHERE notice_module_alt=? ALLOW FILTERING"), array('arguments' => array($row['notice_module_id'])));
                }
                $reassigntype = "individual";
                if ($result_action->count() > 0) {
                    $reassigntype = $result_action[0]['reassigntype'];
                }

                $row['reassigntype'] = $reassigntype;
                foreach ($row as $key => $value) {
                    if (!is_array($value)) {
                        $row[$key] = htmlspecialchars($value);
                    }
                }
                $arr[] = $row;

            }
        }

        array_multisort(array_column($arr, "seconds"), SORT_DESC, $arr);

        $arr_final = array();


        foreach ($arr as $key_n => $value_n) {
            if ($value_n['reassigntype'] == 'individual') {
                $arr_final[$value_n['notice_no']][] = $value_n;
            } else {
                $arr_final[$value_n['transactionid'] . $value_n['notice_module_id']][] = $value_n;
            }
        }

        $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function load_notice_data_per_txn($companycode, $email, $role, $txnId)
{
    try {
        global $session;
        $table_name = "notice";
        $view_by_sel = "";
        if (isset($_POST['view_by_sel'])) {
            $view_by_sel = $_POST['view_by_sel'];
        }
        $fetch_from_db = "current";
        if (isset($_POST['fetch_from_db'])) {
            $fetch_from_db = $_POST['fetch_from_db'];
        }
        if ($view_by_sel == 'last_updated_10' || $view_by_sel == 'all_closed') {
            $notice_alert_status_in = 'settled';
            if ($fetch_from_db == "archive") {
                $table_name = "notice_archive";
            }
        } else {
            $notice_alert_status_in = 'urgent';
        }
        $arr = [];
        $key_query = explode("*|*", $txnId);
        if ($key_query[0] == 'notice_no') {
            $result = $session->execute($session->prepare("SELECT * FROM " . $table_name . " WHERE notice_no=?"), array('arguments' => array(new \Cassandra\Uuid($key_query[1]))));
        } else {
            $result = $session->execute($session->prepare("SELECT * FROM " . $table_name . " WHERE status=? AND notice_alert_status=? AND transactionid=? AND notice_module_id=? ALLOW FILTERING"), array('arguments' => array("1", $notice_alert_status_in, $key_query[1], $key_query[2])));
        }
        $row = $result[0];

        $arr_details = [];
        $arr_submit_action = [];
        $notice_alert_status = "Closed";
        if ($row['notice_alert_status'] == 'urgent') {
            $notice_alert_status = "Open";
        }
        if ($row['notice_module'] == 'action') {
            //For action
            if ($row['notice_module_alt'] == 'submit_action') {
                $result_actions = $session->execute($session->prepare("SELECT transactionid,action FROM actions_data WHERE transactionid=? AND status=? AND owner=? AND owner_role=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], "1", $row['notice_to'], $row['notice_to_role'])));
                foreach ($result_actions as $row_actions) {
                    $modulename_arr = get_transaction_details_from_tid($row_actions['transactionid']);
                    $arr_details[] = ["notice_details" => $row_actions['action'], "action_status" => $notice_alert_status, "module" => $modulename_arr['modulename']];
                }
            } else {
                $result_actions = $session->execute($session->prepare("SELECT * FROM actions_data WHERE transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], "1")));
                foreach ($result_actions as $row_actions) {
                    $modulename_arr = get_transaction_details_from_tid($row_actions['transactionid']);
                    $arr_details[] = ["notice_details" => $row_actions['action'], "action_status" => $notice_alert_status, "module" => $modulename_arr['modulename']];
                }
            }
        } else {
            $arr_details[] = ["notice_details" => $row['notice_details'], "action_status" => $notice_alert_status, "module" => $row['notice_module_desc']];
        }

        $createdate = (string) $row['createdate'];
        if ($createdate == '') {
            $assign_date = "-";
        } else {
            $assign_date = date("d-m-Y", (int) $createdate / 1000);
        }
        $modifydate = (string) ($row['modifydate']);
        if ($modifydate == '') {
            $last_updated_date = "-";
        } else {
            $last_updated_date = date("d-m-Y", (int) $modifydate / 1000);
        }

        foreach ($result as $row_nc) {
            if ($row_nc['notice_to_role'] == "") {
                $row_nc['notice_to_role'] = "";
            }
            $row_nc['notice_no'] = (string) $row_nc['notice_no'];
            $notice_to_name = "";
            if ($row_nc['notice_to'] != "") {
                $notice_to_name = get_name_from_email($row_nc['notice_to']);
            }
            $row_nc['notice_to_name'] = $notice_to_name;
            $arr[] = $row_nc;
        }

        $arr_final = [];
        if (count($arr_details) > 0) {
            $arr_final = [
                'notice_details' => $arr_details,
                'assign_date' => $assign_date,
                'last_updated_date' => $last_updated_date,
                'rest_data' => $arr,
                'txnId' => $txnId
            ];
        }

        $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function email_by_role_for_assign($companycode, $email, $role, $reassign_type, $notice_id)
{
    try {
        global $session;
        if ($role == "") {
            echo json_encode([]);
            exit();
        }
        $arr = [];

        if ($role == 'all_email') {
            $result_role = $session->execute($session->prepare("SELECT rtccustemail FROM roletocustomer WHERE companycode=? AND rolestatus=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
        } else {
            $result_role = $session->execute($session->prepare("SELECT rtccustemail FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtcrole=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $role)));
        }
        foreach ($result_role as $row) {
            array_push($arr, $row['rtccustemail']);
        }
        sort($arr);

        $email_arr = array_unique($arr);
        foreach ($email_arr as $key => $value) {
            $result_er = $session->execute($session->prepare("SELECT custuserpasswd FROM customer WHERE custemailaddress=?"), array('arguments' => array($value)));
            if ($result_er[0]['custuserpasswd'] == '') {
                unset($email_arr[$key]);
            }
        }

        //find notice Details
        $result = $session->execute($session->prepare("SELECT transactionid,notice_to,notice_to_role,notice_module_id FROM notice WHERE notice_no=?"), array('arguments' => array(new \Cassandra\Uuid($notice_id))));
        $transactionid = $result[0]['transactionid'];
        $notice_module_id = $result[0]['notice_module_id'];
        $pre_email = $result[0]['notice_to'];
        $pre_role = $result[0]['notice_to_role'];
        $email_set_from_notice = [];

        if ($pre_email == "") {
            echo json_encode($email_arr);
            exit();
        }

        if ($reassign_type == 'reassign_specific') {
            $result_nxt = $session->execute($session->prepare("SELECT notice_to FROM notice WHERE transactionid=? AND notice_module_id=? AND companycode=? AND status=? AND notice_to_role=? ALLOW FILTERING"), array('arguments' => array($transactionid, $notice_module_id, $companycode, "1", $role)));
            foreach ($result_nxt as $row_nxt) {
                array_push($email_set_from_notice, $row_nxt['notice_to']);
            }
            $arr_diff = array_diff($email_arr, $email_set_from_notice);
            // echo json_encode($arr_diff);
            $arr_return = ["code" => 200, "success" => true, "msg" => "Success", "data" => $arr_diff];
            return $arr_return;
        } elseif ($reassign_type == 'reassign_all_role') {

            //No specific notice txn
            //Find out all transaction related to this user $pre_email & $pre_role
            $arr_ntid = [];
            $result_ntid = $session->execute($session->prepare("SELECT transactionid,notice_module_id FROM notice WHERE companycode=? AND status=? AND notice_to_role=? AND notice_to=? AND notice_alert_status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $pre_role, $pre_email, "urgent")));
            foreach ($result_ntid as $row_ntid) {
                $arr_ntid[$row_ntid['transactionid'] . "-|-" . $row_ntid['notice_module_id']] = "ABC";
            }

            foreach ($arr_ntid as $key_ntid => $value_ntid) {
                $tid_arr = explode("-|-", $key_ntid);
                $result_nxt = $session->execute($session->prepare("SELECT notice_to FROM notice WHERE transactionid=? AND notice_module_id=? AND companycode=? AND status=? AND notice_to_role=? ALLOW FILTERING"), array('arguments' => array($tid_arr[0], $tid_arr[1], $companycode, "1", $role)));
                foreach ($result_nxt as $row_nxt) {
                    array_push($email_set_from_notice, $row_nxt['notice_to']);
                }
            }

            $arr_diff = array_diff($email_arr, $email_set_from_notice);
            // echo json_encode($arr_diff);
            $arr_return = ["code" => 200, "success" => true, "msg" => "Success", "data" => $arr_diff];
            return $arr_return;
        } else {
            //No specific notice txn
            //Find out all transaction related to this user $pre_email & $pre_role
            $arr_ntid = [];
            $result_ntid = $session->execute($session->prepare("SELECT transactionid,notice_module_id,notice_to_role FROM notice WHERE companycode=? AND status=? AND notice_to=? AND notice_alert_status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $pre_email, "urgent")));
            foreach ($result_ntid as $row_ntid) {
                $arr_ntid[$row_ntid['transactionid'] . "-|-" . $row_ntid['notice_module_id'] . "-|-" . $row_ntid['notice_to_role']] = "ABC";
            }

            foreach ($arr_ntid as $key_ntid => $value_ntid) {
                $tid_arr = explode("-|-", $key_ntid);
                $result_nxt = $session->execute($session->prepare("SELECT notice_to FROM notice WHERE transactionid=? AND notice_module_id=? AND companycode=? AND status=? AND notice_to_role=? ALLOW FILTERING"), array('arguments' => array($tid_arr[0], $tid_arr[1], $companycode, "1", $tid_arr[2])));
                foreach ($result_nxt as $row_nxt) {
                    array_push($email_set_from_notice, $row_nxt['notice_to']);
                }
            }

            $arr_diff = array_diff($email_arr, $email_set_from_notice);
            // echo json_encode($arr_diff);
            $arr_return = ["code" => 200, "success" => true, "msg" => "Success", "data" => $arr_diff];
            return $arr_return;
        }

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function reassign_activity_update_reassign($companycode, $email, $role, $data_pre)
{
    try {
        global $session;
        $data = json_decode($data_pre, true);
        $data = $data['reassign_activity_update_reassign'];
        $email = $data['email'];
        $role = $data['role'];
        $assign_all = $data['assign_all'];
        $id = $data['data']['id'];
        $pre_email = $data['data']['pre_email'];
        $pre_role = $data['data']['pre_role'];

        if ($email == '' || $role == '' || $id == '' || $assign_all == '') {
            echo "Invalid Activity";
            exit();
        }

        if ($assign_all == 'reassign_all') {
            $reassign_activity_update_reassign_all = reassign_activity_update_reassign_all($companycode, $email, $role, $pre_email, $pre_role);
            echo $reassign_activity_update_reassign_all;
            exit();
        } elseif ($assign_all == 'reassign_all_role') {
            $reassign_activity_update_reassign_all_by_role = reassign_activity_update_reassign_all_by_role($companycode, $email, $role, $pre_email, $pre_role);
            echo $reassign_activity_update_reassign_all_by_role;
            exit();
        } else {

            $result = $session->execute($session->prepare("SELECT * FROM notice WHERE notice_no=? ALLOW FILTERING"), array('arguments' => array(new \Cassandra\Uuid($id))));
            if ($result->count() == 0) {
                echo "Invalid Activity. Try Again!";
                exit();
            }

            $row = $result[0];
            $validate_notice = validate_notice_eligibility_for_reassign($row['transactionid'], $row['notice_module_id'], $pre_email, $pre_role, $email, $role, $companycode);
            if ($validate_notice == 0) {
                echo "This transaction is already assigned to this user. Please select another user.";
                exit();
            }

            //Insert new notice with new email
            $uuid_new = new \Cassandra\Uuid();
            $reassigntype = get_reassigntype($row['notice_module_id']);
            $query_insert_in_company = $session->prepare('INSERT INTO notice(
       notice_no,
       companycode,
       createdate,
       effectivedate,
       eventid,
       notice_alert_status,
       notice_details,
       notice_expiry,
       notice_from,
       notice_from_role,
       notice_link,
       notice_logid,
       notice_status,
       notice_module,
       notice_module_alt,
       notice_timestamp,
       notice_to,
       notice_to_role,
       notice_type,
       status,
       transactionid,
       notice_law,
       notice_module_id,
       notice_module_desc,
       mail_status,
       reassigntype
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert_in_company,
                array(
                    'arguments' => array(
                        $uuid_new,
                        $companycode,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $row['eventid'],
                        $row['notice_alert_status'],
                        $row['notice_details'],
                        $row['notice_expiry'],
                        $email,
                        $role,
                        $row['notice_link'],
                        $row['notice_logid'],
                        $row['notice_status'],
                        $row['notice_module'],
                        $row['notice_module_alt'],
                        $row['notice_timestamp'],
                        $email,
                        $role,
                        $row['notice_type'],
                        "1",
                        $row['transactionid'],
                        $row['notice_law'],
                        $row['notice_module_id'],
                        $row['notice_module_desc'],
                        "0",
                        $reassigntype
                    )
                )
            );
            //Update notice
            $session->execute($session->prepare("UPDATE notice SET status=?,notice_alert_status=?,modifydate=? WHERE notice_no=?"), array('arguments' => array("reassigned", "settled", new \Cassandra\Timestamp(), new \Cassandra\Uuid($id))));

            //Update Transactions tables for individual type
            switch ($row['notice_module_id']) {
                case 'MA01':
                case 'IA01':
                case 'VA01':
                    // MA Submit
                    $result_txn = $session->execute($session->prepare("SELECT id FROM email_role_map_for_assessment WHERE transactionid=? AND email=? AND role=? AND status=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], $pre_email, $pre_role, "1")));
                    if ($result_txn->count() > 0) {
                        $session->execute($session->prepare("UPDATE email_role_map_for_assessment SET email=?,role=? WHERE id=?"), array('arguments' => array($email, $role, $result_txn[0]['id'])));
                    }
                    $result_txn_as = $session->execute($session->prepare("SELECT testid FROM assessmentstatus WHERE transactionid=? AND custemail=? AND role=? ALLOW FILTERING"), array('arguments' => array(new \Cassandra\Uuid($row['transactionid']), $pre_email, $pre_role)));
                    foreach ($result_txn_as as $row_ttds) {
                        $session->execute($session->prepare("DELETE FROM assessmentstatus WHERE testid=?"), array('arguments' => array($row_ttds['testid'])));
                    }
                    break;

                case 'IT02':
                    // MA Submit
                    $result_txn = $session->execute($session->prepare("SELECT companycode,status,refid,transactionid,createdate,id FROM actions_data WHERE transactionid=? AND owner=? AND owner_role=? AND status=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], $pre_email, $pre_role, "1")));
                    foreach ($result_txn as $row_txn) {
                        $session->execute(
                            $session->prepare("UPDATE actions_data SET owner=?,owner_role=?,modifydate=? WHERE companycode=? AND status=? AND refid=? AND transactionid=? AND createdate=? AND id=?"),
                            array(
                                'arguments' => array(
                                    $email,
                                    $role,
                                    new \Cassandra\Timestamp(),
                                    $row_txn['companycode'],
                                    $row_txn['status'],
                                    $row_txn['refid'],
                                    $row_txn['transactionid'],
                                    $row_txn['createdate'],
                                    $row_txn['id']
                                )
                            )
                        );
                    }
                    break;


                default:
                    // code...
                    break;
            }

            // echo "success|" . (string) $uuid_new . "|" . get_name_from_email($email);

            return ["code" => 200, "success" => true, "message" => 'Success'];
        }
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function reassign_activity_update_reassign_all($companycode, $email, $role, $pre_email, $pre_role)
{
    try {
        global $session;
        $arr = [];

        if ($email == '' || $role == '' || $pre_email == '' || $pre_role == '') {
            return "Invalid Activity";
            exit();
        }

        $result_alt = $session->execute(
            $session->prepare("SELECT transactionid,notice_module_id,notice_to_role FROM notice WHERE notice_to=? AND companycode=? AND notice_alert_status=? AND status=? ALLOW FILTERING"),
            array(
                'arguments' => array(
                    $pre_email,
                    $companycode,
                    'urgent',
                    '1'
                )
            )
        );

        $arr_tid = [];
        foreach ($result_alt as $row_alt) {
            $arr_tid[$row_alt['transactionid'] . "-|-" . $row_alt['notice_module_id']] = $row_alt['notice_to_role'];
        }

        foreach ($arr_tid as $key_tid => $value_tid) {
            $txnid_arr = explode("-|-", $key_tid);
            $validate_notice = validate_notice_eligibility_for_reassign($txnid_arr[0], $txnid_arr[1], $pre_email, $pre_role, $email, $role, $companycode);
            if ($validate_notice == 0) {
                echo "Transaction for this role is already assigned to this user. Please select another user.";
                exit();
            }
        }

        //check if all role is Available
        $role_req = [];
        $result_role = $session->execute(
            $session->prepare("SELECT notice_to_role FROM notice WHERE notice_to=? AND companycode=? AND notice_alert_status=? AND status=? ALLOW FILTERING"),
            array(
                'arguments' => array(
                    $pre_email,
                    $companycode,
                    'urgent',
                    '1'
                )
            )
        );

        foreach ($result_role as $row_role) {
            array_push($role_req, $row_role['notice_to_role']);
        }

        $role_user_has = [];
        $result_user_role = $session->execute(
            $session->prepare("SELECT rtcrole FROM roletocustomer WHERE rtccustemail=? AND companycode=? AND rolestatus=? ALLOW FILTERING"),
            array(
                'arguments' => array(
                    $email,
                    $companycode,
                    '1'
                )
            )
        );
        foreach ($result_user_role as $row_user_role) {
            array_push($role_user_has, $row_user_role['rtcrole']);
        }

        $arr_diff = array_diff($role_req, $role_user_has);


        if (count($arr_diff) > 0) {
            sort($arr_diff);
            return implode(", ", array_unique($arr_diff)) . " role/s are not available for this user to assign all actions_data.";
            exit();
        }

        //Final assignment
        $result = $session->execute(
            $session->prepare("SELECT * FROM notice WHERE notice_to=? AND companycode=? AND notice_alert_status=? AND status=? ALLOW FILTERING"),
            array(
                'arguments' => array(
                    $pre_email,
                    $companycode,
                    'urgent',
                    '1'
                )
            )
        );


        foreach ($result as $row) {
            $reassigntype = get_reassigntype($row['notice_module_id']);
            //Insert new notice with new email
            $uuid_new = new \Cassandra\Uuid();
            $query_insert_in_company = $session->prepare('INSERT INTO notice(
         notice_no,
         companycode,
         createdate,
         effectivedate,
         eventid,
         notice_alert_status,
         notice_details,
         notice_expiry,
         notice_from,
         notice_from_role,
         notice_link,
         notice_logid,
         notice_status,
         notice_module,
         notice_module_alt,
         notice_timestamp,
         notice_to,
         notice_to_role,
         notice_type,
         status,
         transactionid,
         notice_law,
         notice_module_id,
         notice_module_desc,
         mail_status,
         reassigntype
      )
      VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert_in_company,
                array(
                    'arguments' => array(
                        $uuid_new,
                        $companycode,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $row['eventid'],
                        $row['notice_alert_status'],
                        $row['notice_details'],
                        $row['notice_expiry'],
                        $email,
                        $role,
                        $row['notice_link'],
                        $row['notice_logid'],
                        $row['notice_status'],
                        $row['notice_module'],
                        $row['notice_module_alt'],
                        $row['notice_timestamp'],
                        $email,
                        $row['notice_to_role'],
                        $row['notice_type'],
                        "1",
                        $row['transactionid'],
                        $row['notice_law'],
                        $row['notice_module_id'],
                        $row['notice_module_desc'],
                        "0",
                        $reassigntype
                    )
                )
            );
            //Update notice
            $session->execute($session->prepare("UPDATE notice SET status=?,notice_alert_status=?,modifydate=? WHERE notice_no=?"), array('arguments' => array("reassigned", "settled", new \Cassandra\Timestamp(), $row['notice_no'])));

            //Update Transactions tables for individual type
            switch ($row['notice_module_id']) {
                case 'MA01':
                case 'IA01':
                case 'VA01':
                    // MA Submit
                    $result_txn = $session->execute($session->prepare("SELECT id FROM email_role_map_for_assessment WHERE transactionid=? AND email=? AND role=? AND status=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], $pre_email, $pre_role, "1")));
                    if ($result_txn->count() > 0) {
                        $session->execute($session->prepare("UPDATE email_role_map_for_assessment SET email=?,role=? WHERE id=?"), array('arguments' => array($email, $role, $result_txn[0]['id'])));
                    }
                    break;

                case 'IT02':
                    // MA Submit
                    $result_txn = $session->execute($session->prepare("SELECT companycode,status,refid,transactionid,createdate,id FROM actions_data WHERE transactionid=? AND owner=? AND owner_role=? AND status=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], $pre_email, $pre_role, "1")));
                    foreach ($result_txn as $row_txn) {
                        $session->execute(
                            $session->prepare("UPDATE actions_data SET owner=?,owner_role=?,modifydate=? WHERE companycode=? AND status=? AND refid=? AND transactionid=? AND createdate=? AND id=?"),
                            array(
                                'arguments' => array(
                                    $email,
                                    $role,
                                    new \Cassandra\Timestamp(),
                                    $row_txn['companycode'],
                                    $row_txn['status'],
                                    $row_txn['refid'],
                                    $row_txn['transactionid'],
                                    $row_txn['createdate'],
                                    $row_txn['id']
                                )
                            )
                        );
                    }
                    break;
            }
        }

        return "success|reload";

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function reassign_activity_update_reassign_all_by_role($companycode, $email, $role, $pre_email, $pre_role)
{
    try {
        global $session;
        $arr = [];

        if ($email == '' || $role == '' || $pre_email == '' || $pre_role == '') {
            return "Invalid Activity";
            exit();
        }

        $result_alt = $session->execute(
            $session->prepare("SELECT transactionid,notice_module_id FROM notice WHERE notice_to=? AND notice_to_role=? AND companycode=? AND notice_alert_status=? AND status=? ALLOW FILTERING"),
            array(
                'arguments' => array(
                    $pre_email,
                    $pre_role,
                    $companycode,
                    'urgent',
                    '1'
                )
            )
        );

        $arr_tid = [];
        foreach ($result_alt as $row_alt) {
            $arr_tid[$row_alt['transactionid'] . "-|-" . $row_alt['notice_module_id']] = "ABC";
        }

        foreach ($arr_tid as $key_tid => $value_tid) {
            $txnid_arr = explode("-|-", $key_tid);
            $validate_notice = validate_notice_eligibility_for_reassign($txnid_arr[0], $txnid_arr[1], $pre_email, $pre_role, $email, $role, $companycode);
            if ($validate_notice == 0) {
                echo "Transaction for this role is already assigned to this user. Please select another user.";
                exit();
            }
        }

        $result = $session->execute(
            $session->prepare("SELECT * FROM notice WHERE notice_to=? AND notice_to_role=? AND companycode=? AND notice_alert_status=? AND status=? ALLOW FILTERING"),
            array(
                'arguments' => array(
                    $pre_email,
                    $pre_role,
                    $companycode,
                    'urgent',
                    '1'
                )
            )
        );
        foreach ($result as $row) {
            $reassigntype = get_reassigntype($row['notice_module_id']);
            //Insert new notice with new email
            $uuid_new = new \Cassandra\Uuid();
            $query_insert_in_company = $session->prepare('INSERT INTO notice(
         notice_no,
         companycode,
         createdate,
         effectivedate,
         eventid,
         notice_alert_status,
         notice_details,
         notice_expiry,
         notice_from,
         notice_from_role,
         notice_link,
         notice_logid,
         notice_status,
         notice_module,
         notice_module_alt,
         notice_timestamp,
         notice_to,
         notice_to_role,
         notice_type,
         status,
         transactionid,
         notice_law,
         notice_module_id,
         notice_module_desc,
         mail_status,
         reassigntype
      )
      VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute(
                $query_insert_in_company,
                array(
                    'arguments' => array(
                        $uuid_new,
                        $companycode,
                        new \Cassandra\Timestamp(),
                        new \Cassandra\Timestamp(),
                        $row['eventid'],
                        $row['notice_alert_status'],
                        $row['notice_details'],
                        $row['notice_expiry'],
                        $email,
                        $role,
                        $row['notice_link'],
                        $row['notice_logid'],
                        $row['notice_status'],
                        $row['notice_module'],
                        $row['notice_module_alt'],
                        $row['notice_timestamp'],
                        $email,
                        $role,
                        $row['notice_type'],
                        "1",
                        $row['transactionid'],
                        $row['notice_law'],
                        $row['notice_module_id'],
                        $row['notice_module_desc'],
                        "0",
                        $reassigntype
                    )
                )
            );
            //Update notice
            $session->execute($session->prepare("UPDATE notice SET status=?,notice_alert_status=?,modifydate=? WHERE notice_no=?"), array('arguments' => array("reassigned", "settled", new \Cassandra\Timestamp(), $row['notice_no'])));

            //Update Transactions tables for individual type
            switch ($row['notice_module_id']) {
                case 'MA01':
                case 'IA01':
                case 'VA01':
                    // MA Submit
                    $result_txn = $session->execute($session->prepare("SELECT id FROM email_role_map_for_assessment WHERE transactionid=? AND email=? AND role=? AND status=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], $pre_email, $pre_role, "1")));
                    if ($result_txn->count() > 0) {
                        $session->execute($session->prepare("UPDATE email_role_map_for_assessment SET email=?,role=? WHERE id=?"), array('arguments' => array($email, $role, $result_txn[0]['id'])));
                    }
                    break;

                case 'IT02':
                    // MA Submit
                    $result_txn = $session->execute($session->prepare("SELECT companycode,status,refid,transactionid,createdate,id FROM actions_data WHERE transactionid=? AND owner=? AND owner_role=? AND status=? ALLOW FILTERING"), array('arguments' => array($row['transactionid'], $pre_email, $pre_role, "1")));
                    foreach ($result_txn as $row_txn) {
                        $session->execute(
                            $session->prepare("UPDATE actions_data SET owner=?,owner_role=?,modifydate=? WHERE companycode=? AND status=? AND refid=? AND transactionid=? AND createdate=? AND id=?"),
                            array(
                                'arguments' => array(
                                    $email,
                                    $role,
                                    new \Cassandra\Timestamp(),
                                    $row_txn['companycode'],
                                    $row_txn['status'],
                                    $row_txn['refid'],
                                    $row_txn['transactionid'],
                                    $row_txn['createdate'],
                                    $row_txn['id']
                                )
                            )
                        );
                    }
                    break;
            }
        }

        return "success|reload";

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


function validate_notice_eligibility_for_reassign($transactionid, $notice_module_id, $pre_email, $pre_role, $email, $role, $companycode)
{
    try {
        global $session;
        $arr_exist = [];

        if ($transactionid == '' || $notice_module_id == '') {
            return 2;
            exit();
        }
        $result = $session->execute($session->prepare("SELECT notice_to,notice_to_role FROM notice WHERE transactionid=? AND notice_module_id=? AND companycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($transactionid, $notice_module_id, $companycode, "1")));
        foreach ($result as $row) {
            if ($email == $row['notice_to'] && $role == $row['notice_to_role']) {
                return 0;
                exit();
            }
        }
        return 1;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function get_reassigntype($notice_module_id)
{
    try {
        global $session;
        if ($notice_module_id == '') {
            return "individual";
            exit();
        }
        $result_action = $session->execute($session->prepare("SELECT reassigntype FROM notice_master WHERE notice_module_id=? ALLOW FILTERING"), array('arguments' => array($notice_module_id)));
        if ($result_action->count() == 0) {
            $result_action = $session->execute($session->prepare("SELECT reassigntype FROM notice_master WHERE notice_module_alt=? ALLOW FILTERING"), array('arguments' => array($notice_module_id)));
        }
        $reassigntype = "individual";
        if ($result_action->count() > 0) {
            $reassigntype = $result_action[0]['reassigntype'];
        }
        return $reassigntype;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


?>