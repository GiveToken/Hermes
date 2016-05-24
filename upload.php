<?php
$success = 'false';
$data = '';
$errors = [];
if (logged_in()) {
    $file_name = $_SERVER['HTTP_X_FILENAME'] ?? false;
    $content_type = null;
    if ($file_name) {
        //echo file_get_contents('php://input');
        $file_data = file_get_contents('php://input');
        $pos = strpos($file_data, "base64");
        if ($pos) {
            $content_type = substr($file_data, 5, $pos-6);
            $file_data =  base64_decode(substr($file_data, $pos + 7));
        }
        file_put_contents(FILE_STORAGE_PATH.$file_name, $file_data);
        $success = 'true';
    }
}
if (!empty($errors)) {
    $data['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
?>
