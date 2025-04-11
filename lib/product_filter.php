<?php
// product_filter.php 改成函式，傳入 $conn 和 $_GET 參數
function getProductFilterResults($conn, $category_id, $selected_tags) {
    $tag_types = [];

    if ($category_id) {
        $tag_type_sql = "SELECT tt.tag_type_id, tt.name AS tag_type_name
                        FROM tag_type tt
                        JOIN tag_category tc ON tt.tag_type_id = tc.tag_type_id
                        WHERE tc.category_id = ?";
        $stmt = $conn->prepare($tag_type_sql);
        $stmt->bind_param("s", $category_id);
        $stmt->execute();
        $tag_type_result = $stmt->get_result();
        while ($row = $tag_type_result->fetch_assoc()) {
            $tag_id_sql = "SELECT tag_id, name FROM tag WHERE tag_type_id = ?";
            $tag_stmt = $conn->prepare($tag_id_sql);
            $tag_stmt->bind_param("s", $row['tag_type_id']);
            $tag_stmt->execute();
            $tag_result = $tag_stmt->get_result();
            $tags = [];
            while ($tag = $tag_result->fetch_assoc()) {
                $tags[] = $tag;
            }
            $row['tags'] = $tags;
            $tag_types[] = $row;
        }
    }

    // 商品查詢
    if (!empty($selected_tags)) {
        $product_sql = "
        SELECT p.*
        FROM product p
        JOIN product_tag pt ON p.product_id = pt.product_id
        WHERE p.category_id = ?
        AND pt.tag_id IN (" . implode(',', array_fill(0, count($selected_tags), '?')) . ")
        GROUP BY p.product_id
        HAVING COUNT(DISTINCT pt.tag_id) = ?";

        $params = array_merge([$category_id], $selected_tags, [count($selected_tags)]);
        $types = str_repeat("s", count($selected_tags) + 1) . "i";

    } else {
        $product_sql = "SELECT * FROM product WHERE category_id = ?";
        $params = [$category_id];
        $types = "s";
    }

    $stmt = $conn->prepare($product_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $product_result = $stmt->get_result();

    return [$tag_types, $product_result];
}
