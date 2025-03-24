<?php

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

