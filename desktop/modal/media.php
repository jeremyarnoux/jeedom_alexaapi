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
/*
	DEPLACE DANS 	public static function ScanAmazonAlexa($_logical_id = null, $_exclusion = 0) de core class alexaapi.class.php

$json=file_get_contents("http://192.168.0.21:3456/devices");
$json = json_decode($json,true);

foreach($json as $item)
	{



						$device = $item['name'];
						$serial = $item['serial'];
						$type = $item['type'];
						$online = $item['online'];


		$alexaapi = alexaapi::byLogicalId($serial, 'alexaapi');
		if (!is_object($alexaapi)) {
			$alexaapi = new alexaapi();
			$alexaapi->setName($device);
			$alexaapi->setLogicalId($serial); 
			$alexaapi->setEqType_name('alexaapi');
			$alexaapi->setIsEnable(1);
			$alexaapi->setIsVisible(1);
		}
		$alexaapi->setConfiguration('serial',$serial); 
		$alexaapi->setConfiguration('device',$device);
		$alexaapi->setConfiguration('type',$type);
		$alexaapi->setStatus('online',$online);
		$alexaapi->save();
 }
*/

$eqLogics = alexaapi::byType('alexaapi');
?>



                  <!-- Onglet MEDIA -->

        

 <?php
 //echo $_GET['iddevice'];
	$fichierMediaJson = realpath(dirname(__FILE__) . '/../../resources/data/media-'.$_GET['iddevice'].'.json');
	echo '<span class="eqLogicAttr" data-l1key="logicalId"></span>';
    $myData = file_get_contents($fichierMediaJson);
    $myObject = json_decode($myData);
    //$myObjectMap = $myObject->MRData->RaceTable->Races;
    $myObjectMap = $myObject;
  ?>
<table class="table table-condensed tablesorter" id="table">
    <tbody>
      <?php foreach($myObjectMap as $key => $item): ?>
        <tr>
          <td><span class="label label-info" style="font-size : 1em; cursor : default;"><?PHP echo $item->info; ?></span></td>
          <td><?PHP echo $item->value; ?></td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

	<?php
	echo "Dernière mise à jour : ".date ("d F Y H:i:s", filemtime($fichierMediaJson));
?>