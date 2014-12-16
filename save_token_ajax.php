<?php
include_once 'config.php';
include_once 'Token.class.php';

session_start();
$user_id = $_SESSION['user_id'];

try {

	// Save the token
	$token = new Token();
	$token->init((object)$_POST);
	$token->user_id = $user_id;
	$token->save();

	$response['status'] = "SUCCESS";
	$response['giftbox_id'] = $token->id;
	$response['app_url'] = $app_url;
} catch (Exception $e) {
	$response['status'] = "ERROR";
	$response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);