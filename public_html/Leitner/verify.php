<?php
$conn = include "Connection.php";
$tableName = 'students';

try{
    $email = $_GET['email'];
    $hash = $_GET['hash'];
    $sql = "SELECT user_name FROM $tableName WHERE email = '$email' AND isVerified = '0' AND hash = '$hash'";
    $res = $conn->query($sql);
    if($res && $res->num_rows > 0){
        $update = "UPDATE $tableName SET isVerified = '1' WHERE email ='$email'";
        if($conn->query($update)){
            $ch = curl_init('http://leopard.000webhostapp.com/Leitner/Html/verifyPage.html');
            curl_exec($ch);
        }
    }
} catch(Exception $e) {
    echo $e->getMessage();
}