<?php
use \Sizzle\Bacon\Database\{
    Organization,
    User
};

$success = 'false';
$data = '';
$errors = [];
if (logged_in()) {
    $params = [
        'user',
        'organization'
    ];
    foreach ($params as $param) {
        $$param = $_POST[$param] ?? null;
        if (!isset($$param) || 0 > (int) $$param) {
            $errors[] = $param.' cannot be left blank';
        }
    }
    if (empty($errors)) {
        $user = new User($user);
        if (!isset($user->id)) {
            $errors[] = 'invalid user';
        }
        $organization = new Organization($organization);
        if (!isset($organization->id)) {
            $errors[] = 'invalid organization';
        }
    }
    if (empty($errors)) {
        $user->organization_id = $organization->id;
        $user->save();

        $success = 'true';
    }
}
if (!empty($errors)) {
    $data['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
