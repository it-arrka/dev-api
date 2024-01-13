<?php 

function update_assessment_comp_score($tid,$version)
{
  try {
    global $session;
    $arr=[];
    $result= $session->execute($session->prepare("SELECT updated_score,gcontrolno,gapanalysisid FROM gap_analysis_acf WHERE gtransactionid=? AND version=?"),array('arguments'=>array($tid,$version)));
    foreach ($result as $row_1st) {
      if (isset($arr[$row_1st['gcontrolno']])) {
        $arr[$row_1st['gcontrolno']]=return_lowest_score($arr[$row_1st['gcontrolno']],$row_1st['updated_score']);
      }else {
        $arr[$row_1st['gcontrolno']]=$row_1st['updated_score'];
      }
    }

    foreach ($result as $row) {
      $comp_score=$row['updated_score'];
      if (isset($arr[$row['gcontrolno']])) { $comp_score=$arr[$row['gcontrolno']]; }
      $session->execute($session->prepare("UPDATE gap_analysis_acf SET comp_score=?,modifydate=? WHERE gtransactionid=? AND version=? AND gapanalysisid=?"),array('arguments'=>array(
        $comp_score,new \Cassandra\Timestamp(),$tid,$version,$row['gapanalysisid']
      )));
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>[ 'message'=>'success' ] ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}
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
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
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
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
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
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
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
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
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
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function assessment_commit_by_acf($testid,$companycode,$email,$role,$custcode,$name)
{
  //Change status of test
  global $session;
  try {
    $transactionid=""; $gtesttype=" ";

    //Validate Testid
    $result_chk =$session->execute($session->prepare("SELECT * FROM assessmentstatus WHERE testid=?"),array('arguments'=>array(escape_input($testid))));
    if ($result_chk->count()==0) { $arr_return=['success'=>false,"msg"=>"Invalid Testid","data"=>""]; return $arr_return; exit(); }

    if ($result_chk[0]['status']=="1" || $result_chk[0]['status']=='2') {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Already submitted by another user" ]; exit();
    }

    $gtesttype=""; $law_version="1";
    $transactionid=(string)$result_chk[0]['transactionid'];
    $assessment_law=(string)$result_chk[0]['type'];
    $assessment_name=(string)$result_chk[0]['testname'];

    //get law_tid
    $law_tid=get_law_tid_by_ldispname($assessment_law);
    if($law_tid==''){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid Law" ]; exit();
    }

    //Validate Transaction
    $result_from_testtype=$session->execute($session->prepare('SELECT transactiontype,version FROM transactions WHERE transactionid=?'),array('arguments'=>array(new \Cassandra\Uuid($transactionid))));
    if ($result_from_testtype->count()==0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid Transaction" ]; exit();
    }
    $law_version=$result_from_testtype[0]['version'];
    $gtesttype=$result_from_testtype[0]['transactiontype'];


    //get lawmaster array
    $result_lawmap= $session->execute($session->prepare('SELECT sorting_order,lcontrolno,lcontroldesc,lcontrolobjno,lcontrolobjdesc,lcontroldesc,ldomain,ldomainno,arrkacompref,questionno,role FROM law_to_question_mapping WHERE law_tid=? AND version=? AND status=? AND queslist_version=?'),array('arguments'=>array($law_tid,"1","1",$law_version)));
    $arr_lawmap=[]; $name_arr=[];
    foreach ($result_lawmap as $row_lawmap) { $arr_lawmap[$row_lawmap['questionno']][$row_lawmap['role']][]=$row_lawmap; }

    $result_temp=$session->execute($session->prepare('SELECT * FROM temp_gap_acf WHERE assessmentid=?'),array('arguments'=>array(escape_input($testid))));

     foreach ($result_temp as $row) {
       $creator_name="";
       if (isset($name_arr[$row['custemail']])) {
         $creator_name=$name_arr[$row['custemail']];
       }else {
         $creator_name=get_name_from_email($row['custemail']);
         $name_arr[$row['custemail']]=$creator_name;
       }

       if($row['score']==''){ $row['score']='NA'; }

      $get_other_status=get_other_status_for_gap_entries($row['score']);
      $maturity_level=$get_other_status['maturity_level'];
      $score_status=$get_other_status['score_status'];
      $updated_status=$score_status;
      $updated_score=$row['score'];
      $comp_score=$row['score'];

      if ($updated_score=='Provision') { $updated_score="NA"; $comp_score="NA"; }

      //Fetch data from temp upload
      $question_to_update="";
      $result_qlist_commit=$session->execute($session->prepare('SELECT question FROM question_list WHERE questionno=? AND version=? AND quesversion=? ALLOW FILTERING'),array('arguments'=>array($row['questionno'],$row['version'],$row['quesversion'])));
      if ($result_qlist_commit->count()>0) { $question_to_update=$result_qlist_commit[0]['question']; }

      //handle mcq
      $mcq=''; if ($row['mcq']=='') { }else { $mcq_arr =explode("|",$row['mcq']); for ($i=1; $i <sizeof($mcq_arr) ; $i++) { $mcq .= " ".substr($mcq_arr[$i],5); } }

      //Find get_law_arr for the questionno
      $get_law_arr=[];
      if (isset($arr_lawmap[$row['questionno']][$row['role']])) { $get_law_arr=$arr_lawmap[$row['questionno']][$row['role']]; }
      foreach ($get_law_arr as $row_lp) {
        $query_insert = $session->prepare('INSERT INTO gap_analysis_acf (
          gapanalysisid,
          gquestionno,
          createdate,
          effectivedate,
          gcompanycode,
          gcontrolno,
          gcontroldesc,
          gcontrolobjno,
          gcontrolobjdesc,
          gdomainno,
          gdomain,
          gcustcode,
          gdocid,
          gdocname,
          gquestion,
          gresponse,
          grolecreator,
          gscore,
          gtestid,
          gtesttype,
          gtransactionid,
          remark,
          validate_remark,
          version,
          sorting_order,
          quesversion,
          maturity_level,
          updated_maturity_level,
          score_status,
          updated_status,
          updated_score,
          comp_score,
          creator,
          filleremail
        )
        values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $session->execute($query_insert,array('arguments'=>array(
          new \Cassandra\Uuid(),
          $row['questionno'],
          new \Cassandra\Timestamp(),
          new \Cassandra\Timestamp(),
          $companycode,
          $row_lp['lcontrolno'],
          $row_lp['lcontroldesc'],
          $row_lp['lcontrolobjno'],
          $row_lp['lcontrolobjdesc'],
          $row_lp['ldomainno'],
          $row_lp['ldomain'],
          $custcode,
          $row['docid'],
          $row['docname'],
          $question_to_update,
          $mcq.$row['decision'].$row['textbox'],
          $row['role'],
          $row['score'],
          $testid,
          $gtesttype,
          $transactionid,
          $row['remark'],
          "",
          $law_version,
          $row_lp['sorting_order'],
          $row['quesversion'],
          $maturity_level,
          $maturity_level,
          $score_status,
          $updated_status,
          $updated_score,
          $comp_score,
          $creator_name,
          $row['custemail']
        )));
      }
     }
   //assessment update
   $session->execute($session->prepare("UPDATE assessmentstatus SET status=?,modifydate=? WHERE testid=?"),array('arguments'=>array("1",new \Cassandra\Timestamp(),escape_input($testid))));

    //Update notice
    notice_update($transactionid,$companycode,$email,$role,"MA01");

    //Check if assessment is ready for validation
    $email_role_status="0";
    $result_txn=$session->execute($session->prepare('SELECT email_role_status FROM transactions WHERE transactionid=?'),array('arguments'=>array(new \Cassandra\Uuid($transactionid))));
    $result_s= $session->execute($session->prepare("SELECT testid FROM assessmentstatus WHERE transactionid=? AND status=? ALLOW FILTERING"),array('arguments'=>array(new \Cassandra\Uuid($transactionid),"1")));
    if ($result_txn[0]['email_role_status']=='1') {
    $result_em_role= $session->execute($session->prepare("SELECT email FROM email_role_map_for_assessment WHERE transactionid=? ALLOW FILTERING"),array('arguments'=>array($transactionid)));
    if ($result_em_role->count()==$result_s->count()) { $email_role_status="1"; }
    }else { if (count(rolematrix_for_assessment($row['transactiontype']))==$result_s->count()) { $email_role_status="1"; } }

    //notice create for Validation
    if ($email_role_status=="1") {
      //check for all laws and insert unmapped controls
      $lcontrol=array();
      $result_lcontrol= $session->execute($session->prepare("SELECT sorting_order,lcontrolno,lcontroldesc,lcontrolobjno,lcontrolobjdesc,lcontroldesc,ldomain,ldomainno FROM lawmap_content WHERE transactionid=? AND status=? ALLOW FILTERING"),array('arguments'=>array($law_tid,"1")));
      foreach ($result_lcontrol as $row_lcontrol) { $lcontrol[$row_lcontrol['lcontrolno']]=$row_lcontrol; }


      $result_gap_unmapped= $session->execute($session->prepare("SELECT gcontrolno FROM gap_analysis_acf WHERE gtransactionid=? AND version=?"),array('arguments'=>array($transactionid,$law_version)));
      foreach ($result_gap_unmapped as $row_gap_unmapped) {
        unset($lcontrol[$row_gap_unmapped['gcontrolno']]);
      }

      foreach ($lcontrol as $value_con) {
        $query_insert = $session->prepare('INSERT INTO gap_analysis_acf (
          gapanalysisid,gquestionno,createdate,effectivedate,gcompanycode,
          gcontrolno,gcontroldesc,gcontrolobjno,gcontrolobjdesc,gdomainno,gdomain,
          gcustcode,gdocid,gdocname,gquestion,gresponse,
          grolecreator,
          gscore,
          gtestid,
          gtesttype,
          gtransactionid,
          remark,
          validate_remark,
          version,
          sorting_order,
          quesversion,
          maturity_level,
          updated_maturity_level,
          score_status,
          updated_status,
          updated_score,
          comp_score,
          creator,
          filleremail
        )values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $session->execute($query_insert,array('arguments'=>array(
          new \Cassandra\Uuid(),"",new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$companycode,
          $value_con['lcontrolno'],$value_con['lcontroldesc'],$value_con['lcontrolobjno'],$value_con['lcontrolobjdesc'],$value_con['ldomainno'],$value_con['ldomain'],
          $custcode,"","","","",
          "",
          "NA",
          escape_input($testid),
          $gtesttype,
          $transactionid,
          "",
          "",
          $law_version,
          $value_con['sorting_order'],
          "",
          "Not Applicable",
          "Not Applicable",
          "Not Applicable",
          "Not Applicable",
          "NA",
          "NA",
          $name,
          $email
        )));
      }

      //Update comp_score for this in acf
      $update_assessment_comp_score=update_assessment_comp_score($transactionid,$law_version);
      if (!$update_assessment_comp_score['success']) {
        return $update_assessment_comp_score; exit();
      }

      //finding email for that page access
      $email_role_array=module_assign_email_role_list("PG050","modify",$companycode);
      $notice_link="multiple_report_show.php?tid=".$transactionid."&tname=".$assessment_name."&type=maturity&page_type=validate";
      foreach ($email_role_array as $em_role) {
        notice_write("MA02",$companycode,$email,$role,$notice_link,$em_role['email'],$em_role['role'],$assessment_name,$transactionid);
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>[ 'message'=>'success' ] ];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}




?>