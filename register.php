<?php
require_once './lib/db.php';
$conn = getDBConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得 user_id
    $max_id_sql = "SELECT MAX(CAST(SUBSTRING(user_id, 2) AS UNSIGNED)) AS max_id FROM user";
    $result = $conn->query($max_id_sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $max_id = $row['max_id'];
        $new_id_number = ($max_id == null) ? 1 : $max_id + 1;
    } else {
        $new_id_number = 1;
    }
    $new_id = "S" . str_pad($new_id_number, 4, '0', STR_PAD_LEFT);

    // 接收表單資料
    $account = $conn->real_escape_string($_POST['account']);
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $conn->real_escape_string($_POST['name']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);

    // 組 SQL
    $sql = "INSERT INTO user (user_id, account, password, name, birthday, home_address, phone_numbers, email)
            VALUES ('$new_id', '$account', '$hashed_password', '$name', '$birthday', '$address', '$phone', '$email')";


    if ($account!=NULL && $conn->query($sql) === TRUE) {
        echo "<script>alert('註冊成功！'); window.location.href='login.php';</script>";
    }
    else {
        echo "新增失敗：" . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>會員註冊</title>
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


        .register-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
            padding: 20px;
        }
        .register-box {
            background-color: white;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0px 6px 18px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        .register-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #1E3A8A;
        }
        .register-box input[type="text"],
        .register-box input[type="email"],
        .register-box input[type="tel"],
        .register-box input[type="password"],
        .register-box input[type="date"],
        .register-box input[type="submit"],
        .register-box button,
        .register-box textarea {
            width: 80%;           /* 設為 80%，不會太滿 */
            max-width: 350px;     /* 最大不超過 350px */
            padding: 14px;
            margin: 10px auto;    /* 自動左右置中 */
            display: block;       /* 讓 margin auto 生效 */
            border: 1px solid #CCC;
            border-radius: 10px;
            transition: border-color 0.3s ease;
        }

        .register-box textarea {
            resize: vertical;     /* 保留高度調整 */
        }
        .register-box input:focus,
        .register-box textarea:focus {
            outline: none;
            border-color: #1E3A8A;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.3);
        }
        .register-box button,
        .register-box input[type="submit"] {
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
        .register-box button:hover,
        .register-box input[type="submit"]:hover {
            background-color: #163372;
            transform: translateY(-2px);
        }
        .register-box .login-link {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
        }
        .register-box .login-link a {
            color: #1E3A8A;
            text-decoration: none;
            font-weight: bold;
        }
        .hidden {
            display: none;
        }

        @media (max-width: 500px) {
            .register-box {
                padding: 30px 20px;
            }
        }
    </style>

    <script>
        function validateBasicInfo() {
            let form = document.forms["registerForm"];
            let requiredFields = ["account", "email", "name", "phone", "birthday", "address", "password", "confirm_password"];

            for (let i = 0; i < requiredFields.length; i++) {
                let field = form[requiredFields[i]];
                if (!field.value.trim()) {
                    alert(field.placeholder + " 不能為空！");
                    field.focus();
                    return false;
                }
            }

            // 檢查電話格式
            let phone = form["phone"].value;
            let phonePattern = /^09\d{8}$/;
            if (!phonePattern.test(phone)) {
                alert("電話號碼格式不正確，應為09開頭的10位數字。");
                return false;
            }

            // 檢查密碼一致
            let password = form["password"].value;
            let confirmPassword = form["confirm_password"].value;
            if (password !== confirmPassword) {
                alert("密碼與確認密碼不一致！");
                return false;
            }

            // >>> 新增重複檢查
            let account = form["account"].value.trim();
            let email = form["email"].value.trim();
            let phoneNumber = form["phone"].value.trim();

            fetch('check_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ account: account, email: email, phone: phoneNumber })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 通過 → 顯示驗證碼區
                    document.getElementById('verification-section').classList.remove('hidden');
                    document.getElementById('person-info').classList.add('hidden');
                    document.getElementById('basic-info-btn').disabled = true;
                    sendVerificationCode();
                    let button = document.getElementById("basic-info-btn2"); // 取得發送驗證碼按鈕
                    startCountdown(button);
                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error("錯誤:", error);
                alert("檢查失敗，請稍後再試！");
            });

            return false; // 阻止表單提交
        }

        function sendVerificationCode() {
            let email = document.getElementById("email").value;
            if (!email) {
                alert("請輸入 Gmail！");
                return;
            }
            alert("驗證碼已成功寄出！\n請至您的 Gmail 收取，並檢查垃圾郵件匣。");
            fetch('sent_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.code) {
                        sessionStorage.setItem("email_code", data.code);
                        // alert("驗證碼已成功寄出！\n請至您的 Gmail 收取，並檢查垃圾郵件匣。");
                    } else {
                        alert("發送失敗：" + data.error);
                    }
                })
                .catch(error => alert("發送失敗，請稍後再試！"));
        }

        function finalSubmit() {
            let emailCode = document.forms["registerForm"]["email_code"].value;
            if (emailCode !== sessionStorage.getItem("email_code")) {
                alert("驗證碼不正確！");
                return false;
            }
            // alert("註冊成功！");
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

    <br><br><br>
<div class="register-container">
    <div class="register-box">
        <h2>會員註冊</h2>
        <form name="registerForm" action="register.php" method="POST" onsubmit="return finalSubmit()">
            <div id="person-info">
                <input type="text" name="account" placeholder="帳號名稱" maxlength="50" required />
                <input type="email" id="email" name="email" placeholder="Gmail" maxlength="50" required />
                <input type="text" name="name" placeholder="姓名" maxlength="100" required />
                <input type="tel" name="phone" placeholder="電話 (09開頭)" pattern="09\d{8}" required />
                <input type="date" name="birthday" placeholder="生日" required />
                <textarea name="address" rows="2" placeholder="地址" required></textarea>
                <input type="password" name="password" placeholder="密碼" required />
                <input type="password" name="confirm_password" placeholder="確認密碼" required/>
                <button type="button" id="basic-info-btn" onclick="validateBasicInfo()">發送驗證碼</button>
            </div>

            <!-- 驗證碼區 -->
            <div id="verification-section" class="hidden">
                <input type="text" name="email_code" placeholder="輸入驗證碼" required />
                <br><br>
                <button type="button" id="basic-info-btn2" onclick="validateBasicInfo()">發送驗證碼</button>
                <input type="submit" value="完成註冊">
            </div>
        </form>
        <div class="login-link">
            已有帳號？<a href="login.php">點此登入</a>
        </div>
    </div>
</div>

</body>
</html>