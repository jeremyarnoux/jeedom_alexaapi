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

echo "<table width=100% border=0><tr><td>";
echo  '<a class="btn btn-default pull-left refreshAction" data-action="refresh"><i class="fa fa-refresh"></i>  {{Rafraichir}}</a>';

	$fichierMediaJson = realpath(dirname(__FILE__) . '/../../resources/data/media-'.$_GET['iddevice'].'.json');

echo "</td><td><center>";
	echo "Dernière mise à jour : ".date ("d F Y H:i:s", filemtime($fichierMediaJson));
echo "</center></td><td>";

$eqLogics = alexaapi::byType('alexaapi');

 //echo $_GET['iddevice'];
	echo '<span class="eqLogicAttr" data-l1key="logicalId"></span>';
    $myData = file_get_contents($fichierMediaJson);
    $myObject = json_decode($myData);


foreach($myObject as $item): 
	 if ($item->info=='imageURL')
		 echo '<p style="float:right"><img src="'.$item->value.'" alt="logo media" /></p>';
 endforeach; 



	
 ?>
</td></tr></table>


<table class="table table-condensed tablesorter" id="table1">
	<thead>
		<tr>
			<th>{{Info}}</th>
			<th>{{Valeur}}</th>

		</tr>
	</thead>
    <tbody>
      <?php foreach($myObject as $item): ?>
        <tr>
          <td><span class="label label-info" style="font-size : 1em; cursor : default;"><?PHP echo $item->info; ?></span></td>
          <td><?PHP echo $item->value; ?></td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

	<?php
	
foreach($myObject as $item): 
	 if ($item->info=='queue')
			{
 ?>



<table class="table table-condensed tablesorter" id="table2">
	<thead>
		<tr>
			<th>{{File d'attente}}</th>
			<th></th>

		</tr>
	</thead>
	<tbody>

      <?php foreach($item->value as $key => $serial): 
				foreach($serial as $key => $suite): ?>
        <tr>
          <td><span class="label label-info" style="font-size : 1em; cursor : default;"><?PHP echo $key; ?></span></td>
          <td><?PHP echo $suite; ?></td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

	<?php
			endforeach; 
			}
 endforeach;	
	
	
	$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/media?device=".$_GET['iddevice']);

?>
<script>

$('.refreshAction[data-action=refresh]').off('click').on('click',function(){
	
/*	const Url='http://192.168.0.21:3456/volume';
	const data={
		value:"50",
		device:"G090LF118173117U"
	};

		$.post(Url, data, function(data, status){
			$('#div_alert').showAlert({message: '55', level: 'danger'});
		console.log('${data} and status is ${status}');
	});
	*/
	
	
	$('#md_modal').dialog('close');
	$('#md_modal').dialog({title: "{{Info Média}}"});
	$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=media&id=alexaapi&iddevice='+ $('.eqLogicAttr[data-l1key=logicalId]').value()).dialog('open');
});

</script>


<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi'); ?>