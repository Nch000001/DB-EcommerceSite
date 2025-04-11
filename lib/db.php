<?php
function getDBConnection() { // 資料庫連線
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "d1280763";

    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4"); // 避免中文亂碼
    return $conn;
}