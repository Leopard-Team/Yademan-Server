<?php


$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$tablename = "QuizQuestions";
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['hasInput'])) {
    $input = $_POST["hasInput"];
    if ($input == 1) {
        $sql = "SELECT * FROM $tablename WHERE id = " . $_POST["id0"];
        for ($i = 1; $i < 5; $i++)
            $sql .= " OR id =" . $_POST["id" . $i];
        $question["status"] = "OLD";
    } else if ($input == 2) {
        $question["status"] = "NEW";
        $sql = "SELECT * FROM $tablename ORDER BY RAND() LIMIT 5";
    }
    if (($result = $conn->query($sql)) && $size = $result->num_rows > 0) {
        $result = $result->fetch_all();
        $array = array();
        foreach ($result as $r) {
            $question["id"] = $r[0];
            $question["text"] = $r[1];
            $question["opp1"] = $r[2];
            $question["opp2"] = $r[3];
            $question["opp3"] = $r[4];
            $question["opp4"] = $r[5];
            $question["answer"] = $r[6];
            array_push($array, $question);
        }
        echo json_encode($array, JSON_PRETTY_PRINT);
    } else
        echo "Result 0";
} else {
    echo "Failed";
}