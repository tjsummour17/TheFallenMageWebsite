<?php
include('con.php');
header('Content-Type: application/json');

class GetDrivers
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function get_drivers($auth, $driverId)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result1 = mysqli_query($this->connection, $query);
        $query = "SELECT * FROM `drivers` WHERE `login_token` = '$auth'";
        $result2 = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result1) != 1 && mysqli_num_rows($result2) != 1) {
            header("HTTP/1.1 401 Unauthorized");
        } else {
            $query = "SELECT * FROM `driverUnpaidRequests` WHERE `driverId` = $driverId";
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) >= 0) {
                echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            } else {
                header("HTTP/1.1 503 Service Unavailable");
            }
        }
        mysqli_close($this->connection);
    }
}

$getItems = new GetDrivers();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
if (!empty($headerValue)) {
    if (isset($_GET['driverId']))
        $getItems->get_drivers($headerValue, $_GET['driverId']);
    else
        $getItems->get_drivers($headerValue);
}
else
    header("HTTP/1.1 401 Unauthorized");
