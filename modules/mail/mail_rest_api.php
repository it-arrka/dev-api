<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function SendMailHandler($funcCallType){
    try{
      switch($funcCallType){
        case "sendmail":

            $mailto = ""; if(isset($_POST["mailto"])){ $mailto =$_POST["mailto"];  }
            $mailcc = ""; if(isset($_POST["mailcc"])){ $mailcc =$_POST["mailcc"];  }
            
            $mailto_arr = []; if($mailto != ""){ $mailto_arr = explode(";",$mailto); }
            $mailcc_arr = []; if($mailcc != ""){ $mailcc_arr = explode(";",$mailcc); }

            $subject = ""; if(isset($_POST["subject"])){ $subject =$_POST["subject"];  }
            $mailbody = ""; if(isset($_POST["mailbody"])){ $mailbody =$_POST["mailbody"];  }

            $output = send_mail($mailto_arr,$mailcc_arr,$subject,$mailbody);
            if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
            }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
        
            break;

        case "sendmailwithattachment":

            $mailto = ""; if(isset($_POST["mailto"])){ $mailto =$_POST["mailto"];  }
            $mailcc = ""; if(isset($_POST["mailcc"])){ $mailcc =$_POST["mailcc"];  }
            
            $mailto_arr = []; if($mailto != ""){ $mailto_arr = explode(";",$mailto); }
            $mailcc_arr = []; if($mailcc != ""){ $mailcc_arr = explode(";",$mailcc); }

            $subject = ""; if(isset($_POST["subject"])){ $subject =$_POST["subject"];  }
            $mailbody = ""; if(isset($_POST["mailbody"])){ $mailbody =$_POST["mailbody"];  }
            $filelocation = ""; if(isset($_POST["filelocation"])){ $filelocation =$_POST["filelocation"];  }

            $output = send_mail_with_attachment($mailto_arr,$mailcc_arr,$subject,$mailbody,$filelocation);
            if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
            }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
        
            break;

            default:
                catchErrorHandler(400,[ "message"=>E_INV_REQ, "error"=>"" ]);
                break;
      }
    }catch(Exception $e){
      catchErrorHandler($output['code'], [ "message"=>"", "error"=>(string)$e ]);
    }
}

function send_mail_with_attachment($mailto_arr,$mailcc_arr,$subject,$mailbody,$file_location,$key='')
{

  if(count($mailto_arr) == 0){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid mail to address" ]; exit();
  }

  if($subject == ""){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid subject" ]; exit();
  }

  if($mailbody == ""){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid mailbody" ]; exit();
  }

  if(!file_exists($file_location)){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid file" ]; exit();
  }

  $mail_host = $_ENV['MAIL_HOST'];
  $mail_port = $_ENV['MAIL_PORT'];
  $mail_username = $_ENV['MAIL_USERNAME'];
  $mail_password = $_ENV['MAIL_PASSWORD'];

    if($key != '0')
    {
        $mail = new PHPMailer;
        try {
            $mail->isSMTP();
            // $mail->SMTPDebug = 2; //This show multiple message
            $mail->Host = $mail_host;
            $mail->Port = (int)$mail_port;
            $mail->SMTPAuth = true;
            $mail->Username = $mail_username;
            $mail->Password = $mail_password;
            // $mail->addReplyTo($mail_username, 'No Reply');
            $mail->setFrom($mail_username,'Arrka',false);
            foreach ($mailto_arr as $mailto) { $mail->addAddress($mailto); }
            foreach ($mailcc_arr as $mailcc) { $mail->addCC($mailcc); }
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $mailbody;
            $mail->addAttachment($file_location);
            $mail->send();
            $arr_return=["code"=>200, "success"=>true, "data"=>['message' => 'mail sent'] ];
            return $arr_return;
        } catch (Exception $e) {
            return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
        }
    }else{
        $arr_return=["code"=>200, "success"=>true, "data"=>['message' => 'mail sent'] ];
        return $arr_return;
    }
}

function send_mail($mailto_arr,$mailcc_arr,$subject,$mailbody,$key='')
{

  if(count($mailto_arr) == 0){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid mail to address" ]; exit();
  }

  if($subject == ""){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid subject" ]; exit();
  }

  if($mailbody == ""){
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid mailbody" ]; exit();
  }

  $mail_host = $_ENV['MAIL_HOST'];
  $mail_port = $_ENV['MAIL_PORT'];
  $mail_username = $_ENV['MAIL_USERNAME'];
  $mail_password = $_ENV['MAIL_PASSWORD'];

    if($key != '0')
    {
        $mail = new PHPMailer;
        try {
            $mail->isSMTP();
            // $mail->SMTPDebug = 2; //This show multiple message
            $mail->Host = $mail_host;
            $mail->Port = (int)$mail_port;
            $mail->SMTPAuth = true;
            $mail->Username = $mail_username;
            $mail->Password = $mail_password;
            // $mail->addReplyTo($mail_username, 'No Reply');
            $mail->setFrom($mail_username,'Arrka',false);
            foreach ($mailto_arr as $mailto) { $mail->addAddress($mailto); }
            foreach ($mailcc_arr as $mailcc) { $mail->addCC($mailcc); }
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $mailbody;
            //$mail->addAttachment('test.txt');
            $mail->send();
            $arr_return=["code"=>200, "success"=>true, "data"=>['message' => 'mail sent'] ];
            return $arr_return;
        } catch (Exception $e) {
            return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
        }
    }else{
        $arr_return=["code"=>200, "success"=>true, "data"=>['message' => 'mail sent'] ];
        return $arr_return;
    }
}

?>