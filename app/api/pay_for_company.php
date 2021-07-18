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

    public function update_company($auth, $companyId, $orderIds, $paymentValue, $selectAll)
    {
        $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
        $result = mysqli_query($this->connection, $query);
        if (mysqli_num_rows($result) != 1) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            if ($selectAll == 1) {
                $query = "DELETE FROM `companyUnpaidOrders` WHERE `companyId` = $companyId";
                $is_inserted = mysqli_query($this->connection, $query);
                if ($is_inserted == 1) {
                    $query = "UPDATE `companies` SET `wallet` = 0.0, `canceledRequests` = 0.0 WHERE `id` = $companyId";
                    $is_inserted = mysqli_query($this->connection, $query);
                    if ($is_inserted != 1) {
                        header("HTTP/1.1 500 Internal Server Error");
                    }
                }
            } else {
                $query = "SELECT * FROM `companies` WHERE id = $companyId";
                $result = mysqli_query($this->connection, $query);
                $company = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
                if ($company['wallet'] >= $paymentValue) {
                    $newWallet = $company['wallet'] - $paymentValue;
                    $query = "UPDATE `companies` SET `wallet` = " . $newWallet . ", `canceledRequests` = 0.0 WHERE `id` = $companyId";
                    $is_inserted = mysqli_query($this->connection, $query);
                    if ($is_inserted != 1) {
                        header("HTTP/1.1 500 Internal Server Error");
                    }
                    foreach ($orderIds as $orderId) {
                        $query = "DELETE FROM `companyUnpaidOrders` WHERE `companyId` = $companyId AND `id` = $orderId";
                        mysqli_query($this->connection, $query);
                    }
                }
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
    if (isset($data['companyId'], $data['orderIds'], $data['selectAll'])) {
        $companyId = $data['companyId'];
        $orderIds = $data['orderIds'];
        $selectAll = $data['selectAll'];
        $paymentValue = $data['paymentValue'];
        $newCompany->update_company($auth, $companyId, $orderIds, $paymentValue, $selectAll);
    } else {
        header("HTTP/1.1 403 Bad Request");
    }
} else
    header("HTTP/1.1 403 Bad Request");