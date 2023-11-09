<?php 

function GetGovernanceHandler($funcCallType){
    try{
  
      switch($funcCallType){
        case "steps":
          if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['role']) && isset($GLOBALS['law'])){
            $output = get_governance_steps($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'],  $GLOBALS['law']);
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
  

function get_governance_steps($companycode, $email, $role, $law)
{
  try {
    global $session;

    $pageAccess = get_page_access_by_pageid($companycode, $email, $role, "create", "PG068");
    if(!$pageAccess['success']){
        return $pageAccess; exit();
    }

    $landing_status=' ';
    $arr=array(); $arr_n=array(); $next_status='1';
    $result_law= $session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? AND law=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1",$law)));

    if ($result_law->count()==0) {
        $arr_return=["code"=>404, "success"=>false, "message"=>E_RES_NOT_FOUND, "error"=>"" ];
        return $arr_return; exit();
    }

    
    $arr_seq=array(); $count_completion=0;
    $result_diy= $session->execute("SELECT * FROM diytrackermaster");
    $total_status = $result_diy->count();
    foreach ($result_diy as $row) {
      $result_f= $session->execute($session->prepare("SELECT id,createdate,modifydate,fillercustcode FROM companydiytracker WHERE companycode=? AND law=? AND sequence=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,$law,$row['sequence'],"1")));
      if ($result_f->count()==0) {
        $row['sorting_sequence']=(int)$row['sequence'];
        $row['task_status']=false; $next_status='0';
        $row['lastupdatedon']='';
        $row['lastupdatedby']='';
      }else {
        array_push($arr_seq,(int)$row['sequence']);
        $row['sorting_sequence']=(int)$row['sequence']*100;
        $row['task_status']=true;
        $count_completion++;

        $createdate=(string)$result_f[0]['modifydate'];
        if ($createdate=='') {
          $createdate=(string)$result_f[0]['createdate'];
        }

        $row['lastupdatedon']=date("d-m-Y",(int)$createdate/1000);

        if ($result_f[0]['fillercustcode']=='') {
          $row['lastupdatedby']='';
        }else {
          $result_email= $session->execute($session->prepare("SELECT custemailaddress FROM customer WHERE custcode=? ALLOW FILTERING"),array('arguments'=>array($result_f[0]['fillercustcode'])));
          $row['lastupdatedby']=$result_email[0]['custemailaddress'];
        }
      }

      $row['lastupdatedbyname']="-";
      if ($row['lastupdatedon']=='') { if($row['task_status']==false){ $row['lastupdatedon']='Waiting to Start'; } }
      if ($row['lastupdatedby']=='') { if($row['task_status']==false){ $row['lastupdatedby']='-'; } }else { $row['lastupdatedbyname']=get_name_from_email($row['lastupdatedby']); }


      $row['id']=(string)$row['id'];


      $row['sequence']=(int)$row['sequence'];

      unset($row['createdate']);
      unset($row['effectivedate']);
      unset($row['modifydate']);
      unset($row['law']);
      $arr[]=$row;
    }

    if ($next_status=='1') {
      if((int)$landing_status<6) {
      $new_landing_status=(int)$landing_status+1;
      $session->execute($session->prepare("UPDATE company SET landing_status=? WHERE companycode=?"),array('arguments'=>array((string)$new_landing_status,$companycode)));
      }
     }

    foreach ($arr as $temp_key_for_ques => $row_for_required_ques)
    {
       $temp_price_for_ques[$temp_key_for_ques] = $row_for_required_ques["sorting_sequence"];
    }
    array_multisort($temp_price_for_ques, SORT_ASC, $arr);

    foreach ($arr as $key => $value) {
      $arr_n[$value['domain']][]=$value;
    }

    $result_pf= $session->execute($session->prepare("SELECT profilestatus,cregisteredaddress1 FROM company WHERE companycode=?"),array('arguments'=>array($companycode)));

    $company_onboard_status=0; $govern_step_0_status=1;
    if ($result_pf[0]['profilestatus']=='0') {
        $company_onboard_status=1;
    }

    if ($result_pf[0]['cregisteredaddress1']=='') {
        $govern_step_0_status=0;
    }

    //Find initial modal to show
    $arr_govern_initial_seq=array(1,2,3,4,5);
    $arr_govern_final_seq=array_diff($arr_govern_initial_seq,$arr_seq);
    $govern_seq="NA";
    if (count($arr_govern_final_seq)>0) {
      sort($arr_govern_final_seq);
      $govern_seq=$arr_govern_final_seq[0];
    }
    $initiate_modal=true; if ($govern_seq=="NA" || $govern_seq=="") {
      $result_asset= $session->execute($session->prepare("SELECT assetname FROM transasscust WHERE transasscompanycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
      if ($result_asset->count()==0) {
        $govern_seq="add_asset";
      }else {
        $initiate_modal=false;
      }
    }

    $output=[
        "total_status"=>$total_status,
        "law"=>$law,
        "next_status"=>$next_status,
        "completion_status"=>$count_completion,
        "company_onboard_status"=>$company_onboard_status,
        "govern_step_0_status"=>$govern_step_0_status,
        "govern_initial_modal_seq"=>(string)$govern_seq,
        "initiate_modal"=>$initiate_modal,
        "stepsDomain"=>$arr_n
    ];
    $arr_return=["code"=>200, "success"=>true, "data"=>$output];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
  }
}

//figure out if mandatory governance steps is already done
function update_mandatory_governance_steps($companycode,$law)
{
  try {
    global $session;
    if ($law=='' || $companycode=='') {
      $arr_return=["success"=>false,"msg"=>"Invalid Parameters","data"=>$e];
      return $arr_return; exit();
    }

    $result_law= $session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? AND law=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1",$law)));
    if ($result_law->count()==0) {
      $arr_return=["success"=>false,"msg"=>"Invalid Law","data"=>$e];
      return $arr_return; exit();
    }

    $arr_seq=[1,2,3,4,5];

    foreach ($arr_seq as $seq) {
      $result_f= $session->execute($session->prepare("SELECT id,createdate,modifydate,fillercustcode FROM companydiytracker WHERE companycode=? AND status=? AND sequence=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1",(string)$seq)));
      if ($result_f->count()>0) {
        //update these steps for the active Law
        // $update_landing_module=update_landing_module($companycode,$law,(string)$seq);
      }
    }

    $arr_return=["success"=>true,"msg"=>"Done","data"=>""];
    return $arr_return; exit();

  } catch (\Exception $e) {
    $arr_return=["success"=>false,"msg"=>"Error Occured","data"=>(string)$e];
    return $arr_return; exit();
  }
}

?>