<?php 

function GetUserHandler($funcCallType){
  try{

    switch($funcCallType){
      case "userRoles":
        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['role'])){
          $output = get_user_roles($GLOBALS['email'], $GLOBALS['companycode'], $GLOBALS['role']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "pageData":
        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['role']) && isset($GLOBALS['law'])){
          $output = get_common_page_data($GLOBALS['email'],$GLOBALS['companycode'],$GLOBALS['role'],$GLOBALS['law']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "changeRole":
        $jsonString = file_get_contents('php://input');
        $json = json_decode($jsonString,true);
        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['access_token']) && isset($json['role'])){
          $output = change_user_role($GLOBALS['email'],$GLOBALS['companycode'],$GLOBALS['access_token'],$json['role']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "changeLaw":
        $jsonString = file_get_contents('php://input');
        $json = json_decode($jsonString,true);
        if(isset($GLOBALS['email']) && isset($GLOBALS['companycode']) && isset($GLOBALS['access_token']) && isset($json['law'])){
          $output = change_user_law($GLOBALS['email'],$GLOBALS['companycode'],$GLOBALS['access_token'],$json['law']);
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
function get_user_roles($email,$companycode, $activeRole){
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
        if($row['rtcrole'] == $activeRole){
          $row['active'] = true;
        }else{
          $row['active'] = false;
        }
        $userRoleArr[]=[
            "role"=>$row["rtcrole"],
            "active"=>$row["active"]
        ];
      }

      array_multisort( array_column($userRoleArr, "role"), SORT_ASC, $userRoleArr);

      $arr_return=["code"=>200, "success"=>true, "data"=> $userRoleArr ];
      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
}

//get_common_page_data

function get_common_page_data($email,$companycode,$role,$law){
  try{
    global $session;
    $companyname = "";
    $companydisplayname = "";
    $username = "";

    if($companycode== ""|| $email== "" || $role == ""|| $law == ""){
      return ["code"=>401, "success" => false, "message"=>E_AUTH_ERR, "error"=>"" ]; exit();
    }

    //get company name
    $result =$session->execute($session->prepare("SELECT companyname,companydispname FROM company WHERE companycode=?"),array('arguments'=>array(
      $companycode
    )));

    if($result->count()> 0){
      $companyname = $result[0]['companyname'];
      $companydisplayname = $result[0]['companydispname'];
    }

    if($companydisplayname == ""){
      $companydisplayname = $companyname;
    }

    //user name
    $result_name =$session->execute($session->prepare("SELECT custfname,custlname FROM customer WHERE custemailaddress=?"),array('arguments'=>array(
      $email
    )));

    if($result_name->count() > 0){
      $username = $result_name[0]['custfname']." ".$result_name[0]['custlname'];
    }

    $usershortname = shortenNameString($username);

    //get compliance score
    $sum=0;
    $result= $session->execute($session->prepare("SELECT * FROM compliance_score_graph_data WHERE companycode=? AND lawspecific=? ALLOW FILTERING"),array('arguments'=>array($companycode,$law)));
    foreach ($result as $value) {
      $sum=$sum+(float)$value['calculatedscore'];
     }
    $compscore = round($sum/12,2);
     
     $companylogosrc = "";
     $companylogoFunc = get_company_logo($companycode);
     if($companylogoFunc['success']){
      $companylogosrc = $companylogoFunc['data']['src'];
     }


    $data =[
      "email" => $email,
      "companycode" => $companycode,
      "companyname" => $companyname,
      "companydisplayname" => $companydisplayname,
      "role" => $role,
      "law" => $law,
      "lawdisplayname" => $law,
      "username" => $username,
      "usershortname" => $usershortname,
      "compscore"=>$compscore,
      "companylogosrc" => $companylogosrc
    ];
  
    $arr_return=["code"=>200, "success"=>true, "data"=> $data ];
    return $arr_return;

  }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
  }
}

function change_user_role($email, $companycode, $access_token, $roleToChange){
  try{
      global $session;

      if($email=="" || $access_token=="" || $roleToChange=="" || $companycode==""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"" ]; exit();
      }


      //validate roleToChange First
      $result =$session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? AND rtccustemail=? AND rtcrole=? ALLOW FILTERING"),array('arguments'=>array(
        $companycode, "1", $email, $roleToChange
      )));

      if($result->count()==0){
        return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"Invalid role" ]; exit();
      }

      //Update user_active_session
      $timestamp=new \Cassandra\Timestamp();
      $session->execute($session->prepare("UPDATE user_active_session SET role=?, modifydate=? WHERE email=? AND status=? AND access_token=?"),array('arguments'=>array(
        $roleToChange, $timestamp, $email, "active", $access_token
      )));

      //return response
      $arr_return=["code"=>200, "success"=>true, "data"=>["message"=>"role updated"]];
      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
}

//change_user_law
function change_user_law($email, $companycode, $access_token, $lawToChange){
  try{
      global $session;

      if($email=="" || $access_token=="" || $lawToChange=="" || $companycode==""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"" ]; exit();
      }


      //validate lawToChange First
      $result =$session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? AND law=? ALLOW FILTERING"),array('arguments'=>array(
        $companycode, "1", $lawToChange
      )));

      if($result->count()==0){
        return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"Invalid law" ]; exit();
      }

      //Update user_active_session
      $timestamp=new \Cassandra\Timestamp();

      //customer_active_data
      $columns=["email","companycode","createdate","effectivedate","lastactivelaw"];
      $columns_data=[$email,$companycode,$timestamp,$timestamp,$lawToChange];
      $data_for_insert=[
        "action"=>"insert",
        "table_name"=>"customer_active_data",
        "columns"=>$columns,
        "isCondition"=>false,
        "condition_columns"=>"",
        "columns_data"=>$columns_data,
        "isAllowFiltering"=>false
      ];
      $table_insert=table_crud_actions($data_for_insert);
      if (!$table_insert['success']) { return $table_insert; exit(); }

      $session->execute($session->prepare("UPDATE user_active_session SET law=?, modifydate=? WHERE email=? AND status=? AND access_token=?"),array('arguments'=>array(
        $lawToChange, $timestamp, $email, "active", $access_token
      )));

      //return response
      $arr_return=["code"=>200, "success"=>true, "data"=>["message"=>"law updated"]];
      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
}


?>