<?php
use \Sizzle\Bacon\Database\ExperimentNote;

$success = 'false';
$data = '';
$errors = [];
if (logged_in()) {
    $ExperimentNote = new ExperimentNote();
    $params = [
        'note',
        'experiment'
    ];
    foreach ($params as $param) {
        $$param = $_POST[$param] ?? null;
    }
    if (!isset($note) || '' == $note) {
        $errors[] = 'Note cannot be left blank';
    }
    if (!isset($experiment) || 0 >= (int) $experiment) {
        $errors[] = 'Invalid experiment';
    }
    if (empty($errors)) {
        $ExperimentNote->user_id = $_SESSION['user_id'];
        $ExperimentNote->note = $note;
        $ExperimentNote->experiment_id = $experiment;
        $ExperimentNote->save();

        $success = 'true';
        $data = array('id'=>$ExperimentNote->id);
    }
}
if (!empty($errors)) {
    $data['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
