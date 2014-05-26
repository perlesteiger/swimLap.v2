<?php

// Fonction principale - Traitement du fichier FFNEX
function XMLParser($file) {
    // On vérifie que le fichier existe
    if (file_exists($file)) {
        // On charge le fichier pour que php comprenne que c'est du xml (pour info c'est un type SimpleXMLElement)
        $xml = simplexml_load_file($file);
        //On récupère le type du fichier
        $type = $xml->attributes()->type;
        // Si le fichier est bien du type "performance"
        if ($type == 'performance') {
            $dbconn = connect_bdd();

            //Récupération des données pour l'insertion du meeting
            $meet = $xml->MEETS->MEET->attributes();
            $seasonID = getSeasonID($meet->startdate);
            $pool = $xml->MEETS->MEET->POOL->attributes()->size;
            //Appel de la fonction d'insertion du meeting
            insert_meeting($meet, $seasonID, $pool);

            //Récupération des données pour l'insertion des nageurs
            $all_swimmers = $xml->MEETS->MEET->SWIMMERS->children();
            sortClubSwimmers($all_swimmers);

            //Récupération des nageurs enregistrés pour ne récupérer que leurs résultats
            $swimmersID = getSwimmers();
            $results = $xml->MEETS->MEET->RESULTS->children();

            // Parcours des RESULT - Ajout des événements et ajout des résultats dans la base
            foreach ($swimmersID as $swi_id) {
                foreach ($results as $result) {
                    // On ne traite un result que s'il s'agit d'une course individuelle
                    if (isSolo($result)) {
                        // Si le resultat correspond à un nageur du club
                        if ($result->SOLO->attributes()->swimmerid == $swi_id['swi_id']) {
                            if ($result->attributes()->disqualificationid == 0) {
                                //Création de l'event
                                insert_event($meet->id, $result->attributes());
                                $eve_id = getEventByParams('RACE', $meet->id, $result->attributes());

                                //Création du result
                                if ($pool == 25) {
                                    $res_step = 25;
                                    if (hasSplits($result)) {
                                        if (checkSteps($result) == 50) {
                                            $splits = createSplitsBy25($result);
                                        } else {
                                            $splits = createSplits($result);
                                        }
                                    } else {
                                        $split = $result->attributes()->swimtime;
                                        $calculatedSplit = number_format(floatval($split) - (floatval($split) / 2), 4);
                                        $splits = "{ " . $calculatedSplit . ", " . $split . " }";
                                    }
                                } else {
                                    $res_step = 50;
                                    if (hasSplits($result)) {
                                        $splits = createSplits($result);
                                    } else {
                                        $splits = "{ " . $result->attributes()->swimtime . " }";
                                    }
                                }
                                insert_result($swi_id['swi_id'], $eve_id, $result->attributes(), $res_step, $splits);
                            }
                        }
                    } else {
                        echo "L'application ne gère actuellement que les courses individuelles.<br>";
                    }
                }
            }
            
            checkRecords($meet->id);
            
            // Ferme la connexion
            pg_close($dbconn);
        }
    }
    // Si le fichier n'existe pas on renvoie un message d'erreur
    else {
        die('Impossible d\'ouvrir ce fichier');
    }
}

// Fonction permettant la gestion des records indépendamment des fichiers FFNEX RECORDS
function checkRecords($mee_id) {
    $rec_q = "SELECT check_record_for_meeting(".$mee_id.");";
    $q_res = pg_query($rec_q) or die('Échec de la requête : ' . pg_last_error());
}

// Fonction de vérification pour les caractères spéciaux
function checkChars($s) {
    if (!is_string($s)) {
        $s = strval($s);
    }
    $new_s = $s;
    if (strlen($s) > 0) {
        $new_s = str_replace("'", "_", $s);
    }
    return $new_s;
}

// Permet de tester si le tableau en paramètre contient bien un solo
function isSolo($array) {
    foreach ($array as $key => $value) {
        if ($key == 'SOLO')
            return true;
        else
            return false;
    }
}

// Permet de tester si le tableau en paramètre contient des splits
function hasSplits($array) {
    $hasSplits = false;
    foreach ($array as $key => $value) {
        if ($key == 'SPLITS') {
            $hasSplits = true;
        } else {
            $hasSplits = false;
        }
    }
    return $hasSplits;
}

// Permet de vérifier l'intervalle des splits
function checkSteps($result) {
    $splits = $result->SPLITS->children();
    $dist_0 = $splits[0]->attributes()->distance;
    $dist_1 = $splits[1]->attributes()->distance;
    $step = $dist_1 - $dist_0;
    return $step;
}

// Récupération de l'id de la saison correspondant au meeting
function getSeasonID($date) {
    $sea_q = "SELECT sea_id FROM t_e_season_sea WHERE sea_start_date < '" . $date . "' AND sea_end_date > '" . $date . "';";
    $q_res = pg_query($sea_q) or die('Échec de la requête : ' . pg_last_error());
    $season = pg_fetch_object($q_res)->sea_id;
    return $season;
}

// Récupération de l'id du club
function getClubID() {
    $clu_q = "SELECT clu_id FROM t_e_club_clu;";
    $q_res = pg_query($clu_q) or die('Échec de la requête : ' . pg_last_error());
    $club = pg_fetch_object($q_res)->clu_id;
    return $club;
}

// Récupération des nageurs de la base
function getSwimmers() {
    $swi_q = "SELECT swi_id FROM t_e_swimmer_swi;";
    $q_res = pg_query($swi_q) or die('Échec de la requête : ' . pg_last_error());
    $swimmers = pg_fetch_all($q_res);
    return $swimmers;
}

// Récupération des nageurs de la base
function getEventByParams($eveType, $meeId, $attributes) {
    $eve_q = "SELECT eve_id FROM t_e_event_eve WHERE eve_type = '" . $eveType . "' AND eve_mee_id = " . $meeId . " AND eve_rac_id = " . $attributes->raceid . " AND eve_rou_id = " . $attributes->roundid . ";";
    $q_res = pg_query($eve_q) or die('Échec de la requête : ' . pg_last_error());
    $eveID = pg_fetch_object($q_res)->eve_id;
    return $eveID;
}

// Insertion d'un meeting
function insert_meeting($meet, $season_id, $pool_size) {
    $mee_q = "SELECT ps_insertNewMeeting(" . $meet->id . ",'" . checkChars($meet->name) . "','" . checkChars($meet->city) . "','" . $meet->startdate . "','" . $meet->stopdate . "'," . $pool_size . "," . $season_id . ");";
    $q_res = pg_query($mee_q) or die('Échec de la requête : ' . pg_last_error());
}

// Insertion d'un nageur
function insert_swimmer($attributes) {
    $swi_q = "SELECT ps_insertNewSwimmer(" . $attributes->id . ",'" . checkChars($attributes->firstname) . "','" . checkChars($attributes->lastname) . "','" . checkChars($attributes->birthdate) . "','" . $attributes->gender . "'," . $attributes->clubid . ");";
    $q_res = pg_query($swi_q) or die('Échec de la requête : ' . pg_last_error());
}

// Insertion d'un evenement
function insert_event($mee_id, $attributes) {
    $event_q = "SELECT ps_insertnewevent('RACE'," . $mee_id . ", " . $attributes->raceid . ", " . $attributes->roundid . ");";
    $q_res = pg_query($event_q) or die('Échec de la requête : ' . pg_last_error());
}

// Insertion d'un resultat
function insert_result($swiId, $eveId, $attributes, $step, $splits) {
    $result_q = "SELECT ps_insertnewresult(" . $swiId . ", " . $eveId . ", " . $attributes->swimtime . ", " . $step . ", '" . $splits . "');";
    $q_res = pg_query($result_q) or die('Échec de la requête : ' . pg_last_error());
}

// Récupération des nageurs du club
function sortClubSwimmers($allSwimmers) {
    foreach ($allSwimmers as $swimmer) {
        // Si le nageur fait partie du club
        if ($swimmer->attributes()->clubid == getClubID()) {
            insert_swimmer($swimmer->attributes());
        }
    }
}

// Récupération des splits d'un result
function createSplits($result) {
    $splits = $result->SPLITS->children();
    $splitsString = "{ ";
    foreach ($splits as $split) {
        $splitsString .= $split->attributes()->swimtime . ", ";
    }
    $splitsString = substr($splitsString, 0, strlen($splitsString) - 2);
    $splitsString .= " }";
    return $splitsString;
}

// Récupération des splits d'un result
function createSplitsBy25($result) {
    $splits = $result->SPLITS->children();
    $splitsString = "{ ";
    $prevSplit = floatval(0);

    foreach ($splits as $split) {
        $currentSplit = floatval($split->attributes()->swimtime);
        $calculated = number_format($currentSplit - (($currentSplit - $prevSplit) / 2), 4);
        $splitsString .= $calculated . ", " . $currentSplit . ", ";
        $prevSplit = $currentSplit;
    }

    $splitsString = substr($splitsString, 0, strlen($splitsString) - 2);
    $splitsString .= " }";
    return $splitsString;
}

?>