<?php
session_start();
$conn = include 'Connection.php';
$csrf= include "noscrf.php";
$budgets = array(1000, 2000, 5000, 10000);
try{
    $user = $_POST['user'];
    $csrf.NoCSRF::check($user,$_POST);
    if ((int)$_POST['add'] <= 3) {
        $add = $budgets[(int)$_POST['add']];
        $sql = "SELECT budget from students WHERE user_name='$user'";
        $out = array();
        if ($res = $conn->query($sql)) {
            $row = $res->fetch_assoc();
            $budget = $row['budget'];
            $budget += $add;
            $sql = "UPDATE students set budget = '$budget' WHERE user_name='$user'";
            if ($conn->query($sql)) {
                $out['success'] = true;
                $out['budget'] = $budget;
            } else {
                $out['success'] = false;
                $out['budget'] = $budget - $add;

            }
            echo json_encode($out);
        } else
            echo $conn->error();
    } else
        echo 'wrong input';
} catch (Exception $e) {
    echo $e->getMessage();
}