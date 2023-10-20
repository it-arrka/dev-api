<?php 

/*
    This API is to generate tokens
    Call this API exclusively to use the function
*/
use Firebase\JWT\JWT;

require_once 'token_validation.php';

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
        $arr_return=["code"=>200, "success"=>true, "message"=>"", "data"=>$data];
        return $arr_return;
    }catch(Exception $e){
        $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>$e->getMessge()];
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

         //start an active session for this toke
         $session_data=Cassandra\Type::map(Cassandra\Type::varchar(), Cassandra\Type::varchar())->create();
        //  foreach ($_SESSION as $key_session => $value_session) {
        //     if(!is_array($value_session)){
        //         $session_data->set($key_session,(string)$value_session);
        //     }
        //  }

         $columns=[
            "email",
            "session_id",
            "user_agent",
            "session_data",
            "status",
            "createdate",
            "effectivedate",
            "access_token"
          ];
          $columns_data=[
            $email,
            "",
            $_SERVER['HTTP_USER_AGENT'],
            $session_data,
            "active",
            $timestamp,
            $timestamp,
            $access_token
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
            $arr_return=["code"=>200, "success"=>true,"message"=>"","data"=>$jwt_data];
            return $arr_return; 
          }else{
            return $table_insert;
          }
    }catch(Exception $e){
        $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>$e->getMessge()];
        return $arr_return;
    }
}

?>