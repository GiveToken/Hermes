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
            // get city and check that it belongs to new user or this admin user
            $RecruitingCompany = new RecruitingCompany($RecruitingToken->recruiting_company_id);
            if (isset($RecruitingCompany->user_id)) {
                switch ($RecruitingCompany->user_id) {
                case $new_user_id;
                    // no city changes to make
                    break;
                default:
                    if (count($RecruitingCompany->getTokens()) == 1) {
                        // only this token assigned to company
                        // so okay to transfer
                        $RecruitingCompany->user_id = $new_user_id;
                        $RecruitingCompany->save();
                    } else {
                        //something is fubar
                        $data = array('error'=>'Company already assigned to a different user');
                    }
                    break;
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
