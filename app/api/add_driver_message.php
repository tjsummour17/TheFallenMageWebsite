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
    public function add_message($auth, $driverId, $message)
    {
        $query = "SELECT * FROM `drivers` WHERE  `id` = $driverId AND `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            $query = "INSERT INTO `driverMessages`(`id`, `message`, `driverID`) VALUES ( NULL, '$message', $driverId)";
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
    $driverId = $data['id'];
    if (!empty($auth) && !empty($message) && !empty($driverId)) {
        $addMessage->add_message($auth, $driverId, $message);
    } else {
        $json['status'] = 100;
        $json['message'] = 'You must fill all the fields';
        echo json_encode($json);
    }
}
