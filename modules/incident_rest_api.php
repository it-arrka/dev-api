<?php

function GetIncidentHandler($funcCallType)
{
  try {

    switch ($funcCallType) {
      case "list":
        $page = 1;
        $limit = 10;
        $day = "ALL";
        if (isset($_GET["page"])) {
          $page = (int)$_GET["page"];
        }
        if (isset($_GET["limit"])) {
          $limit = (int)$_GET["limit"];
        }
        if (isset($_GET["day"])) {
          $day = $_GET["day"];
        }
        if (isset($GLOBALS['companycode'])) {
          $output = get_incident_list($GLOBALS['companycode'], $limit, $page, $day);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "subcategory":
        $type = "security";
        if (isset($_GET["type"])) {
          $type = $_GET["type"];
        }
        if (isset($GLOBALS['companycode'])) {
          $output = get_subcategory_list($type);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

        //To get incident analyse data
      case "analyse":
        if (isset($_GET['irid']) && isset($GLOBALS['companycode'])) {
          $output = get_incident_analyse_data($GLOBALS['companycode'], $_GET['irid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

        //To get incident resolve data
      case "resolve":
        if (isset($_GET['irid']) && isset($GLOBALS['companycode'])) {
          $output = get_incident_resolve_data($GLOBALS['companycode'], $_GET['irid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

        //To get specific incident investigate date
      case "investigate":
        if (isset($_GET['irid']) && isset($GLOBALS['companycode'])) {
          $output = get_incident_investigate_data($GLOBALS['companycode'], $_GET['irid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

        //To get specific incident Report
      case "report":
        if (isset($_GET['irid']) && isset($GLOBALS['companycode'])) {
          $output = get_incident_report($GLOBALS['companycode'], $_GET['irid']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

        //Write API
      case "initiate":
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
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = initiate_incident($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "save-incident-analyze-security":
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
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = save_incident_analyze_security($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "save-incident-analyze-privacy":
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
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = save_incident_analyze_privacy($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;


      case "save-incident-resolve-security":
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
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = save_incident_resolve_security($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "save-incident-resolve-privacy":
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
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = save_incident_resolve_privacy($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "save-incident-investigate-security":
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
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = save_incident_investigate_security($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "save-incident-investigate-privacy":
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
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = save_incident_investigate_privacy($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
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

//Save APIs

function calc_ia($tid, $companycode, $workflowname, $selection_arr)
{
  global $session;

  try {
    $selections = array();
    foreach ($selection_arr as $title => $selection) {
      if ($title == '') {
        $title = ' ';
      }
      if ($selection == '') {
        $selection = ' ';
      }
      if ($selection == 'no') {
        $query_no = $session->execute("SELECT ciamnocategory,ciamnocategorynumber FROM companyiamaster WHERE ciamcompanycode=? AND ciamworkflowname=? AND ciamiatitle=? AND transactionid=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, $title, $tid)));
        foreach ($query_no as $row_no) {
          $nocat = $row_no['ciamnocategory'];
          $nocat_number = (int)$row_no['ciamnocategorynumber'];
        }
        array_push($selections, $nocat_number);
      }
      if ($selection == 'yes') {
        $query_yes = $session->execute("SELECT ciamyescategory,ciamyescategorynumber FROM companyiamaster WHERE ciamcompanycode=? AND ciamworkflowname=? AND ciamiatitle=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, $title)));
        foreach ($query_yes as $row_yes) {
          $yescat = $row_yes['ciamyescategory'];
          $yescat_number = (int)$row_yes['ciamyescategorynumber'];
        }
        array_push($selections, $yescat_number);
      }
    }
    $max_cat = max($selections);
    $max_cat_str = (string)$max_cat;
    $query_final_ia = $session->execute("SELECT ccatcategoryname FROM companycategorymaster WHERE ccatcompanycode=? AND ccatworkflowname=? AND ccatcategorynumber=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, $max_cat_str)));
    $iascore = "";
    foreach ($query_final_ia as $row) {
      $iascore = $row['ccatcategoryname'];
    }
    $arr_return = ["code" => 200, "success" => true, "data" => $iascore];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function calc_class($tid, $companycode, $workflowname, $impactscore, $priority)
{
  global $session;
  try {
    if ($impactscore == '') {
      $impactscore = ' ';
    }
    if ($priority == '') {
      $priority = ' ';
    }
    //fetching category number for impactscore
    $query_impact_cat = $session->execute("SELECT ccatcategorynumber FROM companycategorymaster WHERE ccatcompanycode=? AND ccatworkflowname=? AND ccatcategoryname=? AND transactionid=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, $impactscore, $tid)));
    foreach ($query_impact_cat as $imp) {
      $impact_cat_no = (int)$imp['ccatcategorynumber'];
    }
    //fetching category number for priority
    $query_priority_cat = $session->execute("SELECT ccatcategorynumber FROM companycategorymaster WHERE ccatcompanycode=? AND ccatworkflowname=? AND ccatcategoryname=? AND transactionid=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, $priority, $tid)));
    foreach ($query_priority_cat as $prio) {
      $priority_cat_no = (int)$prio['ccatcategorynumber'];
    }

    //making comparisions
    if ($impact_cat_no == $priority_cat_no) {
      $query_get_class = $session->execute("SELECT ccatcategoryname FROM companycategorymaster WHERE ccatcompanycode=? AND ccatworkflowname=? AND ccatcategorynumber=? AND transactionid=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, (string)$impact_cat_no, $tid)));
    }
    if ($impact_cat_no > $priority_cat_no) {
      $query_get_class = $session->execute("SELECT ccatcategoryname FROM companycategorymaster WHERE ccatcompanycode=? AND ccatworkflowname=? AND ccatcategorynumber=? AND transactionid=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, (string)$impact_cat_no, $tid)));
    }
    if ($impact_cat_no < $priority_cat_no) {
      $query_get_class = $session->execute("SELECT ccatcategoryname FROM companycategorymaster WHERE ccatcompanycode=? AND ccatworkflowname=? AND ccatcategorynumber=? AND transactionid=? ALLOW FILTERING", array('arguments' => array($companycode, $workflowname, (string)$priority_cat_no, $tid)));
    }

    $classification = "";
    //fetching the categoryname (classification) as per the executed command
    foreach ($query_get_class as $class_name) {
      $classification = $class_name['ccatcategoryname'];
    }
    $arr_return = ["code" => 200, "success" => true, "data" => $classification];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

//save_incident_analyze_security
function save_incident_analyze_security($companycode, $email, $role, $data)
{
  try {
    global $session;

    $map = [
      "public_confidence_and_reputation" => "Public Confidence and Reputation",
      "loss_of_sensitive_information" => "(Loss of) Sensitive Information",
      "customer_service_delivery" => "Customer Service Delivery",
      "regulatory_client_compliance" => "Regulatory / Client Compliance"
    ];

    //Data validation
    $required_keys = [
      "irid", "iaprivrelation", "type_of_incident", "impact", "itornotit",
      "public_confidence_and_reputation", "loss_of_sensitive_information", "customer_service_delivery", "regulatory_client_compliance",
      "priority_to_respond", "additional_information", "assign_for_action_role", "assign_for_action_email"
    ];

    $required_keys_val = [
      "irid", "iaprivrelation", "type_of_incident", "impact", "itornotit",
      "public_confidence_and_reputation", "loss_of_sensitive_information", "customer_service_delivery", "regulatory_client_compliance",
      "priority_to_respond", "assign_for_action_role", "assign_for_action_email"
    ];

    //check if array is valid
    if (!checkKeysExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    //check value incoming
    if (!checkValueExist($data, $required_keys_val)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => implode(", ", $required_keys_val) . " value is mandatory"];
      exit();
    }

    //get tid and wid
    $result = $session->execute("SELECT * FROM incidentraise WHERE irid=?", array('arguments' => array(new \Cassandra\Uuid($data['irid']))));
    if ($result->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }

    $screen_status = (int)$result[0]['screen_status'];
    $screen_status_dpo = (int)$result[0]['screen_status_dpo'];
    $updated_screen_status = '2';
    $incidentno = $result[0]['irincidentno'];

    if ($screen_status > 2 || $screen_status_dpo > 2) {
      $updated_screen_status = '3';
    }

    //check role email combination
    if (!check_if_email_role_exist_in_company($companycode, escape_input($data['assign_for_action_email']), escape_input($data['assign_for_action_role']))) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid email-role combination!"];
      exit();
    }

    $tid = $result[0]['transactionid'];
    $wid = $result[0]['irworkflowid'];
    $data['priority_to_respond'] =  strtolower($data['priority_to_respond']);
    $custcode = get_custcode_from_email($email);
    //create selection_arr
    $selection_arr = [];

    foreach ($map as $key => $value) {
      $selection_arr[escape_input($value)] = escape_input(strtolower($data[$key]));
    }

    $query_get_iaid = $session->execute("SELECT * FROM incidentanalyse WHERE iacompanycode=? AND iaworkflowid=? AND status=? ALLOW FILTERING", array('arguments' => array($companycode, $wid, "1")));
    if ($query_get_iaid->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }

    $calc_ia_func = calc_ia($tid, $companycode, "incident", $selection_arr);
    if ($calc_ia_func['success']) {
      return $calc_ia_func;
      exit();
    }
    $calc_ia = $calc_ia_func['data'];


    $calc_class_func = calc_class($tid, $companycode, "incident", $calc_ia, escape_input($data['priority_to_respond']));
    if ($calc_class_func['success']) {
      return $calc_class_func;
      exit();
    }
    $calc_class = $calc_class_func['data'];

    //update incidentanalyse
    $session->execute("UPDATE incidentanalyse SET iapimpactscore=? WHERE iaid=?", array('arguments' => array($calc_ia, $query_get_iaid[0]['iaid'])));

    //insert into company ia response

    foreach ($selection_arr as $key => $value) {
      $query_insert = $session->prepare('INSERT INTO companyiaresponses(
      ciarid,createdate,effectivedate,
      ciarcompanycode,
      ciarcustcode,
      ciarcustemail,
      ciarresponse,
      ciartitle,
      ciarworkflowid,
      ciarworkflowname,
      status,
      transactionid,
      ciartype
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
      $session->execute($query_insert, array('arguments' => array(
        new \Cassandra\Uuid(), new \Cassandra\Timestamp(), new \Cassandra\Timestamp(),
        $companycode,
        $custcode,
        $email,
        ucwords($value),
        $key,
        $wid,
        "incident",
        '1',
        $tid,
        "privacy"
      )));
    }

    $result_cat = $session->execute($session->prepare("SELECT dwnworkflowcategory FROM defaultworkflowmaster WHERE dwmworkflowsubcategory=? AND dwmworkflowtype=? ALLOW FILTERING"), array('arguments' => array(escape_input($data['category_of_breach']), "security")));
    $iapbreachtype = "";
    if (isset($result_cat[0]['dwnworkflowcategory'])) {
      $iapbreachtype = $result_cat[0]['dwnworkflowcategory'];
    }

    $query_insert = $session->execute(
      $session->prepare("UPDATE incidentanalyse SET
    iarespondpriority=?,
    iabreachtype=?,
    iaanalyseextrainfo=?,
    iaclassification=?,
    iaassignforactionrole=?,
    iaassignforaction=?,
    form_status = ?,
    screen_status = ?,
    iaimpactscore = ?,
    iaprivrelation = ?
    WHERE iaid=?"),
      array('arguments' => array(
        escape_input($data['priority_to_respond']),
        $iapbreachtype,
        escape_input($data['additional_information']),
        $calc_class,
        escape_input($data['assign_for_action_role']),
        escape_input($data['assign_for_action_email']),
        "0",
        $updated_screen_status,
        $calc_ia,
        escape_input($data['iaprivrelation']),
        $query_get_iaid[0]['iaid']
      ))
    );

    notice_update_all($wid, $companycode, $email, $role, "IN01");

    $notice_link = "incident_resolve.php?tid=" . (string)$tid . "&wid=" . $wid;
    notice_write("IN03", $companycode, $email, $role, $notice_link, escape_input($data['assign_for_action_email']), escape_input($data['assign_for_action_role']), $incidentno, $wid);
    $arr_return = ["code" => 200, "success" => true, "data" => ["message" => "Success"]];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}


// save_incident_analyze_privacy
function save_incident_analyze_privacy($companycode, $email, $role, $data)
{
  try {
    global $session;

    $map = [
      "data_leakage" => "Data Leakage (Breach)",
      "unavailability_of_product" => "Unavailability of product / services",
      "unauthorized_access" => "Unauthorized Access",
      "loss_of_privacy" => "Loss of Privacy",
      "manipulation_of_data" => "Manipulation of Data",
      "unavailability_of_key_skills" => "Unavailability of Key Skills",
      "compliance" => "Compliance"
    ];

    //Data validation
    $required_keys = [
      "irid", "iaprivrelation",
      "data_leakage", "unavailability_of_product", "unauthorized_access", "loss_of_privacy",
      "manipulation_of_data", "unavailability_of_key_skills", "compliance",
      "priority_to_respond", "additional_information", "category_of_breach", "assign_for_action_role", "assign_for_action_email",
      "specific_reporting_required"
    ];

    $required_keys_val = [
      "irid", "iaprivrelation",
      "data_leakage", "unavailability_of_product", "unauthorized_access", "loss_of_privacy",
      "manipulation_of_data", "unavailability_of_key_skills", "compliance",
      "priority_to_respond", "category_of_breach", "assign_for_action_role", "assign_for_action_email",
      "specific_reporting_required"
    ];

    //check if array is valid
    if (!checkKeysExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    //check value incoming
    if (!checkValueExist($data, $required_keys_val)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => implode(", ", $required_keys_val) . " value is mandatory"];
      exit();
    }

    //get tid and wid
    $result = $session->execute("SELECT * FROM incidentraise WHERE irid=?", array('arguments' => array(new \Cassandra\Uuid($data['irid']))));
    if ($result->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }

    $screen_status = (int)$result[0]['screen_status'];
    $screen_status_dpo = (int)$result[0]['screen_status_dpo'];
    $updated_screen_status = '2';
    $incidentno = $result[0]['irincidentno'];

    if ($screen_status > 2 || $screen_status_dpo > 2) {
      $updated_screen_status = '3';
    }

    //check role email combination
    if (!check_if_email_role_exist_in_company($companycode, escape_input($data['assign_for_action_email']), escape_input($data['assign_for_action_role']))) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid email-role combination!"];
      exit();
    }

    $tid = $result[0]['transactionid'];
    $wid = $result[0]['irworkflowid'];
    $data['priority_to_respond'] =  strtolower($data['priority_to_respond']);
    $custcode = get_custcode_from_email($email);
    //create selection_arr
    $selection_arr = [];

    foreach ($map as $key => $value) {
      $selection_arr[escape_input($value)] = escape_input(strtolower($data[$key]));
    }

    $query_get_iaid = $session->execute("SELECT * FROM incidentanalyse WHERE iacompanycode=? AND iaworkflowid=? AND status=? ALLOW FILTERING", array('arguments' => array($companycode, $wid, "1")));
    if ($query_get_iaid->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }

    $calc_ia_func = calc_ia($tid, $companycode, "incident", $selection_arr);
    if ($calc_ia_func['success']) {
      return $calc_ia_func;
      exit();
    }
    $calc_ia = $calc_ia_func['data'];


    $calc_class_func = calc_class($tid, $companycode, "incident", $calc_ia, escape_input($data['priority_to_respond']));
    if ($calc_class_func['success']) {
      return $calc_class_func;
      exit();
    }
    $calc_class = $calc_class_func['data'];

    //insert into company ia response

    foreach ($selection_arr as $key => $value) {
      $query_insert = $session->prepare('INSERT INTO companyiaresponses(
      ciarid,createdate,effectivedate,
      ciarcompanycode,
      ciarcustcode,
      ciarcustemail,
      ciarresponse,
      ciartitle,
      ciarworkflowid,
      ciarworkflowname,
      status,
      transactionid,
      ciartype
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
      $session->execute($query_insert, array('arguments' => array(
        new \Cassandra\Uuid(), new \Cassandra\Timestamp(), new \Cassandra\Timestamp(),
        $companycode,
        $custcode,
        $email,
        ucwords($value),
        $key,
        $wid,
        "incident",
        '1',
        $tid,
        "privacy"
      )));
    }

    $result_cat = $session->execute($session->prepare("SELECT dwnworkflowcategory FROM defaultworkflowmaster WHERE dwmworkflowsubcategory=? AND dwmworkflowtype=? ALLOW FILTERING"), array('arguments' => array(escape_input($data['category_of_breach']), "privacy")));
    $iapbreachtype = "";
    if (isset($result_cat[0]['dwnworkflowcategory'])) {
      $iapbreachtype = $result_cat[0]['dwnworkflowcategory'];
    }

    $query_insert = $session->execute(
      $session->prepare("UPDATE incidentanalyse SET
    iaprespondpriority=?,
    iapbreachcategory=?,
    iapbreachtype=?,
    iapanalyseextrainfo=?,
    iapspecificreporting=?,
    iapspecificreportinglaw=?,
    iapclassification=?,
    iapassignforactionrole=?,
    iapassignforaction=?,
    form_status_dpo = ?,
    screen_status_dpo = ?,
    iapimpactscore=?,
    iaprivrelation = ?,
    WHERE iaid=?"),
      array('arguments' => array(
        escape_input($data['priority_to_respond']),
        escape_input($data['category_of_breach']),
        $iapbreachtype,
        escape_input($data['additional_information']),
        escape_input($data['specific_reporting_required']),
        "",
        $calc_class,
        escape_input($data['assign_for_action_role']),
        escape_input($data['assign_for_action_email']),
        "0",
        $updated_screen_status,
        $calc_ia,
        escape_input($data['iaprivrelation']),
        $query_get_iaid[0]['iaid']
      ))
    );

    notice_update_all($wid, $companycode, $email, $role, "IN02");

    $notice_link = "incident_resolve.php?tid=" . (string)$tid . "&wid=" . $wid;
    notice_write("IN04", $companycode, $email, $role, $notice_link, escape_input($data['assign_for_action_email']), escape_input($data['assign_for_action_role']), $incidentno, $wid);

    $arr_return = ["code" => 200, "success" => true, "data" => ["message" => "Success"]];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function save_incident_resolve_security($companycode, $email, $role, $data)
{
  try {
    global $session;

    //Data validation
    $required_keys = [
      "irid",
      "additional_information", "steps_taken", "date"
    ];


    //check if array is valid
    if (!checkKeysExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    if (!is_array($data['steps_taken'])) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "steps_taken needs to be an array"];
      exit();
    }


    //get tid and wid
    $result = $session->execute("SELECT * FROM incidentraise WHERE irid=?", array('arguments' => array(new \Cassandra\Uuid($data['irid']))));
    if ($result->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }


    $screen_status = (int)$result[0]['screen_status'];
    $screen_status_dpo = (int)$result[0]['screen_status_dpo'];
    $updated_screen_status = '2';
    $incidentno = $result[0]['irincidentno'];

    if ($screen_status > 2 || $screen_status_dpo > 2) {
      $updated_screen_status = '3';
    }

    // //check role email combination
    // if (!check_if_email_role_exist_in_company($companycode, escape_input($data['assign_for_action_email']), escape_input($data['assign_for_action_role']))) {
    //   return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid email-role combination!"];
    //   exit();
    // }

    $tid = $result[0]['transactionid'];
    $wid = $result[0]['irworkflowid'];
    $custcode = get_custcode_from_email($email);

    $ireid = "";
    $query_get_ireid = $session->execute($session->prepare("SELECT ireid FROM incidentresolve WHERE irecompanycode=? AND ireworkflowid=? ALLOW FILTERING"), array('arguments' => array($companycode, $wid)));
    foreach ($query_get_ireid as $row) {
      $ireid = (string)$row['ireid'];
    }

    if ($ireid == ""){
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid incident!"];
      exit();
    }

    $irecontainsteps = implode("|", $data['steps_taken']);
    $irecontainstepsdate = $data['date'];
    $additional_information = $data['additional_information'];
    $name = get_name_from_email($email);


    $query_insert = $session->execute(
      $session->prepare("UPDATE incidentresolve SET
      irecontainsteps=?,
      irecontainstepsdate=?,
      irecontainstepstime=?,
      irecustcode=?,
      irecustemail=?,
      irecustname=?,
      ireextrainfo=?,
      status=?
      WHERE ireid=?"),
      array('arguments' => array(
        $irecontainsteps,
        $irecontainstepsdate,
        "",
        $companycode,
        $email,
        $name,
        $additional_information,
        "1",
        new \Cassandra\Uuid($ireid)
      ))
    );

    $session->execute($session->prepare("UPDATE incidentraise SET form_status=? WHERE irid=?"), array('arguments' => array("2", new \Cassandra\Uuid($data['irid']))));

    if ($screen_status == '2') {
      //Notice create/update
      $notice_update = notice_update_all($wid, $companycode, $email, $role, "IN03");
      $result_analyse = $session->execute("SELECT iaassignforaction,iaassignforactionrole FROM incidentanalyse WHERE iacompanycode=? AND iaworkflowid=? AND status=? ALLOW FILTERING", array('arguments' => array($companycode, $wid, "1")));
      foreach ($result_analyse as $row_analyse) {
        //Create notification noDPO
        $notice_link = "incident_investigate.php?tid=" . (string)$tid . "&wid=" . $wid;
        notice_write("IN05", $companycode, $email, $role, $notice_link, $row_analyse['iaassignforaction'], $row_analyse['iaassignforactionrole'], $incidentno, $wid);
      }

      if ($screen_status_dpo == '2') {
        $session->execute($session->prepare("UPDATE incidentraise SET form_status=?,form_status_dpo=?,screen_status=?,screen_status_dpo=? WHERE irid=?"), array('arguments' => array("0", "0", "3", "3", new \Cassandra\Uuid($data['irid']))));
      } else {
        $session->execute($session->prepare("UPDATE incidentraise SET form_status=?,screen_status=? WHERE irid=?"), array('arguments' => array("0", "3", new \Cassandra\Uuid($data['irid']))));
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => ["message" => "Success"]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function save_incident_resolve_privacy($companycode, $email, $role, $data)
{
  try {
    global $session;

    //Data validation
    $required_keys = [
      "irid",
      "additional_information", "steps_taken", "date"
    ];


    //check if array is valid
    if (!checkKeysExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    if (!is_array($data['steps_taken'])) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "steps_taken needs to be an array"];
      exit();
    }


    //get tid and wid
    $result = $session->execute("SELECT * FROM incidentraise WHERE irid=?", array('arguments' => array(new \Cassandra\Uuid($data['irid']))));
    if ($result->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }


    $screen_status = (int)$result[0]['screen_status'];
    $screen_status_dpo = (int)$result[0]['screen_status_dpo'];
    $updated_screen_status = '2';
    $incidentno = $result[0]['irincidentno'];

    if ($screen_status > 2 || $screen_status_dpo > 2) {
      $updated_screen_status = '3';
    }

    // //check role email combination
    // if (!check_if_email_role_exist_in_company($companycode, escape_input($data['assign_for_action_email']), escape_input($data['assign_for_action_role']))) {
    //   return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid email-role combination!"];
    //   exit();
    // }

    $tid = $result[0]['transactionid'];
    $wid = $result[0]['irworkflowid'];
    $custcode = get_custcode_from_email($email);

    $ireid = "";
    $query_get_ireid = $session->execute($session->prepare("SELECT ireid FROM incidentresolve WHERE irecompanycode=? AND ireworkflowid=? ALLOW FILTERING"), array('arguments' => array($companycode, $wid)));
    foreach ($query_get_ireid as $row) {
      $ireid = (string)$row['ireid'];
    }

    if ($ireid == ""){
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "invalid incident!"];
      exit();
    }

    $irecontainsteps = implode("|", $data['steps_taken']);
    $irecontainstepsdate = $data['date'];
    $additional_information = $data['additional_information'];
    $name = get_name_from_email($email);


    $query_insert = $session->execute(
      $session->prepare("UPDATE incidentresolve SET
      irecontainsteps=?,
      irecontainstepsdate=?,
      irecontainstepstime=?,
      irecustcode=?,
      irecustemail=?,
      irecustname=?,
      ireextrainfo=?,
      status=?
      WHERE ireid=?"),
      array('arguments' => array(
        $irecontainsteps,
        $irecontainstepsdate,
        "",
        $companycode,
        $email,
        $name,
        $additional_information,
        "1",
        new \Cassandra\Uuid($ireid)
      ))
    );

    $session->execute($session->prepare("UPDATE incidentraise SET form_status=? WHERE irid=?"), array('arguments' => array("2", new \Cassandra\Uuid($data['irid']))));

    if ($screen_status_dpo == '2') {
      //Notice create/update
      $notice_update = notice_update_all($wid, $companycode, $email, $role, "IN04");
      $result_analyse = $session->execute("SELECT iapassignforaction,iapassignforactionrole FROM incidentanalyse WHERE iacompanycode=? AND iaworkflowid=? AND status=? ALLOW FILTERING", array('arguments' => array($companycode, $wid, "1")));
      foreach ($result_analyse as $row_analyse) {
        //Create notification DPO
        $notice_link = "incident_investigate.php?tid=" . (string)$tid . "&wid=" . $wid;
        $notice_output = notice_write("IN06", $companycode, $email, $role, $notice_link, $row_analyse['iapassignforaction'], $row_analyse['iapassignforactionrole'], $incidentno, $wid);
      }

      if ($screen_status == '2') {
        $session->execute($session->prepare("UPDATE incidentraise SET form_status=?,form_status_dpo=?,screen_status=?,screen_status_dpo=? WHERE irid=?"), array('arguments' => array("0", "0", "3", "3", new \Cassandra\Uuid($data['irid']))));
      } else {
        $session->execute($session->prepare("UPDATE incidentraise SET form_status_dpo=?,screen_status_dpo=? WHERE irid=?"), array('arguments' => array("0", "3", new \Cassandra\Uuid($data['irid']))));
      }
    }
    $arr_return = ["code" => 200, "success" => true, "data" => ["message" => "Success"]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function save_incident_investigate_security($companycode, $email, $role, $data)
{
  try {
    global $session;

    //Data validation
    $required_keys = [
      "irid",
      "name_of_person", "was_vul_indetified", "root_cause_analysis", "learning_from_the_incident", "need_for_evidence_preservation"
    ];


    //check if array is valid
    if (!checkKeysExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    //get tid and wid
    $result = $session->execute("SELECT * FROM incidentraise WHERE irid=?", array('arguments' => array(new \Cassandra\Uuid($data['irid']))));
    if ($result->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }


    $screen_status = (int)$result[0]['screen_status'];
    $screen_status_dpo = (int)$result[0]['screen_status_dpo'];
    $updated_screen_status = '2';
    $incidentno = $result[0]['irincidentno'];

    if ($screen_status > 2 || $screen_status_dpo > 2) {
      $updated_screen_status = '3';
    }


    $tid = $result[0]['transactionid'];
    $wid = $result[0]['irworkflowid'];
    $custcode = get_custcode_from_email($email);
    $name = get_name_from_email($email);

    $iiid = " ";
    $query_get_iiid = $session->execute($session->prepare("SELECT iiid FROM incidentinvestigate WHERE iicompanycode=? AND iiworkflowid=? ALLOW FILTERING"), array('arguments' => array($companycode, $wid)));
    foreach ($query_get_iiid as $row) {
      $iiid = (string)$row['iiid'];
    }

    $query_insert = $session->execute($session->prepare("UPDATE incidentinvestigate SET
    createdate=?,effectivedate=?,iicustcode=?,iicustemail=?,iicustname=?,
    iievidencepreserve=?,iiinvestgigatorname=?,iipotentialdamage=?,iirca=?,iivulidentified=?,
    status=?,iimitigationid=?,transactionid=? WHERE iiid=?"), array('arguments' => array(
      new \Cassandra\Timestamp(), new \Cassandra\Timestamp(), $custcode, $email, $name,
      escape_input($data['need_for_evidence_preservation']), escape_input($data['name_of_person']), 
      escape_input($data['learning_from_the_incident']), escape_input($data['root_cause_analysis']), escape_input($data['was_vul_indetified']),
      "1", "", $tid, new \Cassandra\Uuid($iiid)
    )));

    notice_update_all($wid,$companycode,$email,$role,"IN05");
    $session->execute($session->prepare("UPDATE incidentraise SET form_status=?,screen_status=? WHERE irid=?"),array('arguments'=>array("0","4",new \Cassandra\Uuid($data['irid']))));

    $arr_return = ["code" => 200, "success" => true, "data" => ["message" => "Success"]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function save_incident_investigate_privacy($companycode, $email, $role, $data)
{
  try {
    global $session;

    //Data validation
    $required_keys = [
      "irid",
      "name_of_person", "was_vul_indetified", "root_cause_analysis", "learning_from_the_incident", "need_for_evidence_preservation"
    ];


    //check if array is valid
    if (!checkKeysExist($data, $required_keys)) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }


    //get tid and wid
    $result = $session->execute("SELECT * FROM incidentraise WHERE irid=?", array('arguments' => array(new \Cassandra\Uuid($data['irid']))));
    if ($result->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Incident"];
      exit();
    }


    $screen_status = (int)$result[0]['screen_status'];
    $screen_status_dpo = (int)$result[0]['screen_status_dpo'];
    $updated_screen_status = '2';
    $incidentno = $result[0]['irincidentno'];

    if ($screen_status > 2 || $screen_status_dpo > 2) {
      $updated_screen_status = '3';
    }

    $tid = $result[0]['transactionid'];
    $wid = $result[0]['irworkflowid'];
    $custcode = get_custcode_from_email($email);
    $name = get_name_from_email($email);

    $iiid = " ";
    $query_get_iiid = $session->execute($session->prepare("SELECT iiid FROM incidentinvestigate WHERE iicompanycode=? AND iiworkflowid=? ALLOW FILTERING"), array('arguments' => array($companycode, $wid)));
    foreach ($query_get_iiid as $row) {
      $iiid = (string)$row['iiid'];
    }

    $query_insert = $session->execute($session->prepare("UPDATE incidentinvestigate SET
    createdate=?,effectivedate=?,iicustcode=?,iicustemail=?,iicustname=?,
    iievidencepreserve=?,iiinvestgigatorname=?,iipotentialdamage=?,iirca=?,iivulidentified=?,
    status=?,iimitigationid=?,transactionid=? WHERE iiid=?"), array('arguments' => array(
      new \Cassandra\Timestamp(), new \Cassandra\Timestamp(), $custcode, $email, $name,
      escape_input($data['need_for_evidence_preservation']), escape_input($data['name_of_person']), 
      escape_input($data['learning_from_the_incident']), escape_input($data['root_cause_analysis']), escape_input($data['was_vul_indetified']),
      "1", "", $tid, new \Cassandra\Uuid($iiid)
    )));

    $session->execute($session->prepare("UPDATE incidentraise SET form_status=? WHERE irid=?"),array('arguments'=>array("2",new \Cassandra\Uuid($data['irid']))));
    notice_update_all($wid,$companycode,$email,$role,"IN06");
    $session->execute($session->prepare("UPDATE incidentraise SET form_status_dpo=?,screen_status_dpo=? WHERE irid=?"),array('arguments'=>array("0","4",new \Cassandra\Uuid($data['irid']))));

    $arr_return = ["code" => 200, "success" => true, "data" => ["message" => "Success"]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

/**
 * @param string $screen_status // Analyze/Resolve/Investigate/Solved
 * @param string $type //dpo/ciso 
 */
function report_status_by_screen_status($screen_status, $type)
{
  $report_status = '';
  switch ($screen_status) {
    case '1':
      $report_status = 'Analyze';
      break;
    case '2':
      $report_status = 'Resolve';
      break;
    case '3':
      $report_status = 'Investigate';
      break;
    case '4':
      $report_status = 'Solved';
      break;
  }
  if ($report_status != '') {
    if ($type == 'dpo') {
      $report_status = $report_status . " (Privacy)";
    }
  }
  return $report_status;
}

/**
 * @param string $companycode 
 * @param string $limit //limit of data in each page
 * @param string $page //number of page
 * @param string $day //last 7 day or 30 days etc.
 */
function get_incident_list($companycode, $limit, $page, $day)
{
  try {
    global $session;

    if ($companycode == "") {
      //Bad Request Error
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    //timestamp
    $timestamp = 0;
    if (strtoupper($day) != "ALL") {
      $last_day = (int)$day;
      if ($last_day < 1) {
        $last_day = 1;
      }
      $timestamp = strtotime("-" . $last_day . " days");
    }

    //validate limit and page
    if ($limit < 1) {
      $limit = 1;
    }
    if ($page < 1) {
      $page = 1;
    }
    $page = $page - 1;
    $arr = [];
    $arr_txn = [];
    $total_incident = 0;


    $result_txn = $session->execute($session->prepare("SELECT irid,createdate FROM incidentraise WHERE ircompanycode=? AND status=? ALLOW FILTERING"), array('arguments' => array($companycode, "1")));

    foreach ($result_txn as $row_txn) {
      $modifydate_str = (string)$row_txn['createdate'];
      $modifydate_int = (int)$modifydate_str / 1000;

      if ($modifydate_int >= $timestamp) {
        $total_incident++;
        $arr_txn[(string)$row_txn['irid']] = $modifydate_int;
      }
    }

    arsort($arr_txn);
    $arr_final_txn = [];
    //divide array and find specific chunks
    $array_chunk = array_chunk($arr_txn, $limit, true);
    $total_index = count($array_chunk);
    if (isset($array_chunk[$page])) {
      $arr_final_txn = $array_chunk[$page];
    }

    foreach ($arr_final_txn as $key_id => $value) {
      $result = $session->execute($session->prepare("SELECT createdate,irincidentno,ircustemail,irrole,irincidentcategory,irincisubcategory,iritornonit,irprivrelation,irworkflowid,screen_status,screen_status_dpo,transactionid FROM incidentraise WHERE irid=?"), array('arguments' => array(new \Cassandra\Uuid($key_id))));
      foreach ($result as $row) {

        //Get analyse data from incident raise data
        $result_an = $session->execute($session->prepare("SELECT iaitornonit,iaincidentcategory,iaincidentsubcategory,iaprivrelation FROM incidentanalyse WHERE iaworkflowid=? ALLOW FILTERING"), array('arguments' => array($row['irworkflowid'])));
        foreach ($result_an as $row_an) {
          if ($row_an['iaitornonit'] == '') {
          } else {
            $row['iritornonit'] = $row_an['iaitornonit'];
          }
          if ($row_an['iaincidentcategory'] == '') {
          } else {
            $row['irincidentcategory'] = $row_an['iaincidentcategory'];
          }
          if ($row_an['iaincidentsubcategory'] == '') {
          } else {
            $row['irincisubcategory'] = $row_an['iaincidentsubcategory'];
          }
          if ($row_an['iaprivrelation'] == '') {
          } else {
            $row['irprivrelation'] = $row_an['iaprivrelation'];
          }
        }

        $row['ircustname'] = get_name_from_email($row['ircustemail']);

        //Rest of the data
        $row['screen_status'] = report_status_by_screen_status($row['screen_status'], 'security');
        $row['screen_status_dpo'] = report_status_by_screen_status($row['screen_status_dpo'], 'dpo');
        $row['id'] = $key_id;
        $row['createdate'] = date("d-m-Y", $value);

        $action_status_act = [];
        $action_status_act_dpo = [];

        //get comp_score
        $comp_score = "";
        $comp_score_get = comp_score_for_secific_incident(['workflowid' => $row['irworkflowid']], $companycode);
        if ($comp_score_get['success']) {
          $comp_score = $comp_score_get['data']['comp_score'];
        }
        $row['comp_score'] = $comp_score;

        $row['action_txn_status'] = $action_status_act;
        $row['action_txn_status_dpo'] = $action_status_act_dpo;
        $arr[] = $row;
      }
    }

    $final_data = [
      "limit" => $limit,
      "day" => $day,
      "page" => $page + 1,
      "pagination" => $total_index,
      "total_incident" => $total_incident,
      "incidents" => $arr
    ];

    $arr_return = ["code" => 200, "success" => true, "data" => $final_data];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_raise_data($companycode, $irid)
{
  try {
    global $session;

    if ($irid == "" || $companycode == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }


    $arr = [];
    $result = $session->execute($session->prepare("SELECT 
        createdate,form_status,form_status_dpo,ircompanycode,ircustemail,irdectlocation,irdetectiondate,irdetectiontime,irextrainfo,irhow,irimpact,irincidentcategory,irincidentno,irincidentnofixed,irincisubcategory,iritornonit,irname,irphone,irprivrelation,irreportdate,irreportlocation,irreporttime,irrole,irworkflowid,modifydate,screen_status,screen_status_dpo,status,transactionid
    FROM incidentraise WHERE irid=?"), array('arguments' => array(new \Cassandra\Uuid($irid))));

    foreach ($result as $row) {
      if ($row['ircompanycode'] == $companycode && $row['status'] == '1') {
        $row['screen_status'] = report_status_by_screen_status($row['screen_status'], 'security');
        $row['screen_status_dpo'] = report_status_by_screen_status($row['screen_status_dpo'], 'dpo');

        $createdate_str = (string)$row['createdate'];
        if ($createdate_str == '') {
          $row['createdate'] = "";
        } else {
          $row['createdate'] = date("d-m-Y", (int)$createdate_str / 1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if ($modifydate_str == '') {
          $row['modifydate'] = "";
        } else {
          $row['modifydate'] = date("d-m-Y", (int)$modifydate_str / 1000);
        }

        $comp_score = "";
        $comp_score_get = comp_score_for_secific_incident(['workflowid' => $row['irworkflowid']], $companycode);
        if ($comp_score_get['success']) {
          $comp_score = $comp_score_get['data']['comp_score'];
        }
        $row['ircustname'] = get_name_from_email($row['ircustemail']);
        $row['comp_score'] = $comp_score;

        $arr = $row;
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_analyse_data($companycode, $irid)
{
  try {
    global $session;

    if ($irid == "" || $companycode == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    $arr = [];
    $result_txn = $session->execute($session->prepare("SELECT irworkflowid FROM incidentraise WHERE irid=?"), array('arguments' => array(new \Cassandra\Uuid($irid))));
    foreach ($result_txn as $row_txn) {
      $result = $session->execute("SELECT * FROM incidentanalyse WHERE iacompanycode=? AND status=? AND iaworkflowid=? ALLOW FILTERING", array('arguments' => array($companycode, "1", $row_txn['irworkflowid'])));
      foreach ($result as $row) {
        $createdate_str = (string)$row['createdate'];
        if ($createdate_str == '') {
          $row['createdate'] = "";
        } else {
          $row['createdate'] = date("d-m-Y", (int)$createdate_str / 1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if ($modifydate_str == '') {
          $row['modifydate'] = "";
        } else {
          $row['modifydate'] = date("d-m-Y", (int)$modifydate_str / 1000);
        }

        $row['iacustname'] = get_name_from_email($row['iacustemail']);
        unset($row['iaid']);
        unset($row['effectivedate']);
        $arr = $row;
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}


/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_resolve_data($companycode, $irid)
{
  try {
    global $session;

    if ($irid == "" || $companycode == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    $arr = [];
    $result_txn = $session->execute($session->prepare("SELECT irworkflowid FROM incidentraise WHERE irid=?"), array('arguments' => array(new \Cassandra\Uuid($irid))));
    foreach ($result_txn as $row_txn) {
      $result = $session->execute("SELECT * FROM incidentresolve WHERE irecompanycode=? AND status=? AND ireworkflowid=? ALLOW FILTERING", array('arguments' => array($companycode, "1", $row_txn['irworkflowid'])));
      foreach ($result as $row) {
        $createdate_str = (string)$row['createdate'];
        if ($createdate_str == '') {
          $row['createdate'] = "";
        } else {
          $row['createdate'] = date("d-m-Y", (int)$createdate_str / 1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if ($modifydate_str == '') {
          $row['modifydate'] = "";
        } else {
          $row['modifydate'] = date("d-m-Y", (int)$modifydate_str / 1000);
        }

        $row['irecustname'] = get_name_from_email($row['irecustemail']);
        unset($row['ireid']);
        unset($row['effectivedate']);
        $arr = $row;
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_investigate_data($companycode, $irid)
{
  try {
    global $session;

    if ($irid == "" || $companycode == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    $arr = [];
    $result_txn = $session->execute($session->prepare("SELECT irworkflowid FROM incidentraise WHERE irid=?"), array('arguments' => array(new \Cassandra\Uuid($irid))));
    foreach ($result_txn as $row_txn) {
      $result = $session->execute("SELECT * FROM incidentinvestigate WHERE iicompanycode=? AND status=? AND iiworkflowid=? ALLOW FILTERING", array('arguments' => array($companycode, "1", $row_txn['irworkflowid'])));
      foreach ($result as $row) {
        $createdate_str = (string)$row['createdate'];
        if ($createdate_str == '') {
          $row['createdate'] = "";
        } else {
          $row['createdate'] = date("d-m-Y", (int)$createdate_str / 1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if ($modifydate_str == '') {
          $row['modifydate'] = "";
        } else {
          $row['modifydate'] = date("d-m-Y", (int)$modifydate_str / 1000);
        }

        $row['iicustname'] = get_name_from_email($row['iicustemail']);
        unset($row['iiid']);
        unset($row['effectivedate']);
        $arr = $row;
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_incident_report($companycode, $irid)
{
  try {
    global $session;

    if ($irid == "" || $companycode == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
      exit();
    }

    $raise_data = [];
    $analyse_data = [];
    $resolve_data = [];
    $investigating_data = [];

    $raise_data_func = get_incident_raise_data($companycode, $irid);
    if ($raise_data_func['success']) {
      $raise_data = $raise_data_func['data'];
    }

    $analyse_data_func = get_incident_analyse_data($companycode, $irid);
    if ($analyse_data_func['success']) {
      $analyse_data = $analyse_data_func['data'];
    }

    $resolve_data_func = get_incident_resolve_data($companycode, $irid);
    if ($resolve_data_func['success']) {
      $resolve_data = $resolve_data_func['data'];
    }

    $investigate_data_func = get_incident_investigate_data($companycode, $irid);
    if ($investigate_data_func['success']) {
      $investigate_data = $investigate_data_func['data'];
    }

    $arr = [
      "raise_data" => $raise_data,
      "analyse_data" => $analyse_data,
      "resolve_data" => $resolve_data,
      "investigate_data" => $investigate_data
    ];


    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}


function get_subcategory_list($type = "security")
{
  try {
    global $session;
    $arr = array();
    array_push($arr, 'Select Category');
    $res_subc = $session->execute("SELECT dwmworkflowsubcategory FROM defaultworkflowmaster WHERE dwmworkflowtype=? ALLOW FILTERING", array('arguments' => array($type)));
    foreach ($res_subc as $row_subc) {
      array_push($arr, $row_subc['dwmworkflowsubcategory']);
    }
    sort($arr);
    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

//initiate_incident
function initiate_incident($companycode, $email, $role, $data)
{
  try {
    global $session;
    //validate data
    $reported_date = date("d-m-Y");
    $reported_time = date("H:i:s");
    $detected_date = date("d-m-Y");
    $detected_time = date("H:i:s");

    $incDetectedLocation = escape_input($data['incDetectedLocation']);
    $incReportedLocation = escape_input($data['incReportedLocation']);
    $incAdditionalInfo = escape_input($data['incAdditionalInfo']);
    $incHow = escape_input($data['incHow']);
    $incImpact = escape_input($data['incImpact']);
    $incName = escape_input($data['incName']);
    $incPhone = escape_input($data['incPhone']);

    if ($incDetectedLocation == "addNew") {
      $incDetectedLocation = escape_input($data['incDetectedLocationNew']);
    }

    if ($incReportedLocation == "addNew") {
      $incReportedLocation = escape_input($data['incReportedLocationNew']);
    }

    if ($incDetectedLocation == "" || $incReportedLocation == "" || $incHow == "" || $incImpact == "" || $incName == "" || $incPhone == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Fill all mandatory fields"];
      exit();
    }


    $config_tid  = get_active_config_txn_id($companycode, "incident");
    if ($config_tid == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid configuration"];
      exit();
    }

    $subcategory = escape_input($data['incSubcategory']);
    $incidentcategory = "";
    if ($subcategory == "") {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid subcategory"];
      exit();
    }

    $result_cat = $session->execute($session->prepare("SELECT dwnworkflowcategory FROM defaultworkflowmaster WHERE dwmworkflowsubcategory=? AND dwmworkflowtype=? ALLOW FILTERING"), array('arguments' => array($subcategory, "security")));
    if ($result_cat->count() == 0) {
      return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid subcategory"];
      exit();
    }
    $incidentcategory = $result_cat[0]['dwnworkflowcategory'];


    $result_inc = $session->execute($session->prepare("SELECT count(*) FROM incidentraise WHERE ircompanycode=? ALLOW FILTERING"), array('arguments' => array($companycode)));
    $incidentno = "INC0000" . ($result_inc[0]['count'] + 1);

    $form_status_dpo = '';
    $irprivrelation = "No";
    if ($data['incPrivacy'] == true) {
      $form_status_dpo = '1';
      $irprivrelation = "Yes";
    }

    $iritornonit = "Non-IT";
    if ($data['incIt'] == true) {
      $iritornonit = "IT";
    }


    //get custcode
    $custcode = get_custcode_from_email($email);

    $workflowid = (string)new \Cassandra\Uuid();
    $query_insert = $session->prepare('INSERT INTO incidentraise(
      irid,createdate,effectivedate,irworkflowid,ircompanycode,ircustcode,ircustemail,
      irdectlocation,irdetectiondate,irdetectiontime,irextrainfo,irhow,irimpact,irincidentcategory,
      irincisubcategory,iritornonit,irname,irphone,irprivrelation,
      irreportdate,irreporttime,irreportlocation,status,transactionid,form_status,screen_status,irincidentno,irrole,
      form_status_dpo,screen_status_dpo,irincidentnofixed
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $session->execute($query_insert, array('arguments' => array(
      new \Cassandra\Uuid(), new \Cassandra\Timestamp(), new \Cassandra\Timestamp(), $workflowid, $companycode, $custcode, $email,
      $incDetectedLocation, $detected_date, $detected_time, $incAdditionalInfo, $incHow, $incImpact, $incidentcategory,
      $subcategory, $iritornonit, $incName, $incPhone, $irprivrelation,
      $reported_date, $reported_time, $incReportedLocation, "1", $config_tid, "1", "1", $incidentno, $role,
      $form_status_dpo, $form_status_dpo, (string)new \Cassandra\Uuid()
    )));

    // incidentanalyse workflowid insertion
    $query_insert_ia = $session->prepare('INSERT INTO incidentanalyse(iaid,iaworkflowid,iacompanycode,transactionid) VALUES(?,?,?,?)');
    $session->execute($query_insert_ia, array('arguments' => array(new \Cassandra\Uuid(), $workflowid, $companycode, $config_tid)));

    // incidentresolve workflowid insertion
    $query_insert_ire = $session->prepare('INSERT INTO incidentresolve(ireid,ireworkflowid,irecompanycode,transactionid)VALUES(?,?,?,?)');
    $session->execute($query_insert_ire, array('arguments' => array(new \Cassandra\Uuid(), $workflowid, $companycode, $config_tid)));

    // incidentinvestigate workflowid insertion
    $query_insert_ii = $session->prepare('INSERT INTO incidentinvestigate(iiid,iiworkflowid,iicompanycode,transactionid) VALUES(?,?,?,?)');
    $session->execute($query_insert_ii, array('arguments' => array(new \Cassandra\Uuid(), $workflowid, $companycode, $config_tid)));

    // incidentreport workflowid insertion
    $query_insert_irp = $session->prepare('INSERT INTO incidentreport(irpid,irpworkflowid,irpcompanycode,transactionid) VALUES(?,?,?,?)');
    $session->execute($query_insert_irp, array('arguments' => array(new \Cassandra\Uuid(), $workflowid, $companycode, $config_tid)));
    // $session->execute($session->prepare("UPDATE incidentraise SET form_status=? WHERE irid=?"),array('arguments'=>array("1",new \Cassandra\Uuid($_POST['wid_incident_1']))));

    //Create notification noDPO
    $itNonIt = $iritornonit;
    if ($itNonIt == 'IT') {
      $itNonIt = 'IT-Security';
    }
    $result_analyse = $session->execute("SELECT ccmemail,ccmrole FROM companyconfigmaster WHERE ccmcompanycode=? AND transactionid=? AND status=? AND ccmteamcategory=? AND ccmteamtitle=? ALLOW FILTERING", array('arguments' => array($companycode, $config_tid, "1", "FPC", $itNonIt)));
    if ($result_analyse->count() > 0) {
      foreach ($result_analyse as $row_analyse) {
        $notice_link = "incident_analyze.php?tid=" . (string)$config_tid . "&wid=" . $workflowid;
        notice_write("IN01", $companycode, $email, $role, $notice_link, $row_analyse['ccmemail'], $row_analyse['ccmrole'], $incidentno, $workflowid);
      }
    } else {
      $notice_link = "incident_analyze.php?tid=" . (string)$config_tid . "&wid=" . $workflowid;
      notice_write("IN01", $companycode, $email, $role, $notice_link, "", "", $incidentno, $workflowid);
    }


    //If privacy is yes DPO
    $priv_yes_no = $irprivrelation;
    if ($priv_yes_no == 'Yes') {
      $result_priv = $session->execute("SELECT ccmemail,ccmrole FROM companyconfigmaster WHERE ccmcompanycode=? AND transactionid=? AND status=? AND ccmteamcategory=? AND ccmteamtitle=? ALLOW FILTERING", array('arguments' => array($companycode, $config_tid, "1", "FPC", "DPO")));

      if ($result_priv->count() > 0) {
        foreach ($result_priv as $row_priv) {
          $notice_link = "incident_analyze_dpo.php?tid=" . (string)$config_tid . "&wid=" . $workflowid;
          notice_write("IN02", $companycode, $email, $role, $notice_link, $row_priv['ccmemail'], $row_priv['ccmrole'], $incidentno, $workflowid);
        }
      } else {
        $notice_link = "incident_analyze_dpo.php?tid=" . (string)$config_tid . "&wid=" . $workflowid;
        notice_write("IN02", $companycode, $email, $role, $notice_link, "", "", $incidentno, $workflowid);
      }
    }


    $arr_return = ["code" => 200, "success" => true, "data" => ['incidentno' => $incidentno, "message" => "Incident initiated successfully"]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}
