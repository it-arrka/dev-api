<?php 

function GetCompanyHandler($funcCallType){
  try{

    switch($funcCallType){
      case "companyList":
        if(isset($GLOBALS['email'])){
          $output = get_company_list($GLOBALS['email'], $GLOBALS['companycode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "companyLogo":
        if(isset($GLOBALS['companycode'])){
          $output = get_company_logo($GLOBALS['companycode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "changecompany":
        $jsonString = file_get_contents('php://input');
        if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
        $json = json_decode($jsonString,true);
        if(!is_array($json)){
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
        }
        if(isset($json['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['access_token'])){
          $output = change_company_for_user($json['companycode'], $GLOBALS['email'], $GLOBALS['access_token']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "addressList":
        if(isset($GLOBALS['companycode'])){
          $output = get_address_list($GLOBALS['companycode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "addressListFull":
        if(isset($GLOBALS['companycode'])){
          $output = get_address_list_full($GLOBALS['companycode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "departmentList":
        if(isset($GLOBALS['companycode'])){
          $output = get_department_list($GLOBALS['companycode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "get-all-roles":
        if(isset($GLOBALS['companycode'])){
          $output = get_all_roles_for_company($GLOBALS['companycode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "get-emails-from-role":
        if(isset($GLOBALS['companycode']) && isset($_GET['role'])){
          $output = get_emails_from_roles_in_company($GLOBALS['companycode'], $_GET['role']);
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

//get active company to redirect
function get_last_active_customer_details($email){
  try{
      global $session;
      //get active company list
      $companyListArr = get_company_list($email);
      if(!$companyListArr['success']){
        return $companyListArr; exit();
      }

      $companyListData=$companyListArr['data'];
      $companyList=[];
      foreach($companyListData as $company){
        $companyList[$company['companycode']]= $company['companyname'];
      }

      $defaultCompanyName="";
      $defaultCompanyCode="";
      $getFromList=false;
      
      $result =$session->execute($session->prepare("SELECT defcompflag,defcompcode FROM customer WHERE custemailaddress=?"),array('arguments'=>array($email)));

      foreach($result as $row){
        if($row['defcompcode']==''){ 
          $getFromList=true;
        }else{
          if(isset($companyList[$row['defcompcode']])){
            $defaultCompanyCode=$row['defcompcode'];
            $defaultCompanyName=$companyList[$row['defcompcode']];
          }else{
            $getFromList=true;
          }
        }
      }

      if($getFromList){
        $defaultCompanyName=$companyListData[0]['companyname'];
        $defaultCompanyCode=$companyListData[0]['companycode'];
      }

      //applicable law list
      $lawArr=[];
      $result_law =$session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($defaultCompanyCode,"1")));
      foreach($result_law as $row_law){ if($row_law['law']!=""){ array_push($lawArr,$row_law["law"]); } }

      //role list
      $roleArr=[];
      $result_role =$session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? ALLOW FILTERING"),array('arguments'=>array($defaultCompanyCode,"1")));
      foreach($result_role as $row_role){ if($row_role['rtcrole']!=""){ array_push($roleArr,$row_role["rtcrole"]); } }

      //get default role and law
      $lastactivelaw="";
      $lastactiverole="";
      $result_lr =$session->execute($session->prepare("SELECT lastactivelaw,lastactiverole FROM customer_active_data WHERE email=? AND companycode=?"),array('arguments'=>array($email,$defaultCompanyCode)));
      foreach($result_lr as $row_lr){
        $lastactivelaw=$row_lr['lastactivelaw'];
        $lastactiverole=$row_lr['lastactiverole'];
      }

      if(!in_array($lastactivelaw,$lawArr)){
        $lastactivelaw=$lawArr[0];
      }

      if(!in_array($lastactiverole,$roleArr)){
        $lastactiverole=$roleArr[0];
      }


      $arr_return=["code"=>200, "success"=>true, "data"=>[
        "companyname"=>$defaultCompanyName,
        "companycode"=>$defaultCompanyCode,
        "role"=>$lastactiverole,
        "law"=>$lastactivelaw
      ]];

      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
}

function get_company_list($email, $activeCompanycode="")
{
  try {
    global $session;
    if($email==""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    //convert email to lowercase
    $email=strtolower($email); 
    $arr_company =[];

    //Find if email exists in db
    $result =$session->execute($session->prepare("SELECT companycode FROM roletocustomer WHERE rtccustemail=? AND rolestatus=? ALLOW FILTERING"), array('arguments'=>array(
        $email,"1"
    )));

    $existCompData=[];

    foreach ($result as $row) {
        if($row['companycode']!="" && !isset($existCompData[$row['companycode']])){
            $result_comp =$session->execute($session->prepare("SELECT companyname FROM company WHERE companycode=?"), array('arguments'=>array(
                $row['companycode']
            )));
            if($result_comp->count()>0){
              $active = false;
              if($activeCompanycode == $row['companycode']){
                $active = true;
              }
                $arr_company[] = [
                    "companycode" => $row['companycode'],
                    "companyname" => $result_comp[0]['companyname'],
                    "active" => $active
                ];
            }
            $existCompData[$row['companycode']]=true;
        }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr_company];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function change_company_for_user($change_companycode, $email, $access_token)
{
  try {
    global $session;

    $arr_role = [];
    $role_to_update = ""; $law_to_update = "";

    $timestamp=new \Cassandra\Timestamp();
    //validate companycode
    $result= $session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rtccustemail=? AND rolestatus=? ALLOW FILTERING"),array('arguments'=>array($change_companycode,$email,"1")));
    if($result->count()==0){
      return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"$change_companycode - $email - $access_token" ]; exit();
    }

    foreach ($result as $key) { array_push($arr_role,$key['rtcrole']); } sort($arr_role);
    $role_to_update = $arr_role[0];

    //Get law
    $noLaw=0;
    $result_active= $session->execute($session->prepare("SELECT lastactivelaw FROM customer_active_data WHERE companycode=? AND email=?"),array('arguments'=>array($change_companycode,$email)));
    if ($result->count()>0) {
      $lastactivelaw=$result_active[0]['lastactivelaw'];
      if ($lastactivelaw=="") {
        $noLaw=1;
      }else {
        $result_applaw_check= $session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? AND law=? ALLOW FILTERING"),array('arguments'=>array($change_companycode,"1",$lastactivelaw)));
        if ($result_applaw_check->count()>0) {
          $law_to_update=$lastactivelaw;
        }else {
          $noLaw=1;
        }
      }
    }else {
      $noLaw=1;
    }

    if ($noLaw) {
      $result_applaw= $session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($change_companycode,"1")));
      if ($result_applaw->count()>0) {
        $law_active=$result_applaw[0]['law'];
        if($law_active==""){
          $law_to_update="";
        }else {
          $law_to_update=$law_active;
        }
      }else {
        $law_to_update="";
      }
    }

    if($law_to_update == ""){
      return ["code"=>400, "success" => false, "message"=>E_NO_LAW_FOUND, "error"=>"" ]; exit();
    }

    //first get the user_active_session data
    $result_token= $session->execute($session->prepare("SELECT access_token FROM user_active_session WHERE email=? AND status=? AND access_token=?"),array('arguments'=>array($email,"active",$access_token)));
    if ($result_token->count()== 0) {
      return ["code"=>401, "success" => false, "message"=>E_AUTH_ERR, "error"=>"" ]; exit();
    }

    //update to user_active session
    $session->execute($session->prepare("UPDATE user_active_session SET companycode=?, role=?, law=?, modifydate=? WHERE email=? AND status=? AND access_token=?"),array('arguments'=>array(
      $change_companycode, $role_to_update, $law_to_update, $timestamp, $email, "active", $access_token
    )));

    //update the last companycode
    $session->execute($session->prepare("UPDATE customer SET defcompcode=?,modifydate=? WHERE custemailaddress=?"), array('arguments'=>array($change_companycode,$timestamp,$email)));

    
    //return response
    $arr_return=["code"=>200, "success"=>true, "data"=>["message"=>"company updated"]];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

//get_address_list
function get_address_list($companycode){
  try {
    global $session; $arr_add=array();

    $res_add=$session->execute($session->prepare("SELECT cregisteredaddress1,cregisteredadcountry,cregisteredadstate,cregisteredadpincode,cregisteredadcity FROM company WHERE companycode=?"),array('arguments'=>array($companycode)));
    foreach ($res_add as $row_add) { if($row_add['cregisteredaddress1']=="" || $row_add['cregisteredaddress1']==" "){ } else{ array_push($arr_add,$row_add['cregisteredaddress1']." ".$row_add['cregisteredadcity']." ".$row_add['cregisteredadstate']." ".$row_add['cregisteredadcountry']." ".$row_add['cregisteredadpincode']); } }

    $res_add_2=$session->execute($session->prepare("SELECT locationaddress1,locationaddresscountry,locationaddressstate,locationaddresspincode,locationaddresscity FROM locationinscope WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
    foreach ($res_add_2 as $row_add_2) { if($row_add_2['locationaddress1']=="" || $row_add_2['locationaddress1']==" "){ } else{ array_push($arr_add,$row_add_2['locationaddress1']." ".$row_add_2['locationaddresscity']." ".$row_add_2['locationaddressstate']." ".$row_add_2['locationaddresscountry']." ".$row_add_2['locationaddresspincode']); } }
    $arr = array_unique($arr_add);
    $arr_return=["code"=>200, "success"=>true, "data"=>$arr];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

//get_address_list full
function get_address_list_full($companycode){
  try {
    global $session; $arr_add=array();

    $res_add=$session->execute($session->prepare("SELECT cregisteredaddress1,cregisteredadcountry,cregisteredadstate,cregisteredadpincode,cregisteredadcity FROM company WHERE companycode=?"),array('arguments'=>array($companycode)));
    foreach ($res_add as $row_add) { if($row_add['cregisteredaddress1']=="" || $row_add['cregisteredaddress1']==" "){ } else{ array_push($arr_add,$row_add['cregisteredaddress1']."~*|*~".$row_add['cregisteredadcity']."~*|*~".$row_add['cregisteredadstate']."~*|*~".$row_add['cregisteredadcountry']."~*|*~".$row_add['cregisteredadpincode']); } }

    $res_add_2=$session->execute($session->prepare("SELECT locationaddress1,locationaddresscountry,locationaddressstate,locationaddresspincode,locationaddresscity FROM locationinscope WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
    foreach ($res_add_2 as $row_add_2) { if($row_add_2['locationaddress1']=="" || $row_add_2['locationaddress1']==" "){ } else{ array_push($arr_add,$row_add_2['locationaddress1']."~*|*~".$row_add_2['locationaddresscity']."~*|*~".$row_add_2['locationaddressstate']."~*|*~".$row_add_2['locationaddresscountry']."~*|*~".$row_add_2['locationaddresspincode']); } }
    $arr = array_unique($arr_add);

    $final_arr = [];
    foreach($arr as $addressString){
      $addressArr = [];
      $explodeArr = explode("~*|*~",$addressString);
      $addressArr = [
        'address1' => $explodeArr[0],
        'country' => $explodeArr[3],
        'state' => $explodeArr[2],
        'city' => $explodeArr[1],
        'pincode' => $explodeArr[4]
      ];
      $final_arr[] = $addressArr;
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$final_arr];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>