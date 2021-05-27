<?php
if (isset($_FILES["file"], $_POST["name"])) {
    $name = $_POST["name"];
    $path = $_SERVER['DOCUMENT_ROOT'] . "/hobbyhub/";
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