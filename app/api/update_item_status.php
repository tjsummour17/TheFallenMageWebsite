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
            CURLOPT_POSTFIELDS => '{"notification": {"body": "تم تغير حالة الطلب رقم ' . $orderNum . '", "title": "Barcode"},"priority": "high","data": {"click_action": "FLUTTER_NOTIFICATION_CLICK","id": "108466965866","status": "done"},"to":"' . $companyFcmToken . '" }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=AAAAvy1i6ao:APA91bEVe4MQ1vUrWqJ7EyQ5FlLnEyKK4Gtj-dIBljXDQxvvAVBDdB3PfpTj5ERIrnrZYYqg0Iifj9bdWKspPjMj1KRM-NJmbGBv6pNU_Hl0HwuUizOcnmLLBNbjiTKV-hJ5sv1YH8Ly',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public function item_update($itemId, $status, $driverNote, $auth)
    {
        $query = "SELECT * FROM drivers WHERE login_token='$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthrized");
        } else {
            $query = "UPDATE `items` SET `status`= $status, `driverNotes` = '$driverNote' WHERE `id` = $itemId";
            $is_inserted = mysqli_query($this->connection, $query);
            if ($is_inserted == 1) {
                $query = "SELECT companies.token, companies.wallet, companies.canceledRequests FROM items, companies WHERE items.companyId = companies.id AND items.id = $itemId";
                $result = mysqli_query($this->connection, $query);
                $company = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
                if (mysqli_num_rows($result) == 1) {
                    $this->sendNotificationToCompany($company['token'], $itemId);
                    $query = "SELECT * FROM `items` WHERE items.id = $itemId";
                    $result = mysqli_query($this->connection, $query);
                    if (mysqli_num_rows($result) > 0) {
                        $item = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
                        if ($status == 2) {
                            $newWalletValue = ($item['price'] * $item['quantity']) + $company['wallet'];
                            $query = "UPDATE `companies` SET `wallet`=" . $newWalletValue . " WHERE `id`= " . $item['companyId'];
                            $is_inserted = mysqli_query($this->connection, $query);
                            if ($is_inserted == 1) echo "company no. " . $item['companyId'] . " wallet has been updated";
                        } else
                            if ($status >= 3 && $status != 4) {
                                $query = "INSERT INTO `driverUnpaidRequests`(`id`, `name`, `price`, `quantity`, `status`, `images`, `receiveLocation`, `deliveryLocation`, `customerName`, `customerPhone`, `companyId`, `shipmentCharges`, `driverId`, `moneyWithDriver`, `driverRatio`, `driverNotes`) VALUES (NULL, '" . $item['name'] . "', " . $item['price'] . ", " . $item['quantity'] . ", " . $item['status'] . ", '" . $item['images'] . "', '" . $item['receiveLocation'] . "', '" . $item['deliveryLocation'] . "', '" . $item['customerName'] . "', '" . $item['customerPhone'] . "', " . $item['companyId'] . ", " . $item['shipmentCharges'] . ", " . $item['driverId'] . ", " . $item['moneyWithDriver'] . ", " . $item['driverRatio'] . ", '" . $item['driverNotes'] . "')";
                                $is_inserted1 = mysqli_query($this->connection, $query);
                                if ($is_inserted1 == 1) echo "Order has been added to Driver Unpaid Requests ";
//                        echo("<script>console.log('PHP: " . $query . "');</script>");
                                $query = "INSERT INTO `companyUnpaidOrders`(`id`, `name`, `description`, `price`, `quantity`, `status`, `images`, `receiveLocation`, `deliveryLocation`, `customerName`, `customerPhone`, `companyId`, `shipmentCharges`, `driverId`, `moneyWithDriver`, `driverRatio`, `driverNotes`) VALUES (NULL, '" . $item['name'] . "', '" . $item['description'] . "', " . $item['price'] . ", " . $item['quantity'] . ", " . $item['status'] . ", '" . $item['images'] . "', '" . $item['receiveLocation'] . "', '" . $item['deliveryLocation'] . "', '" . $item['customerName'] . "', '" . $item['customerPhone'] . "', " . $item['companyId'] . ", " . $item['shipmentCharges'] . ", " . $item['driverId'] . ", " . $item['moneyWithDriver'] . ", " . $item['driverRatio'] . ", '" . $item['driverNotes'] . "')";
                                $is_inserted2 = mysqli_query($this->connection, $query);
                                if ($is_inserted2 == 1) echo "Order has been added to Company Unpaid Requests ";
                                $query = "INSERT INTO `archivedOrders`(`id`, `name`, `description`, `price`, `quantity`, `status`, `images`, `receiveLocation`, `deliveryLocation`, `customerName`, `customerPhone`, `companyId`, `shipmentCharges`, `driverId`, `moneyWithDriver`, `driverRatio`, `driverNotes`) VALUES (NULL, '" . $item['name'] . "', '" . $item['description'] . "', " . $item['price'] . ", " . $item['quantity'] . ", " . $item['status'] . ", '" . $item['images'] . "', '" . $item['receiveLocation'] . "', '" . $item['deliveryLocation'] . "', '" . $item['customerName'] . "', '" . $item['customerPhone'] . "', " . $item['companyId'] . ", " . $item['shipmentCharges'] . ", " . $item['driverId'] . ", " . $item['moneyWithDriver'] . ", " . $item['driverRatio'] . ", '" . $item['driverNotes'] . "')";
                                $is_inserted3 = mysqli_query($this->connection, $query);
                                if ($is_inserted3 == 1) echo "Order has been added to Archived Orders ";
                                $query = "DELETE FROM `items` WHERE `id` = $itemId";
                                $is_inserted4 = mysqli_query($this->connection, $query);
                                if ($is_inserted4 == 1) echo "Order has been removed from items ";
                                if ($status == 6) {
                                    $canceledRequests = $company['canceledRequests'] + ($item['shipmentCharges'] * $item['driverRatio']) + ($item['price'] * $item['quantity']);
                                    $query = "UPDATE `companies` SET `canceledRequests`=" . $canceledRequests . " WHERE `id`= " . $item['companyId'];
                                    $is_inserted = mysqli_query($this->connection, $query);
                                    if ($is_inserted == 1) echo "company no. " . $item['companyId'] . " canceledRequests has been updated ";
                                } else
                                    if ($status == 5) {
                                        $canceledRequests = $company['canceledRequests'] + ($item['price'] * $item['quantity']);
                                        $query = "UPDATE `companies` SET `canceledRequests`=" . $canceledRequests . " WHERE `id`= " . $item['companyId'];
                                        $is_inserted = mysqli_query($this->connection, $query);
                                        if ($is_inserted == 1) echo "company no. " . $item['companyId'] . " canceledRequests has been updated ";
                                    }
                            }
                    }
                }
            } else {
                header("HTTP/1.1 503 Service Unavailable");
            }
        }
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
    if (!empty($id) && !empty($status) && !empty($driverNote)) {
        $item->item_update($id, $status, $driverNote, $auth);
    } else {
        header("HTTP/1.1 403 Bad Request");
    }
} else
    header("HTTP/1.1 403 Bad Request");
