<?php

function asset_name_read_with_dept($companycode, $email, $role, $custcode)
{
    try {
        $arr_return = [];
        $arr = array();
        global $session;
        $res_add = $session->execute($session->prepare('SELECT assetname,transasscust,transdeptname FROM transasscust WHERE transasscompanycode=? AND status=? ALLOW FILTERING'), array('arguments' => array($companycode, "1")));
        foreach ($res_add as $row) {
            if ($row['assetname'] != "") {
                $arr[$row['assetname'] . " - " . $row['transdeptname']] = ["id" => (string) $row['transasscust'], "assetname" => $row['assetname'], "dept" => $row['transdeptname']];
            }
        }
        ksort($arr);

        $arr_return = ["code" => 200, "success" => true, "msg" => "Success", "data" => $arr];
        return $arr_return;

    } catch (Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}

?>