<?php
require_once __DIR__ . '/../../../core/php/core.inc.php';
?>

<div class="container-fluid">

    <div class="form-group">
        <label>{{Nom du thermostat}}</label>
        <input type="text" class="form-control" id="thermostat_name" placeholder="{{Ex : Salon}}">
    </div>

    <div class="form-group">
        <label>{{Pièce}}</label>
        <select class="form-control" id="thermostat_room">
            <option value="">{{Sélectionner}}</option>
                  <?php
                  foreach (object::all() as $object) {
                      echo '<option value="' . $object->getId() . '">'
                          . $object->getName()
                          . '</option>';
                  }
                  ?>
        </select>
    </div>

    <hr>

    <div class="text-right">
        <button class="btn btn-default" onclick="$('#md_modal').dialog('close');">
            {{Annuler}}
        </button>

        <button class="btn btn-success" id="btn_create_thermostat">
            <i class="fas fa-check"></i> {{Créer}}
        </button>
    </div>

</div>