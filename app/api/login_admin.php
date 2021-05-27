<?php
include('con.php');
include('const.php');
header('Content-Type: application/json');

class LoginAdmin
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function admin_login($name, $password, $token)
    {
        $query = "SELECT * FROM `admins` WHERE `name`='$name'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) > 0) {
            while ($r = mysqli_fetch_assoc($result)) {
                $user = $r;
            }
            $hash = $user['password'];
            $verify = password_verify($password, $hash);
            if ($verify) {
                $loginToken = bin2hex(random_bytes(30));
                $query = "UPDATE `admins` SET `token` = '$token', login_token = '$loginToken' WHERE `name` = '$name'";
                $is_updated = mysqli_query($this->connection, $query);
                if ($is_updated == 1) {
                    header('login_token:' . $loginToken);
                    header("HTTP/1.1 200 OK");
                    mysqli_close($this->connection);
                    $data = array(
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "token" => $user['token'],
                        "isSuperAdmin" => (int)$user['isSuperAdmin'],
                        "is_archived" => (int)$user['is_archived']
                    );
                    echo  json_encode($data);
                    mysqli_close($this->connection);
                    exit;
                } else {
                    header("HTTP/1.1 503 Service Unavailable");
                    mysqli_close($this->connection);
                    exit;
                }
            } else {
                header("HTTP/1.1 400 SBad Request");
                mysqli_close($this->connection);
                exit;
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            mysqli_close($this->connection);
            exit;
        }
    }
}

$loginDriver = new LoginAdmin();
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['name'], $data['password'], $data['token'])) {
    $headerValue = "";
    foreach (getallheaders() as $name => $value) {
        if ($name == "auth") {
            $headerValue = $value;
        }
    }
    $name = $data['name'];
    $password = $data['password'];
    $token = $data['token'];
    if (!empty($name) && !empty($token) && !empty($password) && $auth == $headerValue) {
        $loginDriver->admin_login($name, $password, $token);
    } else {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
