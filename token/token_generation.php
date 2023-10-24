<?php 

/*
    This API is to generate tokens
    Call this API exclusively to use the function
*/
use Firebase\JWT\JWT;

require_once 'token_validation.php';
require_once $_ENV['HOME_PATH'].'/modules/company_rest_api.php';

//refresh token handler
function RefreshTokenHandler(){
    try{
      $jsonString = file_get_contents('php://input');
      $json = json_decode($jsonString,true);
  
      if(isset($json['refresh_token'])){
        $output = generate_token_by_refresh_token($json['refresh_token']);
        if($output['success']){
          commonSuccessResponse($output['code'],$output['data']);
        }else{
          catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
        }
      }else{
        catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
      }
    }catch(Exception $e){
      catchErrorHandler($output['code'], [ "message"=>"", "error"=>$e->getMessage() ]);
    }
  }

//Call this function to generate tokens. @param :: $email
function generate_jwt_tokens($email){
    try{
        // Your secret key (keep this secure)
        $secretKey =$_ENV['JWT_SECRET_KEY'];
        // User information to include in the token payload

        $currentTimestamp   = new DateTimeImmutable();
        $issuedAt = $currentTimestamp->getTimestamp();
        $expire_access     = $currentTimestamp->modify('+1 day')->getTimestamp();
        $expire_refresh     = $currentTimestamp->modify('+7 day')->getTimestamp();

        $userDataAccess = [
            'iat'  => $issuedAt,                // Issued at: time when the token was generated
            'iss'  => $_SERVER['SERVER_NAME'],  // Issuer
            'nbf'  => $issuedAt,                // Not before
            'exp'  => $expire_access,           // Expire
            'email' => $email,                  // Email
            'type' => "access"                  // Type
        ];

        $userDataRefresh = [
            'iat'  => $issuedAt,                 // Issued at: time when the token was generated
            'iss'  => $_SERVER['SERVER_NAME'],   // Issuer
            'nbf'  => $issuedAt,                 // Not before
            'exp'  => $expire_refresh,           // Expire
            'email' => $email,                   // Email
            'type' => "refresh"                  // Type
        ];

        // Access token (short expiration) 24 hrs
        $accessToken = JWT::encode($userDataAccess, $secretKey, 'HS256');
        // Refresh token (longer expiration) 7 days
        $refreshToken = JWT::encode($userDataRefresh, $secretKey, 'HS256');
        // Return both tokens to the client
        $data= [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ];
        $arr_return=["code"=>200, "success"=>true, "data"=>$data];
        return $arr_return;
    }catch(Exception $e){
        $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>(string)$e];
        return $arr_return;
     }
}

//Call this function to save tokens. This function will call generate_jwt_tokens inside. @param :: $email
function generate_and_save_tokens_for_user($email){
    try{
        global $session;

        $result= $session->execute($session->prepare("SELECT custemailaddress FROM customer WHERE custemailaddress=?"),array('arguments'=>array($email)));
        if($result->count()==0){
            $arr_return=["code"=>500, "success"=>false, "message"=>E_INV_CRED, "error"=>""]; exit();
        }

        //get token
        $jwt_token=generate_jwt_tokens($email);
        if(!$jwt_token['success']){ return $jwt_token; exit(); }

        $jwt_data = $jwt_token['data'];
        $access_token = $jwt_data['access_token'];
        $timestamp=new \Cassandra\Timestamp();

        //get role and company and law
        $lastActiveArr = get_last_active_customer_details($email);
        if(!$lastActiveArr['success']){ return $lastActiveArr; exit(); }
        $companycode = $lastActiveArr['data']['companycode'];
        $role = $lastActiveArr['data']['role'];
        $law = $lastActiveArr['data']['law'];

        foreach ($jwt_data as $keyJwt => $valueJwt) {
            if($keyJwt=='access_token'){
                $jwt_token=$valueJwt;
                $token_type="access";
            }else{
                $jwt_token=$valueJwt;
                $token_type="refresh";
            }

            //save or update in jwt_token table
            $columns=[
                "email",
                "createdate",
                "effectivedate",
                "jwt_token",
                "token_type",
                "status"
            ];
            $columns_data=[
                $email,
                $timestamp,
                $timestamp,
                $jwt_token,
                $token_type,
                "active"
            ];
            $data_for_insert=[
                "action"=>"insert", //read/insert/update/delete
                "table_name"=>"jwt_token", //provide actual table name
                "columns"=>$columns, //Provide one element as ALL for All column else provide individual column name.
                "isCondition"=>false,
                "condition_columns"=>"",
                "columns_data"=>$columns_data,
                "isAllowFiltering"=>false
            ];
            $table_insert=table_crud_actions($data_for_insert);
            if(!$table_insert['success']){
                return $table_insert; exit();
            }
        }

         $columns=[
            "email",
            "user_agent",
            "status",
            "createdate",
            "effectivedate",
            "access_token",
            "companycode",
            "role",
            "law"
          ];
          $columns_data=[
            $email,
            $_SERVER['HTTP_USER_AGENT'],
            "active",
            $timestamp,
            $timestamp,
            $access_token,
            $companycode,
            $role,
            $law
          ];
          $data_for_insert=[
            "action"=>"insert", //read/insert/update/delete
            "table_name"=>"user_active_session", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns"=>$columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition"=>false,
            "condition_columns"=>"",
            "columns_data"=>$columns_data,
            "isAllowFiltering"=>false
          ];
          $table_insert=table_crud_actions($data_for_insert);
          if($table_insert['success']){
            $arr_return=["code"=>200, "success"=>true ,"data"=>$jwt_data];
            return $arr_return; 
          }else{
            return $table_insert;
          }
    }catch(Exception $e){
        $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>(string)$e];
        return $arr_return;
    }
}

//generate token by refresh token
function generate_token_by_refresh_token($refresh_token){
    try{
        global $session;
        // refresh_token
        if($refresh_token==""){
            $arr_return=["code"=>401, "success"=>false, "message"=>E_PAYLOAD_EMPTY, "error"=>""];
            return $arr_return;
            exit();
        }

        //validate this token
        $jwt_token=decode_jwt_access_tokens($refresh_token,"refresh");
        if(!$jwt_token['success']){ return $jwt_token; exit(); }

        $jwt_data=$jwt_token['data'];
        $email = $jwt_data['email'];

        //validate refresh token against jwt_token
        $result= $session->execute($session->prepare("SELECT email FROM jwt_token WHERE email=? AND status=? AND token_type=? AND jwt_token=?"),array('arguments'=>array(
            $email,"active","refresh",$refresh_token
        )));
        if($result->count()==0){
            $arr_return=["code"=>401, "success"=>false, "message"=>E_PAYLOAD_INV, "error"=>""]; exit();
        }

        //generate new token
        $output = generate_and_save_tokens_for_user($email);
        return $output;
    }catch(Exception $e){
        $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>(string)$e];
        return $arr_return;
    }
}

?>