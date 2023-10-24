<?php 

function GetActivityHandler($funcCallType){
  try{

    switch($funcCallType){
      case "activity":
        $page=1; $limit=10;
        if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
        if(isset($_GET["limit"])){ $limit=(int)$_GET["limit"]; } 

        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['role'])){
          $output = get_user_activity($GLOBALS['email'],$GLOBALS['companycode'], $GLOBALS['role'], $limit, $page);
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
        $page=1; $limit=10;
        if(isset($_GET["page"])){ $page=(int)$_GET["page"]; } 
        if(isset($_GET["limit"])){ $limit=(int)$_GET["limit"]; } 

        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode'])){
            $output = get_user_activity_all($GLOBALS['email'],$GLOBALS['companycode'], $limit, $page);
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

//get_user_activity
function get_user_activity($email, $companycode, $role, $limit, $page){
  try{
      global $session;

      if($email=="" || $companycode=="" || $role==""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
      }

      //validate limit and page
      if($limit<1){ $limit=1; } if($page<1){ $page=1; }

      $arr_activity = []; $arr_txn=[]; $unseen_activity = 0;

      $result = $session->execute($session->prepare("SELECT notice_no,createdate,icon_status FROM notice WHERE notice_to = ? AND notice_to_role=? AND companycode=? AND status=? AND notice_status=? ALLOW FILTERING"), array('arguments' => array(
        $email, $role, $companycode, "1", "unseen"
      )));

      //if result count is empty or 0
      if($result->count()==0){
        $arr_return=["code"=>404, "success"=>false, "message"=>E_RES_NOT_FOUND, "error"=>"" ];
        return $arr_return; exit();
      }
      
      foreach ($result as $row_txn) {
        $createdate_str=(string)$row_txn['createdate'];
        $arr_txn[(string)$row_txn['notice_no']]=(int)$createdate_str;
        if ($row_txn['icon_status'] == '') { $unseen_activity++; }
      }
      arsort($arr_txn);
      //divide array and find specific chunks
      $array_chunk=array_chunk($arr_txn,$limit,true);
      $total_index=count($array_chunk);
      if(isset($array_chunk[$page])){
        $arr_final_txn=$array_chunk[$page];
      }else{
        $arr_return=["code"=>404, "success"=>false, "message"=>E_RES_NOT_FOUND, "error"=>"" ];
        return $arr_return; exit();
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
            $date2 = (int)$now_date;
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
        "page" => $page,
        "pagination" => $total_index-1,
        "total_activity" => $result->count(),
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
function get_user_activity_all($email, $companycode, $limit, $page){
    try{
        global $session;
  
        if($email=="" || $companycode==""){
          //Bad Request Error
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
        }
  
        //validate limit and page
        if($limit<1){ $limit=1; } if($page<1){ $page=1; }
  
        $arr_activity = []; $arr_txn=[]; $unseen_activity = 0;
  
        $result = $session->execute($session->prepare("SELECT notice_no,createdate,icon_status FROM notice WHERE notice_to = ? AND companycode=? AND status=? AND notice_status=? ALLOW FILTERING"), array('arguments' => array(
          $email, $companycode, "1", "unseen"
        )));
  
        //if result count is empty or 0
        if($result->count()==0){
          $arr_return=["code"=>404, "success"=>false, "message"=>E_RES_NOT_FOUND, "error"=>"" ];
          return $arr_return; exit();
        }
        
        foreach ($result as $row_txn) {
          $createdate_str=(string)$row_txn['createdate'];
          $arr_txn[(string)$row_txn['notice_no']]=(int)$createdate_str;
          if ($row_txn['icon_status'] == '') { $unseen_activity++; }
        }
        arsort($arr_txn);
        //divide array and find specific chunks
        $array_chunk=array_chunk($arr_txn,$limit,true);
        $total_index=count($array_chunk);
        if(isset($array_chunk[$page])){
          $arr_final_txn=$array_chunk[$page];
        }else{
          $arr_return=["code"=>404, "success"=>false, "message"=>E_RES_NOT_FOUND, "error"=>"" ];
          return $arr_return; exit();
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
              $date2 = (int)$now_date;
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
          "page" => $page,
          "pagination" => $total_index-1,
          "total_activity" => $result->count(),
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