<form id="form_competition" method="post" action="<?php echo MODEL;?>fonctions_request_form.php">
    <h4 class="title2">Ajouter une compétition</h4>
    <input type="hidden" name="type_form" value="competition"/>
    <div>ID de la compétition :</div>
    <input type="text" id="competition_id" name="competition_id" required/>
    <div>Nom de la compétition :</div>
    <input type="text" id="competition_name" name="competition_name" required/>
    <!--datepicker ?-->
    <div>Date de départ :</div>
    <input type="text" id="competition_begin" name="competition_begin" required/>
    <div>Date de fin :</div>
    <input type="text" id="competition_end" name="competition_end" required/>
    <div>Ville :</div>
    <input type="text" id="competition_city" name="competition_city" required/>
    <!--changer le hidden lors de changement-->
    <div>Taille du bassin :</div>
    <select name="select_pool">
        <option value="25">25</option> 
        <option value="50" selected>50</option>
    </select>
    <div>Saison :</div>
    <select name="competition_season">
        <?php foreach ($season as $id => $name) {
            echo '<option value="'.$id.'">'.$name.'</option>';
        } ?>
    </select><br />
    <input type="submit" class="button" value="Ajouter"/>
    <a class="button form_cancel">Annuler</a>       
</form>
