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



?>
<div class="input-group " style="float:right"><a class="btn btn-default pull-right refreshAction" data-action="refresh"><i class="fa fa-refresh"></i>  {{Rafraichir}}</a>
</div>

<?php
/* Suspendu pour l'instant

<div class="input-group " style="float:right">
	<span class="input-group-addon" id="basic-addon1" style="width: 180px">Executer le lancement sur</span>
	<select id="Liste15a5000" class="form-control input-sm expressionAttr" style="width: 200px">
	<option value='15'>15</option>
	<option value='40'>40</option>
	<option value='60'>60</option>
	<option value='500'>500</option>
	<option value='1000'>1000</option>
	<option value='2000'>2000</option>
	<option value='5000'>5000</option>
	</select>
</div>	


<?php



//$data_path = realpath(dirname(__FILE__) . '/../../resources/data');
//echo $data_path;

if ($_GET['size'] == "")
	$size=15;
else
	$size=$_GET['size'];
$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/history?size=".$size);
*/
$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/history?size=50");
//file_put_contents($data_path, $json);
$json = json_decode($json,true);
//echo "****"."http://" . config::byKey('internalAddr') . ":3456/history?size=".$size."****";
?>

<table class="table table-condensed tablesorter" id="table_healthNetwork">
	<thead>
		<tr>
			<th>{{Alexa}}</th>
			<th>{{Texte}}</th>
			<th>{{Date Heure}}</th>
			<th>{{Status}}</th>

		</tr>
	</thead>
	<tbody>
	 <?php
	
	$TouslesDevices = array(); 
	//$TouslesDevices["coucou"] = "sonnom";
 $compteur=1;
foreach($json as $item)
{


		$couleur="success";
//Petit tableau pour garder les valeurs de devices			
if (array_key_exists($item['deviceSerialNumber'], $TouslesDevices)) 
$ledevice = $TouslesDevices[$item['deviceSerialNumber']];	
else
{
    $device = alexaapi::byLogicalId($item['deviceSerialNumber'], 'alexaapi');
	if (is_object($device)) {
		$ledevice=$device->getName();
		$TouslesDevices[$item['deviceSerialNumber']] = $ledevice;
	} else {
		continue;
	}
}	
//***************************************************
$compteur=""; //pour vérifier les lignes

			
          if ($ledevice!="")
	echo '<tr><td>'.$compteur.'<span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">'.$ledevice.'</span></td>';
			else
	echo '<tr><td>'.$compteur.'<span class="label label-danger" style="font-size : 1em; cursor : default;">?????</span></td>';

$compteur++;
	echo '<td><span style="font-size : 1em; cursor : default;">' . str_replace("jacques dit", "(via Jeedom)", $item['summary']) . '</span></td>';
			
			$heures=date("d-m-Y H:i:s",intval($item['creationTimestamp']/1000));
			
			echo '<td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">' .$heures.'</span></td>';


	echo '<td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">' . $item['activityStatus'] . '</span></td>';
	

	echo '</tr>';
	
}
?>
	</tbody>
</table>

    
<script>

$('.refreshAction[data-action=refresh]').on('click',function(){
	//var selectElmt = document.getElementById("Liste15a5000");
	//var selectedSize = selectElmt.options[selectElmt.selectedIndex].value;	

	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Historique}}"});
//	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=history&id=alexaapi&size='+selectedSize).dialog('open');
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=history&id=alexaapi').dialog('open');
});
</script>

<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi');?>
