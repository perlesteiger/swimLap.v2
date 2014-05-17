<div class="section container" id="section_swimmer">
    <?php if ($mode === 'setting') { ?>
        <button class="button add" id="add_swimmer" name="swimmer">+</button>
        <h4 class="title2">Liste des nageurs</h4>
    <?php } else { ?>
        <h4 class="title1">Liste des nageurs</h4>
    <?php } ?>

    <ul id="list_swimmer">
        <?php foreach ($swimmer as $swim) {
            $swim = explode("|", $swim);
            echo '<li><p>'.$swim[0].' '.$swim[1].'</p><p>Né(e) le : '.date('d/m/Y', strtotime($swim[2])).'</p></li>';
        } ?>
    </ul>
</div>