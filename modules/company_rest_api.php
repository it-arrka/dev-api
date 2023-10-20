<?php 

function GetCompanyListHandler($funcCallType){
  try{

    switch($funcCallType){
      case "companyList":
        if(isset($GLOBALS['email'])){
          $output = get_company_list($GLOBALS['email']);
          if($output['success']){
            commonSuccessResponse($output['code'],$output['data'],$output['message']);
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
            commonSuccessResponse($output['code'],$output['data'],$output['message']);
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
    catchErrorHandler($output['code'], [ "message"=>"", "error"=>$e->getMessage() ]);
  }
}

function get_company_list($email)
{
  global $session;
  try {

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

    foreach ($result as $row) {
        if($row['companycode']!=""){
            $result_comp =$session->execute($session->prepare("SELECT companyname FROM company WHERE companycode=?"), array('arguments'=>array(
                $row['companycode']
            )));
            if($result_comp->count()>0){
                $arr_company[] = [
                    "companycode" => $row['companycode'],
                    "companyname" => $result_comp[0]['companyname']
                ];
            }
        }
    }

    $arr_return=["code"=>200, "success"=>true, "message"=>"", "data"=>$arr_company];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
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

    $link=$_ENV['FILE_LINK_CURRENT'];
    $src_link=$link.'/assets/media/yourlogohere.png';

    $clientlogoref = $result[0]['clientlogoref'];
    if($clientlogoref!=''){
      $result_doc=$session->execute($session->prepare("SELECT blobAsText(docupl),doctype,docname FROM docupload WHERE docid=?"),array('arguments'=>array(new Cassandra\Uuid($companycode))));
      if ($result_doc->count()>0) {
        $src_link ='data:image/jpeg;base64,'.$result_doc[0]['system.blobastext(docupl)'];
      }
    }

    $arr_return=["code"=>200, "success"=>true, "message"=>"", "data"=>['src'=>$src_link]];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code"=>500, "success" => false, "message"=>E_FUNC_ERR, "error"=>$e->getMessage() ]; 
  }
}

?>