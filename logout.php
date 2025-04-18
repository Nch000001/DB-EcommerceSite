<?php
session_start();

// 是否是後台管理員，先記起來
$isAdmin = isset($_SESSION['super_user_id']);

if ($isAdmin) {
    require_once 'lib/db.php';
    require_once 'lib/login_logger.php';
    $conn = getDBConnection();
    logLoginAction($conn, $_SESSION['super_user_id'], 'logout');
}

session_unset();    
session_destroy();  

if ($isAdmin) {
    header("Location: /ecommerce/index.php");
} else {
    header("Location: /ecommerce/index.php");
}

exit;
