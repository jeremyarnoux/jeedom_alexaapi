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

require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";
					
					

if (!jeedom::apiAccess(init('apikey'), 'alexaapi')) {
	echo __('Vous n\'êtes pas autorisé à effectuer cette action', __FILE__);
	log::add('alexaapi_mqtt', 'debug',  'Clé Plugin Invalide');
	die();
}

if (init('test') != '') {
	echo 'OK';
	die();
}

$chaineRecuperee=file_get_contents("php://input");



log::add('alexaapi_mqtt', 'debug',  "-----[".$_GET["nom"]."]-----" );
$debut=strpos($chaineRecuperee, "{");
$fin=strrpos($chaineRecuperee, "}");
$longeur=1+intval($fin)-intval($debut);
$chaineRecupereeCorrigee=substr($chaineRecuperee, $debut, $longeur);
log::add('alexaapi_mqtt', 'debug',  $chaineRecupereeCorrigee);

$result = json_decode($chaineRecupereeCorrigee, true);


if (!is_array($result)) {
	log::add('alexaapi_mqtt', 'debug', 'Format Invalide');
	die();
}
//log::add('alexaapi_mqtt', 'debug',  'deviceSerialNumber:'.$result['deviceSerialNumber']);
$logical_id = $result['deviceSerialNumber'];


$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');

			if (!is_object($alexaapi)) {
log::add('alexaapi_mqtt', 'debug',  'Device non trouvé: '.$logical_id);
	die();
			}
			else{
log::add('alexaapi_mqtt', 'debug',  'Device trouvé: '.$logical_id);
			}
			
			
// ----------------- VOLUME ------------------
			
if ($result['volume']!=null)
{
log::add('alexaapi_mqtt', 'debug',  'Volume trouvé: '.$result['volume']);
				$alexaapi->checkAndUpdateCmd('volumeinfo', $result['volume']);
				$alexaapi->setStatus('lastCommunication',date('Y-m-d H:i:s'));
				$alexaapi->save();
}

// ----------------- INTERACTION ------------------
			
if ($result['description']['summary']!=null)
{
log::add('alexaapi_mqtt', 'debug',  'Intéraction trouvée: '.$result['description']['summary']);
				$alexaapi->checkAndUpdateCmd('interactioninfo', $result['description']['summary']);
				$alexaapi->setStatus('lastCommunication',date('Y-m-d H:i:s'));
				$alexaapi->save();
}

/*
if (isset($result['devices'])) {
	foreach ($result['devices'] as $key => $datas) {
		$explode = explode('_',$key);
		$key = $explode[0];
		if ($key == 'aquara'){
			if (!($datas['cmd'] == 'heartbeat' || $datas['cmd'] == 'report' || $datas['cmd'] == 'read_ack')) {
				continue;
			}
			if (!isset($datas['sid'])) {
				continue;
			}
			$logical_id = $datas['sid'];
			if ($datas['model'] == 'gateway') {
				$logical_id = $datas['source'];
			}
			$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');
			if (!is_object($alexaapi)) {
				if ($datas['model'] == 'gateway') {
					//test si gateway qui a changé d'ip
					foreach (eqLogic::byType('alexaapi') as $gateway) {
						if ($gateway->getConfiguration('sid') == $datas['sid']) {
							$gateway->setConfiguration('gateway',$datas['source']);
							$gateway->setLogicalId($datas['source']);
							$gateway->save();
							return;
						}
					}
				}
				$alexaapi= alexaapi::createFromDef($datas,$key);
				if (!is_object($alexaapi)) {
					log::add('alexaapi', 'debug', __('Aucun équipement trouvé pour : ', __FILE__) . secureXSS($datas['sid']));
					continue;
				}
				sleep(2);
				event::add('jeedom::alert', array(
					'level' => 'warning',
					'page' => 'alexaapi',
					'message' => '',
				));
				event::add('alexaapi::includeDevice', $alexaapi->getId());
			}
			if (!$alexaapi->getIsEnable()) {
				continue;
			}
			if ($alexaapi->getConfiguration('gateway') != $datas['source'] && $datas['model'] != 'gateway') {
				$alexaapi->setConfiguration('gateway',$datas['source']);
				$alexaapi->save();
			}
			if ($datas['sid'] !== null && $datas['model'] !== null) {
				if (isset($datas['data'])) {
					$data = $datas['data'];
					foreach ($data as $key => $value) {
						if ($datas['cmd'] == 'heartbeat' && $key == 'status') {
							continue;
						}
						if ($datas['model'] == 'gateway'){
							alexaapi::receiveAquaraData($datas['source'], $datas['model'], $key, $value);
						} else {
							alexaapi::receiveAquaraData($datas['sid'], $datas['model'], $key, $value);
						}
					}
				}
				$alexaapi->setStatus('lastCommunication',date('Y-m-d H:i:s'));
				$alexaapi->save();
			}
		}
		elseif ($key == 'yeelight'){
			if (!isset($datas['capabilities']['id'])) {
				continue;
			}
			$logical_id = $datas['ip'];
			$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');
			if (!is_object($alexaapi)) {
				foreach (eqLogic::byType('alexaapi') as $yeelight) {
					if ($yeelight->getConfiguration('gateway') == $datas['ip']) {
						$yeelight->setLogicalId($datas['ip']);
						$yeelight->save();
						return;
					}
				}
				if (!isset($datas['capabilities']['model'])) {
					continue;
				}
				$alexaapi= alexaapi::createFromDef($datas,$key);
				if (!is_object($alexaapi)) {
					log::add('alexaapi', 'debug', __('Aucun équipement trouvé pour : ', __FILE__) . secureXSS($datas['capabilities']['id']));
					continue;
				}
				sleep(2);
				event::add('jeedom::alert', array(
					'level' => 'warning',
					'page' => 'alexaapi',
					'message' => '',
				));
				event::add('alexaapi::includeDevice', $alexaapi->getId());
			}
			if (!$alexaapi->getIsEnable()) {
				continue;
			}
			if (isset($datas['capabilities'])) {
				$data = $datas['capabilities'];
				$power = ($data['power'] == 'off')? 0:1;
				$alexaapi->checkAndUpdateCmd('status', $power);
				$alexaapi->checkAndUpdateCmd('brightness', $data['bright']);
				if ($alexaapi->getConfiguration('model') != 'mono' && $alexaapi->getConfiguration('model') != 'ceiling') {
					$alexaapi->checkAndUpdateCmd('color_mode', $data['color_mode']);
					$alexaapi->checkAndUpdateCmd('rgb', '#' . str_pad(dechex($data['rgb']), 6, "0", STR_PAD_LEFT));
					$alexaapi->checkAndUpdateCmd('hsv', $data['hue']);
					$alexaapi->checkAndUpdateCmd('saturation', $data['sat']);
				}
				if ($alexaapi->getConfiguration('model') != 'mono') {
					$alexaapi->checkAndUpdateCmd('temperature', $data['ct']);
				}
				if ($alexaapi->getConfiguration('model') == 'ceiling4' || $alexaapi->getConfiguration('model') == 'ceiling10') {
					$bgpower = ($data['bg_power'] == 'off')? 0:1;
					$alexaapi->checkAndUpdateCmd('bg_status', $bgpower);
					$alexaapi->checkAndUpdateCmd('bg_bright', $data['bg_bright']);
					$alexaapi->checkAndUpdateCmd('bg_rgb', $data['bg_rgb']);
				}
				$alexaapi->setConfiguration('ipwifi', $datas['ip']);
				$alexaapi->setConfiguration('gateway', $datas['ip']);
				$alexaapi->setStatus('lastCommunication',date('Y-m-d H:i:s'));
				$alexaapi->save();
			}
		}
		elseif ($key == 'wifi'){
			if (isset($datas['notfound'])){
				$logical_id = $datas['ip'];
				$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');
				event::add('alexaapi::notfound', $alexaapi->getId());
				continue;
			}
			if (isset($datas['found'])){
				$logical_id = $datas['ip'];
				$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');
				$alexaapi->setConfiguration('gateway',$datas['ip']);
				$alexaapi->setConfiguration('sid',$datas['serial']);
				$alexaapi->setConfiguration('short_id',$datas['devtype']);
				$alexaapi->setStatus('lastCommunication',date('Y-m-d H:i:s'));
				$alexaapi->setIsEnable(1);
				$alexaapi->setIsVisible(1);
				if (!in_array($datas['model'], array('vacuum','philipsceiling'))){
					$alexaapi->setConfiguration('password',$datas['token']);
				}
				$alexaapi->save();
				event::add('alexaapi::found', $alexaapi->getId());
				$refreshcmd = alexaapiCmd::byEqLogicIdAndLogicalId($alexaapi->getId(),'refresh');
				$refreshcmd->execCmd();
				continue;
			}
			if (!isset($datas['model']) || !isset($datas['ip'])) {
				continue;
			}
			$logical_id = $datas['ip'];
			$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');
			if (!is_object($alexaapi)) {
				continue;
			}
			if (!$alexaapi->getIsEnable()) {
				continue;
			}
			log::add('alexaapi', 'debug', 'Status ' . print_r($datas, true));
			foreach ($alexaapi->getCmd('info') as $cmd) {
				$logicalId = $cmd->getLogicalId();
				if ($logicalId == '') {
					continue;
				}
				$path = explode('::', $logicalId);
				$value = $datas;
				foreach ($path as $key) {
					if (!isset($value[$key])) {
						continue (2);
					}
					$value = $value[$key];
					if (!is_array($value) && strpos($value, 'toggle') !== false && $cmd->getSubType() == 'binary') {
						$value = $cmd->execCmd();
						$value = ($value != 0) ? 0 : 1;
					}
				}
				if (!is_array($value)) {
					if ($cmd->getSubType() == 'numeric') {
						$value = round($value, 2);
					}
					$cmd->event($value);
				}
				if (strpos($logicalId,'battery') !== false) {
					$alexaapi->batteryStatus($value);
				}
				$alexaapi->setStatus('lastCommunication',date('Y-m-d H:i:s'));
				$alexaapi->save();
			}
		}
	}
}
*/
?>
