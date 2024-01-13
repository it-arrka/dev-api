<?php 
function UserLoginHandler($funcCallType){
  try{
    switch($funcCallType){
      case "login":
          $jsonString = file_get_contents('php://input');
          if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
          $json = json_decode($jsonString,true);
          if(!is_array($json)){
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
          }
      
          if(isset($json['email']) && isset($json['password'])){
            $output = userLogin($json['email'],$json['password']);
      
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>"Invalid Payload", "error"=>"" ]);
          }
        break;

        case "forgotPassword":
          $jsonString = file_get_contents('php://input');
          if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
          $json = json_decode($jsonString,true);
          if(!is_array($json)){
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
          }
      
          if(isset($json['email'])){
            $output = forgot_password($json['email']);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>"Invalid Payload", "error"=>"" ]);
          }
        break;

      case "forgotPasswordLinkVerify":
        $jsonString = file_get_contents('php://input');
        if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
        $json = json_decode($jsonString,true);
        if(!is_array($json)){
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
        }
    
        if(isset($json['hashcode'])){
          $output = forgot_password_link_verify($json['hashcode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>"Invalid Payload", "error"=>"" ]);
        }
      break;

      case "forgotPasswordSetPassword":
        $jsonString = file_get_contents('php://input');
        if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
        $json = json_decode($jsonString,true);
        if(!is_array($json)){
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
        }
    
        if(isset($json['hashcode']) && isset($json['password']) && isset($json['repassword'])){
          $output = forgot_password_set_password($json['hashcode'], $json['password'], $json['repassword']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>"Invalid Payload", "error"=>"" ]);
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

function UserLogoutHandler(){
  try{
    $arr_cookies = [];
    foreach ($_COOKIE as $name => $value) {
      if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
          // Check if the cookie has the "Secure" attribute
          if (isset($_COOKIE[$name]) && is_array($_COOKIE[$name]) && array_key_exists('secure', $_COOKIE[$name])) {
              // This is a secure cookie
              echo "Secure Cookie: Name: $name, Value: $value<br>";
              $arr_cookies[]=[
                "name" => $name,
                "value" => $value
              ];
          }
      }
    }
    commonSuccessResponse(200, $_COOKIE);
  }catch (\Exception $e) {
    catchErrorHandler(500, [ "message"=>"", "error"=>$e->getMessage() ]);
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
        $emailDis=check_if_email_is_active($email);

        if(!$emailDis['success']){
          return $emailDis; exit();
        }
        $emailDisData = $emailDis['data'];

        if (!$emailDisData['active']) {
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
  } catch (\Exception $e) {
    //log this activity
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param string $email
 */
function forgot_password($email)
  {
    try {
        global $session;
        $result= $session->execute($session->prepare('SELECT hashcode,custfname,custlname FROM customer WHERE custemailaddress=?'),array('arguments'=>array($email)));
        $count =$result->count();
        if ($count==0) {
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid email" ]; exit();
        }
          $name=$result[0]['custfname'];
          foreach ($result as $row) { $hashcode=$row['hashcode']; }

          //set date and status
          $session->execute($session->prepare('UPDATE statusrecord SET pwresetstatus=?,pwresetstatusdate=? WHERE custemail=?'),array('arguments'=>array("1",new \Cassandra\Timestamp(),$email)));
          //check for customer approved
          $result_sta= $session->execute($session->prepare('SELECT rolestatus FROM roletocustomer WHERE rtccustemail=? AND rolestatus=? ALLOW FILTERING '),array('arguments'=>array($email,"1")));
          if($result_sta->count() == 0){
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is disabled" ]; exit();
          }

        //Invite link
          $link_to_send =$_ENV['MAIN_PAGE_URL']."/forgot-password-verify?hashcode=".base64_encode($hashcode);

          $mail_temp= password_reset_mail_template($email,$link_to_send,$name);
          $subject=$mail_temp['subject'];
          $mailbody=$mail_temp['mailbody'];
          send_mail(array($email),array(),$subject,$mailbody);
        
          $arr_return=["code"=>200, "success"=>true, "data"=>["message"=>"success"] ];
          return $arr_return;
      } catch (\Exception $e) {
          return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
      }
}

function forgot_password_link_verify($hash){
  try {
    global $session;
    if($hash == ""){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link-1" ]; exit();
    }

    if(!isValidBase64($hash)) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link-2" ]; exit();
    }

    $hash = base64_decode($hash);

    $result_cust =$session->execute($session->prepare("SELECT custemailaddress FROM customer WHERE hashcode=? ALLOW FILTERING"),array('arguments'=>array($hash)));
    if($result_cust->count() == 0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link-3" ]; exit();
    }

    $email =$result_cust[0]['custemailaddress'];

    $result_pw =$session->execute($session->prepare("SELECT pwresetstatus,pwresetstatusdate FROM statusrecord WHERE custemail=?"),array('arguments'=>array($email)));
    if($result_pw->count() == 0) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link-4" ]; exit();
    }

    $createdate_str=(string)$result_pw[0]['pwresetstatusdate']; 

    if($result_pw[0]['pwresetstatus'] != "1" || $createdate_str=="") {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link" ]; exit();
    }

    //date Validation 24 hrs max
    $createdate=date("d-m-Y H:i:sa",(int)$createdate_str/1000);
    $nowdate=date("d-m-Y H:i:sa");
    $date_diff=date_difference($createdate,$nowdate);
    $hours=($date_diff['years']*8760)+($date_diff['months']*730)+($date_diff['days']*24)+$date_diff['hours'];

    //Forwarding
    if ($hours>24) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Link expired" ]; exit();
    }

    return ["code"=>200, "success"=>true, "data"=>['link' => 'verified', 'email' => $email] ];
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function forgot_password_set_password($hashcode, $password_1,$password_2)
{
  try {
    global $session;
    if($hashcode == "" || $password_1 == "" || $password_2 == "") {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid request" ]; exit();
    }

    if(!isPasswordValid($password_1)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid password" ]; exit();
    }

    if ($password_1 != $password_2) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Password did not match" ]; exit();
    }

    $verfiyOutput = forgot_password_link_verify($hashcode);
    if(!$verfiyOutput['success']){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid hash" ]; exit();
    }

    $email = $verfiyOutput['data']['email'];
    $hash_password = password_hash($password_1, PASSWORD_DEFAULT);

    //updating status
    $session->execute($session->prepare("UPDATE customer SET custuserpasswd=?,password_timestamp=? WHERE custemailaddress=?"),array('arguments'=>array($hash_password,new \Cassandra\Timestamp(),$email)));

    $session->execute($session->prepare('UPDATE statusrecord SET pwresetstatus=? WHERE custemail=?'),array('arguments'=>array("0",$email)));

    return ["code"=>200, "success"=>true, "data"=>['message' => 'passoword set', 'email' => $email] ];

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>