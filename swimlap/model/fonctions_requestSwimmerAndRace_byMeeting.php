<?php
include '../var.prepend.php';
include MODEL.'fonctions.inc.php';

if (isset($_POST['id_meeting']))
    $id_meeting = $_POST['id_meeting'];
else
    $id_meeting = '';

$type = $_POST['type'];

if ($type === 'swimmer') {
    $result = recoverSwimmerMeeting($id_meeting);
} else {
    $result = recoverRaceMeeting($id_meeting);
}

echo json_encode($result);
?>
