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

$("#bt_addespeasyAction").on('click', function(event)
{
  var _cmd = {type: 'action'};
  addCmdToTable(_cmd);
});
 $('#bt_addEvent').on('click', function () {
	$('#bt_calendartab').trigger('click');
    $('#md_modal').dialog({title: "{{Ajouter évènement}}"});
    $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=alarm&eqLogic_id=' + $('.eqLogicAttr[data-l1key=id]').value()).dialog('open');
});

$('#bt_sante').on('click', function ()
{
  $('#md_modal').dialog({title: "{{Liste Amazon Echo}}"});
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=health').dialog('open');
});

$('#bt_reminders').on('click', function ()
{
  $('#md_modal').dialog({title: "{{Rappels/Alarmes}}"});
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=reminders').dialog('open');
});

 $('#bt_scan').on('click', function () {
    scanAmazonAlexa();
});

$('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').on('change', function ()
{
  $icon = $('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').value();
  if($icon != '' && $icon != null)
    $('#img_device').attr("src", 'plugins/alexaapi/core/config/devices/' + $icon + '.png');
});

function scanAmazonAlexa()
{
  $.ajax({
      type: "POST", 
      url: "plugins/alexaapi/core/ajax/alexaapi.ajax.php", 
      data:
      {
          action: "scanAmazonAlexa",
      },
      dataType: 'json',
      error: function (request, status, error)
      {
          handleAjaxError(request, status, error);
      },
      success: function (data)
      { 
          if (data.state != 'ok') {
              $('#div_alert').showAlert({message: data.result, level: 'danger'});
              return;
          }
          window.location.reload();
      }
  });
}


function addCmdToTable(_cmd)
{
  if (!isset(_cmd))
    var _cmd = {configuration: {}};

  if (init(_cmd.type) == 'info')
  {
    var tr =
       '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
     +   '<td>'
     +     '<span class="cmdAttr" data-l1key="id"></span>'
     +   '</td>'
     +   '<td>'
     +     '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom du capteur}}"></td>'
     +   '<td>'
//     +     '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>'
     +     '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />'
     +     '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
     +   '</td>'
     +   '<td>'
     +     '<small><span class="cmdAttr"  data-l1key="configuration" data-l2key="cmd"></span> Résultat de la commande <span class="cmdAttr"  data-l1key="configuration" data-l2key="taskname"></span> (<span class="cmdAttr"  data-l1key="configuration" data-l2key="taskid"></span>)</small>'
     +   '</td>'
     +   '<td>'
 //    +     '<span class="cmdAttr"  data-l1key="configuration" data-l2key="value"></span>'
     +   '</td>'
     +   '<td>'
  //   +     '<input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="{{Unite}}">'
     +   '</td>'
     +   '<td>'
     +     '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> '
     +     '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> '
     +   '</td>'
     + '<td>';

    if (is_numeric(_cmd.id))
    {
      tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> '
          + '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }

    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>'
     +   '</td>'
     + '</tr>';

    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
  }

  if (init(_cmd.type) == 'action')
  {
    var tr =
       '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
     +   '<td>'
     +     '<span class="cmdAttr" data-l1key="id"></span>'
     +   '</td>'
     +   '<td>'
     +     '<div class="row">'
     +       '<div class="col-lg-6">'
     +         '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> Icone</a>'
     +         '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>'
     +       '</div>'
     +       '<div class="col-lg-6">'
     +         '<input class="cmdAttr form-control input-sm" data-l1key="name">'
     +       '</div>'
     +     '</div>'
     +     '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="display : none;margin-top : 5px;" title="{{La valeur de la commande vaut par défaut la commande}}">'
     +       '<option value="">Aucune</option>'
     +     '</select>'
     +   '</td>'
     +   '<td>'
//     +     '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>'
     +     '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />'
     +     '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
	 +     '<input class="cmdAttr" data-l1key="configuration" data-l2key="virtualAction" value="1" style="display:none;" />'
     +   '</td>'
     +   '<td>'
     +     '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="request">'
     +   '</td>'
     +   '<td>'
     //+     '<small><small><span class="cmdAttr"  data-l1key="configuration" data-l2key="value"></span></small></small><br><br>';
 
  if (init(_cmd.subType) == 'other')
  {
    tr +=
	     '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="infoName" placeholder="{{Nom de la Commande Info}}" style="width : 250px;" />';
  }

    tr +=
        '</td>'
     +   '<td>'
     +     '<input class="cmdAttr form-control input-sm" data-l1key="unite"  style="width : 100px;" placeholder="{{Unité}}" title="{{Unité}}" >'
     +     '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}} style="margin-top : 3px;"> '
     +     '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}} style="margin-top : 3px;">'
     +   '</td>'
     +   '<td>'
     +     '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> '
     +   '</td>'
     + '<td>';

    if (is_numeric(_cmd.id))
    {
      tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
		   if (!((init(_cmd.name)=="Reminder")||(init(_cmd.name)=="Alarm"))) //Masquer le bouton Tester
			  tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
	}
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>'
     + '  </td>'
     + '</tr>';

    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr:last');
    jeedom.eqLogic.builSelectCmd(
    {
      id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
      filter: {type: 'i'},
      error: function (error)
      {
        $('#div_alert').showAlert({message: error.message, level: 'danger'});
      },
      success: function (result)
      {
        tr.find('.cmdAttr[data-l1key=value]').append(result);
        tr.setValues(_cmd, '.cmdAttr');
        jeedom.cmd.changeType(tr, init(_cmd.subType));
      }
    });
  }
}

jeedom.plugin.getDeamonCookieInfo = function(_params)
{
    var paramsRequired = ['id'];
    var paramsSpecifics = {
        global: false,
    };
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'plugins/alexaapi/core/ajax/alexaapi.ajax.php';
    paramsAJAX.data = {
        action: 'getDeamonCookieInfo',
        id: _params.id
    };
    $.ajax(paramsAJAX);
};

jeedom.plugin.deamonCookieStart = function(_params)
{
    var paramsRequired = ['id'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'plugins/alexaapi/core/ajax/alexaapi.ajax.php';
    paramsAJAX.data = {
        action: 'deamonCookieStart',
        id: _params.id,
        debug: _params.debug || 0,
        forceRestart: _params.forceRestart || 0
    };
    $.ajax(paramsAJAX);
};

jeedom.plugin.deamonCookieStop = function(_params)
{
    var paramsRequired = ['id'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'plugins/alexaapi/core/ajax/alexaapi.ajax.php';
    paramsAJAX.data = {
        action: 'deamonCookieStop',
        id: _params.id
    };
    $.ajax(paramsAJAX);
};



jeedom.plugin.VerifiePresenceCookie = function(_params)
{
    var paramsRequired = ['id'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'plugins/alexaapi/core/ajax/alexaapi.ajax.php';
    paramsAJAX.data = {
        action: 'VerifiePresenceCookie',
        id: _params.id
    };
    $.ajax(paramsAJAX);
};

 /*************************Node************************************************/

 jeedom.plugin.node = function() {
 };

 jeedom.plugin.node.action = function (_params) {
 	var paramsRequired = ['action','node_id'];
 	var paramsSpecifics = {};
 	try {
 		jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
 	} catch (e) {
 		(_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
 		return;
 	}
 	var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
 	var paramsAJAX = jeedom.private.getParamsAJAX(params);
 	paramsAJAX.url = 'plugins/alexaapi/desktop/php/alexaapiProxy.php';
 	paramsAJAX.data = {
 		request: 'deletereminder?id='+_params.node_id+'&type=action&action='+_params.action,
 	};
 	$.ajax(paramsAJAX);
 }

