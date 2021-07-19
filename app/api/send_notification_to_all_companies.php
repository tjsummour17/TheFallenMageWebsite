<?php

include('con.php');
header('Content-Type: application/json');

class SendNotifications
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    private function sendNotificationToCompany($companyFcmToken, $title, $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"notification": {"body": "' . $body . '", "title": "' . $title . '"},"priority": "high","data": {"click_action": "FLUTTER_NOTIFICATION_CLICK","id": "108466965866","status": "done"},"to":"' . $companyFcmToken . '" }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=AAAAvy1i6ao:APA91bEVe4MQ1vUrWqJ7EyQ5FlLnEyKK4Gtj-dIBljXDQxvvAVBDdB3PfpTj5ERIrnrZYYqg0Iifj9bdWKspPjMj1KRM-NJmbGBv6pNU_Hl0HwuUizOcnmLLBNbjiTKV-hJ5sv1YH8Ly',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public function send($auth, $title, $body)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            mysqli_close($this->connection);
        } else {
            $query = "SELECT `token` FROM `companies`";
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) > 0) {
                $results = mysqli_fetch_all($result, MYSQLI_ASSOC);
                foreach ($results as $value) {
                    $this->sendNotificationToCompany($value['token'], $title, $body);
                }
            }
        }
    }
}

$sendNotifications = new SendNotifications();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['title'], $data['body']) && !empty($headerValue)) {
    $title = $data['title'];
    $body = $data['body'];
    if (!empty($title) && !empty($body)) {
        $sendNotifications->send($headerValue, $title, $body);
    } else {
        header("HTTP/1.1 404 bad request");
    }
} else
    header("HTTP/1.1 400 bad state");
