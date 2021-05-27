<?php
include('const.php');

$auth = '';
foreach (getallheaders() as $name => $value) {
    if ($name == "auth") {
        $auth = $value;
    }
}
if ($auth == $myAuth) {
    if (isset($_FILES["file"], $_POST["name"])) {
        $name = $_POST["name"];
        if ($_FILES["file"]["error"] > 0) {
            header("HTTP/1.1 400 Bad Request");
        } else {
            $path = $_SERVER['DOCUMENT_ROOT'] . "/app/api/driverImages/";
            if (file_exists($path . $name)) {
                chmod($name, 0755); //Change the file permissions if allowed
                unlink($name); //remove the file
                header("HTTP/1.1 200 OK");
                move_uploaded_file($_FILES["file"]["tmp_name"], $path . $name);
                exit;
            } else {
                move_uploaded_file($_FILES["file"]["tmp_name"], $path . $name);
                header("HTTP/1.1 200 OK");
            }
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
    }
}
else{
    header("HTTP/1.1 401 Unauthorized");
}