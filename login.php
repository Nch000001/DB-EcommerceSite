<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連線
require_once './lib/db.php';
$conn = getDBConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = $conn->real_escape_string($_POST['account']);
    $inputPassword = $_POST['password'];

    // 從 users 資料表查詢
    $sql = "SELECT account, password FROM user WHERE account='$account'";
    $result = $conn->query($sql);
    
    $sql2 = "SELECT admin_account, admin_password FROM super_admin WHERE admin_account='$account'";
    $result2 = $conn->query($sql2);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($inputPassword, $hashedPassword)) {
            $_SESSION['account'] = $row['account'];
            // echo "<script>alert('登入成功！'); window.location.href='index.php';</script>";
            echo "<script>window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('密碼錯誤！'); window.history.back();</script>";
        }
    } 

    else if($result2->num_rows == 1){
        $row2 = $result2->fetch_assoc();
        $hashedPassword = $row2['admin_password'];

        if (password_verify($inputPassword, $hashedPassword) || ($hashedPassword == $inputPassword)) {
            $_SESSION['account'] = $row2['admin_account'];
            echo "<script>alert('登入成功！'); window.location.href='./admin/ecommerce_admin.php';</script>";
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
    <title>會員登入</title>
    <style>

    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap');

    * { box-sizing: border-box; }

    html {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1; /* 🔥 主內容自動撐開，其餘交給 header/footer */
    }

    body { font-family: 'Noto Sans TC', sans-serif; margin: 0; background-color: #f5f5f5; }

    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 999;
      
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #1E3A8A;
      padding: 15px 20px;
      color: white;
    }
    .navbar .logo a {
      font-size: 24px;
      font-weight: bold;
      color: white;
      text-decoration: none;
    }
    .nav-links {
      display: flex;
      gap: 10px;
    }
    .nav-links a {
      background-color: #D4AF37;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 20px;
    }
    
    .search-bar { flex-grow: 1; display: flex; justify-content: center; }
    .search-bar input { width: 100%; padding: 8px 12PX; border: 1px solid #CCC; border-radius: 5px; max-width: 600px; font-size: 16px;}
    .hidden {
            display: none;
    }

    .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 80px;
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

        .login-box .register-link {
            margin-top: 15px;
            text-align: center;
        }
        .login-box .register-link a {
            color: #1E3A8A;
            text-decoration: none;
        }

        .login-box .reset_password-link {
            margin-top: 15px;
            text-align: center;
        }
        .login-box .reset_password-link a {
            color: #1E3A8A;
            text-decoration: none;
        }

    .footer {
        background-color: #333; color: white; text-align: center;
        padding: 20px; font-size: 14px;
    }

    </style>
</head>
<body>

    <header class="navbar">
        <div class="logo"><a href="index.php">LOGO</a></div>  
        <div class="search-bar"><div class="hidden"><input type="text" placeholder="搜尋產品..."></div></div>   <!-- 搜尋欄  算法待定 -->

        <div class="nav-links">
            <a href="#">會員</a>
            <a href="#">問題</a>
            <a href="register.php">註冊</a>
            <a href="login.php">登入</a>
        </div>

    </header>
    <br>
    <br><br>
    <div class="login-container">
        <div class="login-box">
            <h2>會員登入</h2>
            <form action="login.php" method="POST">
                <input type="text" name="account" placeholder="帳號" required>
                <input type="password" name="password" placeholder="密碼" required>
                <input type="submit" value="登入">
            </form>
            <div class="register-link">
                還沒有帳號？<a href="register.php">點此註冊</a>
            </div>
            <div class="reset_password-link">
                忘記密碼？<a href="reset_password.php">重設密碼</a>
            </div>
        </div>
    </div>

    
</body>
</html>
