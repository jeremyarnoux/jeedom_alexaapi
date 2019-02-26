<?php
if (!isConnect())
{
  include_file('desktop', '404', 'php');
  die();
}
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

//print $_GET['plugin'];
//print $_GET['configure'];
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

include_file('core', 'authentification', 'php');
include_file('desktop', 'alexaapi', 'js', 'alexaapi');


?>

<legend><i class="icon divers-triangular42"></i> {{GÃ©nÃ©ration manuelle du cookie Amazon}}</legend>

		<?php
//On va tester si les dépendances sont installées
		if (!(is_dir(realpath(dirname(__FILE__) . '/../resources/node_modules'))))
		{
		print "<B>DÃ©pendances non prÃ©sentes, gÃ©nÃ©ration manuelle du cookie Amazon impossible !!</B>";	
		print "<br><small>Le dossier <I>".dirname(__FILE__) . "/../resources/node_modules</I> est introuvable</small>";	
		}
else
		{
		?>
		<table class="table table-condensed">
		  <tr>
			<th style="width: 30%">Le Controleur de l'API Cookie-Alexa est :</th>
			<th style="width: 70%" class="deamonCookieState"> <span class="label label-warning" style="font-size:1em;">Non utilisÃ©</span></td>
		  </tr>
		  <tr>
			<th style="position:relative;top:+8px;">Commande(s) du controleur de l'API Cookie-Alexa disponible : </th>
			<th>
				<a class="btn btn-success btn-sm bt_startDeamonCookie"><i class="fa fa-play"></i>
				<a class="btn btn-danger btn-sm bt_stopDeamonCookie"><i class="fa fa-stop"></i></a> <a class="btn btn-warning btn-sm bt_identificationCookie" href="http://<?php print config::byKey('internalAddr')?>:3457" onclick="open('http://<?php print config::byKey('internalAddr')?>:3457', 'Popup', 'scrollbars=1,resizable=1,height=560,width=770'); return false;" ><i class="fa fa-cogs"></i> Identifiez vous sur Amazon</a>
				<a class="btn btn-warning btn-sm bt_identificationCookie2"><i class="fa fa-cogs"></i> Patientez quelques secondes que le DÃ©mon s'initialise. DÃ¨s que "Configuration" devient OK, Lancez le DÃ©mon avec (Re)DÃ©marrer</a>
			</th>
		  </tr>
		</table>

		<?php
		}
?>
<form class="form-horizontal">	
<fieldset>
	<div class="form-group">
		<label class="col-lg-4 control-label">{{RÃ©paration}}</label>
		<div class="col-lg-3" style="padding-left:0px;padding-right:0px;">
			<div><a class="btn btn-danger" style="width:70%;float:right;" id="bt_reinstallNodeJS"><i class="fa fa-erase"></i> {{RÃ©paration de NodeJS}}</a></div>
		</div>
	</div>	
</fieldset>
</form>
<script>
  var timeout_refreshDeamonCookieInfo = null;
  $('.bt_stopDeamonCookie').hide();
  $('.bt_identificationCookie').hide();
  $('.bt_identificationCookie2').hide();

  // On appuie sur Le lancement du serveur... on lance "deamonCookieStart" via action=deamonCookieStart dans alexaapi.ajax.php
  $('.bt_startDeamonCookie').on('click',function()
  {
    clearTimeout(timeout_refreshDeamonInfo);
    jeedom.plugin.deamonCookieStart(
    {
      id : plugin_id,
      forceRestart: 1,
      error: function (error)
      {
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

  $('.bt_stopDeamonCookie').on('click',function()
  {
    clearTimeout(timeout_refreshDeamonInfo);
    jeedom.plugin.deamonCookieStop(
    {
      id : plugin_id,
      error: function (error)
      {
        $('#div_alert').showAlert({message: error.message, level: 'danger'});
        refreshDeamonInfo();
        timeout_refreshDeamonCookieInfo = setTimeout(refreshDeamonInfo, 5000);
      },
      success:function()
      {
        refreshDeamonInfo();
        $('.deamonCookieState').empty().append('<span class="label label-danger" style="font-size:1em;">{{NOK}}</span>');
        $('.bt_startDeamonCookie').show();
        $('.bt_stopDeamonCookie').hide();
        $('.bt_identificationCookie').hide();
        timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 5000);
      }
    });
  });

  $('.bt_identificationCookie').on('click',function()
  {
    $('.bt_identificationCookie').hide();
    $('.bt_identificationCookie2').show();
  });
  $('#bt_reinstallNodeJS').on('click', function() {
		bootbox.confirm('{{Etes-vous sÃ»r de vouloir supprimer et reinstaller NodeJS ?}}', function(result) {
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
							message : "{{RÃ©installation NodeJS effectuÃ©e, merci de patienter jusqu'Ã  la fin de l'installation des dÃ©pendances}}",
							level : 'success'
						});
					}
				});
			}
		});
	});	
</script>

