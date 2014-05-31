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
                $repart= substr ( $line->repartitions, 1 , strlen($line->repartitions)-2 );
                $percent= explode(',', $repart);
                //enregistrer chaque temps
                for ($i = 0; $i < $line->length; $i++) {
                    $tab[] = array('round'=>$line->round_name, 'percent'=>$percent[$i], 'swimmer'=>$line->swimmer_firstname.' '.$line->swimmer_lastname, 'race'=>'');
                }
            }    
        //recherche pour un nageur
        } else if (!empty($id_swimmer)) {
            $query = 'SELECT race_id, race_name, round_name, repartitions, length FROM ps_get_repartition_by_swimmer('.$id_meeting.','.$id_swimmer.') ORDER BY race_name DESC';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

            while ($line = pg_fetch_object($result)) {
                //correspondance tableau postgres/tableau php
                $repart= substr ( $line->repartitions, 1 , strlen($line->repartitions)-2 );
                $percent= explode(',', $repart);
                //enregistrer chaque temps
                for ($i = 0; $i < $line->length; $i++) {
                    $tab[] = array('round'=>$line->round_name, 'percent'=>$percent[$i], 'swimmer'=>'', 'race'=>$line->race_name, 'race_id'=>$line->race_id);
                }
            }  
        //recherche pour le premier affichage
        } else if (empty($id_race) && empty($id_swimmer)) {
            $query = 'SELECT race_id, race_name, round_name, repartitions, length FROM ps_get_repartition_by_meeting('.$id_meeting.') ORDER BY race_name DESC';
            $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());
            while ($line = pg_fetch_object($result)) {
                //correspondance tableau postgres/tableau php
                $repart= substr ( $line->repartitions, 1 , strlen($line->repartitions)-2 );
                $percent= explode(',', $repart);
                //enregistrer chaque temps
                for ($i = 0; $i < $line->length; $i++) {
                    $tab[] = array('round'=>$line->round_name, 'percent'=>$percent[$i], 'swimmer'=>'', 'race'=>$line->race_name, 'race_id'=>$line->race_id);
                }
            }
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
        
        //recuperation des competitions
        $meetings = recoverCompetition();
        
        //ouverture connexion bdd
        $dbconn = connect_bdd();
        
        //recherche pour un type de course
        if (!empty($id_race) && empty($id_swimmer)) {
            
            //pour chaque meeting
            foreach ($meetings as $meet) {
                $meet = explode('|', $meet);
            
                $query = 'SELECT performance FROM ps_get_performances_by_meeting('.$meet[4].') WHERE race_id ='.$id_race.'';
                $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

                if(!empty($result)) {
                    //nbr de perf//somme des perf
                    $tmp =0; $som=0;
                    while ($line = pg_fetch_object($result)) {
                        $som = $som + $line->performance;      
                        $tmp++;
                    }

                    //verification que la competition a des resultats
                    if ($tmp !== 0) {
                        //calcul du pourcentage
                        $percent = round($som/$tmp, 2);

                        //remplissage resultat
                        $tab[] = array('competition'=>$meet[0], 'percent'=>$percent);
                    }
                }
            }
        
        //recherche pour un nageur toute course
        } else if (!empty($id_swimmer) && empty($id_race)) {

            //pour chaque meeting
            foreach ($meetings as $meet) {
                $meet = explode('|', $meet);
            
                $query = 'SELECT performance FROM ps_get_performances_by_meeting('.$meet[4].') WHERE swimmer_id ='.$id_swimmer.'';
                $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

                    //nbr de perf//somme des perf
                    $tmp =0; $som=0;
                    while ($line = pg_fetch_object($result)) {
                        $som = $som + $line->performance;      
                        $tmp++;
                    }

                    //verification que la competition a des resultats
                    if ($tmp !== 0) {
                        //calcul du pourcentage
                        $percent = round($som/$tmp, 2);

                        //remplissage resultat
                        $tab[] = array('competition'=>$meet[0], 'percent'=>$percent);
                    }
            }
        
        //recherche pour premier affichage
        } else if (empty($id_race) && empty($id_swimmer)) {

            //pour chaque meeting
            foreach ($meetings as $meet) {
                $meet = explode('|', $meet);

                $query = 'SELECT performance FROM ps_get_performances_by_meeting('.$meet[4].')';
                $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

                if(!empty($result)) {
                    //nbr de perf//somme des perf
                    $tmp =0; $som=0;
                    while ($line = pg_fetch_object($result)) {
                        $som = $som + $line->performance;      
                        $tmp++;
                    }

                    //verification que la competition a des resultats
                    if ($tmp !== 0) {
                        //calcul du pourcentage
                        $percent = round($som/$tmp, 2);

                        //remplissage resultat
                        $tab[] = array('competition'=>$meet[0], 'percent'=>$percent);
                    }
                }
            }
        
        //recherche pour un nageur et une course
        } else if (!empty($id_race) && !empty($id_swimmer)) {

            //pour chaque meeting
            foreach ($meetings as $meet) {
                $meet = explode('|', $meet);
            
                $query = 'SELECT performance FROM ps_get_performances_by_meeting('.$meet[4].') WHERE swimmer_id ='.$id_swimmer.' AND race_id ='.$id_race.'';
                $result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

                if(!empty($result)) {
                    //nbr de perf//somme des perf
                    $tmp =0; $som=0;
                    while ($line = pg_fetch_object($result)) {
                        $som = $som + $line->performance;      
                        $tmp++;
                    }

                    //verification que la competition a des resultats
                    if ($tmp !== 0) {
                        //calcul du pourcentage
                        $percent = round($som/$tmp, 2);

                        //remplissage resultat
                        $tab[] = array('competition'=>$meet[0], 'percent'=>$percent);
                    }
                }
            }
            
        }
        
        break;
}

// Ferme la connexion
pg_close($dbconn);  

echo json_encode($tab);
?>