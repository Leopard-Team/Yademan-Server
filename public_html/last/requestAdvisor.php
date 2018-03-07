<?php
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
try {
    if (isset($_POST['UAdvisor']) && isset($_POST['ReqText']) && isset($_POST['UUser'])){
        $usernamea = $_POST['UAdvisor'];
        $rtext = $_POST['ReqText'];
        $usernameu = $_POST['UUser'];
        if (isUserExisted($usernamea, NULL, $conn, "advisor")) {
            $sql = "INSERT INTO Requests (advisor,text,student) VALUES ('$usernamea','$rtext','$usernameu')";
            if ($conn->query($sql))
                echo "Success";
            else
                echo "Failed";
        }
        else
            echo "AdvisorNotFound";
    } else {
        echo "NOT SET";
    }
}catch (Exception $e){
    echo "ERROE";
}

$conn->close();

function isUserExisted($username = NULL, $email = NULL , $conn, $type)
{
    if ($type == "advisor")
        $tableName = "Advisors";
    else
        $tableName = "androidlogin";
    if($username != NULL) {
        $stmt = $conn->prepare("SELECT username from $tableName WHERE username = ?");
        $stmt->bind_param("s", $username);
    }
    if($email != NULL){
        $stmt = $conn->prepare("SELECT username from $tableName WHERE email = ?");
        $stmt->bind_param("s", $email);
    }
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}
?>