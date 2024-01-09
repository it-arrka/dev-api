<?php 

function GetRiskRegisterHandler($funcCallType){
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

        default:
          catchErrorHandler(400,[ "message"=>E_INV_REQ, "error"=>"" ]);
        break;
      }
    }catch(Exception $e){
      catchErrorHandler($output['code'], [ "message"=>"", "error"=>$e->getMessage() ]);
    }
}



function get_policy_version_status(){
    
}
?>
