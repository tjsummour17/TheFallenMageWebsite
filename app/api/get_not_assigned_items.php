<?php
include('con.php');
header('Content-Type: application/json');

class GetItems
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function get_items($auth)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            mysqli_close($this->connection);
        } else {
            $query = "SELECT * FROM `items` WHERE `driverId` IS NULL";
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) >= 0) {
                echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            } else {
                header("HTTP/1.1 503 Service Unavailable");
            }
            mysqli_close($this->connection);
        }
    }
}

$getItems = new GetItems();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
if (!empty($headerValue)) {
    $getItems->get_items($headerValue);
} else {
    header("HTTP/1.1 401 Unauthorized");

}
