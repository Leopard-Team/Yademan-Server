<?php
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$tablename = "androidlogin";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Failed");
}
$sql = "SELECT * FROM $tablename";
if ($result = $conn->query($sql)) {
    $i = 0;
    $arr = null;
    while ($row = $result->fetch_assoc()){
        $arr[$i] = $row["username"];
        $i++;
    }
    echo json_encode($arr);
}
