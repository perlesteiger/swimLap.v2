<form id="form_record" method="post" action="<?php echo MODEL;?>fonctions_request_form.php">
    <h4 class="title2">Ajouter un record</h4>
    <input type="hidden" name="type_form" value="record"/>
    <div>Nouveau record :</div>
    <input type="text" name="record_new" id="record_new" required/>
    <div>Nom du nageur :</div>
    <select class="record_swimmer" name="record_swimmer">
        <?php foreach ($swimmers as $swimmer) {
            $swimmer = explode('|', $swimmer);
            echo '<option value="'.$swimmer[3].'">'.$swimmer[0].' '.$swimmer[1].'</option>';
        } ?>
    </select><br/>
    <div>Type de nage :</div>
    <select name="record_swim">
        <?php foreach ($races as $id => $race) {
            echo '<option value="'.$id.'">'.$race.'</option>';
        } ?>
    </select>
    <div>Taille du bassin :</div>
    <select name="select_pool">
        <option value="25">25</option> 
        <option value="50" selected>50</option>
    </select><br />
    <input type="submit" class="button" value="Ajouter"/>
    <a class="button form_cancel">Annuler</a> 
</form>
