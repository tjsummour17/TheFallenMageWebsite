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

    public function save_order_amount($itemId, $amount, $token)
    {
        $query = "SELECT * FROM `drivers` WHERE `login_token` = '$token' AND (drivers.approved = 1 AND drivers.is_archived = 0)";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
        } else {
            $query = "UPDATE `items` SET moneyWithDriver = $amount WHERE `id` = $itemId";
            $is_inserted = mysqli_query($this->connection, $query);
            if ($is_inserted == 1) {
                header("HTTP/1.1 200 OK");
            } else {
                header("HTTP/1.1 503 Service Unavailable");
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
if (isset($data['itemId'], $data['amount']) && $token) {
    $itemId = $data['itemId'];
    $amount = $data['amount'];
    if (!empty($amount) && !empty($itemId)) {
        $driver->save_order_amount($itemId, $amount, $token);
    } else {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
