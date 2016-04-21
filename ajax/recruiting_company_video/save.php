<?php
use Sizzle\Bacon\Database\RecruitingCompany;
use Sizzle\Bacon\Database\RecruitingCompanyVideo;
use Sizzle\Bacon\Database\RecruitingToken;

date_default_timezone_set('America/Chicago');

if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
    if (isset($_POST['recruiting_company_id'])) {
        if (isset($_POST['source'], $_POST['source_id'])) {
            if (in_array($_POST['source'], ['youtube','vimeo'])) {
                // see if company belongs to this user
                $RecruitingCompany = new RecruitingCompany($_POST['recruiting_company_id'], 'id');
                    try {
                        // Save the token video
                        $recruiting_company_video = new RecruitingCompanyVideo();
                        $id = $recruiting_company_video->create($_POST['recruiting_company_id'], $_POST['source'], $_POST['source_id']);
                        $response['status'] = "SUCCESS";
                        $response['id'] = $recruiting_company_video->id;
                    } catch (Exception $e) {
                        error_log($e->getMessage());
                        $response['status'] = "ERROR";
                        $response['message'] = $e->getMessage();
                        $repsonse['object'] = $e;
                    }
            } else {
                $response['status'] = "ERROR";
                $response['message'] = "Must be a YouTube or Vimeo video.";
            }
        } else {
            $response['status'] = "ERROR";
            $response['message'] = "Source and source id required.";
        }
    } else {
        $response['status'] = "ERROR";
        $response['message'] = "Recruiting token id required.";
    }
} else {
    $response['status'] = "ERROR";
    $response['message'] = "User must be logged in.";
}
header('Content-Type: application/json');
echo json_encode($response);
