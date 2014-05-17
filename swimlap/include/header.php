<HEADER>
    <img class="fleft" src="<?php echo IMG;?>logo.png"/>
    <div id="menu" class="fright">
        <div id="menu-lien">
            <a href="<?php echo VIEW."home.php";?>" class="fleft"><span class="icon home"></span></a>
            <a href="<?php echo VIEW."stats.php";?>" class="fleft"><span class="icon stat"></span></a>
            <a href="<?php echo VIEW."settings.php";?>" class="fleft"><span class="icon setting"></span></a>
        </div>
        <div class="clear"></div>
        
        <!--lien pour les statistiques-->
        <ul class="sous-menu" id="sous-menu-stat">
            <li class="repartition active">Répartition</li>
            <li class="performance">Performance</li>
            <li class="planning">Planification</li>
        </ul>
        
        <!--lien pour les parametres-->
        <ul class="sous-menu" id="sous-menu-setting">
            <li class="competition active">Compétitions</li>
            <li class="swimmer">Nageurs</li>
            <li class="record">Records</li>
            <li class="data">Fichiers</li>
        </ul>
    </div>
</HEADER>
