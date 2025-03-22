<?php
session_start();       // 啟動 Session
session_unset();       // 清除所有 Session 變數
session_destroy();     // 銷毀 Session
header("Location: ecommerce_admin_login.php"); // 導回登入頁
exit;
?>