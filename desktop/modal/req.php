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

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
	
}

if (empty($_GET['json']))
	$_GET['json']="wakewords";

					$partieFichier=$_GET['json'].".json";
					switch ($_GET['json']) {
						case 'wakewords':
							$commande="wakewords";
							$masquedevice=true;
						break;
						case 'devicesfull':
							$commande="devicesfull";
							$masquedevice=true;
						break;
						case 'smarthomedevices':
							$commande="smarthomedevices";
							$masquedevice=true;
						break;
						case 'smarthomegroups':
							$commande="smarthomegroups";
							$masquedevice=true;
						break;						
						case 'smarthomebehaviouractiondefinitions':
							$commande="smarthomebehaviouractiondefinitions";
							$masquedevice=true;
						break;						
						case 'smarthomeentities':
							$commande="smarthomeentities";
							$masquedevice=true;
						break;							
						case 'devicepreferences':
							$commande="devicepreferences";
							$masquedevice=true;
						break;
						case 'homegroup':
							$commande="homegroup";
							$masquedevice=true;
						break;						
						case 'activities':
							$commande="activities";
							$masquedevice=false;
							$partieFichier=$_GET['json']."-".$_GET['device'].".json";
						break;
						case 'media':
							$commande="media";
							$masquedevice=false;
							$partieFichier=$_GET['json']."-".$_GET['device'].".json";
						break;
						case 'playerinfo':
							$commande="playerinfo";
							$masquedevice=false;
							$partieFichier=$_GET['json']."-".$_GET['device'].".json";
						break;					}

$fichierJson = realpath(dirname(__FILE__)) . "/../../resources/data/".$partieFichier;

if (date ("U", filemtime($fichierJson))=="0")
	{
	$regenerejson=@file_get_contents("http://" . config::byKey('internalAddr') . ":3456/".$commande."?device=".$_GET['device']);
//echo "génération de http://" . config::byKey('internalAddr') . ":3456/".$commande."?device=".$_GET['device']."<---";
	}

//echo "--->".date ("U", filemtime($fichierJson))."<---";

echo "<table width=100% border=0><tr><td>";
include_file('desktop', 'jsonviewer', 'php', 'alexaapi');


?>
<div class="input-group " style="float:left"><div class="input-group ">
	<span class="input-group-addon" id="basic-addon1" style="width: 180px">Informations à afficher :</span>
	<select onchange="test();" id="ListeJSON" class="form-control input-sm expressionAttr" style="width: 200px">
<option value="activities" <?php if ($_GET['json']=="activities") echo "selected"?>>Activities</option>
<option value="wakewords" <?php if ($_GET['json']=="wakewords") echo "selected"?>>WakeWords</option>
<option value="devicesfull" <?php if ($_GET['json']=="devicesfull") echo "selected"?>>Devices</option>
<option value="devicepreferences" <?php if ($_GET['json']=="devicepreferences") echo "selected"?>>Préférences</option>
<option value="homegroup" <?php if ($_GET['json']=="homegroup") echo "selected"?>>Home Group</option>
<option value="smarthomegroups" <?php if ($_GET['json']=="smarthomegroups") echo "selected"?>>Smarthome Groups</option>
<option value="smarthomedevices" <?php if ($_GET['json']=="smarthomedevices") echo "selected"?>>Smarthome Devices</option>
<option value="smarthomeentities" <?php if ($_GET['json']=="smarthomeentities") echo "selected"?>>Smarthome Entities</option>
<option value="smarthomebehaviouractiondefinitions" <?php if ($_GET['json']=="smarthomebehaviouractiondefinitions") echo "selected"?>>Smarthome Behaviour Action Definitions Devices</option>
<option value="media" <?php if ($_GET['json']=="media") echo "selected"?>>Media</option>
<option value="playerinfo" <?php if ($_GET['json']=="playerinfo") echo "selected"?>>Player Info</option>
	</select></div><div class="input-group" <?php if ($masquedevice) echo 'style="visibility:hidden;"'; ?> >
	<span class="input-group-addon" id="basic-addon1" style="width: 180px">Utiliser :</span>
	<select onchange="test();" id="ListeDevices" class="form-control input-sm expressionAttr" style="width: 200px">
	<?php
		$eqLogics = alexaapi::byType('alexaapi');
		foreach ($eqLogics as $eqLogic)
		{  
			echo '<option ';
			if ($_GET['device']==$eqLogic->getConfiguration('serial')) echo "selected ";
			echo 'value="'.$eqLogic->getConfiguration('serial').'">'.$eqLogic->getName().'</option>';
		}
	?>
	</select>
</div></div>
<?php


echo "</td><td><center>";
echo "Dernière mise à jour : ".date ("d F Y H:i:s", filemtime($fichierJson));
echo "</center></td><td>";

//echo  '<a class="btn btn-default pull-right refreshAction" data-action="refresh"><i class="fa fa-refresh"></i> Rafraichir</a>';
echo  '<a class="btn btn-success pull-right" target="autre" href="http://'.config::byKey('internalAddr') . '/plugins/alexaapi/resources/data/'.$partieFichier.'"><i class="fa fa-upload"></i> Télécharger JSON</a>';
	
 
 ?>
</td></tr></table>

<br>
<?php
 
//récupere le json
//$file = file_get_contents('http://www.webpagetest.org/jsonResult.php?test=180605_G7_bed851a21eadf7995909b59fcac99212');
//le transforme en array
//$json = json_decode($file,true);
 
//affiche le loadTime
//echo $json['data']['runs'][1]['firstView']['loadTime'];
 
//Affiche tout le tableau
//var_dump($json);

            //$url = "activities.json";

echo '<pre>';
	//$file = realpath(dirname(__FILE__) . '/../../resources/data/media-'.$_GET['iddevice'].'.json');
	//$fichierJson = realpath(dirname(__FILE__) . '/../../resources/data/activities.json');
//echo $fichierJson;
//$file = 'http://www.webpagetest.org/jsonResult.php?test=180605_G7_bed851a21eadf7995909b59fcac99212';
			
            $json = @file_get_contents($fichierJson);
            if (empty($json)) die("Json vide");
        echo json_viewer($json);
echo '</pre>';

/*
if (ob_get_length()) {

			ob_end_flush();

			flush();

			}
*/

$regenerejson=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/".$commande."?device=".$_GET['device']);
//echo "http://" . config::byKey('internalAddr') . ":3456/".$commande."?device=".$_GET['device'];


?>
    
<script>

function test()
{
	var selectElmtJSON = document.getElementById("ListeJSON");
	var selectedJSON = selectElmtJSON.options[selectElmtJSON.selectedIndex].value;	
	var selectElmt = document.getElementById("ListeDevices");
	var selectedDevice = selectElmt.options[selectElmt.selectedIndex].value;	
//window.alert(selectedDevice+"-"+selectedJSON);

	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Historique}}"});
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=req&id=alexaapi&json='+selectedJSON+'&device='+selectedDevice).dialog('open');
	
}

$('.refreshAction[data-action=refresh]').on('click',function(){
//test();
});
</script>
<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi');?>
