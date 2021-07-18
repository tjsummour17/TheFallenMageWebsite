<?php

include('con.php');
header('Content-Type: application/json');

class UpdateDriver
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function update_driver($auth, $driverId, $orderIds, $paymentValue, $selectAll)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            if ($selectAll == 1) {
                $query = "DELETE FROM `driverUnpaidRequests` WHERE `driverId` = $driverId";
                $is_inserted = mysqli_query($this->connection, $query);
                if ($is_inserted == 1) {
                    $query = "UPDATE `drivers` SET `wallet` = 0.0, `receipts` = 0.0 WHERE `id` = $driverId";
                    $is_inserted = mysqli_query($this->connection, $query);
                    if ($is_inserted != 1) {
                        header("HTTP/1.1 500 Internal Server Error");
                    }
                }
            } else {
                $query = "SELECT * FROM `drivers` WHERE id = $driverId";
                $result = mysqli_query($this->connection, $query);
                $driver = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
                if ($driver['wallet'] >= $paymentValue) {
                    $newWallet = $driver['wallet'] - $paymentValue;
                    $query = "UPDATE `drivers` SET `wallet` = " . $newWallet . ", `receipts` = 0.0 WHERE `id` = $driverId";
                    $is_inserted = mysqli_query($this->connection, $query);
                    if ($is_inserted != 1) {
                        header("HTTP/1.1 500 Internal Server Error");
                    }
                    foreach ($orderIds as $orderId) {
                        $query = "DELETE FROM `driverUnpaidRequests` WHERE `driverId` = $driverId AND `id` = $orderId";
                        mysqli_query($this->connection, $query);
                    }
                }
            }
        }
        mysqli_close($this->connection);
    }
}

$newDriver = new UpdateDriver();
$auth = '';
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (!empty($auth)) {
    if (isset($data['driverId'], $data['orderIds'], $data['selectAll'])) {
        $driverId = $data['driverId'];
        $orderIds = $data['orderIds'];
        $selectAll = $data['selectAll'];
        $paymentValue = $data['paymentValue'];
        $newDriver->update_driver($auth, $driverId, $orderIds, $paymentValue, $selectAll);
    } else {
        header("HTTP/1.1 403 Bad Request");
    }
} else
    header("HTTP/1.1 403 Bad Request");