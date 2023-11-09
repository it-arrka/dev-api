<?php
// Action Params - 
//     irworkflowid [DPO]
//     irid [Security]

// require '../config.php';
// require '../crud_request_api.php';

/*
CREATE TABLE compliance_score_incident (
    companycode text,
    incidentid text,
    actionrefid text,
    sequence int,
    category text,
    arrkacompref text,
    createdate timestamp,
    effectivedate timestamp,
    modifydate timestamp,
    score int,
    status text,
PRIMARY KEY ((companycode), incidentid, sequence));
*/

// --Script APIs --
//update_compliance_score_for_incident_for_all_company
function update_compliance_score_for_incident_for_all_company(){
    try {
     global $session;
     $result= $session->execute($session->prepare('SELECT irworkflowid,irid,ircompanycode FROM incidentraise WHERE status=? ALLOW FILTERING'),array('arguments'=>array(
       "1"
     )));
 
     foreach($result as $row){
        //get workflowid
         $companycode=$row['ircompanycode'];
         $irworkflowid=$row['irworkflowid'];

         //check data in incidentanalyse
         $result_analyse= $session->execute($session->prepare('SELECT iaprivrelation,arrkacompref FROM incidentanalyse WHERE iacompanycode=? AND status=? AND iaworkflowid=? ALLOW FILTERING'),array('arguments'=>array(
            $companycode,"1",$irworkflowid
          )));

          if($result_analyse->count()>0){
            $irid=(string)$row['irid'];

            $iaprivrelation=$result_analyse[0]['iaprivrelation'];
            $arrkacompref=$result_analyse[0]['arrkacompref'];
            if($arrkacompref==""){ $arrkacompref="NA"; }

            $actionid_arr=[$irid];
            //Check if Privacy relation is Yes
            if($iaprivrelation=='Yes'){ array_push($actionid_arr,$irworkflowid); }

            //Now we have array of actionrefid. Now get actions details
            $count=0;
            foreach($actionid_arr as $actionrefid){
                $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,"","","");
                if(!$output_status['success']){
                    return $output_status; exit();
                }

                //get status
                $status_arr=$output_status['data']['status'];
               
        
                foreach($status_arr as $status_value) {
                $sequence=(int)$status_value['sequence']+$count;

                 $output_write=write_to_compliance_score_incident(
                    $irworkflowid,
                    $actionrefid,
                    $sequence,
                    $arrkacompref,
                    $status_value['score'],
                    $status_value['status'],
                    $companycode,$email,$role,$custcode
                  );
                  
                  //check if write was successful
                  if(!$output_write['success']){ echo $output_write['msg']; exit(); }


                  echo "Success<hr>";
                }

                //increment counter for count to the size of the array
                //0 = 0+0,1+0,2+0,3+0
                //4 = 0+4,1+4,2+4,3+4
                $count=$count+count($status_arr);

                
            }
          }
 
         //Updation in final table. Since ACF is not available. We'll update later
         // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
         //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
         // )));
 
     }
 
  echo "<hr>********************************SUCCESSFUL";
      
   } catch (\Exception $e) {
    echo "<hr>********************************Error\n".(string)$e;
   }
 }




// ----Actual APIS --------------------------------
function write_to_compliance_score_incident($incidentid,$actionrefid,$sequence,$arrkacompref,$score,$status,$companycode,$email,$role,$custcode)
{
  try {
    // $category,$incidentno,$incidentnofixed
    global $session;
    //validate activity id and get area,activity etc
    $result_act= $session->execute($session->prepare('SELECT iaincidentsubcategory FROM incidentanalyse WHERE iaworkflowid=? AND status=? AND iacompanycode=? ALLOW FILTERING'),array('arguments'=>array(
        $incidentid,"1",$companycode
    )));

    //In case activity id is not present
    if($result_act->count()==0){
      $arr_return=["success"=>false,"msg"=>"Invalid Incident","data"=>""];
      return $arr_return; exit();
    }

    //Get data from edischedule
    $category=$result_act[0]['iaincidentsubcategory'];


    //Insert into compliance_score_scheduler
    $columns = [
        "companycode",
        "incidentid",
        "actionrefid",
        "sequence",
        "category",
        "arrkacompref",
        "createdate",
        "effectivedate",
        "score",
        "status"
    ];

    $columns_data = [
      $companycode,
      $incidentid,
      $actionrefid,
      (int)$sequence,
      $category,
      $arrkacompref,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      (int)$score,
      $status
    ];
    $data_for_crud = [
      "action" => "insert",
      "table_name" => "compliance_score_incident",
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
    myLog_new($_SERVER['REMOTE_ADDR'],"AL002","write to write_to_compliance_score_incident","1","write_to_write_to_compliance_score_incident",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    
    $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
    return $arr_return;
  } catch (\Exception $e) {
    errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
    $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>(string)$e];
    return $arr_return;
  }
}

function read_from_compliance_score_incident($companycode,$email,$role,$custcode){
    try {
      global $session;
  
      $result= $session->execute($session->prepare('SELECT * FROM compliance_score_incident WHERE companycode=?'),array('arguments'=>array(
        $companycode
      )));
  
      $arr=[];
      //loop through result
      foreach($result as $row){
        $arr[]=$row;
      }
      myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from read_from_compliance_score_incident","1","read_from_compliance_score_incident",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
      return $arr_return;
       
    } catch (\Exception $e) {
      errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:read_from_compliance_score_incident","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
      $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>(string)$e];
      return $arr_return;
    }
  }

  function read_from_compliance_score_incident_by_specific_incident($incidentid,$companycode,$email,$role,$custcode){
    try {
        global $session;
    
        $result= $session->execute($session->prepare('SELECT * FROM compliance_score_incident WHERE companycode=? AND incidentid=?'),array('arguments'=>array(
          $companycode,$incidentid
        )));
    
        $arr=[];
        //loop through result
        foreach($result as $row){
          $arr[]=$row;
        }
        myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from read_from_compliance_score_incident","1","read_from_compliance_score_incident",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>true,"msg"=>"Succes","data"=>$arr];
        return $arr_return;
         
      } catch (\Exception $e) {
        errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:read_from_compliance_score_incident","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
        $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>(string)$e];
        return $arr_return;
      }
  }

  //update_compliance_score_for_incident
function update_compliance_score_for_incident($companycode,$email,$role,$custcode){
    try {
     global $session;
 
     if($companycode==""){
       $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
       return $arr_return;
       exit();
     }
 
     $result= $session->execute($session->prepare('SELECT irworkflowid,irid FROM incidentraise WHERE ircompanycode=? AND status=? ALLOW FILTERING'),array('arguments'=>array(
       $companycode,"1"
     )));
 
     foreach($result as $row){
        //get workflowid
         $irworkflowid=$row['irworkflowid'];

         //check data in incidentanalyse
         $result_analyse= $session->execute($session->prepare('SELECT iaprivrelation,arrkacompref FROM incidentanalyse WHERE iacompanycode=? AND status=? AND iaworkflowid=? ALLOW FILTERING'),array('arguments'=>array(
            $companycode,"1",$irworkflowid
          )));

          if($result_analyse->count()>0){
            $irid=(string)$row['irid'];

            $iaprivrelation=$result_analyse[0]['iaprivrelation'];
            $arrkacompref=$result_analyse[0]['arrkacompref'];
            if($arrkacompref==""){ $arrkacompref="NA"; }

            $actionid_arr=[$irid];
            //Check if Privacy relation is Yes
            if($iaprivrelation=='Yes'){ array_push($actionid_arr,$irworkflowid); }

            //Now we have array of actionrefid. Now get actions details
            $count=0;
            foreach($actionid_arr as $actionrefid){
                $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,$email,$role,$custcode);
                if(!$output_status['success']){
                    return $output_status; exit();
                }

                //get status
                $status_arr=$output_status['data']['status'];
        
                foreach($status_arr as $status_value) {
                $sequence=(int)$status_value['sequence']+$count;

                 $output_write=write_to_compliance_score_incident(
                    $irworkflowid,
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
                
                //increment counter for count to the size of the array
                //0 = 0+0,1+0,2+0,3+0
                //4 = 0+4,1+4,2+4,3+4
                $count=$count+count($status_arr);
            }
          }
 
         //Updation in final table. Since ACF is not available. We'll update later
         // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
         //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
         // )));
 
     }
 
     myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_incident","1","update_compliance_score_for_incident",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
     return $arr_return;
      
   } catch (\Exception $e) {
     errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_incident","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
     $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>(string)$e];
     return $arr_return;
   }
 }

//  update_compliance_score_for_incident_by_id
 function update_compliance_score_for_incident_by_id($id_arr,$companycode,$email,$role,$custcode){
  try {
   global $session;

   if($companycode==""){
     $arr_return=["success"=>false,"msg"=>"companycode is empty","data"=>""];
     return $arr_return;
     exit();
   }

   if(count($id_arr)==0){
    $arr_return=["success"=>false,"msg"=>"Incident id is empty","data"=>""];
    return $arr_return;
    exit();
  }

  foreach($id_arr as $idlist => $idlist_val){
    $result= $session->execute($session->prepare('SELECT irworkflowid,irid FROM incidentraise WHERE ircompanycode=? AND status=? AND irid=? ALLOW FILTERING'),array('arguments'=>array(
      $companycode,"1",new \Cassandra\Uuid($idlist)
    )));

    if($result->count()==0){
      $result= $session->execute($session->prepare('SELECT irworkflowid,irid FROM incidentraise WHERE ircompanycode=? AND status=? AND irworkflowid=? ALLOW FILTERING'),array('arguments'=>array(
        $companycode,"1",$idlist
      )));
    }
 
    foreach($result as $row){
       //get workflowid
        $irworkflowid=$row['irworkflowid'];
 
        //check data in incidentanalyse
        $result_analyse= $session->execute($session->prepare('SELECT iaprivrelation,arrkacompref FROM incidentanalyse WHERE iacompanycode=? AND status=? AND iaworkflowid=? ALLOW FILTERING'),array('arguments'=>array(
           $companycode,"1",$irworkflowid
         )));
 
         if($result_analyse->count()>0){
           $irid=(string)$row['irid'];
 
           $iaprivrelation=$result_analyse[0]['iaprivrelation'];
           $arrkacompref=$result_analyse[0]['arrkacompref'];
           if($arrkacompref==""){ $arrkacompref="NA"; }
 
           $actionid_arr=[$irid];
           //Check if Privacy relation is Yes
           if($iaprivrelation=='Yes'){ array_push($actionid_arr,$irworkflowid); }
 
           //Now we have array of actionrefid. Now get actions details
           $count=0;
           foreach($actionid_arr as $actionrefid){
               $output_status=get_action_status_with_details($actionrefid,$actionrefid,$companycode,$email,$role,$custcode);
               if(!$output_status['success']){
                   return $output_status; exit();
               }
 
               //get status
               $status_arr=$output_status['data']['status'];
       
               foreach($status_arr as $status_value) {
               $sequence=(int)$status_value['sequence']+$count;
 
                $output_write=write_to_compliance_score_incident(
                   $irworkflowid,
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
               
               //increment counter for count to the size of the array
               //0 = 0+0,1+0,2+0,3+0
               //4 = 0+4,1+4,2+4,3+4
               $count=$count+count($status_arr);
           }
         }
 
        //Updation in final table. Since ACF is not available. We'll update later
        // $session->execute($session->prepare("INSERT INTO compliance_score_acf(createdate,effectivedate,companycode,arrkacompref,module,law,score,transactionid) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
        //   new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,$arrkacompref,'scheduler','All',(string)$score,$notebookid
        // )));
 
    }
  }
   myLog_new($_SERVER['REMOTE_ADDR'],"AL002","read from update_compliance_score_for_incident","1","update_compliance_score_for_incident",$_SESSION['transactionid'],$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
   $arr_return=["success"=>true,"msg"=>"Succes","data"=>""];
   return $arr_return;
    
 } catch (\Exception $e) {
   errorLog($_SERVER['REMOTE_ADDR'],"ER003","database error:update_compliance_score_for_incident","1",(string)$e,"",$_SERVER['PHP_SELF'],$_SERVER['HTTP_REFERER'],session_id(),http_response_code(),$role,$_SERVER['HTTP_USER_AGENT'],$email,$custcode,$companycode);
   $arr_return=["success"=>false,"msg"=>"Error Occured $e","data"=>(string)$e];
   return $arr_return;
 }
}


 //get compliance score for specific incident
 function comp_score_for_secific_incident($options,$companycode) {
   try {
     global $session;

     $workflowid="";
     $actionrefid="";
     if(isset($options['workflowid'])){
        $workflowid=$options['workflowid'];
     }

     if(isset($options['actionrefid'])){
        $actionrefid=$options['actionrefid'];
     }

     //action roles
     $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_incident WHERE companycode=? AND incidentid=?"),array('arguments'=>array(
       $companycode,$workflowid
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
 
 
     $arr_return=["code"=>200, "success"=>true, "data"=>['comp_score'=>$comp_score]];
     return $arr_return;
   } catch (\Exception $e) {
     return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
   }
 }
 

 //get compliance score for incident
function comp_score_for_incident($companycode,$email,$role,$custocde) {
  try {
    global $session;
    //action roles
    $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_incident WHERE companycode=?"),array('arguments'=>array(
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

//get incident graph information
function get_incident_graph_data_by_compliance($companycode,$email,$role,$custocde){
  try{
    global $session;

    $result= $session->execute($session->prepare("SELECT score,status FROM compliance_score_incident WHERE companycode=?"),array('arguments'=>array(
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
 

//  update_compliance_score_for_incident_for_all_company();
?>