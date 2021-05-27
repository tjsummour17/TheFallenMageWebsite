<?php
include('con.php');
header('Content-Type: application/json');
class AddMessage
{
    private $db;
    private $connection;
    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }
    public function add_message($auth, $companyId, $message)
    {
        $query = "SELECT * FROM `companies` WHERE  `id` = $companyId AND `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            $query = "INSERT INTO `contactUsMessages`(`id`, `message`, `companyID`) VALUES ( NULL, '$message', $companyId)";
            $is_inserted = mysqli_query($this->connection, $query);
            if ($is_inserted == 1) {
                $json['status'] = 200;
                $json['message'] = 'Message ' . $message . ' added to queue';
            } else {
                $json['status'] = 401;
                $json['message'] = 'Something wrong';
            }
            echo json_encode($json);
            mysqli_close($this->connection);
        }
    }
}
$addMessage = new AddMessage();
$auth = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['message'], $data['id']) && !empty($auth)) {
    $message = $data['message'];
    $companyId = $data['id'];
    if (!empty($auth) && !empty($message) && !empty($companyId)) {
        $addMessage->add_message($auth, $companyId, $message);
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
}
