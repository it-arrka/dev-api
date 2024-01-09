<?php

function GetAssetHandler($funcCallType)
{
  try {
    switch ($funcCallType) {

      case "load_asset_config_data":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = load_asset_config_data($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "asset_sub_cat":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = asset_sub_cat($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "asset_data_register":

        $jsonString = file_get_contents('php://input');
        $json = json_decode($jsonString, true);

        if (isset($json['data']) && isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role'])) {
          $output = asset_data_register($json['data'], $GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "asset_view_data":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($_GET['page_index'])) {
          $output = asset_view_data($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $_GET['page_index']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "asset_del_id":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($_POST['id'])) {

          die('case');

          $output = asset_del_id($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role'], $_POST['id']);
          if ($output['success']) {
            commonSuccessResponse($output['code'], $output['data']);
          } else {
            catchErrorHandler($output['code'], ["message" => $output['message'], "error" => $output['error']]);
          }
        } else {
          catchErrorHandler(400, ["message" => E_PAYLOAD_INV, "error" => ""]);
        }
        break;

      case "data_row_save":
        if (isset($GLOBALS['companycode']) && isset($GLOBALS['email']) && isset($GLOBALS['role']) && isset($_POST['id'])) {

          die('case');

          $output = data_row_save($GLOBALS['companycode'], $GLOBALS['email'], $GLOBALS['role']);
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



function load_asset_config_data($companycode, $email, $role)
{
  try {
    global $session;
    //find active config_tid
    $config_tid = " ";
    $res_txn_active = $session->execute($session->prepare("SELECT wcvid FROM workflowconfigversions WHERE wcvcompanycode=? AND wcvworkflowname=? AND status=? AND active_status=? ALLOW FILTERING"), array('arguments' => array($companycode, "asset", "1", "active")));
    if ($res_txn_active->count() > 0) {
      $config_tid = (string) $res_txn_active[0]['wcvid'];
    }
    $arr = [];
    $arr_type = ["confidentiality", "integrity", "availability"];
    $arr_default = ["High", "Medium", "Low"];
    foreach ($arr_type as $type) {
      $result = $session->execute($session->prepare('SELECT category FROM asset_config_data WHERE companycode=? AND transactionid=? AND status=? AND type=?'), array('arguments' => array($companycode, $config_tid, "1", $type)));
      if ($result->count() > 0) {
        $arr_data = [];
        foreach ($result as $row) {
          array_push($arr_data, $row['category']);
        }
        $arr[$type] = $arr_data;
      } else {
        $arr[$type] = $arr_default;
      }
    }

    $arr_return = ["code" => 200, "success" => true, "data" => $arr];
    return $arr_return;
  } catch (\Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function asset_sub_cat($companycode)
{
  $arr = array();
  $arr_n = array();
  $arr_d = array();
  $final_arr = array();
  global $session;
  $res = $session->execute($session->prepare('SELECT assetcat FROM assetcat WHERE status=? ALLOW FILTERING'), array('arguments' => array("1")));
  $res_cat = $session->execute($session->prepare('SELECT category FROM assetcategory WHERE status=? AND companycode=? ALLOW FILTERING'), array('arguments' => array("1", $companycode)));
  $res_d = $session->execute($session->prepare('SELECT locationdepartment FROM locationinscope WHERE companycode=? ALLOW FILTERING'), array('arguments' => array($companycode)));
  $res_n = $session->execute($session->prepare('SELECT pdcategory FROM pditem WHERE status=? ALLOW FILTERING'), array('arguments' => array("1")));
  foreach ($res as $row) {
    array_push($arr, $row['assetcat']);
  }
  foreach ($res_cat as $row_cat) {
    array_push($arr, $row_cat['category']);
  }
  foreach ($res_n as $row_n) {
    array_push($arr_n, $row_n['pdcategory']);
  }
  foreach ($res_d as $row_d) {
    $dept = explode("|", $row_d['locationdepartment']);
    foreach ($dept as $det) {
      $dep_t = explode(",", $det);
      if ($dep_t[0] !== "") {
        array_push($arr_d, $dep_t[0]);
      }
    }
  }
  sort($arr);
  sort($arr_n);
  sort($arr_d);
  $final_arr = array("asset" => array_unique($arr), "pd" => array_unique($arr_n), "dept" => array_unique($arr_d));
  // echo json_encode($final_arr);

  $arr_return = ["code" => 200, "success" => true, "data" => $final_arr];
  return $arr_return;
}

function asset_data_register($data, $companycode, $email, $role)
{

  global $session;
  $transcode = $_POST['asset_txn_id_pd'];

  foreach ($data as $key => $value) {
    $data[$key] = escape_input($value);
  }

  try {
    $session->execute(
      $session->prepare('INSERT INTO transasscust(
        transasscust,
        createdate,
        effectivedate,
        status,
        transasscompanycode,
        transasscustcode,
        transasstrancode,
        transdeptname,
        assetcat,
        assettype,
        assetname,
        transassetno,
        transassetowner,
        transconfidentiality,
        transintegrity,
        transavailability

      ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'),
      array(
        'arguments' => array(
          new \Cassandra\Uuid(),
          new \Cassandra\timestamp(),
          new \Cassandra\timestamp(),
          "1",
          $companycode,
          $customer_id,
          (string) $transcode,
          $data['dept'],
          $data['asset_category'],
          $data['asset_type'],
          $data['asset_name_d'],
          $data['asset_no'],
          $data['asset_owner'],
          $data['confidentiality'],
          $data['integrity'],
          $data['availability']
        )
      )
    );
    $arr_return = ["code" => 200, "success" => true];
    return $arr_return;
  } catch (Exception $e) {
    catchErrorHandler($output['code'], ["message" => "", "error" => $e->getMessage()]);
  }
}

function asset_view_data($companycode, $email, $role, $page_index, $return_type = "")
{
  global $session;
  $arr = array();

  try {
    $arr_txn = [];
    $arr_txn_final = [];
    $page_index = 0;
    if (isset($page_index)) {
      $page_index = $page_index;
    }

    $result_txn = $session->execute($session->prepare('SELECT transasscust,assetcat,transdeptname,createdate FROM transasscust WHERE transasscompanycode=? AND status=? ALLOW FILTERING'), array('arguments' => array($companycode, "1")));
    foreach ($result_txn as $row_txn) {
      $createdate_str = (string) $row_txn['createdate'];
      $arr_txn[(string) $row_txn['transasscust']] = (int) $createdate_str;
    }

    arsort($arr_txn);
    if ($return_type == "all_data") {
      $arr_txn_final = $arr_txn;
    } else {
      $arr_chunk = array_chunk($arr_txn, 10, true);
      $total_index = count($arr_chunk);
      if (isset($arr_chunk[$page_index])) {
        $arr_txn_final = $arr_chunk[$page_index];
      }
    }

    foreach ($arr_txn_final as $keyid => $valueid) {
      $result = $session->execute($session->prepare('SELECT * FROM transasscust WHERE transasscust=?'), array('arguments' => array(new \Cassandra\Uuid($keyid))));
      foreach ($result as $row) {
        $pdate = (array) $row['transpurdt'];
        $p_d = "";
        if (isset($pdate['seconds'])) {
          $p_d = date("Y-m-d", $pdate['seconds']);
          if (date("Y", $pdate['seconds']) == '1970') {
            $p_d = date("Y-m-d");
          }
        }
        $edate = (array) $row['transpurdt'];
        $e_d = "";
        if (isset($pdate['seconds'])) {
          $e_d = date("Y-m-d", $edate['seconds']);
          if (date("Y", $edate['seconds']) == '1970') {
            $e_d = date("Y-m-d");
          }
        }
        if ($row["assetcat"] == 'null' || $row["assetcat"] == null) {
          $row["assetcat"] = '';
        }
        if ($row["assettype"] == 'null' || $row["assettype"] == null) {
          $row["assettype"] = '';
        }
        if ($row["assetname"] == 'null' || $row["assetname"] == null) {
          $row["assetname"] = '';
        }
        if ($row["transdeptname"] == 'null' || $row["transdeptname"] == null) {
          $row["transdeptname"] = '';
        }
        if ($row["transassetno"] == 'null' || $row["transassetno"] == null) {
          $row["transassetno"] = '';
        }
        if ($row["transassetowner"] == 'null' || $row["transassetowner"] == null) {
          $row["transassetowner"] = '';
        }
        if ($row["transconfidentiality"] == 'null' || $row["transconfidentiality"] == null) {
          $row["transconfidentiality"] = '';
        }
        if ($row["transintegrity"] == 'null' || $row["transintegrity"] == null) {
          $row["transintegrity"] = '';
        }
        if ($row["transcriticality"] == 'null' || $row["transcriticality"] == null) {
          $row["transcriticality"] = '';
        }
        if ($row["transclassification"] == 'null' || $row["transclassification"] == null) {
          $row["transclassification"] = '';
        }
        if ($row["transbackupreqd"] == 'null' || $row["transbackupreqd"] == null) {
          $row["transbackupreqd"] = '';
        }
        if ($row["transbackupfreq"] == 'null' || $row["transbackupfreq"] == null) {
          $row["transbackupfreq"] = '';
        }
        if ($row["transrettime"] == 'null' || $row["transrettime"] == null) {
          $row["transrettime"] = '';
        }
        if ($row["transprodname"] == 'null' || $row["transprodname"] == null) {
          $row["transprodname"] = '';
        }
        if ($row["transsuppliername"] == 'null' || $row["transsuppliername"] == null) {
          $row["transsuppliername"] = '';
        }
        if ($row["transmodel"] == 'null' || $row["transmodel"] == null) {
          $row["transmodel"] = '';
        }
        if ($row["transslno"] == 'null' || $row["transslno"] == null) {
          $row["transslno"] = '';
        }
        $arr[] = array(
          "assetcat" => $row["assetcat"],
          "assettype" => $row["assettype"],
          "assetname" => $row["assetname"],
          "transdeptname" => $row["transdeptname"],
          "transassetno" => $row["transassetno"],
          "transassetowner" => $row["transassetowner"],
          "transconfidentiality" => $row["transconfidentiality"],
          "transintegrity" => $row["transintegrity"],
          "transavailability" => $row["transavailability"],
          "transpriv" => $row["transpriv"],
          "transcriticality" => $row["transcriticality"],
          "transclassification" => $row["transclassification"],
          "transbackupreqd" => $row["transbackupreqd"],
          "transbackupfreq" => $row["transbackupfreq"],
          "transrettime" => $row["transrettime"],
          "transprodname" => $row["transprodname"],
          "transsuppliername" => $row["transsuppliername"],
          "transmodel" => $row["transmodel"],
          "transslno" => $row["transslno"],
          "transpurdt" => $p_d,
          "transAMCexpirydt" => $e_d,
          "transasscust" => (string) $row["transasscust"]
        );
      }
    }
    $arr_final = [
      "total_index" => $total_index,
      "page_index" => $page_index,
      "data" => $arr
    ];

    $arr_return = ["code" => 200, "success" => true, "data" => $arr_final];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function asset_del_id($companycode, $email, $role, $id)
{
  die('dead asset_del_id');
  try {
    global $session;
    $result = $session->execute($session->prepare('SELECT status FROM transasscust WHERE transasscompanycode=? AND transasscust=? ALLOW FILTERING'), array('arguments' => array($companycode, new \Cassandra\Uuid($id))));
    if ($result->count() > 0) {
      $session->execute($session->prepare('UPDATE transasscust SET status=? WHERE transasscust=?'), array('arguments' => array("0", new \Cassandra\Uuid($id))));

      $arr_return = ["code" => 200, "success" => true];
      return $arr_return;
    } else {
      echo "Error Occured. Try Again!!";
    }
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

function data_row_save($companycode, $email, $role)
{
  global $session;
  $data = json_decode($_POST['data_row_save'], true);
  $purchase_date_ff = date("d F Y", strtotime($data['transpurdt'] . "+1 day"));
  $purchase_date = $purchase_date_ff . " 05:30:00";
  $expiry_date_ff = date("d F Y", strtotime($data['transAMCexpirydt'] . "+1 day"));
  $expiry_date = $expiry_date_ff . " 05:30:00";
  $arr = array();
  try {
    $result = $session->execute(
      $session->prepare('UPDATE transasscust SET
       assetname=?,
       assettype=?,
       transassetno=?,
       transamcexpirydt=?,
       transassetowner=?,
       transavailability=?,
       transbackupfreq=?,
       transbackupreqd=?,
       transclassification=?,
       transconfidentiality=?,
       transcriticality=?,
       transintegrity=?,
       transmodel=?,
       transpriv=?,
       transprodname=?,
       transpurdt=?,
       transrettime=?,
       transslno=?,
       transsuppliername=?,
       modifydate=?
       WHERE transasscust=?
    '),
      array(
        'arguments' => array(
          escape_input($data['assetname']),
          escape_input($data['assettype']),
          escape_input($data['transassetno']),
          new \Cassandra\Date(strtotime($expiry_date)),
          escape_input($data['transassetowner']),
          escape_input($data['transavailability']),
          escape_input($data['transbackupfreq']),
          escape_input($data['transbackupreqd']),
          escape_input($data['transclassification']),
          escape_input($data['transconfidentiality']),
          escape_input($data['transcriticality']),
          escape_input($data['transintegrity']),
          escape_input($data['transmodel']),
          escape_input($data['transpriv']),
          escape_input($data['transprodname']),
          new \Cassandra\Date(strtotime($purchase_date)),
          escape_input($data['transpurdt']),
          escape_input($data['transslno']),
          escape_input($data['transsuppliername']),
          new \Cassandra\timestamp(),
          new \Cassandra\Uuid($_POST['id'])
        )
      )
    );
    $arr_return = ["code" => 200, "success" => true];
    return $arr_return;
  } catch (Exception $e) {
    return ["code" => 500, "success" => false, "message" => E_FUNC_ERR, "error" => $e->getMessage()];
  }
}

?>