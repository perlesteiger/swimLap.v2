<?php
//include '../var.prepend.php';
//include MODEL.'fonctions_crud.php';

$type = $_POST['style'];
if (isset($_POST['nageur']))
    $id_swimmer = $_POST['nageur'];
if (isset($_POST['compet']))
    $id_meeting = $_POST['compet'];
if (isset($_POST['course']))
    $id_race = $_POST['course'];
if (isset($_POST['saison']))
    $id_season = $_POST['saison'];

switch ($type) {
    case 'repartition':
//        
//        $result = recoverSplits($id_swimmer, $id_meeting, $id_race, $id_season);
//        
//        foreach ($result as $tab) {
//            $name_swimmer = $tab['nageur'];
//            $rencontre = $tab['rencontre'];
//            $temps = $tab['temps'];
//            $total = $tab['total'];
//            $round = $tab['round'];
//
//            //penser au different round a ajouter
//            $cpt=1;
//            $repartition=array();
//            foreach ($temps as $tmp) {
//                if ($cpt==1) {
//                    $time = $total-($tmp*100/$total);
//                    array_push ($repartition, $time);
//                    $temporel = $tmp;
//                } else {
//                    $time = $total-(($tmp-$temporel)*100/$total);
//                    array_push($repartition, $time);
//                    $temporel = $tmp;
//                }
//                $cpt++;
//            }
//            $renvoi[]= array('swimmer'=>$name_swimmer, 'meet'=>$rencontre, 'moyenne'=>$repartition, 'round'=>$round);
//            //faire encode pour renvoi ou a la fin du switch
//            echo json_encode($renvoi);
//        }
        //round-race-percent-id-swimmer
        $tab=array();
        if (!empty($id_race)) {

            $tab[] = array('round'=>'1ère', 'percent'=>'60', 'swimmer'=>'Fontaine Léa', 'race'=>'');
            $tab[] = array('round'=>'1ère', 'percent'=>'40', 'swimmer'=>'Fontaine Léa', 'race'=>'');
            $tab[] = array('round'=>'Demi', 'percent'=>'75', 'swimmer'=>'Fontaine Léa', 'race'=>'');
            $tab[] = array('round'=>'Demi', 'percent'=>'25', 'swimmer'=>'Fontaine Léa', 'race'=>'');
            $tab[] = array('round'=>'1ère', 'percent'=>'53', 'swimmer'=>'Pain Laeticia', 'race'=>'');
            $tab[] = array('round'=>'1ère', 'percent'=>'47', 'swimmer'=>'Pain Laeticia', 'race'=>'');
            $tab[] = array('round'=>'Demi', 'percent'=>'46', 'swimmer'=>'Pain Laeticia', 'race'=>'');
            $tab[] = array('round'=>'Demi', 'percent'=>'54', 'swimmer'=>'Pain Laeticia', 'race'=>'');
            $tab[] = array('round'=>'Finale', 'percent'=>'62', 'swimmer'=>'Pain Laeticia', 'race'=>'');
            $tab[] = array('round'=>'Finale', 'percent'=>'38', 'swimmer'=>'Pain Laeticia', 'race'=>'');
            $tab[] = array('round'=>'1ère', 'percent'=>'50', 'swimmer'=>'Rosato Marine', 'race'=>'');
            $tab[] = array('round'=>'1ère', 'percent'=>'50', 'swimmer'=>'Rosato Marine', 'race'=>'');
         
        } else if (!empty($id_swimmer) || (empty($id_race) && empty($id_swimmer))) {

            $tab[] = array('round'=>'1ère', 'percent'=>'60', 'swimmer'=>'', 'race'=>'100m Nage Libre');
            $tab[] = array('round'=>'1ère', 'percent'=>'40', 'swimmer'=>'', 'race'=>'100m Nage Libre');
            $tab[] = array('round'=>'Demi', 'percent'=>'75', 'swimmer'=>'', 'race'=>'100m Nage Libre');
            $tab[] = array('round'=>'Demi', 'percent'=>'25', 'swimmer'=>'', 'race'=>'100m Nage Libre');
            $tab[] = array('round'=>'1ère', 'percent'=>'53', 'swimmer'=>'', 'race'=>'100m Dos');
            $tab[] = array('round'=>'1ère', 'percent'=>'47', 'swimmer'=>'', 'race'=>'100m Dos');
            $tab[] = array('round'=>'Demi', 'percent'=>'46', 'swimmer'=>'', 'race'=>'100m Dos');
            $tab[] = array('round'=>'Demi', 'percent'=>'54', 'swimmer'=>'', 'race'=>'100m Dos');
            $tab[] = array('round'=>'Finale', 'percent'=>'62', 'swimmer'=>'', 'race'=>'100m Dos');
            $tab[] = array('round'=>'Finale', 'percent'=>'38', 'swimmer'=>'', 'race'=>'100m Dos');
            $tab[] = array('round'=>'1ère', 'percent'=>'50', 'swimmer'=>'', 'race'=>'100m Papillon');
            $tab[] = array('round'=>'1ère', 'percent'=>'50', 'swimmer'=>'', 'race'=>'100m Papillon');
            
        }
        break;

    case 'performance':

        break;
    case 'planification':

        break;

    default:
        break;
}

echo json_encode($tab);
?>