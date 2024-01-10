<?php

function GetRiskRegisterHandler($funcCallType)
{
  try {
    switch ($funcCallType) {
      case "read-asset":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = read_asset($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "riskarea-from-vularea":
        if (isset($_POST['vularea']) && isset($_POST['risk_type']) && isset($_POST['txn_id_incident']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = riskarea_from_vularea($_POST['vularea'], $_POST['risk_type'], $_POST['txn_id_incident'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;
      case "impact-by-risk-n-vul":
        if (isset($_POST['impact_by_risk']) && isset($_POST['impact_by_vul']) && isset($_POST['txn_id_incident']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = impact_by_risk_n_vul($_POST['impact_by_risk'], $_POST['impact_by_vul'], $_POST['txn_id_incident'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-email-and-role":
        if (isset($_POST['role']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = get_email_and_role($_POST['role'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "riskrating-by-pb-n-impact":
        if (isset($_POST['riskrating_by_pb']) && isset($_POST['riskrating_by_impact']) && isset($_POST['txn_id_incident']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {


          $output = riskrating_by_pb_n_impact($_POST['riskrating_by_pb'], $_POST['riskrating_by_impact'], $_POST['txn_id_incident'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-law-list-for-risk":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {


          $output = get_law_list_for_risk($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-law-detail-by-law-tid":
        if (isset($_POST['law_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {


          $output = get_law_detail_by_law_tid($_POST['law_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
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



function read_asset($companycode, $email, $role)
{
  try {
    $arr = array();
    global $session;
    $res = $session->execute($session->prepare('SELECT assetname,assetcat FROM transasscust WHERE transasscompanycode=? AND status=? ALLOW FILTERING'), array('arguments' => array($companycode, "1")));
    foreach ($res as $row) {
      array_push($arr, $row['assetname']);
    }
    // $new_array = array_unique($arr);
    // $arr = json_encode($new_array);
    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }


}

function riskarea_from_vularea($vularea, $risk_type, $txn_id_incident, $companycode, $email, $role)
{
  try {
    global $session;
    $version_arr = array();
    $arr = array();

    if ($vularea == '') {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid vularea"];
      exit();
    }

    if ($risk_type == 'master') {
      $result_ver = $session->execute("SELECT version FROM defaultriskthreatimpact");
      foreach ($result_ver as $row_ver) {
        array_push($version_arr, (int) $row_ver['version']);
      }
      $version_no = max($version_arr);

      $result = $session->execute($session->prepare("SELECT riskarea FROM defaultriskthreatimpact WHERE vularea=? AND version=? ALLOW FILTERING"), array('arguments' => array($vularea, (string) $version_no)));
      foreach ($result as $value) {
        array_push($arr, $value['riskarea']);
      }
    }

    if ($risk_type == 'company') {
      // $result_ver= $session->execute("SELECT version FROM defaultriskthreatimpact");
      // foreach ($result_ver as $row_ver) { array_push($version_arr,(int)$row_ver['version']); }
      // $version_no=max($version_arr);
      if (isset($_POST['txn_id_incident'])) {
        $result = $session->execute($session->prepare("SELECT riskarea FROM companyriskthreatimpact WHERE vularea=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($vularea, $_POST['txn_id_incident'], "1")));
        foreach ($result as $value) {
          array_push($arr, $value['riskarea']);
        }
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function impact_by_risk_n_vul($impact_by_risk, $impact_by_vul, $txn_id_incident, $companycode, $email, $role)
{
  try {
    $arr = array();
    global $session;

    $result = $session->execute($session->prepare("SELECT impact FROM companyriskthreatimpact WHERE riskarea=? AND vularea=? AND companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($impact_by_risk, $impact_by_vul, $companycode, $txn_id_incident, "1")));

    $arr_return = ["code" => 200, "success" => true, "data" => $result[0]['impact']];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_email_and_role($role, $companycode, $email, $global_role)
{
  try {

    global $session;
    $arr = [];
    //Email
    $result_role_val = $session->execute($session->prepare("SELECT rtccustemail FROM roletocustomer WHERE rtcrole=? AND companycode=? AND rolestatus=? ALLOW FILTERING"), array('arguments' => array($role, $companycode, "1")));
    if ($result_role_val->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "NO Data Available"];
      exit();
    } else {
      foreach ($result_role_val as $row) {
        $result_name = $session->execute($session->prepare("SELECT custfname,custlname FROM customer WHERE custemailaddress=?"), array('arguments' => array($row['rtccustemail'])));
        $name = $result_name[0]['custfname'] . " " . $result_name[0]['custlname'];
        $arr[] = $row['rtccustemail'] . "*|*" . $name;
      }

      $arr_return = ["code" => 200, "success" => true, "data" => $arr];
      return $arr_return;
    }
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function riskrating_by_pb_n_impact($riskrating_by_pb, $riskrating_by_impact, $txn_id_incident, $companycode, $email, $role)
{
  try {
    $arr = array();
    global $session;

    $result = $session->execute($session->prepare("SELECT value FROM companyriskrating WHERE probab=? AND impact=? AND companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($riskrating_by_pb, $riskrating_by_impact, $companycode, $txn_id_incident, "1")));

    $arr_return = ["code" => 200, "success" => true, "data" => $result[0]['value']];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_law_list_for_risk($companycode, $email, $role)
{
  try {
    global $session;
    if ($companycode == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Companycode should not be null"];
      exit();
    }
    $law = [];
    $result_law = $session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    foreach ($result_law as $row_law) {
      //get disp name
      if ($row_law['law'] != "") {
        $result_disp = $session->execute($session->prepare("SELECT display_name,usingkey FROM lawkeys WHERE dispname=? ALLOW FILTERING"), array('arguments' => array($row_law['law'])));
        $dispname = $row_law['law'];
        $key = $row_law['law'];
        if ($result_disp->count() > 0) {
          $dispname = $result_disp[0]['display_name'];
          $key = $result_disp[0]['usingkey'];
        }

        //get law_tid
        $law_tid = "";
        $law_version = "";
        $result_law_tid = $session->execute($session->prepare("SELECT id,version_overall FROM lawmap_content_txn WHERE ldispname=? ALLOW FILTERING"), array('arguments' => array($row_law['law'])));
        if ($result_law_tid->count() > 0) {
          $law_tid = (string) $result_law_tid[0]['id'];
          $law_version = $result_law_tid[0]['version_overall'];
        }

        $row_law['law_tid'] = $law_tid;
        $row_law['law_version'] = $law_version;
        $row_law['dispname'] = $dispname;
        $row_law['key'] = $key;
        $law[] = $row_law;
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $law];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_law_detail_by_law_tid($law_tid, $companycode, $email, $role)
{
  try {
    global $session;
    if ($law_tid == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Law tid"];
      exit();
    }

    //validate law tid
    $result_law_tid = $session->execute(
      $session->prepare("SELECT id,version_overall,ldispname FROM lawmap_content_txn WHERE id=?"),
      array(
        'arguments' => array(
          new \Cassandra\Uuid($law_tid)
        )
      )
    );

    if ($result_law_tid->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Law tid"];
      exit();
    }

    $version = $result_law_tid[0]['version_overall'];
    $law_name = $result_law_tid[0]['ldispname'];

    $arr_law = [];
    //get law details from lawmap_content
    $result_law = $session->execute(
      $session->prepare("SELECT id,lcontrolno,lcontroldesc,lcontrolobjno,lcontrolobjdesc,ldomain,ldomainno,sorting_order FROM lawmap_content WHERE transactionid=? AND status=? AND version=? ALLOW FILTERING"),
      array(
        'arguments' => array(
          $law_tid,
          "1",
          $version
        )
      )
    );

    foreach ($result_law as $row_law) {
      $row_law['id'] = (string) $row_law['id'];
      $arr_law[$row_law['id']] = $row_law;
    }

    array_multisort(array_column($arr_law, "sorting_order"), SORT_ASC, $arr_law);

    $arr_return = ["code" => 200, "success" => true, "data" => ["controls_list" => $arr_law, "law_name" => $law_name, "law_version" => $version]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

?>