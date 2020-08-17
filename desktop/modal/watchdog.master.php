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
	throw new Exception('{{401 - Accès non autorisé}}');
}

$masters = watchdog_master::all();
?>
<div id='div_watchdogMasterAlert' style="display: none;"></div>
<div class="row row-overflow">
	<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-default watchdogMasterAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fas fa-plus-circle"></i> {{Ajouter un Jeedom cible}}</a>
				<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%" /></li>
				<?php
				foreach ($masters as $master) {
					echo '<li class="cursor li_watchdogMaster" data-watchdogMaster_id="' . $master->getId() . '"><a>' . $master->getName() . '</a></li>';
				}
				?>
			</ul>
		</div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8 col-xs-8 watchdogMaster" style="border-left: solid 1px #EEE; padding-left: 25px;display:none;">
		<a class="btn btn-success watchdogMasterAction pull-right" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
		<a class="btn btn-danger watchdogMasterAction pull-right" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>

		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#watchdogMasterConfigtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Configuration}}</a></li>
			<li role="presentation"><a href="#watchdogMasterAffecttab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Affectation}}</a></li>
		</ul>

		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="watchdogMasterConfigtab">
				<form class="form-horizontal">
					<fieldset>
						<legend>{{Général}}</legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom du jeedom cible}}</label>
							<div class="col-sm-3">
								<input type="text" class="watchdogMasterAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="watchdogMasterAttr form-control" data-l1key="name" placeholder="{{Nom du jeedom cible}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Adresse}}</label>
							<div class="col-sm-3">
								<input type="text" class="watchdogMasterAttr form-control" data-l1key="address" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Clef API}}</label>
							<div class="col-sm-3">
								<input type="text" class="watchdogMasterAttr form-control" data-l1key="apikey" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Mode d'accès}}</label>
							<div class="col-sm-3">
								<select class="watchdogMasterAttr form-control" data-l1key="configuration" data-l2key="network::access">
									<option value="internal">{{Interne}}</option>
									<option value="external">{{Externe}}</option>
								</select>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="watchdogMasterAffecttab">
				<a class="btn btn-success btn-sm pull-right" id="bt_watchdogMasterAddEqLogic"><i class="fas fa-plus-circle"></i> {{Ajouter un équipement}}</a><br>
				<form class="form-horizontal">
					<fieldset>
						<div id="div_watchdogMasterEqLogicList"></div>
					</fieldset>
				</form>
			</div>



		</div>
	</div>
</div>


<script>
	$('.watchdogMasterAction[data-action=add]').on('click', function() {
		$('.watchdogMaster').show();
		$('.watchdogMasterAttr').value('');
	});

	$('#bt_watchdogMasterAddEqLogic').on('click', function() {
		addwatchdogMasterEqLogic();
	});

	function addwatchdogMasterEqLogic(_eqLogic) {
		if (!isset(_eqLogic)) {
			_eqLogic = {};
		}
		var div = '<div class="watchdogMasterEqLogic">';
		div += '<div class="form-group">';
		div += '<label class="col-sm-1 control-label">{{Equipement}}</label>';
		div += '<div class="col-sm-5 has-success">';
		div += '<div class="input-group">';
		div += '<span class="input-group-btn">';
		div += '<a class="btn btn-default bt_removewatchdogMasterEqLogic btn-sm"><i class="fas fa-minus-circle"></i></a>';
		div += '</span>';
		div += '<input class="watchdogMasterEqLogicAttr form-control input-sm" data-l1key="eqLogic" />';
		div += '<span class="input-group-btn">';
		div += '<a class="btn btn-sm listEqLogic btn-success"><i class="fas fa-list-alt"></i></a>';
		div += '</span>';
		div += '</div>';
		div += '</div>';
		div += '</div>';
		$('#div_watchdogMasterEqLogicList').append(div);
		$('#div_watchdogMasterEqLogicList .watchdogMasterEqLogic:last').setValues(_eqLogic, '.watchdogMasterEqLogicAttr');
	}

	$('#div_watchdogMasterEqLogicList').on('click', '.listEqLogic', function() {
		var el = $(this);
		jeedom.eqLogic.getSelectModal({
			cmd: {
				type: 'info'
			}
		}, function(result) {
			el.closest('.watchdogMasterEqLogic').find('.watchdogMasterEqLogicAttr[data-l1key=eqLogic]').value(result.human);
		});
	});

	$('#div_watchdogMasterEqLogicList').on('click', '.bt_removewatchdogMasterEqLogic', function() {
		$(this).closest('.watchdogMasterEqLogic').remove();
	});

	function displaywatchdogMaster(_id) {
		$('.li_watchdogMaster').removeClass('active');
		$('.li_watchdogMaster[data-watchdogMaster_id=' + _id + ']').addClass('active');
		$.ajax({
			type: "POST",
			url: "plugins/watchdog/core/ajax/watchdog.ajax.php",
			data: {
				action: "get_watchdogMaster",
				id: _id,
			},
			dataType: 'json',
			error: function(request, status, error) {
				handleAjaxError(request, status, error, $('#div_watchdogMasterAlert'));
			},
			success: function(data) {
				if (data.state != 'ok') {
					$('#div_watchdogMasterAlert').showAlert({
						message: data.result,
						level: 'danger'
					});
					return;
				}
				$('.watchdogMaster').show();
				$('#div_watchdogMasterEqLogicList').empty();
				$('.watchdogMasterAttr').value('');
				$('.watchdogMaster').setValues(data.result, '.watchdogMasterAttr');
				if (!isset(data.result.configuration)) {
					data.result.configuration = {};
				}
				if (isset(data.result.configuration.eqLogics)) {
					for (var i in data.result.configuration.eqLogics) {
						addwatchdogMasterEqLogic(data.result.configuration.eqLogics[i]);
					}
				}
			}
		});
	}

	$('.li_watchdogMaster').on('click', function() {
		displaywatchdogMaster($(this).attr('data-watchdogMaster_id'));
	});

	$('.watchdogMasterAction[data-action=save]').on('click', function() {
		var watchdog_master = $('.watchdogMaster').getValues('.watchdogMasterAttr')[0];
		if (!isset(watchdog_master.configuration)) {
			watchdog_master.configuration = {};
		}
		watchdog_master.configuration.eqLogics = $('#div_watchdogMasterEqLogicList .watchdogMasterEqLogic').getValues('.watchdogMasterEqLogicAttr');
		$.ajax({
			type: "POST",
			url: "plugins/watchdog/core/ajax/watchdog.ajax.php",
			data: {
				action: "save_watchdogMaster",
				watchdog_master: json_encode(watchdog_master),
			},
			dataType: 'json',
			error: function(request, status, error) {
				handleAjaxError(request, status, error, $('#div_watchdogMasterAlert'));
			},
			success: function(data) {
				if (data.state != 'ok') {
					$('#div_watchdogMasterAlert').showAlert({
						message: data.result,
						level: 'danger'
					});
					return;
				}
				$('#div_watchdogMasterAlert').showAlert({
					message: '{{Sauvegarde réussie}}',
					level: 'success'
				});
				displaywatchdogMaster(data.result.id);
			}
		});
	});

	$('.watchdogMasterAction[data-action=remove]').on('click', function() {
		bootbox.confirm('{{Etês-vous sûr de vouloir supprimer ce jeedom distant ?}}', function(result) {
			if (result) {
				$.ajax({
					type: "POST",
					url: "plugins/watchdog/core/ajax/watchdog.ajax.php",
					data: {
						action: "remove_watchdogMaster",
						id: $('.li_watchdogMaster.active').attr('data-watchdogMaster_id'),
					},
					dataType: 'json',
					error: function(request, status, error) {
						handleAjaxError(request, status, error, $('#div_watchdogMasterAlert'));
					},
					success: function(data) {
						if (data.state != 'ok') {
							$('#div_watchdogMasterAlert').showAlert({
								message: data.result,
								level: 'danger'
							});
							return;
						}
						$('.li_watchdogMaster.active').remove();
						$('.watchdogMaster').hide();
					}
				});
			}
		});
	});
</script>