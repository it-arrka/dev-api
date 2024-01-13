<?php

function GetSchedulerHandler($funcCallType){
    try{
      switch($funcCallType){
        case "add-activity":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }
            
            if(isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])){
                $output = add_scheduler_activity($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
                if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
                }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;

        case "report":
            $page=1; $limit="ALL"; $date_from = ""; $date_to = "";
            if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
            if(isset($_GET["limit"])){ $limit=$_GET["limit"]; } 
            if(isset($_GET["date_from"])){ $date_from=$_GET["date_from"]; }           
            if(isset($_GET["date_to"])){ $date_to=$_GET["date_to"]; }           
            if(isset($GLOBALS['companycode']) && isset($GLOBALS['email'])){
                $output = get_scheduler_report($GLOBALS['companycode'], $date_from, $date_to, $limit, $page);
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

//
function get_scheduler_notebook_id($companycode){
    try {
        global $session;
        $default_notebookid = "";
        $result_sch= $session->execute($session->prepare("SELECT notebookid FROM default_scheduler_txn WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
        
        foreach ($result_sch as $row) {
          $default_notebookid = $row['notebookid'];
        }

        return $default_notebookid;
        
    } catch (\Exception $e) {
        return "";
    }
}

function add_scheduler_activity($companycode, $activeEmail, $activeRole, $data)
{   
  try {
    global $session; 
    //validate data
    $required_keys = ["frequency", "area", "activity", "role", "email", "date"];

    //check if array is valid
    if(!checkKeysExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    //check if data is not null
    if(!checkValueExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>implode(", ",$required_keys)." value is mandatory" ]; exit();
    }

    $frequency = escape_input($data['frequency']);
    $area = escape_input($data['area']);
    $activity = escape_input($data['activity']);
    $role = escape_input($data['role']);
    $email = escape_input($data['email']);
    $date = escape_input($data['date']);

    if(!isDateInRange($date, date("Y-m-d"))){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>$date." Date must not be less than Today Date" ]; exit();
    }

    $name = get_name_from_email($email);
    $activeCustcode = get_custcode_from_email($activeEmail);

    //validate email & role combination
    $email_role_check = check_if_email_role_exist_in_company($companycode, $email, $role);
    if(!$email_role_check){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email role combination doesnot exist" ]; exit();
    }

    //get config transactionid
    $txn_id = get_active_config_txn_id($companycode, "scheduler");
    if($txn_id == ""){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid configuration" ]; exit();
    }


      $default_notebookid=new \Cassandra\Uuid();
      $result_sch= $session->execute($session->prepare("SELECT * FROM default_scheduler_txn WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
      if($result_sch->count() == 0)
      {
        $session->execute($session->prepare("INSERT INTO default_scheduler_txn(
          id,
          companycode,
          createdate,
          effectivedate,
          status,
          screen_status,
          notebookname,
          notebookid,
          transactionid,
          report_status
      ) VALUES(?,?,?,?,?,?,?,?,?,?) "),array('arguments'=>array(
          $default_notebookid,
          $companycode,
          new \Cassandra\Timestamp(),
          new \Cassandra\Timestamp(),
          "1",
          "edit",
          "My Scheduler",
          (string)$default_notebookid,
          (string)$txn_id,
          "0"
          )));
      }else{
       $default_notebookid=$result_sch[0]['notebookid'];
      }


      //insert data into default scheduler
      $id = new \Cassandra\Uuid();
      $timestamp = new \Cassandra\Timestamp();
        $columns = [
          "id",
          "actionid",
          "actionstatus",
          "createdate",
          "effectivedate",
          "filleremail",
          "fillercustcode",
          "companycode",
          "transactionid",
          "area",
          "activity",
          "frequency",
          "role",
          "date",
          "owneremail",
          "ownername",
          "notebookname",
          "notebookid",
          "status",
          "screen_status"
        ];
        $columns_data = [
          $id, 
          (string)$id,
          "0",
          $timestamp,
          $timestamp, 
          $activeEmail,
          $activeCustcode,
          $companycode,
          $txn_id,
          $area,
          $activity,
          $frequency,
          $role,
          $date,
          $email,
          $name,
          "My Scheduler",
          (string)$default_notebookid,
          "1",
          "edit"
        ];
        $data_for_insert = [
          "action" => "insert",
          "table_name" => "defaultschedule",
          "columns" => $columns,
          "isCondition" => false,
          "condition_columns" => "",
          "columns_data" => $columns_data,
          "isAllowFiltering" => false
        ];

        $table_insert = table_crud_actions($data_for_insert);
        if(!$table_insert['success']){
            return $table_insert; exit();
        }

      //notice update for existing notification
      notice_update_all($default_notebookid, $companycode, $email, $role, "SC01");
      notice_update_all($default_notebookid, $companycode, $email, $role, "SC02");

      //create new notice
      $email_role_array=module_assign_email_role_list("PG056","modify",$companycode);
      $notice_link="edit_schedule.php?tid=".$txn_id."&wid=".(string)$default_notebookid;
      foreach ($email_role_array as $em_role) {
        notice_write("SC01",$companycode, $activeEmail, $activeRole,$notice_link,$em_role['email'],$em_role['role'],"My Scheduler",(string)$default_notebookid);
      }
      $arr_return=["code"=>200, "success"=>true, "data"=>['message' => 'success']];
      return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}


function get_scheduler_report($companycode, $date_from, $date_to, $limit, $page)
{
  try {
    global $session; $arr = []; $arr_txn = [];

    //get config transactionid
    $tid = get_active_config_txn_id($companycode, "scheduler");
    if($tid == ""){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Configuration is not complete." ]; exit();
    }

    //get config notebookid 
    $notebookid = get_scheduler_notebook_id($companycode);
    if($notebookid == ""){
        $arr_return=["code"=>200, "success"=>true, "data"=>[]]; return $arr_return; exit();
    }

    //handle date
    $date_from_seconds = (int)strtotime($date_from);
    $date_to_seconds = (int)strtotime($date_to);

    if($date_to_seconds == 0){ $date_to_seconds = 2147483647000; }
    
    $editSchedule = true;
    $result_txn=$session->execute($session->prepare("SELECT id,date,owneremail FROM editschedule WHERE companycode=? AND notebookid=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,$notebookid,"1")));
    if ($result_txn->count()==0) {
      $editSchedule = true;
      $result_txn=$session->execute($session->prepare("SELECT id,date,owneremail FROM defaultschedule WHERE companycode=? AND notebookid=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,$notebookid,"1")));
    }

    foreach ($result_txn as $row_txn) {
        if($row_txn['owneremail']!=''){
            $dateseconds = strtotime($row_txn['date']);
            if($dateseconds >= $date_from_seconds){
                if($dateseconds <= $date_to_seconds){
                    $arr_txn[(string)$row_txn['id']] = $dateseconds;
                }
            }
        }
    }



    asort($arr_txn);
    $arr_final_txn =[];
    //divide array and find specific chunks
    if(strtoupper($limit) == "ALL"){
      $arr_final_txn = $arr_txn;
      $total_index = 0;
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

    $total_activity = count($arr_final_txn);

    foreach ($arr_final_txn as $id => $dateint) {
        if($editSchedule){
            $result=$session->execute($session->prepare("SELECT * FROM editschedule WHERE id=?"),array('arguments'=>array(new \Cassandra\Uuid($id))));
        }else{
            $result=$session->execute($session->prepare("SELECT * FROM defaultschedule WHERE id=?"),array('arguments'=>array(new \Cassandra\Uuid($id))));
        }

        foreach ($result as $row) {
              $row['id'] = (string)$row['id'];
              unset($row['effectivedate']);
              $row['actionstatus'] = trim($row['actionstatus']);
              $row['createdate'] = get_date_by_timestamp($row['createdate']);
              $row['modifydate'] = get_date_by_timestamp($row['modifydate']);
              $row['date']=date("d-m-Y", $dateint);
            //   $arr_def_ct=1;
            //   $result_schedule=$session->execute($session->prepare("SELECT actionstatus FROM defaultschedule WHERE actionid=? ALLOW FILTERING"),array('arguments'=>array($row['actionid'])));
            //   $row['actionstatus']= $result_schedule[0]['actionstatus'];
            //   if($row['actionstatus']=='' || $row['actionstatus']=='0'){ $arr_def_ct=0; }
      
              //find next scheduler action
            //   $arr_scheduleraction_alt=array();
            //   $arr_scheduleraction_alt[$row['actionstatus']][]=array("actionstatus"=>$row['actionstatus'],"actionid"=>$row['actionid'],"notebookname"=>$row['notebookname']);
      
            //   $result_schedule_alt=$session->execute($session->prepare("SELECT * FROM scheduler_actiontxn WHERE refid=? ALLOW FILTERING"),array('arguments'=>array($row['actionid'])));
            //   foreach ($result_schedule_alt as $row_salt) {
            //     if($arr_def_ct==1){ if($row_salt['actionstatus']=='' || $row_salt['actionstatus']=='0'){ $arr_def_ct=0; }  }
            //     $arr_scheduleraction_alt[$row_salt['actionstatus']][]=$row_salt;
            //   }
            //   $temp_price_for_ques_sort=array_column($arr_scheduleraction_alt,'sorting'); array_multisort($temp_price_for_ques_sort, SORT_ASC, $arr_scheduleraction_alt);
      
            // //   $row['arr_def_ct']=$arr_def_ct;
            //   $row['schedule_alt_action']=$arr_scheduleraction_alt;
            $arr[]=$row;
          }
    }

    if($date_to == ""){ $date_to = "ALL"; }
    if($date_from == ""){ $date_from = "ALL"; }
    $final_data=[
        "limit" => $limit,
        "date_from" => $date_from,
        "date_to" => $date_to,
        "page" => $page+1,
        "pagination" => $total_index,
        "total_activity" => $total_activity,
        "activity_list" => $arr
      ];

    $arr_return=["code"=>200, "success"=>true, "data"=>$final_data]; return $arr_return; exit();

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>