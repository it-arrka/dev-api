<?php

function GetPolicyHandler($funcCallType)
{
  try {
    switch ($funcCallType) {
      case "get-policy-version-status":
        if (isset($_GET['law_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = get_policy_version_status($_GET['law_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-policy-domains":
        if (isset($_GET['law_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = get_policy_domains($_GET['law_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "get-policy-domain-data":
        if (isset($_GET['law_tid']) && isset($_GET['domains']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = get_policy_domains_data($_GET['law_tid'], $_GET['domains'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "discard-domain-policy-changes":
        if (isset($_POST['law_tid']) && isset($_POST['domains']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = discard_domain_policy_changes($_POST['law_tid'], $_POST['domains'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }

        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "delete-policy-domains":

        if (isset($_POST['law_tid']) && isset($_POST['domains']) && isset($_POST['domains_text']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = delete_policy_domains($_POST['law_tid'], $_POST['domains'], $_POST['domains_text'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "save-policy-acf-data":
        $jsonString = file_get_contents('php://input');
        $json = json_decode($jsonString, true);

        if (isset($json['data']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = save_policy_acf_data($json['data'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "check-if-all-domains-are-saved":
        if (isset($_POST['law_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = check_if_all_domains_are_saved($_POST['law_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);

          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "discard-policy-overall-changes":
        if (isset($_POST['law_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {

          $output = discard_policy_overall_changes($_POST['law_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);

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

function get_policy_version_status($law_tid, $companycode, $email, $role)
{
  try {
    global $session;
    if ($law_tid == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];
      exit();
    }

    $result_txn = $session->execute($session->prepare('SELECT law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'), array('arguments' => array($law_tid, "1")));
    if ($result_txn->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];
      exit();
    }
    $law_version = (string) $result_txn[0]['law_version'];

    //Get version history
    $arr = [];
    $result_version = $session->execute($session->prepare('SELECT * FROM policyacfoverallmapping_version WHERE law_tid=? AND law_version=? AND companycode=?'), array('arguments' => array($law_tid, $law_version, $companycode)));
    if ($result_version->count() == 0) {
      $arr = [
        'active_version' => 0,
        'active_date' => date("d-m-Y"),
        'edit_status' => false,
        'edit_version' => "",
        'edit_date' => "",
        'archived' => []
      ];
    } else {
      $active_version = 0;
      $active_date = "-";
      $edit_status = false;
      $edit_version = "";
      $edit_date = "";
      $archived = [];
      foreach ($result_version as $row_version) {
        if ($row_version['status'] == 'active') {
          $active_version = $row_version['version'];
          $active_date_str = (string) $row_version['createdate'];
          $active_date = date("d-m-y", (int) $active_date_str / 1000);
        } elseif ($row_version['status'] == 'edit') {
          $edit_status = true;
          $edit_version = $row_version['version'];
          $edit_date_str = (string) $row_version['createdate'];
          $edit_date = date("d-m-y", (int) $edit_date_str / 1000);
        } else {
          $edit_date_str = (string) $row_version['createdate'];
          $edit_date = date("d-m-y", (int) $edit_date_str / 1000);
          $archived[] = [
            "version" => $row_version['version'],
            "date" => $edit_date
          ];
        }
      }

      $arr = [
        'active_version' => $active_version,
        'active_date' => $active_date,
        'edit_status' => $edit_status,
        'edit_version' => $edit_version,
        'edit_date' => $edit_date,
        'archived' => $archived
      ];

    }

    //Rule for policyacfoverallmapping_version table
    //If active = status=active -> Only One entry in a partition. Two rows cannot be active
    //If edit = status=edit -> Only One entry in a partition. Two rows cannot be edit
    //After submission :: edit==active, active=archived
    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_policy_domains($law_tid, $companycode, $email, $role)
{
  try {
    global $session;

    //validate law_tid
    if ($law_tid == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];
      exit();
    }

    $result_txn = $session->execute($session->prepare('SELECT law_tid,law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'), array('arguments' => array($law_tid, "1")));
    if ($result_txn->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];
      exit();
    }
    //Get law_tid transactionid
    $law_version = (string) $result_txn[0]['law_version'];

    //get policy edit version
    $get_policy_version_status_for_law = get_policy_version_status($law_tid, $companycode, $email, $role);
    if (!$get_policy_version_status_for_law['success']) {
      return $get_policy_version_status_for_law;
      exit();
    }

    $policy_vArr = $get_policy_version_status_for_law['data'];
    $policy_edit_version = (int) $policy_vArr['edit_version'];
    $policy_active_version = (int) $policy_vArr['active_version'];
    $version_arr = [$policy_edit_version, $policy_active_version];

    $arr = [];

    if ($policy_active_version == 0) {
      $result = $session->execute($session->prepare('SELECT domains,domains_acf,redundant FROM policyacfmapping WHERE status=? AND version=? AND law_tid=? AND law_version=?'), array('arguments' => array("1", 1, $law_tid, $law_version)));
      foreach ($result as $row) {
        if (!$row['redundant']) {
          $arr[$row['domains_acf']][0] = [
            "domains" => $row['domains'],
            "domains_acf" => $row['domains_acf'],
            "saved" => false
          ];
        }
      }
    }



    $result_version = $session->execute($session->prepare('SELECT domains,domains_text,version,policy_version FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=?'), array(
      'arguments' => array(
        $law_tid,
        $law_version,
        $companycode,
        "1"
      )
    )
    );

    foreach ($result_version as $row_version) {
      $arr[$row_version['domains']][$row_version['version']] = [
        "domains" => $row_version['domains_text'],
        "domains_acf" => $row_version['domains'],
        "saved" => true
      ];
    }


    $arr_final = [];
    foreach ($arr as $key => $value) {
      $max_version = max(array_keys($value));
      if (isset($value[$max_version])) {
        $arr_final[] = $arr[$key][$max_version];
      }
    }

    //Check for deletion
    foreach ($arr_final as $key_final => $value_final) {
      //policyacfclientmapping_deletion
      $result_del = $session->execute($session->prepare('SELECT policy_version FROM policyacfclientmapping_deletion WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=?'), array(
        'arguments' => array(
          $law_tid,
          $law_version,
          $companycode,
          "1",
          $value_final['domains_acf']
        )
      )
      );
      foreach ($result_del as $row_del) {
        if (in_array($row_del['policy_version'], $version_arr)) {
          //remove this element
          unset($arr_final[$key_final]);
        }
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_policy_domains_data($law_tid, $domains, $companycode, $email, $role)
{
  try {
    global $session;
    //validate acf_tid
    if ($domains == "" || $law_tid == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];

      // $arr_return=["success"=>false,"msg"=>"Invalid Request","data"=>""];
      // return $arr_return;
      exit();
    }

    $result_txn = $session->execute($session->prepare('SELECT law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'), array('arguments' => array($law_tid, "1")));
    if ($result_txn->count() == 0) {
      // $arr_return=["success"=>false,"msg"=>"Invalid Law","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];
      exit();
    }
    //Get law_tid transactionid
    $law_version = (string) $result_txn[0]['law_version'];
    $arr = [];
    $arr_version = [];

    $result_version = $session->execute($session->prepare('SELECT createdate,version FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=?'), array(
      'arguments' => array(
        $law_tid,
        $law_version,
        $companycode,
        "1",
        $domains
      )
    )
    );

    foreach ($result_version as $row_version) {
      $createdate_str = (string) $row_version['createdate'];
      $row_version['createdate'] = date("d-m-Y", (int) $createdate_str / 1000);
      $arr_version[$row_version['version']] = $row_version;
    }

    if ($result_version->count() > 0) {
      $version = max(array_keys($arr_version));
      $createdate = $arr_version[$version]['createdate'];
      $result = $session->execute($session->prepare('SELECT domains_acf,statement,subdomains,subdomains_acf,arrkacompref FROM policyacfclientmapping WHERE status=? AND version=? AND law_tid=? AND law_version=? AND companycode=?'), array('arguments' => array("1", $version, $law_tid, $law_version, $companycode)));
      foreach ($result as $row) {
        if ($row['domains_acf'] == $domains) {
          // <Client Name/>
          $row['statement'] = str_replace("<Client Name/>", get_companyname_from_companycode($companycode), $row['statement']);
          $arr[$row['subdomains']][] = $row;
        }
      }
    } else {
      $createdate = date("d-m-Y");
      $version = 0;
      $result = $session->execute($session->prepare('SELECT domains_acf,statement,subdomains,subdomains_acf,redundant,arrkacompref FROM policyacfmapping WHERE status=? AND version=? AND law_tid=? AND law_version=?'), array('arguments' => array("1", 1, $law_tid, $law_version)));
      foreach ($result as $row) {
        if ($row['domains_acf'] == $domains) {
          if (!$row['redundant']) {
            $row['statement'] = str_replace("<Client Name/>", get_companyname_from_companycode($companycode), $row['statement']);
            $arr[$row['subdomains']][] = $row;
          }
        }
      }
    }

    $version_overall = $version;
    $arr_final = [
      "data" => $arr,
      "version" => $version_overall,
      "modifydate" => $createdate
    ];

    // myLog_new($_SERVER['REMOTE_ADDR'],"AL002","task complete","1","get_latest_version_of_question_list","get_latest_version_of_question_list",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    // $arr_return=["success"=>true,"msg"=>"success$e","data"=>$arr_final];
    $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];

    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function discard_domain_policy_changes($law_tid, $domains, $companycode, $email, $role)
{
  try {
    global $session;

    if ($law_tid == "") {
      // $arr_return=["success"=>false,"msg"=>"Invalid Request","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];
    }

    $result_txn = $session->execute($session->prepare('SELECT law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'), array('arguments' => array($law_tid, "1")));
    if ($result_txn->count() == 0) {
      // $arr_return=["success"=>false,"msg"=>"No Data Available","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "No Data Available"];
      exit();
    }
    $law_version = (string) $result_txn[0]['law_version'];

    //Rule for policyacfoverallmapping_version table
    //If active = status=active -> Only One entry in a partition. Two rows cannot be active
    //If edit = status=edit -> Only One entry in a partition. Two rows cannot be edit
    //After submission :: edit==active, active=archived

    //get policy edit version
    $get_policy_version_status_for_law = get_policy_version_status($law_tid, $companycode, $email, $role);
    if (!$get_policy_version_status_for_law['success']) {
      // return $get_policy_version_status_for_law;
      return ["code" => 400, "success" => false, "message" => $get_policy_version_status_for_law, "error" => ""];
      exit();
    }


    $policy_vArr = $get_policy_version_status_for_law['data'];
    if (!$policy_vArr['edit_status']) {
      // $arr_return=["success"=>false,"msg"=>"Not in Edit Mode","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Not in Edit Mode"];
    }
    $policy_edit_version = $policy_vArr['edit_version'];

    $result_domain_version = $session->execute($session->prepare('SELECT version,policy_version,id,createdate,domains_text FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=?'), array('arguments' => array($law_tid, $law_version, $companycode, "1", $domains)));

    $arr_dom_version = [];
    foreach ($result_domain_version as $row_domain_version) {
      if ($row_domain_version['policy_version'] == $policy_edit_version) {
        $arr_dom_version[$row_domain_version['version']] = $row_domain_version;
      }
    }

    if (count($arr_dom_version) == 0) {
      // $arr_return=["success"=>false,"msg"=>"Cannot discard this version. Already last in this domain.","data"=>""];
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Cannot discard this version. Already last in this domain."];
      return $arr_return;
    }

    $max_domain_version = max(array_keys($arr_dom_version));

    $del_id = $arr_dom_version[$max_domain_version]['id'];
    $del_createdate = $arr_dom_version[$max_domain_version]['createdate'];
    $del_version = $arr_dom_version[$max_domain_version]['version'];
    $del_policy_version = $arr_dom_version[$max_domain_version]['policy_version'];
    $del_domains_text = $arr_dom_version[$max_domain_version]['domains_text'];

    //delete and insert again with status = 0 in policyacfclientmapping_version
    $session->execute($session->prepare('DELETE FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=? AND id=?'), array('arguments' => array($law_tid, $law_version, $companycode, "1", $domains, $del_id)));

    $columns = [
      "companycode",
      "version",
      "law_tid",
      "law_version",
      "domains",
      "domains_text",
      "createdate",
      "effectivedate",
      "modifydate",
      "id",
      "policy_version",
      "status",
      "filleremail",
      "fillerrole"
    ];

    $columns_data = [
      $companycode,
      $del_version,
      $law_tid,
      $law_version,
      $domains,
      $del_domains_text,
      $del_createdate,
      $del_createdate,
      new \Cassandra\Timestamp(),
      $del_id,
      $del_policy_version,
      "0",
      $email,
      $role
    ];

    $data_for_crud = [
      "action" => "insert",
      "table_name" => "policyacfclientmapping_version",
      "columns" => $columns,
      "isCondition" => true,
      "condition_columns" => [],
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];

    $output = table_crud_actions($data_for_crud);
    if (!$output['success']) {
      // $arr_return = ["success" => false, "msg" => $output['msg']];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => $output['msg'], "error" => "invalid law_tid"];
    }


    //Now Delete from policyacfclientmapping
    $result_map = $session->execute($session->prepare('SELECT * FROM policyacfclientmapping WHERE status=? AND version=? AND law_tid=? AND law_version=? AND companycode=?'), array('arguments' => array("1", $max_domain_version, $law_tid, $law_version, $companycode)));
    foreach ($result_map as $row_map) {
      //Delete and insert
      if ($row_map['domains_acf'] == $domains && $row_map['policy_version'] == $policy_edit_version) {
        $columns = [
          "companycode",
          "status",
          "version",
          "law_tid",
          "law_version",
          "createdate",
          "effectivedate",
          "domains",
          "domains_acf",
          "statement",
          "subdomains",
          "subdomains_acf",
          "arrkacompref",
          "id",
          "policy_version",
          "modifydate",
          "sequence",
          "filleremail",
          "fillerrole"
        ];

        $columns_data = [
          $companycode,
          "0",
          $row_map['version'],
          $law_tid,
          $law_version,
          $row_map['createdate'],
          $row_map['effectivedate'],
          $row_map['domains'],
          $row_map['domains_acf'],
          $row_map['statement'],
          $row_map['subdomains'],
          $row_map['subdomains_acf'],
          $row_map['arrkacompref'],
          $row_map['id'],
          $row_map['policy_version'],
          new \Cassandra\Timestamp(),
          $row_map['sequence'],
          $email,
          $role
        ];

        $data_for_crud = [
          "action" => "insert",
          "table_name" => "policyacfclientmapping",
          "columns" => $columns,
          "isCondition" => true,
          "condition_columns" => [],
          "columns_data" => $columns_data,
          "isAllowFiltering" => false
        ];

        $output = table_crud_actions($data_for_crud);
        if (!$output['success']) {
          // $arr_return = ["success" => false, "msg" => $output['msg']];
          // return $arr_return;
          return ["code" => 400, "success" => false, "message" => $output['msg'], "error" => "invalid law_tid"];
        }

        //Delete this row with st=1
        $session->execute($session->prepare('DELETE FROM policyacfclientmapping WHERE status=? AND version=? AND law_tid=? AND law_version=? AND companycode=? AND sequence=? AND id=?'), array(
          'arguments' => array(
            "1",
            $row_map['version'],
            $law_tid,
            $law_version,
            $companycode,
            $row_map['sequence'],
            $row_map['id']
          )
        )
        );

      }
    }


    // $arr_return=["success"=>true,"msg"=>"success","data"=>""];
    $arr_return = ["code" => 200, "success" => true, "message" => "success"];

    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function delete_policy_domains($law_tid, $domains, $domains_text, $companycode, $email, $role)
{
  try {
    global $session;
    if ($law_tid == "" || $domains == "" || $domains_text == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid payload"];
    }

    $result_txn = $session->execute($session->prepare('SELECT law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'), array('arguments' => array($law_tid, "1")));
    if ($result_txn->count() == 0) {
      // $arr_return=["success"=>false,"msg"=>"No Data Available","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "No Data Available"];
      exit();
    }
    $law_version = (string) $result_txn[0]['law_version'];

    //get policy edit version
    $get_policy_version_status_for_law = get_policy_version_status($law_tid, $companycode, $email, $role);
    if (!$get_policy_version_status_for_law['success']) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $get_policy_version_status_for_law];
      exit();
    }

    $policy_vArr = $get_policy_version_status_for_law['data'];
    if (!$policy_vArr['edit_status']) {
      // $arr_return=["success"=>false,"msg"=>"Not in Edit Mode","data"=>""];
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Not in Edit Mode"];
      exit();
    }
    $policy_edit_version = (int) $policy_vArr['edit_version'];
    $policy_active_version = (int) $policy_vArr['active_version'];

    $version_arr = [$policy_edit_version, $policy_active_version];

    //From policyacfclientmapping_version
    $result_v = $session->execute($session->prepare('SELECT * FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=?'), array('arguments' => array($law_tid, $law_version, $companycode, "1", $domains)));
    foreach ($result_v as $row_v) {
      if (in_array($row_v['policy_version'], $version_arr)) {
        //From policyacfclientmapping
        $result_main = $session->execute($session->prepare('SELECT * FROM policyacfclientmapping WHERE status=? AND version=? AND law_tid=? AND law_version=? AND companycode=?'), array('arguments' => array("1", (int) $row_v['version'], $law_tid, $law_version, $companycode)));
        foreach ($result_main as $row_main) {
          if (in_array($row_main['policy_version'], $version_arr)) {
            if ($row_main['domains_acf'] == $domains) {
              //delete
              $session->execute($session->prepare('DELETE FROM policyacfclientmapping WHERE status=? AND version=? AND law_tid=? AND law_version=? AND companycode=? AND sequence=? AND id=?'), array('arguments' => array("1", (int) $row_v['version'], $law_tid, $law_version, $companycode, $row_main['sequence'], $row_main['id'])));
              //insert
              $columns = [
                "companycode",
                "status",
                "version",
                "law_tid",
                "law_version",
                "createdate",
                "effectivedate",
                "domains",
                "domains_acf",
                "statement",
                "subdomains",
                "subdomains_acf",
                "arrkacompref",
                "id",
                "policy_version",
                "modifydate",
                "sequence",
                "filleremail",
                "fillerrole"
              ];

              $columns_data = [
                $companycode,
                "0",
                $row_main['version'],
                $law_tid,
                $law_version,
                $row_main['createdate'],
                $row_main['effectivedate'],
                $row_main['domains'],
                $row_main['domains_acf'],
                $row_main['statement'],
                $row_main['subdomains'],
                $row_main['subdomains_acf'],
                $row_main['arrkacompref'],
                $row_main['id'],
                $row_main['policy_version'],
                new \Cassandra\Timestamp(),
                $row_main['sequence'],
                $email,
                $role
              ];

              $data_for_crud = [
                "action" => "insert",
                "table_name" => "policyacfclientmapping",
                "columns" => $columns,
                "isCondition" => true,
                "condition_columns" => [],
                "columns_data" => $columns_data,
                "isAllowFiltering" => false
              ];

              $output = table_crud_actions($data_for_crud);
              if (!$output['success']) {
                // $arr_return = ["success" => false, "msg" => $output['msg']];
                // return $arr_return;
                return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $output['msg']];
                exit();
              }
            }
          }
        }

        //delete
        $session->execute($session->prepare('DELETE FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=? AND id=?'), array('arguments' => array($law_tid, $law_version, $companycode, "1", $domains, $row_v['id'])));
        //insert
        $columns = [
          "companycode",
          "version",
          "law_tid",
          "law_version",
          "domains",
          "domains_text",
          "createdate",
          "effectivedate",
          "modifydate",
          "id",
          "policy_version",
          "status",
          "filleremail",
          "fillerrole"
        ];

        $columns_data = [
          $companycode,
          $row_v['version'],
          $law_tid,
          $law_version,
          $domains,
          $row_v['domains_text'],
          $row_v['createdate'],
          $row_v['effectivedate'],
          new \Cassandra\Timestamp(),
          $row_v['id'],
          $row_v['policy_version'],
          "0",
          $email,
          $role
        ];

        $data_for_crud = [
          "action" => "insert",
          "table_name" => "policyacfclientmapping_version",
          "columns" => $columns,
          "isCondition" => true,
          "condition_columns" => [],
          "columns_data" => $columns_data,
          "isAllowFiltering" => false
        ];

        $output = table_crud_actions($data_for_crud);
        if (!$output['success']) {
          // $arr_return = ["success" => false, "msg" => $output['msg']];
          // return $arr_return;
          return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $output['msg']];
          exit();
        }
      }

    }

    //Insert into domain deletion
    $columns = [
      "law_tid",
      "law_version",
      "companycode",
      "status",
      "domains",
      "createdate",
      "domains_text",
      "effectivedate",
      "policy_version",
      "filleremail",
      "fillerrole"
    ];

    if ($policy_active_version == 0) {
      $policy_active_version = 1;
    }

    $columns_data = [
      $law_tid,
      $law_version,
      $companycode,
      "1",
      $domains,
      new \Cassandra\Timestamp(),
      $domains_text,
      new \Cassandra\Timestamp(),
      $policy_active_version,
      $email,
      $role
    ];

    $data_for_crud = [
      "action" => "insert",
      "table_name" => "policyacfclientmapping_deletion",
      "columns" => $columns,
      "isCondition" => true,
      "condition_columns" => [],
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];

    $output = table_crud_actions($data_for_crud);
    if (!$output['success']) {
      // $arr_return = ["success" => false, "msg" => $output['msg']];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $output['msg']];
      exit();
    }

    $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => "success"];
    // $arr_return=["success"=>true,"msg"=>"success","data"=>""];

    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}


function save_policy_acf_data($data, $companycode, $email, $role)
{
  try {
    global $session;

    //validate acf_tid
    if (count($data) == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Request"];

      // $arr_return=["success"=>false,"msg"=>"Invalid Request","data"=>""];
      // return $arr_return;
      exit();
    }

    $domains_acf = $data['domains'];
    $domains_text = $data['domains_text'];
    $law_tid = $data['law_tid'];

    if ($domains_acf == '' || $law_tid == '' || $domains_text == '') {
      // $arr_return=["success"=>false,"msg"=>"Invalid Domain Data","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Domain Data"];
      exit();
    }

    $result_txn = $session->execute($session->prepare('SELECT law_version FROM compliance_framework_txn WHERE law_tid=? ALLOW FILTERING'), array('arguments' => array($law_tid)));
    if ($result_txn->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Law"];

      // $arr_return=["success"=>false,"msg"=>"Invalid Law","data"=>""];
      // return $arr_return;
      exit();
    }
    $law_version = $result_txn[0]['law_version'];
    $sequence = 9999;
    if ($domains_acf == 'add_new') {
      // Generate new domain
      $domains_acf = (string) new \Cassandra\Uuid();
    } else {
      // validate domain
      $result_domain = $session->execute($session->prepare('SELECT domains,sequence FROM policyacfmaster WHERE arrkacompref=? AND status=? AND version=? ALLOW FILTERING'), array('arguments' => array($domains_acf, "1", 1)));
      if ($result_domain->count() == 0) {
        $result_domain = $session->execute($session->prepare('SELECT version FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=?'), array(
          'arguments' => array(
            $law_tid,
            $law_version,
            $companycode,
            "1",
            $domains_acf
          )
        )
        );
        if ($result_domain->count() == 0) {
          return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Domain"];

          // $arr_return=["success"=>false,"msg"=>"Invalid Domain","data"=>""];
          // return $arr_return;
          exit();
        }
      } else {
        $sequence = $result_domain[0]['sequence'];
      }
    }


    $domains = $domains_text;

    //Get Policy Version
    $get_policy_version_status_for_law = get_policy_version_status($law_tid, $companycode, $email, $role);
    if (!$get_policy_version_status_for_law['success']) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $get_policy_version_status_for_law];

      // return $get_policy_version_status_for_law;
      exit();
    }

    $policy_vArr = $get_policy_version_status_for_law['data'];
    if (!$policy_vArr['edit_status']) {
      // $arr_return=["success"=>false,"msg"=>"Not in Edit Mode","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Not in Edit Mode"];
      exit();
    }

    $policy_edit_version = $policy_vArr['edit_version'];

    //Get domain version
    $result_version = $session->execute($session->prepare('SELECT version FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=?'), array(
      'arguments' => array(
        $law_tid,
        $law_version,
        $companycode,
        "1",
        $domains_acf
      )
    )
    );
    $version = $result_version->count() + 1;

    //Validate data
    $polData = $data['data'];
    foreach ($polData as $key_val => $value_val) {
      $subdomain = $value_val['subdomain'];
      $subdomain_acf = $value_val['subdomain_acf'];
      if ($subdomain_acf == 'add_new') {
        $subdomain_acf_uuid = new \Cassandra\Uuid();
        $subdomain_acf = (string) $subdomain_acf_uuid;
      }

      //Statement Array
      $stmt_arr = $value_val['statement'];
      foreach ($stmt_arr as $key => $value) {
        $arrkacompref = $value['arrkacompref'];
        $statement = $value['statement'];
        $status = "0";
        if ($value['selected']) {
          $status = "1";
        }
        $createdate = new \Cassandra\Timestamp();
        $id = new \Cassandra\Uuid();
        $columns = [
          "companycode",
          "status",
          "version",
          "law_tid",
          "law_version",
          "createdate",
          "effectivedate",
          "domains",
          "domains_acf",
          "statement",
          "subdomains",
          "subdomains_acf",
          "arrkacompref",
          "id",
          "policy_version",
          "sequence",
          "filleremail",
          "fillerrole"
        ];

        $columns_data = [
          $companycode,
          $status,
          $version,
          $law_tid,
          $law_version,
          $createdate,
          $createdate,
          $domains,
          $domains_acf,
          $statement,
          $subdomain,
          $subdomain_acf,
          $arrkacompref,
          (string) $id,
          (int) $policy_edit_version,
          $sequence,
          $email,
          $role
        ];

        $data_for_crud = [
          "action" => "insert",
          "table_name" => "policyacfclientmapping",
          "columns" => $columns,
          "isCondition" => true,
          "condition_columns" => [],
          "columns_data" => $columns_data,
          "isAllowFiltering" => false
        ];

        $output = table_crud_actions($data_for_crud);
        if (!$output['success']) {
          // $arr_return = ["success" => false, "msg" => $output['msg']];
          // return $arr_return;
          return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $output['msg']];

          exit();
        }
      }
    }

    //INSERT in version table
    $createdate = new \Cassandra\Timestamp();
    $id = new \Cassandra\Uuid();
    $columns = [
      "companycode",
      "version",
      "law_tid",
      "law_version",
      "domains",
      "domains_text",
      "createdate",
      "effectivedate",
      "id",
      "policy_version",
      "status",
      "filleremail",
      "fillerrole"
    ];

    $columns_data = [
      $companycode,
      $version,
      $law_tid,
      $law_version,
      $domains_acf,
      $domains,
      $createdate,
      $createdate,
      (string) $id,
      (int) $policy_edit_version,
      "1",
      $email,
      $role
    ];

    $data_for_crud = [
      "action" => "insert",
      "table_name" => "policyacfclientmapping_version",
      "columns" => $columns,
      "isCondition" => true,
      "condition_columns" => [],
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];

    $output = table_crud_actions($data_for_crud);
    if (!$output['success']) {
      // $arr_return = ["success" => false, "msg" => $output['msg']];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $output['msg']];
      exit();
    }

    // myLog_new($_SERVER['REMOTE_ADDR'],"AL002","task complete","1","get_latest_version_of_question_list","get_latest_version_of_question_list",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);

    // $arr_return=["success"=>true,"msg"=>"success","data"=>['domains_acf'=>$domains_acf]];
    $arr_return = ["code" => 200, "success" => true, "data" => ['domains_acf' => $domains_acf]];

    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function check_if_all_domains_are_saved($law_tid, $companycode, $email, $role)
{
  try {
    global $session;

    $get_policy_domains_for_law = get_policy_domains($law_tid, $companycode, $email, $role);
    if (!$get_policy_domains_for_law['success']) {
      // return $get_policy_domains_for_law; 
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $get_policy_domains_for_law];
      exit();
    }

    $domain_data = $get_policy_domains_for_law['data'];
    $domains_not_saved = [];
    $saved_domains = [];
    $saved = true;
    foreach ($domain_data as $value) {
      if (!$value['saved']) {
        $saved = false;
        $domains_not_saved[] = $value;
      } else {
        $saved_domains[] = $value;
      }
    }

    $arr_final = [
      "saved" => $saved,
      "saved_domains" => $saved_domains,
      "not_saved_domains" => $domains_not_saved
    ];

    $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function discard_policy_overall_changes($law_tid, $companycode, $email, $role)
{
  try {
    global $session;
    if ($law_tid == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid law_tid"];

      // $arr_return=["success"=>false,"msg"=>"Invalid Request","data"=>""];
      // return $arr_return;
      exit();
    }

    $result_txn = $session->execute($session->prepare('SELECT law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'), array('arguments' => array($law_tid, "1")));
    if ($result_txn->count() == 0) {
      // $arr_return=["success"=>false,"msg"=>"No Data Available","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "No Data Available"];
      exit();
    }
    $law_version = (string) $result_txn[0]['law_version'];

    //get policy edit version
    $get_policy_version_status_for_law = get_policy_version_status($law_tid, $companycode, $email, $role);
    if (!$get_policy_version_status_for_law['success']) {
      // return $get_policy_version_status_for_law;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $get_policy_version_status_for_law];
      exit();
    }

    $policy_vArr = $get_policy_version_status_for_law['data'];
    if (!$policy_vArr['edit_status']) {
      // $arr_return=["success"=>false,"msg"=>"Not in Edit Mode","data"=>""];
      // return $arr_return;
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Not in Edit Mode"];
      exit();
    }
    $policy_edit_version = $policy_vArr['edit_version'];

    $result_domain_version = $session->execute($session->prepare('SELECT version,policy_version,id,createdate,domains FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=?'), array('arguments' => array($law_tid, $law_version, $companycode, "1")));
    foreach ($result_domain_version as $row_domain_version) {
      if ($row_domain_version['policy_version'] == $policy_edit_version) {
        $discard_domain_policy_changes = discard_domain_policy_changes($law_tid, $row_domain_version['domains'], $companycode, $email, $role);
        if (!$discard_domain_policy_changes['success']) {
          // return $discard_domain_policy_changes; exit();
          return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => $discard_domain_policy_changes];
        }
      }
    }

    //Update policyacfoverallmapping_version table with status=0
    $result_version = $session->execute($session->prepare('SELECT version,id FROM policyacfoverallmapping_version WHERE law_tid=? AND law_version=? AND companycode=?'), array('arguments' => array($law_tid, $law_version, $companycode)));
    foreach ($result_version as $row_version) {
      if ($row_version['version'] == $policy_edit_version) {
        $session->execute($session->prepare('UPDATE policyacfoverallmapping_version SET status=?,modifydate=? WHERE law_tid=? AND law_version=? AND companycode=? AND id=?'), array('arguments' => array("discarded", new \Cassandra\Timestamp(), $law_tid, $law_version, $companycode, $row_version['id'])));
      }
    }

    // $arr_return=["success"=>true,"msg"=>"success","data"=>""];
    $arr_return = ["code" => 200, "success" => true, "message" => "success", "data" => ""];

    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

?>