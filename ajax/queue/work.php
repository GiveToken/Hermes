<?php
use Sizzle\Bacon\Database\{
    RecruitingToken,
    TokenQueue
};

$success = 'false';
$data = '';
if (logged_in()) {
    // take in queue id
    $queueID = (int) ($_POST['id'] ?? 0);

    if ($queueID > 0) {
        // get queue information
        $item = new TokenQueue($queueID);

        if (isset($item->id)) {
            // create user if needed
            // eventually this will be done asynchronously
            $user = (new User())->fetch($item->email_address) ?? new User();
            if (!isset($user->id)) {
                $user->email_address = $item->email_address;
                $user->save();
            }

            // process info into token
            // eventually this will be done asynchronously
            $token = new RecruitingToken();
            $token->job_title = $item->subject;
            $token->job_description = $item->body;
            $token->user_id = $user->id;
            $token->save();

            // mark queue item worked
            $item->markWorked();

            // return long_id
            $data = array('token'=>$token->long_id);
        }
    }
}
header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
