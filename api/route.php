<?php
use Sizzle\API;
use Sizzle\Bacon\Database\User;

// default response
$success = false;
$data = '';
$errors = array();

// Parse URI
$pieces = explode('?', $_SERVER['REQUEST_URI']);
$endpoint = $pieces[0];
$endpoint_parts = explode('/', $endpoint);
if (isset($pieces[1])) {
    $gets = $pieces[1];
    $gets = explode('&', $gets);
    $get_parts = array();
    foreach ($gets as $get) {
        $parts = explode('=', $get);
        $get_parts[$parts[0]] = $parts[1];
    }
}

// validate user
if (isset($get_parts['api_key'])) {
    $user = (new User())->fetch($get_parts['api_key'], 'api_key');
    if ((int) $user->id <= 0) {
        $errors[] = 'Invalid API Key';
    }
} else {
    $errors[] = 'Invalid User';
}

if (empty($errors)) {
    $api = new API($endpoint);

    // validate endpoint
    if ($api->valid) {
        $result = $api->process($user);
        $success = $result['success'];
        $data = $result['data'];
        $errors = $result['errors'];
    } else {
        $errors[] = 'Invalid Endpoint';
    }
}

header('Content-Type: application/json');
echo json_encode(
    array(
    'success'=>$success,
    'data'=>$data,
    'errors'=>$errors
    )
);
