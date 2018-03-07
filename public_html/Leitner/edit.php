<?php
session_start();
/**
 * Users can change their password
 * Created by PhpStorm.
 * User: TheMn
 * Date: 7/26/2017
 * Time: 10:18 PM
 */
$MailClass = include 'Mail.inc';
$conn = include "Connection.php";
$csrf = include 'noscrf.php';
$tableName = 'students';

/**
 * Handle exception if values are not set
 */
try {
    if (isset($_POST['username']) && isset($_POST['oldPassword']) && isset($_POST['newPassword'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $csrf . NoCSRF::check($username, $_POST);
        $oldPassword = mysqli_real_escape_string($conn, $_POST['oldPassword']);
        $newPassword = mysqli_real_escape_string($conn, $_POST['newPassword']);

        if (isUserExisted($username, NULL, $conn)) {
            $u = getUserByUserAndPassword($username, $oldPassword, $conn);
            /**
             * Accepts the username and password
             */
            if ($u != null) {
                $salt = $u['salt'];
                if ($oldPassword != $newPassword) {
                    $newPassword = encrypt::encode($newPassword . $salt, $salt);
                    /**
                     * Set new password for user
                     */
                    $sql = "UPDATE $tableName SET password = '$newPassword' WHERE user_name = '$username'";
                    if ($conn->query($sql))
                        echo "رمز عبور با موفقیت تغییر کرد";
                    else
                        echo "اشکالی در تغییر رمز رخ داد";
                } else
                    echo "رمز جدید نمی تواند همان رمز سابق باشد";
            } else {
                echo "رمز عبور نامعتبر است";
            }
        } else {
            echo "نام کاربری یا رمز عبور نامعتبر است";
        }
    } elseif (isset($_POST['username']) && isset($_POST['newEmail']) && isset($_POST['password'])) {
        $out = array();
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $email = mysqli_real_escape_string($conn, $_POST['newEmail']);
        $user = mysqli_real_escape_string($conn, $_POST['username']);
        $csrf . NoCSRF::check($user, $_POST);
        if (getUserByUserAndPassword($user, $password, $conn) != NULL) {
            if (!isUserExisted(null, $_POST['newEmail'], $conn)) {
                $hash = md5(rand(0, 1000));
                $sql = "UPDATE students set email = '$email',isVerified='0',hash ='$hash' WHERE  user_name='$user'";
                if ($conn->query($sql)) {
                    $MailClass . Mail::editVerification($email, $hash, $user);
                    echo "ایمیل فعالسازی برایتان ارسال شد";
                }
            } else {
                echo "کاربری با این ایمیل وجود دارد لطفا ایمیلی جدید وارد کنید";
            }
        } else echo "رمز عبور نامعتبر است";
    }
} catch (Exception $e) {
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
            return $u;
        }
        return NULL;
    } else {
        return NULL;
    }
}

/**
 * This method will check if the user exist
 * @param null $username the username that should be checked
 * @param null $email the email that should be checked
 * @param $conn
 * @return bool existence of user
 */
function isUserExisted($username = NULL, $email = NULL, $conn)
{
    $tableName = "students";
    if ($username != NULL) {
        $stmt = $conn->prepare("SELECT user_name from $tableName WHERE user_name = ?");
        $stmt->bind_param("s", $username);
    }
    if ($email != NULL) {
        $stmt = $conn->prepare("SELECT user_name from $tableName WHERE email = ?");
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
