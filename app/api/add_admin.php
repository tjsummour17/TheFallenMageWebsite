<?php
include('con.php');
include('const.php');
header('Content-Type: application/json');

class AddAdmin
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function admin_exist($name, $isSuperAdmin, $password, $token, $auth)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth' AND `isSuperAdmin` = 1";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            mysqli_close($this->connection);
            exit;
        } else {
            $query = "SELECT * FROM `admins` WHERE `name`='$name'";
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) > 0) {
                header("HTTP/1.1 304 Not Modified");
                mysqli_close($this->connection);
                exit;
            } else {
                $loginToken = bin2hex(random_bytes(30));
                $password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO `admins`(`id`, `name`, `password`, `isSuperAdmin`,`token`, `login_token`) values ( NULL, '$name', '$password', $isSuperAdmin, '$token', '$loginToken')";
                $is_inserted = mysqli_query($this->connection, $query);
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
        }
    }
}

$admin = new AddAdmin();
$headerValue = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $headerValue = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (!empty($headerValue))
    if (isset($data['name'], $data['isSuperAdmin'], $data['password'], $data['token'])) {
        $name = $data['name'];
        $isSuperAdmin = $data['isSuperAdmin'];
        $password = $data['password'];
        $token = $data['token'];
        if (!empty($name) && !empty($isSuperAdmin) && !empty($password) && !empty($token)) {
            $admin->admin_exist($name, $isSuperAdmin, $password, $token, $headerValue);
        } else {
            header("HTTP/1.1 400 Bad Request");
            exit;
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }
else {
    header("HTTP/1.1 401 Unauthorized");
}