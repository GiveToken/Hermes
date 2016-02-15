<?php
use \Sizzle\{
    EventLogger,
    User
};

function get_post($index)
{
    if (isset($_POST[$index])) {
        return $_POST[$index];
    } else {
        return null;
    }
}

try {
    $user_id = get_post('user_id');
    $user = new User($user_id);

    $user->first_name = get_post('first_name');
    $user->last_name = get_post('last_name');
    $user->email_address = get_post('email');
    $user->location = get_post('location');
    $user->company = get_post('company');
    $user->position = get_post('position');
    $user->about = get_post('about');
    $user->username = get_post('username');
    $user->user_group = get_post('group');
    $user->receive_token_notifications = get_post('receive_token_notifications');

    // password
    if (isset($_POST['password']) && strlen($_POST['password']) > 0) {
        $user->password = password_hash($user->password, PASSWORD_DEFAULT);
    }

    // admin
    if (isset($_POST['admin'])) {
        $user->admin = $_POST['admin'];
    } else {
        $user->admin = "N";
    }

    // group admin
    if (strlen($user->user_group) > 0 AND isset($_POST['group_admin'])) {
        $user->group_admin = $_POST['group_admin'];
    } else {
        $user->group_admin = "N";
    }

    $user->save();

    $response['status'] = "SUCCESS";
    $response['user_id'] = $user->getId();
} catch (Exception $e) {
    $response['status'] = "ERROR";
    $response['message'] = $e->getMessage();
    $repsonse['object'] = $e;
}
header('Content-Type: application/json');
echo json_encode($response);
?>
