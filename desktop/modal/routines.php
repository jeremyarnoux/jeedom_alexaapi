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

$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/routines");
//echo $json;
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
sortBy('status', $json, 'desc');
 ?>
<table class="table table-condensed tablesorter" id="table_healthNetwork">
	<thead>
		<tr>
			<th>{{Routine}}</th>
			<th>{{Locale}}</th>
			<th>{{Création}}</th>
			<th>{{Mise à jour}}</th>
			
			<th>{{Activé}}</th>
          		<th>{{Lancer}}</th>			
		</tr>
	</thead>
	<tbody>
	 <?php
foreach($json as $item)
{


		
        
	

	if ($item['status'] == 'ENABLED'){
      $couleur="success";
      $present = '<span class="label label-success" style="font-size : 1em;" title="{{Actif}}"><i class="fa fa-check-circle"></i></span>';
	} else {
		$present = '<span class="label label-default" style="font-size : 1em;" title="{{Inactif}}"><i class="fa fa-times-circle"></i></span>';
		$couleur="default";
	}

		$type = '<span class="label label-'.$couleur.'" style="font-size : 1em;">'. $item['locale']. '</span>';
        
	echo '<tr><td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">'. $item['utterance']. '</span></td>';




	echo '<td>' . $type . '</td>';
	

  
    
    
    $epoch = intval($item['creationTimeEpochMillis']/1000) ; 
  $dt = new DateTime("@$epoch"); // convert UNIX timestamp to PHP DateTime
  $datecreation= $dt->format('d-m-Y H:i'); // output = 2017-01-01 00:00:00    
   
   $epoch = intval($item['lastUpdatedTimeEpochMillis']/1000) ; 
  $dt = new DateTime("@$epoch"); // convert UNIX timestamp to PHP DateTime
  $datemaj= $dt->format('d-m-Y H:i'); // output = 2017-01-01 00:00:00      
    
  
  			echo '<td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">' .$datecreation.'</span></td>';
		
	echo '<td><span class="label label-'.$couleur.'" style="font-size : 1em; cursor : default;">' .$datemaj. '</span></td>';
	echo '<td>' . $present . '</td>';
	
	echo '<td><a style="position:relative;top:-5px;" class="btn btn-success deletexxxReminder" data-id="'. $item['id'] .'"><i class="fas fa-play"></i></a></td>';
			//$present = '<span class="label label-default" style="font-size : 1em;" title="{{Inactif}}"><i class="fa fa-times-circle"></i></span>';

	echo '</tr>';
}
?>
	</tbody>
</table>  
  
  
<a class="btn btn-default pull-right refreshAction" data-action="refresh"><i class="fa fa-refresh"></i>  {{Rafraichir}}</a>
    
<script>


$('.deleteReminder').on('click',function(){
    jeedom.plugin.node.action({
        action : 'testNode',
        node_id: $(this).attr('data-id'),
        error: function (error) {
	//$('#div_alert').showAlert({message: error.message, level: 'danger'});
	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Rappels / Alarmes}}"});
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=reminders&id=alexaapi').dialog('open');

       },
       success: function (data) {
        // $('#div_alert').showAlert({message: '{{Action réalisée avec succès}}', level: 'success'});
	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Rappels / Alarmes}}"});
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=reminders&id=alexaapi').dialog('open');
     }
 });
});


$('.deleteReminder2').on('click',function(){
	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Rappels / Alarmes}}"});
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=reminders&id=alexaapi').dialog('open');
});

$('.refreshAction[data-action=refresh]').on('click',function(){
	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Routines}}"});
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=routines&id=alexaapi').dialog('open');
});
</script>

<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi');?>