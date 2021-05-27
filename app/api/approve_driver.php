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

    public function approve_driver($driverId, $token)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$token'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            mysqli_close($this->connection);
            exit;
        } else {
            $query = "UPDATE `drivers` SET `approved`=1 , `is_archived`=0 WHERE `id` = $driverId;";
            $is_inserted = mysqli_query($this->connection, $query);
            if ($is_inserted == 1) {
                header("HTTP/1.1 200 OK");
                mysqli_close($this->connection);
                exit;
            } else {
                header("HTTP/1.1 503 Service Unavailable");
                mysqli_close($this->connection);
                exit;
            }
        }
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
    $driverId = $data['driverId'];
    if (!empty($driverId) && !empty($token)) {
        $driver->approve_driver($driverId, $token);
    } else {
        header("HTTP/1.1 400 Bad Request");
        mysqli_close($this->connection);
        exit;
    }
}
