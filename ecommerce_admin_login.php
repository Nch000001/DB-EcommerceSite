<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = $conn->real_escape_string($_POST['account']);
    $inputPassword = $_POST['password'];

    
    $sql = "SELECT admin_account, admin_password FROM super_admin WHERE admin_account='$account'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        $hashedPassword = $row['admin_password'];

        if (password_verify($inputPassword, $hashedPassword) || ($hashedPassword == $inputPassword)) {
            $_SESSION['super_user_account'] = $row['admin_account'];
            // echo "<script>alert('登入成功！'); window.location.href='ecommerce_admin.php';</script>";
            echo "<script>window.location.href='ecommerce_admin.php';</script>";
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




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce 後台</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap');

        body {
            font-family: 'Noto Sans TC', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F5F5F5;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 190px;
            /* width: 100%; */
        }
        .login-box {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1E3A8A;
        }
        .login-box input[type="text"], .login-box input[type="password"] {
            width: 90%;           /* 設為 80%，不會太滿 */
            max-width: 350px;     /* 最大不超過 350px */
            padding: 14px;
            margin: 10px auto;    /* 自動左右置中 */
            display: block;       /* 讓 margin auto 生效 */
            border: 1px solid #CCC;
            border-radius: 10px;
            transition: border-color 0.3s ease;
        }
        .login-box input[type="submit"] {
            width: 100%;
            background-color: #1E3A8A;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-box input[type="submit"]:hover {
            background-color: #163372;
        }

    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <h2>管理員登入</h2>
            <form method="POST">
                <input type="text" name="account" placeholder="帳號" required>
                <input type="password" name="password" placeholder="密碼" required>
                <input type="submit" value="登入">
            </form>
        </div>
    </div>

</body>
</html>
