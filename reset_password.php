<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce"; // ← 換成你的資料庫名稱

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 處理表單送來的資料
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $newPassword = $_POST['new_password'];

    // 檢查 Email 是否存在
    $check_sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check_sql);

    if ($result->num_rows == 1) {
        // Email 存在，更新密碼
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users SET password='$hashedPassword' WHERE email='$email'";
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('密碼已重設成功！請重新登入'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('更新失敗，請稍後再試！'); window.history.back();</script>";
        }
    } 
    else{
        echo "<script>alert('該 Email 不存在！'); window.history.back();</script>";
    }
}

$conn->close();
?>