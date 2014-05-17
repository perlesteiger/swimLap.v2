<?php
    $seasons = recoverSeason();
    $swimmers = recoverSwimmer();
    $meetings = recoverCompetition();
    $races = recoverRace();
    
    include VIEW.'search.php';
?>

