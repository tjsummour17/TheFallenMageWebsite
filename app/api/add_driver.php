<?php
include('con.php');
include('const.php');
header('Content-Type: application/json');

class AddDriver
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function driver_exist($name, $phone, $imageUrl, $carType, $carModel, $city, $password, $token)
    {
        $phoneRegEx = '/([0][7][7-9][0-9]{7})/i';
        if (preg_match($phoneRegEx, $phone) == 1) {
            $query = "SELECT * FROM drivers WHERE phone='$phone'";
            $result = mysqli_query($this->connection, $query);

            if (mysqli_num_rows($result) > 0) {
                header("HTTP/1.1 304 Not Modified");
                mysqli_close($this->connection);
                exit;
            } else {
                $loginToken = bin2hex(random_bytes(30));
                $password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO `drivers`(`id`, `name`, `phone`, `imageUrl`, `password`, `carType`, `carModel`, `city`,`login_token`, `token`) values( NULL, '$name', '$phone', '$imageUrl', '$password', '$carType', $carModel, '$city', '$loginToken', '$token')";
                $is_inserted = mysqli_query($this->connection, $query);
                print($query);
                if ($is_inserted == 1) {
                    header("HTTP/1.1 200 OK");
                    header('login_token:' . $loginToken);
                    mysqli_close($this->connection);
                } else {
                    header("HTTP/1.1 500 Internal Server Error");
                    mysqli_close($this->connection);
                    exit;
                }
            }
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            exit;
        }
    }
}

$driver = new AddDriver();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['name'], $data['phone'], $data['carType'], $data['carModel'], $data['city'], $data['password'], $data['token']) && $headerValue == $auth) {
    $name = $data['name'];
    $phone = $data['phone'];
    $imageUrl = $data['imageUrl'];
    $carType = $data['carType'];
    $carModel = $data['carModel'];
    $city = $data['city'];
    $password = $data['password'];
    $token = $data['token'];
    if (!empty($name) && !empty($phone) && !empty($password)) {
        $driver->driver_exist($name, $phone, $imageUrl, $carType, $carModel, $city, $password, $token);
    } else {
        print("Error in Request");
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
}
