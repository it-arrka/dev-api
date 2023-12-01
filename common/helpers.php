<?php
/**
 * @param mixed $dataArray [associative array]
 * @param mixed $requiredKeys [array]
 */
function checkKeysExist($dataArray, $requiredKeys) {
  foreach ($requiredKeys as $key) {
      if (!array_key_exists($key, $dataArray)) {
          return false; // If any key is missing, return false
      }
  }
  return true; // All keys exist
}


function checkValueExist($dataArray, $requiredKeys) {
  foreach ($requiredKeys as $key) {
      if (isset($dataArray[$key])) {
        if(is_string($dataArray[$key]) || is_numeric($dataArray[$key])) {
          if($dataArray[$key] == ""){
            return false; exit();
          }
        }else{
          return false; exit();
        }
      }else{
        return false; exit();
      }
  }
  return true; 
}


//table crud request API
function table_crud_actions($data)
{
  try {
    global $session;
    // $loadTableJSON = file_get_contents('new-api/table-set.json');
    // $loadTableArray=json_decode($loadTableJSON,true);

    $arr_return=[];

    //Allowed actions on table
    $action_allowed=["read","insert","update","delete"];
    //Action validation
    if(!isset($data['action'])){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Action"]; return $arr_return; exit(); }
    if (!in_array($data['action'],$action_allowed)) { $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"Invalid Action"]; return $arr_return; exit(); }
    $table_action=$data['action'];

    //table_name validation
    if(!isset($data['table_name'])){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Table Name"]; return $arr_return; exit(); }
    $table_name=$data['table_name'];
    // if (isset($loadTableArray[$table_name])) { $table_name=$data['table_name']; }

    //Query create
    $query_initiate="";
    switch ($table_action) {
      case 'read':
        $query_1="SELECT "; $result=[];

        //For column
        if (!isset($data['columns'])) { $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Table Name"]; return $arr_return; exit(); }
        $column=$data['columns'];
        if(count($column)==0){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Columns"]; return $arr_return; exit(); }

        if(count($column)==1){ if($column[0]=='all'){ $query_1.="* "; } }
        else{
          $col_query=implode(",",$column);
          $query_1.=$col_query." ";
        }

        $query_1.="FROM ".$table_name." ";
        //For Where condition
        if (isset($data['isCondition'])) {
          if ($data['isCondition']) {
            $query_1.="WHERE ";

            if (!isset($data['condition_columns']) || !isset($data['columns_data'])) { $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Conditions"]; return $arr_return; exit(); }

            $conditon_column=$data['condition_columns'];
            $conditon_column_data=$data['columns_data'];
            if(count($conditon_column)==0 || count($conditon_column_data)==0){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Conditions"]; return $arr_return; exit(); }
            if(count($conditon_column) != count($conditon_column_data)){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"Conditions column & data don't match"]; return $arr_return; exit(); }
            $col_query=implode("=?, ",$conditon_column)." =?";
            $query_1.=$col_query." ";

            //Is Allow FILTERING true
            if (isset($data['isAllowFiltering'])) {
              if ($data['isAllowFiltering']) {
                $query_1.="ALLOW FILTERING";
              }
            }
            $result=$session->execute($session->prepare($query_1), array('arguments'=>$conditon_column_data));

            $arr_value=[];
            foreach ($result as $row) { $arr_value[]=$row; }
            $arr_return=["code"=>200, "success"=>true, "data"=>$arr_value];
            return $arr_return;
          }else {
            $result=$session->execute($query_1);
            $arr_value=[];
            foreach ($result as $row) { $arr_value[]=$row; }
            $arr_return=["code"=>200, "success"=>true, "data"=>$arr_value];
            return $arr_return;
          }
        }else {
          $result=$session->execute($query_1);
          $arr_value=[];
          foreach ($result as $row) { $arr_value[]=$row; }
          $arr_return=["code"=>200, "success"=>true, "data"=>$arr_value];
          return $arr_return;
        }

        break;

      case 'insert':
          $query_1="INSERT INTO ".$table_name." ("; $result=[];
          //For column
          if (!isset($data['columns'])) { $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Table Name"]; return $arr_return; exit(); }
          $column=$data['columns'];
          if(count($column)==0){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Columns"]; return $arr_return; exit(); }

          $col_query=implode(",",$column);
          $tertiary_operator=[];
          for ($i=0; $i <count($column) ; $i++) {
            array_push($tertiary_operator,"?");
          }
          $tertiary_operator_query=implode(",",$tertiary_operator);

          $query_1.=$col_query.") VALUES(".$tertiary_operator_query.")";

          $conditon_column_data=$data['columns_data'];

          if(count($column)==0 || count($conditon_column_data)==0){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Column"]; return $arr_return; exit(); }
          if(count($column) != count($conditon_column_data)){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"Column & data don't match"]; return $arr_return; exit(); }

          $result=$session->execute($session->prepare($query_1), array('arguments'=>$conditon_column_data));

          $arr_value=['insert'=>true];
          $arr_return=["code"=>200, "success"=>true, "data"=>$arr_value];
          return $arr_return;
        break;


      case 'update':
        $query_1="UPDATE ".$table_name." SET "; $result=[];

        //For column
        if (!isset($data['columns'])) { $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Columns"]; return $arr_return; exit(); }
        $column=$data['columns'];
        $conditon_column_data=$data['columns_data'];
        if(count($column)==1){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"Should be more than 1 column."]; return $arr_return; exit(); }
        if(count($column)==0 || count($conditon_column_data)==0){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Column"]; return $arr_return; exit(); }
        if(count($column) != count($conditon_column_data)){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"Column & data don't match"]; return $arr_return; exit(); }

        // find Last column i.e primary key
        $primary_key=$column[count($column)-1];
        unset($column[count($column)-1]);

        $col_query=implode("=?,",$column)."=?";
        $query_1.=$col_query." WHERE ".$primary_key."=?";

        $result=$session->execute($session->prepare($query_1), array('arguments'=>$conditon_column_data));

        $arr_value=[];
        foreach ($result as $row) { $arr_value[]=$row; }
        $arr_return=["code"=>200, "success"=>true, "data"=>$arr_value];
        return $arr_return;
        break;

      case 'delete':
        $query_1="DELETE FROM ".$table_name." WHERE "; $result=[];

        //For column
        if (!isset($data['columns'])) { $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Columns"]; return $arr_return; exit(); }
        $column=$data['columns'];
        $conditon_column_data=$data['columns_data'];
        if(count($column)>1){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR ,"error"=>"Should not be more than 1 column."]; return $arr_return; exit(); }
        if(count($column)==0 || count($conditon_column_data)==0){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"No Column"]; return $arr_return; exit(); }
        if(count($column) != count($conditon_column_data)){ $arr_return=["code"=>500, "success"=>false, "messsage"=>E_FUNC_ERR, "error"=>"Column & data don't match"]; return $arr_return; exit(); }

        // find Last column i.e primary key
        $primary_key=$column[0];

        $query_1.=$primary_key."=?";

        $result=$session->execute($session->prepare($query_1), array('arguments'=>$conditon_column_data));

        $arr_value=[];
        foreach ($result as $row) { $arr_value[]=$row; }
        $arr_return=["code"=>200, "success"=>true, "data"=>$arr_value];
        return $arr_return;
        break;

      default:
        $arr_return=["code"=>400, "success"=>false,"message"=>E_INV_REQ, "error"=>""];
        break;
    }

  } catch (\Exception $e) {
    $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage()];
    return $arr_return;
  }
}

function validateInput($input, $validationType = 'text', $options = []) {
  // Trim input to remove leading/trailing whitespace
  $input = trim($input);
  switch ($validationType) {
      case 'text':
          // Validate as plain text (no specific rules)
          break;

      case 'email':
          // Validate as an email address
          if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
              return 'Invalid email address';
          }
          break;

      case 'number':
          // Validate as a number (integer or float)
          if (!is_numeric($input)) {
              return 'Invalid number';
          }
          break;

      case 'custom':
          // Add custom validation rules here based on $options
          // Example: Check if the input is within a specified range
          if (isset($options['min']) && $input < $options['min']) {
              return 'Input is too small';
          }
          if (isset($options['max']) && $input > $options['max']) {
              return 'Input is too large';
          }
          break;

      default:
          return 'Invalid validation type';
  }

  // If input passes validation, return true (or sanitized input)
  return $input;
}



//check_if_email_is_active
function check_if_email_is_active($email)
{
  try{
    global $session; $active=false;
    $result =$session->execute($session->prepare("SELECT rtcuuid FROM roletocustomer WHERE rtccustemail=? AND rolestatus=? ALLOW FILTERING"),array('arguments'=>array($email,"1")));
    $count=$result->count();
    if ($count>0) {
      $active=true;
    }
    $arr_return=["code"=>200, "success"=>true, "data"=>["active"=>$active]];
    return $arr_return;
  }catch(Exception $e){
    $arr_return=["code"=>500, "success"=>false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage()];
    return $arr_return;
  }
}

function escape_input($string)
{
  if (is_string($string) || is_int($string)) {
    $newstr = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    return $newstr;
  }else {
    return $string;
  }
}

function get_company_logo($companycode)
{
  try {
    global $session;
    if($companycode==""){
      //Bad Request Error
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    //validate company
    $result=$session->execute($session->prepare("SELECT clientlogoref FROM company WHERE companycode=?"),array('arguments'=>array($companycode)));
    if($result->count()==0){
      //Bad Request Error
      return ["code"=>400, "success" => false, "message"=>E_INV_REQ, "error"=>"" ]; exit();
    }

    $src_link='';

    $clientlogoref = $result[0]['clientlogoref'];
    if($clientlogoref!=''){
      $result_doc=$session->execute($session->prepare("SELECT blobAsText(docupl),doctype,docname FROM docupload WHERE docid=?"),array('arguments'=>array(new Cassandra\Uuid($clientlogoref))));
      $src_link=$result_doc->count();
      if ($result_doc->count()>0) {
        $src_link ='data:image/jpeg;base64,'.$result_doc[0]['system.blobastext(docupl)'];
      }
    }


    $arr_return=["code"=>200, "success"=>true, "data"=>['src'=>$src_link, 'clientlogoref'=>$clientlogoref]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function shortenNameString($fullName) {
  if($fullName == ""){
    return ""; exit();
  }
  // Split the full name into words
  $words = explode(' ', $fullName);
  // Initialize an array to store initials
  $initials = array();
  // Iterate through the words to extract initials
  foreach ($words as $word) {
      // Extract the first character of the word and make it uppercase
      $initial = strtoupper($word[0]);
      $initials[] = $initial;

      // Stop if the maximum length is reached
      if (count($initials) >= 2) {
          break;
      }
  }
  // Join the initials to form the short version
  $shortVersion = implode('', $initials);
  return $shortVersion;
}


/**
 * Returns 200/success=true if page has the access to the particular pageid
 * @param string $email
 * @param string $role
 * @param string $type  :: create/modify/view/all
 * @param string $pageid  :: PG001, PG002
 */
function get_page_access_by_pageid($companycode, $email, $role, $type, $pageid)
{
  global $session;
  try {

    if($companycode == '' || $email == '' || $type == '' || $pageid == '') {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    $result= $session->execute($session->prepare("SELECT createaccess,modifyaccess,viewaccess FROM roletocustomer WHERE rtccustemail=? AND rtcrole=? AND rolestatus=? AND companycode=? ALLOW FILTERING"),array('arguments'=>array(
      $email,$role,"1",$companycode
    )));

    $access = false;

    $pageAccess = [];
    foreach ($result as $row) {
      switch ($type) {
        case 'create':
          $pageAccess=explode("|",$row['createaccess']); array_shift($pageAccess);
          break;
        case 'modify':
          $pageAccess=explode("|",$row['modifyaccess']); array_shift($pageAccess);
          break;
        case 'view':
          $pageAccess=explode("|",$row['viewaccess']); array_shift($pageAccess);
          break;
        case 'all':
          $createaccess=explode("|",$row['createaccess']); array_shift($createaccess);
          $modifyaccess=explode("|",$row['modifyaccess']); array_shift($modifyaccess);
          $viewaccess=explode("|",$row['viewaccess']); array_shift($viewaccess);
          $pageAccess = array_merge($createaccess,$modifyaccess,$viewaccess);
          break;
      }
    }

    if(in_array($pageid, $pageAccess)){ $access = true; }

    if($access){
      $arr_return=["code"=>200, "success"=>true, "data"=>["access"=>true]];
      return $arr_return;
    }else{
      $arr_return=["code"=>403, "success"=>false, "message"=>E_NO_PAGE_ACCESS, "error"=>""];
      return $arr_return;
    }
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

//get Date difference
/**
 * Get date difference
 * @param int $date2Seconds  -- bigger date seconds
 * @param int $date1Seconds  -- lower date seconds
 */
function getDateDifference($date2Seconds, $date1Seconds){
  // Formulate the Difference between two dates
  $diff = abs($date2Seconds - $date1Seconds);
  $seconds = floor($diff / 1000);
  $minutes = floor($seconds / 60);
  $diffHours = "";
  if ($minutes > 60) {
      $hours = floor($seconds / 3600);
      $diffHours = $hours . " hrs ";
      if ($hours > 24) {
        $day = floor($hours / 24);
        $diffHours = $day . " days ";
      }
  } else {
      $diffHours = $minutes . " min ";
  }
  return $diffHours;
}

function date_difference($date_old,$date_new)
{
    // Declare and define two dates
  $date1 = strtotime($date_old);
  $date2 = strtotime($date_new);
  // Formulate the Difference between two dates
  $diff = abs($date2 - $date1);
  $years = floor($diff / (365*60*60*24));
  $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

  $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

  $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));

  $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

  $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
  return array("years"=>$years,"months"=>$months,"days"=>$days,"hours"=>$hours,"minutes"=>$minutes,"seconds"=>$seconds);
}

/**
 * Get name from email
 * This function will return name if avaialble else will return empty string
 * @param string $email
 */
function get_name_from_email($email){
  try {
    global $session;

    if($email == '') { return ""; exit(); }

    $result= $session->execute($session->prepare("SELECT custfname,custlname FROM customer WHERE custemailaddress=?"),array('arguments'=>array(
      $email
    )));

    //get name
    $name = "";
    if($result->count()>0){
      $name = $result[0]['custfname'] ." ". $result[0]['custlname'];
    }
    return $name;
  } catch (\Exception $e) {
    //In case of error.. return ""
    return "";
  }
}

function get_name_and_email_from_custcode($custcode){
  try {
    global $session;

    if($custcode == '') { return [ 'name' => '', 'email' => '' ]; exit(); }

    $result= $session->execute($session->prepare("SELECT custfname,custlname,custemailaddress FROM customer WHERE custcode=? ALLOW FILTERING"),array('arguments'=>array(
      $custcode
    )));

    //get name
    $name = ""; $email = "";
    if($result->count()>0){
      $name = $result[0]['custfname'] ." ". $result[0]['custlname'];
      $email = $result[0]['custemailaddress'];
    }
    return [ 'name' => $name, 'email' => $email ];
  } catch (\Exception $e) {
    //In case of error.. return ""
    return [ 'name' => '', 'email' => '' ];
  }
}

/**
 * Get custcode from email
 * This function will return custcode if avaialble else will return empty string
 * @param string $email
 */
function get_custcode_from_email($email){
  try {
    global $session;

    if($email == '') { return ""; exit(); }

    $result= $session->execute($session->prepare("SELECT custcode FROM customer WHERE custemailaddress=?"),array('arguments'=>array(
      $email
    )));

    //get custcode
    $custcode = "";
    if($result->count()>0){
      $custcode = $result[0]['custcode'];
    }
    return $custcode;
  } catch (\Exception $e) {
    //In case of error.. return ""
    return "";
  }
}

//get config active transaction id
function get_active_config_txn_id($companycode, $module){
  try {
    global $session;

    if($module == '' || $companycode == "") { return ""; exit(); }
    $tid = "";

    $res_txn=$session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? AND status=? AND active_status=? ALLOW FILTERING"),array('arguments'=>array($companycode,$module,"1","active")));
    if ($res_txn->count() > 0) {
      $tid = (string)$res_txn[0]['wcvid'];
    }

    return $tid;
  } catch (\Exception $e) {
    //In case of error.. return ""
    return "";
  }
}

//notice_write
function notice_write($notice_module_id,$companycode,$notice_from,$notice_from_role,$notice_link,$notice_to,$notice_to_role,$txn_name,$transactionid,$txn_name2="")
{
    global $session;
    if($notice_module_id==''){ return "Error Occured."; exit(); }
    $eventid="NA"; $notice_expiry="NA"; $notice_logid="NA"; $notice_law="NA";
    //From DB
    $result_nm=$session->execute($session->prepare('SELECT * FROM notice_master WHERE notice_module_id=? ALLOW FILTERING'),array('arguments'=>array($notice_module_id)));
    if($result_nm->count()==0){
      $result_nm=$session->execute($session->prepare('SELECT * FROM notice_master WHERE notice_module_alt=? ALLOW FILTERING'),array('arguments'=>array($notice_module_id)));
      if($result_nm->count()==0){ return "Error Occured."; exit(); }
    }

    $notice_status=$result_nm[0]['notice_status'];
    $notice_type=$result_nm[0]['notice_type'];
    $notice_alert_status=$result_nm[0]['notice_alert_status'];
    $notice_module=$result_nm[0]['notice_module'];
    $notice_module_alt=$result_nm[0]['notice_module_alt'];
    $notice_module_desc=$result_nm[0]['notice_module_desc'];
    //Details feeding
    $notice_details=str_replace("<tname>","*|*".$txn_name."*|*",$result_nm[0]['notice_details']);
    if ($txn_name2!="") {
      $notice_details=str_replace("<tname2>","*|*".$txn_name2."*|*",$notice_details);
    }
    $notice_timestamp =date("d-m-y H:i:s");
    try {

      $result_action= $session->execute($session->prepare("SELECT reassigntype FROM notice_master WHERE notice_module_id=? ALLOW FILTERING"),array('arguments'=>array($notice_module_id)));
      if ($result_action->count()==0) {
        $result_action= $session->execute($session->prepare("SELECT reassigntype FROM notice_master WHERE notice_module_alt=? ALLOW FILTERING"),array('arguments'=>array($notice_module_alt)));
      }
      $reassigntype="individual";
      if ($result_action->count()>0) { $reassigntype=$result_action[0]['reassigntype']; }

      $query_insert_in_company =$session->prepare('INSERT INTO notice(
         notice_no,
         companycode,
         createdate,
         effectivedate,
         eventid,
         notice_alert_status,
         notice_details,
         notice_expiry,
         notice_from,
         notice_from_role,
         notice_link,
         notice_logid,
         notice_status,
         notice_module,
         notice_module_alt,
         notice_timestamp,
         notice_to,
         notice_to_role,
         notice_type,
         status,
         transactionid,
         notice_law,
         notice_module_id,
         notice_module_desc,
         mail_status,
         reassigntype
      )
      VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
      $session->execute($query_insert_in_company,array('arguments'=>array(
        new \Cassandra\Uuid(),
        $companycode,
        new \Cassandra\Timestamp(),
        new \Cassandra\Timestamp(),
        $eventid,
        $notice_alert_status,$notice_details,$notice_expiry,$notice_from,$notice_from_role,$notice_link,$notice_logid,$notice_status,$notice_module,$notice_module_alt,
        $notice_timestamp,
        $notice_to,$notice_to_role,$notice_type,
        "1",
        $transactionid,
        $notice_law,$notice_module_id,$notice_module_desc,"0",$reassigntype
      )));

      //update notice count
      $notice_count=1;
      $result_nc=$session->execute($session->prepare('SELECT notice_count FROM notice_count WHERE email=? AND role=? AND companycode=?'),array('arguments'=>array($notice_to,$notice_to_role,$companycode)));
      if($result_nc->count()>0){ $notice_count=$result_nc[0]['notice_count']+1; }
      $session->execute($session->prepare('UPDATE notice_count SET notice_count=?,modifydate=?,seen_status=? WHERE email=? AND role=? AND companycode=?'),array('arguments'=>array($notice_count,new \Cassandra\Timestamp(),"0",$notice_to,$notice_to_role,$companycode)));

      $arr_return=["code"=>200, "success"=>true, "data"=>[]];
      return $arr_return;
    } catch (\Exception $e) {
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
}


function notice_update($transactionid,$companycode,$notice_to,$notice_to_role,$notice_module_id)
{
  try {
    global $session;
    $result_notice=$session->execute($session->prepare('SELECT notice_no,notice_to,notice_to_role FROM notice WHERE companycode=? AND transactionid=? AND notice_to=? AND notice_to_role=? AND notice_module_id=? ALLOW FILTERING'),array('arguments'=>array(
      $companycode,$transactionid,$notice_to,$notice_to_role,$notice_module_id)));
    if ($result_notice->count()==0) {
      $result_notice=$session->execute($session->prepare('SELECT notice_no,notice_to,notice_to_role FROM notice WHERE companycode=? AND transactionid=? AND notice_to=? AND notice_to_role=? AND notice_module_alt=? ALLOW FILTERING'),array('arguments'=>array(
        $companycode,$transactionid,$notice_to,$notice_to_role,$notice_module_id)));
    }

    foreach ($result_notice as $row_notice) {
      $session->execute($session->prepare('UPDATE notice SET notice_alert_status=?,notice_status=?,mail_status=? WHERE notice_no=?'),array('arguments'=>array("settled","seen","1",$row_notice['notice_no'])));
      if ($row_notice['notice_to']=="" || $row_notice['notice_to_role']=="") {}else {
        $result_nc=$session->execute($session->prepare('SELECT notice_count FROM notice_count WHERE email=? AND role=? AND companycode=?'),array('arguments'=>array($row_notice['notice_to'],$row_notice['notice_to_role'],$companycode)));
        if($result_nc->count()>0){
          $notice_count=$result_nc[0]['notice_count']-1;
          $session->execute($session->prepare('UPDATE notice_count SET notice_count=?,modifydate=? WHERE email=? AND role=? AND companycode=?'),array('arguments'=>array($notice_count,new \Cassandra\Timestamp(),$row_notice['notice_to'],$row_notice['notice_to_role'],$companycode)));
        }
      }
    }
    $arr_return=["code"=>200, "success"=>true, "data"=>[]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

// In case like change CAB
function notice_update_all($transactionid,$companycode,$notice_to,$notice_to_role,$notice_module_id)
{
  try {
    global $session;
    $result_notice=$session->execute($session->prepare('SELECT notice_no,notice_to,notice_to_role FROM notice WHERE companycode=? AND transactionid=? AND notice_module_id=? ALLOW FILTERING'),array('arguments'=>array($companycode,$transactionid,$notice_module_id)));

    if ($result_notice->count()==0) {
      $result_notice=$session->execute($session->prepare('SELECT notice_no,notice_to,notice_to_role FROM notice WHERE companycode=? AND transactionid=? AND notice_module_alt=? ALLOW FILTERING'),array('arguments'=>array($companycode,$transactionid,$notice_module_id)));
    }

    foreach ($result_notice as $row_notice) {
      $session->execute($session->prepare('UPDATE notice SET notice_alert_status=?,notice_status=?,mail_status=? WHERE notice_no=?'),array('arguments'=>array("settled","seen","1",$row_notice['notice_no'])));
      if ($row_notice['notice_to']=="" || $row_notice['notice_to_role']=="") {}else {
      $result_nc=$session->execute($session->prepare('SELECT notice_count FROM notice_count WHERE email=? AND role=? AND companycode=?'),array('arguments'=>array($row_notice['notice_to'],$row_notice['notice_to_role'],$companycode)));
      if($result_nc->count()>0){
        $notice_count=$result_nc[0]['notice_count']-1;
        $session->execute($session->prepare('UPDATE notice_count SET notice_count=?,modifydate=? WHERE email=? AND role=? AND companycode=?'),array('arguments'=>array($notice_count,new \Cassandra\Timestamp(),$row_notice['notice_to'],$row_notice['notice_to_role'],$companycode)));
      }
     }
    }
    $arr_return=["code"=>200, "success"=>true, "data"=>[]];
    return $arr_return;
  } catch (\Exception $e) {
    return "Error Occured";
  }
}

//to get email & roles for module assign of a module
function module_assign_email_role_list($page_module,$module_type,$companycode)
{
  try {
    global $session; $arr=array();
    if ($page_module=='' || $module_type=='' || $companycode=='') { return $arr; exit(); }
    $page_arr=explode("|",$page_module);
    //Find all roles with modify access
    $result=$session->execute($session->prepare('SELECT * FROM roletocustomer WHERE companycode=? AND rolestatus=? ALLOW FILTERING'),array('arguments'=>array($companycode,"1")));
    foreach ($result as $row) {
      $page_access=array();
      switch ($module_type) {
        case 'create':
          $page_access=explode("|",$row['createaccess']);
          break;
        case 'modify':
          $page_access=explode("|",$row['modifyaccess']);
          break;
        case 'view':
          $page_access=explode("|",$row['viewaccess']);
          break;
      }

      //Final page access array
      array_shift($page_access);

      // //Intersect both array
      $page_intersect=array_intersect($page_arr,$page_access);

      //check if page_arr count remains the same
      if (count($page_arr)==count($page_intersect)) {
        $arr[]=array("email"=>$row['rtccustemail'],"role"=>$row['rtcrole']);
      }
    }


    $arr_final=array_unique($arr,SORT_REGULAR);
    if(count($arr_final)==0) { $arr_final[0]=['email'=>"",'role'=>'']; }
    $arr_return=["code"=>200, "success"=>true, "data"=>$arr_final];
    return $arr_return;

  } catch (\Exception $e) {
    $arr_final[0]=['email'=>"",'role'=>''];
    $arr_return=["code"=>200, "success"=>true, "data"=>$arr_final];
    return $arr_return;
  }
}


function pending_email_roles_for_notice($tid,$companycode)
{
  try {
    global $session;
    $arr_pending=[];
    $result_pending=$session->execute($session->prepare("SELECT notice_to,notice_to_role FROM notice WHERE companycode=? AND transactionid=? AND status=? AND notice_alert_status=? ALLOW FILTERING"),array('arguments'=>array($companycode,$tid,"1","urgent")));
    foreach ($result_pending as $row_pending) {
     $custname=get_name_from_email($row_pending['notice_to']);
      $arr_pending[]=array(
        "email"=>$row_pending['notice_to'],
        "role"=>$row_pending['notice_to_role'],
        "name"=>$custname
      );
    }
    $arr_return=["code"=>200, "success"=>true, "data"=>$arr_pending];
    return $arr_return;
  } catch (\Exception $e) {
    $arr_return=["code"=>200, "success"=>true, "data"=>[]];
    return $arr_return;
  }
}

function update_landing_module($companycode, $email, $role, $custcode, $law, $seq)
{
  try {
    global $session;

    if ($companycode=='' || $law=='' || $seq=='') {
      return false; exit();
    }

    $result_f= $session->execute($session->prepare("SELECT id FROM companydiytracker WHERE companycode=? AND law=? AND sequence=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,$law,$seq,"1")));
    if ($result_f->count()==0) {
   $result_diy= $session->execute($session->prepare("SELECT * FROM diytrackermaster WHERE sequence=? ALLOW FILTERING"),array('arguments'=>array($seq)));
   foreach ($result_diy as $row) {
     $session->execute($session->prepare(
       "INSERT INTO companydiytracker(id,companycode,createdate,domain,effectivedate,fillercustcode,law,module,status,transactionid,version,sequence,domain_type,filleremail,fillerrole)
       VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
       "),array('arguments'=>array(
       new \Cassandra\Uuid(),
       $companycode,
       new \Cassandra\Timestamp(),
       $row['domain'],
       new \Cassandra\Timestamp(),
       $custcode,
       $law,
       $row['module'],
       "1",
       "",
       "1",
       $row['sequence'],
       $row['domain_type'],
       $email,
       $role
     )));
       }
     }
     return true;
  } catch (\Exception $e) {
    return false;
  }
}

//check if email exist in this company
function check_if_email_exist_in_company($companycode, $email)
{
  try {
    global $session;
    $result_email=$session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rtccustemail=? AND rolestatus=? ALLOW FILTERING"),array('arguments'=>array($companycode,$email,"1")));
    if($result_email -> count() > 0){
      return true;
    }else{
      return false;
    }
  } catch (\Exception $e) {
    return false;
  }
}

//validate_law_for_signup
function validate_law_for_signup($law){
  try{
    global $session;
    if($law==""){ return false; }else{

      $res_pages=$session->execute($session->prepare('SELECT law FROM compliance_framework_txn WHERE law=? AND status=? AND platform_show_status=? ALLOW FILTERING'),array('arguments'=>array($law,"1","1")));
      if($res_pages->count()==0){ return false; }else{
        $result_product=$session->execute($session->prepare('SELECT productid FROM product_master WHERE product=? AND status=? ALLOW FILTERING'),array('arguments'=>array($law,"1")));
        if($result_product->count()==0){
          $result_product=$session->execute($session->prepare('SELECT productid FROM product_addon_master WHERE product=? AND status=? ALLOW FILTERING'),array('arguments'=>array($law,"1")));
        }
        if($result_product->count()==0){
          return false;
        }else {
          return true;
        }
      }
    }
  }catch (\Exception $e) {
    return false;
  }
}


//validateEmail
function validateEmail($email)
{
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
      return true;
  } else {
    return false;
  }
}

function check_if_role_exist_in_company($companycode, $role)
{
  try {
    global $session;
    $result_email=$session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rtcrole=? AND rolestatus=? ALLOW FILTERING"),array('arguments'=>array($companycode,$role,"1")));
    if($result_email -> count() > 0){
      return true;
    }else{
      return false;
    }
  } catch (\Exception $e) {
    return false;
  }
}

function check_if_email_role_exist_in_company($companycode, $email, $role)
{
  try {
    global $session;
    $result_email=$session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rtccustemail=? AND rtcrole=? AND rolestatus=? ALLOW FILTERING"),array('arguments'=>array($companycode,$email,$role,"1")));
    if($result_email -> count() > 0){
      return true;
    }else{
      return false;
    }
  } catch (\Exception $e) {
    return false;
  }
}

//get_department_list
function get_department_list($companycode)
{
  try {
    global $session;
    $arr_d=[];
    $result_d= $session->execute($session->prepare("SELECT locationdepartment FROM locationinscope WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
    foreach ($result_d as $row_d) {
      $dept = explode("|",$row_d['locationdepartment']);
      foreach ($dept as $det) { $dep_t =explode(",",$det);
      if ($dep_t[0]!=="") { array_push($arr_d,$dep_t[0]); }
      }
    }
    $arr_d_unique=array_unique($arr_d);
    sort($arr_d_unique);
    $arr_return=["code"=>200, "success"=>true, "data"=>$arr_d_unique];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function module_assign_pages($type)
{
  try {
    $pages="";
    switch ($type) {
      case 'create':
        $pages="create|PG068|PG009|PG023|PG073|PG007|PG006|PG069|PG012|PG070|PG013|PG018|PG005|PG032a|PG032|PG033|PG008|PG035|PG038|PG041|PG042|PG046|PG047|PG054|PG055|PG058|PG057|PG060|PG061|PG062|PG064|PG074|PG075|PG076|PG077|PG078";
        break;
      case 'modify':
        $pages='modify|PG0026|PG019|PG048|PG049|PG009|PG022|PG024|PG025|PG007|PG052|PG006|PG053|PG003|PG012|PG012a|PG013|PG013a|PG018|PG005|PG051|PG032|PG008|PG050|PG035|PG044|PG036|PG037|PG038|PG039|PG040|PG054|PG055|PG066|PG067|PG056|PG057|PG062|PG063|PG064|PG065|PG075|PG077';;
        break;
      case 'view':
        $pages='view|PG068|PG0026|PG030|PG010|PG028|PG029|PG048|PG049|PG009|PG031|PG007|PG006|PG003|PG012|PG013|PG018|PG001|PG005|PG032|PG033|PG008|PG035|PG038|PG041|PG042|PG043|PG047|PG054|PG055|PG059|PG056|PG060|PG061|PG062|PG064|PG075|PG077';
        break;
    }
    return $pages;
  } catch (\Exception $e) {
    return "";
  }
}

function insert_data_into_client_product_subscription($product,$companycode,$expirydate,$unit,$balance,$currency,$email,$role,$custcode)
 {
   try {
     global $session;
       if ($product=="" || $companycode=="" || $expirydate=="") {
         return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
       }

       $result_comp=$session->execute($session->prepare('SELECT companyname FROM company WHERE companycode=?'),array('arguments'=>array($companycode)));
       if ($result_comp->count()==0) {
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid company" ]; exit();
       }

       $result_pr=$session->execute($session->prepare('SELECT * FROM product_master WHERE product=? AND status=? ALLOW FILTERING'),array('arguments'=>array($product,"1")));
       if ($result_pr->count()==0) {
         $result_pr=$session->execute($session->prepare('SELECT * FROM product_addon_master WHERE product=? AND status=? ALLOW FILTERING'),array('arguments'=>array($product,"1")));
       }
       if ($result_pr->count()==0) {
         return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid Product for Subscription" ]; exit();
       }

       $get_company_tier_details=get_company_tier_details($companycode);
       if(!$get_company_tier_details['success']){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Invalid Company Tier" ]; exit();
       }
       $tier=$get_company_tier_details['data']['tier'];

       $expirydate=date("d-m-Y",strtotime($expirydate));
       $start_date = date("d-m-Y");


       $id_cps=(string)new \Cassandra\Uuid();
       $createdate_cps=new \Cassandra\Timestamp();
       foreach ($result_pr as $row_pr) {

          //update old entry to status 0
          $result_cps_up=$session->execute($session->prepare('SELECT id,productid,createdate FROM client_product_subscription WHERE companycode=? AND productid=? ALLOW FILTERING'),array('arguments'=>array($companycode,$row_pr['productid'])));
          foreach ($result_cps_up as $row) {
            $session->execute($session->prepare('UPDATE client_product_subscription SET status=?,modifydate=? WHERE id=? AND productid=? AND createdate=?'),array('arguments'=>array("0",new \Cassandra\Timestamp(),$row['id'],$row['productid'],$row['createdate'])));
          }

         $columns=[
           "id","productid","packageid","moduleid","productversion","packageversion","moduleversion",
           "product_key","product","companycode","email","role","custcode","criteria","description",
           "licencekey","startdate","expirydate","createdate","effectivedate","status","version","unit","balance","restricted","subscription_type","paymentid","payment_mode",
           "currency","add_type","tier","addon_flag"
         ];
         $columns_data=[
          $id_cps,$row_pr['productid'],$row_pr['packageid'],$row_pr['moduleid'],$row_pr['version'],$row_pr['packageversion'],$row_pr['moduleversion'],
          $row_pr['product_key'],$row_pr['product'],$companycode,$email,$role,$custcode,$row_pr['product_key'],$row_pr['product_dispname'],
          (string)new \Cassandra\Uuid(),$start_date,$expirydate,$createdate_cps,$createdate_cps,"1",$row_pr['version'],$unit,(int)$balance,$row_pr['restricted'],"Active","","offline",
          $currency,"by_payment","Tier-1",0
         ];
         $data_for_insert=[
           "action"=>"insert", //read/insert/update/delete`
           "table_name"=>"client_product_subscription", //provide actual table name or dummy table name thats been in JSON/arr file
           "columns"=>$columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
           "isCondition"=>false,
           "condition_columns"=>"",
           "columns_data"=>$columns_data,
           "isAllowFiltering"=>false
         ];
         $table_insert=table_crud_actions($data_for_insert);
        }

        return ["code"=>200, "success"=>true, "data"=>['message' => 'success'] ];

   } catch (\Exception $e) {
     return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
   }
 }

 function check_if_product_is_valid($product)
 {
   try {
     global $session;
     if ($product=="") {
       $arr_return=["success"=>false, "msg"=>"Invalid Product"]; return $arr_return; exit();
     }
     $result_pr=$session->execute($session->prepare('SELECT productid FROM product_master WHERE product=? AND status=? ALLOW FILTERING'),array('arguments'=>array($product,"1")));
     if ($result_pr->count()==0) {
       $result_pr=$session->execute($session->prepare('SELECT * FROM product_addon_master WHERE product=? AND status=? ALLOW FILTERING'),array('arguments'=>array($product,"1")));
     }
     if ($result_pr->count()==0) {
       $arr_return=["success"=>false, "msg"=>"Invalid Product"]; return $arr_return; exit();
     }
   } catch (\Exception $e) {
     $arr_return=["success"=>false, "msg"=>"Error Occured"]; return $arr_return;
   }
 }

 function update_company_expiry_by_laws_expiry_date($companycode)
 {
   try {
     global $session;
     if ($companycode=="") {
       $arr_return=["success"=>false, "msg"=>"Invalid Company"]; return $arr_return; exit();
     }
     $result_pr=$session->execute($session->prepare('SELECT expirydate,subscription_type FROM applicablelaw WHERE companycode=? AND status=? ALLOW FILTERING'),array('arguments'=>array($companycode,"1")));
     if ($result_pr->count()==0) {
       $arr_return=["success"=>false, "msg"=>"No Law Found"]; return $arr_return; exit();
     }
     $arr_dates=[];
     $subscription_type='Trial';
     foreach ($result_pr as $row) {
       $expirydate_int=strtotime($row['expirydate']);
       array_push($arr_dates,$expirydate_int);
       if($row['subscription_type']=='Paid'){ $subscription_type='Paid'; }
     }

     if (count($arr_dates)>0) {
       $max_date_int=max($arr_dates);
       $date_to_update=date("Y-m-d",$max_date_int);
     }
     $session->execute($session->prepare('UPDATE company SET cexpirydate=?,csubscriptiontype=?,modifydate=? WHERE companycode=?'),array('arguments'=>array($date_to_update,$subscription_type,new \Cassandra\Timestamp(),$companycode)));
     $arr_return=["success"=>true, "msg"=>"Updated"]; return $arr_return;
   } catch (\Exception $e) {
     $arr_return=["success"=>false, "msg"=>"Error Occured"]; return $arr_return;
   }
 }

 function get_company_tier_details($companycode)
 {
   try {
     global $session;
     if ($companycode=="") {
       $arr_return=["success"=>false, "msg"=>"Invalid Company"]; return $arr_return; exit();
     }
     $result_pr=$session->execute($session->prepare('SELECT corganizationsize FROM company WHERE companycode=?'),array('arguments'=>array($companycode)));
     if ($result_pr->count()==0) {
      $arr_return=["success"=>false, "msg"=>"Invalid Company"]; return $arr_return; exit();
     }

     $corganizationsize=$result_pr[0]['corganizationsize'];

     switch($corganizationsize){
      case '0-50':
        $tier="Tier-1";
        break;
      case '1-51':
        $tier="Tier-1";
        break;
      case '51-100':
        $tier="Tier-1";
        break;
      case '101-300':
        $tier="Tier-1";
        break;
      case '301-500':
        $tier="Tier-1";
        break;
      case '500+':
        $tier="Tier-2";
        break;
      case '51-500':
        $tier="Tier-2";
        break;
      case '501-2000':
        $tier="Tier-3";
        break;
      default:
        $corganizationsize='0-50';
        $tier="Tier-1";
        break;
     }
     $data=['tier'=>$tier, 'size'=>$corganizationsize];
     $arr_return=["success"=>true, "msg"=>"success", "data"=>$data]; return $arr_return;

   } catch (\Exception $e) {
     $arr_return=["success"=>false, "msg"=>"Error Occured"]; return $arr_return;
   }
 }

 /**
  * @param base64 string $str
  * Return true if valid base64 else false
  */
 function isValidBase64($str) {
  $decoded = base64_decode($str, true);

  // Check if base64_decode returns false or if the decoded length doesn't match the input length
  return $decoded;
}

function isPasswordValid($password) {
  // Check for at least 8 characters
  if (strlen($password) < 8) {
      return false;
  }

  // Check for at least one uppercase letter
  if (!preg_match('/[A-Z]/', $password)) {
      return false;
  }

  // Check for at least one lowercase letter
  if (!preg_match('/[a-z]/', $password)) {
      return false;
  }

  // Check for at least one digit
  if (!preg_match('/\d/', $password)) {
      return false;
  }

  // Check for at least one special character
  if (!preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/', $password)) {
      return false;
  }

  // If all criteria are met, the password is valid
  return true;
}

function isDateInRange($dateString, $minDate = null, $maxDate = null) {
  // Check if the date string is valid
  $inputDate = DateTime::createFromFormat('Y-m-d', $dateString);
  if ($inputDate === false) {
      return false; // Invalid date string
  }

  // Check if the date falls within the specified range
  if (($minDate === null || $inputDate >= new DateTime($minDate)) &&
      ($maxDate === null || $inputDate <= new DateTime($maxDate))) {
      return true;
  }

  return false;
}


function get_other_status_for_gap_entries($gscore)
 {
   $maturity_level="Not Applicable"; $score_status="Not Applicable";
   switch ($gscore) {
     case '0':
       $maturity_level= "Non Compliant";
       $score_status="Non Compliant";
       break;
     case '1':
       $maturity_level= "Defined";
       $score_status="Compliant";
       break;
     case '2':
       $maturity_level= "Implemented";
       $score_status="Compliant";
       break;
     case '3':
       $maturity_level= "Managed";
       $score_status="Compliant";
       break;
     default:
       $maturity_level= "Not Applicable";
       $score_status="Not Applicable";
       break;
   }
   return ['maturity_level'=>$maturity_level,'score_status'=>$score_status];
 }

function rolematrix_for_assessment($type)
   {
     global $session;
     $arr =array();
       try {
         switch ($type) {
           case 'security':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE security=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'privacy':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE privacy=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'SEBI':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE sebi=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'RBIUCB':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE rbi=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'gdpr':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE gdpr=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'bahrain':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE bahrain=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'india':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE india=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'vendor':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE vendor=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'technical risk':
             $result= $session->execute($session->prepare("SELECT rolename FROM rolematrix WHERE techriskstatus=? ALLOW FILTERING"),array('arguments'=>array("1"))); foreach ($result as $row) { array_push($arr,$row['rolename']); }
             break;

           default:
             $col_name=strtolower($type);
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status WHERE $col_name=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
         }
         return array_unique($arr);

       } catch (\Exception $e) {
         return array();
       }
   }

   function rolematrix_for_assessment_ia($type)
   {
     global $session;
     $arr =array();
       try {
         switch ($type) {
           case 'security':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE security=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'privacy':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE privacy=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'SEBI':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE sebi=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'RBIUCB':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE rbi=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'gdpr':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE gdpr=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'bahrain':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE bahrain=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           case 'india':
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE india=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
           default:
             $col_name=strtolower($type);
             $result= $session->execute($session->prepare("SELECT role FROM assessment_role_status_ia WHERE $col_name=? ALLOW FILTERING"),array('arguments'=>array("1")));
             foreach ($result as $row) { array_push($arr,$row['role']); }
             break;
         }
         return array_unique($arr);

       } catch (\Exception $e) {
          return array();
       }
   }

   function get_law_tid_by_ldispname($ldispname)
   {
     try {
       global $session;
       $law_tid="";
       $result_lawtid= $session->execute($session->prepare('SELECT id FROM lawmap_content_txn WHERE ldispname=? ALLOW FILTERING'),array('arguments'=>array($ldispname)));
       if ($result_lawtid->count()>0) {
         $law_tid=(string)$result_lawtid[0]['id'];
       }
       return $law_tid;
     } catch (\Exception $e) {
       return "";
     }
   }

   //get_date_by_timestamp
   function get_date_by_timestamp($timestamp, $format="dd-mm-YYYY")
   {
     try {
       global $session;
       $timestamp_string = (string)$timestamp;
       if($timestamp_string == "") { return ""; }

       $timestamp_int = (int)$timestamp_string/1000;

       $date = date($format, $timestamp_int);
       return $date;
     } catch (\Exception $e) {
       return "";
     }
   }

   function get_vendor_name_by_vendor_id($vendorid)
   {
     try {
       global $session;
       if($vendorid == "") { return ""; }
       $result_vendor= $session->execute($session->prepare('SELECT vccustname FROM vendorcontract WHERE vcid=?'),array('arguments'=>array(new \Cassandra\Uuid($vendorid))));
       $vendor = "";
       if($result_vendor->count() >0){ $vendor = $result_vendor[0]['vccustname']; }
       return $vendor;
     } catch (\Exception $e) {
       return "";
     }
   }

?>