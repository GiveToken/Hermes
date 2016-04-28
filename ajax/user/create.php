<?php
use \Sizzle\Bacon\{
    Database\User,
    Database\UserMilestone,
    Service\PipedriveClient
};

date_default_timezone_set('America/Chicago');

$success = 'false';
$data = '';

$vars = array('signup_email', 'token_id');
foreach ($vars as $var) {
    $$var = $_POST[$var] ?? '';
}

if (filter_var($signup_email, FILTER_VALIDATE_EMAIL)) {
    if ((new User())->exists($signup_email)) {
        $data['errors'] = "The email address $signup_email has already been registered.";
    } else {
        $user = new User();
        $user->email_address = $signup_email;
        $user->activation_key = md5(uniqid(mt_rand(), false));
        $user->save();

        // transfer token if it exists
        $rows_affected = update(
            "UPDATE recruiting_token
             SET user_id = ".$user->id."
             WHERE long_id = '$token_id'
             LIMIT 1"
        );
        if (1 == $rows_affected) {
            $type = 'emailtoken';
        } else {
            $type = 'nopassword';
        }

        // response url
        // http://gosizzle.local/activate?uid=131&key=1234&type=emailtoken
        $url = APP_URL.'activate?uid=' . $user->id;
        $url .= '&key=' . $user->activation_key . '&type=' . $type;
        $data['url'] = $url;
        $success = 'true';

        // milestone signup
        $UserMilestone = new UserMilestone($user->id, 'Signup');

        // Create Free trial in Pipedrive
        $pipedriveClient = new PipedriveClient(PIPEDRIVE_API_TOKEN);
        $pipedriveClient->createFreeTrial($user);
    }
}

header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
