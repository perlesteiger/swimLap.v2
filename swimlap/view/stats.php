<?php $mode = 'stat'; ?>
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
                <div id="stat-forms">
                    <?php include VIEW."stat_repartition.php"; ?>
                    <?php include VIEW."stat_performance.php"; ?>
                    <?php include VIEW."stat_planning.php"; ?>
                </div>
            </div>
        </div>
        <?php include INCLUDES."footer.php"?>
        <script>
            $("header span.stat").addClass('active');
            $("#sous-menu-setting").hide();
        </script>
    </BODY>
</HTML>
