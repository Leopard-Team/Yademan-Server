<?php
session_start();
/**
 * Update a StudentCard
 * Created by PhpStorm.
 * User: TheMn
 * Date: 7/24/2017
 * Time: 10:27 PM
 */
$conn = include 'Connection.php';
$csrf= include "noscrf.php";
$tableName = 'studentCards';
/**
 * Handle exception if values are not set
 */
try {
    $user=$_POST['user'];
    $csrf.NoCSRF::check($user,$_POST);
    $identification = $_POST["serverId"];
    $lastAnswer = date_create($_POST["lastAns"], timezone_open("Asia/Tehran"));
    $level = $_POST["level"];
    /**
     * Calculate the difference between localTime and serverTime
     */
    $localTime = date_create($_POST['localTime'], timezone_open("Asia/Tehran"));
    date_default_timezone_set("Asia/Tehran");
    $serverTime = new DateTime(date('y-m-d h:i:s'), new DateTimeZone("Asia/Tehran"));
    $difference = $localTime->diff($serverTime);
    $lastAnswer = $lastAnswer->add($difference)->format("y-m-d h:i:s");
    $sql = "UPDATE $tableName SET levels = $level, last_ans = '$lastAnswer' WHERE id = $identification";
    /**
     * The row of this studentCard will be updated as result of query
     */
    if ($conn->query($sql)) {
        echo "success";
    } else
        echo $conn->error;
} catch (Exception $e) {
    echo $e->getMessage();
}