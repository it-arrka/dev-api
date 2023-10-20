<?php 

function UserLoginHandler(){
  try{
    $jsonString = file_get_contents('php://input');
    $json = json_decode($jsonString,true);

    if(isset($json['email']) && isset($json['password'])){
      $output = userLogin($json['email'],$json['password']);

      if($output['success']){
        commonSuccessResponse($output['code'],$output['data'],$output['message']);
      }else{
        catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
      }
    }else{
      catchErrorHandler(400,[ "message"=>"Invalid Payload", "error"=>"" ]);
    }
  }catch(Exception $e){
    catchErrorHandler($output['code'], [ "message"=>"", "error"=>$e->getMessage() ]);
  }
}

function userLogin($email,$password)
{
  global $session;
  try {

    if($email=="" || $password==""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_INV_CRED, "error"=>"" ]; exit();
    }

    //convert email to lowercase
    $email=strtolower($email); 

    //Find if email exists in db
    $result =$session->execute($session->prepare("SELECT custuserpasswd FROM customer WHERE custemailaddress=?"), array('arguments'=>array($email)));
    if($result->count()==0){
         //Unauthorized
         return ["code"=>401, "success" => false, "message"=>E_INV_CRED, "error"=>"" ]; exit();
    }

    $dbPassword = $result[0]['custuserpasswd'];

    if($dbPassword==""){
         //Unauthorized
         return ["code"=>401, "success" => false, "message"=>E_INV_CRED, "error"=>"" ]; exit();
    }

    if(password_verify($password, $dbPassword)){

        //check if email is disabled
        if (!check_if_email_is_active($email)) {
            return ["code"=>401, "success" => false, "message"=>E_USR_DISABLED, "error"=>"" ]; exit();
        }

        //generate token for this user
        $generate_and_save_tokens_for_user = generate_and_save_tokens_for_user($email);
        return $generate_and_save_tokens_for_user;

        //send login email


        //Log this activity


        //return the response

    }else{
        //Unauthorized
        return ["code"=>401, "success" => false, "message"=>E_INV_CRED, "error"=>"" ]; exit();
    }




//     $arr_login=array();
//     $result_login = $session->execute("SELECT custemailaddress FROM customer");
//     foreach ($result_login as $row_login) { $arr_login[strtolower($row_login['custemailaddress'])]= $row_login['custemailaddress']; }
//     if (array_key_exists($email,$arr_login)) { $email=$arr_login[$email]; }else{ echo json_encode(array("message"=>"error","data"=>E_INV_CRED)); exit(); }
//     if (emailstatus($email)=="0") { echo json_encode(array("message"=>"error","data"=>"Your Email has been disabled. Contact Administration")); exit(); }
//     $result_login_h =$session->execute($session->prepare("SELECT * FROM customer WHERE custemailaddress=?"), array('arguments'=>array($email)));
//     $profile_status="";
//     foreach ($result_login_h as $row) {
//         if(password_verify($password, $row['custuserpasswd'])){
//           //checking for terms of use flag
//           $_SESSION['email_temp']=$email; $_SESSION['passcode_temp']=$password;

//           if ($row['tncflag']=='') { echo json_encode(array("message"=>"tncflag","data"=>array())); exit(); }
//           $defcompflag=$row['defcompflag']; $defcompcode=$row['defcompcode']; $company_arr=userActiveAndInactiveCompany($email);
//           //company validation

//           if (count($company_arr)==0) { echo json_encode(array("message"=>"error","data"=>"Your Email has been disabled. Contact Administration!")); exit(); }
//           if (count($company_arr)==1) { foreach ($company_arr as $key_com => $value_com) { company_select_redirect($key_com,$defcompflag); exit(); } }
//           //if company list is more than 1
//           if ($defcompflag=="0") {
//             if (array_key_exists($defcompcode,$company_arr['active'])) { company_select_redirect($defcompcode,"0"); exit(); }
//             else { echo json_encode(array("message"=>"companylist","data"=>$company_arr,"defcompflag"=>$row['defcompflag'],"defcompflag"=>"0")); exit(); }
//           }else {
//             echo json_encode(array("message"=>"companylist","data"=>$company_arr,"defcompflag"=>"1")); exit();
//           }
//     }else { send_login_mail($email,"0"); echo json_encode(array("message"=>"error","data"=>E_INV_CRED)); exit(); }
//   }

  } catch (\Exception $e) {
    //log this activity
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>