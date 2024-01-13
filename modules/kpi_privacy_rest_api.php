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
                $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $_POST];
                return $arr_return;

                if (isset($_POST['txn_id']) && isset($_POST['id']) && isset($_POST['area']) && isset($_POST['parameter']) && isset($_POST['kpi']) && isset($_POST['input_parameter_1']) && isset($_POST['input_parameter_2']) && isset($_POST['input_parameter_1_value']) && isset($_POST['input_parameter_2_value']) && isset($_POST['year']) && isset($_POST['month']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {


                    $output = companykpimaster_modify($_POST['txn_id'] && $_POST['id'] && $_POST['area'] && $_POST['parameter'] && $_POST['kpi'] && $_POST['input_parameter_1'] && $_POST['input_parameter_2'] && $_POST['input_parameter_1_value'] && $_POST['input_parameter_2_value'] && $_POST['year'] && $_POST['month'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
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

function companykpimaster_modify($txn_id, $id, $area, $parameter, $kpi, $input_parameter_1, $input_parameter_2, $input_parameter_1_value, $input_parameter_2_value, $year, $month, $companycode, $email, $role)
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
            "oct" => "octomber",
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
?>