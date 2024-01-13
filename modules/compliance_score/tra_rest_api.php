<?php 

// Action Params - id in tra_form_1

// require '../config.php';
// require '../crud_request_api.php';

/*
CREATE TABLE compliance_score_tra (
    companycode text,
    traid text,
    actionrefid text,
    sequence int,
    arrkacompref text,
    createdate timestamp,
    effectivedate timestamp,
    modifydate timestamp,
    score int,
    status text,
PRIMARY KEY ((companycode), traid, sequence));
alter table tra_data add arrkacompref text;
*/

//update_compliance_score_for_tra_for_all
function update_compliance_score_for_tra_for_all(){
    try {
     global $session;
     $result_txn= $session->execute($session->prepare('SELECT id,transaction_id,companycode FROM tra_form_1 WHERE status=? ALLOW FILTERING'),array('arguments'=>array(
       "1"
     )));

     foreach ($result_txn as $row_txn){
        $companycode=$row_txn['companycode'];
        if($row_txn['transaction_id']==''){ $row_txn['transaction_id']=''; }

        $result= $session->execute($session->prepare('SELECT id,arrkacompref,wid FROM tra_data WHERE companycode=? AND status=? AND tid=? AND wid=?'),array('arguments'=>array(
            $companycode,"1",$row_txn['transaction_id'],(string)$row_txn['id']
          )));
        $count=0;
        foreach($result as $row){
            $traid=$row['wid'];
            $arrkacompref=$row['arrkacompref'];
            if($arrkacompref==""){ $arrkacompref="NA"; }
            $actionrefid=(string)$row['id'];
            
            // get action status for tra
            $output_status=get_action_status_with_details($actionrefid,$traid,$companycode,"","","");
            if(!$output_status['success']){
                echo $output_status['msg']; exit();
            }
    
            //get status
            $status_arr=$output_status['data']['status'];
    
            foreach($status_arr as $status_value) {

                $sequence=(int)$status_value['sequence']+$count;

                $output_write=write_to_compliance_score_tra(
                $traid,
                $actionrefid,
                $sequence,
                $arrkacompref,
                $status_value['score'],
                $status_value['status'],
                $companycode,"","",""
                );
                
                //check if write was successful
                if(!$output_write['success']){
                   echo $output_write['msg']."<hr>";
                }
            }

            $count=$count+count($status_arr);
             //Updation in final table. Since ACF is not available. We'll update later
             // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
             //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
             // )));
     
         }
     
     }
     echo "Successfully Updated";

   } catch (\Exception $e) {
     echo $e;
   }
 }

// ----Actual APIS --------------------------------
function write_to_compliance_score_tra($traid,$actionrefid,$sequence,$arrkacompref,$score,$status,$companycode,$email,$role,$custcode)
{
  try {
    global $session;
    //validate traid id

    if($traid=="" || $actionrefid==""){
        $arr_return=["success"=>false,"msg"=>"Invalid tra-id","data"=>""];
        return $arr_return; exit();
      }

    //Insert into compliance_score_tra
    $columns = [
        "companycode",
        "traid",
        "actionrefid",
        "sequence",
        "arrkacompref",
        "createdate",
        "effectivedate",
        "score",
        "status"
    ];

    $columns_data = [
      $companycode,
      $traid,
      $actionrefid,
      (int)$sequence,
      $arrkacompref,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      (int)$score,
      $status
    ];
    $data_for_crud = [
      "action" => "insert",
      "table_name" => "compliance_score_tra",
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
    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","write to write_to_compliance_score_tra","1","write_to_write_to_compliance_score_tra",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
    return $arr_return;
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
    return $arr_return;
  }
}

function read_from_compliance_score_tra($companycode,$email,$role,$custcode){
    try {
      global $session;
  
      $result= $session->execute($session->prepare('SELECT * FROM compliance_score_tra WHERE companycode=?'),array('arguments'=>array(
        $companycode
      )));
  
      $arr=[];
      //loop through result
      foreach($result as $row){
        $arr[]=$row;
      }
      myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from compliance_score_tra","1","compliance_score_tra",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
      return $arr_return;
       
    } catch (\Exception $e) {
      errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:compliance_score_tra","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>$e->getMessage()];
      return $arr_return;
    }
  }

  function read_compliance_score_tra_by_specific_tra($traid,$companycode,$email,$role,$custcode){
    try {
        global $session;
    
        $result= $session->execute($session->prepare('SELECT * FROM compliance_score_tra WHERE companycode=? AND incidentid=?'),array('arguments'=>array(
          $companycode,$traid
        )));
    
        $arr=[];
        //loop through result
        foreach($result as $row){
          $arr[]=$row;
        }
        myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from compliance_score_tra","1","compliance_score_tra",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
        return $arr_return;
         
      } catch (\Exception $e) {
        errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:compliance_score_tra","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>$e->getMessage()];
        return $arr_return;
      }
  }

//update_compliance_score_for_tra
function update_compliance_score_for_tra($companycode,$email,$role,$custcode){
    try {
     global $session;
 
     if($companycode==""){
       $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
       return $arr_return;
       exit();
     }
 
     $result_txn= $session->execute($session->prepare('SELECT id,transaction_id FROM tra_form_1 WHERE companycode=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
       $companycode,"1"
     )));

     foreach($result_txn as $row_txn) {

        if($row_txn['transaction_id']==''){ $row_txn['transaction_id']=''; }
        $result= $session->execute($session->prepare('SELECT id,arrkacompref,wid FROM tra_data WHERE companycode=? AND status=? AND tid=? AND wid=?'),array('arguments'=>array(
            $companycode,"1",$row_txn['transaction_id'],(string)$row_txn['id']
        )));

        foreach($result as $row){
            $traid=$row['wid'];
            $arrkacompref=$row['arrkacompref'];
            if($arrkacompref==""){ $arrkacompref="NA"; }
            $actionrefid=(string)$row['id'];
            
            // get action status for tra
            $output_status=get_action_status_with_details($actionrefid,$traid,$companycode,$email,$role,$custcode);
            if(!$output_status['success']){
                return $output_status; exit();
            }
    
            //get status
            $status_arr=$output_status['data']['status'];
    
            foreach($status_arr as $status_value) {
                $output_write=write_to_compliance_score_tra(
                $traid,
                $actionrefid,
                (int)$status_value['sequence'],
                $arrkacompref,
                $status_value['score'],
                $status_value['status'],
                $companycode,$email,$role,$custcode
                );
                
                //check if write was successful
                if(!$output_write['success']){ return $output_write; exit(); }
            }
             //Updation in final table. Since ACF is not available. We'll update later
             // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
             //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
             // )));
     
         }

     }
 
     myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_tra","1","update_compliance_score_for_tra",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
     return $arr_return;
      
   } catch (\Exception $e) {
     errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_tra","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
     return $arr_return;
   }
 }

 //update_compliance_score_for_tra_by_traid
function update_compliance_score_for_tra_by_traid($tra_arr,$companycode,$email,$role,$custcode){
    try {
     global $session;
 
     if($companycode==""){
       $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
       return $arr_return;
       exit();
     }

     if(count($tra_arr)==0){
        $arr_return=["success"=>false,"msg"=>"TRA id is empty","data"=>""];
        return $arr_return;
        exit();
      }

      $tra_considered_arr=[];

      foreach($tra_arr as $tralist => $tralist_val){

        //tralist == ACTION REF ID == TRA_DATA =ID
        //tralist_val == ACTION TXN ID == TRA_DATA =WID
        //check if $tralist_val is already considered
        
        if(!in_array($tralist_val,$tra_considered_arr)){
          
          array_push($tra_considered_arr,$tralist_val);

          $result_txn= $session->execute($session->prepare('SELECT transaction_id FROM tra_form_1 WHERE id=?'),array('arguments'=>array(
            new \Cassandra\Uuid($tralist_val)
          )));
          $count=0;
          foreach($result_txn as $row_txn){
  
            $result= $session->execute($session->prepare('SELECT id,arrkacompref,wid FROM tra_data WHERE companycode=? AND status=? AND tid=? AND wid=?'),array('arguments'=>array(
              $companycode,"1",$row_txn['transaction_id'],$tralist_val
            )));
  
              foreach($result as $row){
                  $actionrefid=(string)$row['id'];
                  $traid=$row['wid'];
                  $arrkacompref=$row['arrkacompref'];
                  if($arrkacompref==""){ $arrkacompref="NA"; }
                  // get action status for tra
                  $output_status=get_action_status_with_details($actionrefid,$row['wid'],$companycode,$email,$role,$custcode);
                  if(!$output_status['success']){
                      return $output_status; exit();
                  }
                  //get status
                  $status_arr=$output_status['data']['status'];
  
                  foreach($status_arr as $status_value) {
  
                      $sequence=(int)$status_value['sequence']+$count;
  
                      $output_write=write_to_compliance_score_tra(
                      $traid,
                      $actionrefid,
                      $sequence,
                      $arrkacompref,
                      $status_value['score'],
                      $status_value['status'],
                      $companycode,$email,$role,$custcode
                      );
                      //check if write was successful
                      if(!$output_write['success']){ return $output_write; exit(); }
                  }
  
                  //Upgrade the counter with size of incoming action array
                  $count=$count+count($status_arr);
  
              }
          }
        }
     }
 
     myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_tra","1","update_compliance_score_for_tra",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
     return $arr_return;
      
   } catch (\Exception $e) {
     errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_tra","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
     return $arr_return;
   }
 }

 //get compliance score for tra
 function comp_score_for_tra($companycode,$email,$role,$custocde) {
    try {
      global $session;
      //action roles
      $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_tra WHERE companycode=?"),array('arguments'=>array(
        $companycode
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

 //get compliance score for specific tra
 function comp_score_for_secific_tra($options,$companycode,$email,$role,$custocde) {
   try {
     global $session;

     $traid="";
     $actionrefid="";
     if(isset($options['traid'])){
        $traid=$options['traid'];
     }

     if(isset($options['actionrefid'])){
        $actionrefid=$options['actionrefid'];
     }

     //action roles
     $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_tra WHERE companycode=? AND traid=?"),array('arguments'=>array(
       $companycode,$traid
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

 //get tra graph information
function get_tra_graph_data_by_compliance($companycode,$email,$role,$custocde){
  try{
    global $session;

    $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_tra WHERE companycode=?"),array('arguments'=>array(
      $companycode
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
 
//  update_compliance_score_for_tra_for_all();

?>