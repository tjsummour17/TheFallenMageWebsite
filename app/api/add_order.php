<?php
include('con.php');
header('Content-Type: application/json');

class AddItem
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function add_item($auth, $name, $quantity, $description, $price, $customerName, $customerPhone, $receiveLocation, $deliveryLocation, $companyId, $images)
    {
        $query = "SELECT * FROM `companies` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        $query2 = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result2 = mysqli_query($this->connection, $query2);
        if (mysqli_num_rows($result) != 1 && mysqli_num_rows($result2) != 1) {
            header("HTTP/1.1 401 Unauthorized");
        } else {
            if (empty($images))
                $images = "[]";
            $query = "INSERT INTO `items`(`id`, `name`, `description`, `price`, `quantity`, `receiveLocation`, `deliveryLocation`, `customerName`, `customerPhone`, `companyId`,`images`) VALUES ( NULL, '$name', '$description', $price, $quantity, '$receiveLocation', '$deliveryLocation', '$customerName', '$customerPhone', $companyId,'$images')";
            $is_inserted = mysqli_query($this->connection, $query);
            echo $query;
            if ($is_inserted == 1) {
                header("HTTP/1.1 200 OK");
            } else {
                header("HTTP/1.1 503 Service Unavailable");
            }
        }
        mysqli_close($this->connection);
        exit;
    }
}

$addItem = new AddItem();
$auth = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['name'], $data['description'], $data['receivingLocation'], $data['deliveryLocation'], $data['price'], $data['customerName'], $data['customerPhone'], $data['companyId']) && !empty($auth)) {
    $name = $data['name'];
    $description = $data['description'];
    $quantity = $data['quantity'];
    $price = $data['price'];
    $customerName = $data['customerName'];
    $customerPhone = $data['customerPhone'];
    $receiveLocation = $data['receivingLocation'];
    $deliveryLocation = $data['deliveryLocation'];
    $companyId = $data['companyId'];
    $images = $data['images'];
    if (!empty($name) && !empty($quantity) && !empty($price) && !empty($customerName) && !empty($customerPhone) && !empty($receiveLocation) && !empty($deliveryLocation) && !empty($companyId)) {
        $addItem->add_item($auth, $name, $quantity, $description, $price, $customerName, $customerPhone, $receiveLocation, $deliveryLocation, $companyId, $images);
    } else {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
