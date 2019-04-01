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

$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/history?size=15");
$json = json_decode($json,true);

function sortBy($field, &$array, $direction = 'asc')
{
    usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if ($a == $b) return 0;

        $direction = strtolower(trim($direction));

        return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
    '));

    return true;
}

?>

<table class="table table-condensed tablesorter" id="table_healthNetwork">
	<thead>
		<tr>
			<th>{{Alexa}}</th>
			<th>{{Texte}}</th>
			<th>{{Nom ou Musique}}</th>
			<th>{{Status}}</th>

		</tr>
	</thead>
	<tbody>
	 <?php
	
	 
	 
foreach($json as $item)
{


		$couleur="success";
	
            // Retireve the device (if already registered in Jeedom)
            $device = alexaapi::byLogicalId($item['deviceSerialNumber'], 'alexaapi');
          if ($device)
	echo '<tr><td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">'.$device->getName().'</span></td>';
			else
	echo '<tr><td><span class="label label-danger" style="font-size : 1em; cursor : default;">?????</span></td>';


	echo '<td><span style="font-size : 1em; cursor : default;">' . $item['summary'] . '</span></td>';
			
			$heures=date("d-m-Y H:i:s",intval($item['creationTimestamp']/1000));
			
			echo '<td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">' .$heures.'</span></td>';


	echo '<td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">' . $item['activityStatus'] . '</span></td>';
	

	echo '</tr>';
}
?>
	</tbody>
</table>

<a class="btn btn-default pull-right refreshAction" data-action="refresh"><i class="fa fa-refresh"></i>  {{Rafraichir}}</a>
    
<script>

$('.refreshAction[data-action=refresh]').on('click',function(){
	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Historique}}"});
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=history&id=alexaapi').dialog('open');
});
</script>

<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi');?>