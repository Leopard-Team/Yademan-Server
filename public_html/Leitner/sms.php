<?php
$conn = include 'Connection.php';
try {

    $phone_number = mysqli_real_escape_string($conn, $_POST['phone']);
    if (phoneExist($phone_number, $conn)) {
        $result['success'] = false;
        $result['message'] = "این شماره تلفن قبلاً ثبت نام شده است";
    } else {

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
            $result['success'] = true;
            $result['code'] = $verification_code;
            $result['line_number'] = "+9810009035";
            // your sms.ir panel configuration
            $APIKey = "331956a09ca5bfe048bb56a0";
            $SecretKey = ")l!e(o9@5*p#a&r%d^";
            $LineNumber = "10009035";

            // your mobile numbers
            $MobileNumbers = array($phone_number);

            // your text messages
            $Messages = array('سلام دوست عزیز. به اپلیکیشن یادمان خوش اومدی. کد فعالسازی شما:' . $verification_code);

            // sending date
            @$SendDateTime = "";

            $SmsIR_SendMessage = new SmsIR_SendMessage($APIKey, $SecretKey, $LineNumber);
            $SendMessage = $SmsIR_SendMessage->SendMessage($MobileNumbers, $Messages, $SendDateTime);
//            var_dump($SendMessage);

        } catch (Exeption $e) {
            echo 'Error SendMessage : ' . $e->getMessage();
        }
    }

    echo json_encode($result);
} catch (Exception $e) {
    echo $e->getMessage();
}
function phoneExist($phone_number = NULL, $conn)
{
    $tableName = "students";
    if ($phone_number != NULL) {
        $stmt = $conn->prepare("SELECT user_name from $tableName WHERE phoneNumber = ?");
        $stmt->bind_param("s", $phone_number);
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