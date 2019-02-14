<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

include_file('desktop', 'alexaapi', 'js', 'alexaapi');

?>
     <legend><i class="icon divers-triangular42"></i> {{Génération manuelle du cookie Amazon}}</legend>

<table class="table table-condensed">
		<tr>
		<th style="width: 30%">Le Controleur de l'API Cookie-Alexa est :</th>
		<th style="width: 70%" class="deamonCookieState"> 
<span class="label label-warning" style="font-size:1em;">Non utilisé</span>
			</td>
				</tr>


		<tr>
		<th style="position:relative;top:+8px;">Commande(s) du controleur de l'API Cookie-Alexa disponible : </th>
			<th>
				<a class="btn btn-success btn-sm bt_startDeamonCookie"><i class="fa fa-play"></i><a class="btn btn-danger btn-sm bt_stopDeamonCookie"><i class="fa fa-stop"></i></a> <a class="btn btn-warning btn-sm bt_identificationCookie" href="http://<?php print config::byKey('internalAddr')?>:3457"  onclick="open('http://<?php print config::byKey('internalAddr')?>:3457', 'Popup', 'scrollbars=1,resizable=1,height=560,width=770'); return false;" ><i class="fa fa-cogs"></i> Identifiez vous sur Amazon</a>
<a class="btn btn-warning btn-sm bt_identificationCookie2"><i class="fa fa-cogs"></i> Patientez quelques secondes que le Démon s'initialise. Dès que "Configuration" devient OK, Lancez le Démon avec (Re)Démarrer</a>
			</th>
				</tr>
		<tr>
				</tr>			<th>			</th><th>			</th>


		</table>

		<?php
//sendVarToJs('refresh_deamonCookie_info', $refresh);
?>
		<script>
			var timeout_refreshDeamonCookieInfo = null;
			$('.bt_stopDeamonCookie').hide();
			$('.bt_identificationCookie').hide();
			$('.bt_identificationCookie2').hide();

// On appuie sur Le lancement du serveur... on lance "deamonCookieStart" via action=deamonCookieStart dans alexaapi.ajax.php

			$('.bt_startDeamonCookie').on('click',function(){
				clearTimeout(timeout_refreshDeamonInfo);
						jeedom.plugin.deamonCookieStart({
							id : plugin_id,
							forceRestart: 1,
							error: function (error) {
								$('#div_alert').showAlert({message: error.message, level: 'danger'});
								refreshDeamonInfo();
								timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 5000);
							},
							success:function(){
								refreshDeamonInfo();
								$('.deamonCookieState').empty().append('<span class="label label-success" style="font-size:1em;">{{OK}}</span>');
								$('.bt_startDeamonCookie').hide();
								$('.bt_stopDeamonCookie').show();
								$('.bt_identificationCookie').show();
								timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 5000);
							}
						});
			});

					$('.bt_stopDeamonCookie').on('click',function(){
			clearTimeout(timeout_refreshDeamonInfo);
			jeedom.plugin.deamonCookieStop({
				id : plugin_id,
				error: function (error) {
					$('#div_alert').showAlert({message: error.message, level: 'danger'});
					refreshDeamonInfo();
					timeout_refreshDeamonCookieInfo = setTimeout(refreshDeamonInfo, 5000);
				},
				success:function(){
					refreshDeamonInfo();
$('.deamonCookieState').empty().append('<span class="label label-danger" style="font-size:1em;">{{NOK}}</span>');
$('.bt_startDeamonCookie').show();
$('.bt_stopDeamonCookie').hide();
$('.bt_identificationCookie').hide();
					timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 5000);
				}
			});
		});

					$('.bt_identificationCookie').on('click',function(){
$('.bt_identificationCookie').hide();
$('.bt_identificationCookie2').show();

			});


		</script>