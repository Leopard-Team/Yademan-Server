<?php
session_start();
$conn = include 'Connection.php';
$csrf=include 'noscrf.php';
try {

    $user = $_POST['username'];
    $csrf.NoCSRF::check($user,$_POST);
    $budget = $_POST['budget'];
    $sql = "SELECT id FROM students WHERE user_name ='$user'";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id = $row['id'];
        $sql = "UPDATE students set budget = '$budget'WHERE id ='$id'";
        if ($conn->query($sql)) {
            echo 'success';
        } else
            echo 'false';

    }
} catch (Exception $e) {
    echo $e->getMessage();
}