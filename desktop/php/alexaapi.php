<?php
include_file('core', 'authentification', 'php');

if (!isConnect('admin'))
  throw new Exception('{{401 - Refused access}}');

// Obtenir l'identifiant du plugin
$plugin = plugin::byId('alexaapi');
// Charger le javascript
sendVarToJS('eqType', $plugin->getId());
//sendVarToJS('serveurtest', 'lionel dans alexaapi.php');

// Accéder aux données du plugin
$eqLogics = eqLogic::byType($plugin->getId());

//---------------------------------------------------------------------------------------
// récupéré de https://github.com/Apollon77/ioBroker.alexa2/blob/master/main.js
$knownDeviceType = array(
    ('A10A33FOX2NUBK') => array( (TypeEcho) => 'Echo Spot', (commandSupport) => 'true', (icon) => 'spot'),
    ('A12GXV8XMS007S') => array( (TypeEcho) => 'FireTV', (commandSupport) => 'false', (icon) => 'firetv'), 
    ('A15ERDAKK5HQQG') => array( (TypeEcho) => 'Sonos', (commandSupport) => 'false', (icon) => 'sonos'),
    ('A17LGWINFBUTZZ') => array( (TypeEcho) => 'Anker Roav Viva Alexa', (commandSupport) => 'false', (icon) => 'other'),
    ('A18O6U1UQFJ0XK') => array( (TypeEcho) => 'Echo Plus 2.Gen', (commandSupport) => 'true', (icon) => 'echo_plus2'), 
    ('A1DL2DVDQVK3Q') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
    ('A1H0CMF1XM0ZP4') => array( (TypeEcho) => 'Echo Dot/Bose', (commandSupport) => 'false', (icon) => 'other'), 
    ('A1J16TEDOYCZTN') => array( (TypeEcho) => 'Fire tab', (commandSupport) => 'true', (icon) => 'firetab'),
    ('A1NL4BVLQ4L3N3') => array( (TypeEcho) => 'Echo Show', (commandSupport) => 'true', (icon) => 'echo_show'), 
    ('A1RTAM01W29CUP') => array( (TypeEcho) => 'Windows App', (commandSupport) => 'false', (icon) => 'other'), 
    ('A1X7HJX9QL16M5') => array( (TypeEcho) => 'Bespoken.io', (commandSupport) => 'false', (icon) => 'other'),
    ('A21Z3CGI8UIP0F') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
    ('A2825NDLA7WDZV') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
    ('A2E0SNTXJVT7WK') => array( (TypeEcho) => 'Fire TV V1', (commandSupport) => 'false', (icon) => 'firetv'),
    ('A2GFL5ZMWNE0PX') => array( (TypeEcho) => 'Fire TV', (commandSupport) => 'true', (icon) => 'firetv'), 
    ('A2IVLV5VM2W81') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
    ('A2L8KG0CT86ADW') => array( (TypeEcho) => 'RaspPi', (commandSupport) => 'false', (icon) => 'other'), 
    ('A2LWARUGJLBYEW') => array( (TypeEcho) => 'Fire TV Stick V2', (commandSupport) => 'false', (icon) => 'firetv'), 
    ('A2M35JJZWCQOMZ') => array( (TypeEcho) => 'Echo Plus', (commandSupport) => 'true', (icon) => 'echo'), 
    ('A2M4YX06LWP8WI') => array( (TypeEcho) => 'Fire Tab', (commandSupport) => 'false', (icon) => 'firetab'), 
    ('A2OSP3UA4VC85F') => array( (TypeEcho) => 'Sonos', (commandSupport) => 'true', (icon) => 'sonos'), 
    ('A2T0P32DY3F7VB') => array( (TypeEcho) => 'echosim.io', (commandSupport) => 'false', (icon) => 'other'),
    ('A2TF17PFR55MTB') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
    ('A32DOYMUN6DTXA') => array( (TypeEcho) => 'Echo Dot 3.Gen', (commandSupport) => 'true', (icon) => 'echo_dot3'),
    ('A37SHHQ3NUL7B5') => array( (TypeEcho) => 'Bose Homespeaker', (commandSupport) => 'false', (icon) => 'other'), 
    ('A38BPK7OW001EX') => array( (TypeEcho) => 'Raspberry Alexa', (commandSupport) => 'false', (icon) => 'raspi'), 
    ('A38EHHIB10L47V') => array( (TypeEcho) => 'Echo Dot', (commandSupport) => 'true', (icon) => 'echo_dot'), 
    ('A3C9PE6TNYLTCH') => array( (TypeEcho) => 'Multiroom', (commandSupport) => 'true', (icon) => 'multiroom'), 
    ('A3H674413M2EKB') => array( (TypeEcho) => 'echosim.io', (commandSupport) => 'false', (icon) => 'other'),
    ('A3HF4YRA2L7XGC') => array( (TypeEcho) => 'Fire TV Cube', (commandSupport) => 'true', (icon) => 'other'), 
    ('A3NPD82ABCPIDP') => array( (TypeEcho) => 'Sonos Beam', (commandSupport) => 'true', (icon) => 'sonos'), 
    ('A3R9S4ZZECZ6YL') => array( (TypeEcho) => 'Fire Tab HD 10', (commandSupport) => 'true', (icon) => 'firetab'), 
    ('A3S5BH2HU6VAYF') => array( (TypeEcho) => 'Echo Dot 2.Gen', (commandSupport) => 'true', (icon) => 'echo_dot'), 
    ('A3SSG6GR8UU7SN') => array( (TypeEcho) => 'Echo Sub', (commandSupport) => 'true', (icon) => 'echo_sub'), 
    ('A7WXQPH584YP') => array( (TypeEcho) => 'Echo 2.Gen', (commandSupport) => 'true', (icon) => 'echo2'), 
    ('AB72C64C86AW2') => array( (TypeEcho) => 'Echo', (commandSupport) => 'true', (icon) => 'echo'), 
    ('ADVBD696BHNV5') => array( (TypeEcho) => 'Fire TV Stick V1', (commandSupport) => 'false', (icon) => 'firetv'), 
    ('AILBSA2LNTOYL') => array( (TypeEcho) => 'reverb App', (commandSupport) => 'false', (icon) => 'reverb'),
    ('AVE5HX13UR5NO') => array( (TypeEcho) => 'Logitech Zero Touch', (commandSupport) => 'false', (icon) => 'other'), 
    ('AWZZ5CVHX2CD') => array( (TypeEcho) => 'Echo Show 2.Gen', (commandSupport) => 'true', (icon) => 'echo_show2')
);



//---------------------------------------------------------------------------------------
console.log($knownDeviceType);

$logicalIdToHumanReadable = array();
foreach ($eqLogics as $eqLogic)
{
  $logicalIdToHumanReadable[$eqLogic->getLogicalId()] = $eqLogic->getHumanName(true, false);
}
?>

<script>
var logicalIdToHumanReadable = <?php echo json_encode($logicalIdToHumanReadable); ?>

function printEqLogic(data)
{
	
  //if (data.configuration.family === undefined)
  //{
//	 $('#family').hide(); //ajouté, masque Famille si c'est vide
 // }	
  
	// Traitement de Multiroom sur les infos du device
  $('#multiroom-members').empty();
  if (data.configuration.members === undefined)
  {
     //$('#multiroom-members').append('Configuration incomplete.'); //supprimé
	 $('#multiroom-members').parent().hide(); //ajouté

     return;
  }
  if (data.configuration.members.length === 0)
  {
    $('#multiroom-members').parent().hide();
    return;
  }

  var html = '<ul style="list-style-type: none;">';
  for (var i in data.configuration.members)
  {
    var logicalId = data.configuration.members[i];
    if (logicalId in logicalIdToHumanReadable)
      html += '<li style="margin-top: 5px;">' + logicalIdToHumanReadable[logicalId] + '</li>';
    else
      html += '<li style="margin-top: 5px;"><span class="label label-default" style="text-shadow : none;"><i>(Non configuré)</i></span> ' + logicalId + '</li>';
  }
  html += '</ul>';

  $('#multiroom-members').parent().show();
  $('#multiroom-members').append(html);
}

</script>

<!-- Container global (Ligne bootstrap) -->
<div class="row row-overflow">
  <!-- Container des listes de commandes / éléments -->
  <div class="col-xs-12 eqLogicThumbnailDisplay">
    <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
    <div class="eqLogicThumbnailContainer">
		<!-- Bouton de scan des objets -->
		<div class="cursor logoPrimary" id="bt_scan">
			<i class="fas fa-bullseye"></i>
			<br />
			<span>{{Scan}}</span>
		</div>

		<!-- Bouton d accès à la configuration -->
		<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
			<i class="fas fa-wrench"></i>
			<br />
			<span>{{Configuration}}</span>
		</div>

		<div class="cursor logoSecondary" id="bt_sante">
			<i class="fas fa-medkit"></i>
			<br />
			<span>{{Santé}}</span>
		</div>


		<div class="cursor logoSecondary" id="bt_routines">
			<i class="fas divers-viral"></i>
			<br />
			<span>{{Routines}}</span>
		</div>


		<div class="cursor logoSecondary" id="bt_reminders">
			<i class="fas fa-clock"></i>
			<br />
			<span>{{Rappels/Alarmes}}</span>
		</div>


		<div class="cursor logoSecondary" id="bt_history">
			<i class="fas fa-list-alt"></i>
			<br />
			<span>{{Historique}}</span>
		</div>
<?php
if(log::getLogLevel('alexaapi')<200) :
?>

		<div class="cursor logoSecondary" id="bt_req">
			<i class="fas fa-key"></i>
			<br />
			<span>{{Requêteur JSON}}</span>
		</div>

		<div class="cursor logoSecondary" id="bt_req2">
			<i class="fas fa-key"></i>
			<br />
			<span>{{Requêteur}}</span>
		</div><?php
endif;
?>	  
	  
    </div>
    <!-- Début de la liste des objets -->
    <legend><i class="fas fa-table"></i> {{Mes Amazon Echo}}</legend>
	<div class="input-group" style="margin-bottom:5px;">
		<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="input-group-btn">
			<a id="bt_resetEqlogicSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i></a>
		</div>
	</div>	
    <!-- Container de la liste -->
    <div class="eqLogicThumbnailContainer prem">
<?php
foreach($eqLogics as $eqLogic) {

	if ($eqLogic->getConfiguration('devicetype') != "Smarthome") {

		$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
		echo '<div class="eqLogicDisplayCard cursor prem" data-eqLogic_id="'.$eqLogic->getId().'" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;'.$opacity.'" >';

		if (($eqLogic->getStatus('online') != 'true') && (!strstr($eqLogic->getName(), "Alexa Apps")))
			echo '<i class="fas fa-power-off" style="color: red;text-shadow: 4px 4px 4px #ccc;float:right" title="Offline"></i>';

		//$alternateImg = $knownDeviceType[$eqLogic->getConfiguration('type')]['icon'];
		$alternateImg = $eqLogic->getConfiguration('type');
		echo '<center>';
		if (file_exists(dirname(__FILE__).'/../../core/config/devices/'.$alternateImg.'.png'))
			echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/'.$alternateImg.'.png" height="105" width="105" />';
		elseif(file_exists(dirname(__FILE__).'/../../core/config/devices/'.$eqLogic->getConfiguration('family').'.png'))
			echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/'.$eqLogic->getConfiguration('family').'.png" height="105" width="105" />';
		elseif(file_exists(dirname(__FILE__).'/../../core/config/devices/default.png'))
			echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/default.png" height="105" width="105" />';
		else
			echo '<img src="'.$plugin->getPathImgIcon().'" height="105" width="105" />';

		echo '</center>';
		echo '<span  class="name"><center>'.$eqLogic->getHumanName(true, true).'</center></span>';

		echo '</div>';
	}
}
?>
    </div>
    <!-- Début de la liste des objets -->
    <legend><i class="fas fa-table"></i> {{Mes Amazon Smarthome}}</legend>
	<div class="input-group" style="margin-bottom:5px;">
		<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic2" />
		<div class="input-group-btn">
			<a id="bt_resetEqlogicSearch2" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i></a>
		</div>
	</div>	
    <!-- Container de la liste -->
    <div class="eqLogicThumbnailContainer second">
<?php
foreach($eqLogics as $eqLogic) {
	if ($eqLogic->getConfiguration('devicetype') == "Smarthome") {

		$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
		echo '<div class="eqLogicDisplayCard cursor second" data-eqLogic_id="'.$eqLogic->getId().'" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;'.$opacity.'" >';

		if (($eqLogic->getStatus('online') != 'true') && (!strstr($eqLogic->getName(), "Alexa Apps")))
			echo '<i class="fas fa-power-off" style="color: red;text-shadow: 4px 4px 4px #ccc;float:right" title="Offline"></i>';

		//$alternateImg = $knownDeviceType[$eqLogic->getConfiguration('type')]['icon'];
		$alternateImg = $eqLogic->getConfiguration('type');
		echo '<center>';
		if (file_exists(dirname(__FILE__).'/../../core/config/devices/'.$alternateImg.'.png'))
			echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/'.$alternateImg.'.png" height="105" width="105" />';
		elseif(file_exists(dirname(__FILE__).'/../../core/config/devices/'.$eqLogic->getConfiguration('family').'.png'))
			echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/'.$eqLogic->getConfiguration('family').'.png" height="105" width="105" />';
		elseif(file_exists(dirname(__FILE__).'/../../core/config/devices/default.png'))
			echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/default.png" height="105" width="105" />';
		else
			echo '<img src="'.$plugin->getPathImgIcon().'" height="105" width="105" />';

		echo '</center>';
		echo '<span  class="name"><center>'.$eqLogic->getHumanName(true, true).'</center></span>';

		echo '</div>';
	}
}
?>
    </div>  </div>
  <!-- Container du panneau de contrôle -->
  <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
    <!-- Bouton sauvegarder -->
    <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
    <!-- Bouton Supprimer -->
    <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
    <!-- Bouton configuration avancée -->
    <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
    <!-- Liste des onglets -->
    <ul class="nav nav-tabs" role="tablist">
      <!-- Bouton de retour -->
      <li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
      <!-- Onglet "Equipement" -->
      <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
      <!-- Onglet "Commandes" -->
      <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
    </ul>
    <!-- Container du contenu des onglets -->
    <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
      <div role="tabpanel" class="tab-pane active" id="eqlogictab">
        <br/>
        <div class="row">
          <div class="col-sm-7">
            <form class="form-horizontal">
              <fieldset>
                <div class="form-group">
                  <label class="col-sm-4 control-label">{{Nom de l'équipement Jeedom}}</label>
                  <div class="col-sm-8">
                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement Amazon}}"/>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">{{Nom de l'équipement Amazon}}</label> 
                  <div class="col-sm-8"> 
                    <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="device"> </span>
                  </div>
                </div>
                <!-- Onglet "Objet Parent" -->
                <div class="form-group">
                  <label class="col-sm-4 control-label">{{Objet parent}}</label>
                  <div class="col-sm-6">
                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;"/>
                    <select class="eqLogicAttr form-control" data-l1key="object_id">
                    <option value="">{{Aucun}}</option>
<?php
foreach (jeeObject::all() as $object)
    echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
?>
                    </select>
                  </div>
                </div>
                <!-- Catégorie" -->
                <div class="form-group">
                  <label class="col-sm-4 control-label">{{Catégorie}}</label>
                  <div class="col-sm-8">
<?php
foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value)
{
    echo '<label class="checkbox-inline">';
    echo '  <input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
    echo '</label>';
}
?>
                  </div>
                </div>
                <!-- Onglet "Active Visible" -->
                <div class="form-group">
                  <label class="col-sm-4 control-label"></label>
                  <div class="col-sm-8">
                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                  </div>
                </div>
              </fieldset>
            </form>
          </div>
		  
<!--		  
<div class="cursor" id="bt_media" data-l1key="logicalId" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
	<center>
	<i class="fa loisir-musical7" style="font-size : 6em;color:#767676;"></i>
	</center>
<span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Info Média}}</center></span>
</div>
	 	Castré par Nebz et HadesDT   
<div class="cursor" id="bt_test" data-l1key="logicalId" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
<center>
<i class="fa loisir-musical7" style="font-size : 6em;color:#767676;"></i>
</center>
<span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Test}}</center></span>
</div>
-->
<?php

	//if ($eqLogic->getConfiguration('devicetype')!="Smarthome")
	//{
?>		
          <div class="col-sm-5">
            <form class="form-horizontal">
              <fieldset>
                <div class="form-group">
                  <label class="col-sm-2 control-label">{{ID}}</label>
                  <div class="col-sm-8">
                      <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="logicalId"></span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label">{{Type}}</label>
                  <div class="col-sm-8">
                      <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="type"></span>
                  </div>
                </div>
                <div class="form-group" id="family">
                  <label class="col-sm-2 control-label">{{Famille}}</label>
                  <div class="col-sm-8">
                      <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="family"></span>
                  </div>
                </div>                <!-- Onglet "Image" -->
		<div class="form-group">
                  <label class="col-sm-2 control-label">{{Fonctionnalités}}</label>
                  <div class="col-sm-8">
                      <span style="position:relative;top:+5px;left:+5px;font-size: 10px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="capabilities"></span>
                  </div>		      
                <div class="form-group">
                  <div class="col-sm-10">
                    <center>
                      <img src="core/img/no_image.gif" data-original=".jpg" id="img_device" class="img-responsive" style="max-height : 250px;"  onerror="this.src='plugins/alexaapi/core/config/devices/default.png'"/>
                    </center>
                  </div>
                </div>
                
				
				
                <div class="form-group">
                  <label class="col-sm-2 control-label">{{Multiroom}}</label>
                  <div class="col-sm-8" id="multiroom-members">
                  <!-- Liste des membres du multiroom -->

                  </div>
                </div>
              </fieldset>
            </form>
          </div>
		  
<?php
	/*}
	else
	{
		echo "coucou";
	}*/
?>		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
        </div>
      </div>

      <div role="tabpanel" class="tab-pane" id="commandtab">
        

        <table id="table_cmd" class="table table-bordered table-condensed">
          <thead>
            <tr>
              <th style="width: 40px;">#</th>
              <th style="width: 200px;">{{Nom}}</th>
              <th style="width: 150px;">{{Type}}</th>
              <th style="width: 300px;">{{Commande & Variable}}</th>
              <th style="width: 40px;">{{Min}}</th>
              <th style="width: 40px;">{{Max}}</th>
              <th style="width: 150px;">{{Paramètres}}</th>
              <th style="width: 100px;"></th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
		
    <?php
	
	if (config::byKey('utilisateurExperimente', 'alexaapi')!="0")
	{	
	?>
		
		<form class="form-horizontal">
          <fieldset>
            <div class="form-actions">
              <a class="btn btn-success btn-sm cmdAction" id="bt_addespeasyAction"><i class="fa fa-plus-circle"></i> {{Ajouter une commande action}}</a>
            </div>
          </fieldset>
        </form>
<?php
	}
?>		
      </div>






    </div>
  </div>
</div>

<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi'); ?>
<?php include_file('desktop', 'alexaapi', 'css', 'alexaapi'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
<script>
$('#in_searchEqlogic').off('keyup').keyup(function () {
  var search = $(this).value().toLowerCase();
  search = search.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
  if(search == ''){
    $('.eqLogicDisplayCard.prem').show();
    $('.eqLogicThumbnailContainer.prem').packery();
    return;
  }
  $('.eqLogicDisplayCard.prem').hide();
  $('.eqLogicDisplayCard.prem .name').each(function(){
    var text = $(this).text().toLowerCase();
    text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
    if(text.indexOf(search) >= 0){
      $(this).closest('.eqLogicDisplayCard.prem').show();
    }
  });
  $('.eqLogicThumbnailContainer.prem').packery();
});
</script>
