<?php
use \Sizzle\Bacon\{
    Text\HTML,
    Database\RecruitingToken,
    Database\User
};
use Sizzle\Bacon\Service\MandrillEmail;

if (logged_in()) {
    $vars = ['user_id', 'token_id'];
    foreach ($vars as $var) {
        $$var = (int) ($_POST[$var] ?? 0);
    }
    $message = $_POST['message'] ?? '';
    $subject = $_POST['subject'] ?? '';

    $success = 'false';
    $data = '';
    if ($user_id > 0 && $token_id > 0) {
        $token = new RecruitingToken($token_id);
        $user = new User($user_id);
        if (isset($token->id, $user->email_address)) {
            $email_message = file_get_contents(__DIR__.'/../../email_templates/stationary.inline.html');
            $email_message = str_replace('{{email}}', $user->email_address, $email_message);
            $message = HTML::to($message).'<br /><br />';
            $imageFile = $token->screenshot();
            if ($imageFile) {
                $message .= '<a href="'.APP_URL.'token/recruiting/'.$token->long_id.'">';
                $message .= '<img src="'.APP_URL.'uploads/'.str_replace(' ', '%20', $imageFile).'" width=700 />';
                $message .= '</a>';
            }
            $message .='<br /><br />Share on ';
            $encodedLink = urlencode(APP_URL.'token/recruiting/'.$token->long_id);
            $linkedInUrl = 'https://www.linkedin.com/shareArticle?mini=true&url='.$encodedLink;
            $linkedInUrl .= '&title='.urlencode($token->job_title).'&summary='.urlencode(HTML::from($token->job_description));
            if ($imageFile) {
                $linkedInUrl .= '&source='.APP_URL.'uploads/'.str_replace(' ', '%20', $imageFile);
            }
            $message .=' <a href="'.$linkedInUrl.'">LinkedIn</a>,';
            $message .=' <a href="https://twitter.com/home?status='.urlencode($token->job_title).'%20'.$encodedLink.'">Twitter</a>';
            $message .=' or <a href="https://www.facebook.com/sharer/sharer.php?u='.$encodedLink.'">Facebook</a>.';
            $message .='<br /><br />';
            $email_message = str_replace('{{message}}', $message, $email_message);
            $mandrill = new MandrillEmail();
            $mandrill->send(
                array(
                    'to'=>array(array('email'=>$user->email_address)),
                    'from_email'=>'token@gosizzle.io',
                    'from_name'=>'S!zzle',
                    'subject'=>$subject,
                    'html'=>$email_message
                )
            );
            $success = 'true';
        } else {
            $data['error'] = 'Invalid user or token';
        }
    } else {
        $data['error'] = 'Invalid or missing user or token';
    }
    header('Content-Type: application/json');
    echo json_encode(array('success'=>$success, 'data'=>$data));
}
