<?php
try {
    if(isset($_POST['access_token']) && $_POST['refresh_token']){

        $cookieExpiration = time() + (7 * 24 * 60 * 60);
        $access_token = $_POST['access_token'];
        $refresh_token = $_POST['refresh_token'];
    
        setcookie("access_token", '$access_token', $cookieExpiration, '/; samesite=none', '', true, true);
        setcookie("refresh_token", '$refresh_token', $cookieExpiration, '/; samesite=none', '', true, true);

        // Set the SameSite attribute in the Set-Cookie header
        // header('Set-Cookie: access_token=' . $access_token . '; expires=' . gmdate('D, d M Y H:i:s T', $cookieExpiration) . '; path=/; domain=; secure; HttpOnly; SameSite=None');
        // header('Set-Cookie: refresh_token=' . $refresh_token . '; expires=' . gmdate('D, d M Y H:i:s T', $cookieExpiration) . '; path=/; domain=; secure; HttpOnly; SameSite=None');


        $arr = [ "success" => true, "message" => "Token is set" ];
        echo json_encode($arr);
    
    }else{
        $arr = [ "success" => false, "error" => "Could not set token", "data" => $_POST ];
        echo json_encode($arr);
    }
} catch (\Exception $e) {
    $arr = [ "success" => false, "error" => $e->getMessage() ];
    echo json_encode($arr);
}

?>