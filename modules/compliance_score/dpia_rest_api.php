<?php 

// Action Params - id in dpia_txn

// require '../config.php';
// require '../crud_request_api.php';

/*
 CREATE TABLE compliance_score_dpia (
    companycode text,
    dpiaid text,
    actionrefid text,
    sequence int,
    arrkacompref text,
    createdate timestamp,
    effectivedate timestamp,
    modifydate timestamp,
    score int,
    status text,
PRIMARY KEY ((companycode), dpiaid, sequence));
*/

//update_compliance_score_for_dpia_for_all
function update_compliance_score_for_dpia_for_all(){
    try {
     global $session;
     $result= $session->execute($session->prepare('SELECT id,companycode,arrkacompref FROM dpia_txn WHERE status=? ALLOW FILTERING'),array('arguments'=>array(
       "1"
     )));
 
     foreach($result as $row){
        $companycode=$row['companycode'];
        $dpiaid=(string)$row['id'];
        $arrkacompref=$row['arrkacompref'];
        if($arrkacompref==""){ $arrkacompref="NA"; }
        $actionrefid=$dpiaid;
        
        // get action status for dpia
        $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,"","","");
        if(!$output_status['success']){
            echo $output_status['msg']; exit();
        }

        //get status
        $status_arr=$output_status['data']['status'];

        foreach($status_arr as $status_value) {
            $output_write=write_to_compliance_score_dpia(
            $dpiaid,
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
function write_to_compliance_score_dpia($dpiaid,$actionrefid,$sequence,$arrkacompref,$score,$status,$companycode,$email,$role,$custcode)
{
  try {
    global $session;
    //validate dpia id

    if($dpiaid=="" || $actionrefid==""){
        $arr_return=["success"=>false,"msg"=>"Invalid DPIA","data"=>""];
        return $arr_return; exit();
      }

    $result_act= $session->execute($session->prepare('SELECT dpia_name FROM dpia_txn WHERE id=?'),array('arguments'=>array(
        new \Cassandra\Uuid($dpiaid)
    )));

    //In case activity id is not present
    if($result_act->count()==0){
      $arr_return=["success"=>false,"msg"=>"Invalid DPIA","data"=>""];
      return $arr_return; exit();
    }

    //Insert into compliance_score_dpia
    $columns = [
        "companycode",
        "dpiaid",
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
      $dpiaid,
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
      "table_name" => "compliance_score_dpia",
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
    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","write to write_to_compliance_score_dpia","1","write_to_write_to_compliance_score_dpia",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
    return $arr_return;
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
    return $arr_return;
  }
}

function read_from_compliance_score_dpia($companycode,$email,$role,$custcode){
    try {
      global $session;
  
      $result= $session->execute($session->prepare('SELECT * FROM compliance_score_dpia WHERE companycode=?'),array('arguments'=>array(
        $companycode
      )));
  
      $arr=[];
      //loop through result
      foreach($result as $row){
        $arr[]=$row;
      }
      myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from compliance_score_dpia","1","compliance_score_dpia",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
      return $arr_return;
       
    } catch (\Exception $e) {
      errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:compliance_score_dpia","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>$e->getMessage()];
      return $arr_return;
    }
  }

  function read_compliance_score_dpia_by_specific_dpia($dpiaid,$companycode,$email,$role,$custcode){
    try {
        global $session;
    
        $result= $session->execute($session->prepare('SELECT * FROM compliance_score_dpia WHERE companycode=? AND dpiaid=?'),array('arguments'=>array(
          $companycode,$dpiaid
        )));
    
        $arr=[];
        //loop through result
        foreach($result as $row){
          $arr[]=$row;
        }
        myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from compliance_score_dpia","1","compliance_score_dpia",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
        return $arr_return;
         
      } catch (\Exception $e) {
        errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:compliance_score_dpia","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>$e->getMessage()];
        return $arr_return;
      }
  }

//update_compliance_score_for_dpia
function update_compliance_score_for_dpia($companycode,$email,$role,$custcode){
    try {
     global $session;
 
     if($companycode==""){
       $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
       return $arr_return;
       exit();
     }
 
     $result= $session->execute($session->prepare('SELECT id,arrkacompref FROM dpia_txn WHERE companycode=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
       $companycode,"1"
     )));
 
     foreach($result as $row){
        $dpiaid=(string)$row['id'];
        $arrkacompref=$row['arrkacompref'];
        if($arrkacompref==""){ $arrkacompref="NA"; }
        $actionrefid=$dpiaid;
        
        // get action status for dpia
        $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,$email,$role,$custcode);
        if(!$output_status['success']){
            return $output_status; exit();
        }

        //get status
        $status_arr=$output_status['data']['status'];

        foreach($status_arr as $status_value) {
            $output_write=write_to_compliance_score_dpia(
            $dpiaid,
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
 
     myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_dpia","1","update_compliance_score_for_dpia",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
     return $arr_return;
      
   } catch (\Exception $e) {
     errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_dpia","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
     return $arr_return;
   }
 }

 //update_compliance_score_for_dpia_by_dpiaid
function update_compliance_score_for_dpia_by_dpiaid($dpia_arr,$companycode,$email,$role,$custcode){
    try {
     global $session;
 
     if($companycode==""){
       $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
       return $arr_return;
       exit();
     }

     if(count($dpia_arr)==0){
        $arr_return=["success"=>false,"msg"=>"DPIA id is empty","data"=>""];
        return $arr_return;
        exit();
      }

      foreach($dpia_arr as $dpialist => $dpialist_val){
        $result= $session->execute($session->prepare('SELECT id,arrkacompref FROM dpia_txn WHERE companycode=? AND status=? AND id=? ALLOW FILTERING'),array('arguments'=>array(
            $companycode,"1",new \Cassandra\Uuid($dpialist)
          )));
      
          foreach($result as $row){
            $dpiaid=(string)$row['id'];
            $arrkacompref=$row['arrkacompref'];
            if($arrkacompref==""){ $arrkacompref="NA"; }
            $actionrefid=$dpiaid;
            
            // get action status for dpia
            $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,$email,$role,$custcode);
            if(!$output_status['success']){
                return $output_status; exit();
            }

            //get status
            $status_arr=$output_status['data']['status'];

            foreach($status_arr as $status_value) {
                $output_write=write_to_compliance_score_dpia(
                $dpiaid,
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
 
     myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_dpia","1","update_compliance_score_for_dpia",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
     return $arr_return;
      
   } catch (\Exception $e) {
     errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_dpia","1",$e->getMessage(),"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>$e->getMessage()];
     return $arr_return;
   }
 }

 //get compliance score for dpia
 function comp_score_for_dpia($companycode,$email,$role,$custocde) {
    try {
      global $session;
      //action roles
      $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_dpia WHERE companycode=?"),array('arguments'=>array(
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

 //get compliance score for specific dpia
 function comp_score_for_secific_dpia($options,$companycode,$email,$role,$custocde) {
   try {
     global $session;

     $dpiaid="";
     $actionrefid="";
     if(isset($options['dpiaid'])){
        $dpiaid=$options['dpiaid'];
     }

     if(isset($options['actionrefid'])){
        $actionrefid=$options['actionrefid'];
     }

     //action roles
     $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_dpia WHERE companycode=? AND dpiaid=?"),array('arguments'=>array(
       $companycode,$dpiaid
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
 
//get dpia graph information
function get_dpia_graph_data_by_compliance($companycode,$email,$role,$custocde){
    try{
      global $session;
  
      $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_dpia WHERE companycode=?"),array('arguments'=>array(
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

//  update_compliance_score_for_dpia_for_all();

?>