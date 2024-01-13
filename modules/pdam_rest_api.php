<?php 

function GetPDAMHandler($funcCallType)
{
  try {

    switch ($funcCallType) {
      case "get-type-of-data":
        if (isset($GLOBALS['companycode'])) {
          $output = get_type_of_data($GLOBALS['companycode']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

    case "get-product-services":
        if (isset($GLOBALS['companycode'])) {
            $output = get_product_services($GLOBALS['companycode']);
            if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
            } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
            }
        } else {
            catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

        //Write API
      case "add-product-services":
        $jsonString = file_get_contents('php://input');
        if ($jsonString == "") {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        $json = json_decode($jsonString, true);
        if (!is_array($json)) {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          exit();
        }
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = add_new_product_and_services($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $json);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

        case "initiate":
          $jsonString = file_get_contents('php://input');
          if ($jsonString == "") {
            catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
            exit();
          }
          $json = json_decode($jsonString, true);
          if (!is_array($json)) {
            catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
            exit();
          }
          if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
            $output = pdam_initiate_submit($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $GLOBALS['custcode'], $GLOBALS['alw'], $json);
            if ($output['success']) {
              commonSuccessResponse($output['code'], $output['data']);
            } else {
              catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
            }
          } else {
            catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
          }
          break;


      default:
        catchErrorHandler(400, ["message" => E_INV_REQ, "error" => ""]);
        break;
    }
  } catch (Exception $e) {
    catchErrorHandler($output['code'], ["message" => "", "error" => $e->getMessage()]);
  }
}

//add_new_product_and_service
function add_new_product_and_services($companycode,$email,$role,$data)
    {
      try {
        global $session;

        if (!isset($data['product'])) {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Missing Product"];
            exit();
        }

        $product = escape_input($data['product']);

        if ($product=="") {
            return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid Product/Service"];
            exit();
        }

        $columns=[
          "companycode",
          "status",
          "createdate",
          "id",
          "effectivedate",
          "filleremail",
          "fillerrole",
          "product"
        ];
        $columns_data=[
          $companycode,
          "1",
          new \Cassandra\Timestamp(),
          "product",
          new \Cassandra\Timestamp(),
          $email,
          $role,
          $product
        ];
        $data_for_insert=[
          "action"=>"insert", //read/insert/update/delete
          "table_name"=>"product_and_services", //provide actual table name or dummy table name thats been in JSON/arr file
          "columns"=>$columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
          "isCondition"=>false,
          "condition_columns"=>"",
          "columns_data"=>$columns_data,
          "isAllowFiltering"=>false
        ];
        $table_insert=table_crud_actions($data_for_insert);
        return $table_insert;
      } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
      }
    }

    function pdam_initiate_submit($companycode, $email, $role, $custcode, $activeLaw, $data)
    {
    try {
          global $session;
          $required_keys = [
            "pdam_name", "type_of_data","owner", "product_and_service", "product"
          ];

              //check if array is valid
            if (!checkKeysExist($data, $required_keys)) {
              return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => ""];
              exit();
            }

            //check value incoming
            if (!checkValueExist($data, $required_keys)) {
              return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => implode(", ", $required_keys_val) . " value is mandatory"];
              exit();
            }

            $type_sel = escape_input($data['type_of_data']);
            $product_sel = escape_input($data['product']);
            $pdam_name = escape_input($data['pdam_name']);
            $owner = escape_input($data['owner']);
            $pns_sel = escape_input($data['product_and_service']);
      
            $config_tid  = get_active_config_txn_id($companycode, "pdam");
            if ($config_tid == "") {
              return ["code" => 400, "success" => false, "message" => E_PAYLOAD_INV, "error" => "Invalid configuration"];
              exit();
            }
           
            $section_1_tid=new \Cassandra\Uuid();
            $timestamp=new \Cassandra\Timestamp();
            $columns=[
              "companycode","status","createdate","effectivedate","section_1_tid","config_tid","typeofdata","product","pdam_name","owner","product_and_services"
            ];
            $columns_data=[
              $companycode,"1",$timestamp,$timestamp,(string)$section_1_tid,$config_tid,$type_sel,$product_sel,$pdam_name,$owner,$pns_sel
            ];
            $data_for_insert=[
            "action"=>"insert", //read/insert/update/delete
            "table_name"=>"pdam_section_1", //provide actual table name or dummy table name thats been in JSON/arr file
            "columns"=>$columns, //Provide one element as ALL for All column else provide individual column name. It could be also actual or dummny name.
            "isCondition"=>false,
            "condition_columns"=>"",
            "columns_data"=>$columns_data,
            "isAllowFiltering"=>false
            ];
            $table_insert=table_crud_actions($data_for_insert);
            if (!$table_insert['success']) {
              return $table_insert; exit();
            }
            //Create notice

            $email_role_array=module_assign_email_role_list("PG075","create",$companycode);
            foreach ($email_role_array as $em_role) {
                $notice_link="pdam_section_2.php?tid=".$config_tid."&s1_tid=".(string)$section_1_tid;
                $notice_output=notice_write("PDAM201",$companycode,$email,$role,$notice_link,$em_role['email'],$em_role['role'],$pdam_name,(string)$section_1_tid);
            }
            update_landing_module($companycode, $email, $role, $custcode, $activeLaw, "8");

            $arr_return = ["code" => 200, "success" => true, "data" => ""];
            return $arr_return;
        } catch (\Exception $e) {
          return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
        }
    }

//get APIs
function get_type_of_data($companycode)
{
  try {
    global $session;
    $arr=["Customer","Employee","Vendor"];
    $result=$session->execute($session->prepare("SELECT type_of_data FROM pdam_type_of_data WHERE companycode=?  AND status=? ALLOW FILTERING"),array('arguments'=>array($companycode,"1")));
    foreach ($result as $row) { array_push($arr,$row['type_of_data']); }
     $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function get_product_services($companycode)
{
try {
        global $session;
        $arr=[];
        $result= $session->execute($session->prepare("SELECT product FROM product_and_services WHERE companycode=? AND status=? AND id=?"),array('arguments'=>array($companycode,"1","product")));
        foreach ($result as $row) {
            array_push($arr,$row['product']);
        }
        sort($arr);
        $arr_return = ["code" => 200, "success" => true, "data" => $arr];
        return $arr_return;
    } catch (\Exception $e) {
        return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
    }
}


?>