<?php
session_start();
$conn = include 'Connection.php';
$CSRF = include 'noscrf.php';
try {

    $user = $_POST['user'];
    //$CSRF . NoCSRF::check($user, $_POST);
    $price = (int)$_POST['price'];
    $l_ids =json_decode( $_POST['ids'],true);
    $size=(int)$_POST['size'];
    $sql = "SELECT budget,id FROM students WHERE user_name = '$user'";
    $out = array();
    if ($res = $conn->query($sql)) {
        $row = $res->fetch_assoc();
        $budget = (int)$row['budget'];
        $id = $row['id'];
        if ($price > $budget) {
            $out['success'] = false;
            $out['budget'] = $budget;
            $out['message'] = "اعتبار شما کافی نیست";
        } elseif ($price <= $budget) {
            $diff = $budget - $price;
            $sql = "UPDATE students set budget = '$diff' WHERE user_name='$user'";
            if ($conn->query($sql)) {
                for($i=0;$i<$size;$i++) {
                    $my_l_id = $l_ids[$i];
                    $sql = "INSERT into solds (student_id,lesson_id) VALUES ('$id','$my_l_id')";
                    if ($conn->query($sql)) {

                        $out['success'] = true;
                        $out['budget'] = $budget - $price;
                        $out['message'] = "خرید شما با موفقیت انجام شد";
                    } else {
                        if ($size == 1) {
                            $out['success'] = false;
                            $out['budget'] = $budget;
                            $out['message'] = "شما این درس را قبلا خریداری کردید";
                        }
                    }
                }

            } else {
                $out['success'] = false;
                $out['budget'] = $budget;
                $out['message'] = "خطا در برقراری ارتباط";
            }

        }

    } else {
        $out['success'] = false;
        $out['budget'] = $budget;
        $out['message'] = "cخطا در برقراری ارتباط";
    }
    echo json_encode($out);
} catch (Exception $e) {
    echo $e->getMessage();
}