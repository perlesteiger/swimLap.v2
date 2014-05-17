<div class="section container" id="section_record">
    <button class="button add" id="add_record" name="record">+</button>
    <h4 class="title2">Liste des records</h4>
    <ul id="list_record">
        <?php foreach ($record as $rec) {
            $rec = explode("|", $rec);
            echo '<li><p>'.$rec[0].'</p><p>Type de course : '.$rec[3].' '.$rec[4].'</p><p>Record en 25m : '.$rec[1].'</p><p>Record en 50m : '.$rec[2].'</p></li>';
        } ?>
    </ul>
</div>