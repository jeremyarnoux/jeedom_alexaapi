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


$('#bt_sante').on('click', function () {
$('#md_modal').dialog({title: "{{Liste Amazon Echo}}"});
$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=health').dialog('open');
});

 $('#bt_scan').on('click', function () {
    ScanAmazonAlexa();
});


 $('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').on('change', function () {

 //$('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=health').dialog('open');
   //$('#img_device').attr("src",'plugins/alexaapi/core/config/devices/ECHO.jpg');
$icon = $('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').value();
		if($icon != '' && $icon != null)
		{
		$('#img_device').attr("src", 'plugins/alexaapi/core/config/devices/'+$icon+'.png');
		}


 //$('#div_alert').showAlert({message: 'Message :'+$icon, level: 'success'});
   /* if (!confirm('{{Génération des équipements Amazon Echo. Voulez-vous continuer ?}}')) {
        return;
    }

    if (!confirm('{{Dommage !!! Pas encore fait !}}')) {
        return;
    }
*/

});

function ScanAmazonAlexa() {
    $.ajax({
        type: "POST", 
        url: "plugins/alexaapi/core/ajax/alexaapi.ajax.php", 
        data: {
            action: "ScanAmazonAlexa",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { 
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            window.location.reload();
        }
    });
}