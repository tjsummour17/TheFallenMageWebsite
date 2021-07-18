<?php
include('con.php');
include('const.php');
header('Content-Type: application/json');

class Driver
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    private function sendNotificationToDriver($driverFcmToken)
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
            CURLOPT_POSTFIELDS => '{"notification": {"body": "لديك طلب جديد", "title": "Barcode"},"priority": "high","data": {"click_action": "FLUTTER_NOTIFICATION_CLICK","id": "108466965866","status": "done"},"to":"' . $driverFcmToken . '" }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=AAAAvy1i6ao:APA91bEVe4MQ1vUrWqJ7EyQ5FlLnEyKK4Gtj-dIBljXDQxvvAVBDdB3PfpTj5ERIrnrZYYqg0Iifj9bdWKspPjMj1KRM-NJmbGBv6pNU_Hl0HwuUizOcnmLLBNbjiTKV-hJ5sv1YH8Ly',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public function assign_item($itemId, $driverId, $shipmentCharges, $driverRatio, $token)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$token'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
        } else {
            $query = "SELECT * FROM drivers WHERE id = $driverId AND (drivers.approved = 1 AND drivers.is_archived = 0)";
            $result = mysqli_query($this->connection, $query);
            if ($result != 1) {
                header("HTTP/1.1 304 Not Modified");
            } else {
                $query = "SELECT * FROM `items` WHERE id = $itemId";
                $isValid = mysqli_query($this->connection, $query);
                if (mysqli_num_rows($isValid) == 1) {
                    $query = "UPDATE `items` SET shipmentCharges = $shipmentCharges, driverId = $driverId, driverRatio = $driverRatio WHERE `id` = $itemId";
                    $is_inserted = mysqli_query($this->connection, $query);
                    if ($is_inserted == 1) {
                        try {
                            $this->sendNotificationToDriver((mysqli_fetch_all($result, MYSQLI_ASSOC))[0]['token']);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                        header("HTTP/1.1 200 OK");
                    } else {
                        header("HTTP/1.1 503 Service Unavailable");
                    }
                } else
                    header("HTTP/1.1 304 Not Modified");
            }
        }
        mysqli_close($this->connection);
        exit;
    }
}

$driver = new Driver();
$token = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $token = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['driverId'], $data['itemId'], $data['driverRatio']) && $token) {
    $driverId = $data['driverId'];
    $itemId = $data['itemId'];
    $shipmentCharges = $data['shipmentCharges'];
    $driverRatio = $data['driverRatio'];
    if (!empty($driverId) && !empty($itemId)) {
        $driver->assign_item($itemId, $driverId, $shipmentCharges, $driverRatio, $token);
    } else {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
