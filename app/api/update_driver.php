<?php
include('con.php');
header('Content-Type: application/json');
class UpdateDriver
{
    private $db;
    private $connection;
    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }
    public function driver_update(
        $id,
        $name,
        $email,
        $imageUrl,
        $carType,
        $carModel,
        $city,
        $auth
    ) {
        $query = "SELECT * FROM drivers WHERE login_token='$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            $json['status'] = 400;
            $json['message'] = ' Sorry your account is not exist.';
        } else {
            $query = "UPDATE `drivers` SET `name` = '$name', `imageUrl` = '$imageUrl', `carType` = '$carType', `carModel` = $carModel, `city` = '$city' WHERE `id` = $id";
            $is_inserted = mysqli_query($this->connection, $query);
            if ($is_inserted == 1) {
                $json['status'] = 200;
                $json['message'] = $name . '\'s Account Updated.';
            } else {
                $json['status'] = 401;
                $json['message'] = 'Something wrong';
            }
        }
        echo json_encode($json);
        mysqli_close($this->connection);
    }
}
$driver = new UpdateDriver();
$auth = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['id'], $data['name'], $data['email'], $data['imageUrl'], $data['carType'], $data['carModel'], $data['city']) && !empty($auth)) {
    $id = $data['id'];
    $name = $data['name'];
    $email = $data['email'];
    $imageUrl = $data['imageUrl'];
    $carType = $data['carType'];
    $carModel = $data['carModel'];
    $city = $data['city'];
    if (!empty($id) && !empty($name) && !empty($imageUrl) && !empty($carType) && !empty($carModel) && !empty($city) && !empty($auth)) {
        $driver->driver_update(
            $id,
            $name,
            $email,
            $imageUrl,
            $carType,
            $carModel,
            $city,
            $auth
        );
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
}
