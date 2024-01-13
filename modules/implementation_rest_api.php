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
                if (isset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $_POST['overall_activity_read_for_reassign'])) {
                    $companycode = $GLOBALS['companycode'];
                    $email = $GLOBALS['email'];
                    $role = $GLOBALS['role'];
                    $actionType = $_POST['overall_activity_read_for_reassign'];

                    if ($actionType == 'ALL_ACTION') {
                        $output = overall_activity_read_for_reassign_action($companycode, $email, $role, $_POST['view_by_sel'], $_POST['custom_from_date'], $_POST['custom_to_date'], $_POST['fetch_from_db']);
                    } else {
                        $output = overall_activity_read_for_reassign($companycode, $email, $role);
                    }

                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }

            case "load_notice_data_per_txn":
                if (isset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $_POST['load_notice_data_per_txn'])) {
                    $companycode = $GLOBALS['companycode'];
                    $email = $GLOBALS['email'];
                    $role = $GLOBALS['role'];
                    $txnId = $_POST['load_notice_data_per_txn'];

                    $output = load_notice_data_per_txn($companycode, $email, $role, $txnId);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }

            case "email_by_role_for_assign":
                if (isset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $_POST['email_by_role_for_assign'], $_POST['reassign_type'], $_POST['notice_id'])) {
                    $companycode = $GLOBALS['companycode'];
                    $email = $GLOBALS['email'];
                    $role = $_POST['email_by_role_for_assign'];
                    $reassign_type = $_POST['reassign_type'];
                    $notice_id = $_POST['notice_id'];

                    $output = email_by_role_for_assign($companycode, $email, $role, $reassign_type, $notice_id);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
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
            echo json_encode($arr_diff);
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
            echo json_encode($arr_diff);
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
            echo json_encode($arr_diff);
        }


    } catch (\Exception $e) {
        errorLog($_SERVER['REMOTE_ADDR'], "ER003", "database error", "1", $e, $notice_id, $_SERVER['PHP_SELF'], $_SERVER['HTTP_REFERER'], session_id(), http_response_code(), $_SESSION['role'], $_SERVER['HTTP_USER_AGENT'], $_SESSION['email'], $_SESSION['customer_id'], $_SESSION['companycode']);
        echo "Error Occured$e";
    }
}

?>