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

$json=file_get_contents("http://192.168.0.21:3456/reminders");
$json = json_decode($json,true);
/*

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

//$eqLogics = alexaapi::byType('alexaapi');
?>
<table class="table table-condensed tablesorter" id="table_healthNetwork">
	<thead>
		<tr>
			<th>{{Type}}</th>
			<th>{{Nom}}</th>
			<th>{{Heure}}</th>
			<th>{{Date}}</th>
			<th>{{Actif}}</th>
			<th>{{Répétition}}</th>
		</tr>
	</thead>
	<tbody>
	 <?php
foreach($json as $item)
{

	$type="Rappel";
	if ($item['type'] == 'Alarm')
		$type="Alarme";

	
	if ($item['status'] == 'ON'){
		$present = '<span class="label label-success" style="font-size : 1em;" title="{{Actif}}"><i class="fa fa-check-circle"></i></span>';
	} else {
		$present = '<span class="label label-danger" style="font-size : 1em;" title="{{Inactif}}"><i class="fa fa-times-circle"></i></span>';
	}
	
$repetition="";	
	switch ($item['recurringPattern']) {
    case "P1D":
        $repetition="Tous les jours";
        break;
    case "XXXX-WD":
        $repetition="En semaine";
        break;
    case "XXXX-WE":
        $repetition="Week-ends";
        break;
    case "XXXX-WXX-1":
        $repetition="Chaque lundi";
        break;
    case "XXXX-WXX-2":
        $repetition="Chaque mardi";
        break;
    case "XXXX-WXX-3":
        $repetition="Chaque mercredi";
        break;
    case "XXXX-WXX-4":
        $repetition="Chaque jeudi";
        break;
    case "XXXX-WXX-5":
        $repetition="Chaque vendredi";
        break;
    case "XXXX-WXX-6":
        $repetition="Chaque samedi";
        break;
    case "XXXX-WXX-7":
        $repetition="Chaque dimanche";
        break;
}


	echo '<tr><td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $type . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $item['reminderLabel'] . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . substr($item['originalTime'],0,5) . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' .substr($item['originalDate'],8,2). substr($item['originalDate'],4,4). substr($item['originalDate'],0,4) . '</span></td>';
	echo '<td>' . $present . '</td>';
	echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $repetition . '</span></td>';
	echo '</tr>';
}
?>
	</tbody>
</table>
