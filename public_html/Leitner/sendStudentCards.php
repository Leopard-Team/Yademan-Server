<?php
session_start();
$connection = include 'Connection.php';
$csrf= include "noscrf.php";

if ($connection->connect_error) {
    die ("Connection Failed" . $connection->connect_error);
}
try {
    $localTime;
    /**
     * Handle exception if values are not set
     */
    /**
     * Calculate the difference between localTime and serverTime
     */
    $username = $_POST['Student'];
    $csrf.NoCSRF::check($username,$_POST);
    $localTime = date_create($_POST['localTime'], timezone_open("Asia/Tehran"));
    date_default_timezone_set("Asia/Tehran");
    $currentTime = new DateTime(date('y-m-d h:i:s'), new DateTimeZone("Asia/Tehran"));
    $difference = $currentTime->diff($localTime);

    $sql = "SELECT id FROM students WHERE user_name='$username'";
    /**
     * Result of query is StudentId
     */
    $res = $connection->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $studentId = $row["id"];

        $sql = "SELECT * FROM studentCards WHERE student_id = '$studentId' ";
        /**
         * Result of query is StudentCards of this student
         */
        $res = $connection->query($sql);
        if ($res->num_rows > 0) {
            $cards = array();
            $i = 0;
            while ($row = $res->fetch_assoc()) {
                /**
                 * Create new Student card with values of each row and
                 * add it to output cards
                 */
                $id = $row["id"];
                $card_id = $row["card_id"];
                $last_ans = date_create($row["last_ans"], timezone_open("Asia/Tehran"));
                $last_ans = $last_ans->add($difference)->format("Y-m-d h:i:s");
                $level = $row["levels"];
                $student_id = $row["student_id"];
                $cards[$i++] = new StudentCard($id, $card_id, $last_ans, $level, $student_id);
            }

            /**
             * Sends the array of studentCards of this student
             * as a json to client
             */
            echo json_encode(array_values($cards));
        }
    }
} catch (Exception $e) {
    echo  $e->getMessage();
}

/**
 * Class StudentCard
 */
class StudentCard
{
    var $id;
    var $card_id;
    var $last_ans;
    var $level;
    var $student_id;

    /**
     * StudentCard constructor. Create new studentCard with values
     * @param $id
     * @param $card_id
     * @param $last_ans
     * @param $level
     * @param $student_id
     */
    function __construct($id, $card_id, $last_ans, $level, $student_id)
    {
        $this->id = $id;
        $this->card_id = $card_id;
        $this->last_ans = $last_ans;
        $this->level = $level;
        $this->student_id = $student_id;
    }
}

