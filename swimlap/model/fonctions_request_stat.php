<?php
include '../var.prepend.php';
include MODEL.'fonctions.inc.php';

//recuperation des donnees
$type = $_POST['style'];
if (isset($_POST['nageur']))
    $id_swimmer = $_POST['nageur'];
if (isset($_POST['compet']))
    $id_meeting = $_POST['compet'];
if (isset($_POST['course']))
    $id_race = $_POST['course'];
if (isset($_POST['saison']))
    $id_season = $_POST['saison'];

//ouverture connexion bdd
$dbconn = connect_bdd();

switch ($type) {
    case 'repartition':
        $tab=array();
        //recherche our un type de course
        if (!empty($id_race)) {
            $query = 'SELECT swimmer_lastname, swimmer_firstname, round_name, repartitions, length FROM ps_get_repartition_by_race('.$id_meeting.','.$id_race.')';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

            while ($line = pg_fetch_object($result)) {
                //correspondance tableau postgres/tableau php
                $repart= substr ( $line->repartitions, 1 , strlen($line->repartitions)-1 );
                $percent= explode(',', $repart);
                //enregistrer chaque temps
                for ($i = 0; $i < $line->length; $i++) {
                    $tab[] = array('round'=>$line->round_name, 'percent'=>$percent[$i], 'swimmer'=>$line->swimmer_firstname.' '.$line->swimmer_lastname, 'race'=>'', 'length'=>$line->length);
                }
            }    
        //recherche pour un nageur
        } else if (!empty($id_swimmer)) {
            $query = 'SELECT race_id, race_name, round_name, repartitions, length FROM ps_get_repartition_by_swimmer('.$id_meeting.','.$id_swimmer.') ORDER BY race_name DESC';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

            while ($line = pg_fetch_object($result)) {
                print_r($line);
                //correspondance tableau postgres/tableau php
                $repart= substr ( $line->repartitions, 1 , strlen($line->repartitions)-1 );
                $percent= explode(',', $repart);
                //enregistrer chaque temps
                for ($i = 0; $i < $line->length; $i++) {
                    $tab[] = array('round'=>$line->round_name, 'percent'=>$percent[$i], 'swimmer'=>'', 'race'=>$line->race_name, 'length'=>$line->length, 'race_id'=>$line->race_id);
                }
            }  
        //recherche pour le premier affichage
        } else if (empty($id_race) && empty($id_swimmer)) {
            $query = 'SELECT race_id, race_name, round_name, repartitions, length FROM ps_get_repartition_by_meeting('.$id_meeting.') ORDER BY race_name DESC';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());
            while ($line = pg_fetch_object($result)) {
                //correspondance tableau postgres/tableau php
                $repart= substr ( $line->repartitions, 1 , strlen($line->repartitions)-1 );
                $percent= explode(',', $repart);
                //enregistrer chaque temps
                for ($i = 0; $i < $line->length; $i++) {
                    $tab[] = array('round'=>$line->round_name, 'percent'=>$percent[$i], 'swimmer'=>'', 'race'=>$line->race_name, 'length'=>$line->length, 'race_id'=>$line->race_id);
                }
            }

            // Ferme la connexion
            pg_close($dbconn);  
        }
        break;

    case 'performance':
        //round-race-percent-id-swimmer ->faire moyenne
        $tab=array();
        
        //recherche pour un type de course
        if (!empty($id_race) && empty($id_swimmer)) {
            $query = 'SELECT swimmer_name, round_name, performance FROM ps_get_performances_by_meeting('.$id_meeting.') WHERE race_id ='.$id_race.' ORDER BY swimmer_id ASC';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

            while ($line = pg_fetch_object($result)) {
                $tab[] = array('round'=>$line->round_name, 'percent'=>$line->performance, 'swimmer'=>$line->swimmer_name);
            }          
        //recherche pour un nageur
        } else if (!empty($id_swimmer) && empty($id_race)) {
            $query = 'SELECT race_id, race_name, round_name, performance FROM ps_get_performances_by_meeting('.$id_meeting.') WHERE swimmer_id ='.$id_swimmer.' ORDER BY race_name DESC';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

            while ($line = pg_fetch_object($result)) {
                $tab[] = array('round'=>$line->round_name, 'percent'=>$line->performance, 'race'=>$line->race_name, 'race_id'=>$line->race_id);
            }  
        //recherche pour le premier affichage    
        } else if (empty($id_race) && empty($id_swimmer)) {
            $query = 'SELECT race_id, race_name, round_name, performance, swimmer_name FROM ps_get_performances_by_meeting('.$id_meeting.') ORDER BY race_name DESC';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

            while ($line = pg_fetch_object($result)) {
                $tab[] = array('round'=>$line->round_name, 'percent'=>$line->performance, 'race'=>$line->race_name, 'race_id'=>$line->race_id, 'swimmer'=>$line->swimmer_name);
            }  
        }
        break;
        
    case 'planification':
        $tab=array();
        if (!empty($id_race) && empty($id_swimmer)) {

            $tab[] = array('competition'=>'Meeting Régional N°1 Minimes et plus', 'percent'=>'77');
            $tab[] = array('competition'=>'Rencontre Poussins N°1', 'percent'=>'82');
            $tab[] = array('competition'=>'Championnat Régional Interclubs TC', 'percent'=>'94');
            $tab[] = array('competition'=>'4e Etape de la Coupe du monde 2013', 'percent'=>'79');
         
        } else if (!empty($id_swimmer) && empty($id_race)) {

            $tab[] = array('competition'=>'Meeting Régional N°1 Minimes et plus', 'percent'=>'77');
            $tab[] = array('competition'=>'Championnat Régional Interclubs TC', 'percent'=>'94');
            $tab[] = array('competition'=>'4e Etape de la Coupe du monde 2013', 'percent'=>'79');
            
        } else if (empty($id_race) && empty($id_swimmer)) {

            $tab[] = array('competition'=>'Meeting Régional N°1 Minimes et plus', 'percent'=>'77');
            $tab[] = array('competition'=>'Rencontre Poussins N°1', 'percent'=>'82');
            $tab[] = array('competition'=>'Championnat Régional Interclubs TC', 'percent'=>'94');
            $tab[] = array('competition'=>'4e Etape de la Coupe du monde 2013', 'percent'=>'79');
            $tab[] = array('competition'=>'Meeting Benjamins N°2', 'percent'=>'88');
            $tab[] = array('competition'=>'Rencontre Poussins N°5', 'percent'=>'62');
            
        } else if (!empty($id_race) && !empty($id_swimmer)) {

            $tab[] = array('competition'=>'Meeting Régional N°1 Minimes et plus', 'percent'=>'77');
            $tab[] = array('competition'=>'Rencontre Poussins N°1', 'percent'=>'82');
            $tab[] = array('competition'=>'Championnat Régional Interclubs TC', 'percent'=>'94');
            $tab[] = array('competition'=>'4e Etape de la Coupe du monde 2013', 'percent'=>'79');
            $tab[] = array('competition'=>'Meeting Benjamins N°2', 'percent'=>'99');
            $tab[] = array('competition'=>'Rencontre Poussins N°5', 'percent'=>'63');
            $tab[] = array('competition'=>'Championnats d Europe en petit bassin', 'percent'=>'68');
            $tab[] = array('competition'=>'16e Meeting du Luxembourg', 'percent'=>'81');
            
        }
        
        break;

    default:
        break;
}

echo json_encode($tab);
?>