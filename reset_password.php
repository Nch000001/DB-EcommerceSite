<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連線
include 'db.php';
global $conn;


// 處理表單送來的資料
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $newPassword = $_POST['new_password'];

    // 檢查 Email 是否存在
    $check_sql = "SELECT * FROM user WHERE email='$email'";
    $result = $conn->query($check_sql);

    if ($result->num_rows == 1) {
        // Email 存在，更新密碼
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $update_sql = "UPDATE user SET password='$hashedPassword' WHERE email='$email'";
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('密碼已重設成功！請重新登入'); window.location.href='login.php';</script>";
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>重設密碼</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap');
        body {
            font-family: 'Noto Sans TC', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F0F4F8;
        }
        .navbar {
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
            padding: 8px 15px;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }
        .nav-links a:hover {
            background-color: #b88a20;
        }

        .reset-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
            padding: 20px;
        }
        .reset-box {
            background-color: white;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0px 6px 18px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        .reset-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #1E3A8A;
        }
        .reset-box input[type="email"],
        .reset-box input[type="password"],
        .reset-box input[type="text"],
        .reset-box input[type="submit"],
        .reset-box button {
            width: 80%;
            max-width: 350px;
            padding: 14px;
            margin: 10px auto;
            display: block;
            border: 1px solid #CCC;
            border-radius: 10px;
            transition: border-color 0.3s ease;
        }
        .reset-box input:focus {
            outline: none;
            border-color: #1E3A8A;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.3);
        }
        .reset-box button,
        .reset-box input[type="submit"] {
            width: 100%;
            background-color: #1E3A8A;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 12px;
            transition: background-color 0.3s ease, transform 0.2s;
        }
        .reset-box button:hover,
        .reset-box input[type="submit"]:hover {
            background-color: #163372;
            transform: translateY(-2px);
        }
        .hidden {
            display: none;
        }
    </style>

    <script>
        function sendResetCode() {
            document.getElementById('verification-section').classList.add('hidden');
            let email = document.getElementById("email").value.trim();
            if (!email) {
                alert("請輸入 Gmail！");
                return;
            }
            alert("驗證碼已寄出，請檢查您的 Gmail 或垃圾信匣！");
            document.getElementById('person-info').classList.add('hidden');
            document.getElementById('verification-section').classList.remove('hidden');


            // Call backend API to send verification code
            fetch('sent_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.code) {
                    sessionStorage.setItem("reset_code", data.code);
                    let button = document.getElementById("send-code-btn"); // 取得發送驗證碼按鈕
                    startCountdown(button);
                } else {
                    alert("發送失敗：" + data.error);
                }
            })
            .catch(error => {
                alert("發送失敗，請稍後再試！");
            });
        }

        function validateReset() {
            let code = document.getElementById("code").value.trim();
            let newPass = document.getElementById("new_password").value.trim();
            let confirmPass = document.getElementById("confirm_password").value.trim();


            if (code !== sessionStorage.getItem("reset_code")) {
                alert("驗證碼不正確！");
                return false;
            }
            if (!newPass || !confirmPass) {
                alert("請填寫新密碼！");
                return false;
            }
            if (newPass !== confirmPass) {
                alert("兩次密碼不一致！");
                return false;
            }

            //alert("密碼重設成功！");
            return true;
        }

        function startCountdown(button) {
            button.disabled = true;
            let countdown = 60;
            button.textContent = `請在 ${countdown} 秒後重試`;

            let timer = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    button.textContent = `請在 ${countdown} 秒後重試`;
                } else {
                    clearInterval(timer);
                    button.disabled = false;
                    button.textContent = "重新發送驗證碼";
                }
            }, 1000);
        }
    </script>
</head>

<body>

<div class="navbar">
    <div class="logo"><a href="index.php">LOGO</a></div>
    <div class="nav-links">
        <a href="index.php">會員</a>
        <a href="index.php">問題</a>
        <a href="register.php">註冊</a>
        <a href="login.php">登入</a>
    </div>
</div>

<div class="reset-container">
    <div class="reset-box">
        <h2>重設密碼</h2>
        <form action="reset_password.php" method="POST" onsubmit="return validateReset()">
            <div id="person-info">
                <input type="email" id="email" name="email" placeholder="Gmail" required />
                <br>
                <button type="button" onclick="sendResetCode()">發送驗證碼</button>
            </div>

            <div id="verification-section" class="hidden">
                <input type="text" id="code" name="code" placeholder="輸入驗證碼" required />
                <input type="password" id="new_password" name="new_password" placeholder="新密碼" required />
                <input type="password" id="confirm_password" placeholder="確認新密碼" required />
                <br><br>
                <button type="button" id="send-code-btn" onclick="sendResetCode()">發送驗證碼</button>
                <input type="submit" value="重設密碼">
            </div>
        </form>
    </div>
</div>


</body>
</html>