<?php
function document_upload_api($filesPost,$allowedFileType,$minFileSize=1,$maxFileSize=5000000,$description="",$companycode,$filleremail,$fillercustcode,$transactionid){
  global $session; $arr=array();
  // $filesPost=$_FILES['post'];
  //Validation of data
  $no_files = count($filesPost['name']);
  if ($no_files<1) { 
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Please choose at least one file" ]; exit();
  }
  $allowed= $allowedFileType;
  for ($i = 0; $i < $no_files; $i++) {
    if ($filesPost["error"][$i] > 0) { 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File Error: ".$filesPost["error"][$i] ]; exit();
    }
    if ($filesPost["size"][$i] > $maxFileSize) { 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File size not allowed" ]; exit();
    }
    $filename=$filesPost['name'][$i];
    $doctype = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($doctype, $allowed)){ 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File type not allowed" ]; exit();
    }
  }
  //Actual data entry
  $data=[];
  $createdate=new \Cassandra\Timestamp();
  $effectivedate=new \Cassandra\Timestamp();
  if ($no_files>0) {
  $docid ="docid";
  $docname="docname";
  for ($i = 0; $i < $no_files; $i++) {
      // Check file size
      $tmpFilePath = $filesPost['tmp_name'][$i];
      $filename=$filesPost['name'][$i];
      $docname=$filename;
      $contents= file_get_contents($tmpFilePath);
      $doctype = pathinfo($filename, PATHINFO_EXTENSION);
      $base64 = base64_encode($contents);
      $uuid_for_file = new \Cassandra\Uuid();
      $docid=(string)$uuid_for_file;

      $query_for_upload = $session->prepare("INSERT INTO docupload (response,custemail,docid,docname,docupl,custcode,doctype,transactionid,companycode,createdate,effectivedate) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
      $session->execute($query_for_upload,array('arguments'=>array($description,$filleremail,$uuid_for_file,$filename,$base64,$fillercustcode,$doctype,$transactionid,$companycode,$createdate,$effectivedate)));
      $data[]=["docid"=>$docid,"doctype"=>$doctype,"docname"=>$docname];
    }
  }

  $arr_return=["code"=>200, "success"=>true, "data"=>$data ];
  return $arr_return;
}

function document_upload_folder_api($filesPost,$allowedFileType,$minFileSize=1,$maxFileSize=5000000,$description="",$companycode,$filleremail,$fillercustcode,$transactionid,$folder_location){
  global $session; $arr=array();
  // $filesPost=$_FILES['post'];
  //Validation of data
  $no_files = count($filesPost['name']);
  if ($no_files<1) { 
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Please choose at least one file" ]; exit();
  }
  if ($folder_location=="") { 
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Folder location is not specified." ]; exit();
  }
  if(!is_dir($folder_location)) { 
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Folder location does not exist." ]; exit();
  }

  $allowed= $allowedFileType;
  for ($i = 0; $i < $no_files; $i++) {
    if ($filesPost["error"][$i] > 0) { 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File Error: ".$filesPost["error"][$i] ]; exit();
    }
    if ($filesPost["size"][$i] > $maxFileSize) { 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File size not allowed" ]; exit();
    }
    $filename=$filesPost['name'][$i];
    $doctype = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($doctype, $allowed)){ 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File type not allowed" ]; exit();
    }
  }
  //Actual data entry
  $data=[];
  if ($no_files>0) {
  $docid ="docid";
  $docname="docname";
  for ($i = 0; $i < $no_files; $i++) {
      // Check file size
      $tmpFilePath = $filesPost['tmp_name'][$i];
      $filename=$filesPost['name'][$i];
      $docname=$filename;
      $contents= file_get_contents($tmpFilePath);
      $doctype = pathinfo($filename, PATHINFO_EXTENSION);
      $base64 = base64_encode($contents);
      $uuid_for_file = new \Cassandra\Uuid();
      $docid=(string)$uuid_for_file;

      // $folder_location=server_path("ENTIRE")."/files/arrka/";

      if(!is_dir($folder_location.(string)$uuid_for_file ."/")) { mkdir($folder_location.(string)$uuid_for_file ."/",0777); }

      $target_location=$folder_location.(string)$uuid_for_file ."/".basename($filename);
      if (move_uploaded_file($tmpFilePath, $target_location)) {
      } else { $arr_final=["success"=>false,"msg"=>"Sorry, there was an error uploading your file."]; return $arr_final; exit(); }
      $data[]=["docid"=>$docid,"doctype"=>$doctype,"docname"=>$docname];
    }
  }

  $arr_return=["code"=>200, "success"=>true, "data"=>$data ];
  return $arr_return;
}


function document_validation_api($filesPost,$allowedFileType,$minFileSize=1,$maxFileSize=5000000){
  global $session; $arr=array();
  // $filesPost=$_FILES['post'];
  //Validation of data
  $no_files = count($filesPost['name']);
  if ($no_files<1) { 
    return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"Please choose at least one file" ]; exit();
  }
  $allowed= $allowedFileType;
  for ($i = 0; $i < $no_files; $i++) {
    if ($filesPost["error"][$i] > 0) { 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File Error: ".$filesPost["error"][$i] ]; exit();
    }
    if ($filesPost["size"][$i] > $maxFileSize) { 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File size not allowed" ]; exit();
    }
    $filename=$filesPost['name'][$i];
    $doctype = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($doctype, $allowed)){ 
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"File type not allowed" ]; exit();
    }
  }
  $arr_return=["code"=>200, "success"=>true, "data"=>[ 'message'=>'File is ok' ] ];
  return $arr_return;
}



?>
