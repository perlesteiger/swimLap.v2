<div class="section" id="section_data">
    <div id="import_block" class="file_block">
        <h4 class="title2">Importer un fichier FFNEX</h4>
        <form enctype="multipart/form-data" id="form_import" method="post" action="<?php echo MODEL;?>fonctions_request_form.php">
            <input type="hidden" name="type_form" value="import"/>
            <label for="import_file">Sélectionner le fichier à importer :</label><br/>
            <input type="file" name="import_file" id="import_file"/>
            <input type="submit" name="import" id="importBtn" class="button" value="Importer"/>
        </form>
    </div>
    <div id="export_block" class="file_block">
        <h4 class="title2">Exporter un fichier FFNEX</h4>
        <form id="form_export" method="post" action="<?php echo MODEL;?>fonctions_request_form.php">
            <input type="hidden" name="type_form" value="export"/>
            <label for="export_compet">Sélectionner la compétition à exporter :</label><br/>
            <select class="data_export" name="export_compet">
                <?php foreach ($meetings as $meet) {
                    $meet = explode('|', $meet);
                    echo '<option value="'.$meet[4].'">'.$meet[0].'</option>';
                } ?>
            </select><br/>
            <input type="submit" name="export" id="exportBtn" class="button" value="Exporter"/><br/>
        </form>
    </div>
</div>