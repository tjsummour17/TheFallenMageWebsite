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

    public function get_drivers($auth, $orderBy = 'name')
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
        } else {
            $query = "SELECT `id`, `name`, `phone`, `imageUrl`, `carType`, `carModel`, `city`, `token`, `wallet`, `receipts`, `approved`, `login_token`, `t_update`, `t_create`, `is_archived` FROM `drivers` ORDER BY `$orderBy` ASC";
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
    if (isset($_GET['orderBy']))
        $getItems->get_drivers($headerValue, $_GET['orderBy']);
    else
        $getItems->get_drivers($headerValue);
}
else
    header("HTTP/1.1 401 Unauthorized");
