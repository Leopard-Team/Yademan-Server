<?php
session_start();
$conn = include 'Connection.php';
$out = array();
try {
    $used;
    $discount=$_POST['discount_code'];
    $sql = "SELECT * FROM discount WHERE code = '$discount'";
    $out = array();
    if ($res = $conn->query($sql)) {
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $out['success'] = true;
            $out['message'] = $row['description'];
            $used=$row['used'];
            $used++;
            $sql = "UPDATE discount SET used = $used WHERE code = '$discount'";
            $conn->query($sql);
        }else{
            $out['success']=false;
            $out['message']='این کد معتبر نیست';
        }
    }
    else{
        $out['success']=false;
        $out['message']='این کد معتبر نیست';

    }
    echo json_encode($out);

}catch (Exception $e){
    $out['success']=false;
    $out['message']='خطا در برقراری ارتباط';
    $out['exeption']=$e->getMessage();
    echo json_encode($out);


}