<?php
include '../var.prepend.php';
include MODEL.'fonctions.inc.php';

$id_meeting = $_POST['id_meeting'];

$type = $_POST['type'];

if ($type === 'swimmer') {
    $result = recoverSwimmerMeeting($id_meeting);
} else {
    $result = recoverRaceMeeting($id_meeting);
}

echo json_encode($result);
?>
