<?php
require_once __DIR__ . '/../../../include/prepend.inc.php';
/*if (!isset($_SESSION['github_token'])){
    header('HTTP/1.0 403 Forbidden');
    die;
}*/
$errors = [];
if (!isset($_POST['vote']) || ctype_digit($_POST['session_id']) === false) {
    $errors['vote'] = 'Vous n\'avez pas mis de note.';
}
if (!isset($_POST['session_id']) || ctype_digit($_POST['session_id']) === false) {
    $errors['session_id'] = 'Hmm... Il manque une information';
}

if ($errors !== []) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    die;
}

