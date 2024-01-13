<?php 

// Define constants for log levels
// LOG_EMERG	system is unusable
// LOG_ALERT	action must be taken immediately
// LOG_CRIT	critical conditions
// LOG_ERR	error conditions
// LOG_WARNING	warning conditions
// LOG_NOTICE	normal, but significant, condition
// LOG_INFO	informational message
// LOG_DEBUG	debug-level message

function write_log($log_level=LOG_INFO,$msg,$url_c,$url_p){
    try {
        // Define the log file name
        $logFile = dirname(__DIR__)."/log/masterlog.log";

        // Get the current date and time for the log entry
        $currentdatetime = date("Y-m-d H:i:s");
        $timestamp = strtotime($currentdatetime);
        // Get the user's IP address
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        // Get the User-Agent string
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        //session id
        $session_id=session_id();
        //http_response_code
        $http_response_code=http_response_code();
        //email
        $email=""; if(isset($_SESSION['email'])){ $email=$_SESSION['email']; }
        //role
        $role=""; if(isset($_SESSION['role'])){ $role=$_SESSION['role']; }
        //companycode
        $companycode=""; if(isset($_SESSION['companycode'])){ $companycode=$_SESSION['companycode']; }
        
        // Append the log entry to the log file
        $log_to_write =$log_level."|".$ipAddress.'|'.$currentdatetime.'|'.$timestamp.'|'.$msg.'|'.$url_c.'|'.$url_p.'|'.$session_id.'|'.$http_response_code.'|'.$userAgent.'|'.$role.'|'.$email.'|'.$companycode. PHP_EOL;

        file_put_contents($logFile, $log_to_write, FILE_APPEND);

        return true;

    } catch (Exception $e) {
        return false;
    }
}

?>