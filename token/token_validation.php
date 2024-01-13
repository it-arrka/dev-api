<?php 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


// token handler
function AccessTokenValidationHandler($successReturn=false) {
    // Get the Authorization header
    if(!isset($_SERVER['HTTP_AUTHORIZATION'])){
        catchErrorHandler(401, [ "message"=>E_NO_TOKEN, "error"=>"" ]);
    }
    $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

    // Check if the header is set and starts with "Bearer "
    if ($authorizationHeader && preg_match('/^Bearer (.+)$/', $authorizationHeader, $matches)) {
        // Extract the token
        $token = $matches[1];
        
        $output=validate_access_token($token);
        if(!$output['success']){
            catchErrorHandler($output['code'], [ "message"=>$output['message'], "error"=>$output['error'] ]);
        }
        if($successReturn){
            commonSuccessResponse($output['code'], $output['data']);
        }else{
            return $output;
        }

    } else {
        // Authorization header is missing or in the wrong format
        // Handle the error as needed
        catchErrorHandler(401, [ "message"=>E_NO_BEARER_TOKEN, "error"=>"" ]);
    }
}

//decode JWT Token tokens
function decode_jwt_access_tokens($token,$type){
    try{
        // Your secret key (keep this secure)
        $secretKey = $_ENV['JWT_SECRET_KEY'];
        $now = new DateTimeImmutable();
        $serverName = $_SERVER['SERVER_NAME'];
        // echo json_encode("yaha tak chala".$token); exit();

        // Decode the token and verify its signature
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));


        if($decoded->iss != $serverName || $decoded->nbf > $now->getTimestamp() || $decoded->exp < $now->getTimestamp() || $type!=$decoded->type){
            $arr_return=["code"=>401, "success"=>false, "message"=>E_INV_TOKEN, "error"=>"" ];
            return $arr_return;
        }else{
             // Access token claims
             $email = $decoded->email;

             // Token has expired.. check for refresh token
             $arr_return=["code"=>200, "success"=>true, "data"=>['email'=>$email]];
             return $arr_return;
        }
    }catch(Exception $e){
        $arr_return=["code"=>401, "success"=>false, "message"=>E_TOKEN_ERR, "error"=>$e->getMessage()];
        return $arr_return;
     }
}

function validate_access_token($access_token){
    try{
        global $session;
        if($access_token==""){
            $arr_return=["code"=>401, "success"=>false, "message"=>E_NO_TOKEN, "error"=>""];
            return $arr_return;
            exit();
        }

        //get token
        $jwt_token_output=decode_jwt_access_tokens($access_token,"access");
        if(!$jwt_token_output['success']){
            return $jwt_token_output; exit();
        }

        $jwt_data= $jwt_token_output['data'];
        $email=$jwt_data['email'];

        //validate email against user active session
        $result_token= $session->execute($session->prepare("SELECT email,companycode,law,role FROM user_active_session WHERE email=? AND status=? AND access_token=?"),array('arguments'=>array(
            $email,"active",$access_token
        )));

        if($result_token->count()==0){
            //If token is valid but entry is deactivated from user_active_session, then this will also return 401
            $arr_return=["code"=>401, "success"=>false, "message"=>E_TOKEN_REG, "error"=>""];
            return $arr_return;
            exit();
        }

        $arr_return=[
            "code"=>200, "success"=>true,
            "data"=>[
                "email"=>$email,
                "role"=>$result_token[0]['role'],
                "companycode"=>$result_token[0]['companycode'],
                "law"=>$result_token[0]['law'],
                "access_token"=>$access_token
            ]];
        return $arr_return;
    }catch(Exception $e){
       $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage()];
       return $arr_return;
    }
}


?>