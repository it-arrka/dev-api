<?php

// require '../config.php';
// require '../crud_request_api.php';

/*
  Scheduler compliance score API based on ACF Framework
  todo: 
    1. Update ACF reference in master table. If not available push dummy data.
      a. arrkaschedulermaster [Push dummy data]
    2. Update the APIs for ACF reference
      a. This will be done in configuration and scheudler initiate means general scheduler API
    3. Create a new compliance score table for scheduler
    CREATE TABLE compliance_score_scheduler (
      companycode text,
      activityid text,
      area text,
      activity text,
      frequency text,
      role text,
      date text,
      notebookid text,
      arrkacompref text,
      status text,
      createdate timestamp,
      effectivedate timestamp,
      modifydate timestamp,
      score int,
      PRIMARY KEY ((companycode, notebookid),activityid)
   ) WITH CLUSTERING ORDER BY (activityid ASC);

*/

//Push dummy data in arrkaschedulermaster
function push_dummy_acf_in_arrkaschedulermaster(){
  try {
    global $session;
    $result= $session->execute('SELECT id FROM arrkaschedulermaster');
    foreach($result as $count => $row){
      $arrkacompref="ARR.1.".$count;
      $session->execute($session->prepare('UPDATE arrkaschedulermaster SET arrkacompref=?,modifydate=? WHERE id=?'),array('arguments'=>array(
        $arrkacompref,new \Cassandra\Timestamp(), $row['id']
      )));
    }
    return "Updated All Data";
  } catch (\Exception $e) {
    return $e;
  }
}

//Push data in companyschedulermaster
function push_dummy_acf_in_companyschedulermaster(){
  try {
    global $session;
    $result= $session->execute('SELECT id,area,activity,frequency FROM companyschedulermaster');
    foreach($result as $count => $row){
      $arrkacompref="";
      if($row['area']=="" || $row['activity']=="" || $row['frequency']==""){}else{
        //Get arrkacompref from arrkaschedulermaster
        $result_alt=$session->execute($session->prepare("SELECT arrkacompref FROM arrkaschedulermaster WHERE  area=? AND activity=? AND frequency=? ALLOW FILTERING"),array('arguments'=>array(
          $row['area'],$row['activity'],$row['frequency']
        )));
  
        if($result_alt->count() > 0){
          foreach($result_alt as $row_as){ $arrkacompref=$row_as['arrkacompref']; }
        }
      }
      
      //Update companyschedulermaster
      $session->execute($session->prepare('UPDATE companyschedulermaster SET arrkacompref=?,modifydate=? WHERE id=?'),array('arguments'=>array(
        $arrkacompref,new \Cassandra\Timestamp(), $row['id']
      )));
    }
    echo "Updated All Data";
  } catch (\Exception $e) {
    echo $e;
  }
}

//update arrkacompref in temp_defaultschedule
//update arrkacompref in defaultschedule
//update arrkacompref in editschedule

function update_arrkacompref_in_scheduler_table(){
  try {
    global $session;
    //make a scheduler table
    echo '<!DOCTYPE html>
    <html lang="en" dir="ltr"><body><pre>';

    $arr=[];
    $result_master=$session->execute("SELECT * FROM companyschedulermaster");
    foreach($result_master as $row_master){
      $arr[$row_master['transactionid']][$row_master['area']][$row_master['activity']][$row_master['frequency']][$row_master['role']]=$row_master['arrkacompref'];
    }

    // print_r($arr); exit();

    echo "<h1>START temp_defaultschedule script...</h1><hr>";
    //temp_defaultschedule
    $result_1= $session->execute('SELECT * FROM temp_defaultschedule');
    foreach($result_1 as $row_1){
      $arrkacompref="";
      if(isset($arr[$row_1['transactionid']][$row_1['area']][$row_1['activity']][$row_1['frequency']][$row_1['role']])){
        $arrkacompref=$arr[$row_1['transactionid']][$row_1['area']][$row_1['activity']][$row_1['frequency']][$row_1['role']];
      }

      //update the temp_defaultschedule
      $session->execute($session->prepare('UPDATE temp_defaultschedule SET arrkacompref=? WHERE id=?'),array('arguments'=>array(
        $arrkacompref, $row_1['id']
      )));

      echo $row_1['area']." -> ".$arrkacompref."<hr>";
    }
    echo "<h1>END temp_defaultschedule script...</h1><hr>";




    echo "<h1>START defaultschedule script...</h1><hr>";
     //defaultschedule
     $result_1= $session->execute('SELECT * FROM defaultschedule');
     foreach($result_1 as $row_1){
       $arrkacompref="";
       if(isset($arr[$row_1['transactionid']][$row_1['area']][$row_1['activity']][$row_1['frequency']][$row_1['role']])){
         $arrkacompref=$arr[$row_1['transactionid']][$row_1['area']][$row_1['activity']][$row_1['frequency']][$row_1['role']];
       }

       //update the defaultschedule
      $session->execute($session->prepare('UPDATE defaultschedule SET arrkacompref=? WHERE id=?'),array('arguments'=>array(
        $arrkacompref, $row_1['id']
      )));

       echo $row_1['area']." -> ".$arrkacompref."<hr>";
     }
    echo "<h1>END defaultschedule script...</h1><hr>";





    echo "<h1>START editschedule script...</h1><hr>";
     //editschedule
     $result_1= $session->execute('SELECT * FROM editschedule');
     foreach($result_1 as $row_1){
       $arrkacompref="";
       if(isset($arr[$row_1['transactionid']][$row_1['area']][$row_1['activity']][$row_1['frequency']][$row_1['role']])){
         $arrkacompref=$arr[$row_1['transactionid']][$row_1['area']][$row_1['activity']][$row_1['frequency']][$row_1['role']];
       }

        //update the editschedule
      $session->execute($session->prepare('UPDATE editschedule SET arrkacompref=? WHERE id=?'),array('arguments'=>array(
        $arrkacompref, $row_1['id']
      )));

       echo $row_1['area']." -> ".$arrkacompref."<hr>";
     }
    echo "<h1>END editschedule script...</h1><hr>";




    echo '</pre></body>';
  } catch (\Exception $e) {
    echo $e;
  }
}


//update_compliance_score_for_all_scheduler
function update_compliance_score_for_all_scheduler(){
  try {
   global $session;

   $result_txn= $session->execute('SELECT * FROM default_scheduler_txn');
   foreach($result_txn as $row_txn){

    $companycode=$row_txn['companycode'];
    $notebookid=$row_txn['notebookid'];
    $transactionid=$row_txn['transactionid'];

    

    $result= $session->execute($session->prepare('SELECT owneremail,actionid,arrkacompref FROM editschedule WHERE companycode=? AND notebookid=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
      $companycode,$notebookid,"1"
    )));

    if($result->count() == 0){
      $result= $session->execute($session->prepare('SELECT owneremail,actionid,arrkacompref FROM defaultschedule WHERE companycode=? AND notebookid=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
        $companycode,$notebookid,"1"
      )));
    }
 
    foreach($result as $row){
      // if($row['owneremail']!=""){
 
        //get score of this activityid
        $actionid=$row['actionid'];
        $activityid=$actionid;
        $arrkacompref=$row['arrkacompref'];
 
        $output_status=get_action_status_with_details($actionid,$notebookid,$companycode,"","","");
        if(!$output_status['success']){
          return $output_status; exit();
        }
 
        //get status
        $status_arr=$output_status['data']['status'];
        if($arrkacompref==""){ $arrkacompref="NA"; }
        
        $score=1;

        foreach($status_arr as $status_value) {
          $output_write=write_to_compliance_score_scheduler($activityid,$status_value['sequence'],$arrkacompref,$status_value['score'],$status_value['status'],$companycode,"","","");
          if(!$output_write['success']){
            return $output_write; exit();
          }
        }
 
        // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
        //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
        // )));
 
      // }
    }
   }
   $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
   return $arr_return;
    
 } catch (\Exception $e) {
   $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
   return $arr_return;
 }
}


  //  -----Actual APIS STart from Here -----

function write_to_compliance_score_scheduler($activityid,$sequence,$arrkacompref,$score,$status,$companycode,$email,$role,$custcode)
{
  try {

    global $session;
    //validate activity id and get area,activity etc
    $result_act= $session->execute($session->prepare('SELECT area,activity,role,date,notebookid,frequency FROM editschedule WHERE actionid=? ALLOW FILTERING'),array('arguments'=>array(
      $activityid
    )));

    if($result_act->count()==0){
      $result_act= $session->execute($session->prepare('SELECT area,activity,role,date,notebookid,frequency FROM defaultschedule WHERE actionid=? ALLOW FILTERING'),array('arguments'=>array(
        $activityid
      )));
    }

    //In case activity id is not present
    if($result_act->count()==0){
      $arr_return=["success"=>false,"msg"=>"Invalid activity-1","data"=>""];
      return $arr_return; exit();
    }

    //Get data from edischedule
    $area=$result_act[0]['area'];
    $activity=$result_act[0]['activity'];
    $frequency=$result_act[0]['frequency'];
    $role_scheduler=$result_act[0]['role_scheduler'];
    $date=$result_act[0]['date'];
    $notebookid=$result_act[0]['notebookid'];


    //Insert into compliance_score_scheduler
    $columns = [
      "companycode",
      "activityid",
      "sequence",
      "area",
      "activity",
      "frequency",
      "role",
      "date",
      "notebookid",
      "arrkacompref",
      "status",
      "createdate",
      "effectivedate",
      "score"
    ];

    $columns_data = [
      $companycode,
      $activityid,
      (int)$sequence,
      $area,
      $activity,
      $frequency,
      $role_scheduler,
      $date,
      $notebookid,
      $arrkacompref,
      $status,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      (int)$score
    ];
    $data_for_crud = [
      "action" => "insert",
      "table_name" => "compliance_score_scheduler",
      "columns" => $columns,
      "isCondition" => true,
      "condition_columns" => [],
      "columns_data" => $columns_data,
      "isAllowFiltering" => false
    ];
    $output = table_crud_actions($data_for_crud);
    if (!$output['success']) {
      $arr_return=["success"=>false,"msg"=>$output['msg'],"data"=>""];
      return $arr_return;
      exit();
    }

    //Log this function
    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","write to compliance_score_scheduler","1","write_to_compliance_score_scheduler",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
    return $arr_return;
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
    return $arr_return;
  }
}

function read_from_compliance_score_scheduler($notebookid,$companycode,$email,$role,$custcode){
  //compliance_score_scheduler
  try {
    global $session;

    if($notebookid==""){
      $arr_return=["success"=>true,"msg"=>"notebookid is empty","data"=>""];
      return $arr_return;
      exit();
    }

    $result= $session->execute($session->prepare('SELECT * FROM compliance_score_scheduler WHERE companycode=? AND notebookid=?'),array('arguments'=>array(
      $companycode,$notebookid
    )));

    $arr=[];
    //loop through result
    foreach($result as $row){
      $arr[]=$row;
    }
    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from read_from_compliance_score_scheduler","1","read_from_compliance_score_scheduler",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
    return $arr_return;
     
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:read_from_compliance_score_scheduler","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>$e->getMessage()];
    return $arr_return;
  }
}

function read_from_compliance_score_scheduler_by_activity($activityid,$notebookid,$companycode,$email,$role,$custcode){
  //compliance_score_scheduler
  try {
    global $session;

    if($notebookid=="" || $activityid==""){
      $arr_return=["success"=>true,"msg"=>"notebookid/activityid is empty","data"=>""];
      return $arr_return;
      exit();
    }

    $result= $session->execute($session->prepare('SELECT * FROM compliance_score_scheduler WHERE companycode=? AND notebookid=? AND activityid=?'),array('arguments'=>array(
      $companycode,$notebookid,$activityid
    )));

    $arr=[];
    //loop through result
    foreach($result as $row){
      $arr=$row;
    }
    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from read_from_compliance_score_scheduler","1","read_from_compliance_score_scheduler",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
    return $arr_return;
     
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:read_from_compliance_score_scheduler","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>$e->getMessage()];
    return $arr_return;
  }
}


//update_compliance_score_for_scheduler
function update_compliance_score_for_scheduler($transactionid,$notebookid,$companycode,$email,$role,$custcode){
   try {
    global $session;

    if($notebookid=="" || $companycode==""){
      $arr_return=["success"=>false,"msg"=>"notebookid is empty","data"=>""];
      return $arr_return;
      exit();
    }

    $result= $session->execute($session->prepare('SELECT owneremail,actionid,arrkacompref FROM editschedule WHERE companycode=? AND notebookid=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
      $companycode,$notebookid,"1"
    )));

    if($result->count()==0){
      $result= $session->execute($session->prepare('SELECT owneremail,actionid,arrkacompref FROM defaultschedule WHERE companycode=? AND notebookid=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
        $companycode,$notebookid,"1"
      )));
    }

    foreach($result as $row){
      // if($row['owneremail']!=""){

        //get score of this activityid
        $actionid=$row['actionid'];
        $activityid=$actionid;
        $arrkacompref=$row['arrkacompref'];

        $output_status=get_action_status_with_details($actionid,$notebookid,$companycode,$email,$role,$custcode);
        if(!$output_status['success']){
          return $output_status; exit();
        }

        //get status
        $status_arr=$output_status['data']['status'];
        if($arrkacompref==""){ $arrkacompref="NA"; }

        foreach($status_arr as $status_value) {
          $output_write=write_to_compliance_score_scheduler($activityid,$status_value['sequence'],$arrkacompref,$status_value['score'],$status_value['status'],$companycode,$email,$role,$custcode);
          if(!$output_write['success']){
            return $output_write; exit();
          }
        }

        

        // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
        //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
        // )));

      // }
    }

    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from read_from_compliance_score_scheduler","1","read_from_compliance_score_scheduler",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
    return $arr_return;
     
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:read_from_compliance_score_scheduler","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
    return $arr_return;
  }
}


//update_compliance_score_for_scheduler_for_activity
function update_compliance_score_for_scheduler_for_activity($activity_arr,$transactionid,$notebookid,$companycode,$email,$role,$custcode){
  try {
   global $session;

   if($notebookid=="" || $companycode==""){
     $arr_return=["success"=>false,"msg"=>"notebookid is empty","data"=>""];
     return $arr_return;
     exit();
   }

   if(count($activity_arr)==0){
    $arr_return=["success"=>false,"msg"=>"Activity list is empty","data"=>""];
    return $arr_return;
    exit();
   }

   foreach($activity_arr as $activitylist => $activitylist_val){
    $result= $session->execute($session->prepare('SELECT owneremail,actionid,arrkacompref FROM editschedule WHERE companycode=? AND notebookid=? AND status=? AND actionid=? ALLOW FILTERING'),array('arguments'=>array(
      $companycode,$notebookid,"1",$activitylist
    )));

    if($result->count()==0){
      $result= $session->execute($session->prepare('SELECT owneremail,actionid,arrkacompref FROM defaultschedule WHERE companycode=? AND notebookid=? AND status=? AND actionid=? ALLOW FILTERING'),array('arguments'=>array(
        $companycode,$notebookid,"1",$activitylist
      )));
    }
 
    foreach($result as $row){
      // if($row['owneremail']!=""){
        //get score of this activityid
        $actionid=$row['actionid'];
        $activityid=$actionid;
        $arrkacompref=$row['arrkacompref'];
 
        $output_status=get_action_status_with_details($actionid,$notebookid,$companycode,$email,$role,$custcode);
        if(!$output_status['success']){
          return $output_status; exit();
        }
 
        //get status
        $status_arr=$output_status['data']['status'];
        if($arrkacompref==""){ $arrkacompref="NA"; }
 
        foreach($status_arr as $status_value) {
          $output_write=write_to_compliance_score_scheduler($activityid,$status_value['sequence'],$arrkacompref,$status_value['score'],$status_value['status'],$companycode,$email,$role,$custcode);
          if(!$output_write['success']){
            return $output_write; exit();
          }
        }
 
        // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
        //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
        // )));
 
      // }
    }
   }   

   myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from read_from_compliance_score_scheduler","1","read_from_compliance_score_scheduler",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
   $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
   return $arr_return;
    
 } catch (\Exception $e) {
   errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:read_from_compliance_score_scheduler","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
   $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
   return $arr_return;
 }
}


//comp_score_for_scheduler
function comp_score_for_scheduler($options,$companycode,$email,$role,$custocde) {
  try {
    global $session;
    //Find if actionid from create schedule or scheduler_actiontxn
    $notebookid=$options['transactionid'];

    if($notebookid=='default') {
      //get default notebookid
      $result_notebook= $session->execute($session->prepare("SELECT notebookid FROM default_scheduler_txn WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array(
        $companycode,"1"
      )));

      if($result_notebook->count()>0){
        $notebookid=$result_notebook[0]['notebookid'];
      }
    }

    //action roles
    $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_scheduler WHERE companycode=? AND notebookid=?"),array('arguments'=>array(
      $companycode,$notebookid
    )));

    $total=0; $score=0; $comp_score=1;

    foreach($result as $row){
      $total++;
      $score=$score+$row['score'];
    }

    if($total>0){
        $comp_score=$score/$total;
        $comp_score=$comp_score*100;
        $comp_score= number_format((float)$comp_score, 0, '.', '');
    }else{
      $comp_score=0;
    }


    $arr_return=["success"=>true,"msg"=>"success","data"=>$comp_score];
    return $arr_return;
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error","1",$e,$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$_SESSION['role'],$_SERVER['HTTP_USER_AGENT'],$_SESSION['email'],$_SESSION['customer_id'],$_SESSION['companycode']);
    $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>""];
    return $arr_return;
  }
}

//get scheduler graph information
function get_scheduler_graph_data_by_compliance($notebookid,$companycode,$email,$role,$custocde){
  try{
    global $session;

    if($notebookid=='compScoreGraph' || $notebookid=='default'){
       //get default notebookid
       $result_notebook= $session->execute($session->prepare("SELECT notebookid FROM default_scheduler_txn WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array(
        $companycode,"1"
      )));

      if($result_notebook->count()>0){
        $notebookid=$result_notebook[0]['notebookid'];
      }
    }
    
    $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_scheduler WHERE companycode=? AND notebookid=?"),array('arguments'=>array(
      $companycode,$notebookid
    )));

    $open=0; $closed=0; $accept=0;
    foreach($result as $row){
      switch($row['status']){
        case 'Open':
          $open++;
          break;
        case 'Risk Accepted':
          $accept++;
          break;
        case 'Closed':
          $closed++;
          break;
      }
    }

    $arr_gdata_header=["Status","Open","Closed","Accepted"];
    $arr_final_gdata=[];
    $color=[];
    if ($open>0 || $closed>0 || $accept>0) {
      $color=["red","green","orange"];
      $arr_final_gdata=[
        $arr_gdata_header,
        ["Status",$open,$closed,$accept]
      ];
    }
    return ['data'=>$arr_final_gdata,'color'=>$color];

  }catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error","1",$e,$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$_SESSION['role'],$_SERVER['HTTP_USER_AGENT'],$_SESSION['email'],$_SESSION['customer_id'],$_SESSION['companycode']);
    return [];
  }
}

  

// print_r(push_dummy_acf_in_arrkaschedulermaster());
// push_dummy_acf_in_companyschedulermaster();
// update_arrkacompref_in_scheduler_table();
// print_r(update_compliance_score_for_all_scheduler());
?>
