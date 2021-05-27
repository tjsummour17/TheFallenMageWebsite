<?php
include('con.php');
header('Content-Type: application/json');

class GetAdmins
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function get_admins($auth)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
        } else {
            $query = "SELECT `id`, `name`, `isSuperAdmin`, `token`, `is_archived` FROM admins";
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) > 0) {
                echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            } else {
                header("HTTP/1.1 503 Service Unavailable");
            }
            mysqli_close($this->connection);
        }
    }
}

$getAdmins = new GetAdmins();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
if (!empty($headerValue)) {
    $getAdmins->get_admins($headerValue);
}
