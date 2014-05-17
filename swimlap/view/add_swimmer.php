<form id="form_swimmer" method="post" action="<?php echo MODEL;?>fonctions_request_form.php">
    <h4 class="title2">Ajouter un nageur</h4>
    <input type="hidden" name="type_form" value="swimmer"/>
    <div>Nom du nageur :</div>
    <input type="text" name="swimmer_name" id="swimmer_name" required/>
    <div>Prénom du nageur :</div>
    <input type="text" name="swimmer_firstname" id="swimmer_firstname" required/>
    <div>Id FFNEX du nageur :</div>
    <input type="text" name="swimmer_id" id="swimmer_id" required/>
     <!--changer le hidden lors de changement-->
    <div>Sexe :</div>
    <select name="select_sexe">
        <option value="F">Féminine</option> 
        <option value="M" selected>Masculin</option>
    </select>
    <div>Date de naissance :</div>
    <input type="text" name="swimmer_birth" id="swimmer_birth" placeholder="1999-02-14" required/><br />
    <input type="submit" class="button" value="Ajouter"/>
    <a class="button form_cancel">Annuler</a> 
</form>
