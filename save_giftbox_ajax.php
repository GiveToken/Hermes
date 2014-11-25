<?php
include_once 'util.php';
include_once 'config.php';

session_start();
$user_id = $_SESSION['user_id'];
$giftbox_id = null;
if (isset($_POST['giftbox_id'])) {
	$giftbox_id = $_POST['giftbox_id'];
}
$giftbox_name = $_POST['name'];
$css_id = $_POST['css_id'];
$giftbox_name = str_replace("'", "''", $giftbox_name);
$letter_text = $_POST['letter_text'];
$letter_text = str_replace("'", "''", $letter_text);
$wrapper_type = $_POST['wrapper_type'];
$unload_count = $_POST['unload_count'];
$user_agent = $_POST['user_agent'];
$bentos = $_POST['bentos'];
$dividers = $_POST['dividers'];

// If no giftbox_id is passed in, then INSERT a record, otherwise UPDATE the giftbox name and delete the bentos
if (!$giftbox_id) {
	$sql = "INSERT into giftbox (name, css_id, user_id, letter_text, wrapper_type, unload_count, user_agent) "
		."VALUES ('$giftbox_name', '$css_id', $user_id, '$letter_text', '$wrapper_type', $unload_count, '$user_agent')";
	$giftbox_id = insert($sql);
} else {
	execute("UPDATE giftbox SET name = '$giftbox_name', letter_text = '$letter_text', "
		. "wrapper_type = '$wrapper_type', unload_count = $unload_count, "
		. "last_modified = CURRENT_TIMESTAMP() "
		. "WHERE id = $giftbox_id");
	execute("DELETE FROM bento WHERE giftbox_id = $giftbox_id");
	execute("DELETE FROM divider WHERE giftbox_id = $giftbox_id");
}

// Insert bentos
foreach ($bentos as $bento) {
	$image_file_name = str_replace("'", "''", $bento['image_file_name']);
	$download_file_name = str_replace("'", "''", $bento['download_file_name']);
	$sql = "INSERT INTO bento (giftbox_id, css_id, css_width, css_height, css_top, css_left, "
		."image_file_name, image_width, image_height, image_top, image_left, download_file_name, download_mime_type, content_uri, slider_value, "
		."image_top_in_container, image_left_in_container) "
		."VALUES ($giftbox_id, '{$bento['css_id']}', '{$bento['width']}', '{$bento['height']}', '{$bento['top']}', '{$bento['left']}', "
		."'$image_file_name', '{$bento['image_width']}', '{$bento['image_height']}', '{$bento['image_top']}', '{$bento['image_left']}', "
		."'$download_file_name', '{$bento['download_mime_type']}', '{$bento['content_uri']}', {$bento['slider_value']}, "
		. "'{$bento['image_top_in_container']}', '{$bento['image_left_in_container']}')";
	execute($sql);
}
// Insert dividers
foreach ($dividers as $divider) {
	$sql = "INSERT INTO divider (giftbox_id, css_id, css_width, css_height, css_top, css_left) "
		."VALUES ($giftbox_id, '{$divider['css_id']}', '{$divider['width']}', '{$divider['height']}', '{$divider['top']}', '{$divider['left']}')";
	execute($sql);
}

$response['status'] = "SUCCESS";
$response['giftbox_id'] = $giftbox_id;
$response['app_url'] = $app_url;

header('Content-Type: application/json');
echo json_encode($response);