<!DOCTYPE HTML>
<HTML>
    <?php include "../include/general.php"?>
    <BODY>
        <?php include INCLUDES."header.php"?>
        <div class="clear"></div>
        <div id="content">
            <h4 class="title2">Récapitulatif de l'enregistrement</h4>
            <center>
                <div class="container">
                    <?php switch ($_GET['form']) {
                            case 'general' :
                                $chaine = 'Le nouveau nom du club est : '.$_GET['new'];
                            break;
                            case 'swimmer' :
                                $chaine = $_GET['name'].' '.$_GET['first'].' ('.$_GET['id'].', '.$_GET['birth'].', '.$_GET['genre'].') a bien été ajouté à la liste.';
                            break;
                            case 'record' :
                                $chaine = 'En '.$_GET['race'].", le nageur avec l'id ".$_GET['name']." a comme nouveau record : ".$_GET['record']." (".$_GET['pool'].").";
                            break;
                            case 'competition' :
                                $chaine = 'La compétition : '.$_GET['name']." (".$_GET['city'].", ".$_GET['begin']." / ".$_GET['end'].") a bien été enregistrée.";
                            break;
                            default:
                            break;
                    }?>
                    <p><?php echo $chaine; ?></p>
                    <a class="button" href="settings.php">Retour</a>
                </div>
            </center>
        </div>
        <?php include INCLUDES."footer.php"?>
        <script>
            $("header span.setting").addClass('active');
            $("#sous-menu").hide();
        </script>
    </BODY>
</HTML>
