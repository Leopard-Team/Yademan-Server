<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2/23/2017
 * Time: 11:27 AM
 */
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$tablename = "Competitions";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//SELECT * FROM `Competitions` WHERE 1
if(isset($_POST['USERNAME'])) {
    $username = $_POST['USERNAME'];
    $sql = "SELECT * FROM $tablename WHERE userName1='$username' OR userName2 = '$username'";
    if (!($result = $conn->query($sql)))
        die($conn->error);
    if ($result->num_rows) {
        $i = 0;
        $competitions = null;
        while ($row = $result->fetch_assoc()) {
            $competitions[$i] = $row;
            $i++;
        }
        echo json_encode(array("Competitions" => $competitions));
    }
}else
    echo "Failed";
