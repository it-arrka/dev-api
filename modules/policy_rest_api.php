<?php 

function GetPolicyHandler($funcCallType){
    try{
      switch($funcCallType){
        case "get-policy-version-status":
          if(isset($_GET['law_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])){
            $output = get_policy_version_status($_GET['law_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "get-policy-domains":
        if(isset($_GET['law_tid']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])){
            $output = get_policy_domains($_GET['law_tid'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
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

function get_policy_version_status($law_tid,$companycode,$email,$role){
    try {
        global $session;
        if ($law_tid=="") {
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law_tid" ]; 
            exit();
        }

        $result_txn= $session->execute($session->prepare('SELECT law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'),array('arguments'=>array($law_tid,"1")));
        if ($result_txn->count()==0) {
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law_tid" ]; 
            exit();
        }
        $law_version=(string)$result_txn[0]['law_version'];

        //Get version history
        $arr=[];
        $result_version= $session->execute($session->prepare('SELECT * FROM policyacfoverallmapping_version WHERE law_tid=? AND law_version=? AND companycode=?'),array('arguments'=>array($law_tid,$law_version,$companycode)));
            if ($result_version->count()==0) {
                $arr=[
                'active_version'=>0,
                'active_date'=>date("d-m-Y"),
                'edit_status'=>false,
                'edit_version'=>"",
                'edit_date'=>"",
                'archived'=>[]
                ];
            }else {
                $active_version=0;
                $active_date="-";
                $edit_status=false;
                $edit_version="";
                $edit_date="";
                $archived=[];
                foreach ($result_version as $row_version) {
                if ($row_version['status']=='active') {
                    $active_version=$row_version['version'];
                    $active_date_str=(string)$row_version['createdate'];
                    $active_date=date("d-m-y",(int)$active_date_str/1000);
                }
                elseif ($row_version['status']=='edit') {
                    $edit_status=true;
                    $edit_version=$row_version['version'];
                    $edit_date_str=(string)$row_version['createdate'];
                    $edit_date=date("d-m-y",(int)$edit_date_str/1000);
                }else {
                    $edit_date_str=(string)$row_version['createdate'];
                    $edit_date=date("d-m-y",(int)$edit_date_str/1000);
                    $archived[]=[
                    "version"=>$row_version['version'],
                    "date"=>$edit_date
                    ];
                }
                }

                $arr=[
                'active_version'=>$active_version,
                'active_date'=>$active_date,
                'edit_status'=>$edit_status,
                'edit_version'=>$edit_version,
                'edit_date'=>$edit_date,
                'archived'=>$archived
                ];

            }

            //Rule for policyacfoverallmapping_version table
            //If active = status=active -> Only One entry in a partition. Two rows cannot be active
            //If edit = status=edit -> Only One entry in a partition. Two rows cannot be edit
            //After submission :: edit==active, active=archived
            $arr_return=["code"=>200, "success"=>true, "data"=>$arr];
            return $arr_return;
        } catch (\Exception $e) {
            return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
        }
}

function get_policy_domains($law_tid,$companycode,$email,$role)
{
  try {
    global $session;
    
    //validate law_tid
    if ($law_tid=="") {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law_tid" ]; 
      exit();
    }

    $result_txn= $session->execute($session->prepare('SELECT law_tid,law_version FROM compliance_framework_txn WHERE law_tid=? AND policy_map_status=? ALLOW FILTERING'),array('arguments'=>array($law_tid,"1")));
    if ($result_txn->count()==0) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid law_tid" ]; 
        exit();
    }
    //Get law_tid transactionid
    $law_version=(string)$result_txn[0]['law_version'];

    //get policy edit version
    $get_policy_version_status_for_law=get_policy_version_status($law_tid,$companycode,$email,$role);
    if (!$get_policy_version_status_for_law['success']) {
      return $get_policy_version_status_for_law;
      exit();
    }

    $policy_vArr=$get_policy_version_status_for_law['data'];
    $policy_edit_version=(int)$policy_vArr['edit_version'];
    $policy_active_version=(int)$policy_vArr['active_version'];
    $version_arr=[$policy_edit_version,$policy_active_version];

    $arr=[];

    if ($policy_active_version==0) {
      $result= $session->execute($session->prepare('SELECT domains,domains_acf,redundant FROM policyacfmapping WHERE status=? AND version=? AND law_tid=? AND law_version=?'),array('arguments'=>array("1",1,$law_tid,$law_version)));
      foreach ($result as $row) {
        if (!$row['redundant']) {
          $arr[$row['domains_acf']][0]=[
            "domains"=>$row['domains'],
            "domains_acf"=>$row['domains_acf'],
            "saved"=>false
          ];
        }
      }
    }



    $result_version= $session->execute($session->prepare('SELECT domains,domains_text,version,policy_version FROM policyacfclientmapping_version WHERE law_tid=? AND law_version=? AND companycode=? AND status=?'),array('arguments'=>array(
      $law_tid,$law_version,$companycode,"1"
    )));

    foreach ($result_version as $row_version) {
        $arr[$row_version['domains']][$row_version['version']]=[
          "domains"=>$row_version['domains_text'],
          "domains_acf"=>$row_version['domains'],
          "saved"=>true
        ];
    }


    $arr_final=[];
    foreach ($arr as $key => $value) {
      $max_version=max(array_keys($value));
      if (isset($value[$max_version])) {
        $arr_final[]=$arr[$key][$max_version];
      }
    }

    //Check for deletion
    foreach ($arr_final as $key_final => $value_final) {
      //policyacfclientmapping_deletion
      $result_del= $session->execute($session->prepare('SELECT policy_version FROM policyacfclientmapping_deletion WHERE law_tid=? AND law_version=? AND companycode=? AND status=? AND domains=?'),array('arguments'=>array(
        $law_tid,$law_version,$companycode,"1",$value_final['domains_acf']
      )));
      foreach ($result_del as $row_del) {
        if (in_array($row_del['policy_version'],$version_arr)) {
          //remove this element
          unset($arr_final[$key_final]);
        }
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr_final];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>