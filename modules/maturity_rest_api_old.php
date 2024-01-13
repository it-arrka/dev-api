<?php

//Load the question for No ACF
function load_question_to_response($companycode,$custcode,$email,$transactionid,$type,$role,$testid,$testid_to_fetch_question,$version_to_txn)
{
  try{
    global $session;
    if ($type=="vendor") {
      //Finding Latest assessment set Version
      $version_array =array();
      $version_q= $session->execute($session->prepare('SELECT version FROM suppliersecurityassessment WHERE role LIKE ? AND testid=? ALLOW FILTERING'),array('arguments'=>array("%".$role."%",$testid_to_fetch_question)));
      foreach ($version_q as $v_q) { array_push($version_array,(int)$v_q['version']); }
      $latest_version =max($version_array);
      //query question and their latest version
      $result_q= $session->execute($session->prepare('SELECT questionno,effectivedate FROM suppliersecurityassessment WHERE role LIKE ? AND version=? AND status=? AND testid=? ALLOW FILTERING'),array('arguments'=>array("%".$role."%",(string)$latest_version,"1",$testid_to_fetch_question)));
      foreach ($result_q as $row) {
          $ques_ver= $session->execute($session->prepare('SELECT quesversion FROM suppliersecurityassessment WHERE questionno=? ALLOW FILTERING'),array('arguments'=>array($row['questionno'])));
          $question_version_array=array();
          foreach ($ques_ver as $q_v) { array_push($question_version_array,(int)$q_v);}
          $max_ques_version =max($question_version_array);
            $query_insert_in_company =$session->prepare('INSERT INTO tempsuppresponse(
              assessmentid ,
              companycode ,
              custcode ,
              questionno ,
              quesversion ,
              role ,
              testid ,
              version,
              testid_to_fetch,
              createdate,
              effectivedate
            )
            VALUES(?,?,?,?,?,?,?,?,?,?,?)');
            $session->execute($query_insert_in_company,array('arguments'=>array(
              new \Cassandra\Uuid(),
              $companycode,
              $custcode,
              $row['questionno'],
              (string)$max_ques_version,
              $role,
              $testid,
              (string)$latest_version,
              $testid_to_fetch_question,
              new \Cassandra\Timestamp(),
              new \Cassandra\Timestamp()
            )));
        }
        $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
        return $arr_return;
      }

    //For Privacy Notice
    elseif ($type=="privacy_notice") {
      //Finding Latest assessment set Version
      $version_array =array();
      $version_q= $session->execute('SELECT version FROM privnoticeassessment');
      foreach ($version_q as $v_q) { array_push($version_array,(int)$v_q['version']); }
      $latest_version =max($version_array);
      //query question and their latest version
      $result_q= $session->execute('SELECT questionno,effectivedate FROM privnoticeassessment');
      foreach ($result_q as $row) {
        // if (date_validation($row['effectivedate'])==1) {
          $ques_ver= $session->execute('SELECT quesversion FROM privnoticeassessment');
          $question_version_array=array();
          foreach ($ques_ver as $q_v) { array_push($question_version_array,(int)$q_v);}
          $max_ques_version =max($question_version_array);
            $query_insert_in_company =$session->prepare('INSERT INTO privnoticeassessmentresponse(
            testid,
            companycode ,
            custcode ,
            questionno ,
            quesversion ,
            version,
            createdate,
            effectivedate
            )
            VALUES(?,?,?,?,?,?,?,?)');
            $session->execute($query_insert_in_company,array('arguments'=>array(
              $testid,
              $companycode,
              $custcode,
              $row['questionno'],
              (string)$max_ques_version,
              (string)$latest_version,
              new \Cassandra\Timestamp(),
              new \Cassandra\Timestamp()
            )));
          
          }
          $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
          return $arr_return;
      }
      //RBIUCB - SEBI
      elseif ($type=="RBIUCB" || $type=="SEBI" || $type=="security" || $type=="privacy" || $type=="gdpr" || $type=="india" || $type=="bahrain" || $type=="NISTCSF") {
        //Finding Latest assessment set Version
        if ($version_to_txn=='4') {
          $sec_priv_ques = sec_priv_ques($companycode,$custcode,$email,$transactionid,$role,$testid,$type,$version_to_txn);
          if(!$sec_priv_ques['success']){
            return $sec_priv_ques; exit();
          }
          $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
          return $arr_return;
        }else {
          $sec_priv_ques_pre = sec_priv_ques_pre($companycode,$custcode,$email,$transactionid,$role,$testid,"security",$type,$version_to_txn);
          if(!$sec_priv_ques_pre['success']){
            return $sec_priv_ques_pre; exit();
          }
          $sec_priv_ques_pre = sec_priv_ques_pre($companycode,$custcode,$email,$transactionid,$role,$testid,"privacy",$type,$version_to_txn);
          if(!$sec_priv_ques_pre['success']){
            return $sec_priv_ques_pre; exit();
          }
          $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
          return $arr_return;
         }
        }
        //Load question by ACF
        elseif ((int)$version_to_txn>4) {
          $load_assessment_question_by_acf=load_assessment_question_by_acf($role,$testid,$type,$version_to_txn);
          if (!$load_assessment_question_by_acf['success']) {
            return $load_assessment_question_by_acf; exit();
          }
          $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
          return $arr_return;
        }
      elseif (strpos($type, '_ia') !== false) {
            $type_ia=explode("_",$type);
            $result_q_ia=$session->execute($session->prepare('SELECT ques_ref FROM internal_audit_master WHERE role=? AND type=? ALLOW FILTERING'),array('arguments'=>array($role,$type_ia[0])));
            foreach ($result_q_ia as $row_q_ia) {
            $query_insert_gdpr_ia =$session->prepare('INSERT INTO temp_gap(
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
            $session->execute($query_insert_gdpr_ia,array('arguments'=>array(
              $testid,
              $companycode,
              $custcode,
              $email,
              $row_q_ia['ques_ref'],
              "1",
              $role,
              "1",
              $transactionid,
              new \Cassandra\Timestamp(),
              new \Cassandra\Timestamp()
            )));
            }
            $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success", "testid" => base64_encode($testid)] ];
            return $arr_return;
        }else {
          return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"" ]; exit();
        }
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
  
}

function sec_priv_ques($companycode,$custcode,$email,$transactionid,$role,$testid,$type,$version_to_txn)
{
  try {
  global $session;
  $latest_version =(int)$version_to_txn;

  //ldispname for lawmap
  $ldispname=' ';
  $result_ldispname= $session->execute($session->prepare('SELECT dispname FROM lawkeys WHERE usingkey=? ALLOW FILTERING'),array('arguments'=>array($type)));
  $ldispname=$result_ldispname[0]['dispname'];

  //Loading arrkaref

  $check_arf_arr=array(); $check_ques_arr=array();
  $result_arrkaref= $session->execute($session->prepare('SELECT larrkaref FROM lawmap WHERE ldispname=? AND status=? ALLOW FILTERING'),array('arguments'=>array($ldispname,"1")));
  foreach ($result_arrkaref as $row_arf) {

    //Make sure unique arrkaref goes in
    if (in_array($row_arf['larrkaref'],$check_arf_arr)) { }else{
      //Enter arrkaref to check array
      array_push($check_arf_arr,$row_arf['larrkaref']);

      //Fetch questionno from questionlist
      $result_ques= $session->execute($session->prepare('SELECT questionno FROM question_list WHERE qrolecreator LIKE ? AND version=? AND qstatus=? AND qarrkaref=? ALLOW FILTERING'),array('arguments'=>array("%".$role."%",(string)$latest_version,"1",$row_arf['larrkaref'])));
      foreach ($result_ques as $row_ques) {

        //Make sure unique questionno goes in
        if (in_array($row_ques['questionno'],$check_ques_arr)) { }else{
          //Enter questionno to check array
          array_push($check_ques_arr,$row_ques['questionno']);

        //Make sure of latest quesversion --- Query Again for each questionno to check if there is another version--Find max version
        $ques_ver= $session->execute($session->prepare('SELECT quesversion FROM question_list WHERE qrolecreator LIKE ? AND version=? AND qstatus=? AND qarrkaref=? AND questionno=? ALLOW FILTERING'),array('arguments'=>array("%".$role."%",(string)$latest_version,"1",$row_arf['larrkaref'],$row_ques['questionno'])));
        $question_version_array=array();
        foreach ($ques_ver as $q_v) { array_push($question_version_array,(int)$q_v['quesversion']);}
        $max_ques_version =max($question_version_array);

        //Lets populate temp_gap
        $query_insert_in_company =$session->prepare('INSERT INTO temp_gap(
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
          $row_ques['questionno'],
          (string)$max_ques_version,
          $role,
          (string)$latest_version,
          $transactionid,
          new \Cassandra\Timestamp(),
          new \Cassandra\Timestamp()
        )));
      }
    }
   }
  }
  $arr_return=["code"=>200, "success"=>true, "data"=>[] ];
  return $arr_return;

  } catch (\Exception $e) {
    $session->execute($session->prepare('DELETE FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($testid)));
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

//Logic for version < 4
function sec_priv_ques_pre($companycode,$custcode,$email,$transactionid,$role,$testid,$a_type,$type,$version_to_txn)
{
  global $session;
  $latest_version =(int)$version_to_txn;

  if ($a_type=="security") { $result_q= $session->execute($session->prepare('SELECT questionno,effectivedate FROM question_list WHERE qrolecreator LIKE ? AND version=? AND qstatus=? ALLOW FILTERING'),array('arguments'=>array("%".$role."%",(string)$latest_version,"1"))); }
  else { $result_q= $session->execute($session->prepare('SELECT questionno,effectivedate FROM question_list_privacy WHERE qrolecreator LIKE ? AND version=? AND qstatus=? ALLOW FILTERING'),array('arguments'=>array("%".$role."%",(string)$latest_version,"1"))); }
  //query question and their latest version
  if ($type=="RBIUCB") { $result_arrkaref_v= $session->execute('SELECT arrkaref FROM rbiucbmaster'); }
  if ($type=="SEBI") { $result_arrkaref_v= $session->execute('SELECT arrkaref FROM sebimaster'); }
  if ($type=="security") { $result_arrkaref_v= $session->execute('SELECT arrkaref FROM isoquesmapping'); }
  if ($type=="privacy") { $result_arrkaref_v= $session->execute('SELECT arrkaref FROM bsquesmapping'); }
  if ($type=="gdpr") { $result_arrkaref_v= $session->execute('SELECT arrkaref FROM gdprmaster'); }
  if ($type=="india") { $result_arrkaref_v= $session->execute('SELECT arrkaref FROM indialawmaster'); }
  if ($type=="bahrain") { $result_arrkaref_v= $session->execute('SELECT arrkaref FROM bahrainmaster'); }

  $hit_arr=array();
  $hit_arr_f=array();
  $ques_arr=array();
  foreach ($result_arrkaref_v as $hit) { $arrkaref_arr=explode("|",$hit['arrkaref']); foreach ($arrkaref_arr as $hit_ak) { array_push($hit_arr,$hit_ak); } }
  foreach ($hit_arr as $ar_ref) {
    $ques_av= $session->execute($session->prepare('SELECT armquestionno FROM arrkarefmapping WHERE armarrkarefno=? ALLOW FILTERING'),array('arguments'=>array($ar_ref)));
    foreach ($ques_av as $qv) { array_push($hit_arr_f,$qv['armquestionno']); }
  }
  foreach ($result_q as $ques) { array_push($ques_arr,$ques['questionno']); }
  $final_arr =array_intersect($hit_arr_f,$ques_arr);
  foreach ($final_arr as $row) {
    if ($a_type=="security") { $ques_ver= $session->execute($session->prepare('SELECT quesversion FROM question_list WHERE questionno=? ALLOW FILTERING'),array('arguments'=>array($row))); }
    else { $ques_ver= $session->execute($session->prepare('SELECT quesversion FROM question_list_privacy WHERE questionno=? ALLOW FILTERING'),array('arguments'=>array($row))); }
      $question_version_array=array();
      foreach ($ques_ver as $q_v) { array_push($question_version_array,(int)$q_v['quesversion']);}
      $max_ques_version =max($question_version_array);

      if($a_type=='security'){
        $final_validation_of_ques= $session->execute($session->prepare('SELECT quesversion FROM question_list WHERE questionno=? AND qrolecreator=? AND quesversion=? AND qstatus=? ALLOW FILTERING'),array('arguments'=>array($row,$_SESSION['role'],(string)$max_ques_version,"1")));
      }else {
        $final_validation_of_ques= $session->execute($session->prepare('SELECT quesversion FROM question_list_privacy WHERE questionno=? AND qrolecreator=? AND quesversion=? AND qstatus=? ALLOW FILTERING'),array('arguments'=>array($row,$_SESSION['role'],(string)$max_ques_version,"1")));
      }

      try {
        if($final_validation_of_ques->count()>0){
        $query_insert_in_company =$session->prepare('INSERT INTO temp_gap(
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
          $row,
          (string)$max_ques_version,
          $role,
          (string)$latest_version,
          $transactionid,
          new \Cassandra\Timestamp(),
          new \Cassandra\Timestamp()
        )));
      }
      $arr_return=["code"=>200, "success"=>true, "data"=>[] ];
      return $arr_return;
      }catch (\Exception $e) {
        $session->execute($session->prepare('DELETE FROM assessmentstatus WHERE testid=?'),array('arguments'=>array($testid)));
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
      }
  }
}

?>