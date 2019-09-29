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
//sendVarToJS('log_display_name', init('log', 'event'));
sendVarToJS('log_display_name', "alexaapi_node");
sendVarToJS('log_default_search', init('search', ''));
if(init('log','event') == 'event'){
	if(log::getLogLevel('event') > 200){
		echo '<div class="alert alert-danger">{{Attention votre niveau de log (event) est inférieure à info, vous ne pouvez donc pas voir de temps réel}}</div>';
	}
}
?>

<textarea class="form-control marketAttr" id="requeteremote" placeholder='{"host":"alexa.amazon.fr","path":"/api/bootstrap?version=0","method":"GET","timeout":10000,"headers":{}}
' style="height: 40px;"></textarea>
<textarea class="form-control marketAttr" id="requetedata" placeholder='{"behaviorId":"PREVIEW","sequenceJson":"{\"@type\":\"com.amazon.alexa.behaviors.model.Sequence\",\"startNode\":{\"@type\":\"com.amazon.alexa.behaviors.model.OpaquePayloadOperationNode\",\"operationPayload\":{\"deviceType\":\"A3S5BH2HU6VAYF\",\"deviceSerialNumber\":\"GGGGGGGGGGGGGGGG\",\"locale\":\"fr-FR\",\"customerId\":\"AAAAAAAAAAAAAA\",\"value\":50},\"type\":\"Alexa.DeviceControls.Volume\"}}","status":"ENABLED"}' style="height: 90px;"></textarea>
<a class="btn btn-success pull-left" id="bt_Lancer"><i class="far jeedomapp-done"></i> {{Lancer}}</a> <input style="position:relative;top:+10px;left:+25px;" type="checkbox" id="requeteremotevidecache" /> <span style="position:relative;top:+7px;left:+20px;" >Vider le log au lancement</span><a class="btn btn-danger pull-right" id="bt_logdisplayremoveLog"><i class="far fa-trash-alt"></i>  {{Supprimer}}</a>
<a class="btn btn-warning pull-right" id="bt_logdisplayclearLog"><i class="fas fa-times"></i> {{Vider}}</a>
<a class="btn btn-success pull-right" id="bt_logdisplaydownloadLog"><i class="fas fa-cloud-download-alt"></i> {{Télécharger}}</a>
<a class="btn btn-warning pull-right" data-state="1" id="bt_eventLogStopStart"><i class="fas fa-pause"></i> {{Pause}}</a>
<input class="form-control pull-right" id="in_eventLogSearch" style="width : 200px;margin-left:5px;" placeholder="{{Rechercher}}" />
<br/><br>
<pre id='pre_eventlog' style='overflow: auto; height: calc(100% - 175px);width:100%;'></pre>

<script>
jeedom.log.autoupdate({
	log : log_display_name,
	default_search : log_default_search,
	display : $('#pre_eventlog'),
	search : $('#in_eventLogSearch'),
	control : $('#bt_eventLogStopStart'),
});

$("#bt_logdisplayclearLog").on('click', function(event) {
	jeedom.log.clear({
		log : log_display_name,
	});
});

$("#bt_logdisplayremoveLog").on('click', function(event) {
	jeedom.log.remove({
		log : log_display_name,
	});
});
$('#bt_logdisplaydownloadLog').click(function() {
	window.open('core/php/downloadFile.php?pathfile=log/' + log_display_name, "_blank", null);
});

</script>
<?php include_file('desktop', 'alexaapi', 'js', 'alexaapi');?>