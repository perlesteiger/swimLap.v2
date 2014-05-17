<div class="section container put" id="section_competition">
    <button class="button add" id="add_competition" name="competition">+</button>
    <h4 class="title2">Liste des compétitions</h4>
    <ul id="list_competition">
        <?php foreach ($competition as $compet) {
            $compet = explode("|", $compet);
            echo '<li><p>'.$compet[0].'</p><p>Située à : '.$compet[1].' ('.date('d/m/Y', strtotime($compet[2])).'-'.date('d/m/Y', strtotime($compet[3])).')</p></li>';
        } ?>
    </ul>
</div>
