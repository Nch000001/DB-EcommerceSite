<?php

function getNextProductId(mysqli $conn, string $category_id): string {
    // 取前兩碼作為 prefix
    $prefix = strtoupper(substr($category_id, 0, 2));

    // 找最新一筆相同 prefix 的 product_id
    $stmt = $conn->prepare("
        SELECT product_id FROM product 
        WHERE product_id LIKE CONCAT(?, '%') 
        ORDER BY product_id DESC 
        LIMIT 1
    ");
    $stmt->bind_param('s', $prefix);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // 如果有找到，就從後面三碼遞增
    if ($row) {
        $last_id = $row['product_id'];
        $last_num = (int)substr($last_id, 2);
        $next_num = $last_num + 1;
    } else {
        $next_num = 1;
    }

    return $prefix . str_pad((string)$next_num, 8, '0', STR_PAD_LEFT);
}
