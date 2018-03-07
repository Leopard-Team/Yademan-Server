<?php

/**
 * Created by PhpStorm.
 * User: TheMn
 * Date: 8/1/2017
 * Time: 2:07 PM
 */
$MailClass = include 'Mail.inc';
$conn = include 'Connection.php';

try {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    if (strpos($email, '@') !== false) {
        $user = getUserByMail($email, $conn);
        if ($user != NULL) {
            $password = str_replace(trim($user['salt']), '', encrypt::decode($user['password'], $user['salt']));
            $MailClass . Mail::forgotPass($email, $user['user_name'], $password);
            echo 'گذرواژه به این ایمیل ارسال شد';
        } else echo 'کاربری با این ایمیل وجود ندارد';
    }else{
        $phone = $email;
        $user = getUserByPhone($phone, $conn);
        if ($user != NULL) {
            $password = str_replace(trim($user['salt']), '', encrypt::decode($user['password'], $user['salt']));
            sendSms($phone, $user['user_name'], $password);
            echo 'گذرواژه به این شماره ارسال شد';
        } else echo 'کاربری با این شماره تلفن وجود ندارد';
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

/**
 * This method will return a user by specified email
 * @param $mail
 * @param $conn
 * @return null if user not exist
 */
function getUserByMail($mail, $conn)
{
    $tableName = "students";
    $sql = "SELECT * FROM $tableName WHERE email='$mail' ";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $u['user_name'] = $row['user_name'];
        $u['password'] = $row['password'];
        $u['salt'] = $row['salt'];
        return $u;
    } else {
        return NULL;
    }
}

function getUserByPhone($phone, $conn)
{
    $tableName = "students";
    $sql = "SELECT * FROM $tableName WHERE phoneNumber='$phone' ";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $u['user_name'] = $row['user_name'];
        $u['password'] = $row['password'];
        $u['salt'] = $row['salt'];
        return $u;
    } else {
        return NULL;
    }
}

function sendSms($phone, $user, $pass){
    class SmsIR_SendMessage
    {
        protected function getAPIMessageSendUrl()
        {
            return "http://RestfulSms.com/api/MessageSend";
        }

        protected function getApiTokenUrl()
        {
            return "http://RestfulSms.com/api/Token";
        }

        public function __construct($APIKey, $SecretKey, $LineNumber)
        {
            $this->APIKey = $APIKey;
            $this->SecretKey = $SecretKey;
            $this->LineNumber = $LineNumber;
        }

        public function SendMessage($MobileNumbers, $Messages, $SendDateTime = '')
        {

            $token = $this->GetToken($this->APIKey, $this->SecretKey);
            if ($token != false) {
                $postData = array(
                    'Messages' => $Messages,
                    'MobileNumbers' => $MobileNumbers,
                    'LineNumber' => $this->LineNumber,
                    'SendDateTime' => $SendDateTime,
                    'CanContinueInCaseOfError' => 'false'
                );

                $url = $this->getAPIMessageSendUrl();
                $SendMessage = $this->execute($postData, $url, $token);
                $object = json_decode($SendMessage);
                if (is_object($object)) {
                    $array = get_object_vars($object);
                    if (is_array($array)) {
                        $result = $array['Message'];
                    } else {
                        $result = false;
                    }
                } else {
                    $result = false;
                }

            } else {
                $result = false;
            }
            return $result;
        }

        private function GetToken()
        {
            $postData = array(
                'UserApiKey' => $this->APIKey,
                'SecretKey' => $this->SecretKey,
                'System' => 'php_rest_v_1_1'
            );
            $postString = json_encode($postData);

            $ch = curl_init($this->getApiTokenUrl());
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POST, count($postString));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result);

            if (is_object($response)) {
                $resultVars = get_object_vars($response);
                if (is_array($resultVars)) {
                    @$IsSuccessful = $resultVars['IsSuccessful'];
                    if ($IsSuccessful == true) {
                        @$TokenKey = $resultVars['TokenKey'];
                        $resp = $TokenKey;
                    } else {
                        $resp = false;
                    }
                }
            }

            return $resp;
        }

        private function execute($postData, $url, $token)
        {

            $postString = json_encode($postData);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-sms-ir-secure-token: ' . $token
            ));
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POST, count($postString));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }
    }

    try {

        date_default_timezone_set("Asia/Tehran");

        $verification_code = mt_rand(10000, 99999);
        // your sms.ir panel configuration
        $APIKey = "331956a09ca5bfe048bb56a0";
        $SecretKey = ")l!e(o9@5*p#a&r%d^";
        $LineNumber = "10009035";

        // your mobile numbers
        $MobileNumbers = array($phone);

        // your text messages
        $Messages = array('سلام دوست عزیز.نام کاربری شما:' . $user . "\n پسورد:". $pass);

        // sending date
        @$SendDateTime = "";

        $SmsIR_SendMessage = new SmsIR_SendMessage($APIKey, $SecretKey, $LineNumber);
        $SendMessage = $SmsIR_SendMessage->SendMessage($MobileNumbers, $Messages, $SendDateTime);
//            var_dump($SendMessage);

    } catch (Exeption $e) {
        echo 'Error SendMessage : ' . $e->getMessage();
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