<?php
use Sizzle\Bacon\Database\{
    Support
};
use Sizzle\Bacon\Service\MandrillEmail;

$status = 'ERROR';
if (isset($_POST['email'], $_POST['message'])
    && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
) {
    $vars = array('email', 'message', 'name', 'subject');
    foreach ($vars as $var) {
        $$var = $_POST[$var] ?? '';
    }
    $from = $name . ' (' . $email . ')';
    $subject = '' == $subject ? 'Message from '.$from : $subject;
    $message .= "\r\n\r\n - $from";
    if (ENVIRONMENT != 'production') {
        $to = 'support+dev@gosizzle.io';
    } else {
        $to = 'support@gosizzle.io';
    }
    $mandrill = new MandrillEmail();
    $mandrill->send(
        array(
            'to'=>array(array('email'=>$to)),
            'from_email'=>'support@gosizzle.io',
            'from_name'=>'S!zzle',
            'subject'=>$subject,
            'html'=>$message,
            'headers'=>array('Reply-To'=>$email)
        )
    );
    $status = 'SUCCESS';
    (new Support())->create($email, $message);
}
header('Content-Type: application/json');
echo json_encode(array('status' => $status));

?>
