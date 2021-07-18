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

    public function get_driver($token)
    {
        $query = "SELECT * FROM `drivers` WHERE `login_token` = '$token'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) == 1) {
            $user = array();
            while ($r = mysqli_fetch_assoc($result)) {
                $user = $r;
            }
            $data = array(
                "id" => $user['id'],
                "name" => $user['name'],
                "phone" => $user['phone'],
                "email" => $user['email'],
                "imageUrl" => $user['imageUrl'],
                "carType" => $user['carType'],
                "carModel" => $user['carModel'],
                "city" => $user['city'],
                "token" => $user['token'],
                "wallet" => $user['wallet'],
                "receipts" => $user['receipts'],
                "approved" => (int)$user['approved'],
                "is_archived" => (int)$user['is_archived']
            );
            echo json_encode($data);
        } else {
            header("HTTP/1.1 401 Unauthorized");
        }
        mysqli_close($this->connection);
    }
}

$driver = new Driver();
$auth = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
if (!empty($auth)) {
    $driver->get_driver($auth);
} else {
    header("HTTP/1.1 401 Unauthorized");
}
