<?php
$serverName = "localhost";
$dbUsername = "id554796_leopard";
$dbPassword = "m.emami1391";
$dbName = "id554796_database1";
$tableName = "androidlogin";

$conn = new mysqli($serverName, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $sqlTopMembers = "SELECT XP, username, email, FIND_IN_SET( XP, (
SELECT GROUP_CONCAT( XP
ORDER BY XP DESC ) 
FROM $tableName )
) AS rank FROM $tableName ORDER BY XP DESC LIMIT 10";
    if (!($result = $conn->query($sqlTopMembers)))
        die($conn->error);
    if ($result->num_rows) {
        $response['rankings'] = [];
        while ($row = $result->fetch_assoc()) {
            array_push($response['rankings'], $row);
        }
        echo json_encode($response);
    }
} catch (Exception $e) {
    echo "Error ", $e->getMessage();
}