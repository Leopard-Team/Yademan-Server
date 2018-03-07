<?php
session_start();
/**
 * Add student cards to table
 * Created by PhpStorm.
 * User: mahdi
 * Date: 7/24/2017
 * Time: 10:49 AM
 */
$conn = include "Connection.php";
$csrf = include 'noscrf.php';
/**
 * Handle exception if values are not set
 */
try {
    $username = $_POST['username'];
    $csrf . NoCSRF::check($username, $_POST);
    $size = $_POST['size'];
    $sql = "SELECT id FROM students WHERE user_name='$username'";
    /**
     * Calculate the difference between localTime and serverTime
     */
    $localTime = date_create($_POST['localTime'],
        timezone_open("Asia/Tehran"));
    date_default_timezone_set("Asia/Tehran");
    $currentTime = new DateTime(date('y-m-d h:i:s'), new DateTimeZone("Asia/Tehran"));
    $difference = $localTime->diff($currentTime);//->format("%y-%m-%d %h:%i:%s");

    /**
     * Result of query is StudentId
     */
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $studentId = $row["id"];
        }
        $decode = json_decode($_POST['studentCards'], true);
        $out = array();
        for ($i = 0; $i < $size; $i++) {
            /**
             * Fill the output array with values of each StudentCard
             */
            $card_id = $decode["studentCards"][$i]["card_id"];
            $last_ans = date_create($decode["studentCards"][$i]["lastAns"], timezone_open("Asia/Tehran"));
            $last_ans = $last_ans->add($difference)->format("y-m-d h:i:s");
            $level = $decode["studentCards"][$i]["level"];
            $local_id = $decode["studentCards"][$i]["local_id"];
            $sql = "INSERT INTO studentCards (card_id,last_ans,levels,student_id)
                              VALUES ('$card_id','$last_ans','$level','$studentId');";
            if ($conn->query($sql)) {
                $lastId = $conn->insert_id;
                $out[$i] = new Identification($lastId, $local_id);
            } else {
                $sql = "SELECT id from studentCards WHERE card_id ='$card_id' AND student_id='$studentId'";
                $res = $conn->query($sql);
                if ($res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $id = $row['id'];
                    $sql = "UPDATE studentCards set levels='$level',last_ans ='$last_ans' WHERE id ='$id'";

                    if ($conn->query($sql)) {
                        $out[$i] = new Identification($id, $local_id);
                    }
                }
            }
        }

        /**
         * Sends the StudentCards as a json
         */
        echo json_encode($out);
    }

} catch (Exception $e) {
    echo $e->getMessage();
}

/**
 * Class Identification
 */
class Identification
{
    var $serverId;
    var $localId;

    /**
     * Identification constructor. Create unique id for each StudentCard
     * @param $serverId
     * @param $localId
     */
    function __construct($serverId, $localId)
    {
        $this->serverId = $serverId;
        $this->localId = $localId;

    }
}
