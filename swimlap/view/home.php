<?php $mode='home'; ?>

<!DOCTYPE HTML>
<HTML>
    <?php include "../include/general.php"?>
    <BODY>
        <?php include INCLUDES."header.php"?>
        <div class="clear"></div>
        <div id="content">
            <div class="fleft" id="content-left">
                <?php include CONTROLLER."controller_list_swimmer.php"; ?>                
            </div>
            <div class="fleft" id="content-right">
                <?php include VIEW."stat_planning.php"; ?>
            </div>
        </div>
        <?php include INCLUDES."footer.php"?>
        <script>
            $("header span.home").addClass('active');
            $("#sous-menu-stat").hide();
            $("#sous-menu-setting").hide();
        </script>
    </BODY>
</HTML>
