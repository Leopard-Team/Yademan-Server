<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 4/3/2017
 * Time: 3:18 PM
 */
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
try{
    if (isset($_POST['AdvisorAnswer']) && isset($_POST['QuestionId'])){
        $adanswer = $_POST['AdvisorAnswer'];
        $questionid = $_POST['QuestionId'];


    $sql = "UPDATE questions  SET answertext = '$adanswer',answerdate= current_date  WHERE id = $questionid";
    if ($conn->query($sql))
        echo "Success";
    else
        echo "Failed";


    } else {
        echo "NOT SET";
    }



} catch (Exception $e){
    echo "ERROE";
}

$conn->close();
?>