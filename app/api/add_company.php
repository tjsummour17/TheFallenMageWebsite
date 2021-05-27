<?php
include('con.php');
include('const.php');
header('Content-Type: application/json');

class AddComppany
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function comapny_exist($name, $admin, $phone, $imageUrl, $location, $city, $password, $token)
    {
        $phoneRegEx = '/([0][7][7-9][0-9]{7})/i';
        if (preg_match($phoneRegEx, $phone) == 1) {
            $query = "SELECT * FROM companies WHERE phone='$phone'";
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) > 0) {
                header("HTTP/1.1 304 Not Modified");
            } else {
                $loginToken = bin2hex(random_bytes(30));
                $password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO `companies`(`id`, `name`, `image`, `admin`, phone, `password`, `token`, `location`, `city`, `login_token`) values( NULL, '$name', '$imageUrl', '$admin', '$phone',  '$password', '$token', '$location', '$city', '$loginToken')";
                $is_inserted = mysqli_query($this->connection, $query);
                if ($is_inserted == 1) {
                    header("HTTP/1.1 200 OK");
                    header('login_token:' . $loginToken);
                } else {
                    header("HTTP/1.1 500 Internal Server Error");
                }
            }
        } else {
            header("HTTP/1.1 500 Internal Server Error");
        }
        mysqli_close($this->connection);
        exit;
    }
}

$company = new AddComppany();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['name'], $data['admin'], $data['phone'], $data['imageUrl'], $data['location'], $data['city'], $data['password'], $data['token']) && $headerValue == $auth) {
    $name = $data['name'];
    $admin = $data['admin'];
    $phone = $data['phone'];
    $imageUrl = $data['imageUrl'];
    $location = $data['location'];
    $city = $data['city'];
    $password = $data['password'];
    $token = $data['token'];
//    echo $name . ' ' . $admin . ' ' . $phone . ' ' . $imageUrl . ' ' . $location . ' ' . $city . ' ' . $password . ' ' . $token;
    if (!empty($name) && !empty($admin) && !empty($phone) && !empty($location)  && !empty($password) && !empty($token)) {
        $company->comapny_exist($name, $admin, $phone, $imageUrl, $location, $city, $password, $token);
    } else {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
}