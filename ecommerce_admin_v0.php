<?php
session_start(); // 啟用 session

// 🔹 資料庫連線參數
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "D1280763";

// 🔹 建立資料庫連線
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

/**
 * 🔹 檢查管理員登入資訊
 *
 * @param string $admin_account 管理員帳號
 * @param string $password 使用者輸入的密碼
 * @param object $conn 資料庫連線物件
 * @return bool 驗證成功回傳 true，否則 false
 */
function checkAdminCredentials($admin_account, $password, $conn) {
    // 預處理 SQL 避免 SQL Injection
    $stmt = $conn->prepare("SELECT admin_password FROM super_admins WHERE admin_account = ?");
    $stmt->bind_param("s", $admin_account);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // 使用 password_verify() 進行密碼比對
        /*if (password_verify($password, $row['admin_password'])) {
            return true;
        }*/
        if ($password === $row['admin_password']) {
            return true;
        }
    }
    return false;
}

// 🔹 若尚未登入則進行登入檢查
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    if (isset($_POST['admin_account']) && isset($_POST['password'])) {
        $admin_account = $_POST['admin_account'];
        $password = $_POST['password'];

        if (checkAdminCredentials($admin_account, $password, $conn)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_account'] = $admin_account;
            header("Location: admin.php");
            exit();
        } else {
            $login_error = "帳號或密碼錯誤。";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>後臺登入</title>
    </head>
    <body>
        <h2>後臺管理系統登入</h2>
        <?php if (isset($login_error)) echo "<p style='color:red;'>" . $login_error . "</p>"; ?>
        <form method="post" action="">
            <label for="admin_account">帳號：</label><br/>
            <input type="text" id="admin_account" name="admin_account" required><br/>
            <label for="password">密碼：</label><br/>
            <input type="password" id="password" name="password" required><br/><br/>
            <input type="submit" value="登入">
        </form>
    </body>
    </html>
    <?php
    exit();
}

// 🔹 若登入成功，開始提供後臺管理功能

/**
 * 🔹 新增紀錄
 */
function addRecord($conn, $data) {
    $stmt = $conn->prepare("INSERT INTO data (field1, field2) VALUES (?, ?)");
    $stmt->bind_param("ss", $data['field1'], $data['field2']);
    $stmt->execute();
    $stmt->close();
}

/**
 * 🔹 更新紀錄
 */
function updateRecord($conn, $id, $data) {
    $stmt = $conn->prepare("UPDATE data SET field1=?, field2=? WHERE id=?");
    $stmt->bind_param("ssi", $data['field1'], $data['field2'], $id);
    $stmt->execute();
    $stmt->close();
}

/**
 * 🔹 刪除紀錄
 */
function deleteRecord($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM data WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// 🔹 根據 GET 參數判斷執行哪一個管理功能
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        if (isset($_POST['field1']) && isset($_POST['field2'])) {
            $data = ['field1' => $_POST['field1'], 'field2' => $_POST['field2']];
            addRecord($conn, $data);
            echo "新增成功！";
        }
        ?>
        <h2>新增資料</h2>
        <form method="post" action="?action=add">
            <label for="field1">欄位1：</label><br/>
            <input type="text" id="field1" name="field1" required><br/>
            <label for="field2">欄位2：</label><br/>
            <input type="text" id="field2" name="field2" required><br/><br/>
            <input type="submit" value="新增">
        </form>
        <?php
    } elseif ($_GET['action'] == 'update') {
        if (isset($_GET['id']) && isset($_POST['field1']) && isset($_POST['field2'])) {
            $id = $_GET['id'];
            $data = ['field1' => $_POST['field1'], 'field2' => $_POST['field2']];
            updateRecord($conn, $id, $data);
            echo "更新成功！";
        }
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            ?>
            <h2>更新資料 (ID: <?php echo $id; ?>)</h2>
            <form method="post" action="?action=update&id=<?php echo $id; ?>">
                <label for="field1">欄位1：</label><br/>
                <input type="text" id="field1" name="field1" required><br/>
                <label for="field2">欄位2：</label><br/>
                <input type="text" id="field2" name="field2" required><br/><br/>
                <input type="submit" value="更新">
            </form>
            <?php
        }
    } elseif ($_GET['action'] == 'delete') {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            deleteRecord($conn, $id);
            echo "刪除成功！";
        }
    } else {
        echo "未知的動作。";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>後臺管理系統</title>
    </head>
    <body>
        <h2>歡迎，<?php echo $_SESSION['admin_account']; ?></h2>
        <ul>
            <li><a href="?action=add">新增資料</a></li>
            <li><a href="?action=update&id=1">更新資料 (範例：ID=1)</a></li>
            <li><a href="?action=delete&id=1">刪除資料 (範例：ID=1)</a></li>
        </ul>
    </body>
    </html>
    <?php
}

$conn->close();
?>
