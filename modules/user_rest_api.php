<?php 

function GetUserHandler($funcCallType){
  try{

    switch($funcCallType){
      case "userRoles":
        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode'])){
          $output = get_user_roles($GLOBALS['email'],$GLOBALS['companycode']);
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

//get active company to redirect
function get_user_roles($email,$companycode){
  try{
      global $session;

      if($email=="" || $companycode==""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
      }


      //get role list
      $userRoleArr=[];

      $result =$session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtccustemail=? ALLOW FILTERING"),array('arguments'=>array(
        $companycode, "1", $email
      )));

      foreach($result as $row){
        $userRoleArr[]=[
            "role"=>$row["rtcrole"]
        ];
      }

      $arr_return=["code"=>200, "success"=>true, "data"=> $userRoleArr ];
      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
}

?>