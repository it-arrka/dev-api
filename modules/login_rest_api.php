<?php 

function UserLoginHandler(){
  try{
    $jsonString = file_get_contents('php://input');
    $json = json_decode($jsonString,true);

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

?>