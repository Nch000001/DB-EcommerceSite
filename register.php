<?php
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "ecommerce";

// 建立連線
$conn = new mysqli($servername, $username, $db_password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 取得 user_id
$max_id_sql = "SELECT MAX(CAST(SUBSTRING(user_id, 2) AS UNSIGNED)) AS max_id FROM users";
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
$sql = "INSERT INTO users (user_id, account, password, name, birthday, home_address, phone_numbers, email)
        VALUES ('$new_id', '$account', '$hashed_password', '$name', '$birthday', '$address', '$phone', '$email')";

// Debug
echo "SQL 語句：" . $sql . "<br>";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('註冊成功！'); window.location.href='login.html';</script>";
    // echo "window.location.href='login.html';</script>";
} else {
    echo "新增失敗：" . $conn->error;
}

$conn->close();
?>