<?php 

function GetActivityHandler($funcCallType){
  try{

    switch($funcCallType){

    case "activityInfo":
      $limit=10; $day = "ALL";
      if(isset($_GET["limit"])){ $limit=(int)$_GET["limit"]; } 
      if(isset($_GET["day"])){ $day=$_GET["day"]; } 

      if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['role'])){
          $output = get_user_activity_info($GLOBALS['email'],$GLOBALS['companycode'], $GLOBALS['role'], $limit, $day);
          if($output['success']){
          commonSuccessResponse($output['code'],$output['data']);
          }else{
          catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
      }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
      }
      break;

      case "activity":
        $page=1; $limit=10; $day = "ALL";
        if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
        if(isset($_GET["limit"])){ $limit=(int)$_GET["limit"]; } 
        if(isset($_GET["day"])){ $day=$_GET["day"]; } 

        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['role'])){
          $output = get_user_activity($GLOBALS['email'],$GLOBALS['companycode'], $GLOBALS['role'], $limit, $page, $day);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "activityAll":
        $page=1; $limit=10; $day = "ALL";
        if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
        if(isset($_GET["limit"])){ $limit=(int)$_GET["limit"]; } 
        if(isset($_GET["day"])){ $day=$_GET["day"]; } 

        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode'])){
            $output = get_user_activity_all($GLOBALS['email'],$GLOBALS['companycode'], $limit, $page, $day);
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
    catchErrorHandler($output['code'], [ "message"=>"", "error"=>(string)$e ]);
  }
}

//get_user_activity_info
function get_user_activity_info($email, $companycode, $role, $limit, $day){
  try{
      global $session;

      if($email=="" || $companycode=="" || $role== ""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
      }

      $total_activity=0; $unseen_activity = 0; $open_activity = 0; $closed_activity = 0; $role_open = 0; 

      //validate limit and page
      if($limit<1){ $limit=1; }

      $timestamp = 0;
      if(strtoupper($day) != "ALL"){ 
        $last_day = (int)$day;
        if($last_day < 1){ $last_day = 1; }
        $timestamp = strtotime("-". $last_day. " days");
      }


      $result = $session->execute($session->prepare("SELECT createdate,modifydate,icon_status,notice_to_role FROM notice WHERE notice_to = ? AND companycode=? AND status=? AND notice_status=? ALLOW FILTERING"), array('arguments' => array(
        $email, $companycode, "1", "unseen"
      )));

      foreach($result as $row){
        $modifydate_str=(string)$row['modifydate'];
        if($modifydate_str == ""){ $modifydate_str=(string)$row['createdate']; }
        $modifydate_int = (int)$modifydate_str/1000;

        if($modifydate_int >= $timestamp){
          $total_activity++;
          $open_activity++;
          if ($row['icon_status'] == '') { $unseen_activity++; }
          if ($row['notice_to_role'] == $role) { $role_open++; }
        }
      }

      $result_closed = $session->execute($session->prepare("SELECT createdate,modifydate FROM notice WHERE notice_to = ? AND companycode=? AND status=? AND notice_status=? ALLOW FILTERING"), array('arguments' => array(
        $email, $companycode, "1", "seen"
      )));

      foreach($result_closed as $row_closed){
        $modifydate_str=(string)$row_closed['modifydate'];
        if($modifydate_str == ""){ $modifydate_str=(string)$row_closed['createdate']; }
        $modifydate_int = (int)$modifydate_str/1000;
        if($modifydate_int >= $timestamp){
          $total_activity++;
          $closed_activity++;
        }
      }

      $pagination = ceil($open_activity/$limit);

      $final_data=[
        "total_activity" => $total_activity,
        "unseen_activity" => $unseen_activity,
        "open_activity" => $open_activity,
        "closed_activity" => $closed_activity,
        "role_open" => $role_open,
        "role" => $role,
        "day" => $day,
        "limit" => $limit,
        "pagination" => $pagination
      ];

      $arr_return=["code"=>200, "success"=>true, "data"=>$final_data ];
      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
}


//get_user_activity
function get_user_activity($email, $companycode, $role, $limit, $page, $day){
  try{
      global $session;

      if($email=="" || $companycode=="" || $role==""){
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

      $arr_activity = []; $arr_txn=[]; $unseen_activity = 0; $total_activity = 0;

      $result = $session->execute($session->prepare("SELECT notice_no,createdate,modifydate,icon_status FROM notice WHERE notice_to = ? AND notice_to_role=? AND companycode=? AND status=? AND notice_status=? ALLOW FILTERING"), array('arguments' => array(
        $email, $role, $companycode, "1", "unseen"
      )));

      
      foreach ($result as $row_txn) {
        $modifydate_str=(string)$row_txn['modifydate'];
        if($modifydate_str == ""){ $modifydate_str=(string)$row_txn['createdate']; }
        $modifydate_int = (int)$modifydate_str/1000;

        if($modifydate_int >= $timestamp){
          $total_activity++;
          $arr_txn[(string)$row_txn['notice_no']] = $modifydate_int;
          if ($row_txn['icon_status'] == '') { $unseen_activity++; }
        }
      }
      arsort($arr_txn);
      $arr_final_txn = [];
      //divide array and find specific chunks
      $array_chunk=array_chunk($arr_txn,$limit,true);
      $total_index=count($array_chunk);
      if(isset($array_chunk[$page])){
        $arr_final_txn=$array_chunk[$page];
      }

      //loop through arr_txn
      foreach ($arr_final_txn as $notice_no => $createdate) {
        $result_assign = $session->execute($session->prepare("SELECT * FROM notice WHERE notice_no = ?"), array('arguments' => array(
            new \Cassandra\Uuid($notice_no)
          )));
    
        foreach ($result_assign as $row) {
            unset($row['effectivedate']);
            $modifydate = (string)$row['modifydate'];
            $row['modifydate']=(int)$modifydate;
            $row['createdate']=$createdate;

            $now_date = (string)new \Cassandra\Timestamp();
            $date2 = (int)$now_date/1000;
            $date1 = $createdate;
            // Formulate the Difference between two dates
            $diff = abs($date2 - $date1);
            $seconds = floor($diff / 1000);
            $minutes = floor($seconds / 60);
            if ($minutes > 60) {
                $hours = floor($seconds / 3600);
                $diffHours = $hours . " hrs ";
                if ($hours > 24) {
                $day = floor($hours / 24);
                $diffHours = $day . " days ";
                }
            } else {
                $diffHours = $minutes . " min ";
            }
            $row['notice_no'] = $notice_no;
            $row['link'] = $row['notice_link'];
            $row['seconds'] = $seconds;
            $row['notice_timestamp'] = $diffHours;
            $arr_activity[] = $row;
        }
      }

      $final_data=[
        "limit" => $limit,
        "day" => $day,
        "page" => $page+1,
        "pagination" => $total_index,
        "total_activity" => $total_activity,
        "unseen_activity" => $unseen_activity,
        "activity" => $arr_activity
      ];

      $arr_return=["code"=>200, "success"=>true, "data"=>$final_data ];
      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
}


//get_user_activity_all
function get_user_activity_all($email, $companycode, $limit, $page, $day){
    try{
        global $session;
  
        if($email=="" || $companycode==""){
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
  
        $arr_activity = []; $arr_txn=[]; $unseen_activity = 0; $total_activity= 0;
  
        $result = $session->execute($session->prepare("SELECT notice_no,createdate,modifydate,icon_status FROM notice WHERE notice_to = ? AND companycode=? AND status=? AND notice_status=? ALLOW FILTERING"), array('arguments' => array(
          $email, $companycode, "1", "unseen"
        )));
  
        foreach ($result as $row_txn) {
          $modifydate_str=(string)$row_txn['modifydate'];
          if($modifydate_str == ""){ $modifydate_str=(string)$row_txn['createdate']; }
          $modifydate_int = (int)$modifydate_str/1000;
  
          if($modifydate_int >= $timestamp){
            $total_activity++;
            $arr_txn[(string)$row_txn['notice_no']]=(int)$modifydate_int;
            if ($row_txn['icon_status'] == '') { $unseen_activity++; }
          }
        }
        arsort($arr_txn);
        $arr_final_txn = [];
        //divide array and find specific chunks
        $array_chunk=array_chunk($arr_txn,$limit,true);
        $total_index=count($array_chunk);
        if(isset($array_chunk[$page])){
          $arr_final_txn=$array_chunk[$page];
        }
  
        //loop through arr_txn
        foreach ($arr_final_txn as $notice_no => $createdate) {
          $result_assign = $session->execute($session->prepare("SELECT * FROM notice WHERE notice_no = ?"), array('arguments' => array(
              new \Cassandra\Uuid($notice_no)
            )));
      
          foreach ($result_assign as $row) {
              unset($row['effectivedate']);
              $modifydate = (string)$row['modifydate'];

              if($modifydate == '') {
                $modifydate ="-";
              }else{
                $modifydate = date("d-m-Y H:i:s",(int)$modifydate/1000);
              }
              $row['modifydate']=$modifydate;

              $row['createdate']=$createdate;
  
              $now_date = (string)new \Cassandra\Timestamp();
              $date2 = (int)$now_date/1000;
              $date1 = $createdate;
              // Formulate the Difference between two dates
              $diff = abs($date2 - $date1);
              $seconds = floor($diff / 1000);
              $minutes = floor($seconds / 60);
              if ($minutes > 60) {
                  $hours = floor($seconds / 3600);
                  $diffHours = $hours . " hrs ";
                  if ($hours > 24) {
                  $day = floor($hours / 24);
                  $diffHours = $day . " days ";
                  }
              } else {
                  $diffHours = $minutes . " min ";
              }
              $row['notice_no'] = $notice_no;
              $row['link'] = $row['notice_link'];
              $row['seconds'] = $seconds;
              $row['notice_timestamp'] = $diffHours;
              $arr_activity[] = $row;
          }
        }
  
        $final_data=[
          "limit" => $limit,
          "day" => $day,
          "page" => $page+1,
          "pagination" => $total_index,
          "total_activity" => $total_activity,
          "unseen_activity" => $unseen_activity,
          "activity" => $arr_activity
        ];
  
        $arr_return=["code"=>200, "success"=>true, "data"=>$final_data ];
        return $arr_return;
  
      }catch(Exception $e){
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
      }
  }

?>