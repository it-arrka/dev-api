<?php 

function GetRopaHandler($funcCallType){
    try{
      switch($funcCallType){
        case "save-controller-contact":
          $jsonString = file_get_contents('php://input');
          if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
          $json = json_decode($jsonString,true);
          if(!is_array($json)){
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
          }
          
          if(isset($GLOBALS['companycode']) && isset($GLOBALS['email'])){
            $output = controller_contact_data_save($GLOBALS['companycode'], $GLOBALS['email'], $json);
            if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
            }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
            }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "save-controller-data":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }
            
            if(isset($GLOBALS['companycode']) && isset($GLOBALS['email'])){
                $output = controller_data_save($GLOBALS['companycode'], $GLOBALS['email'], $json);
                if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
                }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;

          case "save-processor-contact":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
              catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }
            
            if(isset($GLOBALS['companycode']) && isset($GLOBALS['email'])){
              $output = processor_contact_data_save($GLOBALS['companycode'], $GLOBALS['email'], $json);
              if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
              }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
              }
            }else{
              catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;

        case "save-processor-data":
            $jsonString = file_get_contents('php://input');
            if($jsonString == ""){ catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit(); }
            $json = json_decode($jsonString,true);
            if(!is_array($json)){
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]); exit();
            }
            
            if(isset($GLOBALS['companycode']) && isset($GLOBALS['email'])){
                $output = processor_data_save($GLOBALS['companycode'], $GLOBALS['email'], $json);
                if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
                }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;

        case "get-controller-contact":
            if(isset($GLOBALS['companycode'])){
                $output = get_controller_contact($GLOBALS['companycode']);
                if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
                }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;

        case "get-controller-data":
            if(isset($GLOBALS['companycode'])){
                $output = get_controller_data($GLOBALS['companycode']);
                if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
                }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;

        case "get-controller-specific-data":
          if(isset($_GET['id'])){
            if(isset($GLOBALS['companycode'])){
              $output = get_controller_data($GLOBALS['companycode'], $_GET['id']);
              if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
              }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
              }
          }else{
              catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;

        case "get-processor-contact":
            if(isset($GLOBALS['companycode'])){
                $output = get_processor_contact($GLOBALS['companycode']);
                if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
                }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;

        case "get-processor-data":
            if(isset($GLOBALS['companycode'])){
                $output = get_processor_data($GLOBALS['companycode']);
                if($output['success']){
                commonSuccessResponse($output['code'],$output['data']);
                }else{
                catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
                }
            }else{
                catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
            }
            break;


        case "get-ropa-init-data":
          if(isset($GLOBALS['companycode'])){
              $output = get_ropa_init_data($GLOBALS['companycode']);
              if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
              }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
              }
          }else{
              catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          break;


        case "get-processor-specific-data":
          if(isset($_GET['id'])){
            if(isset($GLOBALS['companycode'])){
              $output = get_processor_data($GLOBALS['companycode'], $_GET['id']);
              if($output['success']){
              commonSuccessResponse($output['code'],$output['data']);
              }else{
              catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
              }
          }else{
              catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
          }
          }else{
            catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
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

function get_ropa_init_data($companycode){
  try {
    global $session; 

    //get business data
    $business=array();
    $result= $session->execute($session->prepare("SELECT deptname FROM ropadepartments"));
    foreach ($result as $row) { array_push($business,$row['deptname']); }
    // Extra data from db
    $result_n= $session->execute($session->prepare("SELECT bussinessfunction FROM ropacontroller WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
    foreach ($result_n as $row_n) {
      $bussinessfunction=explode("|",$row_n['bussinessfunction']);
      $business=array_merge($business,$bussinessfunction);
    }

    //get category data
    $category=array("Employees","Successful candidates","Unsuccessful candidates","Existing customers","Potential customers");
    $result_n= $session->execute($session->prepare("SELECT individualcat FROM ropacontroller WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
    foreach ($result_n as $row_n) {
      if (!$row_n['individualcat']=='') {
        $individualcat=explode("|",$row_n['individualcat']);
        $category=array_merge($category,$individualcat);
      }
    }

    //get category for personal data
    $category_pd = [];
    $result= $session->execute("SELECT pdcategory FROM pditem");
    foreach ($result as $row) { array_push($category_pd,$row['pdcategory']); }

    $source_pd=array("Data subject","Controller");
    $result_n= $session->execute($session->prepare("SELECT sourcepd FROM ropacontroller WHERE companycode=? ALLOW FILTERING"),array('arguments'=>array($companycode)));
    foreach ($result_n as $row_n) {
      if (!$row_n['sourcepd']=='') {
        $sourcepd=explode("|",$row_n['sourcepd']);
        $source_pd=array_merge($source_pd,$sourcepd);
      }
    }

    //impact assessment progress
    $impact_assessment = ['Yet to Start', 'In Progress', 'Completed'];


    $final_arr = [
      "business" => array_unique($business),
      "category" => array_unique($category),
      "category_pd" => array_unique($category_pd),
      "source_pd" => array_unique($source_pd),
      "impact_assessment" => $impact_assessment
    ];


    $arr_return=["code"=>200, "success"=>true, "data"=>$final_arr ];
    return $arr_return;

} catch(Exception $e){
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
}
}

function get_controller_contact($companycode)
{
    try {
        global $session;
        $category_arr = ['general', 'representative', 'dpo'];
        $arr = [];
        foreach ($category_arr as $category) {
            $result= $session->execute($session->prepare("SELECT * FROM ropacontrollercontact WHERE companycode=? AND status=? AND category=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1",$category)));
            foreach ($result as $row) { 
                unset($row['createdate']);
                unset($row['effectivedate']);
                unset($row['modifydate']);
                $row['id'] = (string)$row['id'];
                $arr[$category]=$row; 
            }
        }

        $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
        return $arr_return;

    } catch(Exception $e){
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
}

function get_controller_data($companycode, $id="")
{
  try {
    global $session; $arr=[];
    if($id == ""){
      $result= $session->execute($session->prepare("SELECT * FROM ropacontroller WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
      foreach ($result as $value) {
        unset($value['createdate']);
        unset($value['effectivedate']);
        unset($value['modifydate']);
        $value['id'] = (string)$value['id'];
        $value['bussinessfunction']=explode("|",$value['bussinessfunction']);
        $value['pdcat']=explode("|",$value['pdcat']);
        $value['individualcat']=explode("|",$value['individualcat']);
        $value['rights']=explode("|",$value['rights']);
        $arr[]=$value; 
      }
    }else{
      $result= $session->execute($session->prepare("SELECT * FROM ropacontroller WHERE companycode=? AND status=? AND id=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1", new \Cassandra\Uuid($id))));
      foreach ($result as $value) {
        unset($value['createdate']);
        unset($value['effectivedate']);
        unset($value['modifydate']);
        $value['id'] = (string)$value['id'];
        $value['bussinessfunction']=explode("|",$value['bussinessfunction']);
        $value['pdcat']=explode("|",$value['pdcat']);
        $value['individualcat']=explode("|",$value['individualcat']);
        $value['rights']=explode("|",$value['rights']);
        $arr=$value; 
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

function get_processor_contact($companycode)
{
    try {
        global $session;
        $category_arr = ['general', 'representative', 'dpo'];
        $arr = [];
        foreach ($category_arr as $category) {
            $result= $session->execute($session->prepare("SELECT * FROM ropaprocessorcontact WHERE companycode=? AND status=? AND category=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1",$category)));
            foreach ($result as $row) { 
                unset($row['createdate']);
                unset($row['effectivedate']);
                unset($row['modifydate']);
                $row['id'] = (string)$row['id'];
                $arr[$category]=$row; 
            }
        }

        $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
        return $arr_return;

    } catch(Exception $e){
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
}

function get_processor_data($companycode, $id="")
{
  try {
    global $session; $arr=[];

    if($id == ""){
      $result= $session->execute($session->prepare("SELECT * FROM ropaprocessor WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
      foreach ($result as $row) { 
          unset($row['createdate']);
          unset($row['effectivedate']);
          unset($row['modifydate']);
          $row['id'] = (string)$row['id'];
          $arr[]=$row; 
      }
    }else{
      $result= $session->execute($session->prepare("SELECT * FROM ropaprocessor WHERE companycode=? AND status=? AND id=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1", new \Cassandra\Uuid($id))));
      foreach ($result as $row) { 
          unset($row['createdate']);
          unset($row['effectivedate']);
          unset($row['modifydate']);
          $row['id'] = (string)$row['id'];
          $arr=$row; 
      }
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr ];
    return $arr_return;

  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

  /**
   * @param string $companycode
   * @param string $email
   * @param array $data
   */
function controller_contact_data_save($companycode, $email, $data)
{
  try {
    global $session;
    //Validation
    $required_keys = ["dpo", "general", "representative"];
    $data_keys = array_keys($data);

    $commonValues = array_intersect($required_keys, $data_keys);

    if (empty($commonValues)) {
      return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

      foreach ($data as $key_st => $value_st) {
        foreach ($value_st as $key_stv => $value_stv) {
          $data[$key_st][$key_stv]=escape_input($value_stv);
        }
      }

      //validate email
      $custcode = get_custcode_from_email($email);


      foreach ($data as $key => $value) {
        if($key == 'general' || $key == 'dpo' || $key == 'representative'){}else{
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid data" ]; exit();
        }
        if ($key=='general') {
        foreach ($value as $key_1 => $value_1) {
          if ($value_1 =="") { 
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Value should not be null. ".$key_1 ]; exit();
          }
          if ($key_1 =="contactno") {
            if (!is_numeric($value_1)) { 
                return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be number" ]; exit();
            }
            if (strlen($value_1)== 10) { } else{ 
                return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be only 10 digit" ]; exit();
            }
          }
          if ($key_1 == "email") {
            if (!filter_var($value_1,FILTER_VALIDATE_EMAIL)) { 
                return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is not valid" ]; exit();
            }
          }
        }
       }

       if ($key=='representative') {
          foreach ($value as $key_1 => $value_1) {
            if ($value_1 =="") {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Value should not be null. ".$key_1 ]; exit();
            }
            if ($key_1 =="contactno") {
              if (!is_numeric($value_1)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be number" ]; exit();
              }
              if (strlen($value_1)== 10) { } else{
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be only 10 digit" ]; exit();
              }
            }
            if ($key_1 == "email") {
              if (!filter_var($value_1,FILTER_VALIDATE_EMAIL)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is not valid" ]; exit();
              }
            }
          }
       }

       if ($key=='dpo') {
          foreach ($value as $key_1 => $value_1) {
            if ($value_1 =="") {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Value should not be null. ".$key_1 ]; exit();
            }
            if ($key_1 =="contactno") {
              if (!is_numeric($value_1)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be number" ]; exit();
              }
              if (strlen($value_1)== 10) { } else{
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be only 10 digit" ]; exit();
              }
            }
            if ($key_1 == "email") {
              if (!filter_var($value_1,FILTER_VALIDATE_EMAIL)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is not valid" ]; exit();
              }
            }
          }
        }
      }


    foreach ($data as $key => $value) {
        $result_id=$session->execute($session->prepare("SELECT id FROM ropacontrollercontact WHERE companycode=? AND status=? AND version=? AND category=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1","1",$key)));
        if ($result_id->count()>0) {
            $uuid=$result_id[0]['id'];
        }else {
            $uuid=new \Cassandra\Uuid();
        }

        $session->execute($session->prepare('INSERT INTO ropacontrollercontact(
                id,
                companycode,
                fillercustcode,
                category,
                status,
                createdate,
                effectivedate,
                version,
                name,
                address,
                email,
                contactno
            )
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(

            $uuid,
            $companycode,
            $custcode,
            $key,
            "1",
            new \Cassandra\Timestamp(),
            new \Cassandra\Timestamp(),
            "1",
            $value['name'],
            $value['address'],
            $value['email'],
            $value['contactno'],
        )));
    }
    $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success"] ];
    return $arr_return;
    } catch (Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
    }
  }

  /**
   * @param string $companycode
   * @param string $email
   * @param array $data
   */
  function processor_contact_data_save($companycode, $email, $data)
  {
  try {
    global $session;

     //validate data
      foreach ($data as $key_st => $value_st) {
        foreach ($value_st as $key_stv => $value_stv) {
          $data[$key_st][$key_stv]=escape_input($value_stv);
        }
      }

      //validate email
      $custcode = get_custcode_from_email($email);


      foreach ($data as $key => $value) {
        if($key == 'general' || $key == 'dpo' || $key == 'representative'){}else{
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"invalid data" ]; exit();
        }
        if ($key=='general') {
        foreach ($value as $key_1 => $value_1) {
          if ($value_1 =="") { 
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Value should not be null. ".$key_1 ]; exit();
          }
          if ($key_1 =="contactno") {
            if (!is_numeric($value_1)) { 
                return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be number" ]; exit();
            }
            if (strlen($value_1)== 10) { } else{ 
                return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be only 10 digit" ]; exit();
            }
          }
          if ($key_1 == "email") {
            if (!filter_var($value_1,FILTER_VALIDATE_EMAIL)) { 
                return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is not valid" ]; exit();
            }
          }
        }
       }

       if ($key=='representative') {
          foreach ($value as $key_1 => $value_1) {
            if ($value_1 =="") {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Value should not be null. ".$key_1 ]; exit();
            }
            if ($key_1 =="contactno") {
              if (!is_numeric($value_1)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be number" ]; exit();
              }
              if (strlen($value_1)== 10) { } else{
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be only 10 digit" ]; exit();
              }
            }
            if ($key_1 == "email") {
              if (!filter_var($value_1,FILTER_VALIDATE_EMAIL)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is not valid" ]; exit();
              }
            }
          }
       }

       if ($key=='dpo') {
          foreach ($value as $key_1 => $value_1) {
            if ($value_1 =="") {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Value should not be null. ".$key_1 ]; exit();
            }
            if ($key_1 =="contactno") {
              if (!is_numeric($value_1)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be number" ]; exit();
              }
              if (strlen($value_1)== 10) { } else{
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be only 10 digit" ]; exit();
              }
            }
            if ($key_1 == "email") {
              if (!filter_var($value_1,FILTER_VALIDATE_EMAIL)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is not valid" ]; exit();
              }
            }
          }
        }
      }


    foreach ($data as $key => $value) {
        $result_id=$session->execute($session->prepare("SELECT id FROM ropacontrollercontact WHERE companycode=? AND status=? AND version=? AND category=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1","1",$key)));
        if ($result_id->count()>0) {
          $uuid=$result_id[0]['id'];
        }else {
          $uuid=new \Cassandra\Uuid();
        }

      $session->execute($session->prepare('INSERT INTO ropaprocessorcontact(
          id,
          companycode,
          fillercustcode,
          category,
          status,
          createdate,
          effectivedate,
          version,
          name,
          address,
          email,
          contactno
            )
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(

          $uuid,
          $companycode,
          $custcode,
          $key,
          "1",
          new \Cassandra\Timestamp(),
          new \Cassandra\Timestamp(),
          "1",
          $value['name'],
          $value['address'],
          $value['email'],
          $value['contactno'],
        )));
        }
        $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success"] ];
        return $arr_return;
      } catch (Exception $e) {
        return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
      }
  }

/**
   * @param string $companycode
   * @param string $email
   * @param array $data
*/
  function processor_data_save($companycode,$email,$data)
  {
  try {
    global $session;

    //Validation
    $required_keys = ["addresscontroller", "addressrepcontroller", "contactnocontroller", "contactrepcontroller", "controllername", "countrypdtransfer", "emailcontroller", "emailrepcontroller", "generaldesc", "linkcontactcontroller", "namecontroller", "namerepcontroller", "processingcat", "safeguard"];

    //check if array is valid
    if(!checkKeysExist($data, $required_keys)){
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    foreach ($data as $key_st => $value_st) {
        $data[$key_st]=escape_input($value_st);
    }

     $custcode = get_custcode_from_email($email);

      //Validate data
      //Check for null
      foreach ($data as $key => $value) {

        if(is_array($value)){ $value=implode("|",$value); $data[$key]=$value; }

        $value=strip_tags($value);
        $data[$key]=$value;

        if ($key=='contactnocontroller' || $key=='contactrepcontroller') {
          if (!is_numeric($value)) {
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be Number" ]; exit();
          }

          if (strlen($value)== 10) { } else{
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Telephone Number should be only 10 digit" ]; exit();
          }
        }
        elseif ($key=='emailcontroller' || $key=='emailrepcontroller') {
          if (!filter_var($value,FILTER_VALIDATE_EMAIL)) {
              return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Email is not valid" ]; exit();
          }
        }
        else {
          if ($value=='') {
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Values should not be empty" ]; exit();
          }
        }
      }

      $id = new \Cassandra\Uuid();
      $timestamp = new \Cassandra\Timestamp();
      $modifydate = new \Cassandra\Timestamp();
      $result_validate= $session->execute($session->prepare("SELECT id,createdate FROM ropaprocessor WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
      if($result_validate -> count() > 0)
      {
        $id = $result_validate[0]['id'];
        $timestamp = $result_validate[0]['createdate'];
      }

      $session->execute($session->prepare('INSERT INTO ropaprocessor(
            id,
            companycode,
            fillercustcode,
            status,
            createdate,
            effectivedate,
            modifydate,
            version,
            controllername,
            linkcontactcontroller,
            namecontroller,
            namerepcontroller,
            addresscontroller,
            addressrepcontroller,
            emailcontroller,
            emailrepcontroller,
            contactnocontroller,
            contactrepcontroller,
            processingcat,
            countrypdtransfer,
            safeguard,
            generaldesc
            )
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(
            $id,
            $companycode,
            $custcode,
            "1",
            $timestamp,
            $timestamp,
            $modifydate,
            "1",
            $data['controllername'],
            $data['linkcontactcontroller'],
            $data['namecontroller'],
            $data['namerepcontroller'],
            $data['addresscontroller'],
            $data['addressrepcontroller'],
            $data['emailcontroller'],
            $data['emailrepcontroller'],
            $data['contactnocontroller'],
            $data['contactrepcontroller'],
            $data['processingcat'],
            $data['countrypdtransfer'],
            $data['safeguard'],
            $data['generaldesc']
            )));
            $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success"] ];
            return $arr_return;
        } catch (Exception $e) {
            return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
        }
    }

    function controller_data_save($companycode, $email, $data)
    {
    try {
      global $session;
      $required_keys = [
        'bussinessfunction','purpose','jointcontroller','individualcat','pdcat','recipientcat','link','transfercountry',
        'safeguard','retentionschedule','securitydesc','applicablelaw','rights','artnopd','artnospecialdata',
        'legitinterestprocess','legitinterestapplicable','autodecision','sourcepd','consentlink','locationpd','dpiarequired',
        'dpiaprogress','linkdpia','pdbreach','linkpdbreach','dpbill',
        'lawbasisprocess','linkretention','pdretained','reason'
      ];

      //check if array is valid
      if(!checkKeysExist($data, $required_keys)){
          return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
      }
  
    //Validate data


      //Check for null
      foreach ($data as $key => $value) {
        if(is_array($value)){ $value=implode("|",$value); $data[$key]=$value; }
        $value=escape_input($value);
        $data[$key]=$value;
        if ($key=='dpiaprogress') {
          if ($data['dpiarequired']=='No') {}else {
            if ($value=='') {
                return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Values should not be empty" ]; exit();
            }
          }
        }else {
          if ($value=='') {
            return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Values should not be empty" ]; exit();
          }
        }
      }

      $id = new \Cassandra\Uuid();
      $timestamp = new \Cassandra\Timestamp;
      $modifydate = new \Cassandra\Timestamp;
      $result_validate= $session->execute($session->prepare("SELECT id,createdate FROM ropacontroller WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
      if($result_validate -> count() > 0)
      {
        $id = $result_validate[0]['id'];
        $timestamp = $result_validate[0]['createdate'];
      }

      $custcode = get_custcode_from_email($email);
  
        $session->execute($session->prepare('INSERT INTO ropacontroller(
            id,
            companycode,
            fillercustcode,
            status,
            createdate,
            effectivedate,
            modifydate,
            version,
            bussinessfunction,
            purpose,
            jointcontroller,
            individualcat,
            pdcat,
            recipientcat,
            link,
            transfercountry,
            safeguard,
            retentionschedule,
            securitydesc,
            applicablelaw,
            rights,
            artnopd,
            artnospecialdata,
            legitinterestprocess,
            legitinterestapplicable,
            autodecision,
            sourcepd,
            consentlink,
            locationpd,
            dpiarequired,
            dpiaprogress,
            linkdpia,
            pdbreach,
            linkpdbreach,
            dpbill,
            lawbasisprocess,
            linkretention,
            pdretained,
            reason
        )
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'),array('arguments'=>array(

            $id,
            $companycode,
            $custcode,
            "1",
            $timestamp,
            $timestamp,
            $modifydate,
            "1",
            $data['bussinessfunction'],
            $data['purpose'],
            $data['jointcontroller'],
            $data['individualcat'],
            $data['pdcat'],
            $data['recipientcat'],
            $data['link'],
            $data['transfercountry'],
            $data['safeguard'],
            $data['retentionschedule'],
            $data['securitydesc'],
            $data['applicablelaw'],
            $data['rights'],
            $data['artnopd'],
            $data['artnospecialdata'],
            $data['legitinterestprocess'],
            $data['legitinterestapplicable'],
            $data['autodecision'],
            $data['sourcepd'],
            $data['consentlink'],
            $data['locationpd'],
            $data['dpiarequired'],
            $data['dpiaprogress'],
            $data['linkdpia'],
            $data['pdbreach'],
            $data['linkpdbreach'],
            $data['dpbill'],
            $data['lawbasisprocess'],
            $data['linkretention'],
            $data['pdretained'],
            $data['reason']
        )));
        $arr_return=["code"=>200, "success"=>true, "data"=>['message' => "success"] ];
        return $arr_return;
        } catch (Exception $e) {
            return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
        }
    }

?>