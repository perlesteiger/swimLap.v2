<?php
include '../var.prepend.php';
include MODEL.'fonctions.inc.php';

$type = $_POST['type_form'];

switch ($type) {
    case 'general':
        $new_name = $_POST['general_club'];
        $id = $_POST['general_id'];
        
        $update_name = updateClub($new_name, $id);

        //ajouter condition si non reussi
        header("Location: ".VIEW."result.php?form=general&new=".$new_name);

        break;

    case 'swimmer':
        $lastname = $_POST['swimmer_name'];
        $firstname = $_POST['swimmer_firstname'];
        $id = $_POST['swimmer_id'];
        $birth = $_POST['swimmer_birth'];
        $sexe = $_POST['select_sexe'];
        
        $add_swimmer = addSwimmer($lastname, $firstname, $id, $sexe, $birth);

        //ajouter condition si non reussi
        header("Location: ".VIEW."result.php?form=swimmer&name=".$lastname."&first=".$firstname."&id=".$id."&birth=".$birth."&genre=".$sexe);

        break;
    case 'record':
        $record = $_POST['record_new'];
        $id_swimmer = $_POST['record_swimmer'];
        $race = $_POST['record_swim'];
        $pool = $_POST['select_pool'];
        
        $add_record = addRecord($record, $id_swimmer, $race, $pool);

        //ajouter condition si non reussi
        header("Location: ".VIEW."result.php?form=record&name=".$id_swimmer."&record=".$record."&pool=".$pool."&race=".$race);

        break;
    case 'competition':
        $id = $_POST['competition_id'];
        $begin = $_POST['competition_begin'];
        $name = $_POST['competition_name'];
        $end = $_POST['competition_end'];
        $city = $_POST['competition_city'];
        $pool = $_POST['select_pool'];
        $season = $_POST['competition_season'];
        
        $add_competition = addCompetition($id, $name, $begin, $end, $city, $pool, $season);

        //ajouter condition si non reussi
        header("Location: ".VIEW."result.php?form=competition&name=".$name."&begin=".$begin."&end=".$end."&city=".$city);

        break;
    default:
        break;
}
?>