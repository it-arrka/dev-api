<?php 

function GetIncidentHandler($funcCallType){
    try{
  
      switch($funcCallType){
        case "list":
          $page=1; $limit=10; $day = "ALL";
          if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
          if(isset($_GET["limit"])){ $limit=(int)$_GET["limit"]; } 
          if(isset($_GET["day"])){ $day=$_GET["day"]; } 
          if(isset($GLOBALS['companycode'])){
            $output = get_incident_list($GLOBALS['companycode'], $limit, $page, $day);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "subcategory":
          $type="security";
          if(isset($_GET["type"])){ $type=$_GET["type"]; } 
          if(isset($GLOBALS['companycode'])){
            $output = get_subcategory_list($type);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        //To get incident analyse data
        case "analyse":
          if(isset($_GET['irid']) && isset($GLOBALS['companycode'])){
            $output = get_incident_analyse_data($GLOBALS['companycode'], $_GET['irid']);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        //To get incident resolve data
        case "resolve":
          if(isset($_GET['irid']) && isset($GLOBALS['companycode'])){
            $output = get_incident_resolve_data($GLOBALS['companycode'], $_GET['irid']);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        //To get specific incident investigate date
        case "investigate":
          if(isset($_GET['irid']) && isset($GLOBALS['companycode'])){
            $output = get_incident_investigate_data($GLOBALS['companycode'], $_GET['irid']);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;
        
        //To get specific incident Report
        case "report":
          if(isset($_GET['irid']) && isset($GLOBALS['companycode'])){
            $output = get_incident_report($GLOBALS['companycode'], $_GET['irid']);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

          //Write API
        case "initiate":
          $jsonString = file_get_contents('php://input');
          if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
          $json = json_decode($jsonString,true);
          if(!is_array($json)){
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
          }
          if(isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])){
            $output = initiate_incident($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
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

/**
 * @param string $screen_status // Analyze/Resolve/Investigate/Solved
 * @param string $type //dpo/ciso 
 */
function report_status_by_screen_status($screen_status, $type)
{
  $report_status='';
    switch ($screen_status) {
      case '1':
        $report_status='Analyze';
        break;
      case '2':
        $report_status='Resolve';
        break;
      case '3':
        $report_status='Investigate';
        break;
      case '4':
        $report_status='Solved';
        break;
    }
    if($report_status !=''){
      if($type=='dpo'){
          $report_status=$report_status." (Privacy)";
      }
    }
    return $report_status;
}
  
/**
 * @param string $companycode 
 * @param string $limit //limit of data in each page
 * @param string $page //number of page
 * @param string $day //last 7 day or 30 days etc.
 */
function get_incident_list($companycode, $limit, $page, $day)
{
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
        $total_incident = 0;


        $result_txn=$session->execute($session->prepare("SELECT irid,createdate FROM incidentraise WHERE ircompanycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
        
        foreach ($result_txn as $row_txn) {
           $modifydate_str=(string)$row_txn['createdate'];
           $modifydate_int = (int)$modifydate_str/1000;

          if($modifydate_int >= $timestamp){
            $total_incident++;
            $arr_txn[(string)$row_txn['irid']] = $modifydate_int;
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

        foreach ($arr_final_txn as $key_id => $value) {
          $result=$session->execute($session->prepare("SELECT createdate,irincidentno,ircustemail,irrole,irincidentcategory,irincisubcategory,iritornonit,irprivrelation,irworkflowid,screen_status,screen_status_dpo,transactionid FROM incidentraise WHERE irid=?"),array('arguments'=>array(new \Cassandra\Uuid($key_id))));
          foreach ($result as $row) {

            //Get analyse data from incident raise data
            $result_an=$session->execute($session->prepare("SELECT iaitornonit,iaincidentcategory,iaincidentsubcategory,iaprivrelation FROM incidentanalyse WHERE iaworkflowid=? ALLOW FILTERING"),array('arguments'=>array($row['irworkflowid'])));
            foreach ($result_an as $row_an) {
              if ($row_an['iaitornonit']=='') {}else{ $row['iritornonit']=$row_an['iaitornonit']; }
              if ($row_an['iaincidentcategory']=='') {}else{ $row['irincidentcategory']=$row_an['iaincidentcategory']; }
              if ($row_an['iaincidentsubcategory']=='') {}else{ $row['irincisubcategory']=$row_an['iaincidentsubcategory']; }
              if ($row_an['iaprivrelation']=='') {}else{ $row['irprivrelation']=$row_an['iaprivrelation']; }
            }

            $row['ircustname']=get_name_from_email($row['ircustemail']);
            
            //Rest of the data
            $row['screen_status']=report_status_by_screen_status($row['screen_status'],'security');
            $row['screen_status_dpo']=report_status_by_screen_status($row['screen_status_dpo'],'dpo');
            $row['id']=$key_id;
            $row['createdate']=date("d-m-Y",$value);
    
            $action_status_act=[];
            $action_status_act_dpo=[];
    
            //get comp_score
            $comp_score="";
            $comp_score_get=comp_score_for_secific_incident(['workflowid'=>$row['irworkflowid']],$companycode);
            if($comp_score_get['success']){
              $comp_score=$comp_score_get['data']['comp_score'];
            }
            $row['comp_score']=$comp_score;
    
            $row['action_txn_status']=$action_status_act;
            $row['action_txn_status_dpo']=$action_status_act_dpo;
            $arr[]=$row;
    
          }
        }
     
        $final_data=[
            "limit" => $limit,
            "day" => $day,
            "page" => $page+1,
            "pagination" => $total_index,
            "total_incident" => $total_incident,
            "incidents" => $arr
          ];
    
          $arr_return=["code"=>200, "success"=>true, "data"=>$final_data ];
          return $arr_return;
    
      } catch (\Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
      }
}

/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_raise_data($companycode, $irid){
  try {
    global $session;

    if($irid == "" || $companycode == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }


    $arr = [];
    $result=$session->execute($session->prepare("SELECT 
        createdate,form_status,form_status_dpo,ircompanycode,ircustemail,irdectlocation,irdetectiondate,irdetectiontime,irextrainfo,irhow,irimpact,irincidentcategory,irincidentno,irincidentnofixed,irincisubcategory,iritornonit,irname,irphone,irprivrelation,irreportdate,irreportlocation,irreporttime,irrole,irworkflowid,modifydate,screen_status,screen_status_dpo,status,transactionid
    FROM incidentraise WHERE irid=?"),array('arguments'=>array(new \Cassandra\Uuid($irid))));

    foreach ($result as $row) {
      if($row['ircompanycode']==$companycode && $row['status']=='1'){
        $row['screen_status']=report_status_by_screen_status($row['screen_status'],'security');
        $row['screen_status_dpo']=report_status_by_screen_status($row['screen_status_dpo'],'dpo');
        
        $createdate_str = (string)$row['createdate'];
        if($createdate_str == ''){
          $row['createdate']="";
        }else{
          $row['createdate']=date("d-m-Y", (int)$createdate_str/1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if($modifydate_str == ''){
          $row['modifydate']="";
        }else{
          $row['modifydate']=date("d-m-Y", (int)$modifydate_str/1000);
        }

        $comp_score="";
        $comp_score_get=comp_score_for_secific_incident(['workflowid'=>$row['irworkflowid']],$companycode);
        if($comp_score_get['success']){
          $comp_score=$comp_score_get['data']['comp_score'];
        }
        $row['ircustname']=get_name_from_email($row['ircustemail']);
        $row['comp_score']=$comp_score;
        
        $arr = $row;
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_analyse_data($companycode, $irid){
  try {
    global $session;

    if($irid == "" || $companycode == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    $arr = [];
    $result_txn=$session->execute($session->prepare("SELECT irworkflowid FROM incidentraise WHERE irid=?"),array('arguments'=>array(new \Cassandra\Uuid($irid))));
    foreach ($result_txn as $row_txn) {
      $result=$session->execute("SELECT * FROM incidentanalyse WHERE iacompanycode=? AND status=? AND iaworkflowid=? ALLOW FILTERING", array('arguments'=>array($companycode, "1", $row_txn['irworkflowid'])));
      foreach ($result as $row) {
        $createdate_str = (string)$row['createdate'];
        if($createdate_str == ''){
          $row['createdate']="";
        }else{
          $row['createdate']=date("d-m-Y", (int)$createdate_str/1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if($modifydate_str == ''){
          $row['modifydate']="";
        }else{
          $row['modifydate']=date("d-m-Y", (int)$modifydate_str/1000);
        }

        $row['iacustname']=get_name_from_email($row['iacustemail']);
        unset($row['iaid']);
        unset($row['effectivedate']);
        $arr = $row;
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}


/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_resolve_data($companycode, $irid){
  try {
    global $session;

    if($irid == "" || $companycode == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    $arr = [];
    $result_txn=$session->execute($session->prepare("SELECT irworkflowid FROM incidentraise WHERE irid=?"),array('arguments'=>array(new \Cassandra\Uuid($irid))));
    foreach ($result_txn as $row_txn) {
      $result=$session->execute("SELECT * FROM incidentresolve WHERE irecompanycode=? AND status=? AND ireworkflowid=? ALLOW FILTERING", array('arguments'=>array($companycode, "1", $row_txn['irworkflowid'])));
      foreach ($result as $row) {
        $createdate_str = (string)$row['createdate'];
        if($createdate_str == ''){
          $row['createdate']="";
        }else{
          $row['createdate']=date("d-m-Y", (int)$createdate_str/1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if($modifydate_str == ''){
          $row['modifydate']="";
        }else{
          $row['modifydate']=date("d-m-Y", (int)$modifydate_str/1000);
        }

        $row['irecustname']=get_name_from_email($row['irecustemail']);
        unset($row['ireid']);
        unset($row['effectivedate']);
        $arr = $row;
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param string $companycode
 * @param string $irid // Incident ID
 * @return array
 */
function get_incident_investigate_data($companycode, $irid){
  try {
    global $session;

    if($irid == "" || $companycode == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    $arr = [];
    $result_txn=$session->execute($session->prepare("SELECT irworkflowid FROM incidentraise WHERE irid=?"),array('arguments'=>array(new \Cassandra\Uuid($irid))));
    foreach ($result_txn as $row_txn) {
      $result=$session->execute("SELECT * FROM incidentinvestigate WHERE iicompanycode=? AND status=? AND iiworkflowid=? ALLOW FILTERING", array('arguments'=>array($companycode, "1", $row_txn['irworkflowid'])));
      foreach ($result as $row) {
        $createdate_str = (string)$row['createdate'];
        if($createdate_str == ''){
          $row['createdate']="";
        }else{
          $row['createdate']=date("d-m-Y", (int)$createdate_str/1000);
        }

        $modifydate_str = (string)$row['modifydate'];
        if($modifydate_str == ''){
          $row['modifydate']="";
        }else{
          $row['modifydate']=date("d-m-Y", (int)$modifydate_str/1000);
        }

        $row['iicustname']=get_name_from_email($row['iicustemail']);
        unset($row['iiid']);
        unset($row['effectivedate']);
        $arr = $row;
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function get_incident_report($companycode, $irid){
  try {
    global $session;

    if($irid == "" || $companycode == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    $raise_data = []; $analyse_data = []; $resolve_data = []; $investigating_data = []; 

    $raise_data_func = get_incident_raise_data($companycode, $irid);
    if($raise_data_func['success']){
      $raise_data = $raise_data_func['data'];
    }

    $analyse_data_func = get_incident_analyse_data($companycode, $irid);
    if($analyse_data_func['success']){
      $analyse_data = $analyse_data_func['data'];
    }

    $resolve_data_func = get_incident_resolve_data($companycode, $irid);
    if($resolve_data_func['success']){
      $resolve_data = $resolve_data_func['data'];
    }

    $investigate_data_func = get_incident_investigate_data($companycode, $irid);
    if($investigate_data_func['success']){
      $investigate_data = $investigate_data_func['data'];
    }

    $arr = [
      "raise_data" => $raise_data,
      "analyse_data" => $analyse_data,
      "resolve_data" => $resolve_data,
      "investigate_data" => $investigate_data
    ];


    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}


function get_subcategory_list($type = "security")
{
  try {
    global $session; $arr=array();
    array_push($arr,'Select Category');
    $res_subc=$session->execute("SELECT dwmworkflowsubcategory FROM defaultworkflowmaster WHERE dwmworkflowtype=? ALLOW FILTERING", array('arguments'=>array($type)));
    foreach ($res_subc as $row_subc) {  array_push($arr,$row_subc['dwmworkflowsubcategory']); }
    sort($arr);
    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

//initiate_incident
function initiate_incident($companycode, $email, $role, $data){
  try {
    global $session; 
    //validate data
    $reported_date=date("d-m-Y");
    $reported_time=date("H:i:s");
    $detected_date=date("d-m-Y");
    $detected_time=date("H:i:s");

    $incDetectedLocation = escape_input($data['incDetectedLocation']);
    $incReportedLocation = escape_input($data['incReportedLocation']);
    $incAdditionalInfo = escape_input($data['incAdditionalInfo']);
    $incHow = escape_input($data['incHow']);
    $incImpact = escape_input($data['incImpact']);
    $incName = escape_input($data['incName']);
    $incPhone = escape_input($data['incPhone']);

    if($incDetectedLocation == "addNew"){
      $incDetectedLocation = escape_input($data['incDetectedLocationNew']);
    }

    if($incReportedLocation == "addNew"){
      $incReportedLocation = escape_input($data['incReportedLocationNew']);
    }

    if($incDetectedLocation == "" || $incReportedLocation == "" || $incHow == "" || $incImpact == "" || $incName == "" || $incPhone == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Fill all mandatory fields" ]; exit();
    }
    

    $config_tid  = get_active_config_txn_id($companycode, "incident");
    if($config_tid == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid configuration" ]; exit();
    }

    $subcategory = escape_input($data['incSubcategory']);
    $incidentcategory = "";
    if($subcategory == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid subcategory" ]; exit();
    }

    $result_cat= $session->execute($session->prepare("SELECT dwnworkflowcategory FROM defaultworkflowmaster WHERE dwmworkflowsubcategory=? AND dwmworkflowtype=? ALLOW FILTERING"),array('arguments'=>array($subcategory,"security")));
    if($result_cat -> count() == 0){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid subcategory" ]; exit();
    }
    $incidentcategory = $result_cat[0]['dwnworkflowcategory'];


    $result_inc= $session->execute($session->prepare("SELECT count(*) FROM incidentraise WHERE ircompanycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
    $incidentno="INC0000".($result_inc[0]['count']+1);

    $form_status_dpo='';
    $irprivrelation = "No";
    if($data['incPrivacy']==true){ $form_status_dpo='1'; $irprivrelation = "Yes"; }

    $iritornonit = "Non-IT";
    if($data['incIt']==true){ $iritornonit = "IT"; }
    

    //get custcode
    $custcode = get_custcode_from_email($email);

    $workflowid=(string)new \Cassandra\Uuid();
    $query_insert =$session->prepare('INSERT INTO incidentraise(
      irid,createdate,effectivedate,irworkflowid,ircompanycode,ircustcode,ircustemail,
      irdectlocation,irdetectiondate,irdetectiontime,irextrainfo,irhow,irimpact,irincidentcategory,
      irincisubcategory,iritornonit,irname,irphone,irprivrelation,
      irreportdate,irreporttime,irreportlocation,status,transactionid,form_status,screen_status,irincidentno,irrole,
      form_status_dpo,screen_status_dpo,irincidentnofixed
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $session->execute($query_insert,array('arguments'=>array(
      new \Cassandra\Uuid(),new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$workflowid,$companycode,$custcode,$email,
      $incDetectedLocation,$detected_date,$detected_time,$incAdditionalInfo,$incHow,$incImpact,$incidentcategory,
      $subcategory,$iritornonit,$incName,$incPhone,$irprivrelation,
      $reported_date,$reported_time,$incReportedLocation,"1",$config_tid,"1","1",$incidentno,$role,
      $form_status_dpo,$form_status_dpo,(string)new \Cassandra\Uuid()
    )));

    // incidentanalyse workflowid insertion
    $query_insert_ia =$session->prepare('INSERT INTO incidentanalyse(iaid,iaworkflowid,iacompanycode,transactionid) VALUES(?,?,?,?)');
    $session->execute($query_insert_ia,array('arguments'=>array(new \Cassandra\Uuid(),$workflowid,$companycode,$config_tid)));

    // incidentresolve workflowid insertion
    $query_insert_ire =$session->prepare('INSERT INTO incidentresolve(ireid,ireworkflowid,irecompanycode,transactionid)VALUES(?,?,?,?)');
    $session->execute($query_insert_ire,array('arguments'=>array(new \Cassandra\Uuid(),$workflowid,$companycode,$config_tid)));

    // incidentinvestigate workflowid insertion
    $query_insert_ii =$session->prepare('INSERT INTO incidentinvestigate(iiid,iiworkflowid,iicompanycode,transactionid) VALUES(?,?,?,?)');
    $session->execute($query_insert_ii,array('arguments'=>array(new \Cassandra\Uuid(),$workflowid,$companycode,$config_tid)));

    // incidentreport workflowid insertion
    $query_insert_irp =$session->prepare('INSERT INTO incidentreport(irpid,irpworkflowid,irpcompanycode,transactionid) VALUES(?,?,?,?)');
    $session->execute($query_insert_irp,array('arguments'=>array(new \Cassandra\Uuid(),$workflowid,$companycode,$config_tid)));
    // $session->execute($session->prepare("UPDATE incidentraise SET form_status=? WHERE irid=?"),array('arguments'=>array("1",new \Cassandra\Uuid($_POST['wid_incident_1']))));

    //Create notification noDPO
    $itNonIt=$iritornonit;
    if($itNonIt=='IT'){ $itNonIt='IT-Security'; }
    $result_analyse=$session->execute("SELECT ccmemail,ccmrole FROM companyconfigmaster WHERE ccmcompanycode=? AND transactionid=? AND status=? AND ccmteamcategory=? AND ccmteamtitle=? ALLOW FILTERING", array('arguments'=>array($companycode,$config_tid,"1","FPC",$itNonIt)));
    if($result_analyse->count()>0){
      foreach ($result_analyse as $row_analyse) {
        $notice_link="incident_analyze.php?tid=".(string)$config_tid."&wid=".$workflowid;
        notice_write("IN01",$companycode,$email,$role,$notice_link,$row_analyse['ccmemail'],$row_analyse['ccmrole'],$incidentno,$workflowid);
      }
    }else{
      $notice_link="incident_analyze.php?tid=".(string)$config_tid."&wid=".$workflowid;
      notice_write("IN01",$companycode,$email,$role,$notice_link,"","",$incidentno,$workflowid);
    }
    

    //If privacy is yes DPO
    $priv_yes_no=$irprivrelation;
    if($priv_yes_no=='Yes'){
    $result_priv=$session->execute("SELECT ccmemail,ccmrole FROM companyconfigmaster WHERE ccmcompanycode=? AND transactionid=? AND status=? AND ccmteamcategory=? AND ccmteamtitle=? ALLOW FILTERING", array('arguments'=>array($companycode,$config_tid,"1","FPC","DPO")));
    
    if($result_priv->count()>0){
      foreach ($result_priv as $row_priv) {
        $notice_link="incident_analyze_dpo.php?tid=".(string)$config_tid."&wid=".$workflowid;
        notice_write("IN02",$companycode,$email,$role,$notice_link,$row_priv['ccmemail'],$row_priv['ccmrole'],$incidentno,$workflowid);
       }
    }else{
      $notice_link="incident_analyze_dpo.php?tid=".(string)$config_tid."&wid=".$workflowid;
      notice_write("IN02",$companycode,$email,$role,$notice_link,"","",$incidentno,$workflowid);
    }
   }


    $arr_return=["code"=>200, "success"=>true, "data"=>['incidentno' => $incidentno, "message" => "Incident initiated successfully"] ];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>