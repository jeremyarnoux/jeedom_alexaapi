/* This file is part of Plugin openzwave for jeedom.
 *
 * Plugin openzwave for jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Plugin openzwave for jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Plugin openzwave for jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

//let Alexa = include('../../resources/alexa-remote-http/alexa-remote');
//let alexa = new Alexa();
//const Alexa = 
/*
try {
  
const express = require('express');
const fs = require('fs');

//const a=require('cookie.js');
}

catch(error) 
{
  
//console.error(error);
  
	//bootbox.alert('error');
	alert(error);
}
*/

 $('#bt_closeCookie').off().on('click', function (event) {
	
	//bootbox.alert("hello world!1");


                //$('#div_alert').showAlert({message: 'coucou', level: 'danger'});
 
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/alexaapi/core/ajax/alexaapi.ajax.php", // url du fichier php
            data: {
                action: "closeCookie",
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Le démon a été correctement (re)démaré}}', level: 'success'});
            $('#ul_plugin .li_plugin[data-plugin_id=alexaapi]').click();
            }
        });

});


 $('#bt_createCookie').off().on('click', function (event) {
	
	//bootbox.alert("hello world!1");


                //$('#div_alert').showAlert({message: 'coucou', level: 'danger'});
 
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/alexaapi/core/ajax/alexaapi.ajax.php", // url du fichier php
            data: {
                action: "createCookie",
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Le démon a été correctement (re)démaré}}', level: 'success'});
            $('#ul_plugin .li_plugin[data-plugin_id=alexaapi]').click();
            }
        });

});

$('#bt_webespeasy').on('click', function () {
  var nodeId = $('#idespeasy').value();
  $('#md_modal').dialog({title: "{{Identification Amazon}}"});
//  $('#md_modal').load('index.php?v=d&plugin=espeasy&modal=web&ip=' + nodeId).dialog('open');
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=web&ip=192.168.0.21').dialog('open');
});

