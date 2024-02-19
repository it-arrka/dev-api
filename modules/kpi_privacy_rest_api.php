<?php

function GetKpiPrivacyHandler($funcCallType)
{
    try {
        switch ($funcCallType) {
            case "get-area-for-kpi":
                if (isset($_GET['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_area_for_kpi($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;
            case "get-selected-area-activity-value":
                if (isset($_POST['txn_id']) && isset($_POST['value']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_selected_area_activity_value($_POST['txn_id'], $_POST['value'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "modify-kpi":
                if (isset($_GET['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = modify_kpi($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "email-by-role":
                if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = email_by_role($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "companykpimaster-modify":

                if (isset($_POST['txn_id']) && isset($_POST['id']) && isset($_POST['area']) && isset($_POST['parameter']) && isset($_POST['kpi']) && isset($_POST['input_parameter_1']) && isset($_POST['input_parameter_2']) && isset($_POST['input_parameter_1_value']) && isset($_POST['input_parameter_2_value']) && isset($_POST['year']) && isset($_POST['month']) && isset($_POST['session_kpi']) && isset($_POST['kpiref']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = companykpimaster_modify($_POST['txn_id'], $_POST['id'], $_POST['area'], $_POST['parameter'], $_POST['kpi'], $_POST['input_parameter_1'], $_POST['input_parameter_2'], $_POST['input_parameter_1_value'], $_POST['input_parameter_2_value'], $_POST['year'], $_POST['month'], $_POST['kpiref'], $_POST['session_kpi'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);

                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "get-activity-list-on-page-load":
                if (isset($_GET['txn_id']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_activity_list_on_page_load($_GET['txn_id'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "submit-kpi-schedule-data":
                if (isset($_GET['tid']) && isset($_GET['wid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = submit_kpi_schedule_data($_GET['tid'], $_GET['wid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "approve-accept-reject":
                if (isset($_POST['id']) && isset($_POST['lawid']) && isset($_POST['notebookid']) && isset($_POST['transactionid']) && isset($_POST['type']) && isset($_POST['january']) && isset($_POST['february']) && isset($_POST['march']) && isset($_POST['april']) && isset($_POST['may']) && isset($_POST['june']) && isset($_POST['july']) && isset($_POST['august']) && isset($_POST['september']) && isset($_POST['october']) && isset($_POST['november']) && isset($_POST['december']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

                    $output = approve_accept_reject(
                        $_POST['id'],
                        $_POST['lawid'],
                        $_POST['notebookid'],
                        $_POST['transactionid'],
                        $_POST['type'],
                        $_POST['january'],
                        $_POST['february'],
                        $_POST['march'],
                        $_POST['april'],
                        $_POST['may'],
                        $_POST['june'],
                        $_POST['july'],
                        $_POST['august'],
                        $_POST['september'],
                        $_POST['october'],
                        $_POST['november'],
                        $_POST['december'],
                        $GLOBALS['companycode'],
                        $GLOBALS['email'],
                        $GLOBALS['role']
                    );
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "show-define-report-all":
                if (isset($_GET['page_index']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = show_define_report_all($_GET['page_index'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;

            case "get-comp-score-by-module":
                if (isset($_GET['option_for_comp_score']) && isset($_GET['comp_score_module']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_comp_score_by_module($_GET['option_for_comp_score'], $_GET['comp_score_module'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
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

function get_area_for_kpi($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = [];
        if ($tid == '') {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Transaction Id"];
            exit();
        }
        $result = $session->execute($session->prepare("SELECT area,parameter FROM companykpimaster WHERE companycode=? and transactionid= ? AND  status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
        if ($result->count() == 0) {
            $arr_return = ["success" => false, "msg" => "NO Data Available", "data" => ''];
            return $arr_return;
        } else {
            foreach ($result as $row) {
                $arr[] = $row['area'];
                $arr[] = $row['parameter'];
            }

            $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $arr];
            return $arr_return;
        }
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function get_selected_area_activity_value($txn_id, $value, $companycode, $email, $role)
{
    try {

        global $session;
        $arr = [];
        $arr_existing = [];
        //Email
        $result_role_val = $session->execute($session->prepare("SELECT * FROM companykpimaster WHERE companycode=? AND transactionid=? AND area =? ALLOW FILTERING"), array('arguments' => array($companycode, $txn_id, $value)));
        if ($result_role_val->count() == 0) {
            $result_role_area = $session->execute($session->prepare("SELECT * FROM companykpimaster WHERE companycode=? AND transactionid=? AND parameter =? ALLOW FILTERING"), array('arguments' => array($companycode, $txn_id, $value)));
            foreach ($result_role_area as $row_val) {
                $row_val['id'] = (string) $row_val['id'];
                $arr[] = $row_val;
            }
            $result_role_areaval = $session->execute($session->prepare("SELECT year FROM defaulteditkpi WHERE companycode=? AND area=? ALLOW FILTERING"), array('arguments' => array($companycode, $value)));
            if ($result_role_areaval->count() == 0) {
                $result_role_areaval = $session->execute($session->prepare("SELECT year FROM defaulteditkpi WHERE companycode=? AND parameter=? ALLOW FILTERING"), array('arguments' => array($companycode, $value)));
                if ($result_role_areaval->count() == 0) {

                } else {
                    foreach ($result_role_areaval as $row_val) {
                        $year = $row_val;
                        array_push($arr_existing, $year);
                    }
                }
            } else {
                foreach ($result_role_areaval as $row_val) {
                    $year = $row_val;
                    array_push($arr_existing, $year);
                }
            }

            $arr_return = ["code" => 200, "success" => true, "message" => "activity", "data" => array_unique($arr), "arr_existing" => $arr_existing];
            return $arr_return;

        } else {
            foreach ($result_role_val as $row_val) {
                $row_val['id'] = (string) $row_val['id'];
                $arr[] = $row_val;
            }
            $result_role_areaval = $session->execute($session->prepare("SELECT year FROM defaulteditkpi WHERE companycode=? AND area=? ALLOW FILTERING"), array('arguments' => array($companycode, $value)));
            if ($result_role_areaval->count() == 0) {
                $result_role_areaval = $session->execute($session->prepare("SELECT year FROM defaulteditkpi WHERE companycode=? AND parameter=? ALLOW FILTERING"), array('arguments' => array($companycode, $value)));
                if ($result_role_areaval->count() == 0) {

                } else {
                    foreach ($result_role_areaval as $row_val) {
                        $year = $row_val;
                        array_push($arr_existing, $year);
                    }
                }
            } else {
                foreach ($result_role_areaval as $row_val) {
                    $year = $row_val;
                    array_push($arr_existing, $year);
                }
            }
            $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => $arr, "arr_existing" => $arr_existing];
            return $arr_return;
        }
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


function modify_kpi($tid, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = array();
        $arr_assigned = array();
        //$result= $session->execute($session->prepare("SELECT * FROM companykpimaster"));
        $result = $session->execute($session->prepare("SELECT * FROM companykpimaster where id =? "), array('arguments' => array(new \Cassandra\Uuid($tid))));

        foreach ($result as $row) {
            $row['id'] = (string) $row['id'];
            $arr[] = $row;
        }
        $res_ass = $session->execute($session->prepare("SELECT rolename FROM rolematrix"));

        foreach ($res_ass as $row_ass) {
            array_push($arr_assigned, $row_ass['rolename']);
        }
        sort($arr_assigned);
        $final_arr = array("company_data" => $arr, "assigned_role" => array_unique($arr_assigned));

        // echo json_encode($final_arr);

        $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => $final_arr];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function email_by_role($companycode, $email, $role)
{


    try {
        global $session;
        if ($role == '') {
            $role = ' ';
        }
        if ($role == 'Auto') {
            $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => "Auto"];
            return $arr_return;
        }
        $arr = array();

        //Roles
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
        $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => $email_arr];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function companykpimaster_modify($txn_id, $id, $area, $parameter, $kpi, $input_parameter_1, $input_parameter_2, $input_parameter_1_value, $input_parameter_2_value, $year, $month, $kpiref, $session_kpi, $companycode, $email, $role)
{
    try {
        global $session;

        $kpiref = "";
        if (isset($_POST['kpiref'])) {
            $kpiref = $_POST['kpiref'];
        }

        $session_kpi = "";
        if (isset($_POST['session_kpi'])) {
            $session_kpi = $_POST['session_kpi'];
        }
        if ($session_kpi == '') {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Session lost. Refresh the page and try again."];
            exit();
        }


        if ($id == '' || $area == '' || $parameter == '' || $kpi == '') {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Error Occured. Invalid ID"];
            exit();
        }
        if ($year == "" || $month == "" || $role == "" || $email == "") {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Fields should not be empty"];
            exit();
        }
        if ($input_parameter_1 != 'Not Applicable') {
            if ($input_parameter_1_value == "") {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Parameter 1 should  be only number and  not be empty"];
                exit();
            }
        }
        if ($input_parameter_2 != 'Not Applicable') {
            if ($input_parameter_2_value == "") {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Parameter 2 should  be only number and  not be empty"];
                exit();
            }
        }

        if ($input_parameter_2_value == '') {
            if (is_numeric($input_parameter_1_value)) {
                $val = $input_parameter_1_value;
            } else {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Parameter 1 value should be number"];
                exit();
            }
        } else {
            if (is_numeric($input_parameter_1_value) && is_numeric($input_parameter_2_value)) {
                if ($input_parameter_1_value <= 0) {
                } else {
                    if ($input_parameter_1_value > 0) {
                        if ($input_parameter_2_value <= 0) {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Parameter 2 value should be greater than 0"];
                            exit();
                        }
                    } else {
                        if ($input_parameter_2_value <= 0) {
                            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Parameter 2 value should be greater than 0"];
                            exit();
                        }
                    }
                }


                $input_parameter_1_value = (int) $input_parameter_1_value;
                $input_parameter_2_value = (int) $input_parameter_2_value;
                if ($input_parameter_2_value <= 0) {
                    $val = 0;
                } else {
                    $val = ($input_parameter_1_value / $input_parameter_2_value) * 100;
                }


            } else {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Parameter 1 and Parameter 2 value should be number only"];
                exit();
            }
        }

        $input_parameter_1_value = (string) $input_parameter_1_value;
        $input_parameter_2_value = (string) $input_parameter_2_value;
        $final_val = number_format((float) $val, 2, '.', '');

        $month_arr = array(
            "jan" => "january",
            "feb" => "february",
            "mar" => "march",
            "apr" => "april",
            "may" => "may",
            "jun" => "june",
            "jul" => "july",
            "aug" => "august",
            "sep" => "september",
            "oct" => "october",
            "nov" => "november",
            "dec" => "december"
        );

        $month_lower = strtolower($month);

        $resultalw = $session->execute($session->prepare("SELECT * FROM defaulteditkpi WHERE companycode=? AND lawid=? AND year=? AND session_id=? ALLOW FILTERING"), array('arguments' => array($companycode, $id, $year, $session_kpi)));
        if ($resultalw->count() > 0) {
            foreach ($resultalw as $rowalw) {
                $txn_id = $rowalw['id'];
                if (isset($month_arr[$month_lower])) {
                    $up_col_1 = $month_arr[$month_lower];
                    $up_col_2 = "input_parameter_1_value_" . $month_lower;
                    $up_col_3 = "input_parameter_2_value_" . $month_lower;
                    $up_col_4 = "final_" . $month_lower;
                    $up_col_5 = "role_" . $month_lower;
                    $up_col_6 = "email_" . $month_lower;
                    $session->execute(
                        $session->prepare("UPDATE defaulteditkpi SET $up_col_1=?,$up_col_2=?,$up_col_3=?,$up_col_4=?,$up_col_5=?,$up_col_6=?,kpireference=?,status=?,temp_status WHERE id=?"),
                        array(
                            'arguments' => array(
                                $month,
                                $input_parameter_1_value,
                                $input_parameter_2_value,
                                $final_val,
                                $role,
                                $email,
                                $kpiref,
                                "0",
                                $txn_id,
                                "1"
                            )
                        )
                    );

                    // myLog_new($_SERVER['REMOTE_ADDR'], "AL002", "task complete", "1", "KPI Master Creation: Successful", $_SESSION['transactionid'], $_SERVER['PHP_SELF'], $_SERVER['HTTP_REFERER'], session_id(), http_response_code(), $_SESSION['role'], $_SERVER['HTTP_USER_AGENT'], $_SESSION['email'], $_SESSION['customer_id'], $_SESSION['companycode']);
                    // echo "success";
                    $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => "success"];
                    return $arr_return;
                } else {
                    echo "Invalid data";
                    exit();
                }
            }
        } else {

            if (isset($month_arr[$month_lower])) {
                $up_col_1 = $month_arr[$month_lower];
                $up_col_2 = "input_parameter_1_value_" . $month_lower;
                $up_col_3 = "input_parameter_2_value_" . $month_lower;
                $up_col_4 = "final_" . $month_lower;
                $up_col_5 = "role_" . $month_lower;
                $up_col_6 = "email_" . $month_lower;

                $query_insert = $session->prepare("INSERT INTO defaulteditkpi(
            id,createdate,effectivedate,lawid,area,parameter,kpi,input_parameter_1,input_parameter_2,year,$up_col_1,$up_col_2,$up_col_3,status,companycode,$up_col_5,$up_col_6,kpireference,$up_col_4,prev_id,session_id,transactionid,temp_status)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $session->execute(
                    $query_insert,
                    array(
                        'arguments' => array(
                            new \Cassandra\Uuid(),
                            new \Cassandra\Timestamp(),
                            new \Cassandra\Timestamp(),
                            $id,
                            $area,
                            $parameter,
                            $kpi,
                            $input_parameter_1,
                            $input_parameter_2,
                            $year,
                            $month,
                            $input_parameter_1_value,
                            $input_parameter_2_value,
                            "0",
                            $companycode,
                            $role,
                            $email,
                            $kpiref,
                            $final_val,
                            "",
                            $session_kpi,
                            $txn_id,
                            "1"
                        )
                    )
                );

                $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => "success"];
                return $arr_return;
                // myLog_new($_SERVER['REMOTE_ADDR'], "AL002", "task complete", "1", "KPI Master modification: Successful", $_SESSION['transactionid'], $_SERVER['PHP_SELF'], $_SERVER['HTTP_REFERER'], session_id(), http_response_code(), $_SESSION['role'], $_SERVER['HTTP_USER_AGENT'], $_SESSION['email'], $_SESSION['customer_id'], $_SESSION['companycode']);
                // echo "success";
                // myLog_new($_SERVER['REMOTE_ADDR'], "AL002", "task complete", "1", "KPI Master Creation: Successful", $_SESSION['transactionid'], $_SERVER['PHP_SELF'], $_SERVER['HTTP_REFERER'], session_id(), http_response_code(), $_SESSION['role'], $_SERVER['HTTP_USER_AGENT'], $_SESSION['email'], $_SESSION['customer_id'], $_SESSION['companycode']);
            } else {
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid data"];
                exit();
            }
        }

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function get_activity_list_on_page_load($txn_id, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = [];
        $arr_final = [];
        //Email
        $result_role_val = $session->execute($session->prepare("SELECT * FROM  defaulteditkpi  WHERE companycode=?  AND status =?   ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
        if ($result_role_val->count() == 0) {


        } else {
            foreach ($result_role_val as $row_val) {
                $row_val['id'] = (string) $row_val['id'];
                $arr_final[] = $row_val;
            }

        }
        $result_role_val = $session->execute($session->prepare("SELECT * FROM  defaulteditkpi WHERE companycode=?  AND status =? AND temp_status=?  ALLOW FILTERING"), array('arguments' => array($companycode, "0", "1")));
        if ($result_role_val->count() == 0) {


        } else {
            foreach ($result_role_val as $row_val) {
                $row_val['id'] = (string) $row_val['id'];
                $arr[] = $row_val;
            }
        }
        $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => $arr, "arr_final" => $arr_final];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}
function submit_kpi_schedule_data($tid, $wid, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = array();
        $result_kpi = $session->execute($session->prepare("SELECT * FROM default_kpi_txn WHERE companycode=? AND  notebookid=? AND screen_status=? ALLOW FILTERING"), array('arguments' => array($companycode, $wid, "approve")));
        $version_kpi = $result_kpi[0]['version'];

        $result = $session->execute($session->prepare("SELECT * FROM defaulteditkpi WHERE companycode=?  AND notebookid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $wid, "approve")));
        $month_arr = array("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec");
        $monthFull_arr = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");

        foreach ($result as $key => $value) {
            $email_show = "";
            $role_show = "";
            $value['id'] = (string) $value['id'];
            foreach ($monthFull_arr as $key_fr => $value_fr) {
                if ($value[$value_fr] == ucwords($month_arr[$key_fr])) {
                    $email_show = $value['email_' . $month_arr[$key_fr]];
                    $role_show = $value['role_' . $month_arr[$key_fr]];

                    if ($value['input_parameter_2_value_' . $month_arr[$key_fr]] != '') {
                        $value['final_' . $month_arr[$key_fr]] = $value['final_' . $month_arr[$key_fr]] . "%";
                    }

                    $value['btn_' . $month_arr[$key_fr]] = ucwords($month_arr[$key_fr]);

                    $value['email_show'] = $email_show;
                    $value['role_show'] = $role_show;

                } else {
                    $value['btn_' . $month_arr[$key_fr]] = "";

                    $value['final_' . $month_arr[$key_fr]] = "";
                }
            }
            $arr[] = $value;
        }
        $temp_price_for_ques = array_column($arr, 'area');
        array_multisort($temp_price_for_ques, SORT_ASC, $arr);

        $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => $arr];
        return $arr_return;

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function approve_accept_reject(
    $id,
    $lawid,
    $notebookid,
    $transactionid,
    $type,
    $january,
    $february,
    $march,
    $april,
    $may,
    $june,
    $july,
    $august,
    $september,
    $october,
    $november,
    $december,
    $companycode,
    $email,
    $role
) {
    try {
        global $session;

        if ($id == '' || $lawid == '' || $notebookid == '' || $transactionid == '' || $type == '') {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Data"];
            exit();
        }

        $result_edit_data = $session->execute($session->prepare("SELECT prev_id FROM defaulteditkpi WHERE id=?"), array('arguments' => array(new \Cassandra\Uuid($id))));
        if ($result_edit_data->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Data"];
            exit();
        }
        $row_prev_data = array();

        $reject_data = 1;
        if ($result_edit_data[0]['prev_id'] == '') {
            $reject_data = 0;
        } else {
            $result_prev_data = $session->execute($session->prepare("SELECT * FROM defaulteditkpi WHERE id=?"), array('arguments' => array(new \Cassandra\Uuid($result_edit_data[0]['prev_id']))));
            $row_prev_data = $result_prev_data[0];
        }

        $result_editkpi = $session->execute($session->prepare("SELECT id FROM defaulteditkpi WHERE companycode =? AND status =?    AND notebookid =?    ALLOW FILTERING"), array('arguments' => array($companycode, "approve", $notebookid)));
        if ($result_editkpi->count() == 1) {
            $result_kpi = $session->execute($session->prepare("SELECT id FROM default_kpi_txn WHERE companycode =? AND status =?    AND notebookid =?    ALLOW FILTERING"), array('arguments' => array($companycode, "1", $notebookid)));
            foreach ($result_kpi as $row_kpi) {
                $notice_update = notice_update_all($notebookid, $companycode, $email, $role, "KP02");

                $session->execute($session->prepare("UPDATE default_kpi_txn SET  screen_status=? WHERE id=?"), array('arguments' => array("edit", new \Cassandra\Uuid($row_kpi['id']))));
            }
        }

        $month_arr = array(
            "jan" => "january",
            "feb" => "february",
            "mar" => "march",
            "apr" => "april",
            "may" => "may",
            "jun" => "june",
            "jul" => "july",
            "aug" => "august",
            "sep" => "september",
            "oct" => "october",
            "nov" => "november",
            "dec" => "december"
        );

        $input_month = array($january, $february, $march, $april, $may, $june, $july, $august, $september, $october, $november, $december);

        //Accept
        foreach ($input_month as $key_month => $value_month) {
            if ($value_month == '') {
                unset($input_month[$key_month]);
            } else {
                $month = strtolower($value_month);
                if (isset($month_arr[$month])) {
                } else {
                    return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Option"];
                    exit();
                }
            }
        }


        if ($type == 'accept') {
            foreach ($input_month as $key_month => $value_month) {
                $month = strtolower($value_month);

                $result_kpi = $session->execute($session->prepare("SELECT id,version FROM default_kpi_txn WHERE companycode =? AND status =?    AND notebookid =?  ALLOW FILTERING"), array('arguments' => array($companycode, "1", $notebookid)));
                foreach ($result_kpi as $row_kpi) {
                    $version = $row_kpi['version'];
                    $version_new = $version + 0.1;
                    gettype($version_new);
                    $session->execute($session->prepare("UPDATE default_kpi_txn SET  version = ? WHERE id = ?"), array('arguments' => array(strval($version_new), new \Cassandra\Uuid($row_kpi['id']))));
                }

                $up_col_1 = $month_arr[$month];
                $up_col_2 = 'version_' . $month;
                $session->execute($session->prepare("UPDATE defaulteditkpi SET status=?,$up_col_1=?,$up_col_2 = ?,modifydate=? WHERE id=?"), array('arguments' => array("1", "approved", strval($version_new), new \Cassandra\Timestamp(), new \Cassandra\Uuid($id))));
            }
            $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => "The request is approved."];
            return $arr_return;

        } else {

            foreach ($input_month as $key_month => $value_month) {
                $month = strtolower($value_month);

                $result_kpi = $session->execute($session->prepare("SELECT id,version FROM default_kpi_txn WHERE companycode =? AND status =?    AND notebookid =?  ALLOW FILTERING"), array('arguments' => array($companycode, "1", $notebookid)));
                foreach ($result_kpi as $row_kpi) {
                    $version = $row_kpi['version'];
                    $version_new = $version + 0.1;
                    gettype($version_new);
                    $session->execute($session->prepare("UPDATE default_kpi_txn SET  version = ? WHERE id = ?"), array('arguments' => array(strval($version_new), new \Cassandra\Uuid($row_kpi['id']))));
                }

                $up_col_1 = $month_arr[$month];
                $up_col_2 = 'version_' . $month;
                $up_col_3 = 'input_parameter_1_value_' . $month;
                $up_col_4 = 'input_parameter_2_value_' . $month;
                $up_col_5 = 'final_' . $month;

                $input_parameter_1_value_jan = "";
                $input_parameter_2_value_jan = "";
                $final_jan = "";
                if ($reject_data == 1) {
                    $input_parameter_1_value_jan = $row_prev_data[$up_col_3];
                    $input_parameter_2_value_jan = $row_prev_data[$up_col_4];
                    $final_jan = $row_prev_data[$up_col_5];
                }

                $session->execute(
                    $session->prepare("UPDATE defaulteditkpi SET status=?,$up_col_1=?,$up_col_2 = ?,modifydate=?,$up_col_3=?,$up_col_4=?,$up_col_5=? WHERE id=?"),
                    array(
                        'arguments' => array(
                            "1",
                            "reject",
                            strval($version_new),
                            new \Cassandra\Timestamp(),
                            $input_parameter_1_value_jan,
                            $input_parameter_2_value_jan,
                            $final_jan,
                            new \Cassandra\Uuid($id)
                        )
                    )
                );
            }
            $arr_return = ["code" => 200, "success" => true, "message" => "area", "data" => "The request is rejected."];
            return $arr_return;
        }

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function show_define_report_all(
    $page_index,
    $companycode,
    $email,
    $role
) {
    try {
        global $session;
        $arr = array();
        $arr_txn = [];
        if (isset($page_index)) {
            $page_index = (int) $page_index;
        }
        $result_status = $session->execute(
            $session->prepare("SELECT id,createdate FROM default_kpi_txn WHERE companycode =?   AND status =? ALLOW FILTERING"),
            array('arguments' => array($companycode, "1"))
        );

        foreach ($result_status as $row_txn) {
            $createdate_str = (string) $row_txn['createdate'];
            $arr_txn[(string) $row_txn['id']] = (int) $createdate_str;
        }
        $result_id = $session->execute(
            $session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvcompanycode =?   AND status =? AND active_status=? AND wcvworkflowname=? ALLOW FILTERING"),
            array('arguments' => array($companycode, "1", 'active', 'kpi'))
        );
        arsort($arr_txn);
        $array_chunk = array_chunk($arr_txn, 10, true);
        $total_index = count($array_chunk);
        $arr_final_txn = $array_chunk[$page_index];

        foreach ($arr_final_txn as $key_id => $value) {
            $result_status = $session->execute(
                $session->prepare("SELECT * FROM default_kpi_txn WHERE id=?"),
                array('arguments' => array(new \Cassandra\Uuid($key_id)))
            );
            foreach ($result_status as $row) {
                $row['id'] = $key_id;
                $createdate_str = (string) $row['createdate'];
                $createdate_int = (int) $createdate_str / 1000;
                $row['createdate'] = date("d-m-Y", $createdate_int);
                if ($row['transactionid'] == '') {
                    if ((string) $result_id[0]['wcvid'] != '') {
                        $row['transactionid'] = (string) $result_id[0]['wcvid'];
                        $arr[] = $row;
                    }

                } else {
                    $arr[] = $row;
                }


            }
        }

        $arr_fn = [
            "total_index" => $total_index,
            "page_index" => $page_index,
            "data" => $arr
        ];

        $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $arr_fn];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function get_comp_score_by_module($option_for_comp_score, $comp_score_module, $companycode, $email, $role)
{
    try {

        $options_input = json_decode($_POST['option_for_comp_score'], true);

        global $session;
        $transactionid = " ";
        $vendorid = "";
        if ($options_input['vendorid']) {
            $vendorid = $options_input['vendorid'];
        }
        if ($vendorid != "") {
            $result_vendor = $session->execute(
                $session->prepare("SELECT transactionid FROM transactions WHERE vendorid=? AND companycode=? AND active_for_scoring=? ALLOW FILTERING"),
                array(
                    'arguments' => array(
                        $vendorid,
                        $companycode,
                        1
                    )
                )
            );

            if ($result_vendor->count() > 0) {
                $transactionid = (string) $result_vendor[0]['transactionid'];
            }
        }

        if ($options_input['transactionid']) {
            $transactionid = $options_input['transactionid'];
        }

        $comp_score_arr = get_comp_score_for_acf_assessment($transactionid, $companycode);
        if (!$comp_score_arr['success']) {
            $arr_return = ["success" => false, "msg" => "No data found"];
        }
        $comp_score = number_format((float) $comp_score_arr['data']['score'], 2, '.', '');
        $arr_return = ["success" => true, "msg" => "$transactionid", "data" => $comp_score];


        $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $arr_return];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}
?>