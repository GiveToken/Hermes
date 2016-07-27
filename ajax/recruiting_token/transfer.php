<?php
use \Sizzle\Bacon\Database\{
    RecruitingCompany,
    RecruitingToken
};

$success = 'false';
$data = '';
if (logged_in() && isset($_POST['token_id'], $_POST['old_user_id'], $_POST['new_user_id'])) {
    $long_id = $_POST['token_id'];
    $old_user_id = (int) $_POST['old_user_id'];
    $new_user_id = (int) $_POST['new_user_id'];
    if (!empty($long_id) && 0 != $old_user_id && 0 != $new_user_id) {
        // get recruiting token
        $RecruitingToken = new RecruitingToken($long_id, 'long_id');
        if (isset($RecruitingToken->user_id) && $RecruitingToken->user_id == $old_user_id) {
            // get company and check that it belongs to new user's org or this is admin user
            $RecruitingCompany = new RecruitingCompany($RecruitingToken->recruiting_company_id);
            if (isset($RecruitingCompany->organization_id)) {
                $user = new User($new_user_id);
                if ($RecruitingCompany->organization_id != $user->organization_id && !is_admin()) {
                    $data['error'] = "This company ({$RecruitingCompany->name}) belongs to a diffferent organization."
                } elseif (is_admin()) {
                    $RecruitingCompany->organization_id = $user->organization_id;
                    $RecruitingCompany->save();
                }
            }
            if (!isset($data['error'])) {
                $RecruitingToken->user_id = $new_user_id;
                $RecruitingToken->save();
                $success = 'true';
            }
        }
    }
}
header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
