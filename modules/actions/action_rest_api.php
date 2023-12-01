<?php 

function GetActionHandler($funcCallType){
  try{

    switch($funcCallType){
      case "save-management-response":
        $jsonString = file_get_contents('php://input');
        if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
        $json = json_decode($jsonString,true);
        if(!is_array($json)){
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
        }

        if(isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])){
          $output = save_management_response_by_module($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "save-temp-define-action":
        $jsonString = file_get_contents('php://input');
        if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
        $json = json_decode($jsonString,true);
        if(!is_array($json)){
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
        }

        if(isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])){
          $output = temp_save_define_actions($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "save-define-action":
        $jsonString = file_get_contents('php://input');
        if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
        $json = json_decode($jsonString,true);
        if(!is_array($json)){
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
        }

        if(isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])){
          $output = save_define_actions($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

        default:
          catchErrorHandler(400,[ "message"=>E_INV_REQ, "error"=>"" ]);
          break;
    }
  }catch(Exception $e){
    catchErrorHandler($output['code'], [ "message"=>"", "error"=>$e->getMessage() ]);
  }
}

/**
 * @param string $module
 * @param string $refid
 * @param string $txnid
 */
function validate_txnid_refid_for_action($module, $refid, $txnid){
  $status = false;
  switch($module){
    case "incident":

      break;
    case "internalAudit":
    
      break;
    case "maturity":
  
      break;
    case "risk":

      break;
    case "vendor":

      break;
    case "change":
    
      break;
    case "client":
  
      break;
    case "tra":

      break;
    case "dpia":
  
      break;
    case "dsrr":
  
      break;
    case "scheduler":

      break;
    default:
      $status = false;
      break;
  }
  return $status;
}

/**
 * @param array $actionsArr
 */
function validate_temp_actions_input_data($companycode, $transactionid, $refid, $module, $actionsArr){

  try {
    global $session;
    $required_keys = [ "action", "owner_role", "owner_email", "estimated_closure_date", "actionid"];

      //check if array is valid
      if(!checkKeysExist($actionsArr, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
      }
  
      //check if array is valid for values
      if(!checkValueExist($actionsArr, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>implode(", ",$required_keys)." value is mandatory" ]; exit();
      }
  
      //get actions data
      $action = escape_input($actionsArr['action']);
      $owner_role = escape_input($actionsArr['owner_role']);
      $owner_email = escape_input($actionsArr['owner_email']);
      $estimated_closure_date = escape_input($actionsArr['estimated_closure_date']);
      $actionid = escape_input($actionsArr['actionid']);
  
      if($action == ""){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid action" ]; exit();
      }
  
      if(!check_if_email_role_exist_in_company($companycode, $owner_email, $owner_role)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>$owner_email." & ".$owner_role." combination does not exist" ]; exit();
      }
  
      //datestring, min, max
      if(!isDateInRange($estimated_closure_date, date("YYYY-mm-dd"))){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>$estimated_closure_date." Estimated Closure Date must not be less than Today Date" ]; exit();
      }

      if ($module == 'scheduler') {
        $res_sch = $session->execute($session->prepare("SELECT date FROM editschedule WHERE actionid=? ALLOW FILTERING"), array('arguments' => array($refid)));
        if ($res_sch->count() == 0) {
          $res_sch = $session->execute($session->prepare("SELECT date FROM defaultschedule WHERE actionid=?  ALLOW FILTERING"), array('arguments' => array($refid)));
        }
        if (strtotime($estimated_closure_date) > strtotime($res_sch[0]['date'])) {
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>$estimated_closure_date." Date must less than assigned date" ]; exit();
        }
      }
  
      if($actionid!= ""){
        $res_temp = $session->execute(
          $session->prepare("SELECT * FROM temp_actions_data WHERE companycode=? AND status =? AND transactionid=? AND refid=? AND id=?"),
          array('arguments' => array($companycode, "1", $transactionid, $refid, new \Cassandra\Uuid($actionid)))
        );

        if($res_temp->count()==0){
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>$estimated_closure_date." invalid action id" ]; exit();
        }
      }
  
    $arr_return=["code"=>200, "success"=>true, "data"=>""];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function save_management_response_by_module($companycode, $email, $role, $data){
  try{
    global $session;

    //validate data
    // {
    //   "refid" : "REFID",
    //   "transactionid" : "TXNID",
    //   "module" : "Maturity",
    //   "management_response" : {
    //     "response" : "A001",
    //     "option" : "Yes/No"
    //   }
    // }

    $required_keys = [ "refid", "transactionid", "module", "management_response"];
    //check if array is valid
    if(!checkKeysExist($data, $required_keys)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    //check if array is valid for values
    $required_keys_val = [ "refid", "transactionid", "module"];
    if(!checkValueExist($data, $required_keys_val)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>implode(", ",$required_keys_val)." value is mandatory" ]; exit();
    }

    $refid = escape_input($data["refid"]);
    $transactionid = escape_input($data["transactionid"]);
    $module = escape_input($data["module"]);

    //validate management response
    $management_response = $data["management_response"];
    if(!isset($management_response['response'])){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"response in management response is mandatory" ]; exit();
    }

    $mgmt_response = escape_input($management_response['response']);
    if(!in_array($mgmt_response, ["A001", "A002", "A003", "A004"])){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid management response" ]; exit();
    }

    if($mgmt_response == 'A001'){
      if(!isset($management_response['response'])){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"option in management response is mandatory" ]; exit();
      }
    }

    $management_option = $management_response["option"];
    if(!in_array($management_option, ["yes", "no"])){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid management option (should be yes/no)" ]; exit();
    }

    //validate module
    $res_module=$session->execute($session->prepare('SELECT module_name FROM action_module_master WHERE module_name=? ALLOW FILTERING'),array('arguments'=>array($module)));
    if($res_module -> count() == 0){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid module" ]; exit();
    }

    //validate refid & transactionid
    if(!validate_txnid_refid_for_action($module, $refid, $transactionid)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid transactionid or refid" ]; exit();
    }

    //save in action management response
    $res_txn = $session->execute($session->prepare("SELECT resid FROM action_management_response WHERE transactionid=? AND refid =? ALLOW FILTERING"), array('arguments' => array($transactionid, $refid)));
    if ($res_txn->count() > 0) {
      foreach($res_txn as $row_txn){
        $session->execute($session->prepare("UPDATE action_management_response SET mgmtresponseaction=?,selected_response=?,modifydate=? WHERE resid=?"), array('arguments' => array($mgmt_response, $management_option, new \Cassandra\Timestamp(), $row_txn['resid'])));
      }
    } else {
      $columns = [
        "resid",
        "companycode",
        "createdate",
        "effectivedate",
        "mgmtresponseaction",
        "modulename",
        "refid",
        "status",
        "transactionid",
        "selected_response"
      ];
      $columns_data = [
        new \Cassandra\Uuid(),
        $companycode,
        new \Cassandra\Timestamp(),
        new \Cassandra\Timestamp(),
        $mgmt_response,
        $module,
        $refid,
        "1",
        $transactionid,
        $management_option
      ];

      $data_for_insert = [
        "action" => "insert",
        "table_name" => "action_management_response",
        "columns" => $columns,
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
    }
    $table_insert = table_crud_actions($data_for_insert);
    return $table_insert;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function temp_save_define_actions($companycode, $email, $role, $data){
  try{
    global $session;

    //validate data
    // {
    //   "refid" : "REFID",
    //   "transactionid" : "TXNID",
    //   "module" : "Maturity",
    //   "actions" :{
    //       "action" : "Action - 1",
    //       "owner_role" : "role",
    //       "owner_email" : "vik@email.com",
    //       "estimated_closure_date" : "2023-04-09",
    //        "actionid" : "" //null in case of add new
    //     }
    // }

    $required_keys = [ "refid", "transactionid", "module", "actions"];
    //check if array is valid
    if(!checkKeysExist($data, $required_keys)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    //check if array is valid for values
    $required_keys_val = [ "refid", "transactionid", "module"];
    if(!checkValueExist($data, $required_keys_val)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>implode(", ",$required_keys_val)." value is mandatory" ]; exit();
    }

    $refid = escape_input($data["refid"]);
    $transactionid = escape_input($data["transactionid"]);
    $module = escape_input($data["module"]);

    //validate module
    $res_module=$session->execute($session->prepare('SELECT module_name FROM action_module_master WHERE module_name=? ALLOW FILTERING'),array('arguments'=>array($module)));
    if($res_module -> count() == 0){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid module" ]; exit();
    }

    //validate refid & transactionid
    if(!validate_txnid_refid_for_action($module, $refid, $transactionid)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid transactionid or refid" ]; exit();
    }

    $actions_data = $data["actions"];
    //validate actions
    $validate_temp_actions_input_data = validate_temp_actions_input_data($companycode,$transactionid, $refid, $module, $actions_data);
    if(!$validate_temp_actions_input_data['success']){
      return $validate_temp_actions_input_data; exit();
    }

    //save in temp actions data
      $action = escape_input($actions_data['action']);
      $owner_role = escape_input($actions_data['owner_role']);
      $owner_email = escape_input($actions_data['owner_email']);
      $estimated_closure_date = escape_input($actions_data['estimated_closure_date']);
      $actionid = escape_input($actions_data['actionid']);

      $newDateFormating = $estimated_closure_date . " 05:30:00";
      $newDate = date("d F Y H:i:s", strtotime($newDateFormating));

      if($actionid!=""){
        $session->execute(
          $session->prepare("UPDATE temp_actions_data SET action=?,owner=?,owner_role=?,closure_date=?,modifydate=? WHERE companycode=? AND status = ? AND transactionid=? AND refid= ? AND id=? "),
          array('arguments' => array($action, $owner_email, $owner_role, new \Cassandra\Date(strtotime($newDate)), new \Cassandra\Timestamp(), $companycode, "1", $transactionid, $refid, $actionid))
        );
      }else{
        $id_for_action = new \Cassandra\Uuid();
        $actionid = (string)$id_for_action;

        $insert_action_txn_details=insert_action_txn_details($transactionid,$refid,"Review");
        if(!$insert_action_txn_details['success']){
          return $insert_action_txn_details; exit();
        }

        $columns = [
          "id",
          "refid",
          "createdate",
          "effectivedate",
          "action",
          "response",
          "status",
          "owner",
          "owner_role",
          "closure_date",
          "transactionid",
          "action_status",
          "review_status",
          "companycode",
          "accept_reject"
        ];
        $columns_data = [
          $id_for_action,
          $refid,
          new \Cassandra\Timestamp(),
          new \Cassandra\Timestamp(),
          $action,
          "",
          "1",
          $owner_email,
          $owner_role,
          new \Cassandra\Date(strtotime($newDate)),
          $transactionid,
          "open",
          "Review",
          $companycode,
          "Implement"
        ];

        $data_for_insert = [
          "action" => "insert",
          "table_name" => "temp_actions_data",
          "columns" => $columns,
          "isCondition" => false,
          "condition_columns" => "",
          "columns_data" => $columns_data,
          "isAllowFiltering" => false
        ];

        $table_insert = table_crud_actions($data_for_insert);
        return $table_insert;
      }

      // return success
      $arr_return=["code"=>200, "success"=>true, "data"=>[
        'message' => 'success',
        'actionid' => $actionid
      ]];
      return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param string $txn_id
 * @param string $refid
 * @param string $status
 */
function insert_action_txn_details($txn_id,$refid,$status){
  try {
        global $session;
        if($status == "Validate")
        {
          $columns = [
            "id",
            "transactionid",
            "refid",
            "review_status",
            "validate_date"
          ];
          $columns_data = [
            new \Cassandra\UUid(),
            (string) $txn_id,
            (string) $refid,
            "Validate",
            new \Cassandra\Timestamp()
          ];

          $data_for_insert = [
            "action" => "insert",
            "table_name" => "action_txn_details",
            "columns" => $columns,
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);
          return $table_insert; 
        }elseif($status == "Submit")
        {
          $columns = [
            "id",
            "transactionid",
            "refid",
            "review_status",
            "submit_date"
          ];
          $columns_data = [
            new \Cassandra\UUid(),
            (string) $txn_id,
            (string) $refid,
            "Submit",
            new \Cassandra\Timestamp()
          ];

          $data_for_insert = [
            "action" => "insert",
            "table_name" => "action_txn_details",
            "columns" => $columns,
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);
          return $table_insert; 
        }elseif($status == "Review")
        {
          $columns = [
            "id",
            "transactionid",
            "refid",
            "review_status",
            "review_date"
          ];
          $columns_data = [
            new \Cassandra\UUid(),
            (string) $txn_id,
            (string) $refid,
            "Review",
            new \Cassandra\Timestamp()
          ];

          $data_for_insert = [
            "action" => "insert",
            "table_name" => "action_txn_details",
            "columns" => $columns,
            "isCondition" => false,
            "condition_columns" => "",
            "columns_data" => $columns_data,
            "isAllowFiltering" => false
          ];
          $table_insert = table_crud_actions($data_for_insert);
          return $table_insert; 
        }else{
          return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"In saving action transaction details" ]; 
        }
    } catch (\Exception $e) {
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
}

function save_define_actions($txn_id, $refid,$type, $globalVariableAction, $companycode, $email, $role, $notice_arr = [])
{
  try {
    global $session;
    $arr = [];
    $response = '';
    $id_for_action = new \Cassandra\UUid();


    $data=$globalVariableAction['tid_ref_id_arr'];
    $res_axt = $session->execute($session->prepare("SELECT * FROM temp_actions_data WHERE transactionid=? and refid =? ALLOW FILTERING"), array('arguments' => array($txn_id,$refid)));
    if ($res_axt->count() == 0) {
      $arr = ["success" => false, "msg" => "No Data Found", "data" => ''];
      return $arr;
      exit();
    }



    $refid_arr = [];
    foreach ($res_axt as $row_axt) {
      $cont_key = $txn_id . $txn_id . $id_for_action;

      $report_display = 0;
      if (isset($globalVariableAction[$row_axt['refid'] . "*|*report_display"])) {
        $report_display = (int) $globalVariableAction[$row_axt['refid'] . "*|*report_display"];
      }


      if (!in_array($row_axt['refid'], $refid_arr)) {
        $report_data = [];
        if (isset($globalVariableAction[$row_axt['refid'] . "*|*report_data"])) {
          $report_data = $globalVariableAction[$row_axt['refid'] . "*|*report_data"];
        }
        $action_report_data_insert = action_report_data_insert($txn_id, $row_axt['refid'], $type, $report_data, $companycode, $email, $role);
        if (!$action_report_data_insert['success']) {
          return $action_report_data_insert;
          exit();
        }
        array_push($refid_arr, $row_axt['refid']);
      }
      $columns = [
        "id",
        "refid",
        "createdate",
        "effectivedate",
        "action",
        "response",
        "status",
        "owner",
        "owner_role",
        "closure_date",
        "transactionid",
        "action_status",
        "companycode",
        "accept_reject",
        "review_status",
        "variable_1",
        "variable_2",
        "variable_3",
        "variable_4",
        "report_display"
      ];
      $columns_data = [
        new \Cassandra\UUid(), $row_axt['refid'],
        new \Cassandra\Timestamp(),
        new \Cassandra\Timestamp(), $row_axt['action'],
        $response,
        "1", $row_axt['owner'], $row_axt['owner_role'], $row_axt['closure_date'],
        (string) $txn_id,
        "open",
        $companycode,
        "Implement",
        "Review",
        $row_axt['variable_1'], $row_axt['variable_2'], $row_axt['variable_3'], $row_axt['variable_4'],
        $report_display
      ];
      $data_for_insert = [
        "action" => "insert",
        //read/insert/update/delete
        "table_name" => "actions_data",
        //provide actual table name or dummy table name thats been in JSON/arr file
        "columns" => $columns,
        //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      // $arr=["success"=>false,"msg"=>"actions","data"=>''];
      //   return $arr; exit();

      $table_insert = table_crud_actions($data_for_insert);
      if (!$table_insert['success']) {
        $arr = ["success" => false, "msg" => $table_insert['msg'], "data" => $table_insert];
        return $arr;
        exit();
      }

      $insert_action_txn_details=insert_action_txn_details($txn_id,$row_axt['refid'],"Review");
      //if ($type == 'scheduler') {
        //$session->execute($session->prepare("DELETE FROM temp_actions_data WHERE id=?"),array('arguments'=>array($row_axt['id'])));
        $session->execute(
          $session->prepare("DELETE FROM temp_actions_data WHERE transactionid=? AND refid=? AND companycode=? AND status=? AND id=?"),
          array('arguments' => array($txn_id, $row_axt['refid'], $companycode, "1", new \Cassandra\Uuid($row_axt['id'])))
        );
      //}
    }

    // transaction per row
    $columns = [
      "id",
      "action",
      "module_name",
      "companycode",
      "createdate",
      "effectivedate",
      "refid",
      "status",
      "transactionid",
      "action_status",
      "cont_key"
    ];
    $columns_data = [
      new \Cassandra\UUid(),
      (string) $txn_id,
      $type,
      $companycode,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      (string) new \Cassandra\UUid(),
      "1",
      (string) $txn_id,
      "Review",
      $cont_key
    ];
    $data_for_insert = [
      "action" => "insert",
      "table_name" => "action_txn",
      "columns" => $columns,
      "isCondition" => false,
      "condition_columns" => "",
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $table_insert = table_crud_actions($data_for_insert);




    // $session->execute($session->prepare("UPDATE action_txn SET action_status=?,modifydate=? WHERE id=?"),array('arguments'=>array('Review',new \ Cassandra\Timestamp(), new \Cassandra\Uuid($refid))));
    if ($table_insert['success']) {



      $email_role_array = reviewer_notice_write($companycode, $email, $role);
      $notice_update = notice_update_all($txn_id, $companycode, $email, $role, "IT01");
      $txn_name = '';
      if (isset($notice_arr['txn_name'])) {
        $txn_name = $notice_arr['txn_name'];
      }
      if ($txn_name == '') {
        $txn_type = transaction_type_validation($txn_id);
        $txn_name = $txn_type['name'];
      }
      

      $link = "review_act.php?tid=" . $cont_key . "&tname=" . $txn_name . "&type=" . $type;

      foreach ($email_role_array['data'] as $em_role) {
        $notice_output = notice_write("IT01", $companycode, $email, $role, $link, $em_role['cabemail'], $em_role['cabrole'], $txn_name, (string) $txn_id);
      }

      $res_axt = $session->execute($session->prepare("SELECT * FROM temp_actions_data WHERE transactionid=? ALLOW FILTERING"), array('arguments' => array($txn_id)));
      $arr = ["success" => true, "msg" => "Action Inserted Successfully", "data" =>$res_axt->count()];
      return $arr;
    }
  } catch (\Exception $e) {
    return "Error Occured" . $e;
  }
}


function check_report_data_validity($report_data){
    $arr_flag_validation = [0, 1];
    $row_no_flag_validation = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
    $seq_no_flag_validation = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
    $errCode = 0;
    $errMsg = [];

    //data validation
    foreach ($report_data as $value_val) {

      //display validation
      if (isset($value_val['display'])) {
        if (!in_array($value_val['display'], $arr_flag_validation)) {
          $errCode = 1;
          array_push($errMsg, "Display flag is invalid in report. It Should be only 0/1.");
        }
      } else {
        $errCode = 1;
        array_push($errMsg, "Display flag is mandatory in report.");
      }

      //row_no validation
      if (isset($value_val['row_no'])) {
        if (!in_array($value_val['row_no'], $row_no_flag_validation)) {
          $errCode = 1;
          array_push($errMsg, "Row no flag is invalid in report. It Should be between 1-20.");
        }
      } else {
        $errCode = 1;
        array_push($errMsg, "Row no flag is mandatory in report.");
      }

      //sequence validation
      if (isset($value_val['sequence'])) {
        if (!in_array($value_val['row_no'], $seq_no_flag_validation)) {
          $errCode = 1;
          array_push($errMsg, "Sequence flag is invalid in report. It Should be between 1-20.");
        }
      } else {
        $errCode = 1;
        array_push($errMsg, "Sequence flag is mandatory in report.");
      }

      //Header validation
      if (isset($value_val['header'])) {
        if ($value_val['header'] == "") {
          $errCode = 1;
          array_push($errMsg, "Header should not be empty in report.");
        }
      } else {
        $errCode = 1;
        array_push($errMsg, "Header is mandatory in report.");
      }
    }


    if ($errCode == 1) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Error while validating report data: ".implode(", ", $errMsg) ]; exit();
    }

    // return success
    $arr_return=["code"=>200, "success"=>true, "data"=>[ 'message' => 'success' ]];
    return $arr_return;
}


// action report data insertion
function action_report_data_insert($txn_id, $refid, $module_name, $report_data, $companycode, $email, $role)
{
  try {
    global $session;
    //validate the report data
    $crdv = check_report_data_validity($report_data);
    if(!$crdv['success']){ return $crdv; exit(); }

    $res_sch = $session->execute($session->prepare("SELECT * FROM action_module_report_data WHERE transactionid=? AND refid =?"), array('arguments' => array($txn_id, $refid)));
    if (count($report_data) > 0) {
      // foreach ($res_sch as $row_sch) {
      //   $session->execute($session->prepare("DELETE FROM action_module_report_data WHERE refid=? AND transactionid=? AND row_no=? AND sequence=? AND id=?"),
      //   array('arguments'=>array($row_sch['refid'],$row_sch['transactionid'],$row_sch['row_no'],$row_sch['sequence'],$row_sch['id'])));
      // }
    }
    if ($res_sch->count() > 0) {
      foreach ($res_sch as $row_sch) {
        foreach ($res_sch as $row_sch) {
          $session->execute(
            $session->prepare("DELETE FROM action_module_report_data WHERE refid=? AND transactionid=? AND row_no=? AND sequence=? AND id=?"),
            array('arguments' => array($row_sch['refid'], $row_sch['transactionid'], $row_sch['row_no'], $row_sch['sequence'], $row_sch['id']))
          );
        }
      }
    }
    foreach ($report_data as $key_data => $value_data) {
      $header = (string) $value_data['header'];
      $value = (string) $value_data['value'];
      $row_no = (int) $value_data['row_no'];
      $sequence = (int) $value_data['sequence'];
      $display = (int) $value_data['display'];
      $columns = [
        "refid",
        "transactionid",
        "row_no",
        "sequence",
        "id",
        "companycode",
        "createdate",
        "display",
        "effectivedate",
        "header",
        "modulename",
        "value"
      ];
      $columns_data = [
        (string) $refid,
        $txn_id,
        $row_no,
        $sequence,
        new \Cassandra\Uuid(),
        $companycode,
        new \Cassandra\Timestamp(),
        $display,
        new \Cassandra\Timestamp(),
        $header,
        $module_name,
        $value
      ];

      $data_for_insert = [
        "action" => "insert",
        "table_name" => "action_module_report_data",
        "columns" => $columns,
        "isCondition" => false,
        "condition_columns" => "",
        "columns_data" => $columns_data,
        "isAllowFiltering" => false
      ];
      $table_insert = table_crud_actions($data_for_insert);
      if (!$table_insert['success']) {
        $errCode = 1;
        array_push($errMsg, "Header is mandatory in report.");
      }
    }

    $arr = ["success" => true, "msg" => "Successfully Saved2", "data" => $refid];
    return $arr;

  } catch (\Exception $e) {
    $arr = ["success" => false, "msg" => "Error Occured" . $e, "data" => ""];
    return $arr;
    exit();
  }
}

function get_action_mgmt_response($tid, $refid, $companycode)
{
  try {
    global $session;
    $arr = [];
    $result_mgmtresponse = $session->execute($session->prepare("SELECT resid,createdate,mgmtresponseaction,modulename,selected_response FROM action_management_response WHERE refid=? AND transactionid =? AND status=? AND companycode= ? ALLOW FILTERING"), array('arguments' => array($refid, $tid, "1",$companycode)));
    foreach ($result_mgmtresponse as $row) {
      $row['resid'] = (string)$row['resid'];
      $row['createdate'] = get_date_by_timestamp($row['createdate'], "d-m-Y");
      $arr = $row;
    }
    return $arr;
  } catch (\Exception $e) {
    return [];
  }
}


?>