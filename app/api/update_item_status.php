<?php
include('con.php');
header('Content-Type: application/json');
class UpdateItem
{
    private $db;
    private $connection;
    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }
    private function sendNotificationToCompany($companyFcmToken, $orderNum)
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
            CURLOPT_POSTFIELDS => '{"notification": {"body": "تم تغير حالة الطلب رقم '.$orderNum.'", "title": "Barcode"},"priority": "high","data": {"click_action": "FLUTTER_NOTIFICATION_CLICK","id": "108466965866","status": "done"},"to":"'. $companyFcmToken .'" }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=AAAAvy1i6ao:APA91bEVe4MQ1vUrWqJ7EyQ5FlLnEyKK4Gtj-dIBljXDQxvvAVBDdB3PfpTj5ERIrnrZYYqg0Iifj9bdWKspPjMj1KRM-NJmbGBv6pNU_Hl0HwuUizOcnmLLBNbjiTKV-hJ5sv1YH8Ly',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public function item_update($itemId, $status, $driverNote , $auth)
    {
        $query = "SELECT * FROM drivers WHERE login_token='$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            $json['status'] = 400;
            $json['message'] = 'Unauthrized';
        } else {
            $query = "UPDATE `items` SET `status`= $status, `driverNotes` = '$driverNote' WHERE `id` = $itemId";
            $is_inserted = mysqli_query($this->connection, $query);
            if ($is_inserted == 1) {
                $json['status'] = 200;
                $json['message'] = $itemId . '\'s item Updated.';
                $query = "SELECT companies.token FROM items, companies WHERE items.companyId = companies.id AND items.id = $itemId";
                $result = mysqli_query($this->connection, $query);
                if (mysqli_num_rows($result) == 1) {
                    $this->sendNotificationToCompany((mysqli_fetch_all($result, MYSQLI_ASSOC))[0]['token'],$itemId);
                }
            } else {
                $json['status'] = 401;
                $json['message'] = 'Something wrong';
            }
        }
        echo json_encode($json);
        mysqli_close($this->connection);
    }
}
$item = new UpdateItem();
$auth = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['id'], $data['driverNote'], $data['status']) && !empty($auth)) {
    $id = $data['id'];
    $status = $data['status'];
    $driverNote = $data['driverNote'];
    if (!empty($id) && !empty($status) && !empty($driverNote) && !empty($auth)) {
        $item->item_update($id, $status, $driverNote , $auth);
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
}
