<?php
include('con.php');
header('Content-Type: application/json');

class UpdateCompany
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function update_company($name, $phone, $imageUrl, $location, $city, $admin, $auth)
    {
        $query = "SELECT * FROM `companies` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            $query = "UPDATE `companies` SET `name` = '$name', `phone` = '$phone', `image` = '$imageUrl', `location` = '$location', city = $city, `admin` = '$admin' WHERE `id` = $id";
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

$newCompany = new UpdateCompany();
$auth = '';
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (!empty($auth)) {
    if (isset($data['name'], $data['phone'], $data['imageUrl'], $data['location'], $data['city'])) {
        $name = $data['name'];
        $phone = $data['phone'];
        $admin = $data['admin'];
        $imageUrl = $data['imageUrl'];
        $location = $data['location'];
        $city = $data['city'];
        $newCompany->update_company($name, $phone, $imageUrl, $location, $city, $admin, $auth);
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
}
