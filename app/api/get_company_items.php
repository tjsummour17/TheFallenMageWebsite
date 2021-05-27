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
    public function get_compay_items($companyId, $auth)
    {
        $query = "SELECT * FROM `companies` WHERE `id` = $companyId AND `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            $json['status'] = 400;
            $json['message'] = ' Sorry you dont have any access to do this.';
            echo json_encode($json);
            mysqli_close($this->connection);
        } else {
            $query = "SELECT * FROM `items` WHERE companyId = $companyId  ORDER BY `status` ASC";
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) > 0) {
                $json['status'] = 200;
                $json['message'] = 'Success';
                    $json['data'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
            } else {
                $json['status'] = 401;
                $json['message'] = 'Something wrong';
            }
            echo json_encode($json);
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
        $getItems->get_compay_items($companyId, $headerValue);
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
}
