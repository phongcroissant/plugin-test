<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<div style="padding: 20px;">
    <div class="form-group">
        <label>{{Nombre de thermostats à créer}}</label>
        <input type="number" class="form-control" id="thermostat_number" placeholder="1" min="1" value="1" />
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <button class="btn btn-success" id="bt_saveThermostat">{{Valider}}</button>
    </div>
</div>

<script>
    $('#bt_saveThermostat').on('click', function () {
        var number = $('#thermostat_number').val();
        if (number <= 0) {
            alert('Saisissez au moins 1 thermostat');
        } else {
            alert('Création de ' + number + ' thermostat(s)');
            $('#md_modal').dialog('close');
        }
    });
</script>