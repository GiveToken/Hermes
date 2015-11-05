<?php
use \GiveToken\User;
use \GiveToken\EventLogger;

require_once 'config.php';
require_once 'util.php';
_session_start();

$response['status'] = 'ERROR';
if(isset($_SESSION['user_id'])) {
    $user = new User($_SESSION['user_id']);
} else if(isset($_POST['email']) && User::exists($_POST['email'])) {
    $user = User::fetch($_POST['email']);
} else {
    $user = null;
    $response['status'] = 'NO_USER';
}

if($user != null && isset($_POST['access_token'])) {
    $access_token = $_POST['access_token'];
    $user->update_token($access_token, $user->getId());
    $response['status'] = 'SUCCESS';
}

header('Content-Type: application/json');
echo json_encode($response);
