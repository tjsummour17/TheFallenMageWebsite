<?php
include('con.php');
header('Content-Type: application/json');

class GetCompanies
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function get_companies($auth, $companyId)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            $query = "SELECT * FROM `companyUnpaidOrders` WHERE companyId = $companyId AND `is_archived` = 0";
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

$getItems = new GetCompanies();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
if (!empty($headerValue)) {
    if (isset($_GET['companyId']))
        $getItems->get_companies($headerValue, $_GET['companyId']);
    else
        header("HTTP/1.1 400 Bad Request");

} else
    header("HTTP/1.1 403 Unauthorized");
