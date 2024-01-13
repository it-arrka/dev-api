<?php 

if(isset($_POST['tokenType'])) {
    $token = "";
    if($_POST['tokenType'] == 'access_token') {

        if(isset($_COOKIE['access_token'])){
            $token = $_COOKIE['access_token'];
        }

    }else if($_POST['tokenType'] == 'refresh_token') {

        if(isset($_COOKIE['refresh_token'])){
            $token = $_COOKIE['refresh_token'];
        }
    }

    echo json_encode([ 'token' => $token ]);

}else{
    echo json_encode([ 'token' => '' ]);
}

?>