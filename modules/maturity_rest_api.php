<?php 

function GetMaturityHandler($funcCallType){
    try{
  
      switch($funcCallType){
        case "assessmentList":
          $page=1; $limit=10; $day = "ALL";
          if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
          if(isset($_GET["limit"])){ $limit=(int)$_GET["limit"]; } 
          if(isset($_GET["day"])){ $day=$_GET["day"]; } 
          if(isset($GLOBALS['companycode'])){
            $output = get_maturity_assessment_list($GLOBALS['companycode'], $limit, $page, $day);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "questionList":
          $page=1; $limit = "ALL";
          if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
          if(isset($_GET["limit"])){ $limit=$_GET["limit"]; } 
          if(isset($_GET['testid'])){
            $output = get_maturity_question($_GET["testid"], $limit, $page);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "roleUserListByLaw":
          $jsonString = file_get_contents('php://input');
          $json = json_decode($jsonString,true);
          if(isset($GLOBALS['companycode']) && isset($json['law']) && isset($json['type'])){
            $output = get_user_role_list_by_law($GLOBALS['companycode'], $json['law'],  $json['type']);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "initiate":
          $jsonString = file_get_contents('php://input');
          if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
          $json = json_decode($jsonString,true);
          if(!is_array($json)){
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
          }
          
          if(isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($GLOBALS['law']) ){
            $output = maturity_transaction_write($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['law'], $json);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "initiateAssessment":
          $jsonString = file_get_contents('php://input');
          if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
          $json = json_decode($jsonString,true);
          if(!is_array($json)){
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
          }
          
          if(isset($GLOBALS['companycode']) && isset($json['email']) && isset($json['role']) && isset($json['transactionid']) ){
            $output = assessmentstatus_write($GLOBALS['companycode'], $json['email'], $json['role'], $json['transactionid']);
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

//get_maturity_assessment_list
function get_maturity_assessment_list($companycode, $limit, $page, $day){
    try {
        global $session;

        if($companycode==""){
            //Bad Request Error
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
        }

        //timestamp
        $timestamp = 0;
        if(strtoupper($day) != "ALL"){ 
            $last_day = (int)$day;
            if($last_day < 1){ $last_day = 1; }
            $timestamp = strtotime("-". $last_day. " days");
        }

        //validate limit and page
        if($limit<1){ $limit=1; } if($page<1){ $page=1; }
        $page = $page - 1; 
        $arr=[]; $arr_txn=[]; 
        $total_maturity = 0;

        $result_txn = $session->execute($session->prepare("SELECT transactionid,createdate,status,transactiontype FROM transactions WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
        foreach ($result_txn as $row_txn) {
          if ($row_txn['status']!='disable') {
            if($row_txn['transactiontype']=="vendor" || $row_txn['transactiontype']=="technical risk" || $row_txn['transactiontype']=="privacy_notice"){ }else{
                $modifydate_str=(string)$row_txn['createdate'];
                $modifydate_int = (int)$modifydate_str/1000;
   
                if($modifydate_int >= $timestamp){
                    $total_maturity++;
                    $arr_txn[(string)$row_txn['transactionid']] = $modifydate_int;
                }

            }
          }
        }

        arsort($arr_txn);
        $arr_final_txn =[];
        //divide array and find specific chunks
        $array_chunk=array_chunk($arr_txn,$limit,true);
        $total_index=count($array_chunk);
        if(isset($array_chunk[$page])){
            $arr_final_txn=$array_chunk[$page];
        }

        foreach ($arr_final_txn as $tid => $value) {
            $result = $session->execute($session->prepare("SELECT * FROM transactions WHERE transactionid=?"),array('arguments'=>array(new \Cassandra\Uuid($tid))));
            foreach ($result as $row) {
                $updated_report_status="0";
                $txn_type_show=$row['transactiontype'];
                $result_keys= $session->execute("SELECT dispname FROM lawkeys WHERE usingkey=? ALLOW FILTERING",array('arguments'=>array($txn_type_show)));
                if($result_keys->count()>0){ $txn_type_show=$result_keys[0]['dispname']; }

                //finding updated status
                $result_action_status = $session->execute($session->prepare("SELECT id FROM actions_data  WHERE transactionid=? AND status=? ALLOW FILTERING"),array('arguments'=>array((string)$row['transactionid'],"1")));
                if($result_action_status->count()>0){ $updated_report_status="1"; }else{
                    $result_action_status = $session->execute($session->prepare("SELECT action_status FROM action_txn  WHERE transactionid=? AND status=? ALLOW FILTERING"),array('arguments'=>array((string)$row['transactionid'],"1")));
                    if($result_action_status->count()>0){ $updated_report_status="1"; }else{
                        $result_action_status=$session->execute($session->prepare("SELECT mgmtresponseaction FROM action_management_response WHERE transactionid=? AND mgmtresponseaction=? ALLOW FILTERING"),array('arguments'=>array((string)$row['transactionid'],"A001")));
                        if($result_action_status->count()>0){ $updated_report_status="1"; }else{
                        $result_notice=$session->execute($session->prepare('SELECT notice_no,notice_to,notice_to_role FROM notice WHERE  transactionid=? AND notice_module_alt=?  AND notice_alert_status =?  ALLOW FILTERING'),array('arguments'=>array((string)$row['transactionid'],"define_action","settled")));
                            if ($result_notice->count() > 0) {
                                $updated_report_status="1";
                            }
                        }
                    }

                    $createdate=(string)$row['createdate'];
                    if($createdate==""){ $create_date="-"; }else{ $create_date=date("d-m-Y",(int)$createdate/1000); }


                    $modifydateDifference = "-";
                    $modifydate=(string)$row['modifydate'];
                    if($modifydate==""){ $modify_date="-"; }else{ 
                        $modify_date=date("d-m-Y",(int)$modifydate/1000);
                        $now_date = (string)new \Cassandra\Timestamp();
                        $modifydateDifference = getDateDifference((int)$modifydate/1000, (int)$now_date/1000);
                    }

                    //Get status of Maturity
                    $txn_status_comp = "";
                    switch ($row['status']) {
                        case '0':
                            $txn_status_comp = 'Assessment In Progress';
                            //check if all assessment is done in and validation started
                            $result_er = $session->execute($session->prepare("SELECT id FROM email_role_map_for_assessment WHERE transactionid=? AND status=? ALLOW FILTERING"),array('arguments'=>array($tid,"1")));
                            $result_as = $session->execute($session->prepare("SELECT testid FROM assessmentstatus WHERE transactionid=? AND status=? ALLOW FILTERING"),array('arguments'=>array($row['transactionid'],"1")));
                            if ($result_er->count()==$result_as->count()) {
                                $txn_status_comp="Validate Assessment";
                            }
                           break;
                        case '2':
                            //check if all actions are closed
                            $txn_status_comp = 'Management Response';
                           break;
                    }

                    //Get law display name
                    if($row['transactiontype']==''){ $row['transactiontype']=' '; }
                    $active_law_for_tid=$row['transactiontype'];
                    $result_law= $session->execute($session->prepare("SELECT dispname FROM lawkeys WHERE usingkey=? ALLOW FILTERING"),array('arguments'=>array($row['transactiontype'])));
                    if ($result_law->count()>0) { $active_law_for_tid=$result_law[0]['dispname']; }

                    $transactionname=$row['transactionname'];

                    $arr[]=array(
                        "report_status"=>$row['status'],
                        "ldispname"=>$active_law_for_tid,
                        "txn_status_comp"=>$txn_status_comp,
                        "updated_report_status"=>$updated_report_status,
                        "txn_name"=>$transactionname,
                        "txn_type"=>$row['transactiontype'],
                        "txn_type_show"=>$txn_type_show,
                        "txn_id"=>(string)$row['transactionid'],
                        "type"=>"maturity",
                        "createdate"=>$create_date ,
                        "modifydate"=>$modify_date ,
                        "modifydateDiff"=>$modifydateDifference
                      );

                }
            }
        }
     
        $final_data=[
            "limit" => $limit,
            "day" => $day,
            "page" => $page+1,
            "pagination" => $total_index,
            "total_maturity" => $total_maturity,
            "incidents" => $arr
          ];
    
          $arr_return=["code"=>200, "success"=>true, "data"=>$final_data ];
          return $arr_return;
    
      } catch (\Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
      }
}

/**
 * @param $comapnycode
 * @param $law
 * @param $type //maturity/internal
 */
function get_user_role_list_by_law($companycode , $law, $type)
{
  try {
    global $session;
    $role_arr=[];

    $active_flag_data=check_if_law_is_active_by_expirydate($companycode,$law);
    if(!$active_flag_data['success']){
      return $active_flag_data; exit();
    }

    $active_flag=$active_flag_data['data']['status'];
    if($active_flag==0){
      $final_arr = [
        "message" =>"Your subscription has expired. Please renew to access the full range of features and benefits of APMP.",
        "data"=> []
      ];
      $arr_return=["code"=>200, "success"=>true, "data"=>$final_arr ];
      return $arr_return;
      exit();
    }


    if ($type=='maturity') {
      $get_roles_for_laws_by_acf=get_roles_for_laws_by_acf_maturity($law);
    }else {
      $get_roles_for_laws_by_acf=get_roles_for_laws_by_acf_internal($law);
    }

    if ($get_roles_for_laws_by_acf['success']) { $role_arr=$get_roles_for_laws_by_acf['data']; }
    $arr=[];
    foreach ($role_arr as $value_role) {
      $get_name_email=get_name_email_set_by_role($companycode,$value_role);
      $arr[$value_role]=$get_name_email;
    }

    if(count($arr) == 0){
      $final_arr = [
        "message" =>"Get ready to experience the future, coming soon!",
        "data"=> []
      ];
      $arr_return=["code"=>200, "success"=>true, "data"=>$final_arr ];
      return $arr_return;
      exit();
    }

    $final_arr = [
      "message" =>"success",
      "data"=> $arr
    ];

    $arr_return=["code"=>200, "success"=>true, "data"=>$final_arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param string $companycode
 * @param string $email
 * @param string $role
 * @param string $activeLaw
 * @param array $transaction_array
 */
function maturity_transaction_write($companycode, $email, $role, $activeLaw ,$transaction_array)
{
  global $session;
  try {

    $required_keys = ["transaction_name", "transaction_locationinscope", "transaction_department", "transaction_law", "role_email_map"];
    $required_keys_lis = ["address1", "country", "state", "city", "pincode"];

    //check if array is valid
    if(!checkKeysExist($transaction_array, $required_keys)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    $transaction_locationinscope= $transaction_array['transaction_locationinscope'];
    if(!is_array($transaction_locationinscope)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid location in scope" ]; exit();
    }

    foreach ($transaction_locationinscope as $value_lis_val) {
      //check if lis array is valid
      if(!checkKeysExist($value_lis_val, $required_keys_lis)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid location in scope" ]; exit();
      }
    }

    $role_email_arr= $transaction_array['role_email_map'];
    if(!is_array($role_email_arr)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid role_email_map" ]; exit();
    }

    $transaction_department= escape_input($transaction_array['transaction_department']);
    $transaction_name= escape_input($transaction_array['transaction_name']);
    $transaction_law= escape_input($transaction_array['transaction_law']);

    //Validate Department
    if(!is_array($transaction_department)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid department" ]; exit();
    }
    if (count($transaction_department)==0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Select Department" ]; exit();
    }

    $getAllDept = get_department_list($companycode);
    if(!$getAllDept['success']){
      return $getAllDept; exit();
    }
    $allDeptInComp =  $getAllDept['data'];

    foreach ($transaction_department as $value_department) {
      if(!in_array($value_department, $allDeptInComp)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid department" ]; exit();
      }
    }

    //validate law
    $result_law= $session->execute($session->prepare('SELECT dispname FROM lawkeys WHERE dispname=? ALLOW FILTERING'),array('arguments'=>array($transaction_law)));
    if($result_law -> count() == 0){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
    }

    $custcode = get_custcode_from_email($email);
    
    $version=''; $version_array=array();
    $result_version= $session->execute("SELECT version FROM question_list");
    foreach ($result_version as $row_version) { array_push($version_array,(int)$row_version['version']); }
    $version=(string)max($version_array);

    $getAllRolesFunc = get_user_role_list_by_law($companycode, $transaction_law, "maturity");
    if(!$getAllRolesFunc['success']){
      return $getAllRolesFunc; exit();
    }
    $allRolesReq =  $getAllRolesFunc['data']['data'];

    //validate email role mapping
    $roleIncoming = [];
    foreach ($role_email_arr as $keyRole => $check_arr) { 
      if(!is_array($check_arr)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid role_email_map" ]; exit();
      }
      if (sizeof($check_arr)==0) { 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Atleast one email must be checked under each role!" ]; exit();
      } 

      foreach ($check_arr as $valueEmail) {
        if(!check_if_email_role_exist_in_company($companycode, $valueEmail, $keyRole)){
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid email-role combination!" ]; exit();
        }
      }
      array_push($roleIncoming, $keyRole);
    }

    if(!checkKeysExist($allRolesReq, $roleIncoming)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"send all required role and email. All roles ".implode(', ', array_keys($allRolesReq)) ]; exit();
    }

    if ($transaction_name=='') {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Fill Assesment Name" ]; exit();
    }

    if ($transaction_law=='') {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Select Assessment Law" ]; exit();
    }

    if (count($transaction_locationinscope)==0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Select Location in Scope" ]; exit();
    }

    $acf_flag="1";

    $txn_id =new \Cassandra\Uuid();
    $query_insert = $session->prepare("INSERT INTO transactions(
         transactionid,
         companycode,
         creator,
         status,
         transactionname,
         transactiontype,
         email_role_status,
         createdate,
         effectivedate,
         version,
         acf_flag
    ) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
    $session->execute($query_insert,array('arguments'=>array(
        $txn_id,
        $companycode,
        $custcode,
        '0',
        escape_input($transaction_name),
        escape_input($transaction_law),
        "1",
        new \Cassandra\Timestamp(),
        new \Cassandra\Timestamp(),
        $version,
        $acf_flag
    )));

      foreach ($role_email_arr as $key_a => $val_a) {
        foreach ($val_a as $val_f) {

          //temp notice === For dot representation purpose
          $result_temp_notice= $session->execute($session->prepare('SELECT id,assessment_notice FROM temp_notice WHERE email=? AND role=? AND companycode=? AND law=? ALLOW FILTERING'),array('arguments'=>array($val_f,$key_a,$companycode,$transaction_law)));
          if ($result_temp_notice->count()>0) {
            foreach ($result_temp_notice as $row_temp_notice) {
              $session->execute($session->prepare('UPDATE temp_notice SET assessment_notice=? WHERE id=?'),array('arguments'=>array("1",$row_temp_notice['id'])));
            }
          }else {
            $session->execute($session->prepare('INSERT INTO temp_notice (id,email,role,law,assessment_notice,companycode,createdate,effectivedate) VALUES(?,?,?,?,?,?,?,?)'),array('arguments'=>array(
              new \Cassandra\Uuid(),$val_f,$key_a,$transaction_law,"1",$companycode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp()
            )));
          }

          //Assign submit pages to all roles
          $result_submit= $session->execute("SELECT modifyaccess,rtcuuid FROM roletocustomer WHERE rtcrole=? AND rtccustemail=? AND rolestatus=? AND companycode=? ALLOW FILTERING",array('arguments'=>array(
            $key_a,$val_f,"1",$companycode
          )));
          foreach ($result_submit as $row_submit) { $modifyaccess=explode("|",$row_submit['modifyaccess']);
            if ($row_submit['modifyaccess']=="") {
              $session->execute("UPDATE roletocustomer SET modifydate=?,modifyaccess=? WHERE rtcuuid=?",array('arguments'=>array(new \Cassandra\Timestamp(),"modify|PG008",$row_submit['rtcuuid'])));
            }else {
              if (strpos($row_submit['modifyaccess'], 'PG008') !== false) { } else{
                  $session->execute("UPDATE roletocustomer SET modifydate=?,modifyaccess=? WHERE rtcuuid=?",array('arguments'=>array(new \Cassandra\Timestamp(),$row_submit['modifyaccess']."|PG008",$row_submit['rtcuuid'])));
              }
            }
          }

          $query_insert = $session->prepare("INSERT INTO email_role_map_for_assessment(
             id ,
             createdate ,
             effectivedate ,
             email ,
             role ,
             status ,
             transactionid,
             companycode
          ) VALUES(?,?,?,?,?,?,?,?)");
          $session->execute($query_insert,array('arguments'=>array(
              new \Cassandra\Uuid(),
              new \Cassandra\Timestamp(),
              new \Cassandra\Timestamp(),
              $val_f,
              $key_a,
              "1",
              (string)$txn_id,
              $companycode
          )));

          //Create notification
           notice_write("MA01",$companycode,$email,$role,(string)$txn_id,$val_f,$key_a,$transaction_name,(string)$txn_id);

        }
      }


      //Insert department and location in scope
      foreach ($transaction_department as $value_department) {
        $query_insert = $session->prepare("INSERT INTO transactions_department(
          id,
          companycode,
          transactionid,
          department,
          createdate,
          effectivedate,
          status
        ) VALUES(?,?,?,?,?,?,?)");
        $session->execute($query_insert,array('arguments'=>array(
            new \Cassandra\Uuid(),
            $companycode,
            (string)$txn_id,
            $value_department,
            new \Cassandra\Timestamp(),
            new \Cassandra\Timestamp(),
            "1"
        )));
      }

      foreach ($transaction_locationinscope as $value_lis) {
        $query_insert = $session->prepare("INSERT INTO transactions_locationinscope(
          id,
          companycode,
          transactionid,
          address1,
          country,
          state,
          city,
          pincode,
          createdate,
          effectivedate,
          status
        ) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
        $session->execute($query_insert,array('arguments'=>array(
            new \Cassandra\Uuid(),
            $companycode,
            (string)$txn_id,
            escape_input($value_lis['address1']),
            escape_input($value_lis['country']),
            escape_input($value_lis['state']),
            escape_input($value_lis['city']),
            escape_input($value_lis['pincode']),
            new \Cassandra\Timestamp(),
            new \Cassandra\Timestamp(),
            "1"
        )));
      }

      //Update landing update
      update_landing_module($companycode, $email, $role, $custcode, $activeLaw, "6");

      $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "Successfully Initiated"] ];
      return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param string $companycode
 * @param string $email
 * @param string $role
 * @param string $transactionid //MA+IA+Vendor+Privacy_Notice
 */
function assessmentstatus_write($companycode, $email, $role, $transactionid, $reload=true)
{
 try{
  global $session;
  $transactionid = escape_input($transactionid);
  $email = escape_input($email);
  $role = escape_input($role);
  if($transactionid == "" || $email == "" || $role == ""){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
  }

  //validate email & role
  if(!check_if_email_role_exist_in_company($companycode, $email, $role)){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid role & email" ]; exit();
  }

  $acf_flag=""; $type_of_txn_table="transactions"; $lawSel="";
  $email_role_status=' '; $version_to_txn=' '; $assessment_type_alt='';

  //validate transactions
  $result_txn_chk= $session->execute($session->prepare('SELECT * FROM transactions WHERE transactionid=?'),array('arguments'=>array(new \Cassandra\Uuid($transactionid))));
  if ($result_txn_chk->count()==0) {
    //For Internal
    $result_txn_chk= $session->execute($session->prepare('SELECT * FROM internal_audit_txn WHERE id=?'),array('arguments'=>array(new \Cassandra\Uuid($transactionid))));
    if ($result_txn_chk->count()==0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid transactionid" ]; exit();
    }
    foreach ($result_txn_chk as $row_txn_chk) {
      $type_of_txn_table="internal";
      $data['testid']="NA";
      $data['testname']=$row_txn_chk['name'];
      $email_role_status="1";
      $version_to_txn=$row_txn_chk['version'];
      $acf_flag=$row_txn_chk['acf_flag'];
      $data['type']=$row_txn_chk['type']."_ia";
      $lawSel=$row_txn_chk['type'];
      if ($acf_flag=="1") {
        $data['type']=$lawSel;
      }
    }
  }else {
    //For Maturity
    foreach ($result_txn_chk as $row_txn_chk) {
      $txn_n_tx =explode("|",$row_txn_chk['transactionname']);
      $data['type']=$row_txn_chk['transactiontype'];
      $lawSel=$row_txn_chk['transactiontype'];
      $data['testid']="NA";
      if(isset($txn_n_tx[1])){ $data['testid']=$txn_n_tx[1]; }
      $data['testname']=$txn_n_tx[0];
      $email_role_status=$row_txn_chk['email_role_status'];
      $version_to_txn=$row_txn_chk['version'];
      $assessment_type_alt=$row_txn_chk['transactiontype_alt'];
      $acf_flag=$row_txn_chk['acf_flag'];
    }
  }

  $testid_to_fetch_question=$data['testid'];
  $testid=""; $assessmentversion="";

  $custcode = get_custcode_from_email($email);

  //For Privacy Notice
  if ($data['type']=="privacy_notice") {
    $result= $session->execute($session->prepare('SELECT testid,assessmentversion FROM assessmentstatus WHERE transactionid=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid))));
    foreach ($result as $a_v) { $assessmentversion=$a_v['assessmentversion']; $testid=$a_v['testid']; }
    if ($testid=="") { $testid=$transactionid; }
    $count =$result->count();
  }else {
      if ($email_role_status=='1') {
        //check if email & role combination exists
        $result_er = $session->execute($session->prepare("SELECT id FROM email_role_map_for_assessment WHERE transactionid=? AND status=? AND role=? AND email=? ALLOW FILTERING"),array('arguments'=>array($transactionid,"1",$role,$email)));
        if ($result_er->count() == 0) {
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid role & email for this transaction" ]; exit();
        }
        
        $result= $session->execute($session->prepare('SELECT testid,assessmentversion FROM assessmentstatus WHERE transactionid=? AND role=? AND custemail=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid),$role,$email)));
        foreach ($result as $a_v) { $assessmentversion=$a_v['assessmentversion']; $testid=$a_v['testid']; }
      }else {
        $result= $session->execute($session->prepare('SELECT testid,assessmentversion FROM assessmentstatus WHERE transactionid=? AND role=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid),$role)));
        foreach ($result as $a_v) { $assessmentversion=$a_v['assessmentversion']; $testid=$a_v['testid']; }
      }
    if ($testid=="") { $testid=$transactionid."-".$role."-".$data['type']."-".$custcode; }
    $count =$result->count();
  }
  if ($count==0) {
    //check for type_of_txn_table
    if($type_of_txn_table == "transactions" || $type_of_txn_table == "internal"){}else{
      return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"" ]; exit();
    }

    //Insert data in assessmentstatus
      $query_insert_in_company =$session->prepare('INSERT INTO assessmentstatus(
        testid ,
        custcode ,
        custemail ,
        role ,
        status ,
        testname ,
        transactionid,
        type,
        testid_to_fetch,
        assessmentversion,
        createdate,
        effectivedate,
        email_role_status,
        type_alt
      )
      VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
      $session->execute($query_insert_in_company,array('arguments'=>array(
        $testid,
        $custcode,
        $email,
        $role,
        "0",
        $data['testname'],
        new \Cassandra\Uuid($transactionid),
        $data['type'],
        $testid_to_fetch_question,
        "new",
        new \Cassandra\Timestamp(),
        new \Cassandra\Timestamp(),
        "1",
        $assessment_type_alt
      )));

      if ($acf_flag=="1") {
        load_question_to_response_acf($companycode,$custcode,$email,$transactionid,$type_of_txn_table,$lawSel,$role,$testid,$version_to_txn);
      }else {
        load_question_to_response($companycode,$custcode,$email,$transactionid,$data['type'],$role,$testid,$testid_to_fetch_question,$version_to_txn);
      }
    }else {
     if ($data['type']=='privacy_notice') {
       $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
       return $arr_return;
     }else {
       $ccheck = check_for_question_in_assessment($companycode, $email, $role, $transactionid, $data,$assessmentversion,$testid,$acf_flag);
       if(!$ccheck['success']) { return $ccheck; exit(); }

       if($ccheck['reload']){
        if($reload){
          assessmentstatus_write($companycode, $email, $role, $transactionid, false);
        }else{
           return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>"An unexpected error occurred." ]; 
        }
       }else{
        return $ccheck;
       }
     }
    }
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param string $companycode
 * @param string $email
 * @param string $role
 * @param string $transactionid
 * @param string $type_of_txn_table
 * @param string $type
 * @param string $role
 * @param string $testid
 * @param string $version_to_txn
 */
function load_question_to_response_acf($companycode,$custcode,$email,$transactionid,$type_of_txn_table,$type,$role,$testid,$version_to_txn)
{
  //Load question by ACF
  if ($type_of_txn_table=='transactions') {

    $load_assessment_question_by_acf=load_assessment_question_by_acf($companycode,$custcode,$email,$transactionid,$role,$testid,$type,$version_to_txn);
    if (!$load_assessment_question_by_acf['success']) { 
      return $load_assessment_question_by_acf; exit(); 
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
    return $arr_return;
  }else{
    //$type_of_txn_table = internal
    $load_assessment_question_by_acf=load_assessment_question_by_acf_internal($companycode,$custcode,$email,$transactionid,$role,$testid,$type,$version_to_txn);
    if (!$load_assessment_question_by_acf['success']) { 
      return $load_assessment_question_by_acf; exit(); 
    }
    $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
    return $arr_return;
  }
}

/**
 * @param string $companycode
 * @param string $email
 * @param string $role
 * @param string $transactionid
 * @param array $assessmentstatus_write_arr
 * @param string $assessmentversion
 * @param string $testid_t_ff
 */
function check_for_question_in_assessment($companycode, $email, $role, $transactionid, $assessmentstatus_write_arr,$assessmentversion,$testid_t_ff,$acf_flag)
{
try{
  global $session;
    $output = false; $reload = false;
    switch ($assessmentstatus_write_arr['type']) {
      case 'vendor':
      if ($assessmentversion=='new') { $result=$session->execute($session->prepare('SELECT testid FROM assessmentstatus WHERE transactionid=? AND role=? AND custemail=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid),$role,$email))); }
      else{ $result=$session->execute($session->prepare('SELECT testid FROM assessmentstatus WHERE transactionid=? AND role=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid),$role))); }
      foreach ($result as $row) {
        $res=$session->execute($session->prepare('SELECT assessmentid FROM tempsuppresponse WHERE testid=? ALLOW FILTERING'),array('arguments'=>array($row['testid'])));
          if ($res->count()==0) {
            $session->execute($session->prepare('DELETE FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($row['testid'])));
            $output = true; $reload = true;
          }else {
            $output = true; $reload = false;
          }
        }
        break;
      case 'privacy_notice':
        $result=$session->execute($session->prepare('SELECT testid FROM assessmentstatus WHERE transactionid=? AND role=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid),$role)));
        foreach ($result as $row) {
          $res=$session->execute($session->prepare('SELECT testid FROM privnoticeassessmentresponse WHERE testid=? ALLOW FILTERING'),array('arguments'=>array($row['testid'])));
          if ($res->count()==0) {
            $session->execute($session->prepare('DELETE FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($row['testid'])));
            $output = true; $reload = true;
          }else {
            $output = true; $reload = false;
          }
        }
        break;

      default:
        if ($assessmentversion=='new') { $result=$session->execute($session->prepare('SELECT testid FROM assessmentstatus WHERE transactionid=? AND role=? AND custemail=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid),$role,$email))); }
        else{ $result=$session->execute($session->prepare('SELECT testid FROM assessmentstatus WHERE transactionid=? AND role=? ALLOW FILTERING'),array('arguments'=>array(new \Cassandra\Uuid($transactionid),$role))); }
          foreach ($result as $row) {
            if($acf_flag == '1'){
              $res=$session->execute($session->prepare('SELECT assessmentid,questionno FROM temp_gap_acf WHERE assessmentid=?'),array('arguments'=>array($row['testid'])));
            }else{
              $res=$session->execute($session->prepare('SELECT assessmentid,questionno FROM temp_gap WHERE assessmentid=?'),array('arguments'=>array($row['testid'])));
            }
            if ($res->count()==0) {
              $session->execute($session->prepare('DELETE FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($row['testid'])));
              $output = true; $reload = true;
            }else {
              $output = true; $reload = false;
            }
          }
        
        break;
    }

    if($output){
      $arr_return=["code"=>200, "success"=>true, "reload" =>$reload, "data"=>['message' => "success", "testid" => base64_encode($testid_t_ff)] ];
      return $arr_return;
    }else{
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>"An unexpected error occurred." ]; 
    }

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}


function get_maturity_question($assessmentid, $limit, $page)
{
  global $session;
  $arr =array();
  try {
    $assessmentid = base64_decode($assessmentid);
    $assessmentid = escape_input($assessmentid);

    $result_testid= $session->execute($session->prepare('SELECT transactionid FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($assessmentid)));
    if ($result_testid->count() == 0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid testid" ]; exit();
    }

    $transactionid = $result_testid[0]['transactionid'];

    if((string)$transactionid == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid maturity transaction" ]; exit();
    }

    $result_txn= $session->execute($session->prepare('SELECT acf_flag FROM transactions WHERE transactionid=?'),array('arguments'=>array($transactionid)));
    if ($result_txn->count() == 0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid maturity transaction" ]; exit();
    }
    $acf_flag= $result_txn[0]['acf_flag'];
   
    $arr=[]; $arr_txn=[]; $total_question = 0;

    if ($acf_flag=="1") {
      $res_txn= $session->execute($session->prepare('SELECT questionno FROM temp_gap_acf WHERE assessmentid=?'),array('arguments'=>array($assessmentid)));
    }else {
      $res_txn= $session->execute($session->prepare('SELECT questionno FROM temp_gap WHERE assessmentid=?'),array('arguments'=>array($assessmentid)));
    }

    $total_question = $res_txn->count();
    foreach ($res_txn as $row_txn) {
      $arr_txn[] = trim($row_txn['questionno']);
    }

    asort($arr_txn);
    $arr_final_txn =[];
    //divide array and find specific chunks
    if(strtoupper($limit) == "ALL"){
      $arr_final_txn = $arr_txn;
      $total_index=0;
      $page = 0;
    }else{
      $limit = (int)$limit;
      if($limit<1){ $limit=1; } if($page<1){ $page=1; }
      $page = $page - 1; 
      $array_chunk=array_chunk($arr_txn,$limit,true);
      $total_index=count($array_chunk);
      if(isset($array_chunk[$page])){
          $arr_final_txn=$array_chunk[$page];
      }
    }

    
    

    foreach ($arr_final_txn as $questionno) {
      //get questionno from temp_gap
      if ($acf_flag=="1") {
        $res= $session->execute($session->prepare('SELECT quesversion,version,decision,textbox,docid,docname,score,remark FROM temp_gap_acf WHERE assessmentid=? AND questionno=?'),array('arguments'=>array($assessmentid,$questionno)));
      }else {
        $res= $session->execute($session->prepare('SELECT quesversion,version,decision,textbox,docid,docname,score,remark FROM temp_gap WHERE assessmentid=? AND questionno=?'),array('arguments'=>array($assessmentid,$questionno)));
      }

      //get question details
      foreach ($res as $tmp) {
        $result= $session->execute($session->prepare('SELECT optc,optd,opte,question FROM question_list WHERE questionno=? AND quesversion=? AND version=? ALLOW FILTERING'),array('arguments'=>array($questionno,$tmp['quesversion'],$tmp['version'])));
        if ($result->count()==0) {
          $result= $session->execute($session->prepare('SELECT optc,optd,opte,question FROM question_list_privacy WHERE questionno=? AND quesversion=? AND version=? ALLOW FILTERING'),array('arguments'=>array($questionno,$tmp['quesversion'],$tmp['version'])));
        }

        foreach ($result as $row) {
          //Option for question
          $options=array(
            "A"=>"Not Applicable",
            "B"=>"None",
            "C"=>str_replace("'","",$row['optc']),
            "D"=>str_replace("'","",$row['optd']),
            "E"=>str_replace("'","",$row['opte']),
            "F"=>"other"
          );
          $arr[]=array(
                'questionno'=>$questionno,
                'options'=>$options,
                'question'=>$row['question'],
                'decision'=>$tmp['decision'],
                'score'=>$tmp['score'],
                'textbox'=>$tmp['textbox'],
                'docid'=>$tmp['docid'],
                'docname'=>$tmp['docname'],
                'remark'=>$tmp['remark']
          );
        }
      }
    }

    $final_data=[
      "limit" => $limit,
      "page" => $page+1,
      "pagination" => $total_index,
      "total_question" => $total_question,
      "questions" => $arr
    ];

    $arr_return=["code"=>200, "success"=>true, "data"=>$final_data ];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>