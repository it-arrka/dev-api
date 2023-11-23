<?php 

function check_if_law_is_active_by_expirydate($companycode,$law)
{
  try {
    global $session;
    if ($companycode=="" || $law=="") {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
    }
    //validate law
    $result_pr=$session->execute($session->prepare('SELECT expirydate FROM applicablelaw WHERE companycode=? AND status=? AND law=? ALLOW FILTERING'),array('arguments'=>array($companycode,"1",$law)));
    if ($result_pr->count()==0) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
    }

    $active_flag=1; $expirydate=$result_pr[0]['expirydate'];
    $today_int = strtotime("today midnight");
    foreach($result_pr as $row_pr){
     $expirydate_int=strtotime($row_pr['expirydate']);
     if($today_int >= $expirydate_int){ $active_flag=0; $expirydate=$row_pr['expirydate']; }
    }

    $arr_return=["success"=>true, "msg"=>"success", "data"=>['status'=>$active_flag,'expirydate'=>$expirydate]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
  }
}

function get_name_email_set_by_role($companycode,$role) {
    try {
      global $session;
      $arr_email=[]; $arr=[];
      $result=$session->execute($session->prepare("SELECT rtccustemail FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtcrole=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1",$role)));
      foreach ($result as $row) { $arr_email[$row['rtccustemail']]=$row['rtccustemail']; }
      foreach ($arr_email as $email => $email_val) {
        $result_name=$session->execute($session->prepare("SELECT custfname,custlname FROM customer WHERE custemailaddress=?"),array('arguments'=>array($email)));
        foreach ($result_name as $row_name) {
          $arr[]=["name"=>$row_name['custfname']." ".$row_name['custlname'],"email"=>$email];
        }
      }
      array_multisort( array_column($arr, "name"), SORT_ASC, $arr);
      return $arr;
    } catch (\Exception $e) {
      return [];
    }
  }

function get_roles_for_laws_by_acf_maturity($law){
    try {
      global $session;
      if ($law=="") {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
      }
  
      $result=$session->execute($session->prepare("SELECT version FROM version_master WHERE module=?"),array('arguments'=>array("maturity")));
      $version = $result[0]["version"];
  
      $rolecreator=[];
  
      //Get Law Tid
      $result_lawtid= $session->execute($session->prepare('SELECT id FROM lawmap_content_txn WHERE ldispname=? ALLOW FILTERING'),array('arguments'=>array($law)));
      if ($result_lawtid->count()==0) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
      }
      $law_tid=(string)$result_lawtid[0]['id'];
  
      $result_map= $session->execute($session->prepare('SELECT questionno,role FROM law_to_question_mapping WHERE law_tid=? AND status=? AND version=? AND queslist_version=?'),array('arguments'=>array($law_tid,"1","1",$version)));
      foreach ($result_map as $row_map) {
        $rolecreator[trim($row_map['role'])]=trim($row_map['role']);
      }
      ksort($rolecreator);
      $arr_return=["code"=>200, "success"=>true, "data"=>$rolecreator ];
      return $arr_return;
    } catch (\Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
  }
  
  function get_roles_for_laws_by_acf_internal($law){
    try {
      global $session;
      if ($law=="") {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
      }
      $rolecreator=[];
  
      //Get Law Tid
      $result_lawtid= $session->execute($session->prepare('SELECT id FROM lawmap_content_txn WHERE ldispname=? ALLOW FILTERING'),array('arguments'=>array($law)));
      if ($result_lawtid->count()==0) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
      }
      $law_tid=(string)$result_lawtid[0]['id'];
  
      $result_map= $session->execute($session->prepare('SELECT questionno,role FROM law_to_question_mapping_ia WHERE law_tid=? AND status=? AND version=? AND queslist_version=?'),array('arguments'=>array($law_tid,"1","1","1")));
      foreach ($result_map as $row_map) {
        $rolecreator[trim($row_map['role'])]=trim($row_map['role']);
      }
      ksort($rolecreator);
      $arr_return=["code"=>200, "success"=>true, "data"=>$rolecreator ];
      return $arr_return;
    } catch (\Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
  }

//load_assessment_question_by_acf
function load_assessment_question_by_acf($companycode,$custcode,$email,$transactionid,$role,$testid,$ldispname,$version_to_txn)
{
  global $session;
  try {
  $latest_version =(string)$version_to_txn;
  $check_ques_arr=[];

  if ($ldispname=="") {
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
  }

  //Get Law Tid
  $result_lawtid= $session->execute($session->prepare('SELECT id FROM lawmap_content_txn WHERE ldispname=? ALLOW FILTERING'),array('arguments'=>array($ldispname)));
  if ($result_lawtid->count()==0) {
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
  }
  $law_tid=(string)$result_lawtid[0]['id'];

  $result_map= $session->execute($session->prepare('SELECT questionno FROM law_to_question_mapping WHERE law_tid=? AND status=? AND version=? AND queslist_version=? AND role=? ALLOW FILTERING'),array('arguments'=>array($law_tid,"1","1",$latest_version,$role)));
  foreach ($result_map as $row_map) {
    if (in_array($row_map['questionno'],$check_ques_arr)) { }else{
      array_push($check_ques_arr,$row_map['questionno']);
    //Lets populate temp_gap
    $query_insert_in_company =$session->prepare('INSERT INTO temp_gap_acf(
      assessmentid ,
      companycode ,
      custcode ,
      custemail,
      questionno ,
      quesversion ,
      role ,
      version,
      transactionid,
      createdate,
      effectivedate
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?)');
    $session->execute($query_insert_in_company,array('arguments'=>array(
      $testid,
      $companycode,
      $custcode,
      $email,
      $row_map['questionno'],
      (string)"1",
      $role,
      $latest_version,
      $transactionid,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp()
    )));
   }
  }
  $arr_return=["code"=>200, "success"=>true, "data"=>["message" => "success"] ];
  return $arr_return;
 }catch (\Exception $e) {
    $session->execute($session->prepare('DELETE FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($testid)));
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
  }
}

//load_assessment_question_by_acf_internal
function load_assessment_question_by_acf_internal($companycode,$custcode,$email,$transactionid,$role,$testid,$ldispname,$version_to_txn)
{
  global $session;
  try {
  $latest_version =(string)$version_to_txn;
  $check_ques_arr=[];

  if ($ldispname=="") {
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
  }

  //Get Law Tid
  $result_lawtid= $session->execute($session->prepare('SELECT id FROM lawmap_content_txn WHERE ldispname=? ALLOW FILTERING'),array('arguments'=>array($ldispname)));
  if ($result_lawtid->count()==0) {
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law" ]; exit();
  }
  $law_tid=(string)$result_lawtid[0]['id'];

  $result_map= $session->execute($session->prepare('SELECT questionno FROM law_to_question_mapping_ia WHERE law_tid=? AND status=? AND version=? AND queslist_version=? AND role=? ALLOW FILTERING'),array('arguments'=>array($law_tid,"1","1",$latest_version,$role)));
  foreach ($result_map as $row_map) {
    if (in_array($row_map['questionno'],$check_ques_arr)) { }else{
      array_push($check_ques_arr,$row_map['questionno']);
    //Lets populate temp_gap
    $query_insert_in_company =$session->prepare('INSERT INTO temp_gap_acf(
      assessmentid ,
      companycode ,
      custcode ,
      custemail,
      questionno ,
      quesversion ,
      role ,
      version,
      transactionid,
      createdate,
      effectivedate
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?)');
    $session->execute($query_insert_in_company,array('arguments'=>array(
      $testid,
      $companycode,
      $custcode,
      $email,
      $row_map['questionno'],
      (string)"1",
      $role,
      $latest_version,
      $transactionid,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp()
    )));
   }
  }
  $arr_return=["code"=>200, "success"=>true, "data"=>["message" => "success"] ];
  return $arr_return;
 }catch (\Exception $e) {
    $session->execute($session->prepare('DELETE FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($testid)));
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
  }
}



?>