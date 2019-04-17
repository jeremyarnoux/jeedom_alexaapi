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
    'A10A33FOX2NUBK' => array('Echo Spot', 'commandSupport' => 'true', 'icon' => 'icons/spot.png'),
    'A12GXV8XMS007S' => array('FireTV', 'commandSupport' => 'false', 'icon' => 'icons/firetv.png'), 
    'A15ERDAKK5HQQG' => array('Sonos', 'commandSupport' => 'false', 'icon' => 'icons/sonos.png'),
    'A17LGWINFBUTZZ' => array('Anker Roav Viva Alexa', 'commandSupport' => 'false', 'icon' => 'icons/other.png'),
    'A18O6U1UQFJ0XK' => array('Echo Plus 2.Gen', 'commandSupport' => 'true', 'icon' => 'icons/echo_plus2.png'), 
    'A1DL2DVDQVK3Q' => array('Apps', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A1H0CMF1XM0ZP4' => array('Echo Dot/Bose', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A1J16TEDOYCZTN' => array('Fire tab', 'commandSupport' => 'true', 'icon' => 'icons/firetab.png'),
    'A1NL4BVLQ4L3N3' => array('Echo Show', 'commandSupport' => 'true', 'icon' => 'icons/echo_show.png'), 
    'A1RTAM01W29CUP' => array('Windows App', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A1X7HJX9QL16M5' => array('Bespoken.io', 'commandSupport' => 'false', 'icon' => 'icons/other.png'),
    'A21Z3CGI8UIP0F' => array('Apps', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A2825NDLA7WDZV' => array('Apps', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A2E0SNTXJVT7WK' => array('Fire TV V1', 'commandSupport' => 'false', 'icon' => 'icons/firetv.png'),
    'A2GFL5ZMWNE0PX' => array('Fire TV', 'commandSupport' => 'true', 'icon' => 'icons/firetv.png'), 
    'A2IVLV5VM2W81' => array('Apps', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A2L8KG0CT86ADW' => array('RaspPi', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A2LWARUGJLBYEW' => array('Fire TV Stick V2', 'commandSupport' => 'false', 'icon' => 'icons/firetv.png'), 
    'A2M35JJZWCQOMZ' => array('Echo Plus', 'commandSupport' => 'true', 'icon' => 'icons/echo.png'), 
    'A2M4YX06LWP8WI' => array('Fire Tab', 'commandSupport' => 'false', 'icon' => 'icons/firetab.png'), 
    'A2OSP3UA4VC85F' => array('Sonos', 'commandSupport' => 'true', 'icon' => 'icons/sonos.png'), 
    'A2T0P32DY3F7VB' => array('echosim.io', 'commandSupport' => 'false', 'icon' => 'icons/other.png'),
    'A2TF17PFR55MTB' => array('Apps', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A32DOYMUN6DTXA' => array('Echo Dot 3.Gen', 'commandSupport' => 'true', 'icon' => '/icons/echo_dot3.png'),
    'A37SHHQ3NUL7B5' => array('Bose Homespeaker', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'A38BPK7OW001EX' => array('Raspberry Alexa', 'commandSupport' => 'false', 'icon' => 'icons/raspi.png'), 
    'A38EHHIB10L47V' => array('Echo Dot', 'commandSupport' => 'true', 'icon' => '/icons/echo_dot.png'), 
    'A3C9PE6TNYLTCH' => array('Multiroom', 'commandSupport' => 'true', 'icon' => '/icons/multiroom.png'), 
    'A3H674413M2EKB' => array('echosim.io', 'commandSupport' => 'false', 'icon' => 'icons/other.png'),
    'A3HF4YRA2L7XGC' => array('Fire TV Cube', 'commandSupport' => 'true', 'icon' => 'icons/other.png'), 
    'A3NPD82ABCPIDP' => array('Sonos Beam', 'commandSupport' => 'true', 'icon' => 'icons/sonos.png'), 
    'A3R9S4ZZECZ6YL' => array('Fire Tab HD 10', 'commandSupport' => 'true', 'icon' => 'icons/firetab.png'), 
    'A3S5BH2HU6VAYF' => array('Echo Dot 2.Gen', 'commandSupport' => 'true', 'icon' => '/icons/echo_dot.png'), 
    'A3SSG6GR8UU7SN' => array('Echo Sub', 'commandSupport' => 'true', 'icon' => '/icons/echo_sub.png'), 
    'A7WXQPH584YP' => array('Echo 2.Gen', 'commandSupport' => 'true', 'icon' => '/icons/echo2.png'), 
    'AB72C64C86AW2' => array('Echo', 'commandSupport' => 'true', 'icon' => '/icons/echo.png'), 
    'ADVBD696BHNV5' => array('Fire TV Stick V1', 'commandSupport' => 'false', 'icon' => 'icons/firetv.png'), 
    'AILBSA2LNTOYL' => array('reverb App', 'commandSupport' => 'false', 'icon' => 'icons/reverb.png'),
    'AVE5HX13UR5NO' => array('Logitech Zero Touch', 'commandSupport' => 'false', 'icon' => 'icons/other.png'), 
    'AWZZ5CVHX2CD' => array('Echo Show 2.Gen', 'commandSupport' => 'true', 'icon' => '/icons/echo_show2.png')
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
  $('#multiroom-members').empty();
  if (data.configuration.members === undefined)
  {
     $('#multiroom-members').append('Configuration incomplete.');
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
  <!-- Container bootstrap du menu latéral -->
  <div class="col-lg-2 col-md-3 col-sm-4">
    <!-- Container du menu latéral -->
    <div class="bs-sidebar">
    <!-- Menu latéral -->
      <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
        <!-- Filtre des objets -->
        <li class="filter" style="margin-bottom: 5px; width: 100%"><input class="filter form-control input-sm" placeholder="{{Rechercher}}"/></li>
        <!-- Liste des objets -->
        <?php foreach ($eqLogics as $eqLogic) : ?>
        <li class="cursor li_eqLogic" data-eqLogic_id="<?php echo $eqLogic->getId(); ?>">
          <a><?php echo $eqLogic->getHumanName(true); ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <script>
    $('#bt_backupsZwave').on('click', function () {
      $('#md_modal2').dialog({title: "{{Génération cookie Amazon}}"});
      $('#md_modal2').load('index.php?v=d&plugin=alexaapi&modal=cookie').dialog('open');
    });
  </script>

  <!-- Container des listes de commandes / éléments -->
  <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay">
    <legend><i class="fa fa-cog"></i> {{Gestion}}</legend>
    <div class="eqLogicThumbnailContainer">
      <!-- Bouton de scan des objets -->
      <div class="cursor" id="bt_scan" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
        <center>
          <i class="fa fa-bullseye" style="font-size : 6em;color:#94ca02;"></i>
        </center>
          <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>{{Scan}}</center></span>
      </div>
	  
	        <!-- Bouton d accès à la configuration -->
      <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
        <center>
          <i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
      </div>
	  
      <div class="cursor" id="bt_sante" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
        <center>
          <i class="fa fa-medkit" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
      </div>
	  
	  
      <div class="cursor" id="bt_routines" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
        <center>
          <i class="fa divers-viral" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Routines}}</center></span>
       </div>
	  
	  
      <div class="cursor" id="bt_reminders" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
        <center>
          <i class="fa fa-clock" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Rappels/Alarmes}}</center></span>
      </div>

      <div class="cursor" id="bt_history" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
        <center>
          <i class="fa fa-list-alt" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Historique}}</center></span>
      </div>

      <div class="cursor" id="bt_req" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
        <center>
          <i class="fa fa-key" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Requêteur}}</center></span>
      </div>
	  
	  
    </div>
    <!-- Début de la liste des objets -->
    <legend><i class="fa fa-table"></i> {{Mes Amazon Echo}}</legend>
    <!-- Container de la liste -->
    <div class="eqLogicThumbnailContainer">
<?php
foreach ($eqLogics as $eqLogic)
{
    $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
    echo '<center>';

    $alternateImg = $eqLogic->getConfiguration('type');
    if (file_exists(dirname(__FILE__) . '/../../core/config/devices/' . $alternateImg . '.png'))
        echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/' . $alternateImg . '.png" height="105" width="105" />';
    elseif (file_exists(dirname(__FILE__) . '/../../core/config/devices/default.png'))
        echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/default.png" height="105" width="105" />';
    else
        echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="105" />';

    echo '</center>';
    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
    echo '</div>';
}
?>
    </div>
  </div>
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
foreach (object::all() as $object)
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

	  
          <div class="col-sm-5">
            <form class="form-horizontal">
              <fieldset>
                <div class="form-group">
                  <label class="col-sm-2 control-label">{{ID}}</label>
                  <div class="col-sm-8">
                      <span class="eqLogicAttr" data-l1key="logicalId"></span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label">{{Type}}</label>
                  <div class="col-sm-8">
                      <span class="eqLogicAttr" data-l1key="configuration" data-l2key="type"></span>
                  </div>
                </div>
                <!-- Onglet "Image" -->
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
		
    
		
		<form class="form-horizontal">
          <fieldset>
            <div class="form-actions">
              <a class="btn btn-success btn-sm cmdAction" id="bt_addespeasyAction"><i class="fa fa-plus-circle"></i> {{Ajouter une commande action}}</a>
            </div>
          </fieldset>
        </form>
		
      </div>






    </div>
  </div>
</div>

<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi'); ?>
<?php include_file('desktop', 'alexaapi', 'css', 'alexaapi'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
