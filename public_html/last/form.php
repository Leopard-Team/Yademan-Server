<?php
$servername = "localhost";
$dbUsername = "id554796_leopard";
$dbPassword = "m.emami1391";
$dbName = "id554796_database1";
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
try {
    if (isset($_POST['TYPE']) && isset($_POST['USER']) && isset($_POST['FAMILY']) && isset($_POST['NAME'])) {
        $type = $_POST['TYPE'];
        $username = $_POST['USER'];
        $familyName = $_POST['FAMILY'];
        $name = $_POST['NAME'];
        if ($type == "adviser") {
            $sql = "UPDATE Advisors SET name='$name',familyName='$familyName' WHERE username='$username'";
            if ($conn->query($sql))
                echo "Success";
            else
                echo "query unsuccessful";
        } else if ($type == "student") {
            if (isset($_POST['FIELD'])) {
                $field = $_POST['FIELD'];
                $sql = "UPDATE androidlogin SET Field='$field',familyName='$familyName',name='$name' WHERE username='$username'";
                if ($conn->query($sql))
                    echo "Success";
                else
                    echo "query unsuccessful";
            } else {
                echo "field not sent";
            }
        } else {
            echo "type is not ok";
        }
    } else {
        echo "not set";
    }
} catch (Exception $e) {
    echo "Error ", $e->getMessage();
}
$conn->close();
?>