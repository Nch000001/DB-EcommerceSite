<?php
header('Content-Type: application/json');

// 資料庫連線設定
require_once 'lib/db.php';
$conn = getDBConnection();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 接收前端傳來的資料
    $data = json_decode(file_get_contents("php://input"), true);
    $account = $conn->real_escape_string($data['account']);
    $email = $conn->real_escape_string($data['email']);
    $phone = $conn->real_escape_string($data['phone']);


    // 查詢是否有重複
    $sql = "SELECT * FROM user WHERE account='$account' OR email='$email' OR phone_numbers='$phone'";
    $result = $conn->query($sql);

    $sql2 = "SELECT * FROM super_admin WHERE admin_account='$account'";
    $result2 = $conn->query($sql2);

    if ($result->num_rows + $result2->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "帳號、Email 或電話已存在"]);
    } else {
        echo json_encode(["success" => true]);
    }
}

$conn->close();
?>