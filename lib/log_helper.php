<?php
function log_admin_action($conn, $admin_id, $action_type, $target_table, $target_id, $details = '') { // help for admin_action_log
    if (!$conn || !$admin_id || !$action_type || !$target_table) return;

    $stmt = $conn->prepare("
        INSERT INTO admin_action_log (admin_id, action_type, target_table, target_id, details)
        VALUES (?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        error_log("Prepare failed in log_admin_action: " . $conn->error);
        return;
    }

    $stmt->bind_param("sssss", $admin_id, $action_type, $target_table, $target_id, $details);
    $stmt->execute();
}
