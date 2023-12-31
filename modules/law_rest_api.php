<?php
//GetLawHandler
function GetLawHandler($funcCallType){
    try{
      switch($funcCallType){
        case "lawList":
          if(isset($GLOBALS['companycode']) && isset($GLOBALS['law'])){
            $output = get_applicable_law_in_company($GLOBALS['companycode'],$GLOBALS['law']);
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

//get active law list from a company
function get_applicable_law_in_company($companycode, $activeLaw)
{
  try {
    global $session;
    if($companycode==""){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }
    $law=[];
    $result_law= $session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
    foreach ($result_law as $row_law) {
      //get disp name
      if ($row_law['law']!="") {
        if($row_law['law']==$activeLaw){
          $row_law['active']=true;
        }else{
          $row_law['active']=false;
        }

        $result_disp= $session->execute($session->prepare("SELECT display_name,usingkey FROM lawkeys WHERE dispname=? ALLOW FILTERING"),array('arguments'=>array($row_law['law'])));
        $dispname=$row_law['law'];
        $key=$row_law['law'];
        if ($result_disp->count()>0) {
          $dispname=$result_disp[0]['display_name'];
          $key=$result_disp[0]['usingkey'];
        }

        //get law_tid
        $law_tid="";
        $law_version="";
        $law_desc="";
        $result_law_tid= $session->execute($session->prepare("SELECT id,version_overall,lname FROM lawmap_content_txn WHERE ldispname=? ALLOW FILTERING"),array('arguments'=>array($row_law['law'])));
        if ($result_law_tid->count()>0) {
          $law_tid=(string)$result_law_tid[0]['id'];
          $law_version=$result_law_tid[0]['version_overall'];
          $law_desc =$result_law_tid[0]['lname'];
        }

        $row_law['law_tid']=$law_tid;
        $row_law['law_version']=$law_version;
        $row_law['dispname']=$dispname;
        $row_law['key']=$key;
        $row_law['law_desc']=$law_desc;
        $law[]=$row_law;
      }
    }

    if(count($law)==0){
        return ["code"=>404, "success" => false, "message"=>E_RES_NOT_FOUND, "error"=>"" ]; exit();
    }

    //sort this array by law
    array_multisort( array_column($law, "law"), SORT_ASC, $law);

    $arr_return=["code"=>200, "success"=>true, "data"=>$law];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>