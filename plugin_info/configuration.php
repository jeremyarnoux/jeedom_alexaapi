<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal">
  <div class="form-group">
    <fieldset>

      <div class="form-group">
        <label class="col-lg-4 control-label">{{IP Controleur de l'API Alexa}} :</label>
        <div class="col-lg-4">
          <?php
            echo config::byKey('internalAddr');
          ?>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-4 control-label">{{Port Controleur à utiliser}} :</label>
        <div class="col-lg-4">
          3456
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-4 control-label">{{Exemple de commande }} :</label>
        <div class="col-lg-4"><?php
            echo config::byKey('internalAddr');
          ?>:3456/speak?device=salon&text=coucou
        </div>
      </div>
    </fieldset>
  </form>
<form class="form-horizontal">
    <fieldset>
    <legend><i class="icon loisir-darth"></i> {{Génération manuelle du cookie Amazon}}</legend>
		 <div class="form-group">
	<label class="col-lg-4"></label>
	<div class="col-lg-8">
		<a class="btn btn-warning" id="bt_backupsZwave"><i class="fa fa-cogs"></i> {{Lancer la génération}}</a>
	</div>
</fieldset>
</form>
<form class="form-horizontal">	
<fieldset>
	<div class="form-group">
		<label class="col-lg-4 control-label">{{Réparation}}</label>
		<div class="col-lg-3" style="padding-left:0px;padding-right:0px;">
			<div><a class="btn btn-danger" style="width:70%;float:right;" id="bt_reinstallNodeJS"><i class="fa fa-erase"></i> {{Réparation de NodeJS}}</a></div>
		</div>
	</div>	
</fieldset>
</form>
</div>

<script>
	$('#bt_backupsZwave').on('click', function () {
		$('#md_modal2').dialog({title: "{{Génération cookie Amazon}}"});
		$('#md_modal2').load('index.php?v=d&plugin=alexaapi&modal=cookie').dialog('open');
	});
	$('#bt_reinstallNodeJS').on('click', function() {
		bootbox.confirm('{{Etes-vous sûr de vouloir supprimer et reinstaller NodeJS ?}}', function(result) {
			if (result) {
				$.ajax({
					type : 'POST',
					url : 'plugins/alexaapi/core/ajax/alexaapi.ajax.php',
					data : {
						action : 'reinstallNodeJS',
					},
					dataType : 'json',
					global : false,
					error : function(request, status, error) {
						$('#div_alert').showAlert({
							message : error.message,
							level : 'danger'
						});
					},
					success : function(data) {
						$('li.li_plugin.active').click();
						$('#div_alert').showAlert({
							message : "{{Réinstallation NodeJS effectuée, merci de patienter jusqu'à la fin de l'installation des dépendances}}",
							level : 'success'
						});
					}
				});
			}
		});
	});	
</script>
