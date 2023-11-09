<html>

<body>
<pre>
<?php 

require '../config.php';
// require '/usr/share/nginx/html/arr-test/api/config.php';

function get_vendor_txn(){
    try {
        global $session;
        $result_company= $session->execute("SELECT companyname,companycode FROM company");
        foreach($result_company as $row_company){
            $companycode= $row_company['companycode'];
            $companyname= $row_company['companyname'];
            $arr=[];
            $result_txn= $session->execute($session->prepare("SELECT * FROM transactions WHERE companycode=? AND transactiontype=? ALLOW FILTERING"),array('arguments'=>array($companycode,"vendor")));
            foreach($result_txn as $row_txn){
                $arr[$row_txn['vendorid']][]= $row_txn;
            }

            echo $companyname."<br>";

            foreach($arr as $vendorid => $value_arr){
                //find out which vendor needs to be updated
                foreach($value_arr as $value){
                    if($value['active_for_scoring']==1){
                        unset($arr[$vendorid]);
                    }
                }
            }

            //lets updation begin
            foreach($arr as $vendorid => $value_arr){
                //find out which vendor needs to be updated
                $seconds_arr=[];
                foreach($value_arr as $value){
                    // unset($arr[$vendorid]);
                    //find out the latest one
                    $createdate=(string)$value['modifydate'];
                    if($createdate==""){
                        $createdate=(string)$value['createdate'];
                    }
                    $seconds_arr[(string)$value['transactionid']]=(int)$createdate;
                }
                print_r($seconds_arr);

                if(count($seconds_arr)>0){
                    $tid_to_update_arr = array_keys($seconds_arr, max($seconds_arr));
                    $tid_to_update=$tid_to_update_arr[0];
                    echo $tid_to_update."<br>";
                     $result_txn_cosusj= $session->execute($session->prepare("SELECT * FROM transactions WHERE transactionid=?"),array('arguments'=>array(
                        new \Cassandra\Uuid($tid_to_update)
                    )));

                    if($result_txn_cosusj->count()>0){
                        $session->execute($session->prepare("UPDATE transactions SET active_for_scoring=? WHERE transactionid=?"),array('arguments'=>array(
                            1,new \Cassandra\Uuid($tid_to_update)
                        ))); 
                      }


                    echo "<hr>";

                }

            }


          
          


           
         

        }

      } catch (\Exception $e) {
        echo $e;
      }
}

function vendor_comp_score_assessment_fix()
{
  try {
    global $session;

    $result_company= $session->execute("SELECT companyname,companycode FROM company");
    foreach($result_company as $row_company){
        $companycode= $row_company['companycode'];
        $companyname= $row_company['companyname'];
        $result_txn= $session->execute($session->prepare("SELECT version,vendorid,transactionid FROM transactions WHERE companycode=? AND transactiontype=? ALLOW FILTERING"),array('arguments'=>array(
            $companycode,"vendor"
        )));

        foreach($result_txn as $row_txn){
          if($row_txn['vendorid']!=''){
            $tid =(string)$row_txn['transactionid'];
            $arr=[];

            $result= $session->execute($session->prepare("SELECT comp_score FROM suppresponse WHERE transactionid=? ALLOW FILTERING"),array('arguments'=>array($tid)));
            foreach ($result as $row) {
              if ($row['comp_score']=='1' || $row['comp_score']=='2' || $row['comp_score']=='3') { $row['comp_score']='1'; }
              if (isset($arr[$row['comp_score']])) {
                $arr[$row['comp_score']]=$arr[$row['comp_score']]+1;
              }else {
                $arr[$row['comp_score']]=1;
              }
            }

            $notapplicable=0; $compliant=0; $noncompliant=0; $score=0;
    
            if (isset($arr['NA'])) { $notapplicable=$arr['NA']; }
            if (isset($arr['1'])) { $compliant=$arr['1']; }
            if (isset($arr['0'])) { $noncompliant=$arr['0']; }
        
            $total=$notapplicable+$compliant+$noncompliant;
            $divisor=$total-$notapplicable;
        
            if ($divisor==0) {
              $score=0.01;
            }else {
              $score=$compliant/$divisor;
              if($score==0){ $score=0.01; }
            }

            $score=number_format((float)$score, 2, '.', '');
    
            $session->execute($session->prepare("INSERT INTO compliance_score_assessment(transactionid,companycode,createdate,effectivedate,notapplicable,compliant,noncompliant,score) VALUES(?,?,?,?,?,?,?,?)"),array('arguments'=>array(
            $tid,$companycode,new \Cassandra\Timestamp(),new \Cassandra\Timestamp(),(int)$notapplicable,(int)$compliant,(int)$noncompliant,new \Cassandra\Float($score)
            )));

            echo $companyname." Successfully added<hr>";

          }
        }
    }

  } catch (\Exception $e) {
    $arr_return=['success'=>false,"msg"=>"Error Occured$e","data"=>(string)$e]; return $arr_return;
  }
}

// get_vendor_txn();
// vendor_comp_score_assessment_fix();
?>
</pre>
</body>

</html>