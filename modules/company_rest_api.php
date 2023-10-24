<?php 

function GetCompanyHandler($funcCallType){
  try{

    switch($funcCallType){
      case "companyList":
        if(isset($GLOBALS['email'])){
          $output = get_company_list($GLOBALS['email']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
          }
        }else{
          catchErrorHandler(400,[ "message"=>E_PAYLOAD_INV, "error"=>"" ]);
        }
        break;

      case "companyLogo":
        if(isset($GLOBALS['companycode'])){
          $output = get_company_logo($GLOBALS['companycode']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data']);
          }else{
            catchErrorHandler($output['code'],[ "message"=>$output['message'], "error"=>$output['error'] ]);
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
    catchErrorHandler($output['code'], [ "message"=>"", "error"=>(string)$e ]);
  }
}

//get active company to redirect
function get_last_active_customer_details($email){
  try{
      global $session;
      //get active company list
      $companyListArr = get_company_list($email);
      if(!$companyListArr['success']){
        return $companyListArr; exit();
      }

      $companyListData=$companyListArr['data'];
      $companyList=[];
      foreach($companyListData as $company){
        $companyList[$company['companycode']]= $company['companyname'];
      }

      $defaultCompanyName="";
      $defaultCompanyCode="";
      $getFromList=false;
      
      $result =$session->execute($session->prepare("SELECT defcompflag,defcompcode FROM customer WHERE custemailaddress=?"),array('arguments'=>array($email)));

      foreach($result as $row){
        if($row['defcompcode']==''){ 
          $getFromList=true;
        }else{
          if(isset($companyList[$row['defcompcode']])){
            $defaultCompanyCode=$row['defcompcode'];
            $defaultCompanyName=$companyList[$row['defcompcode']];
          }else{
            $getFromList=true;
          }
        }
      }

      if($getFromList){
        $defaultCompanyName=$companyListData[0]['companyname'];
        $defaultCompanyCode=$companyListData[0]['companycode'];
      }

      //applicable law list
      $lawArr=[];
      $result_law =$session->execute($session->prepare("SELECT law FROM applicablelaw WHERE companycode=? AND status=? ALLOW FILTERING"),array('arguments'=>array($defaultCompanyCode,"1")));
      foreach($result_law as $row_law){ if($row_law['law']!=""){ array_push($lawArr,$row_law["law"]); } }

      //role list
      $roleArr=[];
      $result_role =$session->execute($session->prepare("SELECT rtcrole FROM roletocustomer WHERE companycode=? AND rolestatus=? ALLOW FILTERING"),array('arguments'=>array($defaultCompanyCode,"1")));
      foreach($result_role as $row_role){ if($row_role['rtcrole']!=""){ array_push($roleArr,$row_role["rtcrole"]); } }

      //get default role and law
      $lastactivelaw="";
      $lastactiverole="";
      $result_lr =$session->execute($session->prepare("SELECT lastactivelaw,lastactiverole FROM customer_active_data WHERE email=? AND companycode=?"),array('arguments'=>array($email,$defaultCompanyCode)));
      foreach($result_lr as $row_lr){
        $lastactivelaw=$row_lr['lastactivelaw'];
        $lastactiverole=$row_lr['lastactiverole'];
      }

      if(!in_array($lastactivelaw,$lawArr)){
        $lastactivelaw=$lawArr[0];
      }

      if(!in_array($lastactiverole,$roleArr)){
        $lastactiverole=$roleArr[0];
      }


      $arr_return=["code"=>200, "success"=>true, "data"=>[
        "companyname"=>$defaultCompanyName,
        "companycode"=>$defaultCompanyCode,
        "role"=>$lastactiverole,
        "law"=>$lastactivelaw
      ]];

      return $arr_return;

    }catch(Exception $e){
      return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
    }
}

function get_company_list($email)
{
  try {
    global $session;
    if($email==""){
        //Bad Request Error
        return ["code"=>400, "success" => false, "message"=>E_PAYLOAD_INV, "error"=>"" ]; exit();
    }

    //convert email to lowercase
    $email=strtolower($email); 
    $arr_company =[];

    //Find if email exists in db
    $result =$session->execute($session->prepare("SELECT companycode FROM roletocustomer WHERE rtccustemail=? AND rolestatus=? ALLOW FILTERING"), array('arguments'=>array(
        $email,"1"
    )));

    $dataFound = false;
    $existCompData=[];

    foreach ($result as $row) {
        if($row['companycode']!="" && !isset($existCompData[$row['companycode']])){
            $result_comp =$session->execute($session->prepare("SELECT companyname FROM company WHERE companycode=?"), array('arguments'=>array(
                $row['companycode']
            )));
            if($result_comp->count()>0){
                $dataFound = true;
                $arr_company[] = [
                    "companycode" => $row['companycode'],
                    "companyname" => $result_comp[0]['companyname']
                ];
            }
            $existCompData[$row['companycode']]=true;
        }
    }

    if(!$dataFound){
      return ["code"=>404, "success" => false, "message"=>E_RES_NOT_FOUND, "error"=>"" ]; exit();
    }

    $arr_return=["code"=>200, "success"=>true, "data"=>$arr_company];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>(string)$e ]; 
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

?>