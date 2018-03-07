<?php
session_start();

/**
 * Users can delete their account
 */
$conn = include "Connection.php";
$csrf= include "noscrf.php";

/**
 * Handle exception if values are not set
 */
try {
    $isOkey = false;
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $csrf . NoCSRF::check($user, $_POST);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    if (getUserByUserAndPassword($user, $pass, $conn)) {
        $sql = "SELECT id FROM students WHERE user_name='$user'";
        if ($conn->query($sql) && $conn->query($sql)->num_rows > 0) {
            $res = $conn->query($sql);
            $row = $res->fetch_assoc();
            $id = $row['id'];
            $sql = "DELETE FROM studentCards WHERE student_id='$id'";
            if ($conn->query($sql)) {
                $isOkey = true;
            } else
                echo $conn->error;
            $sql = "DELETE FROM solds WHERE student_id='$id'";
            if ($conn->query($sql)) {
                $sql = "DELETE FROM students WHERE user_name = '$user'";
                $isOkey = true;
            } else
                echo $conn->error;
        } else
            $conn->error;

        /**
         * The row of this student will be deleted as result of query
         */
        if ($conn->query($sql) && $isOkey)
            echo "success";
        else {
            echo $conn->error;
        }
    } else echo "رمز عبور نامعتبر است";
}catch (Exception $e){
    echo $e->getMessage();
}

/**
 * This method will get the user with unique specifications
 * @param $user
 * @param $password
 * @param $conn
 * @return null as there is not user
 */
function getUserByUserAndPassword($user, $password, $conn)
{
    $tableName = "students";
    $sql = "SELECT * FROM $tableName WHERE user_name='$user' ";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $u['user_name'] = $row['user_name'];
        $u['password'] = $row['password'];
        $u['salt'] = $row['salt'];
        $u['email'] = $row['email'];
        $u['isVerified'] = $row['isVerified'];
        $salt = $u['salt'];
        if (trim($password . $salt) == trim(encrypt::decode($u['password'], $salt))) {
            return true;
        }
        return false;
    } else {
        return false;
    }
}

class encrypt
{
    /**
     * This method will encode the password
     * @param $pure_string
     * @param $encryption_key
     * @return string the result
     */
    public static function encode($pure_string, $encryption_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))), utf8_encode(trim($pure_string)), MCRYPT_MODE_ECB, $iv);
        return base64_encode($encrypted_string);
    }

    /**
     * this method will decode the password
     * @param $encrypted_string
     * @param $encryption_key
     * @return string the result
     */
    public static function decode($encrypted_string, $encryption_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))), base64_decode(trim($encrypted_string)), MCRYPT_MODE_ECB, $iv);
        return $decrypted_string;
    }

}