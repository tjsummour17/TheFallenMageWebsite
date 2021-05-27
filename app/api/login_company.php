<?php
include('con.php');
include('const.php');
header('Content-Type: application/json');
class LoginCompany
{
    private $db;
    private $connection;
    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }
    public function driver_login($phone, $password, $token)
    {
        $query = "SELECT * FROM companies WHERE phone='$phone'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) > 0) {
            while ($r = mysqli_fetch_assoc($result)) {
                $user = $r;
            }
            $hash =  $user['password'];
            $verify = password_verify($password, $hash);
            if ($verify) {
                $loginToken = bin2hex(random_bytes(30));
                $query = "UPDATE `companies` SET `token` = '$token', login_token = '$loginToken' WHERE `phone` = '$phone'";
                $is_updated = mysqli_query($this->connection, $query);
                if ($is_updated == 1) {
                    header('login_token:' . $loginToken);
                    $json['status'] = 200;
                    $json['message'] = 'Login Successful';
                    $data = array(
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "phone" => $user['phone'],
                        "admin" => $user['admin'],
                        "imageUrl" => $user['image'],
                        "location" => $user['location'],
                        "token" => $user['token'],
                        "city"=> (int)$user['city'],
                        "wallet" => $user['wallet'],
                        "canceledRequests" => $user['canceledRequests'],
                    );
                    $json['data'] = $data;
                }
            } else {
                $json['status'] = 401;
                $json['message'] = 'Invalid login password';
            }
        } else {
            $json['status'] = 401;
            $json['message'] = 'Invalid login credentials';
        }
        echo json_encode($json);
        mysqli_close($this->connection);
    }
}

$loginCompany = new LoginCompany();
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['phone'], $data['password'], $data['token'])) {
    $headerValue = "";
    foreach (getallheaders() as $name => $value) {
        if ($name == "auth") {
            $headerValue = $value;
        }
    }
    $phone = $data['phone'];
    $password = $data['password'];
    $token = $data['token'];
    if (!empty($phone) && !empty($token) && !empty($password) && !empty($headerValue)) {
        $loginCompany->driver_login($phone, $password, $token);
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
} else {
    $json['status'] = 101;
    $json['message'] = 'missing parmeeters';
    echo json_encode($json);
}
