<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = $conn->real_escape_string($_POST['account']);
    $inputPassword = $_POST['password'];

    // 從 users 資料表查詢
    $sql = "SELECT account, password FROM users WHERE account='$account'";
    $result = $conn->query($sql);
    
    $sql2 = "SELECT admin_account, admin_password FROM super_admins WHERE admin_account='$account'";
    $result2 = $conn->query($sql2);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($inputPassword, $hashedPassword)) {
            $_SESSION['account'] = $row['account'];
            echo "<script>alert('登入成功！'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('密碼錯誤！'); window.history.back();</script>";
        }
    } 

    else if($result2->num_rows == 1){
        $row2 = $result2->fetch_assoc();
        $hashedPassword = $row2['admin_password'];

        if (password_verify($inputPassword, $hashedPassword) || ($hashedPassword == $inputPassword)) {
            $_SESSION['account'] = $row2['admin_account'];
            echo "<script>alert('登入成功！'); window.location.href='ecommerce_admin.html';</script>";
        } else {
            echo "<script>alert('密碼錯誤！'); window.history.back();</script>";
        }
    }

    else {
        echo "<script>alert('帳號不存在！'); window.history.back();</script>";
    }

    

}

$conn->close();
?>