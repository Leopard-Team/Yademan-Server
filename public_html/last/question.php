<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 3/2/2017
 * Time: 6:26 PM
 */
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
try {
    if (isset($_POST['QText']) && isset($_POST['Topic']) && isset($_POST['AdvisorUser']) && isset($_POST['StudentUser'])){
        $usernamea = $_POST['AdvisorUser'];
        $topic = $_POST['Topic'];
        $qtext = $_POST['QText'];
        $usernames =$_POST['StudentUser'];
        $adata = '';
        $atext ='';

        $sql = "INSERT INTO questions (advisor,topic,questiontext,questiondate,student,answertext,answerdate) VALUES ('$usernamea','$topic','$qtext',current_date,'$usernames','$atext','$adata')";
        if ($conn->query($sql))
            echo "Success";
        else
            echo $conn->error;



    } else {
        echo "NOT SET";
    }
}catch (Exception $e){
    echo "ERROE";
}

$conn->close();
