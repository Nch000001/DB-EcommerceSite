<?php
require_once '../../lib/auth_helper.php';
requireLevel(1);
require_once '../../lib/db.php';
$conn = getDBConnection();

if (!isset($_GET['category_id'])) exit;
$category_id = $_GET['category_id'];
$selected_tags = $_POST['selected_tags'] ?? []; // 從 POST 傳來

$tag_type_sql = "SELECT DISTINCT tt.tag_type_id, tt.name as tag_type_name 
                 FROM tag_category tc
                 JOIN tag_type tt ON tc.tag_type_id = tt.tag_type_id
                 WHERE tc.category_id = '$category_id' 
                 ORDER BY tt.name";

$tag_types = $conn->query($tag_type_sql)->fetch_all(MYSQLI_ASSOC);

$html = "";
foreach ($tag_types as $type) {
  $type_id = $type['tag_type_id'];
  $type_name = htmlspecialchars($type['tag_type_name']);

  $tags_result = $conn->query("SELECT tag_id, name FROM tag WHERE tag_type_id = '$type_id' ORDER BY name");

  $html .= "<div class='mb-2'>";
  $html .= "<label class='form-label'><strong>$type_name</strong></label><div class='d-flex flex-wrap gap-3'>";

  while ($tag = $tags_result->fetch_assoc()) {
    $tag_id = htmlspecialchars($tag['tag_id']);
    $tag_name = htmlspecialchars($tag['name']);
    $checked = isset($selected_tags[$type_id]) && $selected_tags[$type_id] === $tag_id ? 'checked' : '';

    $html .= "<div class='form-check'>
      <input class='form-check-input' type='radio' name='tags[$type_id]' value='$tag_id' id='$tag_id' $checked>
      <label class='form-check-label' for='$tag_id'>$tag_name</label>
    </div>";
  }

  $html .= "</div></div>";
}

echo $html;