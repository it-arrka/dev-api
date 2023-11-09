<?php 

function get_action_status_by_refid($refid){
    try {
        global $session;
        $result_act_mgmt=$session->execute($session->prepare("SELECT mgmtresponseaction,selected_response FROM action_management_response WHERE refid=? AND status=? AND companycode=?  ALLOW FILTERING"),array('arguments'=>array((string)$key_id,"1",$_SESSION['companycode'])));
        if ($result_act_mgmt->count()>0) {
          if($result_act_mgmt[0]['mgmtresponseaction']=='A001'){
            $row['screen_status']='Risk Accepted';
          }
          // }else{
        $result_act=$session->execute($session->prepare("SELECT review_status FROM actions_data WHERE transactionid=? AND status=?  ALLOW FILTERING"),array('arguments'=>array((string)$key_id,"1")));
        if ($result_act->count()>0) {
          foreach($result_act as $row_act){
            $action_status_act_arr=get_assessment_status_and_link_for_action_txn("","","","",$row_act['review_status']);
            $status=$action_status_act_arr['txn_status'];
            array_push($action_status_act,$status);
          }
        }else {
          $result_act_n=$session->execute($session->prepare("SELECT action_status FROM actions_data WHERE transactionid=? AND status=?  ALLOW FILTERING"),array('arguments'=>array((string)$key_id,"1")));
          if ($result_act_n->count()>0) {
            foreach($result_act_n as $row_act_n){
              $action_status_act_arr=get_assessment_status_and_link_for_old_action("","","","",$row_act_n['action_status']);
              $status=$action_status_act_arr['txn_status'];
            array_push($action_status_act,$status);
              }
          }
        }
      
        }
    } catch (\Exception $e) {
        //throw $th;
    }
}


?>