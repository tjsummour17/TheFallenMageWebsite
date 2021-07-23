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

    public function update_driver($auth, $driverId, $orderIds, $selectAll)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            if ($selectAll == 1) {
                $query = "DELETE FROM `driverUnpaidRequests` WHERE `companyId` = $driverId";
                mysqli_query($this->connection, $query);
            } else {
                $query = "SELECT * FROM `drivers` WHERE id = $driverId";
                $result = mysqli_query($this->connection, $query);
                if ($result == 1) {
                    print_r($orderIds);
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
    if (isset($data['companyId'], $data['orderIds'], $data['selectAll'])) {
        $companyId = $data['companyId'];
        $orderIds = $data['orderIds'];
        $selectAll = $data['selectAll'];
        $newDriver->update_driver($auth, $companyId, $orderIds, $selectAll);
    } else {
        header("HTTP/1.1 403 Bad Request");
    }
} else
    header("HTTP/1.1 403 Bad Request");