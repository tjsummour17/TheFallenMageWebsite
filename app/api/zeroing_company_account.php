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

    public function update_company($auth, $id)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            $query = "UPDATE `companies` SET `wallet` = 0.0, `canceledRequests` = 0.0 WHERE `id` = $id";
            $is_inserted = mysqli_query($this->connection, $query);
            if ($is_inserted == 1) {

            } else {
                header("HTTP/1.1 500 Internal Server Error");
            }
        }
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
    if (isset($data['id'])) {
        $id = $data['id'];
        $newCompany->update_company($auth, $id);
    } else {
        header("HTTP/1.1 403 Bad Request");
    }
}
else
    header("HTTP/1.1 403 Bad Request");