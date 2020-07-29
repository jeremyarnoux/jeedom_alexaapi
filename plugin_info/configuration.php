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

// Ajouté pour le serveur Cookies 11/07/2020
$plugin = plugin::byId('alexaapi');
$deamon_info = $plugin->deamon_info();




        //log::add('alexaapi', 'debug', 'Test de config::byKey dans config: ' . config::byKey('amazonserver','alexaapi'));

// code trouvé dans core\ajax\plugin.ajax.php
		$update = update::byLogicalId('alexaapi');
		$return = utils::o2a($update);
		$versionJeedom = $return['configuration']['version'];

?>
<style>
pre#pre_eventlog {
    font-family: Menlo, Monaco, Consolas, "Courier New", monospace !important;
}
</style>
	<legend><i class="icon divers-triangular42"></i> {{Génération manuelle du cookie Amazon}}</legend>
	<center>

		<a class="btn btn-success btn-sm bt_startDeamonCookie"> {{Identifiez-vous sur Amazon pour créer le cookie d'identification}} </a>
		<a class="btn btn-warning btn-sm bt_identificationCookie2"><i class="fa fa-clock"></i> {{... Attendez la génération du Cookie Amazon ...}} </a>
		<a class="btn btn-default btn-sm bt_identificationCookie2bis"><i class="fa fa-clock"></i> {{... Attendez la génération du Cookie Amazon ...}} </a>
		<a class="btn btn-danger btn-sm bt_identificationCookie2echec"><i class="fa fa-times"></i> {{La génération du Cookie Amazon a échoué}} </a>
		<a class="btn btn-success btn-sm bt_identificationCookie3"><i class="fa fa-check"></i> {{Bravo : Cookie d'identification Amazon chargé !}} </a>
		<a class="btn btn-primary btn-sm bt_identificationCookie"><i class="fa fa-spinner fa-spin"></i> {{Ouverture de la fenetre d'identification Amazon Alexa en cours ...}} </a>
		<a class="btn btn-default btn-sm bt_identificationCookie1"><i class="fa fa-spinner fa-spin"></i> {{Cliquez ici quand vous avez terminé l'identification}} </a>
		<br><br>
	</center>
		<?php //echo '<p align="right"><span class="label label-info">{{Debug Proxy 3457 : }}'. $deamon_info['stateCookies'] .'</span></p>';
		?>	<br />
	<legend><i class="fa fa-wrench"></i> {{Réparations}}</legend>
	<center>
		<a class="btn btn-danger btn-sm" id="bt_reinstallNodeJS"><i class="fa fa-recycle"></i> {{Réparation de NodeJS}} </a>
	</center>




<form class="form-horizontal">
    <fieldset>
    <legend><i class="icon nature-planet5"></i> {{Options internationales}}</legend>
       <div class="form-group">
        <label class="col-sm-4 control-label">{{Adresse du serveur Amazon}}</label>
    <div class="col-lg-2">
        <input class="configKey form-control" data-l1key="amazonserver" placeholder="{{amazon.fr}}" />
    </div>
   </div>

   <div class="form-group">
    <label class="col-lg-4 control-label">{{Adresse du serveur Alexa (ou Pitangui ou Layla ...)}}</label>
    <div class="col-lg-2">
        <input class="configKey form-control" data-l1key="alexaserver" placeholder="{{alexa.amazon.fr}}" />
    </div>
</div>
</fieldset>
</form>

<form class="form-horizontal">
    <fieldset>
         <legend><i class="fa fa-list-alt"></i> {{Profil Utilisateur}}</legend>
      <div class="form-group">
        <label class="col-lg-4 control-label">{{Activer les fonctions réservées aux utilisateurs expérimentés}}</label>
        <div class="col-lg-3">
           <input type="checkbox" class="configKey" data-l1key="utilisateurExperimente" />
       </div>
	</div>
	<div class="form-group">
		  <label class="col-lg-4 control-label" >{{Ajouter automatiquement les équipements détectés dans :}}</label>
		  <div class="col-lg-3">
			<select id="sel_object" class="configKey form-control" data-l1key="defaultParentObject">
			  <option value="">{{Aucune}}</option>
			  <?php
				foreach (jeeObject::all() as $object) {
				  echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
				}
			  ?>
			</select>
		  </div>
	</div>
	<div class="form-group">
		<label class="col-lg-4 col-md-3 col-sm-4 col-xs-6 control-label">{{Recharger la configuration par défaut de toutes les commandes}}</label>
		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
			<a class="btn btn-warning bt_forcerDefaultAllCmd"><i class="fas fa-search"></i> {{Lancer}}</a>
		</div>
	</div>	
	<div class="form-group">
		<label class="col-lg-4 col-md-3 col-sm-4 col-xs-6 control-label">{{Supprimer tous les devices !! et relancer un Scan}}</label>
		<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
			<a class="btn btn-danger bt_supprimeTouslesDevices"><i class="fas fa-exclamation-triangle"></i> {{Lancer}}</a>
		</div>
	</div>
   </fieldset>
</form>


<form class="form-horizontal">
    <fieldset>
    <legend><i class="fas fa-server"></i> {{Option Lien serveur}}</legend>

	<div class="form-group">
			<label class="col-sm-4 control-label">{{Relance de l'identification au serveur}}</label>
				<div class="col-lg-2">
					<div class="input-group">
					<input type="text" class="configKey form-control" data-l1key="autorefresh" placeholder="33 3 * * *"/>
					<span class="input-group-btn">
					<a class="btn btn-success btn-sm " id="bt_cronGenerator" ><i class="fas fa-question-circle"></i></a>
					</span>
					</div>
				</div>
			</div>
</div>
</fieldset>
</form>
<?php
		$foundSelect = false;
		if (config::byKey("listRoutines","alexaapi","") != '') {
			$listRoutines = '';
			$elements = explode(';', config::byKey("listRoutines","alexaapi",""));
			// code trouvé dans core\class\cmd?.class.php
			foreach ($elements as $element) {
				$coupleArray = explode('|', $element);
				$listRoutines .= '<option value="' . $coupleArray[0] . '">' . $coupleArray[1] . '</option>';
				$foundSelect = true;
			}
		}
		if (!$foundSelect) $listRoutines = '<option value="">Aucune</option>' . $listRoutines;
		
		$listRoutinesValidDebut = date("d-m-Y H:i:s",config::byKey("listRoutinesValidDebut","alexaapi",""));
		$listRoutinesValidFin = date("d-m-Y H:i:s",config::byKey("listRoutinesValidFin","alexaapi",""));
		$listRoutinesProchain = date("d-m-Y H:i:s",config::byKey("listRoutinesProchain","alexaapi",""));
		if (config::byKey("listRoutinesValidFin","alexaapi","")=="123") $listRoutinesValidFin=$listRoutinesProchain; //si on a appuyé sur Reset
		?>
<form class="form-horizontal">
    <fieldset>
    <legend><i class="fas fa-project-diagram"></i> {{Routines Amazon}}</legend>
  <div class="form-group">
        <label class="col-sm-4 control-label">{{Liste des Routines Amazon}}</label>
    <div class="col-lg-3">
        <select class="selectCmd"><?php echo $listRoutines?></select>
    </div>
	<div class="col-lg-4">
		<input class="configKey form-control" data-l1key="listRoutines" placeholder="{{en test}}" />
	</div>  
 </div>

  <div class="form-group">
    <label class="col-lg-4 control-label">{{Mise à jour}}</label>
    <div class="col-lg-3">
        Dernière mise à jour : <?php echo $listRoutinesValidDebut?>
    </div>
    <div class="col-lg-3">
        sera rechargée le  <?php echo $listRoutinesValidFin?>
    </div><a class="btn btn-success btn-xs pull-left" id="bt_saveUpdateRoutines"><i class="fas fa-sync"></i> {{Avancer la mise à jour}}</a>
</div>   
</fieldset>
</form>
          

<form class="form-horizontal">
    <fieldset>
    <legend><i class="fas fa-info-circle"></i> {{Informations diverses}}</legend>
	
       <div class="form-group">
        <label class="col-sm-4 control-label">{{Nombre d'équipements détectés}}</label>
    <div class="col-lg-1">
        <input class="configKey form-control" data-l1key="numDevices" />
    </div>
   </div>

   <div class="form-group">
    <label class="col-lg-4 control-label">{{Nombre de players Audio}}</label>
    <div class="col-lg-1">
        <input class="configKey form-control" data-l1key="numAudioPlayer" />
    </div>
</div>   

<div class="form-group">
    <label class="col-lg-4 control-label">{{Nombre d'équipements smartHome}}</label>
    <div class="col-lg-1">
        <input class="configKey form-control" data-l1key="numsmartHome" />
    </div>
</div>
</fieldset>
</form>







  
<script>
$("#bt_saveUpdateRoutines").on('click', function (event) {
//console.log("coucou");
  //var el = $(this);
//console.log(el);
var heureMaintenant=Math.round(+new Date() / 1000);
var heureMaintenant="123";
  jeedom.config.save({
    plugin : 'alexaapi',
//    configuration: {listPlaylistsValidFin: el.attr('data-state')},
    configuration: {listRoutinesValidFin: heureMaintenant},
    error: function (error) {
      $('#div_alert').showAlert({message: error.message, level: 'danger'});
    },
    success: function () {
		$('#md_modal').dialog( "close" );
		$('#md_modal').dialog({title: "{{Configuration du plugin (après Reset validité Routines)}}"});
		$("#md_modal").load('index.php?v=d&p=plugin&ajax=1&id='+eqType).dialog('open');
      //$('#div_alert').showAlert({message: 'coucou', level: 'danger'});
    }
  });

  return false;
});

    var compteVerifCookie=0;
    var CookiePresent=0;
	
	function VerifierSiCookieGenere1() 
		{
		compteVerifCookie++;
		if (compteVerifCookie>8)
			{
		$('.bt_identificationCookie2').hide();
		$('.bt_identificationCookie2bis').hide();
		$('.bt_identificationCookie2echec').show();
			return;
			}
		if ((compteVerifCookie>4) && (CookiePresent=1))
			{
		$('.bt_identificationCookie2').hide();  
		$('.bt_identificationCookie2bis').hide();
		$('.bt_identificationCookie3').show();
			return;
			}		
		$('.bt_identificationCookie1').hide();
		$('.bt_identificationCookie2').show();
		$('.bt_identificationCookie2bis').hide();
		setTimeout(VerifierSiCookieGenere2, 1000);
		}

	function VerifierSiCookieGenere2() 
	{

    jeedom.plugin.VerifiePresenceCookie(
    {
      id : plugin_id,
      forceRestart: 1,
      error: function (error)
      {
//On ne fait rien, on attend le cookie
	  },
      success:function(){
	CookiePresent=1;
      }
    });

		$('.bt_identificationCookie2').hide();
		$('.bt_identificationCookie2bis').show();
		setTimeout(VerifierSiCookieGenere1, 1000);
	}

function PopUpCentre(url, width, height) {
    var leftPosition, topPosition;
    //Allow for borders.
    leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
    //Allow for title and status bars.
    topPosition = (window.screen.height / 2) - ((height / 2) + 50);
    //Open the window.
    nouvellefenetre=window.open(url, "Window2",
    "status=no,height=" + height + ",width=" + width + ",resizable=yes,left="
    + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY="
    + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
		
		
		
if(nouvellefenetre)
	{ //securité pour fermer la fenetre si le focus est perdu
		
		window.onfocus=function()
		{
		nouvellefenetre.window.close();
		$('.bt_identificationCookie').hide();
		  $('.bt_identificationCookie1').hide();
		VerifierSiCookieGenere1();
		}
	
	}
}

  var timeout_refreshDeamonCookieInfo = null;
  $('.bt_stopDeamonCookie').hide();
  $('.bt_identificationCookie').hide();
  $('.bt_identificationCookie2').hide();
  $('.bt_identificationCookie2bis').hide();
  $('.bt_identificationCookie2echec').hide();
  $('.bt_identificationCookie3').hide();
    $('.bt_identificationCookie1').hide();

  // On appuie sur Le lancement du serveur... on lance "deamonCookieStart" via action=deamonCookieStart dans alexaapi.ajax.php
  $('.bt_startDeamonCookie').off('click').on('click',function()
  {
	var textToDisplay='{{<b>NB : Cette fonctionnalité n\'est disponible que en local, pas à distance !!</b>}}';
	if(window.location.hostname != "<?php echo network::getNetworkAccess('internal','ip'); ?>") {
		textToDisplay+='{{<font color="red"><br /><br /><b>!! ATTENTION !!</b><br />Vous n\'accédez pas à votre Jeedom via son ip interne (voir config Jeedom>Réseau)<br />Utilisant un autre port (3457), il est donc possible que cette fonctionnalité ne fonctionne pas... <br />Si c\'est le cas, réessayez à partir de votre réseau interne sur l\'ip interne de Jeedom<br /><br />Ou cliquez sur OK pour essayer quand même...</font>}}';
	}
	bootbox.confirm(textToDisplay, function(result) {
		if (result) {
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
					$('.bt_startDeamonCookie').hide();
					$('.bt_identificationCookie').show();
					timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 1000);
					attendre();
				}
			});
		}
	});
 });
  
  
 $('.bt_supprimeTouslesDevices').off('click').on('click', function() {
	$('#md_modal').dialog('close'); 
	
		bootbox.confirm({
			message: "Etes-vous sûr de vouloir supprimer tous les équipements du plugin Alexa-API (et des autres plugin Alexa-xx) ? Il faudra refaire les scénarios.",
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
					type : 'POST',
					url : 'plugins/alexaapi/core/ajax/alexaapi.ajax.php',
					data : {
						action : 'supprimeTouslesDevices',
					},
					dataType : 'json',
					global : false,
					error : function(request, status, error) {
						//$.hideLoading(); ??
						$('#div_alert').showAlert({
							message : error.message,
							level : 'danger'
						});
					},
					success : function(data) {
						//$.hideLoading();??
						//$('li.li_plugin.active').click();??
						
					}
				});
			}			
			}
		});
	 
});	

 $('.bt_forcerDefaultAllCmd').off('click').on('click', function() {
	$('#md_modal').dialog('close'); 
	
		bootbox.confirm({
			message: "Voulez-vous recharger la configuration par défaut de toutes les commandes du plugin Alexa-API ?",
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
					type : 'POST',
					url : 'plugins/alexaapi/core/ajax/alexaapi.ajax.php',
					data : {
						action : 'forcerDefaultAllCmd',
					},
					dataType : 'json',
					global : false,
					error : function(request, status, error) {
						//$.hideLoading(); ??
						$('#div_alert').showAlert({
							message : error.message,
							level : 'danger'
						});
					},
					success : function(data) {
						//$.hideLoading();??
						//$('li.li_plugin.active').click();??
						
					}
				});
			}			
			}
		});
	 
});	


function attendre() {
window.setTimeout(lancer, 3000);
window.setTimeout(lancer2, 5000);
}



function lancer() {
PopUpCentre("http://<?php print config::byKey('internalAddr')?>:3457", 480, 700);
}

function lancer2() {

  $('.bt_identificationCookie').hide();
  $('.bt_identificationCookie1').show();

}


  $('.bt_stopDeamonCookie').off('click').on('click',function()
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

  $('.bt_identificationCookie').off('click').on('click',function()
  {
  });
  
  $('.bt_identificationCookie1').off('click').on('click',function()
  {VerifierSiCookieGenere1();
  });  
  
  $('#bt_reinstallNodeJS').off('click').on('click', function() {
		bootbox.confirm('{{Etes-vous sûr de vouloir supprimer et reinstaller NodeJS ? <br /> Merci de patienter 10-20 secondes quand vous aurez cliqué...}}', function(result) {
			if (result) {
				$.showLoading();
				$.ajax({
					type : 'POST',
					url : 'plugins/alexaapi/core/ajax/alexaapi.ajax.php',
					data : {
						action : 'reinstallNodeJS',
					},
					dataType : 'json',
					global : false,
					error : function(request, status, error) {
						$.hideLoading();
						$('#div_alert').showAlert({
							message : error.message,
							level : 'danger'
						});
					},
					success : function(data) {
						$.hideLoading();
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
