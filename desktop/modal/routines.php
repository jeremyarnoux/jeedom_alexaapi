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

<div class="input-group " style="float:right">
	<span class="input-group-addon" id="basic-addon1" style="width: 180px">Executer le lancement sur</span>
	<select id="ListeDevices" class="form-control input-sm expressionAttr" style="width: 200px">
		<?php
		$eqLogics = alexaapi::byType('alexaapi');
		foreach ($eqLogics as $eqLogic) {
			if (($eqLogic->getConfiguration('devicetype') != "Player") && ($eqLogic->getConfiguration('devicetype') != "PlayList")) {
				echo '<option value="' . $eqLogic->getConfiguration('serial') . '">' . $eqLogic->getName() . '</option>';
			}
		}
		?>
	</select>
</div>

<?php

log::add('alexaapi', 'debug', '=============================================================Routines');

$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/routines");
//echo $json;
$json = json_decode($json, true);

function sortBy($field, &$array, $direction = 'asc')
{
	usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if ($a == $b) return 0;

        $direction = strtolower(trim($direction));

        return ($a ' . ($direction == 'desc' ? '>' : '<') . ' $b) ? -1 : 1;
    '));

	return true;
}
sortBy('utterance', $json, 'asc');
?>

<table class="table table-condensed tablesorter" id="table_healthNetwork">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>{{Routine}}</th>
			<th>{{Locale, Time zone}}</th>
			<th>{{Répétition}}</th>
			<th>{{Création}}</th>
			<th>{{Mise à jour}}</th>
			<th>{{Activé}}</th>
			<th>{{Lancer}}</th>
			<th>{{ID Routine}}</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($json as $item) {
			if ($item['utterance'] === '')
				$typeroutine = "divers-circular114";
			else
				$typeroutine = "jeedomapp-audiospeak";

			$resultattriggerTime = "";
			$resultattimeZoneId = $item['locale'];

			$repetition = "";
			switch ($item['recurrence']) {
				case "P1D":
					$repetition = "Tous les jours";
					break;
				case "XXXX-WD":
					$repetition = "En semaine";
					break;
				case "XXXX-WE":
					$repetition = "Week-ends";
					break;
				case "XXXX-WXX-1":
					$repetition = "Chaque lundi";
					break;
				case "XXXX-WXX-2":
					$repetition = "Chaque mardi";
					break;
				case "XXXX-WXX-3":
					$repetition = "Chaque mercredi";
					break;
				case "XXXX-WXX-4":
					$repetition = "Chaque jeudi";
					break;
				case "XXXX-WXX-5":
					$repetition = "Chaque vendredi";
					break;
				case "XXXX-WXX-6":
					$repetition = "Chaque samedi";
					break;
				case "XXXX-WXX-7":
					$repetition = "Chaque dimanche";
					break;
			}

			if ($item['triggerTime'] != '') {
				$resultattriggerTime = substr($item['triggerTime'], 0, 2) . ":" . substr($item['triggerTime'], 2, 2);
				$resultattimeZoneId = $item['timeZoneId'];
			}

			$epoch = intval($item['creationTimeEpochMillis'] / 1000);
			$dt = new DateTime("@$epoch"); // convert UNIX timestamp to PHP DateTime
			$datecreation = $dt->format('d-m-Y H:i'); // output = 2017-01-01 00:00:00    

			$epoch = intval($item['lastUpdatedTimeEpochMillis'] / 1000);
			$dt = new DateTime("@$epoch"); // convert UNIX timestamp to PHP DateTime
			$datemaj = $dt->format('d-m-Y H:i'); // output = 2017-01-01 00:00:00     	

			if ($item['status'] == 'ENABLED') {
				$couleur = "success";
				$present = '<span class="label label-' . $couleur . '" style="font-size : 1em;" title="{{Actif}}"><i class="fas fa-check-circle"></i></span>';
			} else {
				$couleur = "default";
				$present = '<span class="label label-' . $couleur . '" style="font-size : 1em;" title="{{Inactif}}"><i class="fas fa-times-circle"></i></span>';
			}

			echo '<tr>';
			echo '<td><span class="label label-' . $couleur . '" style="font-size : 1em;"><i class="fa ' . $typeroutine . '"></i></span></td>';
			echo '<td><span class="label label-' . $couleur . '" style="font-size : 1em; cursor : default;">' . $resultattriggerTime . $item['utterance'] . '</span></td>';
			echo '<td><span class="label label-' . $couleur . '" style="font-size : 1em;">' . $resultattimeZoneId . '</span></td>';
			echo '<td><span class="label label-' . $couleur . '" style="font-size : 1em; cursor : default;">' . $repetition . '</span></td>';
			echo '<td><span class="label label-' . $couleur . '" style="font-size : 1em; cursor : default;">' . $datecreation . '</span></td>';
			echo '<td><span class="label label-' . $couleur . '" style="font-size : 1em; cursor : default;">' . $datemaj . '</span></td>';
			echo '<td>' . $present . '</td>';
			echo '<td><a style="position:relative;top:-5px;" class="btn btn-success RunRoutine" data-id="' . $item['creationTimeEpochMillis'] . '"><i class="fas fa-play"></i></a></td>';
			echo '<td>' . $item['creationTimeEpochMillis'] . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

<a class="btn btn-default pull-right refreshAction" data-action="refresh"><i class="fas fa-refresh"></i> {{Rafraichir}}</a>

<script>
	$('.RunRoutine').off('click').on('click', function() {
		//if($(this).hasClass('btn-default')) return false;
		var selectElmt = document.getElementById("ListeDevices");
		console.log(selectElmt);
		var selectedDevice = selectElmt.options[selectElmt.selectedIndex].value;
		jeedom.plugin.node.action2({
			action: 'testNode',
			node_id: $(this).attr('data-id'),
			node_id2: selectedDevice,
			error: function(error) {
				//$('#div_alert').showAlert({message: error.message, level: 'danger'});
				$('#md_modal').dialog('close');
				$('#md_modal').dialog({
					title: "{{Routines}}"
				});
				$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=routines&id=alexaapi').dialog('open');
			},
			success: function(data) {
				// $('#div_alert').showAlert({message: '{{Action réalisée avec succès}}', level: 'success'});
				$('#md_modal').dialog('close');
				$('#md_modal').dialog({
					title: "{{Routines}}"
				});
				$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=routines&id=alexaapi').dialog('open');
			}
		});
	});

	$('.refreshAction[data-action=refresh]').off('click').on('click', function() {
		$('#md_modal').dialog('close');
		$('#md_modal').dialog({
			title: "{{Routines}}"
		});
		$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=routines&id=alexaapi').dialog('open');
	});
</script>

<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi'); ?>