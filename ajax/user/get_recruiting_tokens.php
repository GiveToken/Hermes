<?php
use \Sizzle\Bacon\{
    Text\HTML,
    Database\RecruitingToken
};

// collect id
$user_id = (int) ($endpoint_parts[4] ?? '');

$success = 'false';
$data = '';
if (logged_in()) {
    /*$token = new RecruitingToken($id, 'long_id');
    if (isset($token->id)) {
        $success = 'true';
        $data = $token;
    }*/
    $tokens = execute_query(
        "SELECT long_id, job_title, id
         FROM recruiting_token
         WHERE user_id = '$user_id'
         ORDER BY job_title, long_id"
    )->fetch_all(MYSQLI_ASSOC);
    $data = $tokens;
}
header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
