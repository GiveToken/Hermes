<?php
use Sizzle\Bacon\{
    Database\RecruitingCompany,
    Database\RecruitingToken,
    Text\HTML
};

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    if (isset($_POST['recruiting_company_id'])) {
        $recruiting_company_id = (int) $_POST['recruiting_company_id'];
        try {
            $RecruitingCompany = new RecruitingCompany($recruiting_company_id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $response['status'] = "ERROR";
            $response['message'] = $e->getMessage();
            $response['object'] = $e;
        }
    } else {
        $RecruitingCompany = new RecruitingCompany();
    }
    if (!isset($response, $response['status']) || $response['status'] != "ERROR") {
        try {
            // save company info
            $RecruitingCompany->user_id = $RecruitingCompany->user_id ?? $user_id;
            $RecruitingCompany->name = $_POST['company'] ?? '';
            $RecruitingCompany->description = HTML::to($_POST['company_description'] ?? '');
            $RecruitingCompany->website = $_POST['company_website'] ?? '';
            $RecruitingCompany->values = HTML::to($_POST['company_values'] ?? '');
            $RecruitingCompany->facebook = $_POST['company_facebook'] ?? '';
            $RecruitingCompany->linkedin = $_POST['company_linkedin'] ?? '';
            $RecruitingCompany->youtube = $_POST['company_youtube'] ?? '';
            $RecruitingCompany->twitter = $_POST['company_twitter'] ?? '';
            $RecruitingCompany->google_plus = $_POST['company_google_plus'] ?? '';
            $RecruitingCompany->pinterest = $_POST['company_pinterest'] ?? '';
            $RecruitingCompany->save();

            // save company_id to token
            if (isset($_POST['recruiting_token_id'])) {
                $RecruitingToken = new RecruitingToken($_POST['recruiting_token_id'],  'long_id');
                if (isset($RecruitingToken->id)) {
                    $RecruitingToken->recruiting_company_id = $RecruitingCompany->id;
                    $RecruitingToken->save();
                }
            }

            //prepare response
            $response['status'] = "SUCCESS";
            $response['id'] = $RecruitingCompany->id;
            $response['user_id'] = $user_id;
        } catch (Exception $e) {
            error_log($e->getMessage());
            $response['status'] = "ERROR";
            $response['message'] = $e->getMessage();
            $repsonse['object'] = $e;
        }
    }
} else {
    $response['status'] = "ERROR";
    $response['message'] = "Session information unavailable.";
}
header('Content-Type: application/json');
echo json_encode($response);
