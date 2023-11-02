<?php
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
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
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

?>