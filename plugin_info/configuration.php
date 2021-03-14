<?php
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

include_file('core', 'authentification', 'php');
include_file('desktop', 'alexaapi', 'js', 'alexaapi');

// Obtenir l'identifiant du plugin
$plugin = plugin::byId('alexasmarthome');
// Accéder aux données du plugin
$eqLogics = eqLogic::byType($plugin->getId());

$tousLesFabriquants = array();
foreach ($eqLogics as $eqLogic) {
	$fabriquant=$eqLogic->getConfiguration('manufacturerName');
	if ($fabriquant=="smart_life") $fabriquant="Smart Life";
	array_push($tousLesFabriquants, $fabriquant);
	//echo "<br>".$eqLogic->getName()."-".$eqLogic->getConfiguration('manufacturerName');
}
$tousLesFabriquants = array_unique($tousLesFabriquants);

//if (config::byKey("desactiveScanJeedomDevices", "alexasmarthome", "0") != "1") unset($tousLesFabriquants[array_search('Jeedom', $tousLesFabriquants)]);
?>
<form class="form-horizontal">
    <fieldset>
        <legend><i class="fas fa-list-alt"></i> {{Gestion des devices}}</legend>
        <div class="form-group">
            <label class="col-lg-4 col-md-3 col-sm-4 col-xs-6 control-label">{{Supprimer tous les devices smartHome !!}}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
                <a class="btn btn-danger bt_supprimeTouslesDevicesSmartHome"><i class="fas fa-exclamation-triangle"></i>
                    {{Supprimer tous les équipements smartHome}}</a>
            </div>
        </div>
		
    </fieldset>		
		<fieldset>
        <legend><i class="far fa-check-square"></i> {{Activer/Désactiver les équipements smartHome de certains fabriquants}}</legend>
        <div class="form-group">
 <?php
	foreach ($tousLesFabriquants as $Fabriquant) {
		if ($Fabriquant!="") {
			$FabriquantID=str_replace(" ", "_", $Fabriquant);
			?><div>
				<label class="col-lg-4 col-md-3 col-sm-4 col-xs-6 control-label"></label>
			<input type="checkbox" class="configKey" data-l1key="fabriquant_<?php echo $FabriquantID?>"/>
			<label for="coding"><?php echo $Fabriquant;?></label><?php
			if ($Fabriquant=="Jeedom") { echo " (déconseillé)";}
			?>
			</div><?php
		}
	}
?>     
            <br><label class="col-lg-4 col-md-3 col-sm-4 col-xs-6 control-label"></label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
                <a class="btn btn-success bt_desactiverFabriquants"><i class="far fa-check-square"></i>
                    {{Activer uniquement les fabriquants cochés}}</a>
            </div>

        </div>	
		
</fieldset>
		

		
		
		

</form>

<script>
    $('.bt_desactiverFabriquants').off('click').on('click', function () {
		//savePluginConfig( {"success":function(){window.location.reload()}})
	savePluginConfig( {"success":function(){
		
		$.ajax({
			type: "POST",
			url: "plugins/alexasmarthome/core/ajax/alexasmarthome.ajax.php",
			data: {
			  action: "metAjourFabriquantsDesactives",
			},
			dataType: 'json',
			error: function (request, status, error) {
			  handleAjaxError(request, status, error);
			},
			success: function (data) {

			  window.location.reload();
			}
		  });
		
	}})	
	
  });

    $('.bt_supprimeTouslesDevicesSmartHome').off('click').on('click', function () {
        $('#md_modal').dialog('close');

        bootbox.confirm({
            message: "Etes-vous sûr de vouloir supprimer tous les équipements du plugin Alexa-smartHome ?",
            buttons: {
                confirm: {
                    label: 'Oui',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'Non',
                    className: 'btn-success'
                }
            },
            callback: function (result) {
                /*$('#div_alert').showAlert({
                    message : "{{Suppression en cours ...}}",
                    level : 'success'
                });*/
                if (result) {
                    //$.showLoading(); ??
                    $.ajax({
                        type: 'POST',
                        url: 'plugins/alexaapi/core/ajax/alexaapi.ajax.php',
                        data: {
                            action: 'supprimeTouslesDevicesSmartHome',
                        },
                        dataType: 'json',
                        global: false,
                        error: function (request, status, error) {
                            //$.hideLoading(); ??
                            $('#div_alert').showAlert({
                                message: error.message,
                                level: 'danger'
                            });
                        },
                        success: function (data) {
                            //$.hideLoading();??
                            //$('li.li_plugin.active').click();??
      window.location.reload();

                        }
                    });
                }
            }
        });

    });
</script>