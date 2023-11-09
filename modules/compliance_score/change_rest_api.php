<?php 

// Action Params - id in changerequest

// require '../config.php';
// require '../crud_request_api.php';

/*
CREATE TABLE compliance_score_change (
    companycode text,
    changeid text,
    actionrefid text,
    sequence int,
    arrkacompref text,
    createdate timestamp,
    effectivedate timestamp,
    modifydate timestamp,
    score int,
    status text,
PRIMARY KEY ((companycode), changeid, sequence));
*/

//update_compliance_score_for_change_for_all
function update_compliance_score_for_change_for_all(){
    try {
     global $session;
      echo "HELLO";
    

     $result= $session->execute($session->prepare('SELECT id,companycode,arrkacompref FROM changerequest WHERE status=? ALLOW FILTERING'),array('arguments'=>array(
       "1"
     )));
 
     foreach($result as $row){
        $companycode=$row['companycode'];
        $changeid=(string)$row['id'];
        $arrkacompref=$row['arrkacompref'];
        if($arrkacompref==""){ $arrkacompref="NA"; }
        $actionrefid=$changeid;
        
        // get action status for change
        $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,"","","");
        if(!$output_status['success']){
            echo $output_status['msg']; exit();
        }

        //get status
        $status_arr=$output_status['data']['status'];

        foreach($status_arr as $status_value) {
            $output_write=write_to_compliance_score_change(
            $changeid,
            $actionrefid,
            (int)$status_value['sequence'],
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
         //Updation in final table. Since ACF is not available. We'll update later
         // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
         //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
         // )));
 
     }
 
     echo "Successfully Updated";
      
   } catch (\Exception $e) {
     echo $e;
   }
 }

// ----Actual APIS --------------------------------
function write_to_compliance_score_change($changeid,$actionrefid,$sequence,$arrkacompref,$score,$status,$companycode,$email,$role,$custcode)
{
  try {
    global $session;
    //validate change id

    if($changeid=="" || $actionrefid==""){
        $arr_return=["success"=>false,"msg"=>"Invalid Change","data"=>""];
        return $arr_return; exit();
      }

    $result_act= $session->execute($session->prepare('SELECT crno FROM changerequest WHERE id=?'),array('arguments'=>array(
        new \Cassandra\Uuid($changeid)
    )));

    //In case activity id is not present
    if($result_act->count()==0){
      $arr_return=["success"=>false,"msg"=>"Invalid Change","data"=>""];
      return $arr_return; exit();
    }

    //Insert into compliance_score_change
    $columns = [
        "companycode",
        "changeid",
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
      $changeid,
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
      "table_name" => "compliance_score_change",
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
    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","write to write_to_compliance_score_change","1","write_to_write_to_compliance_score_change",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
    return $arr_return;
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>(string)$e];
    return $arr_return;
  }
}

function read_from_compliance_score_change($companycode,$email,$role,$custcode){
    try {
      global $session;
  
      $result= $session->execute($session->prepare('SELECT * FROM compliance_score_change WHERE companycode=?'),array('arguments'=>array(
        $companycode
      )));
  
      $arr=[];
      //loop through result
      foreach($result as $row){
        $arr[]=$row;
      }
      myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from compliance_score_change","1","compliance_score_change",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
      return $arr_return;
       
    } catch (\Exception $e) {
      errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:compliance_score_change","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>(string)$e];
      return $arr_return;
    }
  }

  function read_compliance_score_change_by_specific_change($changeid,$companycode,$email,$role,$custcode){
    try {
        global $session;
    
        $result= $session->execute($session->prepare('SELECT * FROM compliance_score_change WHERE companycode=? AND incidentid=?'),array('arguments'=>array(
          $companycode,$changeid
        )));
    
        $arr=[];
        //loop through result
        foreach($result as $row){
          $arr[]=$row;
        }
        myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from compliance_score_change","1","compliance_score_change",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
        return $arr_return;
         
      } catch (\Exception $e) {
        errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:compliance_score_change","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>(string)$e];
        return $arr_return;
      }
  }

//update_compliance_score_for_change
function update_compliance_score_for_change($companycode,$email,$role,$custcode){
    try {
     global $session;
 
     if($companycode==""){
       $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
       return $arr_return;
       exit();
     }
 
     $result= $session->execute($session->prepare('SELECT id,arrkacompref FROM changerequest WHERE companycode=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
       $companycode,"1"
     )));
 
     foreach($result as $row){
        $changeid=(string)$row['id'];
        $arrkacompref=$row['arrkacompref'];
        if($arrkacompref==""){ $arrkacompref="NA"; }
        $actionrefid=$changeid;
        
        // get action status for change
        $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,$email,$role,$custcode);
        if(!$output_status['success']){
            return $output_status; exit();
        }

        //get status
        $status_arr=$output_status['data']['status'];

        foreach($status_arr as $status_value) {
            $output_write=write_to_compliance_score_change(
            $changeid,
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
 
     myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_change","1","update_compliance_score_for_change",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
     return $arr_return;
      
   } catch (\Exception $e) {
     errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_change","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>(string)$e];
     return $arr_return;
   }
 }

 //update_compliance_score_for_change_by_changeid
function update_compliance_score_for_change_by_changeid($change_arr,$companycode,$email,$role,$custcode){
    try {
     global $session;
 
     if($companycode==""){
       $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
       return $arr_return;
       exit();
     }

     if(count($change_arr)==0){
        $arr_return=["success"=>false,"msg"=>"Change id is empty","data"=>""];
        return $arr_return;
        exit();
      }

      foreach($change_arr as $changelist => $changelist_val){
        $result= $session->execute($session->prepare('SELECT id,arrkacompref FROM changerequest WHERE companycode=? AND status=? AND id=? ALLOW FILTERING'),array('arguments'=>array(
            $companycode,"1",new \Cassandra\Uuid($changelist)
          )));
      
          foreach($result as $row){
            $changeid=(string)$row['id'];
            $arrkacompref=$row['arrkacompref'];
            if($arrkacompref==""){ $arrkacompref="NA"; }
            $actionrefid=$changeid;
            
            // get action status for change
            $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,$email,$role,$custcode);
            if(!$output_status['success']){
                return $output_status; exit();
            }

            //get status
            $status_arr=$output_status['data']['status'];

            foreach($status_arr as $status_value) {
                $output_write=write_to_compliance_score_change(
                $changeid,
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
 
     myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_change","1","update_compliance_score_for_change",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
     return $arr_return;
      
   } catch (\Exception $e) {
     errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_change","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>(string)$e];
     return $arr_return;
   }
 }

 //get compliance score for change
 function comp_score_for_change($companycode,$email,$role,$custocde) {
    try {
      global $session;
      //action roles
      $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_change WHERE companycode=?"),array('arguments'=>array(
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

 //get compliance score for specific incident
 function comp_score_for_secific_change($options,$companycode,$email,$role,$custocde) {
   try {
     global $session;

     $changeid="";
     $actionrefid="";
     if(isset($options['changeid'])){
        $changeid=$options['changeid'];
     }

     if(isset($options['actionrefid'])){
        $actionrefid=$options['actionrefid'];
     }

     //action roles
     $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_change WHERE companycode=? AND changeid=?"),array('arguments'=>array(
       $companycode,$changeid
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

 //get change graph information
function get_change_graph_data_by_compliance($companycode,$email,$role,$custocde){
  try{
    global $session;

    $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_change WHERE companycode=?"),array('arguments'=>array(
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
 
//  update_compliance_score_for_change_for_all();

?>