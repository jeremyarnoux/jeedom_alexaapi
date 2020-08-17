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


$('#bt_forcerDefaultCmd').off('click').on('click', function () {
  var dialog_title = '{{Recharge configuration par défaut}}';
  var dialog_message = '<form class="form-horizontal onsubmit="return false;"> ';
  dialog_title = '{{Recharger la configuration par défaut ?}}';
  dialog_message += '<label class="lbl lbl-warning" for="name">{{Notez que cette fonction ne supprime pas votre équipement ni ses commandes, cela évite d\'avoir à refaire les scénarios. Elle va supprimer toute personnalisation et toutes les commandes vont revenir à leur état initial.}}</label> ';
  dialog_message += '</form>';
  bootbox.dialog({
    title: dialog_title,
    message: dialog_message,
    buttons: {
      "{{Annuler}}": {
        className: "btn-danger",
        callback: function () {}
      },
      success: {
        label: "{{Démarrer}}",
        className: "btn-success",
        callback: function () {
          $.ajax({
            type: "POST",
            url: "plugins/alexaapi/core/ajax/alexaapi.ajax.php",
            data: {
              action: "forcerDefaultCmd",
              id: $('.eqLogicAttr[data-l1key=id]').value(),
              createcommand: 0,
            },
            dataType: 'json',
            global: false,
            error: function (request, status, error) {
              handleAjaxError(request, status, error);
            },
            success: function (data) {
              if (data.state != 'ok') {
                $('#div_alert').showAlert({
                  message: data.result,
                  level: 'danger'
                });
                return;
              }
              $('#div_alert').showAlert({
                message: '{{Opération réalisée avec succès}}',
                level: 'success'
              });
              $('.eqLogicDisplayCard[data-eqLogic_id=' + $('.eqLogicAttr[data-l1key=id]').value() + ']').click();
            }
          });

        }
      },
    }
  });

});


$('#bt_Lancer').off('click').on('click', function () {
  var tempo = 0;
  if (requeteremotevidecache.checked) {
    tempo = 2000;
    jeedom.log.clear({
      log: log_display_name,
    });
  }

  setTimeout("envoiQuery(requeteremote.value, requetedata.value)", tempo);


  //console.log ("fini");
});

$('#bt_resetEqlogicSearch').on('click', function () {
  $('#in_searchEqlogic').val('')
  $('#in_searchEqlogic').keyup()
})

$('#in_searchEqlogic2').off('keyup').keyup(function () {
  var search = $(this).value().toLowerCase();
  search = search.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
  if (search == '') {
    $('.eqLogicDisplayCard.second').show();
    $('.eqLogicThumbnailContainer.second').packery();
    return;
  }
  $('.eqLogicDisplayCard.second').hide();
  $('.eqLogicDisplayCard.second .name').each(function () {
    var text = $(this).text().toLowerCase();
    text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
    if (text.indexOf(search) >= 0) {
      $(this).closest('.eqLogicDisplayCard.second').show();
    }
  });
  $('.eqLogicThumbnailContainer.second').packery();
});
$('#bt_resetEqlogicSearch2').on('click', function () {
  $('#in_searchEqlogic2').val('')
  $('#in_searchEqlogic2').keyup()
})

$('#in_searchEqlogic3').off('keyup').keyup(function () {
  var search = $(this).value().toLowerCase();
  search = search.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
  if (search == '') {
    $('.eqLogicDisplayCard.third').show();
    $('.eqLogicThumbnailContainer.third').packery();
    return;
  }
  $('.eqLogicDisplayCard.third').hide();
  $('.eqLogicDisplayCard.third .name').each(function () {
    var text = $(this).text().toLowerCase();
    text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
    if (text.indexOf(search) >= 0) {
      $(this).closest('.eqLogicDisplayCard.third').show();
    }
  });
  $('.eqLogicThumbnailContainer.third').packery();
});
$('#bt_resetEqlogicSearch3').on('click', function () {
  $('#in_searchEqlogic3').val('')
  $('#in_searchEqlogic3').keyup()
})


function envoiQuery(query, data) {
  //if (query=='') query='{"host":"alexa.amazon.fr","path":"/api/bootstrap?version=0","method":"GET","timeout":10000,"headers":{}}';
  if (query == '') query = '{"host":"alexa.amazon.fr","path":"/api/behaviors/preview","method":"POST","timeout":12345,"headers":{}}';
  if (data == '') data = '{"behaviorId":"PREVIEW","sequenceJson":"{\"@type\":\"com.amazon.alexa.behaviors.model.Sequence\",\"startNode\":{\"@type\":\"com.amazon.alexa.behaviors.model.OpaquePayloadOperationNode\",\"operationPayload\":{\"deviceType\":\"A3S5BH2HU6VAYF\",\"deviceSerialNumber\":\"G090LF118173117U\",\"locale\":\"fr-FR\",\"customerId\":\"A1P3694S7PYD78\",\"value\":50},\"type\":\"Alexa.DeviceControls.Volume\"}}","status":"ENABLED"}';
  var yourUrl = "http://192.168.0.21:3456/query?query=" + encodeURIComponent(query) + "&data=" + encodeURIComponent(data);
  var http = new XMLHttpRequest();
  console.log("adresse:" + yourUrl);
  http.open('GET', yourUrl, true);
  http.send();
}



$('#bt_cronGenerator').off('click').on('click', function () {
  jeedom.getCronSelectModal({}, function (result) {
    $('.configKey[data-l1key=autorefresh]').value(result.value);
  });
});

$("#bt_addespeasyAction").off('click').on('click', function (event) {
  var _cmd = {
    type: 'action'
  };
  addCmdToTable(_cmd);
});
$('#bt_addEvent').off('click').on('click', function () {
  $('#bt_calendartab').trigger('click');
  $('#md_modal').dialog({
    title: "{{Ajouter évènement}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=alarm&eqLogic_id=' + $('.eqLogicAttr[data-l1key=id]').value()).dialog('open');
});

$('#bt_media').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Info Media}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=media&iddevice=' + $('.eqLogicAttr[data-l1key=logicalId]').value()).dialog('open');
});
$('#bt_req').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Requêteur JSON}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=req&iddevice=' + $('.eqLogicAttr[data-l1key=logicalId]').value()).dialog('open');
});
$('#bt_req2').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Requêteur}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=req2&iddevice=' + $('.eqLogicAttr[data-l1key=logicalId]').value()).dialog('open');
});
$('#bt_sante').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Liste Amazon Echo}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=health').dialog('open');
});

$('#bt_reminders').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Rappels/Alarmes}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=reminders').dialog('open');
});

$('#bt_history').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Historique}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=history').dialog('open');
});

$('#bt_routines').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Routines}}"
  });
  $('#md_modal').load('index.php?v=d&plugin=alexaapi&modal=routines').dialog('open');
});


$('#bt_scan').off('click').on('click', function () {
  scanAmazonAlexa();
});

$('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').on('change', function () {
  // Ici il faudra faire comme dans desktop/php/alexaapi.php ligne 268 chercher type et à défaut family
  $icon = $('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').value();
  if ($icon != '' && $icon != null)
    $('#img_device').attr("src", 'plugins/alexaapi/core/config/devices/' + $icon + '.png');

  var id = $('.eqLogicAttr[data-l1key=id]')[0].value;
  if (id) {
    jeedom.eqLogic.byId({
      "id": $('.eqLogicAttr[data-l1key=id]')[0].value,
      noCache: true,
      success: function (obj) {
        if (obj && obj.configuration && obj.configuration.capabilities && obj.configuration.capabilities.length && obj.configuration.capabilities instanceof Array) {
          $('.eqLogicAttr[data-l1key=configuration][data-l2key=capabilities]')[0].innerHTML = obj.configuration.capabilities.join(', ');
        }
      }
    });
  }
});

function scanAmazonAlexa() {
  $.ajax({
    type: "POST",
    url: "plugins/alexaapi/core/ajax/alexaapi.ajax.php",
    data: {
      action: "scanAmazonAlexa",
    },
    dataType: 'json',
    error: function (request, status, error) {
      handleAjaxError(request, status, error);
    },
    success: function (data) {
      if (data.state != 'ok') {
        $('#div_alert').showAlert({
          message: data.result,
          level: 'danger'
        });
        return;
      }
      window.location.reload();
    }
  });
}

$("#table_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});


function addCmdToTable(_cmd) {
  if (!isset(_cmd))
    var _cmd = {
      configuration: {}
    };

  var DefinitionDivPourCommandesPredefinies = 'style="display: none;"';
  if (init(_cmd.logicalId) == "")
    DefinitionDivPourCommandesPredefinies = "";
  //  if ((init(_cmd.logicalId) == 'whennextreminder') || (init(_cmd.logicalId) == '00whennextalarm') || (init(_cmd.logicalId) == 'whennextreminderlabel') || (init(_cmd.logicalId) == 'musicalalarmmusicentity') || (init(_cmd.logicalId) == 'whennextmusicalalarm')) {

  if ((init(_cmd.logicalId) == 'updateallalarms')) {
    return;
  }

  console.log("addCmdToTable : " + init(_cmd.logicalId));

  if (init(_cmd.type) == 'info') {
    var tr =
      '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">' +
      '<td>' +
      '<span class="cmdAttr" data-l1key="id"></span>' +
      '</td>' +
      '<td>' +
      '<div class="row">' +
      '<div class="col-lg-1">'
      //    +         '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> Icone</a>'
      +
      '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>' +
      '</div>' +
      '<div class="col-lg-8">' +
      '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom du capteur}}"></td>' +
      '<td>'
      //     +     '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>'
      +
      '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />'
      //     +     '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
      +
      '</td>' +
      '<td>'
      //     +     '<small><span class="cmdAttr"  data-l1key="configuration" data-l2key="cmd"></span> Résultat de la commande <span class="cmdAttr"  data-l1key="configuration" data-l2key="taskname"></span> (<span class="cmdAttr"  data-l1key="configuration" data-l2key="taskid"></span>)</small>'

      //    +     '<span class="cmdAttr"  data-l1key="configuration" data-l2key="value"></span>'
      +
      '</td>' +
      '<td>'
      //   +     '<input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="{{Unite}}">'
      +
      '</td>' +
      '<td>' +
      '</td>' +
      '<td>' +
      '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ' +
      '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ' +
      '</td>' +
      '<td>';

    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fas fa-cogs"></i></a> ' +
        '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }

    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>' +
      '</td>' +
      '</tr>';

    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
  }

  if (init(_cmd.type) == 'action') {
    var tr =
      '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">' +
      '<td>' +
      '<span class="cmdAttr" data-l1key="id"></span>' +
      '</td>' +
      '<td>' +
      '<div class="row">' +
      '<div class="col-lg-1">' +
      '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>' +
      '</div>' +
      '<div class="col-lg-8">' +
      '<input class="cmdAttr form-control input-sm" data-l1key="name">' +
      '</div>' +
      '</div>';

    tr += '</td>';


    tr += '<td>';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled />';
    tr += '<div ' + DefinitionDivPourCommandesPredefinies + '>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</div></td>';
    tr += '<td>' +
      '<input class="cmdAttr form-control input-sm"';
    if (init(_cmd.logicalId) != "")
      tr += 'readonly';

    if (init(_cmd.logicalId) == "refresh")
      tr += ' style="display:none;" ';

    tr += ' data-l1key="configuration" data-l2key="request">';

    if (init(_cmd.subType) == 'select') {
      tr += '<input class="tooltips cmdAttr form-control input-sm expertModeVisible" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}">';
    }

    tr += '</td>';
    tr += '<td>';

    if ((init(_cmd.logicalId) == "") || (init(_cmd.logicalId) == "volume")) {
      tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite"  style="width : 100px;" placeholder="{{Unité}}" title="{{Unité}}" >';
      tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}"  title="{{Min}} style="margin-top : 3px;"> ';
      tr += '</td>';
      tr += '<td>';
      tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}} style="margin-top : 3px;">';
    } else {
      tr += '</td>';
      tr += '<td>';
    }




    tr += '</td>' +
      '<td>' +
      '<input class="tooltips cmdAttr form-control input-sm expertModeVisible" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}" style="margin-top : 5px;">' +
      '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ' +
      '</td>' +
      '<td>';

    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      if (!((init(_cmd.name) == "Routine") || (init(_cmd.name) == "xxxxxxxx"))) //Masquer le bouton Tester
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>' +
      '  </td>' +
      '</tr>';

    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr:last');
    jeedom.eqLogic.builSelectCmd({
      id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
      filter: {
        type: 'i'
      },
      error: function (error) {
        $('#div_alert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function (result) {
        tr.find('.cmdAttr[data-l1key=value]').append(result);
        tr.setValues(_cmd, '.cmdAttr');
        jeedom.cmd.changeType(tr, init(_cmd.subType));
      }
    });
  }
}

jeedom.plugin.getDeamonCookieInfo = function (_params) {
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

jeedom.plugin.deamonCookieStart = function (_params) {
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

jeedom.plugin.deamonCookieStop = function (_params) {
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



jeedom.plugin.VerifiePresenceCookie = function (_params) {
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

jeedom.plugin.node = function () {};

jeedom.plugin.node.action = function (_params) { //Delete reminder
  var paramsRequired = ['action', 'node_id'];
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
    request: _params.action + 'reminder?id=' + _params.node_id + '&type=action&action=' + _params.action,
  };
  $.ajax(paramsAJAX);
}

jeedom.plugin.node.action2 = function (_params) { //Lancement d'une routine
  var paramsRequired = ['action', 'node_id', 'node_id2'];
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
    request: 'routine?device=' + _params.node_id2 + '&routine=' + _params.node_id,
  };
  $.ajax(paramsAJAX);
}