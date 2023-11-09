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
  
          default:
            catchErrorHandler(400,[ "message"=>E_INV_REQ, "error"=>"" ]);
            break;
      }
    }catch(Exception $e){
      catchErrorHandler($output['code'], [ "message"=>"", "error"=>(string)$e ]);
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
        
        //if result count is empty or 0
        if($result_txn->count()==0){
            $arr_return=["code"=>404, "success"=>false, "message"=>E_RES_NOT_FOUND, "error"=>"" ];
            return $arr_return; exit();
        }
        
        foreach ($result_txn as $row_txn) {
           $modifydate_str=(string)$row_txn['createdate'];
           $modifydate_int = (int)$modifydate_str/1000;

          if($modifydate_int >= $timestamp){
            $total_incident++;
            $arr_txn[(string)$row_txn['irid']] = $modifydate_int;
          }
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
            $comp_score="NADA";
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
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
      }
}


?>