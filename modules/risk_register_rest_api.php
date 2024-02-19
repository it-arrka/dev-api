<?php

function GetRiskRegisterHandler($funcCallType)
{
  try {
    switch ($funcCallType) {

      case "productid-read-for-cmpany":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = productid_read_for_cmpany($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "internal-and-external-team-list":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = internal_and_external_team_list($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "comapany-email-dept-read":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = comapany_email_dept_read($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

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

      case "show-define-risk-all":

        $page = 1;
        $limit = 10;
        $day = "ALL";
        if (isset($_GET["page"])) {
          $page = (int) $_GET["page"];
        }
        if (isset($_GET["limit"])) {
          $limit = (int) $_GET["limit"];
        }
        if (isset($_GET["day"])) {
          $day = $_GET["day"];
        }


        if (isset($_GET['type_n']) && isset($_GET['riskregister']) && isset($_GET['page_index']) && isset($_GET['page_access']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {


          $output = show_define_risk_all($_GET['type_n'], $_GET['riskregister'], $_GET['page_index'], $_GET['page_access'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $limit, $page, $day);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "vularea-read-from-company":
        if (isset($_GET['tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = vularea_read_from_company($_GET['tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "define-risk-data-save":
        $jsonString = file_get_contents('php://input');
        $json = json_decode($jsonString, true);
        if (isset($json['arr_final_to_send']) && isset($json['txn_id_incident']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = define_risk_data_save($json['arr_final_to_send'], $json['txn_id_incident'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "fetch-risk-report":
        if (isset($_GET['tid']) && isset($_GET['wid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = fetch_risk_report($_GET['tid'], $_GET['wid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
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


function productid_read_for_cmpany($companycode, $email, $role)
{
  try {
    global $session;
    $arr = array();
    $result = $session->execute($session->prepare("SELECT * FROM productid WHERE companycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    foreach ($result as $value) {
      $arr[$value['product']] = explode("|", $value['module']);
    }
    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function internal_and_external_team_list($companycode, $email, $role)
{
  try {
    global $session;
    $arr_client = [];
    $arr_vendor = [];
    $arr_d = [];

    $result_d = $session->execute($session->prepare("SELECT locationdepartment FROM locationinscope WHERE companycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    foreach ($result_d as $row_d) {
      $dept = explode("|", $row_d['locationdepartment']);
      foreach ($dept as $det) {
        $dep_t = explode(",", $det);
        if ($dep_t[0] !== "") {
          array_push($arr_d, $dep_t[0]);
        }
      }
    }
    $arr_d_unique = array_unique($arr_d);
    sort($arr_d_unique);

    $result = $session->execute($session->prepare("SELECT ccid,cccustname FROM clientcontract WHERE cccompanycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    foreach ($result as $row) {
      $arr_client[(string) $row['ccid']] = $row['cccustname'];
    }
    asort($arr_client);

    $result_v = $session->execute($session->prepare("SELECT vcid,vccustname FROM vendorcontract WHERE vccompanycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    foreach ($result_v as $row_v) {
      $arr_vendor[(string) $row_v['vcid']] = $row_v['vccustname'];
    }
    asort($arr_vendor);

    $arr_final = ["client_list" => $arr_client, "vendor_list" => $arr_vendor, "dept_list" => $arr_d_unique];

    $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}


function comapany_email_dept_read($companycode, $email, $role)
{
  try {
    // global $session;
    // $customer_array = array();
    // $arr_d = array();
    // $arr_role = array();

    // //Email
    // $result_for_required_customer = $session->execute($session->prepare("SELECT custemailaddress FROM customer WHERE custcompanycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    // $result_for_required_customer_n = $session->execute($session->prepare("SELECT custemail FROM custassignedcompany WHERE assignedcompany = ? ALLOW FILTERING"), array('arguments' => array($companycode)));
    // foreach ($result_for_required_customer as $row) {
    //   array_push($customer_array, $row['custemailaddress']);
    // }

    // foreach ($result_for_required_customer_n as $row_n) {

    //   $result_role_val = $session->execute($session->prepare("SELECT rtcuuid FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtccustemail=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $row_n['custemail'])));
    //   if ($result_role_val->count() > 0) {
    //     array_push($customer_array, $row_n['custemail']);
    //   }
    // }
    // $customer_array_n = array_unique($customer_array);
    // sort($customer_array_n);

    // $law_arr = applicable_laws_read("array");
    // unset($law_arr['vendor']);
    // unset($law_arr['technical risk']);
    // //Dept
    // $res_d = $session->execute($session->prepare('SELECT locationdepartment FROM locationinscope WHERE companycode=? ALLOW FILTERING'), array('arguments' => array($companycode)));
    // foreach ($res_d as $row_d) {
    //   $dept = explode("|", $row_d['locationdepartment']);
    //   foreach ($dept as $det) {
    //     $dep_t = explode(",", $det);
    //     if ($dep_t[0] !== "") {
    //       array_push($arr_d, $dep_t[0]);
    //     }
    //   }
    // }
    // sort($arr_d);

    // $result_role = $session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    // foreach ($result_role as $row) {
    //   array_push($arr_role, $row['rtcrole']);
    // }
    // sort($arr_role);

    // $res = json_encode(array("email" => $customer_array_n, "dept" => array_unique($arr_d), "law" => $law_arr, "role" => array_unique($arr_role)));

    global $session;
    $customer_array = array();
    $arr_d = array();
    $arr_role = array();



    $result_role = $session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
    foreach ($result_role as $row) {
      array_push($arr_role, $row['rtcrole']);
    }
    sort($arr_role);

    $arr_return = ["code" => 200, "success" => true, "data" => $arr_role];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
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

function show_define_risk_all($type_n, $riskregister, $page_index, $page_access, $companycode, $email, $role, $limit, $page, $day)
{
  try {
    global $session;
    $arr = [];
    $arr_txn = [];
    $page_index = 0;
    if (isset($_POST['page_index'])) {
      $page_index = (int) $_POST['page_index'];
    }
    if (isset($_POST['page_access'])) {
      $page_access = (int) $_POST['page_access'];
    }
    if ($riskregister != 'all') {
      $result = $session->execute($session->prepare("SELECT riskidfixed,createdate FROM default_riskmgmtdefinerisk WHERE companycode=? AND status=?   AND riskregistername = ? AND filleremail= ?  ALLOW FILTERING"), array('arguments' => array($companycode, "1", $riskregister, $email)));
    } else {
      if ($page_access == 'PG059') {
        $result = $session->execute($session->prepare("SELECT riskidfixed,createdate FROM default_riskmgmtdefinerisk WHERE companycode=? AND status=?  ALLOW FILTERING"), array('arguments' => array($companycode, "1")));
      } else {
        $result = $session->execute($session->prepare("SELECT riskidfixed,createdate FROM default_riskmgmtdefinerisk WHERE companycode=? AND status=?   AND filleremail= ?  ALLOW FILTERING"), array('arguments' => array($companycode, "1", $email)));
      }
    }


    foreach ($result as $row_txn) {
      $createdate_str = (string) $row_txn['createdate'];
      $arr_txn[(string) $row_txn['riskidfixed']] = (int) $createdate_str;
    }
    arsort($arr_txn);

    $array_chunk = array_chunk($arr_txn, 10, true);
    $total_index = count($array_chunk);
    $arr_final_txn = $array_chunk[$page_index];



    foreach ($arr_final_txn as $key_id => $value) {

      $result = $session->execute($session->prepare("SELECT * FROM default_riskmgmtdefinerisk WHERE riskidfixed=? ALLOW FILTERING"), array('arguments' => array($key_id)));


      foreach ($result as $row) {
        $row['riskidfixed'] = $key_id;

        //get comp_score
        $comp_score = "-";
        $comp_score_get = comp_score_for_secific_risk(['riskid' => $key_id], $companycode, $email, $role);

        if ($comp_score_get['success']) {
          $comp_score = $comp_score_get['data'];
        }
        $row['comp_score'] = $comp_score;

        $row['report_display'] = get_info_button_details_of_risk_acceptance($key_id, $row['riskidfixed']);
        $row['createdate'] = date("d M Y", $value / 1000);
        $res = $session->execute($session->prepare('SELECT riskratingupdate FROM riskmgmtreviewriskrating WHERE riskidfixed=? ALLOW FILTERING'), array('arguments' => array($key_id)));
        if ($res->count() > 0) {
          $row['riskratingupdate'] = $res[0]['riskratingupdate'];
        } else {
          $row['riskratingupdate'] = '';
        }

        $result_rrr = $session->execute($session->prepare("SELECT riskrating FROM riskmgmtreviewrisk WHERE riskidfixed=? ALLOW FILTERING"), array('arguments' => array($key_id)));
        if ($result_rrr->count() > 0) {
          $row['riskrating'] = $result_rrr[0]['riskrating'];
        }

        $row['id'] = (string) $row['id'];


        if ($row['mgmtdecision'] == '') {
          $result_mgmt = $session->execute($session->prepare("SELECT mgmtresponseaction FROM action_management_response WHERE refid=? ALLOW FILTERING"), array('arguments' => array($key_id)));
          $mgmtresponse_show = '';
          switch ($result_mgmt[0]['mgmtresponseaction']) {
            case 'A001':
              $mgmtresponse_show = 'Accept';
              break;
            case 'A002':
              $mgmtresponse_show = 'Reduce';
              break;
            case 'A003':
              $mgmtresponse_show = 'Transfer';
              break;
            case 'A004':
              $mgmtresponse_show = 'Avoid';
              break;
            default:
          }
          $row['mgmtdecision'] = $mgmtresponse_show;
        } else {
          $row['mgmtdecision'] = $row['mgmtdecision'];
        }
        $arr[] = $row;
      }
    }
    $arr_fn = [
      "total_index" => $total_index,
      "page_index" => $page_index,
      "data" => $arr
    ];

    $arr_return = ["code" => 200, "success" => true, "data" => $arr_fn];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

// Helper Functions
function comp_score_for_secific_risk($options, $companycode, $email, $role)
{
  try {
    global $session;

    $riskid = "";
    $actionrefid = "";
    if (isset($options['riskid'])) {
      $riskid = $options['riskid'];
    }

    if (isset($options['actionrefid'])) {
      $actionrefid = $options['actionrefid'];
    }

    //action roles
    $result = $session->execute(
      $session->prepare("SELECT score,status FROM compliance_score_risk WHERE companycode=? AND riskid=?"),
      array(
        'arguments' => array(
          $companycode,
          $riskid
        )
      )
    );

    $total = 0;
    $score = 0;
    $comp_score = 1;

    foreach ($result as $row) {
      $total++;
      $score = $score + $row['score'];
    }

    if ($total > 0) {
      $comp_score = $score / $total;
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

function get_info_button_details_of_risk_acceptance($refid, $txn_id)
{
  try {
    global $session;
    $arr = [];
    $arr_rw = [];
    if ($txn_id == '') {
      $txn_id = '';
    }
    $result_rn = $session->execute(
      $session->prepare("SELECT * FROM action_module_report_data WHERE  refid=? AND transactionid=? ALLOW FILTERING"),
      array('arguments' => array($refid, $txn_id))
    );
    foreach ($result_rn as $row_rn) {
      $arr_rw[] = $row_rn;
    }


    $arr_return = ["code" => 200, "success" => true, "message" => "Data Available", "data" => $arr_rw];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function vularea_read_from_company($tid, $companycode, $email, $role)
{
  try {
    global $session;
    $arr = array();
    $result = $session->execute($session->prepare("SELECT vularea FROM companyriskthreatimpact WHERE companycode=? AND transactionid=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, $tid, "1")));
    foreach ($result as $row) {
      array_push($arr, $row['vularea']);
    }

    $arr_return = ["code" => 200, "success" => true, "message" => "Data Available", "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function define_risk_data_save($data, $tid, $companycode, $email, $role)
{
  try {
    global $session;

    $result_txn = $session->execute($session->prepare("SELECT * FROM risk_txn WHERE companycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));

    if ($result_txn->count() == 0) {
      $new_tid = new \Cassandra\Uuid();
      $session->execute(
        $session->prepare("INSERT INTO risk_txn(
         id,
        companycode,
        createdate,
        effectivedate,
        fillercustcode,
        filleremail,
        notebookid,
        notebookname,
        report_status,
        screen_status,
        status,
        transactionid,
        version ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?) "),
        array(
          'arguments' => array(

            new \Cassandra\Uuid(),
            $_SESSION["companycode"],
            new \Cassandra\Timestamp(),
            new \Cassandra\Timestamp(),
            $_SESSION["customer_id"],
            $_SESSION["email"],
            (string) $new_tid,
            'New Risk Registers',
            "",
            "",
            "1",
            (string) $tid,
            "1"
          )
        )
      );

    } else {
      $result_txn = $session->execute($session->prepare("SELECT * FROM risk_txn WHERE companycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
      $new_tid = $result_txn['0']['notebookid'];
    }

    $result_id = $session->execute($session->prepare("SELECT * FROM default_riskmgmtdefinerisk WHERE companycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    $riskid = "RISK00" . (string) ($result_id->count() + 1);
    $riskidfixed = (string) new \Cassandra\Uuid();

    foreach ($data as $key => $value) {
      $session->execute(
        $session->prepare("INSERT INTO default_riskmgmtdefinerisk(
        id,
        assets,
        companycode,
        controlref,
        controltype,
        createdate,
        effectivedate,
        fillercustcode,
        filleremail,
        impact,
        mgmtdecision,
        policyref,
        probability,
        remarksimpact,
        riskarea,
        riskdesc,
        riskid,
        riskidfixed,
        riskownemail,
        riskownname,
        riskownrole,
        riskrating,
        riskregisterid,
        riskregistername,
        status,
        transactionid,
        vularea,screen_status,new_tid,department,newregistername ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) "),
        array(
          'arguments' => array(

            new \Cassandra\Uuid(),
            implode("|", $value['assets']),
            $companycode,
            "",
            $value["controltype"],
            new \Cassandra\Timestamp(),
            new \Cassandra\Timestamp(),
            $email,
            $value["impact"],
            "",
            $value["policyref"],
            $value["probability"],
            $value["remarksimpact"],
            $value["riskarea"],
            $value["riskdesc"],
            $riskid,
            $riskidfixed,
            $value["riskownemail"],
            $value["riskownname"],
            $value["riskownrole"],
            $value["riskrating"],
            "",
            "",
            "1",
            $tid,
            $value["vularea"],
            "review_risk",
            (string) $new_tid,
            $value['department'],
            'New Risk Registers'
          )
        )
      );





      //controlref starts here
      $controlRefArr = $value['controlref'];
      foreach ($controlRefArr as $law_tid => $controlsInputArr) {
        if ($law_tid != "") {
          //get law_array

          $lawOutputArr = get_law_detail_by_law_tid($law_tid, $companycode, $email, $role);


          if ($lawOutputArr['success']) {

            $lawOutput = $lawOutputArr['data'];

            $law_name = $lawOutput['law_name'];
            $law_version = $lawOutput['law_version'];
            $controls_list = $lawOutput['controls_list'];

            foreach ($controlsInputArr as $control_id) {

              if (isset($controls_list[$control_id])) {
                $ctrlArr = $controls_list[$control_id];
                $columns = [
                  "companycode",
                  "riskidfixed",
                  "law_tid",
                  "law_version",
                  "control_id",
                  "law_name",
                  "lcontrolno",
                  "lcontroldesc",
                  "lcontrolobjno",
                  "lcontrolobjdesc",
                  "ldomainno",
                  "ldomain",
                  "createdate",
                  "effectivedate",
                  "type",
                ];
                $columns_data = [
                  $companycode,
                  $riskidfixed,
                  $law_tid,
                  $law_version,
                  $control_id,
                  $law_name,
                  $ctrlArr['lcontrolno'],
                  $ctrlArr['lcontroldesc'],
                  $ctrlArr['lcontrolobjno'],
                  $ctrlArr['lcontrolobjdesc'],
                  $ctrlArr['ldomainno'],
                  $ctrlArr['ldomain'],
                  new \Cassandra\Timestamp(),
                  new \Cassandra\Timestamp(),
                  "define"
                ];
                $data_for_insert = [
                  "action" => "insert",
                  "table_name" => "control_list_risk",
                  "columns" => $columns,
                  "isCondition" => false,
                  "condition_columns" => "",
                  "columns_data" => $columns_data,
                  "isAllowFiltering" => false
                ];
                $table_insert = table_crud_actions($data_for_insert);
              }
            }
          }
        }
        //controlref ends here

      }

    }
    //create notification


    $email_role_array = risk_page_access_email_role_list($tid, $riskidfixed, "review_risk", $companycode);

    $notice_link = "review_risk.php?tid=" . $tid . "&wid=" . (string) $riskidfixed;
    $notice_update = notice_update_all((string) $riskidfixed, $companycode, $email, $role, "RM01");

    foreach ($email_role_array as $em_role) {
      $notice_output = notice_write("RM01", $companycode, $email, $role, $notice_link, $em_role['email'], $em_role['role'], $riskid, (string) $riskidfixed);
    }

    $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $riskid];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }


}


function fetch_risk_report($tid, $wid, $companycode, $email, $role)
{
  try {
    global $session;
    $arr = array();
    $final_arr = array();

    // define_risk_for_specific_riskid

    if ($wid == 'NA') {
      $res = $session->execute($session->prepare('SELECT * FROM default_riskmgmtdefinerisk WHERE companycode=? AND status=? ALLOW FILTERING'), array('arguments' => array($companycode, "1")));
    } else {
      $res = $session->execute($session->prepare('SELECT * FROM default_riskmgmtdefinerisk WHERE companycode=? AND status=? AND riskidfixed=? ALLOW FILTERING'), array('arguments' => array($companycode, "1", $wid)));
    }
    foreach ($res as $key => $value) {
      $value['assets'] = explode("|", $value['assets']);
      $arr[] = $value;
    }

    // review_risk_for_specific_riskid

    // $res = $session->execute($session->prepare('SELECT * FROM riskmgmtreviewrisk WHERE companycode=? AND status=? AND riskidfixed=? ALLOW FILTERING'), array('arguments' => array($companycode, "1", $wid)));
    // foreach ($res as $key => $value) {
    //   $value['assets'] = explode("|", $value['assets']);
    //   $arr[] = $value;
    // }

    // define_actions_for_specific_riskid

    // $res = $session->execute($session->prepare('SELECT * FROM riskmgmtdefineactions WHERE companycode=? AND status=? AND riskidfixed=? ALLOW FILTERING'), array('arguments' => array($companycode, "1", $wid)));
    // foreach ($res as $key => $value) {
    //   $value['assets'] = explode("|", $value['assets']);
    //   $arr[] = $value;
    // }

    // update_risk_rating_for_specific_riskid

    // $res = $session->execute($session->prepare('SELECT * FROM riskmgmtupdateriskrating WHERE companycode=? AND status=? AND riskidfixed=? ALLOW FILTERING'), array('arguments' => array($companycode, "1", $wid)));
    // foreach ($res as $key => $value) {
    //   $value['assets'] = explode("|", $value['assets']);
    //   $arr[] = $value;
    // }


    // review_risk_rating_for_specific_riskid
    // $res = $session->execute($session->prepare('SELECT * FROM riskmgmtreviewriskrating WHERE companycode=? AND status=? AND riskidfixed=? ALLOW FILTERING'), array('arguments' => array($companycode, "1", $wid)));
    // foreach ($res as $key => $value) {
    //   $value['assets'] = explode("|", $value['assets']);
    //   $arr[] = $value;
    // }
    $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => $arr];
    return $arr_return;


  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

// Helper Function

function risk_page_access_email_role_list($tid, $riskidfixed, $type, $companycode)
{
  try {
    global $session;
    $arr = array();
    $res_txn = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? AND status=? AND active_status=? ALLOW FILTERING"), array('arguments' => array($companycode, "risk", "1", "active")));
    $tid = (string) $res_txn[0]['wcvid'];

    switch ($type) {
      case 'review_risk':
        // $result_df=$session->execute($session->prepare("SELECT reassignemail,reassignrole FROM riskmgmtdefinerisk WHERE companycode=? AND status=? AND riskidfixed=? ALLOW FILTERING"),array('arguments'=>array($_SESSION['companycode'],"1",$riskidfixed)));
        // if ($result_df->count()>0 && $result_df[0]['reassignemail']!='' && $result_df[0]['reassignrole']!='') {
        //   $arr[]=array("email"=>$result_df[0]['reassignemail'],"role"=>$result_df[0]['reassignrole']);
        // }else {
        //check if onwer is reassigned for this transaction
        $result_reassign = $session->execute("SELECT notice_to,notice_to_role FROM notice WHERE transactionid=? AND notice_module_id=? AND status=? ALLOW FILTERING", array('arguments' => array($riskidfixed, "RM01", "1")));
        if ($result_reassign->count() > 0) {
          foreach ($result_reassign as $row_assign) {
            $arr[] = array("email" => $row_assign['notice_to'], "role" => $row_assign['notice_to_role']);
          }
        } else {
          $result = $session->execute(
            $session->prepare("SELECT * FROM companyconfigmaster WHERE  transactionid=? AND status=? AND ccmteamcategory=? AND ccmteamtitle=? AND ccmcompanycode=? ALLOW FILTERING"),
            array(
              'arguments' => array(
                $tid,
                "1",
                "FPC",
                "FPC-Review Risk",
                $companycode
              )
            )
          );
          foreach ($result as $row) {
            $arr[] = array("email" => $row['ccmemail'], "role" => $row['ccmrole']);
          }
        }
        // }
        break;

      case 'define_actions':
        $arr = module_assign_email_role_list("PG066", "modify", $companycode);
        foreach ($arr as $key_rn => $value_rn) {
          $result_rn = $session->execute($session->prepare("SELECT reassignemail,reassignrole FROM risk_reassign WHERE companycode=? AND status=? AND riskidfixed=? AND screen_status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $riskidfixed, 'define_actions')));
          if ($result_rn->count() > 0) {
            $arr[$key_rn]['email'] = $result_rn[0]['reassignemail'];
            $arr[$key_rn]['role'] = $result_rn[0]['reassignrole'];
          }
        }
        break;

      case 'update_risk_rating':
        $arr = module_assign_email_role_list("PG067", "modify", $companycode);
        foreach ($arr as $key_rn => $value_rn) {
          $result_rn = $session->execute($session->prepare("SELECT reassignemail,reassignrole FROM risk_reassign WHERE companycode=? AND status=? AND riskidfixed=? AND screen_status=? AND transactionid=? ALLOW FILTERING"), array('arguments' => array($companycode, "1", $riskidfixed, 'update_risk_rating', $tid)));
          if ($result_rn->count() > 0) {
            $arr[$key_rn]['email'] = $result_rn[0]['reassignemail'];
            $arr[$key_rn]['role'] = $result_rn[0]['reassignrole'];
          }
        }
        break;

      case 'review_risk_rating':
        // $result_df=$session->execute($session->prepare("SELECT reassignemail,reassignrole FROM riskmgmtupdateriskrating WHERE companycode=? AND status=? AND riskidfixed=? ALLOW FILTERING"),array('arguments'=>array($_SESSION['companycode'],"1",$riskidfixed)));
        // if ($result_df->count()>0 && $result_df[0]['reassignemail']!='' && $result_df[0]['reassignrole']!='') {
        //   $arr[]=array("email"=>$result_df[0]['reassignemail'],"role"=>$result_df[0]['reassignrole']);
        // }else {

        $result_reassign = $session->execute("SELECT notice_to,notice_to_role FROM notice WHERE transactionid=? AND notice_module_id=? AND status=? ALLOW FILTERING", array('arguments' => array($riskidfixed, "RM04", "1")));
        if ($result_reassign->count() > 0) {
          foreach ($result_reassign as $row_assign) {
            $arr[] = array("email" => $row_assign['notice_to'], "role" => $row_assign['notice_to_role']);
          }
        } else {
          $result = $session->execute(
            $session->prepare("SELECT * FROM companyconfigmaster WHERE  transactionid=? AND status=? AND ccmteamcategory=? AND ccmteamtitle=? AND ccmcompanycode=? ALLOW FILTERING"),
            array(
              'arguments' => array(
                $tid,
                "1",
                "FPC",
                "FPC-Residual Risk Rating",
                $companycode
              )
            )
          );
          foreach ($result as $row) {
            $arr[] = array("email" => $row['ccmemail'], "role" => $row['ccmrole']);
          }
        }


        // }
        break;
    }

    if (count($arr) == 0) {
      return $arr;
    } else {
      //unified the array
      $arr_to_send = array_unique($arr, SORT_REGULAR);
      return $arr_to_send;
    }
  } catch (\Exception $e) {
    return $e;
  }
}

?>