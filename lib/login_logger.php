<?php
function logLoginAction(mysqli $conn, string $admin_id, string $action) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $stmt = $conn->prepare("
        INSERT INTO admin_login_log (admin_id, action, ip_address)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $admin_id, $action, $ip);
    $stmt->execute();
}
