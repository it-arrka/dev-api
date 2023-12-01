<?php 

function UserSignupHandler($funcCallType){
    try{
        switch($funcCallType){
        case "promocodeDetails":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }

            if(isset($json['hashcode'])){
                $output = get_promocode_details($json['hashcode']);
                if($output['success']){
                    commonSuccessResponse($output['code'],$output['data']);
                }else{
                    catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
               catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;
        case "signup":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
              catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }

            $output = temp_signup_create($json);
            if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
            }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
            break;

        case "signupSetPassword":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }

            $output = create_company_n_customer_from_temp($json);
            if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
            }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
            break;

        case "signupEmailVerified":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }

            $output = create_company_n_customer_from_temp_for_existing($json);
            if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
            }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
            break;

            

        case "signupLawlist":
            $output = get_signup_law_list();
            if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
            }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
            break;

        case "signupVerifyLink":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }

            if(isset($json['hashcode']) && isset($json['type'])){
                $output = signup_link_verification($json['hashcode'], $json['type']);
                if($output['success']){
                    commonSuccessResponse($output['code'],$output['data']);
                }else{
                    catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
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

/**
 * @param mixed $hashcode
 */
function get_promocode_details($hashcode){
    try {
        global $session;
        $hashcode = escape_input($hashcode);
        if($hashcode == ""){
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid hashcode" ]; exit();
        }

        $result_pr = $session->execute($session->prepare('SELECT * FROM promocode WHERE hashcode=? ALLOW FILTERING'), array('arguments' => array($hashcode)));
        if ($result_pr->count() == 0) {
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid hashcode" ]; exit();
        }

        $arr = [
            "promocode" => $result_pr[0]['promocode_value'],
            "subscription_msg" => $result_pr[0]['subscription_msg']
        ];
        $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
}

function temp_signup_create($data)
{
  try {
    global $session;
    $required_keys = ["companyname", "firstname", "lastname", "email", "law", "tncprivacy", "tncconfirm", "hashcode"];
    $required_keys_val = ["companyname", "firstname", "lastname", "email", "law"];
    
    //check if array is valid
    if(!checkKeysExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    if(!checkValueExist($data, $required_keys_val)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>implode(", ",$required_keys_val)." value is mandatory" ]; exit();
    }

    $companyname = escape_input($data["companyname"]);
    $firstname = escape_input($data["firstname"]);
    $lastname = escape_input($data["lastname"]);
    $email = escape_input($data["email"]);
    $law = escape_input($data["law"]);
    $hashcodePromo = escape_input($data["hashcode"]);
    $tncprivacy = escape_input($data["tncprivacy"]);
    $tncconfirm = escape_input($data["tncconfirm"]);

    $cin = "";
    $gstn = "";
    $tanpan = "";
    $middlename = "";
    $subscription = "Trial";
    $product = "Arrka Privacy Management Platform";
    $number_of_users = "1 to 5";
    $email=strtolower($email);

    //validate promocode
    $promocodeDetails = get_promocode_details($hashcodePromo);
    if(!$promocodeDetails['success']){
        return $promocodeDetails; exit();
    }
    $promocode = $promocodeDetails['data']['promocode'];

    //validate law
    if(!validate_law_for_signup($law)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid law" ]; exit();
    }

    $res_cemail=$session->execute($session->prepare('SELECT hashcode FROM temp_registration WHERE custemailaddress=? ALLOW FILTERING'),array('arguments'=>array($email)));
    foreach ($res_cemail as $row_cemail) {
      $session->execute($session->prepare('UPDATE temp_registration SET linkstatus=? WHERE hashcode=?'),array('arguments'=>array("0",$row_cemail['hashcode'])));
    }

    //Check if email is valid
    if (!validateEmail($email)) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid email" ]; exit();
    }

    //check if tnc is checked
    if (!$tncconfirm) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"tncconfirm needs to be checked" ]; exit();
    }
    if (!$tncprivacy) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"tncprivacy needs to be checked" ]; exit();
    }

    //CREATE AS A NEW COMPANY
    $hashcode =(string)new \Cassandra\Uuid();

    $linkdate=date("d-m-Y H:i:s");
    $linkexpirydate=date("d-m-Y H:i:s",strtotime('+120 hours', strtotime($linkdate)));

    //self_assessment
    $self_assessment_flag='0';

    //Insert data temp_company
    $query_insert_in_company =$session->prepare('INSERT INTO temp_registration(
      hashcode,companyname,clientcinno,cgstnumber,clienttanpan,custfname,custlname,
      custmname,custemailaddress,csubscriptiontype,linkstatusdate,createdate,linkexpirydate,linkstatus,cplatformusers,product,law,promocode_value,self_assessment_flag
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $session->execute($query_insert_in_company,array('arguments'=>array(
      $hashcode,$companyname,$cin,$gstn,$tanpan,$firstname,$lastname,$middlename,
      $email,$subscription,$linkdate,new \Cassandra\Timestamp(),$linkexpirydate,"1",$number_of_users,$product,$law,$promocode,$self_assessment_flag
    )));

      $link_to_send =$_ENV['MAIN_PAGE_URL']."/signup-verify?hashcode=".base64_encode($hashcode)."&type=00100sp";
      //send Invite link
      $mail_temp=new_signup_verification_mail_template($email,$link_to_send,$companyname);
      $subject=$mail_temp['subject'];
      $mailbody=$mail_temp['mailbody'];
      send_mail([$email],[],$subject,$mailbody);

      // //email @help@arrka.com
      $mail_temp_help=registration_sign_up_mail_template($email,$companyname,$law);
      $subject_help=$mail_temp_help['subject'];
      $mailbody_help=$mail_temp_help['mailbody'];
      send_mail([$_ENV['HELP_EMAIL']],[],$subject_help,$mailbody_help);

      $arr_return=["code"=>200, "success"=>true, "data"=>["message"=>"success"] ];
      return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

/**
 * @param $hashcode string
 * @param $type string
 */
function signup_link_verification($hashcode, $type){
    try{
        global $session;
        $hashcode = escape_input($hashcode);
        $type = escape_input($type);

        if($hashcode == "" || $type ==""){
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link" ]; exit();
        }

        //validate type
        if ($type!="00100sp") {
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link" ]; exit();
        }

        if(!isValidBase64($hashcode)) {
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link" ]; exit();
        }

        //validate hashcode
        $hashcode =base64_decode($hashcode);
        $result =$session->execute($session->prepare("SELECT * FROM temp_registration WHERE hashcode=?"),array('arguments'=>array($hashcode)));

        if($result->count() == 0){
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid link" ]; exit();
        }
        $custemail =$result[0]['custemailaddress'];

		    $session->execute($session->prepare('UPDATE temp_registration SET modifydate=? WHERE hashcode=?'),array('arguments'=>array(new \Cassandra\Timestamp(),$hashcode)));

        //Verify linkstatus
        if ($result[0]['linkstatus']!="1") {
            return ["code"=>200, "success"=>true, "data"=>["expire"=>true, "setpassword" =>false, "message"=> "Link Expired"] ]; exit();
        }
        
        $linkexpirydate_str=$result[0]['linkexpirydate'];
		    if($linkexpirydate_str==''){
            return ["code"=>200, "success"=>true, "data"=>["expire"=>true, "setpassword" =>false, "message"=> "Link Expired"] ]; exit();
        }

        $linkexpirydate=strtotime($linkexpirydate_str);
        $nowdate=strtotime(date("d-m-Y H:i:s"));
        if ($nowdate>$linkexpirydate) {
                return ["code"=>200, "success"=>true, "data"=>["expire"=>true, "setpassword" =>false, "message"=> "Link Expired"] ]; exit();
        }

        $res_cust_check=$session->execute($session->prepare('SELECT custemailaddress FROM customer WHERE custemailaddress=?'),array('arguments'=>array($custemail)));
		    if ($res_cust_check->count()==0) {
        return ["code"=>200, "success"=>true, "data"=>["expire"=>false, "setpassword" =>true, "message"=> "Set Password"] ]; exit();
        }else{
            return ["code"=>200, "success"=>true, "data"=>["expire"=>false, "setpassword" =>false, "message"=> "Email Verified"] ]; exit();
        }
    } catch (\Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
}

function get_signup_law_list()
{
  try {
    global $session; $array=array();
    $result =$session->execute($session->prepare("SELECT law FROM compliance_framework_txn WHERE status=? AND platform_show_status=? ALLOW FILTERING"),array('arguments'=>array("1","1")));
    foreach ($result as $key) { array_push($array,$key['law']); }
    sort($array);
    return ["code"=>200, "success"=>true, "data"=>array_unique($array) ];
  }
  catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function create_company_n_customer_from_temp($data)
{
  try {
    global $session;

    $required_keys = ['hashcode', 'password', 'repassword'];
    
    if(!checkKeysExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    if(!checkValueExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>implode(", ",$required_keys)." value is mandatory" ]; exit();
    }

    $hash = $data['hashcode'];
    $pass = escape_input($data['password']);
    $repass = escape_input($data['repassword']);

    if(!isValidBase64($hash)) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid hashcode" ]; exit();
    }

    $hash = base64_decode($hash);

    if(!isPasswordValid($pass)){
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid password" ]; exit();
    }

    if ($pass!=$repass) {
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Password did not match" ]; exit();
    }

    $res_data=$session->execute($session->prepare('SELECT * FROM temp_registration WHERE hashcode=?'),array('arguments'=>array($hash)));
    if($res_data->count()== 0){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid hashcode" ]; exit();
    }

    $hashcode =(string) new\Cassandra\Uuid();
    $companycode=(string) new\Cassandra\Uuid();
    $custcode=(string) new\Cassandra\Uuid();

    $companyname=$res_data[0]['companyname'];
    $email=$res_data[0]['custemailaddress'];
    $product_i=$res_data[0]['product'];
    $law=$res_data[0]['law'];
    $cplatformusers=$res_data[0]['cplatformusers'];
    $promocode_value=$res_data[0]['promocode_value'];

    $result_promocode=$session->execute($session->prepare("SELECT period FROM promocode WHERE promocode_value=? AND promocode_status=? ALLOW FILTERING"),array('arguments'=>array($promocode_value,"1")));
    if ($result_promocode->count()==0) { echo "Promocode is not valid!"; exit(); }
    $period=$result_promocode[0]['period'];
    if ($period=='') {
      $today_date=strtotime(date("Y-m-d"));
      $cexpirydate=date("Y-m-d",strtotime("+180 day", $today_date));
    }else {
      $today_date=strtotime(date("Y-m-d"));
      $today_date_add="+".$period." day";
      $cexpirydate=date("Y-m-d",strtotime($today_date_add, $today_date));
    }

    //Reference ID like $hash to company.
    //CREATE AS A NEW COMPANY
    $query_insert_in_company =$session->prepare('INSERT INTO company(
      companycode,
      companyname ,
      cgroupname ,
      cemail ,
      csubscriptiontype ,
      hashcode,
      createdate,
      effectivedate,
      profilestatus,
      clientcinno,
      cgstnumber,
      clienttanpan,
      cplatformusers,
      cexpirydate,
      promocode_value,
      ext_ref_id
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $session->execute($query_insert_in_company,array('arguments'=>array(
      $companycode,
      $companyname,
      "Arrka-External",
      $email,
      $res_data[0]['csubscriptiontype'],
      $hashcode,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      "0",
      $res_data[0]['clientcinno'],
      $res_data[0]['cgstnumber'],
      $res_data[0]['clienttanpan'],
      $cplatformusers,
      $cexpirydate,
      $promocode_value,
      $res_data[0]['hashcode']
    )));

//Customer entry  temp_customer
      $query_insert_in_customer =$session->prepare('INSERT INTO customer(
        custfname,
        custmname ,
        custlname ,
        custcompanycode ,
        custcompanyname ,
        custcode,
        custemailaddress,
        hashcode,
        createdate,
        effectivedate,
        custuserpasswd,
        tncflag
      )
      VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
      $session->execute($query_insert_in_customer,array('arguments'=>array(
        $res_data[0]['custfname'],
        $res_data[0]['custmname'],
        $res_data[0]['custlname'],
        $companycode,
        $companyname,
        $custcode,
        strtolower($email),
        $hashcode,
        new \Cassandra\Timestamp(),
        new \Cassandra\Timestamp(),
        password_hash($pass,PASSWORD_DEFAULT),
        "1"
      )));

      $roles_arr=array("IT Head","HR","Legal Head","Internal Audit Head","CISO","Admin Head","Function Head","Risk Head","DPO","Admin");

      foreach ($roles_arr as $role_v) {
      $res_pages=$session->execute($session->prepare('SELECT createaccess,modifyaccess,viewaccess FROM rolematrix WHERE rolename=? ALLOW FILTERING'),array('arguments'=>array($role_v)));
      foreach ($res_pages as $pages) {
        if ($role_v=='CISO' || $role_v=='DPO' || $role_v=='Admin') {

          //Assign all module to CISO & DPO
          $createaccess =module_assign_pages("create");
          $modifyaccess =module_assign_pages("modify");
          $viewaccess=module_assign_pages("view");
        }else {
          $createaccess =$pages['createaccess'];
          $modifyaccess =$pages['modifyaccess'];
          $viewaccess =$pages['viewaccess'];
        }

        $session->execute($session->prepare('INSERT INTO roletocustomer(rtcuuid,rtccustcode,rtccustemail,rtcrole,createaccess,modifyaccess,viewaccess,companycode,rolestatus,createdate,effectivedate) VALUES(?,?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(
          new \Cassandra\Uuid(),$custcode,$email,$role_v,$createaccess,$modifyaccess,$viewaccess,$companycode,"1",new \Cassandra\Timestamp(),new \Cassandra\Timestamp())));
        }
      }
      $session->execute($session->prepare('INSERT INTO statusrecord(custemail,invitestatus,custcode,createdate,effectivedate) VALUES(?,?,?,?,?)'),array('arguments'=>array($email,"0",$custcode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp())));

      //Product
        $session->execute($session->prepare('INSERT INTO productid(id,companycode,createdate,effectivedate,custemail,custrole,product,module,status) VALUES(?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(
          new \Cassandra\Uuid(),$companycode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$email,"NA",$product_i,$law,"1")));

      //Applicable law
        $session->execute($session->prepare('INSERT INTO applicablelaw(id,companycode,createdate,effectivedate,custemail,custcode,law,status,expirydate,subscription_type) VALUES(?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(
          new \Cassandra\Uuid(),$companycode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$email,$custcode,$law,"1",$cexpirydate,"Trial")));

        $insert_data_into_client_product_subscription=insert_data_into_client_product_subscription($law,$companycode,$cexpirydate,"Quarterly",1,"",$email,"",$custcode);
        if (!$insert_data_into_client_product_subscription['success']) {
          return $insert_data_into_client_product_subscription; exit();
        }

      $session->execute($session->prepare('UPDATE temp_registration SET linkstatus=? WHERE hashcode=?'),array('arguments'=>array("0",$hash)));

      //email @help@arrka.com
      $mail_temp_help=registration_verification_mail_template($email,$companyname,$law);
      $subject_help=$mail_temp_help['subject'];
      $mailbody_help=$mail_temp_help['mailbody'];
      send_mail(array($_ENV['HELP_EMAIL']),array(),$subject_help,$mailbody_help);

      return ["code"=>200, "success"=>true, "data"=>['message' => 'success'] ];

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}


function create_company_n_customer_from_temp_for_existing($data)
{
  try {
    global $session;
    $required_keys = ['hashcode'];
    
    if(!checkKeysExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    if(!checkValueExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>implode(", ",$required_keys)." value is mandatory" ]; exit();
    }

    if(!isValidBase64($data['hashcode'])) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid hashcode" ]; exit();
    }
    $hash = base64_decode($data['hashcode']);

    $res_data=$session->execute($session->prepare('SELECT * FROM temp_registration WHERE hashcode=?'),array('arguments'=>array($hash)));

    $hashcode =(string) new\Cassandra\Uuid();
    $companycode=(string) new\Cassandra\Uuid();

    $companyname=$res_data[0]['companyname'];
    $email=$res_data[0]['custemailaddress'];
    $law=$res_data[0]['law'];

    $res_customer=$session->execute($session->prepare('SELECT custcode FROM customer WHERE custemailaddress=?'),array('arguments'=>array($email)));
    $custcode=$res_customer[0]['custcode'];

    //Fetch existing data
    $res_cust_check=$session->execute($session->prepare('SELECT custemailaddress,custcompanycode,custcode FROM customer WHERE custemailaddress=?'),array('arguments'=>array($email)));

    //cexpirydate
    $today_date=strtotime(date("Y-m-d"));
    $cexpirydate=date("Y-m-d",strtotime("+180 day", $today_date));

    //CREATE AS A NEW COMPANY
    $query_insert_in_company =$session->prepare('INSERT INTO company(
      companycode,
      companyname ,
      cgroupname ,
      cemail ,
      csubscriptiontype ,
      hashcode,
      createdate,
      effectivedate,
      profilestatus,
      clientcinno,
      cgstnumber,
      cexpirydate,
      promocode_value,
      ext_ref_id
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $session->execute($query_insert_in_company,array('arguments'=>array(
      $companycode,
      $companyname,
      "Arrka-External",
      $email,
      $res_data[0]['csubscriptiontype'],
      $hashcode,
      new \Cassandra\Timestamp(),
      new \Cassandra\Timestamp(),
      "0",
      $res_data[0]['clientcinno'],
      $res_data[0]['cgstnumber'],
      $cexpirydate,
      $res_data[0]['promocode_value'],
      $res_data[0]['hashcode']
    )));

//Customer entry  temp_customer
      $query_insert_in_customer =$session->prepare('INSERT INTO custassignedcompany(
         id,
         assignedcompany,
         companycode,
         createdate,
         custcode,
         custemail,
         effectivedate,
         status
      )
      VALUES(?,?,?,?,?,?,?,?)');
      $session->execute($query_insert_in_customer,array('arguments'=>array(
        new \Cassandra\Uuid(),
        $companycode,
        $res_cust_check[0]['custcompanycode'],
        new \Cassandra\Timestamp(),
        $custcode,
        $email,
        new \Cassandra\Timestamp(),
        "1"
      )));

      $roles_arr=array("IT Head","HR","Legal Head","Internal Audit Head","CISO","Admin Head","Function Head","Risk Head","DPO","Admin");

      foreach ($roles_arr as $role_v) {
      $res_pages=$session->execute($session->prepare('SELECT createaccess,modifyaccess,viewaccess FROM rolematrix WHERE rolename=? ALLOW FILTERING'),array('arguments'=>array($role_v)));
      foreach ($res_pages as $pages) {

        if ($role_v=='CISO' || $role_v=='DPO' || $role_v=='Admin') {

          //Assign all module to CISO & DPO
          $createaccess =module_assign_pages("create");
          $modifyaccess =module_assign_pages("modify");
          $viewaccess=module_assign_pages("view");
        }else {
          $createaccess =$pages['createaccess'];
          $modifyaccess =$pages['modifyaccess'];
          $viewaccess =$pages['viewaccess'];
        }

        $session->execute($session->prepare('INSERT INTO roletocustomer(rtcuuid,rtccustcode,rtccustemail,rtcrole,createaccess,modifyaccess,viewaccess,companycode,rolestatus,createdate,effectivedate) VALUES(?,?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(
          new \Cassandra\Uuid(),$custcode,$email,$role_v,$createaccess,$modifyaccess,$viewaccess,$companycode,"1",new \Cassandra\Timestamp(),new \Cassandra\Timestamp())));
        }
      }
      $session->execute($session->prepare('INSERT INTO statusrecord(custemail,invitestatus,custcode,createdate,effectivedate) VALUES(?,?,?,?,?)'),array('arguments'=>array($email,"0",$custcode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp())));

      //Product
      $product=array("Arrka Privacy Management Platform"=>"Arrka Privacy Management Platform");
      foreach ($product as $key => $value) {
        $session->execute($session->prepare('INSERT INTO productid(id,companycode,createdate,effectivedate,custemail,custrole,product,module,status) VALUES(?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(
          new \Cassandra\Uuid(),$companycode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$email,"NA",$key,$value,"1")));
      }

      //Applicable law
        $session->execute($session->prepare('INSERT INTO applicablelaw(id,companycode,createdate,effectivedate,custemail,custcode,law,status,expirydate,subscription_type) VALUES(?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(
          new \Cassandra\Uuid(),$companycode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),$email,$custcode,$law,"1",$cexpirydate,"Trial")));
        //insert into client_product_subscription
        $insert_data_into_client_product_subscription=insert_data_into_client_product_subscription($law,$companycode,$cexpirydate,"Quarterly",1,"",$email,"",$custcode);
        if (!$insert_data_into_client_product_subscription['success']) {
          return $insert_data_into_client_product_subscription['msg']; exit();
        }

      $session->execute($session->prepare('UPDATE temp_registration SET linkstatus=? WHERE hashcode=?'),array('arguments'=>array("0",$hash)));

      //email @help@arrka.com
      $mail_temp_help=registration_verification_mail_template($email,$companyname,$law);
      $subject_help=$mail_temp_help['subject'];
      $mailbody_help=$mail_temp_help['mailbody'];
      send_mail(array($_ENV['HELP_EMAIL']),array(),$subject_help,$mailbody_help);


      return ["code"=>200, "success"=>true, "data"=>['message' => 'success'] ];

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>