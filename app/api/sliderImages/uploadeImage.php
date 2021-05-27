<?php
include('con.php');

$db = new Connection();
$connection = $db->get_connection();
$auth = '';
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
if (!empty($auth)) {
    $query = "SELECT * FROM `admins` WHERE `login_token` = '$auth'";
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) != 1) {
        header("HTTP/1.1 401 Unauthorized");
        mysqli_close($connection);
        exit;
    } else {
        if (isset($_FILES["file"], $_POST['name'])) {
            $name = $_POST['name'];
            if ($_FILES["file"]["error"] > 0) {
                header("HTTP/1.1 400 Bad Request");
                mysqli_close($connection);
                exit;
            } else {
                $path = $_SERVER['DOCUMENT_ROOT'] . "/app/api/sliderImages/";
                if (file_exists($path . $name)) {
                    chmod($name, 0755); //Change the file permissions if allowed
                    unlink($name); //remove the file
                    header("HTTP/1.1 200 OK");
                    mysqli_close($connection);
                    move_uploaded_file($_FILES["file"]["tmp_name"], $path . $name);
                    exit;
                }
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
        }
    }
} else
    header("HTTP/1.1 400 Bad Request");
