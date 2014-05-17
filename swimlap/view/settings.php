<?php $mode = 'setting'; ?>

<!DOCTYPE HTML>
<HTML>
    <?php include "../include/general.php"?>
    <BODY>
        <?php include INCLUDES."header.php"?>
        <div class="clear"></div>
        <div id="content">
            <!--partie recherche-->
            <div class="fleft" id="content-left">
                <?php include CONTROLLER."controller_search.php"; ?>                
            </div>
            <!--partie centrale-->
            <div class="fleft" id="content-right">
                <div id="setting-forms">
                    <?php include CONTROLLER."controller_add_general.php"?>
                    <?php include CONTROLLER."controller_import_export.php"?>
                    <?php include CONTROLLER."controller_list_competition.php"?>
                    <?php include CONTROLLER."controller_list_swimmer.php"?>
                    <?php include CONTROLLER."controller_list_record.php"?>
                    
                    <?php include CONTROLLER."controller_add_competition.php"?>
                    <?php include CONTROLLER."controller_add_swimmer.php"?>
                    <?php include CONTROLLER."controller_add_record.php"?>
                </div>
            </div>
        </div>
        <?php include INCLUDES."footer.php"?>
        <script>
            $("header span.setting").addClass('active');
            $("#sous-menu-stat").hide();
            $("#param").hide();
        </script>
    </BODY>
</HTML>
