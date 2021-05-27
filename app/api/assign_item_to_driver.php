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

    public function approve_driver($driverId, $itemId, $token)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$token'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
        } else {
            $query = "SELECT * FROM drivers WHERE id = $driverId AND (drivers.approved = 0 AND drivers.is_archived = 1)";
            $result = mysqli_query($this->connection, $query);
            if ($result == 1) {
                header("HTTP/1.1 304 Not Modified");
            } else {
                $query = "UPDATE `items` SET driverId = $driverId  WHERE `id` = $itemId";
                $is_inserted = mysqli_query($this->connection, $query);
                if ($is_inserted == 1) {
                    header("HTTP/1.1 200 OK");
                } else {
                    header("HTTP/1.1 503 Service Unavailable");
                }
            }
        }
        mysqli_close($this->connection);
        exit;
    }
}

$driver = new Driver();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['driverId']) && $headerValue) {
    $name = $data['driverId'];
    $itemId = $data['itemId'];
    if (!empty($name) && !empty($token)) {
        $driver->assign_item($name, $token);
    } else {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
