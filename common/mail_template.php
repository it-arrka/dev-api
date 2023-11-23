<?php
function mail_signature($userid)
{
  $sign="For any query reach out to <a href='mailto:help@arrka.com'>help@arrka.com</a>.";
  $sign.="<br><div style='text-align:center;'>";
  $sign.="<br>This email was meant for <a href='mailto:$userid'>$userid</a><br>Arrka Infosec Pvt. Ltd. India</div><br><br>";
  $sign.="<i>This is a system generated e-mail. We request you not to reply to this e-mail. </i><br><br>";
  $sign.="<i>This email may contain confidential information and/or copyright material. This email is intended for the use of the addressee only. Any unauthorized use may be unlawful.</i>";
  return $sign;
}

function custom_signup_mail_signature($userid)
{
  $sign="For any query reach out to <a href='mailto:help@arrka.com'>help@arrka.com</a>.";
  $sign.="<div style='text-align:center;'><br>This email was meant for <a href='mailto:$userid'>$userid</a><br>Arrka Infosec Pvt. Ltd. India</div><br><br>";
  $sign.="<i>This is a system generated e-mail. We request you not to reply to this e-mail. </i><br><br>";
  $sign.="<i>This email may contain confidential information and/or copyright material. This email is intended for the use of the addressee only. Any unauthorized use may be unlawful.</i>";
  return $sign;
}

function email_verification_mail_template($userid,$link_to_send)
{
  $subject='Arrka | Email Verification Required';
  $mailbody="<br><strong>$userid</strong> <br><br> You have one more step remaining to activate your Arrka Privacy Management Platform account. Click on the button below to verify your email address:<br><br>";
  $mailbody.="<div style='text-align:left;'><a href=".$link_to_send." target='_blank'><button type='button' style='background:#0275d8; color:#fff; padding:10px; border-radius:5px;'>Verify Email</button></a></div>";
  $mailbody.="<br> Didn't work? Copy the link below into your web browser: <br><a href=".$link_to_send." target='_blank'>".$link_to_send."</a> <br><br><br>".custom_signup_mail_signature($userid);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function new_signup_verification_mail_template($userid,$link_to_send,$companyname)
{
  $subject='Arrka | Email Verification Required';
  $mailbody="<br><strong>$userid</strong> <br><br> You have one more step remaining to add new client <strong>".$companyname."</strong>. Click on the button below to verify your email address:<br><br>";
  $mailbody.="<div style='text-align:left;'><a href=".$link_to_send." target='_blank'><button type='button' style='background:#0275d8; color:#fff; padding:10px; border-radius:5px;'>Verify Email</button></a></div>";
  $mailbody.="<br> Didn't work? Copy the link below into your web browser: <br><a href=".$link_to_send." target='_blank'>".$link_to_send."</a> <br><br><br>".custom_signup_mail_signature($userid);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function password_reset_mail_template($userid,$link_to_send,$firstname)
{
  $subject='Arrka | Password Reset Request';
  $mailbody="<br>Dear <strong>".$firstname."</strong><br><br>";
  $mailbody.="We have received your request for reset password.<br>To reset, please click the link below:<br><br><a href=".$link_to_send." target='_blank'>".$link_to_send."</a><br><br>";
  $mailbody.="This link is valid for 24 hours from your request initiation for password recovery.";
  $mailbody.="<br><br><br>".mail_signature($userid);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function new_task_assigned_mail_template($userid,$link_to_send,$assessment_name,$type_task)
{
  $subject='APMP | New Task Assigned';
  $mailbody="<br><strong>$userid</strong><br><br>You have been assigned a new task. Click on the button below to complete the task.<br><br>";
  $mailbody.="Assessment Name:$assessment_name <br> Tast Type: $type_task<br><br>";
  $mailbody.="<div><a href=".$link_to_send." target='_blank'><button type='button' style='background:#0275d8; color:#fff; padding:10px; border-radius:5px;'>Go To Task</button></a></div>";
  $mailbody.="<br> Didn't work? Copy the link below into your web browser: <br><a href=".$link_to_send." target='_blank'>".$link_to_send."</a> <br><br><br>".mail_signature($userid);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function trial_reminder_mail_template($userid,$companyname,$end_date)
{
  $subject="Reminder: $companyname - Arrka Privacy Management Platform account shall expire";
  $mailbody="<br>Your account for <strong>'$companyname'</strong> shall expire on <strong>'$end_date'.</strong>";
  $mailbody.="<br><br>Please reach out to IT@arrka.com for renewal of the Arrka Privacy Management Platform account.<br><br><br>".mail_signature($userid);;
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function summary_of_all_notification($userid,$email,$content)
{
  $subject="Arrka | Your Pending Task(s)";
  $mailbody="Dear <strong>".$userid."</strong><br><br>Please take appropriate action on below request(s)";
  $mailbody.="<br>".$content."<br>";
  $mailbody.="<br>You can also take appropriate action on the Application by logging to Arrka Privacy Management Platform.<br><br><br>".mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function notification_mail_template($userid,$email,$content)
{
  $subject="Arrka Notification | You've been assigned a task!";
  $mailbody="Dear <strong>".$userid."</strong><br><br>Please take appropriate action on below request:";
  $mailbody.="<br>".$content;
  $mailbody.="<br>You can also take appropriate action on the Application by logging to Arrka Privacy Management Platform<br><br><br>".mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function module_access_request_mail_template($userid,$email,$role,$module_name)
{
  $subject="Arrka Notification | Module Access Request";
  $mailbody="Dear <strong>".$userid."</strong><br><br>Please take appropriate action on below request:<br>";
  $mailbody.="Requestor".$email;
  $mailbody.="Module Name".$module_name;
  $mailbody.="Role".$role;
  $mailbody.="<br>You can also take appropriate action on the Application by logging to Arrka Privacy Management Platform.<br><br><br>".mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function login_success_mail_template($userid)
{
  $date = date('Y-m-d H:i:s');
  $date_time=explode(" ",$date);
  $current_date=$date_time[0];
  $current_time=$date_time[1];
  $subject='Arrka | New Login Alert';
  $mailbody="<br><strong>Dear $userid, </strong><br><br>You have successfully logged in to your Arrka Privacy Management Platform on ".$current_date." at ".$current_time." If you did not take this action, please report this at help@arrka.com.<br><br>";
  //$mailbody.="<br> Best Regards, </br>".$userid ." ,"."<br>Arrka Privacy Management Platform<br><br>";
  $mailbody.="<br> Best Regards, </br> Arrka Privacy Management Platform Team<br><br>";
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function login_failed_mail_template($userid)
{
  $date = date('Y-m-d H:i:s');
  $date_time=explode(" ",$date);
  $current_date=$date_time[0];
  $current_time=$date_time[1];
  $subject='Arrka | Failed Login Alert';
  $mailbody="<br><strong>Dear $userid, </strong><br><br>You have attempted to login to your Arrka Privacy Management Platform on ".$current_date." at ".$current_time." If you did not take this action, please report this at help@arrka.com.<br><br>";
  //$mailbody.="<br> Best Regards, </br>".$userid ." ,"."<br>Arrka Privacy Management Platform<br><br>";
  $mailbody.="<br> Best Regards, </br> Arrka Privacy Management Platform Team<br><br>";
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function summary_of_all_notification_not_read($userid,$email,$content)
{
  $subject="Arrka | Your Pending Task(s)";
  $mailbody="Dear <strong>".$userid."</strong><br><br>Please take appropriate action on below request(s)";
  $mailbody.="<br>".$content."<br>";
  $mailbody.="<br>You can also take appropriate action on the Application by logging to Arrka Privacy Management Platform.<br><br><br>".mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}
function creation_client_mail_template($email,$companyname)
{
  $subject="Arrka | New Client Created Successfully";
  $mailbody="Dear <strong>".$userid."</strong><br><br>Please take appropriate action on below request(s)";
  $mailbody.="<br>".$companyname." is Successfully created"."<br>";
  $mailbody.="<br>You can also take appropriate action on the Application by logging to Arrka Privacy Management Platform.<br><br><br>".mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}
function help_certification_mail_template_read($type,$subject,$name,$email,$contact,$details,$companyname)
{
  $platform=$_ENV['HOST'];
  if ($type=='help') {
    $subject="HELP | $subject";
    $mailbody="Host: ".$platform."<br>";
    $mailbody.="<li>Name: $name</li><li>Client Name: $companyname</li><li>Email: $email</li><li>Phone Number: $contact</li><li>Details: $details</li><br><br><br>".mail_signature($email);
    return array("subject"=>$subject, "mailbody"=>$mailbody);
  }elseif ($type=='subscribe') {
    $subject="Subscribe | $subject";
    $mailbody="Host: ".$platform."<br>";
    $mailbody.="<li>Name: $name</li><li>Client Name: $companyname</li><li>Email: $email</li><li>Phone Number: $contact</li><li>Details: $details</li><br><br><br>".mail_signature($email);
    return array("subject"=>$subject, "mailbody"=>$mailbody);
  }else {
    $subject="Certification | $subject";
    $mailbody="Host: ".$platform."<br>";
    $mailbody.="<li>Name: $name</li><li>Client Name: $companyname</li><li>Email: $email</li><li>Phone Number: $contact</li><li>Details: $details</li><br><br><br>".mail_signature($email);
    return array("subject"=>$subject, "mailbody"=>$mailbody);
  }
}
function registration_sign_up_mail_template($email,$companyname,$law)
{
  $platform=$_ENV['HOST'];
  $subject="New Registration |".$companyname;
  $mailbody="Host: ".$platform."<br>";
  $mailbody.="Client Name:".$companyname;
  $mailbody.="<br>Email id:".$email."<br>";
  $mailbody.="<br>Law / Framework:".$law."<br>";
  mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function registration_verification_mail_template($email,$companyname,$law)
{
  $platform=$_ENV['HOST'];
  $subject="Verified |".$companyname;
  $mailbody="Host: ".$platform."<br>";
  $mailbody.="Client Name:".$companyname;
  $mailbody.="<br>Email id:".$email."<br>";
  $mailbody.="<br>Law / Framework:".$law."<br>";
  mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function offline_payment_mail_template($companyname,$email,$cgstnumber,$product,$productid,$sub_end_date,$licence_period,$unit,$balance,$referenceid,$amount,$discount,$tax,$total,$addonFlag)
{
  $platform=$_ENV['HOST'];
  $subject="Offline Payment Request |".$companyname;
  $mailbody="Host: ".$platform."<br>";
  $mailbody.="Client Name: ".$companyname."<br>";
  $mailbody.="<br>Email: ".$email."<br>";
  $mailbody.="<br>Product ID: ".$productid."<br>";
  $mailbody.="<br>Product: ".$product."<br>";
  $mailbody.="<br>GSTN / TAN: ".$cgstnumber."<br>";
  if ($addonFlag) {
    $mailbody.="<br>Unit: ".$unit."<br>";
    $mailbody.="<br>Balance: ".$balance."<br>";
  }else {
    $mailbody.="<br>Subscription Period: ".$licence_period." Days<br>";
  }
  $mailbody.="<br>Reference ID: ".$referenceid."<br>";
  $mailbody.="<br>Amount: ".$amount."<br>";
  $mailbody.="<br>Discount: ".$discount."<br>";
  $mailbody.="<br>Tax: ".$tax."%<br>";
  $mailbody.="<br>Total: ".$total."<br>";
  mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}

function offline_payment_cancel_request_mail_template($companyname,$email,$product,$productid,$referenceid,$reason)
{
  $platform=$_ENV['HOST'];
  $subject="Cancel Offline Payment Request |".$companyname;
  $mailbody="Host: ".$platform."<br>";
  $mailbody.="Client Name: ".$companyname."<br>";
  $mailbody.="<br>Email: ".$email."<br>";
  $mailbody.="<br>Product ID: ".$productid."<br>";
  $mailbody.="<br>Product: ".$product."<br>";
  $mailbody.="<br>Reference ID: ".$referenceid."<br>";
  $mailbody.="<br>Reason for Cancellation: ".$reason."<br>";
  mail_signature($email);
  return array("subject"=>$subject, "mailbody"=>$mailbody);
}
?>
