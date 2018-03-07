<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2/23/2017
 * Time: 9:13 PM
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
//isset($_POST['Field']) && isset($_POST['uScore1']) && isset($_POST['score'])
if (isset($_POST['uName1']) && isset($_POST['uName2']) && isset($_POST['State'])) {
    $uName1 = $_POST['uName1'];
    $uName2 = $_POST['uName2'];
    $state = $_POST['State'];
    $field = $_POST['Field'];
    if ($state == -1) {//Competition Started
        if (!isUserExisted($uName2, $conn))
            echo "Not Exists In Database";
        else if ($uName2 == $uName1)
            echo "You Can't Compete With Yourself";
        else {
            $uScore1 = $_POST['uScore1'];
            $score = $_POST['score'];
            $idQuestions = $_POST['idz'];
            $sql = "INSERT INTO $tablename (userName1,userName2,uScore1,score,state,field,idQuestions) VALUES ('$uName1','$uName2', $uScore1,'$score' ,$state ,'$field' ,'$idQuestions')";
            if ($conn->query($sql))
                echo "Competition Started";
            else
                echo $conn->error;
        }
    } else if ($state == 0) {//Competition Accepted
        $sql = "UPDATE $tablename SET state='$state' WHERE (userName1='$uName1' AND userName2='$uName2') ";
        if ($conn->query($sql))
            echo "Competition Accepted";
        else
            echo $conn->error;
    } else if ($state == 1) {//Competition Finished
        if (!isset($_POST['uScore2']))
            echo "Not Set";
        else {
            $uScore2 = $_POST['uScore2'];
            $sql = "UPDATE $tablename SET uScore2='$uScore2',state='$state' WHERE (userName1='$uName1' AND userName2='$uName2' AND field='$field')";
            if ($conn->query($sql))
                echo "Competition Finished";
            else
                echo $conn->error;
        }
    }
} else {
    echo "Not Set";
}
$conn->close();
function isUserExisted($userName, $conn)
{
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM androidlogin WHERE username='$userName'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
