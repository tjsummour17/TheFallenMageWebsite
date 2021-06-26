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
    public function get_company_items($companyId, $auth)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            mysqli_close($this->connection);
        } else {
            $query = "SELECT * FROM `items` WHERE companyId = $companyId  AND (`status`=3 OR `status`=6 OR `status`=7  OR `status`=5 )ORDER BY `status` ASC" ;
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) > 0) {
                header("HTTP/1.1 200 OK");
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode($data);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
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
if (isset($_GET['companyId'])) {
    $companyId = $_GET['companyId'];
    if (!empty($companyId) && !empty($headerValue)) {
        $getItems->get_company_items($companyId, $headerValue);
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
}
