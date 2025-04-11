<?php
require_once '../../lib/db.php';

header('Content-Type: application/json');
$category_id = $_GET['category_id'] ?? '';

if (!$category_id) {
    echo json_encode([]);
    exit;
}

$conn = getDBConnection();

$sql = "
SELECT tt.tag_type_id, tt.name AS tag_type_name, t.tag_id, t.name AS tag_name
FROM tag_type tt
JOIN tag_category tc ON tt.tag_type_id = tc.tag_type_id
LEFT JOIN tag t ON t.tag_type_id = tt.tag_type_id
WHERE tc.category_id = ?
ORDER BY tt.name, t.name
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $category_id);
$stmt->execute();
$result = $stmt->get_result();

$groupedTags = [];

while ($row = $result->fetch_assoc()) {
    $typeId = $row['tag_type_id'];
    if (!isset($groupedTags[$typeId])) {
        $groupedTags[$typeId] = [
            'tag_type_id' => $typeId,
            'tag_type_name' => $row['tag_type_name'],
            'tags' => []
        ];
    }
    if ($row['tag_id']) {
        $groupedTags[$typeId]['tags'][] = [
            'tag_id' => $row['tag_id'],
            'tag_name' => $row['tag_name']
        ];
    }
}

echo json_encode(array_values($groupedTags));
