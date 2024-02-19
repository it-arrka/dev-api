<?php

function GetKpiSecurityHandler($funcCallType)
{
    try {
        switch ($funcCallType) {
            case "get-kpi-for-kpi":
                if (isset($_POST['tid']) && isset($_POST['parameter']) && isset($_POST['area']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_kpi_for_kpi($_POST['tid'], $_POST['parameter'], $_POST['area'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
                    if ($output['success']) {
                        commonSuccessResponse($output['code'], $output['data']);
                    } else {
                        catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
                    }
                } else {
                    catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
                }
                break;
            case "get-kpi-reference-kpi":
                if (isset($_POST['txn_id']) && isset($_POST['parameter']) && isset($_POST['area']) && isset($_POST['kpi']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
                    $output = get_kpi_reference_kpi($_POST['txn_id'], $_POST['parameter'], $_POST['area'], $_POST['kpi'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
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


function get_kpi_for_kpi($tid, $parameter, $area, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = [];
        if ($tid == '') {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Transaction Id"];
            exit();
        }
        $result = $session->execute($session->prepare("SELECT * FROM companykpisecmaster WHERE companycode=? and transactionid= ? AND  status=? AND parameter=? AND area=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1", $parameter, $area)));
        if ($result->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "NO Data Availableccc"];
            exit();
        } else {
            foreach ($result as $row) {
                $row['id'] = (string) $row['id'];
                $arr[] = $row;
            }
            $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $arr];
            return $arr_return;
        }

    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

function get_kpi_reference_kpi($txn_id, $parameter, $area, $kpi, $companycode, $email, $role)
{
    try {
        global $session;
        $arr = [];
        if ($txn_id == '') {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Transaction Id"];
            exit();
        }
        $result = $session->execute($session->prepare("SELECT kpi,id,kpireference FROM companykpisecmaster WHERE companycode=? and transactionid= ? AND  status=? AND parameter=? AND area=? AND kpi=? ALLOW FILTERING"), array('arguments' => array($companycode, $txn_id, "1", $parameter, $area, $kpi)));
        if ($result->count() == 0) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "NO Data Availableccc"];
            exit();
        } else {
            foreach ($result as $row) {
                $row['id'] = (string) $row['id'];
                $arr[] = $row;
            }
            $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $arr];
            return $arr_return;
        }
    } catch (Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}
?>