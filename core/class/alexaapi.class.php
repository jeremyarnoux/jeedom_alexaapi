<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class alexaapi extends eqLogic {
	
    public static function templateWidget(){
		$return = array('info' => array('string' => array()));
		$return = array('action' => array('select' => array(), 'slider' => array()));
		$return['info']['string']['subText2'] = array('template' => 'album' );
		$return['info']['string']['alarmmusicalmusic'] = array('template' => 'alarmmusicalmusic', 'replace' => array("#hide_name#" => "hidden"));
		$return['info']['string']['title'] =    array('template' => 'title');
		$return['info']['string']['url'] =    array('template' => 'image');
		$return['info']['string']['interaction'] =    array('template' => 'cadre');
		$return['action']['message']['message'] =    array(
				'template' => 'message',
				'replace' => array("#_desktop_width_#" => "100","#_mobile_width_#" => "50", "#title_disable#" => "1", "#message_disable#" => "0")
		);
		$return['action']['select']['list'] =    array(
				'template' => 'table',
				'replace' => array("#_desktop_width_#" => "100","#_mobile_width_#" => "50", "#hide_name#" => "whidden")
		);		
		$return['action']['slider']['volume'] =    array(
				'template' => 'bouton',
				'replace' => array("#hide_name#" => "hidden", "#step#" => "10")
		);
		$return['info']['string']['state'] = array(
				'template' => 'tmplmultistate_alexaapi',
				'replace' => array("#hide_name#" => "hidden", "#hide_state#" => "hidden", "#marge_gauche#" => "5px", "#marge_haut#" => "-15px"),
				'test' => array(
					array('operation' => "#value# == 'PLAYING'", 'state_light' => "<img src='plugins/alexaapi/core/img/playing.png'  title ='" . __('Playing', __FILE__) . "'>",
							'state_dark' => "<img src='plugins/alexaapi/core/img/playing.png' title ='" . __('En charge', __FILE__) . "'>"),
					array('operation' => "#value# != 'PLAYING'",'state_light' => "<img src='plugins/alexaapi/core/img/paused.png' title ='" . __('En Pause', __FILE__) . "'>")
				)
			);
		$return['info']['string']['alarm'] = array(
				'template' => 'alarm',
				'replace' => array("#hide_name#" => "hidden", "#marge_gauche#" => "55px", "#marge_haut#" => "15px"),
				'test' => array(
					array('operation' => "#value# == ''", 
					'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-Off.png' title ='" . __('Playing', __FILE__) . "'>",
					'state_dark'  => "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-Off_dark.png' title ='" . __('En charge', __FILE__) . "'>"),
					array('operation' => "#value# != ''",
					'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-On.png' title ='" . __('En Pause', __FILE__) . "'>",
					'state_dark' =>  "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-On_dark.png' title ='" . __('En Pause', __FILE__) . "'>")
				)
			);
		$return['info']['string']['alarmmusical'] = array(
				'template' => 'alarm',
				'replace' => array("#hide_name#" => "hidden", "#marge_gauche#" => "55px", "#marge_haut#" => "15px"),
				'test' => array(
					array('operation' => "#value# == ''", 
					'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-Off.png' title ='" . __('Playing', __FILE__) . "'>",
					'state_dark'  => "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-Off_dark.png' title ='" . __('En charge', __FILE__) . "'>"),
					array('operation' => "#value# != ''",
					'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-On.png' title ='" . __('En Pause', __FILE__) . "'>",
					'state_dark' =>  "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-On_dark.png' title ='" . __('En Pause', __FILE__) . "'>")
				)
			);				
		$return['info']['string']['reminder'] = array(
				'template' => 'alarm',
				'replace' => array("#hide_name#" => "hidden", "#marge_gauche#" => "55px", "#marge_haut#" => "4px"),
				'test' => array(
					array('operation' => "#value# == ''", 
					'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-Off.png' title ='" . __('Playing', __FILE__) . "'>",
					'state_dark'  => "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-Off_dark.png' title ='" . __('En charge', __FILE__) . "'>"),
					array('operation' => "#value# != ''",
					'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-On.png' title ='" . __('En Pause', __FILE__) . "'>",
					'state_dark' =>  "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-On_dark.png' title ='" . __('En Pause', __FILE__) . "'>")
				)
			);	
	return $return;
	}	


	public static function callProxyAlexaapi($_url) {
		$url = 'http://' . config::byKey('internalAddr') . ':3456/' . trim($_url, '/') . '&apikey=' . jeedom::getApiKey('openzwave');
		$ch = curl_init();
		curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true,));
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			$curl_error = curl_error($ch);
			curl_close($ch);
			throw new Exception(__('Echec de la requête http : ', __FILE__) . $url . ' Curl error : ' . $curl_error, 404);
		}
		curl_close($ch);
		return (is_json($result)) ? json_decode($result, true) : $result;
	}

	public static function deamon_info() {
		$return = array();
		$return['log'] = 'alexaapi_node';
		$return['state'] = 'nok'; 
		// Regarder si alexaapi.js est lancé
		$pid = trim(shell_exec('ps ax | grep "alexaapi/resources/alexaapi.js" | grep -v "grep" | wc -l'));
		if ($pid != '' && $pid != '0') $return['state'] = 'ok';
		// Regarder si le cookie existe :alexa-cookie.json
		$request = realpath(dirname(__FILE__) . '/../../resources/data/alexa-cookie.json');
		if (file_exists($request)) $return['launchable'] = 'ok';
		else {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = "Cookie Amazon ABSENT ";
		}
		return $return;
	}

	public static function deamon_start($_debug = false) {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		log::add('alexaapi', 'info', 'Lancement du démon alexaapi');
		$url = network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/alexaapi/core/api/jeealexaapi.php?apikey=' . jeedom::getApiKey('alexaapi');
		$log = $_debug ? '1' : '0';
		$sensor_path = realpath(dirname(__FILE__) . '/../../resources');
		$cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexaapi.js ' . network::getNetworkAccess('internal') . ' ' . config::byKey('amazonserver', 'alexaapi', 'amazon.fr') . ' ' . config::byKey('alexaserver', 'alexaapi', 'alexa.amazon.fr').' '.jeedom::getApiKey('alexaapi');
		log::add('alexaapi', 'debug', 'Lancement démon alexaapi : ' . $cmd);
		$result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_node') . ' 2>&1 &');
		//$cmdStart='nohup ' . $cmd . ' | tee >(grep "WS-MQTT">>'.log::getPathToLog('alexaapi_mqtt').') >(grep -v "WS-MQTT">>'. log::getPathToLog('alexaapi_node') . ')';
		if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
			log::add('alexaapi', 'error', $result);
			return false;
		}
		$i = 0;
		while ($i < 30) {
			$deamon_info = self::deamon_info();
			if ($deamon_info['state'] == 'ok') break;
			sleep(1);
			$i++;
		}
		if ($i >= 30) {
			log::add('alexaapi', 'error', 'Impossible de lancer le démon alexaapi, vérifiez le port', 'unableStartDeamon');
			return false;
		}
		message::removeAll('alexaapi', 'unableStartDeamon');
		log::add('alexaapi', 'info', 'Démon alexaapi lancé');
		return true;
	}

	public static function deamon_stop() {
		exec('kill $(ps aux | grep "/alexaapi.js" | awk \'{print $2}\')');
		log::add('alexaapi', 'info', 'Arrêt du service alexaapi');
		$deamon_info = self::deamon_info();
		if ($deamon_info['state'] == 'ok') {
			sleep(1);
			exec('kill -9 $(ps aux | grep "/alexaapi.js" | awk \'{print $2}\')');
		}
		$deamon_info = self::deamon_info();
		if ($deamon_info['state'] == 'ok') {
			sleep(1);
			exec('sudo kill -9 $(ps aux | grep "/alexaapi.js" | awk \'{print $2}\')');
		}
	}
	
	public static function reinstallNodeJS() { // Reinstall NODEJS from scratch (to use if there is errors in dependancy install)
		$pluginalexaapi = plugin::byId('alexaapi');
		log::add('alexaapi', 'info', 'Suppression du Code NodeJS');
		$cmd = system::getCmdSudo() . 'rm -rf ' . dirname(__FILE__) . '/../../resources/node_modules &>/dev/null';
		log::add('alexaapi', 'info', 'Suppression de NodeJS');
		$cmd = system::getCmdSudo() . 'apt-get -y --purge autoremove npm';
		exec($cmd);
		$cmd = system::getCmdSudo() . 'apt-get -y --purge autoremove nodejs';
		exec($cmd);
		log::add('alexaapi', 'info', 'Réinstallation des dependances');
		$pluginalexaapi->dependancy_install(true);
		return true;
	}

	
	public static function deamonCookie_start($_debug = false) { //*********** Demon Cookie***************
		self::deamonCookie_stop();
		$deamon_info = self::deamon_info();
		log::add('alexaapi_cookie', 'info', 'Lancement du démon cookie');
		$log = $_debug ? '1' : '0';
		$sensor_path = realpath(dirname(__FILE__) . '/../../resources');
		$cmd = "kill $(ps aux | grep 'initCookie.js' | awk '{print $2}')";	//Par sécurité, on Kill un éventuel précédent proessus initCookie.js
		log::add('alexaapi', 'debug', '---- Kill initCookie.js: ' . $cmd);
		$cmd = 'nice -n 19 nodejs ' . $sensor_path . '/initCookie.js ' . config::byKey('internalAddr') . ' ' . config::byKey('amazonserver', 'alexaapi', 'amazon.fr') . ' ' . config::byKey('alexaserver', 'alexaapi', 'alexa.amazon.fr');
		log::add('alexaapi', 'debug', '---- Lancement démon Alexa-API-Cookie sur port 3457 : ' . $cmd);
		$result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_cookie') . ' 2>&1 &');
		if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
			log::add('alexaapi', 'error', $result);
			return false;
		}
		message::removeAll('alexaapi', 'unableStartDeamonCookie');
		log::add('alexaapi_cookie', 'info', 'Démon cookie lancé');
		return true;
	}

	public static function deamonCookie_stop() {
		exec('kill $(ps aux | grep "/initCookie.js" | awk \'{print $2}\')');
		log::add('alexaapi', 'info', 'Arrêt du service cookie');
		$deamon_info = self::deamon_info();
		if ($deamon_info['stateCookie'] == 'ok') {
			sleep(1);
			exec('kill -9 $(ps aux | grep "/initCookie.js" | awk \'{print $2}\')');
		}
	}

	public static function dependancy_info() {	//************Dépendances ***********
		$return = array();
		$return['log'] = 'alexaapi_dep';
		$resources = realpath(dirname(__FILE__) . '/../../resources/');
		$packageJson=json_decode(file_get_contents($resources.'/package.json'),true);
		$state='ok';
		foreach($packageJson["dependencies"] as $dep => $ver){
			if(!file_exists($resources.'/node_modules/'.$dep.'/package.json')) {
				$state='nok';
			}
		}
		$return['progress_file'] = jeedom::getTmpFolder('alexaapi') . '/dependance';
		//$return['state'] = is_dir($resources.'/node_modules') ? 'ok' : 'nok';
		$return['state']=$state;
		return $return;
	}
	
	public static function supprimeTouslesDevices() {
		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Suppression en cours ...', __FILE__)));
		$plugin = plugin::byId('alexaapi');
		$eqLogics = eqLogic::byType($plugin->getId());
		foreach ($eqLogics as $eqLogic)
			{
			$eqLogic->remove();
			}
		self::scanAmazonAlexa();
	}
		
	public static function cron($_eqlogic_id = null) {
		// Toutes les minutes, on cherche les players en lecture et on les actualise
		$dd= new Cron\CronExpression('* * * * *', new Cron\FieldFactory);
		$deamon_info = self::deamon_info();
		if ($dd->isDue() && $deamon_info['state'] == 'ok') {
			$plugin = plugin::byId('alexaapi');
			$eqLogics = eqLogic::byType($plugin->getId());
			foreach($eqLogics as $eqLogic) {
				if ($eqLogic->getStatus('Playing')) {// On va chercher un Device en "Playing"
						log::add('alexaapi', 'debug', 'Refresh automatique (CRON) de '.$eqLogic->getName());
						$eqLogic->refresh();
				}
			}
		}

		$d = new Cron\CronExpression('*/15 * * * *', new Cron\FieldFactory);
		$deamon_info = self::deamon_info();
		if ($d->isDue() && $deamon_info['state'] == 'ok') {
			log::add('alexaapi', 'debug', '---------------------------------------------DEBUT CRON-'.$autorefresh.'-----------------------');
			$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/devices");
			$json = json_decode($json, true);
			$status=[];
			foreach ($json as $item) {
				if ($item['name'] == 'This Device') continue;
				
				$eq=eqLogic::byLogicalId($item['serial'],'alexaapi');
				if(is_object($eq)) {
					log::add('alexaapi','debug','updating online status of '.$item['name'].' to '.(($item['online'])?'true':'false'));
					$eq->setStatus('online', (($item['online'])?true:false));
				}
			}
			
			
			/*
			26/10/2019 Sigalou Désactivation du test 2060, devenu inutile et provoquant un souci avec mqtt
			$eqLogics = ($_eqlogic_id !== null) ? array(eqLogic::byId($_eqlogic_id)) : eqLogic::byType('alexaapi', true);
			$test2060NOK=true;
			$hasOneReminderDevice=false;
			foreach ($eqLogics as $alexaapi) {
				if($alexaapi->hasCapaorFamilyorType("REMINDERS") && $alexaapi->getStatus('online') == true) {
					$hasOneReminderDevice=true;
					log::add('alexaapi', 'debug', '-----------------------------Test     Lancé sur *'.$alexaapi->getName().'*------------------------');
					if ($test2060NOK && $alexaapi->test2060()) {
						$test2060NOK=false;
					} else {
						break;	
					}


					//log::add('alexaapi', 'debug', '---------------------------------------------FIN Boucle CRON------------------------');
					sleep(2);
				}
				else {
					log::add('alexaapi', 'debug', '-----------------------------Test NON Lancé sur *'.$alexaapi->getName().'*------------------------');
				}			
			}

			// On va tester si la connexion est active à l'aide d'un rappel en 2060 qu'on retire derrière.
			// $compteurNbTest2060OK correspond au nb de test qui on été OK, si =0 faut relancer le serveur
			if ($test2060NOK && $hasOneReminderDevice) {
				self::restartServeurPHP();
				//message::add('alexaapi', 'Connexion close détectée dans le CRON, relance transparente du serveur '.date("Y-m-d H:i:s").' OK !');
				log::add('alexaapi', 'debug', 'Connexion close détectée dans le CRON, relance transparente du serveur '.date("Y-m-d H:i:s").' OK !');
			}
			else {//pourra $etre supprimé quand stable
				if($hasOneReminderDevice) {
					log::add('alexaapi', 'debug', 'Connexion close non détectée dans le CRON. Tout va bien.');
				} else {
					log::add('alexaapi', 'debug', 'Aucun périphérique ne gère les rappels, on ne peut pas tester les connexions close.');
				}
			}*/
		}
		
		$c = new Cron\CronExpression('*/6 * * * *', new Cron\FieldFactory);
		if ($c->isDue() && $deamon_info['state'] == 'ok') {
		self::checkAuth();		
		}	
			
		$autorefreshRR = config::byKey('autorefresh', 'alexaapi', '33 3 * * *');/* boucle qui relance la connexion au serveur*/
		$cc = new Cron\CronExpression($autorefreshRR, new Cron\FieldFactory);
		if ($cc->isDue() && $deamon_info['state'] == 'ok') {
		self::restartServeurPHP();		
		}
		
		$r = new Cron\CronExpression('*/15 * * * *', new Cron\FieldFactory);// boucle refresh
		if ($r->isDue() && $deamon_info['state'] == 'ok') {
			$eqLogics = ($_eqlogic_id !== null) ? array(eqLogic::byId($_eqlogic_id)) : eqLogic::byType('alexaapi', true);
			foreach ($eqLogics as $alexaapi) {
				$alexaapi->refresh(); 				
				sleep(2);
			}	
		}
	//log::add('alexaapi', 'debug', '---------------------------------------------FIN CRON------------------------');
	}

	public static function checkAuth() {
				$result = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/checkAuth");
				$resultjson = json_decode($result, true);
				$value = $resultjson['authenticated'];	
		if ($value==1)	
			log::add('alexaapi', 'debug', 'Résultat du checkAuth  OK ('.$value.')');
		else
		{
			log::add('alexaapi', 'debug', 'Résultat du checkAuth NOK ('.$value.') ==> Relance Serveur');
			self::restartServeurPHP();
			message::add('alexaapi', '(Beta Alexa-api) Authentification Amazon revalidée, tout va bien');
		}
	}

	public static function restartServeurPHP() {
		$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/restart");
		sleep(2);
	}

	public static function forcerDefaultCmd($_id = null) {
		if (!is_null($_id)) { 
		$device = alexaapi::byId($_id);
				if (is_object($device)) {
				$device->setStatus('forceUpdate',true);
				$device->save();
				}
		}		
	}

	public static function forcerDefaultAllCmd() {
		$plugin = plugin::byId('alexaapi');
		$eqLogics = eqLogic::byType($plugin->getId());
			foreach ($eqLogics as $eqLogic)
			{
				$eqLogic->setStatus('forceUpdate',true);
				$eqLogic->save();  
			}		
	event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Mise à jour terminée', __FILE__)));
	}

	public static function scanAmazonAlexa() {
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != "ok") {
			event::add('jeedom::alert', array('level' => 'danger', 'page' => 'alexaapi', 'message' => __('Cookie Amazon Absent, allez dans la Configuration du plugin', __FILE__),));
			return;
		}
		// --- Mise à jour des Amazon Echo
		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan en cours...', __FILE__),));
		$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/devices");
		$json = json_decode($json, true);
		$numDevices = 0;
		$numNewDevices = 0;
		foreach ($json as $item) {
			// Skip the special device named "This Device"
			if ($item['name'] == 'This Device') continue;
			// On teste s'il faut créer un autre Device Player
			if  ((config::byKey('utilisateurMultimedia', 'alexaapi',0)!="0") && (in_array("AUDIO_PLAYER",$item['capabilities']))) {
					// Device PLAYLIST
					$device = alexaapi::byLogicalId($item['serial']."_playlist", 'alexaapi');
					if (!is_object($device)) {
						$device = self::createNewDevice($item['name']." PlayList", $item['serial']."_playlist");
						$device->setIsVisible(0);					
					}
					// Update device configuration
					$device->setConfiguration('device', $item['name']);
					$device->setConfiguration('type', $item['type']);
					$device->setConfiguration('devicetype', "PlayList");
					$device->setConfiguration('family', $item['family']);
					$device->setConfiguration('members', $item['members']);
					$device->setIsVisible(0);
					$device->setIsEnable(0);
					$device->setConfiguration('capabilities', $item['capabilities']);
					$device->save();
					$device->setStatus('online', (($item['online'])?true:false));
					// Device PLAYER
					$device = alexaapi::byLogicalId($item['serial']."_player", 'alexaapi');
						if (!is_object($device)) {
							$device = self::createNewDevice($item['name']." Player", $item['serial']."_player");
							$numNewDevices++;
							$device->setConfiguration('widgetPlayListEnable', 0);
						}
					// Update device configuration
					$device->setConfiguration('device', $item['name']);
					$device->setConfiguration('type', $item['type']);
					$device->setConfiguration('devicetype', "Player");
					$device->setConfiguration('family', $item['family']);
					$device->setConfiguration('members', $item['members']);
					$device->setConfiguration('capabilities', $item['capabilities']);
					$device->save();
					$device->setStatus('online', (($item['online'])?true:false));
					$numDevices++;
			}
			// Retireve the device (if already registered in Jeedom)
			$device = alexaapi::byLogicalId($item['serial'], 'alexaapi');
			if (!is_object($device)) {
				$device = self::createNewDevice($item['name'], $item['serial']);
				//$device->save();
				$numNewDevices++;
			}
			// Update device configuration
			$device->setConfiguration('device', $item['name']);
			$device->setConfiguration('type', $item['type']);
			$device->setConfiguration('devicetype', "Echo");
			$device->setConfiguration('family', $item['family']);
			$device->setConfiguration('members', $item['members']);
			$device->setConfiguration('capabilities', $item['capabilities']);
			$device->save();
			$device->setStatus('online', (($item['online'])?true:false)); //SetStatus doit être lancé après Save et Save après inutile
			$numDevices++;
		}
		
		if (config::byKey('utilisateurSmarthome', 'alexaapi',0)!="0") {			
			// --- Mise à jour des SmartHome Devices
			$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/smarthomeEntities");
			$json = json_decode($json, true);
			foreach ($json as $item) {
				// Retireve the device (if already registered in Jeedom)
				$device = alexaapi::byLogicalId($item['id'], 'alexaapi');
				if (!is_object($device)) {
					$device = self::createNewDevice($item['displayName'], $item['id']);
					$numNewDevices++;
				}
				// Update device configuration
				$device->setConfiguration('device', $item['displayName']);
				//$device->setConfiguration('type', $item['description']); a voir si on utilise ou pas descriotion
				$device->setConfiguration('type', $item['providerData']['deviceType']);
				$device->setConfiguration('devicetype', "Smarthome");
				$device->setConfiguration('family', $item['providerData']['categoryType']);
				//$device->setConfiguration('members', $item['members']);
				$device->setConfiguration('capabilities', $item['supportedProperties']);
				//On va mettre dispo, on traite plus tard.
				//$device->setStatus('online', (($item['online'])?true:false));
				$device->save();
				$device->setStatus('online', 'true');
				$numDevices++;
			}
		}
	event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan terminé. ' . $numDevices . ' équipements mis a jour dont ' . $numNewDevices . " ajouté(s). Appuyez sur F5 si votre écran ne s'est pas actualisé", __FILE__)));
	}

	private static function createNewDevice($deviceName, $deviceSerial) {
		$defaultRoom = intval(config::byKey('defaultParentObject','alexaapi','',true));
		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Ajout de "'.$deviceName.'"', __FILE__),));
		$newDevice = new alexaapi();
		$newDevice->setName($deviceName);
		$newDevice->setLogicalId($deviceSerial);
		$newDevice->setEqType_name('alexaapi');
		$newDevice->setIsVisible(1);
		if($defaultRoom) $newDevice->setObject_id($defaultRoom);
		// JUSTE pour SIGALOU pour aider au dev
		if (substr ($deviceName,0,7) == "Piscine")
			$newDevice->setObject_id('15');
		$newDevice->setDisplay('height', '500');
		$newDevice->setConfiguration('device', $deviceName);
		$newDevice->setConfiguration('serial', $deviceSerial);
		$newDevice->setIsEnable(1);
		return $newDevice;
	}

	public function hasCapaorFamilyorType($thisCapa) {
		
		// Si c'est la bonne famille, on dit OK tout de suite
		$family=$this->getConfiguration('family',"");	
		if($thisCapa == $family) return true; // ajouté pour filtrer sur la famille (pour les groupes par exemple)
		// Si c'est le bon type, on dit OK tout de suite
		$type=$this->getConfiguration('type',"");	
		if($thisCapa == $type) return true; // 
		$capa=$this->getConfiguration('capabilities',"");
		if(((gettype($capa) == "array" && in_array($thisCapa,$capa))) || ((gettype($capa) == "string" && strpos($capa, $thisCapa) !== false))) {
			if($thisCapa == "REMINDERS" && $type == "A15ERDAKK5HQQG") return false;
			return true;
		} else {
			return false;
		}
	}
	
	public function sortBy($field, &$array, $direction = 'asc') {
		usort($array, create_function('$a, $b', '
		$a = $a["' . $field . '"];
		$b = $b["' . $field . '"];
		if ($a == $b) return 0;
		$direction = strtolower(trim($direction));
		return ($a ' . ($direction == 'desc' ? '>' : '<') . ' $b) ? -1 : 1;
    	'));
		return true;
	}

	public function refresh() { //$_routines c'est pour éviter de charger les routines lors du scan
		$deamon_info = alexaapi::deamon_info();
		if ($deamon_info['state'] != 'ok') return false;
		$widgetPlayer=($this->getConfiguration('devicetype') == "Player");
		$widgetSmarthome=($this->getConfiguration('devicetype') == "Smarthome");
		$widgetPlaylist=($this->getConfiguration('devicetype') == "PlayList");
		$widgetEcho=(!($widgetPlayer||$widgetSmarthome||$widgetPlaylist));
		$device=str_replace("_player", "", $this->getConfiguration('serial'));

		if ($widgetPlayer) {	// Refresh d'un player
			$url = network::getNetworkAccess('internal'). "/plugins/alexaapi/core/php/jeeAlexaapi.php?apikey=".jeedom::getApiKey('alexaapi')."&nom=refreshPlayer"; // Envoyer la commande Refresh via jeeAlexaapi
			$ch = curl_init($url);
			$data = array(
				'deviceSerialNumber' => $device,
				'audioPlayerState' => 'REFRESH'
			);
			$payload = json_encode($data);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			$_playlists=true;
		}

		if ($_playlists) {
			$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/playlists?device=".$device);
			$json = json_decode($json, true);	
			$ListeDesRoutines = [];
			foreach ($json as $key => $value) {
				foreach ($value as $key2 => $playlist) {
					foreach ($playlist as $key3 => $value2) {
					$ListeDesPlaylists[]= $value2['playlistId'] . '|' . $value2['title']." (".$value2['trackCount'].")";
					}	
				}
			}		
			$cmd = $this->getCmd(null, 'playList');
			if (is_object($cmd)) { //routine existe on  met à jour la liste des routines
				$cmd->setConfiguration('listValue', join(';',$ListeDesPlaylists));
				$cmd->save();
				log::add('alexaapi', 'debug', 'Mise à jour de la liste des Playlists de '.$this->getName());
			}
		}

		if ($widgetEcho)	{
			log::add('alexaapi', 'debug', 'execute : refresh routines');
			$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/routines");
			$json = json_decode($json, true);	// Met à jour la liste des routines des commandes action "routine"
			self::sortBy('utterance', $json, 'asc');
			$ListeDesRoutines = [];
			foreach ($json as $item) {
				if ($item['utterance'] != '') 
					$ListeDesRoutines[]= $item['creationTimeEpochMillis'] . '|' . $item['utterance'];
				else {
					if ($item['triggerTime'] != '') $resultattriggerTime = substr($item['triggerTime'], 0, 2) . ":" . substr($item['triggerTime'], 2, 2);
					$ListeDesRoutines[]= $item['creationTimeEpochMillis'] . '|' . $resultattriggerTime;
				}
			}
			$cmd = $this->getCmd(null, 'routine');
			if (is_object($cmd)) {
				$cmd->setConfiguration('listValue', join(';',$ListeDesRoutines));
				$cmd->save();
			}

			try {
				foreach ($this->getCmd('action') as $cmd) {
					if ($cmd->getConfiguration('RunWhenRefresh', 0) != '1') {
						continue; // si le lancement n'est pas prévu, ça va au bout de la boucle foreach
					}
					$value = $cmd->execute();
				}
			}
			catch(Exception $exc) {log::add('alexaapi', 'error', __('Erreur pour ', __FILE__) . $this->getHumanName() . ' : ' . $exc->getMessage());}
		}
	}
		
	public function test2060() {
		$deamon_info = alexaapi::deamon_info();
		if ($deamon_info['state'] != 'ok') {
			log::add('alexaapi', 'debug', '-----------------------------Demon non OK, Test annulé------------------------');
			return 0;
		}
		// Rustine d'anti-connexion close
		// On va aller ajouter un rappel en 2060 et on va aller vérifier si elle a bien été ajoutée.
		$cmd = $this->getCmd(null, 'reminder');
		if (is_object($cmd)) {
			// Nous sommes sur un équipement qui a la function reminder, sinon on ne fait pas le test du rappel en 2060
			$options['when']="2060-12-31 23:59:00";
			$options['text']="test Alexa-api";
			$value = $cmd->execute($options);
			// On liste les alarmes 
			$trouveReminder=false;
			$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/reminders");
			$json = json_decode($json, true);
			foreach($json as $item)
			{
				if ($item['type']!="Reminder") continue;
				//log::add('alexaapi', 'debug', '*********************************************************On boucle sur item:'.$item['originalDate']);
				if (($item['originalDate']=="2060-12-31") && ($item['reminderLabel']=="Test Alexa-api")) {
					$trouveReminder=true;
					// On supprime le rappel 2060
					$cmd = $this->getCmd(null, 'deleteReminder');
					log::add('alexaapi', 'debug', '**********************Suppression Reminder id**'.$item['id'].'*********************************');
					$options['id']=$item['id'];
					$value = $cmd->execute($options);	
				}
			}
			if ($trouveReminder) {
				log::add('alexaapi', 'debug', '********************** TROUVE le Reminder 2060 donc c\'est OK**********************************');
				return true ;
			}
			else {
				log::add('alexaapi', 'debug', '**********************PAS TROUVE**'.$cmd->getName().'*********************************');
				return false;
			}
		}		
	}
	
	

	public function updateCmd ($forceUpdate, $LogicalId, $Type, $SubType, $RunWhenRefresh, $Name, $IsVisible, $title_disable, $setDisplayicon, $setTemplate_version, $setTemplate_lien, $request, $infoName, $listValue, $Order, $Test) {
	// Le $setTemplate_version n'est plus utilisé, il peut être pris pour autre chose.
		if ($Test) {
			try {
				if (empty($Name)) $Name=$LogicalId;
				$cmd = $this->getCmd(null, $LogicalId);
				if ((!is_object($cmd)) || $forceUpdate) {
					if (!is_object($cmd)) $cmd = new alexaapiCmd();
					$cmd->setType($Type);
					$cmd->setLogicalId($LogicalId);
					$cmd->setSubType($SubType);
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName($Name);
					$cmd->setIsVisible((($IsVisible)?1:0));
					if (!empty($setTemplate_lien)) {
						$cmd->setTemplate("dashboard", $setTemplate_lien);
						$cmd->setTemplate("mobile", $setTemplate_lien);
					}						
					if (!empty($setDisplayicon)) $cmd->setDisplay('icon', '<i class="'.$setDisplayicon.'"></i>');
					if (!empty($request)) $cmd->setConfiguration('request', $request);
					if (!empty($infoName)) $cmd->setConfiguration('infoName', $infoName);
					if (!empty($listValue)) $cmd->setConfiguration('listValue', $listValue);
					if (($LogicalId=='volumeinfo') || ($LogicalId=='volume')) {
						$cmd->setConfiguration('minValue', '0');
						$cmd->setConfiguration('maxValue', '100');
						$cmd->setDisplay('forceReturnLineBefore', true);
						}
					$cmd->setConfiguration('RunWhenRefresh', $RunWhenRefresh);				
					$cmd->setDisplay('title_disable', $title_disable);
					$cmd->setOrder($Order);
				}
				$cmd->save();
			}
			catch(Exception $exc) {
				log::add('alexaapi', 'error', __('Erreur pour ', __FILE__) . ' : ' . $exc->getMessage());
			}
		} else {
		$cmd = $this->getCmd(null, $LogicalId);
			if (is_object($cmd)) {
				$cmd->remove();
			}
		}
	}




	public function postSave() {
		//log::add('alexaapi', 'debug', '**********************postSave '.$this->getName().'***********************************');
		$F=$this->getStatus('forceUpdate');// forceUpdate permet de recharger les commandes à valeur d'origine, mais sans supprimer/recréer les commandes
				$capa=$this->getConfiguration('capabilities','');
				$type=$this->getConfiguration('type','');
		if(!empty($capa)) {
					if (strstr($this->getName(), "Alexa Apps")) {
						self::updateCmd ($F, 'push', 'action', 'message', false, 'Push', true, true, 'fa jeedomapp-audiospeak', null, null, 'push?text=#message#', null, null, 1, true);
						return;
					}
			$widgetPlayer=($this->getConfiguration('devicetype') == "Player");
			$widgetSmarthome=($this->getConfiguration('devicetype') == "Smarthome");
			$widgetPlaylist=($this->getConfiguration('devicetype') == "PlayList");

			$cas1=(($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && $widgetPlayer);
			$cas1bis=(($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && !$widgetPlayer);
			$cas2=(($this->hasCapaorFamilyorType("TIMERS_AND_ALARMS")) && !$widgetPlayer);
			$cas3=(($this->hasCapaorFamilyorType("REMINDERS")) && !$widgetPlayer);
			$cas4=(($this->hasCapaorFamilyorType("REMINDERS")) && !$widgetSmarthome);
			$cas5=($this->hasCapaorFamilyorType("VOLUME_SETTING"));
			$cas6=($cas5 && (!$this->hasCapaorFamilyorType("WHA")));
			$cas7=((!$this->hasCapaorFamilyorType("WHA")) && ($this->getConfiguration('devicetype') != "Player") &&(!$this->hasCapaorFamilyorType("FIRE_TV")) && !$widgetSmarthome && (!$this->hasCapaorFamilyorType("AMAZONMOBILEMUSIC_ANDROID")));
			$cas8=(($this->hasCapaorFamilyorType("turnOff")) && $widgetSmarthome);


			self::updateCmd ($F, 'musicalalarmmusicentity', 'action', 'other', true, 'musicalalarmmusicentity', false, false, null, null, null, 'musicalalarmmusicentity?position=1', 'Musical Alarm Music', null, 1, $cas2);
			self::updateCmd ($F, 'whennextmusicalalarm', 'action', 'other', true, 'Next Musical Alarm When', false, false, 'fa-bell', null, null, 'whennextmusicalalarm?position=1', 'Next Musical Alarm Hour', null, 1, $cas2);
			self::updateCmd ($F, 'interactioninfo', 'info', 'string', false, 'Dernier dialogue avec Alexa', true, false, null, 'dashboard','alexaapi::interaction', null, null, null, 2, $cas7);	
			self::updateCmd ($F, 'bluetoothDevice', 'info', 'string', false, 'Est connecté en Bluetooth', true, false, null, 'dashboard','alexaapi::interaction', null, null, null, 2, $cas7);				
			self::updateCmd ($F, 'whennextalarm', 'action', 'other', true, 'Next Alarm When', false, false, 'fa-bell', null, null, 'whennextalarm?position=1', 'Next Alarm Hour', null, 2, $cas2);				
			self::updateCmd ($F, 'deleteReminder', 'action', 'message', false, 'DeleteReminder', false, false, 'maison-poubelle', null, null, 'deleteReminder?id=#id#', null, null, 2, $cas3);			
			self::updateCmd ($F, 'subText2', 'info', 'string', false, null, true, false, null, 'dashboard', 'alexaapi::subText2', null, null, null, 2, $cas1);
			self::updateCmd ($F, 'subText1', 'info', 'string', false, null, true, false, null, 'dashboard', 'alexaapi::title', null, null, null, 4, $cas1);			
			self::updateCmd ($F, 'url', 'info', 'string', false, null, true, false, null, 'dashboard', 'alexaapi::image', null, null, null, 5, $cas1);			
			self::updateCmd ($F, 'title', 'info', 'string', false, null, true, false, null, 'dashboard', 'alexaapi::title', null, null, null, 9, $cas1);
			self::updateCmd ($F, 'previous', 'action', 'other', false, 'Previous', true, true, 'fa fa-step-backward', null, null, 'command?command=previous', null, null, 16, $cas1);
			self::updateCmd ($F, 'pause', 'action', 'other', false, 'Pause', true, true, 'fa fa-pause', null, null, 'command?command=pause', null, null, 17, $cas1);
			self::updateCmd ($F, 'play', 'action', 'other', false, 'Play', true, true, 'fa fa-play', null, null, 'command?command=play', null, null, 18, $cas1);
			self::updateCmd ($F, 'next', 'action', 'other', false, 'Next', true, true, 'fa fa-step-forward', null, null, 'command?command=next', null, null, 19, $cas1);			
			self::updateCmd ($F, 'providerName', 'info', 'string', false, 'Fournisseur de musique :', true, true, 'loisir-musical7', null, null , null, null, null, 20, $cas1);
			self::updateCmd ($F, 'contentId', 'info', 'string', false, 'Amazon Music Id', false, true, 'loisir-musical7', null, null , null, null, null, 21, $cas1);			
			self::updateCmd ($F, 'routine', 'action', 'select', false, 'Lancer une routine', true, false, null, 'dashboard','alexaapi::list', 'routine?routine=#select#', null, 'Lancer Refresh|Lancer Refresh', 21, $cas3);			
			self::updateCmd ($F, 'playList', 'action', 'select', false, 'Ecouter une playlist', true, false, null, 'dashboard', 'alexaapi::list', 'playlist?playlist=#select#', null, 'Lancer Refresh|Lancer Refresh', 24, $cas1);
			self::updateCmd ($F, 'radio', 'action', 'select', false, 'Ecouter une radio', true, false, null, 'dashboard', 'alexaapi::list', 'radio?station=#select#', null, 's2960|Nostalgie;s6617|RTL;s6566|Europe1', 25, $cas1);	
			self::updateCmd ($F, 'playMusicTrack', 'action', 'select', false, 'Ecouter une piste musicale', true, false, null, 'dashboard', 'alexaapi::list', 'playmusictrack?trackId=#select#', null, '53bfa26d-f24c-4b13-97a8-8c3debdf06f0|Piste1;7b12ee4f-5a69-4390-ad07-00618f32f110|Piste2', 26, $cas1);
			self::updateCmd ($F, 'volume', 'action', 'slider', false, 'Volume', true, true, 'fa fa-volume-up', 'dashboard','alexaapi::volume', 'volume?value=#slider#', null, null, 27, $cas5);			
			self::updateCmd ($F, 'volumeinfo', 'info', 'string', false, 'Volume Info', false, false, 'fa fa-volume-up', null, null, null, null, null, 28, $cas6);	
			self::updateCmd ($F, 'whennextalarminfo', 'info', 'string', false, 'Next Alarm Hour', true, false, null, 'dashboard','alexaapi::alarm', null, null, null, 29, $cas2);			
			self::updateCmd ($F, 'whennextmusicalalarminfo', 'info', 'string', false, 'Next Musical Alarm Hour', true, false, null, 'dashboard','alexaapi::alarmmusical', null, null, null, 30, $cas2);	
			self::updateCmd ($F, 'musicalalarmmusicentityinfo', 'info', 'string', false, 'Musical Alarm Music', true, false, 'loisir-musical7', 'dashboard','alexaapi::alarmmusicalmusic', null, null, null, 31, $cas2);			
			self::updateCmd ($F, 'whennextreminderinfo', 'info', 'string', false, 'Next Reminder Hour', true, false, null, 'dashboard','alexaapi::reminder', null, null, null, 32, $cas3);
			self::updateCmd ($F, 'whennextreminder', 'action', 'other', true, 'Next Reminder When', false, false, null, null, null, 'whennextreminder?position=1', 'Next Reminder Hour', null, 33, $cas3);
			self::updateCmd ($F, 'whennextreminderlabel', 'action', 'other', true, 'whennextreminderlabel', false, false, null, null, null, 'whennextreminderlabel?position=1', 'Reminder Label', null, 34, $cas3);
			self::updateCmd ($F, 'whennextreminderlabelinfo', 'info', 'string', false, 'Reminder Label', true, false, 'loisir-musical7', 'dashboard','alexaapi::alarmmusicalmusic', null, null, null, 35, $cas2);
	
			
			
			
			self::updateCmd ($F, 'alarm', 'action', 'select', false, 'Lancer une alarme', true, false, null, 'dashboard', 'alexaapi::list', 'alarm?when=#when#&recurring=#recurring#&sound=#sound#', null, 'system_alerts_melodic_01|Alarme simple;system_alerts_melodic_01|Timer simple;system_alerts_melodic_02|A la dérive;system_alerts_atonal_02|Métallique;system_alerts_melodic_05|Clarté;system_alerts_repetitive_04|Comptoir;system_alerts_melodic_03|Focus;system_alerts_melodic_06|Lueur;system_alerts_repetitive_01|Table de chevet;system_alerts_melodic_07|Vif;system_alerts_soothing_05|Orque;system_alerts_atonal_03|Lumière du porche;system_alerts_rhythmic_02|Pulsar;system_alerts_musical_02|Pluvieux;system_alerts_alarming_03|Ondes carrées', 36, $cas3);
			//self::updateCmd ($F, 'rwd', 'action', 'other', false, 'Rwd', true, true, 'fa fa-fast-backard', null, null, 'command?command=rwd', null, null, 15, $cas1);
			//self::updateCmd ($F, 'fwd', 'action', 'other', false, 'Fwd', true, true, 'fa fa-step-forward', null, null, 'command?command=fwd', null, null, 20, $cas1);
			//self::updateCmd ($F, 'repeat', 'action', 'other', false, 'Repeat', true, true, 'fa fa-refresh', null, null, 'command?command=repeat', null, null, 25, $cas1);
			//self::updateCmd ($F, 'shuffle', 'action', 'other', false, 'Shuffle', true, true, 'fa fa-random', null, null, 'command?command=shuffle', null, null, 26, $cas1);
			
			self::updateCmd ($F, 'playlistName', 'info', 'string', false, null, true, true, null, null, null, null, null, null, 79, $widgetPlaylist);
			//self::updateCmd ($F, 'playlistName', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 2, $widgetPlayer);
			//self::updateCmd ($F, 'songName', 'info', 'string', false, null, true, false, null, null, null, null, null, null, 79, ($widgetPlaylist || $widgetPlayer));
			self::updateCmd ($F, 'playlisthtml', 'info', 'string', false, null, true, true, null, null, null, null, null, null, 79, $widgetPlaylist);
			self::updateCmd ($F, 'turnOn', 'action', 'other', false, 'turnOn', true, true, "fas fa-circle", null, null, 'SmarthomeCommand?command=turnOn', null, null, 79, $cas8);			
			self::updateCmd ($F, 'turnOff', 'action', 'other', false, 'turnOff', true, true, "far fa-circle", null, null, 'SmarthomeCommand?command=turnOff', null, null, 79, $cas8);

			self::updateCmd ($F, 'command', 'action', 'message', false, 'Command', false, true, "fa fa-play-circle", null, null, 'command?command=#select#', null, null, 79, $cas1);		
			self::updateCmd ($F, 'speak', 'action', 'message', false, 'Faire parler Alexa', true, true, null, 'dashboard', 'alexaapi::message', 'speak?text=#message#', null, null, 79, $cas1bis);
			self::updateCmd ($F, 'announcement', 'action', 'message', false, 'Lancer une annonce', false, true, null, 'dashboard', 'alexaapi::message', 'announcement?text=#message#', null, null, 79, $cas1bis);			
			self::updateCmd ($F, 'mediaLength', 'info', 'string', false, null, false, false, null, null, null , null, null, null, 79, $cas1);
			self::updateCmd ($F, 'mediaProgress', 'info', 'string', false, null, false, false, null, null, null , null, null, null, 79, $cas1);
			self::updateCmd ($F, 'state', 'info', 'string', false, null, true, false, null, 'dashboard', 'alexaapi::state', null, null, null, 79, $cas1);
			//self::updateCmd ($F, 'playlistName', 'info', 'string', false, null, false, false, null, null, null, null, null, null, 2, $cas1);
			self::updateCmd ($F, 'nextState', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 79, $cas1);
			self::updateCmd ($F, 'previousState', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 79, $cas1);
			self::updateCmd ($F, 'playPauseState', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 79, $cas1);
			//self::updateCmd ($F, 'loopMode', 'info', 'string', false, null, true, false, null, null, null, null, null, null, 79, $cas1);
			//self::updateCmd ($F, 'playBackOrder', 'info', 'string', false, null, true, false, null, null, null, null, null, null, 79, $cas1);
			//self::updateCmd ($F, 'alarm', 'action', 'message', false, 'Alarm', false, true, 'fa fa-bell', null, null, 'alarm?when=#when#&recurring=#recurring#&sound=#sound#', null, null, 79, $cas2);
			self::updateCmd ($F, 'deleteallalarms', 'action', 'message', false, 'Delete All Alarms', false, false, 'maison-poubelle', null, null, 'deleteallalarms?type=alarm&status=all', null, null, 79, $cas2);
				if($type == "A15ERDAKK5HQQG") {
					log::add('alexaapi', 'warning', '****Rencontre du type A15ERDAKK5HQQG = Sonos Première Génération sur : '.$this->getName());
					log::add('alexaapi', 'warning', '****On ne crée pas les commandes REMINDERS dessus car bug!');
				}
			self::updateCmd ($F, 'reminder', 'action', 'message', false, 'Envoyer un rappel', true, false, null, 'dashboard', 'alexaapi::message', 'reminder?text=#message#&when=#when#&recurring=#recurring#', null, null, 79, $cas3);	


			$volinfo = $this->getCmd(null, 'volumeinfo');
			$vol = $this->getCmd(null, 'volume');
					if((is_object($volinfo)) && (is_object($vol))) {
					$vol->setValue($volinfo->getId());// Lien entre volume et volumeinfo
					$vol->save();
					}
		// Pour la commande Refresh, on garde l'ancienne méthode
			if (($this->hasCapaorFamilyorType("REMINDERS")) && !($this->getConfiguration('devicetype') == "Smarthome")) { 
				//Commande Refresh
				$createRefreshCmd = true;
				$refresh = $this->getCmd(null, 'refresh');
				if (!is_object($refresh)) {
					$refresh = cmd::byEqLogicIdCmdName($this->getId(), __('Rafraichir', __FILE__));
					if (is_object($refresh)) {
						$createRefreshCmd = false;
					}
				}
				if ($createRefreshCmd) {
					if (!is_object($refresh)) {
						$refresh = new alexaapiCmd();
						$refresh->setLogicalId('refresh');
						$refresh->setIsVisible(1);
						$refresh->setDisplay('icon', '<i class="fa fa-sync"></i>');
						$refresh->setName(__('Refresh', __FILE__));
					}
					$refresh->setType('action');
					$refresh->setSubType('other');
					$refresh->setEqLogic_id($this->getId());
					$refresh->save();
				}
			}
		} else {
		log::add('alexaapi', 'warning', 'Pas de capacité détectée sur '.$this->getName().' , assurez-vous que le démon est OK');
		}

		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Mise à jour de "'.$this->getName().'"', __FILE__),));
		$this->refresh(); 

		if ($widgetPlayer) {
				$device_playlist=str_replace("_player", "", $this->getConfiguration('serial'))."_playlist"; //Nom du device de la playlist
				// Si la case "Activer le widget Playlist" est cochée, on rend le device _playlist visible sinon on le passe invisible		
				$eq=eqLogic::byLogicalId($device_playlist,'alexaapi');
						if(is_object($eq)) {
							$eq->setIsVisible((($this->getConfiguration('widgetPlayListEnable'))?1:0));
							$eq->setIsEnable((($this->getConfiguration('widgetPlayListEnable'))?1:0));
							$eq->setObject_id($this->getObject_id()); // Attribue au widget Playlist la même pièce que son Player
							$eq->save();
						}
			}



		$this->setStatus('forceUpdate', false); //dans tous les cas, on repasse forceUpdate à false
	}

	public static function dependancy_install($verbose = "false") {
		if (file_exists(jeedom::getTmpFolder('alexaapi') . '/dependance')) {
			return;
		}
		log::remove('alexaapi_dep');
		$_debug = 0;
		if (log::getLogLevel('alexaapi') == 100 || $verbose === "true" || $verbose === true) $_debug = 1;
		log::add('alexaapi', 'info', 'Installation des dépendances : ');
		$resource_path = realpath(dirname(__FILE__) . '/../../resources');
		return array('script' => $resource_path . '/nodejs.sh ' . $resource_path . ' alexaapi ' . $_debug, 'log' => log::getPathToLog('alexaapi_dep'));
	}

	public function preUpdate() {
	}
	
	public function preRemove () {
		if ($this->getConfiguration('devicetype') == "Player") { // Si c'est un type Player, il faut supprimer le Device Playlist
			$device_playlist=str_replace("_player", "", $this->getConfiguration('serial'))."_playlist"; //Nom du device de la playlist
		$eq=eqLogic::byLogicalId($device_playlist,'alexaapi');
				if(is_object($eq)) $eq->remove();
		}
	}
	
	public function preSave() {
	}

// https://github.com/NextDom/NextDom/wiki/Ajout-d%27un-template-a-votre-plugin	
// https://jeedom.github.io/documentation/dev/fr_FR/widget_plugin	

  public function toHtml($_version = 'dashboard') {
	$replace = $this->preToHtml($_version);
	log::add('alexaapi_widget','debug','************Début génération Widget de '.$replace['#logicalId#']);  
	$typeWidget="alexaapi";	
	if ((substr($replace['#logicalId#'], -7))=="_player") $typeWidget="alexaapi_player";
	if ((substr($replace['#logicalId#'], -9))=="_playlist") $typeWidget="alexaapi_playlist";
    if ($typeWidget!="alexaapi_playlist") return parent::toHtml($_version);
	log::add('alexaapi_widget','debug',$typeWidget.'************Début génération Widget de '.$replace['#name#']);        
	if (!is_array($replace)) {
		return $replace;
	}
	$version = jeedom::versionAlias($_version);
	if ($this->getDisplay('hideOn' . $version) == 1) {
		return '';
	}
	foreach ($this->getCmd('info') as $cmd) {
		 	log::add('alexaapi_widget','debug',$typeWidget.'dans boucle génération Widget');        
            $replace['#' . $cmd->getLogicalId() . '_history#'] = '';
            $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
            $replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
            $replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
            if ($cmd->getLogicalId() == 'encours'){
                $replace['#thumbnail#'] = $cmd->getDisplay('icon');
            }
            if ($cmd->getIsHistorized() == 1) {
                $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
            }
        }
	$replace['#height#'] = '800';
		if ($typeWidget=="alexaapi_playlist") {
			if ("#playlistName#" != "") {
				$replace['#name_display#']='#playlistName#';
			}
		}
	log::add('alexaapi_widget','debug',$typeWidget.'***************************************************************************Fin génération Widget');        
	return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, $typeWidget, 'alexaapi')));
	}
}

class alexaapiCmd extends cmd {

	public function dontRemoveCmd() {
		if ($this->getLogicalId() == 'refresh') {
			return true;
		}
		return false;
	}
	
	public function postSave() {

	}
	
	public function preSave() {
		if ($this->getLogicalId() == 'refresh') {
			return;
		}
		if ($this->getType() == 'action') {
			$eqLogic = $this->getEqLogic();
			$this->setConfiguration('value', 'http://' . config::byKey('internalAddr') . ':3456/' . $this->getConfiguration('request') . "&device=" . $eqLogic->getConfiguration('serial'));
		}
		$actionInfo = alexaapiCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getName());
		if (is_object($actionInfo)) $this->setId($actionInfo->getId());
		if (($this->getType() == 'action') && ($this->getConfiguration('infoName') != '')) {//Si c'est une action et que Commande info est renseigné
			$actionInfo = alexaapiCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getConfiguration('infoName'));
			if (!is_object($actionInfo)) {//C'est une commande qui n'existe pas
				$actionInfo = new alexaapiCmd();
				$actionInfo->setType('info');
				$actionInfo->setSubType('string');
				$actionInfo->setConfiguration('taskid', $this->getID());
				$actionInfo->setConfiguration('taskname', $this->getName());
			}
			$actionInfo->setName($this->getConfiguration('infoName'));
			$actionInfo->setEqLogic_id($this->getEqLogic_id());
			$actionInfo->save();
			$this->setConfiguration('infoId', $actionInfo->getId());
		}
	}

	public function execute($_options = null) {
		if ($this->getLogicalId() == 'refresh') {
			$this->getEqLogic()->refresh();
			return;
		}
		$request = $this->buildRequest($_options);
		log::add('alexaapi', 'info', 'Request : ' . $request);//Request : http://192.168.0.21:3456/volume?value=50&device=G090LF118173117U
		$request_http = new com_http($request);
		$request_http->setAllowEmptyReponse(true);//Autorise les réponses vides
		if ($this->getConfiguration('noSslCheck') == 1) $request_http->setNoSslCheck(true);
		if ($this->getConfiguration('doNotReportHttpError') == 1) $request_http->setNoReportError(true);
		if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true) {// option non activée 
			$request_http->setNoReportError(true);
			$request_http->exec(0.1, 1);
			return;
		}
		$result = $request_http->exec($this->getConfiguration('timeout', 3), $this->getConfiguration('maxHttpRetry', 3));//Time out à 3s 3 essais
		if (!$result) throw new Exception(__('Serveur injoignable', __FILE__));
		// On traite la valeur de resultat (dans le cas de whennextalarm par exemple)
		$resultjson = json_decode($result, true);
		$value = $resultjson['value'];
		$detail = $resultjson['detail'];
		// Ici, on va traiter une commande qui n'a pas été executée correctement (erreur type "Connexion Close")
		if (($value =="Connexion Close") || ($detail =="Unauthorized")){
			log::add('alexaapi', 'debug', '**On traite '.$value.$detail.' Connexion Close** dans la Class');
			sleep(6);
				if (ob_get_length()) {
				ob_end_flush();
				flush();
				}	
			log::add('alexaapi', 'debug', '**On relance '.$request);
			$result = $request_http->exec($this->getConfiguration('timeout', 2), $this->getConfiguration('maxHttpRetry', 3));
			if (!result) throw new Exception(__('Serveur injoignable', __FILE__));
			$jsonResult = json_decode($json, true);
			if (!empty($jsonResult)) throw new Exception(__('Echec de l\'execution: ', __FILE__) . '(' . $jsonResult['title'] . ') ' . $jsonResult['detail']);
			$resultjson = json_decode($result, true);
			$value = $resultjson['value'];
		}
		
		if (($this->getType() == 'action') && ($this->getConfiguration('infoName') != '')) {
			foreach ($this->getEqLogic()->getCmd('info') as $cmd) {// On enregistre la valeur de retour dans le champ info
				if ($cmd->getName() == $this->getConfiguration('infoName')) {
					$cmd->setConfiguration('value', $value);
					$cmd->event($value);
					$cmd->save();
				}
			}
		}
		log::add('alexaapi', 'debug', 'Result : ' . $result);
		return true;
	}



	private function buildRequest($_options = array()) {
		if ($this->getType() != 'action') return $this->getConfiguration('request');
		list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);
		switch ($command) {
			case 'volume':
				$request = $this->build_ControledeSliderSelectMessage($_options, '50');
			break;
			case 'playlist':
			case 'routine':
				$request = $this->build_ControledeSliderSelectMessage($_options, "");
			break;			
			case 'playmusictrack':
				$request = $this->build_ControledeSliderSelectMessage($_options, "53bfa26d-f24c-4b13-97a8-8c3debdf06f0");
			break;				
			case 'speak':
			case 'announcement':
			case 'push':
				$request = $this->build_ControledeSliderSelectMessage($_options);
			break;
			case 'reminder':
			case 'alarm':
				$now=date("Y-m-d H:i:s", strtotime('+3 second'));
				$request = $this->build_ControleWhenTextRecurring($now, "Ceci est un essai", $_options);
			break;			
			case 'radio':
				$request = $this->build_ControledeSliderSelectMessage($_options, 's2960');
			break;
			case 'SmarthomeCommand':
				$request = $this->build_ControledeSliderSelectMessage();
			break;			
			case 'command':
				$request = $this->build_ControledeSliderSelectMessage($_options, 'pause');
			break;
			case 'whennextalarm':
			case 'whennextmusicalalarm':
			case 'musicalalarmmusicentity':
			case 'whennextreminderlabel':
			case 'whennextreminder':
				$request = $this->build_ControlePosition($_options);
			break;			
			case 'deleteallalarms':
				$request = $this->buildDeleteAllAlarmsRequest($_options);
			break;
			case 'deleteReminder':
				$request = $this->buildDeleteReminderRequest($_options);
			break;			
			case 'restart':
				$request = $this->buildRestartRequest($_options);
			break;				
			default:
				$request = '';
			break;
		}
		//log::add('alexaapi_debug', 'debug', '----RequestFinale:'.$request);
		$request = scenarioExpression::setTags($request);
		if (trim($request) == '') throw new Exception(__('Commande inconnue ou requête vide : ', __FILE__) . print_r($this, true));
		$device=str_replace("_player", "", $this->getEqLogic()->getConfiguration('serial'));
		return 'http://' . config::byKey('internalAddr') . ':3456/' . $request . '&device=' . $device;
	}

	
	private function build_ControledeSliderSelectMessage($_options = array(), $default = "0123 Ceci est un message de test") {
		$request = $this->getConfiguration('request');
		if ((isset($_options['slider'])) && ($_options['slider'] == "")) $_options['slider'] = $default;
		if ((isset($_options['select'])) && ($_options['select'] == "")) $_options['select'] = $default;
		if ((isset($_options['message'])) && ($_options['message'] == "")) $_options['message'] = $default;
		$request = str_replace(array('#slider#', '#select#', '#message#'), array($_options['slider'], $_options['select'], urlencode($_options['message'])), $request);
		return $request;
	}	

	private function build_ControleWhenTextRecurring($defaultWhen, $defaultText, $_options = array()) {
		$request = $this->getConfiguration('request');
		log::add('alexaapi', 'debug', '----build_ControledeSliderSelectMessage RequestFinale:'.$request);
		log::add('alexaapi', 'debug', '----build_ControledeSliderSelectMessage _optionsAVANT:'.json_encode($_options));
		if ((!isset($_options['sound'])) && (!isset($_options['message'])) && (!isset($_options['when']))) {
			if (isset($_options['select'])) { // On est dans le cas d'un son d'alarme envoyé depuis le widget
				$_options['sound']=urlencode($_options['select']);
				$_options['select']="";
			}
		}
		if ($_options['when'] == "") $_options['when'] = $defaultWhen;		
		if ($_options['message'] == "") $_options['message'] = $defaultText;	
		if ($_options['sound'] == "") $_options['sound'] = 'system_alerts_melodic_01';	
		$request = str_replace(array('#when#', '#message#', '#recurring#', '#sound#'), array(urlencode($_options['when']), urlencode($_options['message']), urlencode($_options['select']), $_options['sound']), $request);
		return $request;
	}
	
	private function build_ControlePosition($_options = array()) {
		$request = $this->getConfiguration('request');
		$request = str_replace('#position#', urlencode($_options['position']), $request);
		return $request;
	}

	private function buildDeleteAllAlarmsRequest($_options = array()) {
		$request = $this->getConfiguration('request');
		if ($_options['type'] == "") $_options['type'] = "alarm";
		if ($_options['status'] == "") $_options['status'] = "ON";
		return str_replace(array('#type#', '#status#'), array($_options['type'], $_options['status']), $request);
	}
	
	private function builddeleteReminderRequest($_options = array()) {
		$request = $this->getConfiguration('request');
		if ($_options['id'] == "") $_options['id'] = "coucou";
		if ($_options['status'] == "") $_options['status'] = "ON";
		return str_replace(array('#id#', '#status#'), array($_options['id'], $_options['status']), $request);
	}	
		
	private function buildRestartRequest($_options = array()) {
		log::add('alexaapi_debug', 'debug', '------buildRestartRequest---UTILISE QUAND ???--A simplifier--------------------------------------');
		$request = $this->getConfiguration('request')."?truc=vide";
		return str_replace('#volume#', $_options['slider'], $request);
	}
	
	public function getWidgetTemplateCode($_version = 'dashboard', $_noCustom = false) {
		if ($_version != 'scenario') return parent::getWidgetTemplateCode($_version, $_noCustom);
		list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);
		if ($command == 'speak' && strpos($arguments, '#volume#') !== false) 
			return getTemplate('core', 'scenario', 'cmd.speak.volume', 'alexaapi');
		if ($command == 'reminder') 
			return getTemplate('core', 'scenario', 'cmd.reminder', 'alexaapi');
		if ($command == 'deleteallalarms') 
			return getTemplate('core', 'scenario', 'cmd.deleteallalarms', 'alexaapi');
		if ($command == 'command' && strpos($arguments, '#select#')) 
			return getTemplate('core', 'scenario', 'cmd.command', 'alexaapi');
		if ($command == 'alarm') 
			return getTemplate('core', 'scenario', 'cmd.alarm', 'alexaapi');
		return parent::getWidgetTemplateCode($_version, $_noCustom);
	}
}
/*
	public static function getKnownDeviceType() {
		// récupéré de https://github.com/Apollon77/ioBroker.alexa2/blob/master/main.js
		$knownDeviceType = array(
			('A10A33FOX2NUBK') => array( (TypeEcho) => 'Echo Spot', (commandSupport) => 'true', (icon) => 'spot'),
			('A12GXV8XMS007S') => array( (TypeEcho) => 'FireTV', (commandSupport) => 'false', (icon) => 'firetv'), 
			('A15ERDAKK5HQQG') => array( (TypeEcho) => 'Sonos', (commandSupport) => 'false', (icon) => 'sonos'),
			('A17LGWINFBUTZZ') => array( (TypeEcho) => 'Anker Roav Viva Alexa', (commandSupport) => 'false', (icon) => 'other'),
			('A18O6U1UQFJ0XK') => array( (TypeEcho) => 'Echo Plus 2.Gen', (commandSupport) => 'true', (icon) => 'echo_plus2'), 
			('A1DL2DVDQVK3Q') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
			('A1H0CMF1XM0ZP4') => array( (TypeEcho) => 'Echo Dot/Bose', (commandSupport) => 'false', (icon) => 'other'), 
			('A1J16TEDOYCZTN') => array( (TypeEcho) => 'Fire tab', (commandSupport) => 'true', (icon) => 'firetab'),
			('A1NL4BVLQ4L3N3') => array( (TypeEcho) => 'Echo Show', (commandSupport) => 'true', (icon) => 'echo_show'), 
			('A1RTAM01W29CUP') => array( (TypeEcho) => 'Windows App', (commandSupport) => 'false', (icon) => 'other'), 
			('A1X7HJX9QL16M5') => array( (TypeEcho) => 'Bespoken.io', (commandSupport) => 'false', (icon) => 'other'),
			('A21Z3CGI8UIP0F') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
			('A2825NDLA7WDZV') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
			('A2E0SNTXJVT7WK') => array( (TypeEcho) => 'Fire TV V1', (commandSupport) => 'false', (icon) => 'firetv'),
			('A2GFL5ZMWNE0PX') => array( (TypeEcho) => 'Fire TV', (commandSupport) => 'true', (icon) => 'firetv'), 
			('A2IVLV5VM2W81') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
			('A2L8KG0CT86ADW') => array( (TypeEcho) => 'RaspPi', (commandSupport) => 'false', (icon) => 'other'), 
			('A2LWARUGJLBYEW') => array( (TypeEcho) => 'Fire TV Stick V2', (commandSupport) => 'false', (icon) => 'firetv'), 
			('A2M35JJZWCQOMZ') => array( (TypeEcho) => 'Echo Plus', (commandSupport) => 'true', (icon) => 'echo'), 
			('A2M4YX06LWP8WI') => array( (TypeEcho) => 'Fire Tab', (commandSupport) => 'false', (icon) => 'firetab'), 
			('A2OSP3UA4VC85F') => array( (TypeEcho) => 'Sonos', (commandSupport) => 'true', (icon) => 'sonos'), 
			('A2T0P32DY3F7VB') => array( (TypeEcho) => 'echosim.io', (commandSupport) => 'false', (icon) => 'other'),
			('A2TF17PFR55MTB') => array( (TypeEcho) => 'Apps', (commandSupport) => 'false', (icon) => 'other'), 
			('A32DOYMUN6DTXA') => array( (TypeEcho) => 'Echo Dot 3.Gen', (commandSupport) => 'true', (icon) => 'echo_dot3'),
			('A37SHHQ3NUL7B5') => array( (TypeEcho) => 'Bose Homespeaker', (commandSupport) => 'false', (icon) => 'other'), 
			('A38BPK7OW001EX') => array( (TypeEcho) => 'Raspberry Alexa', (commandSupport) => 'false', (icon) => 'raspi'), 
			('A38EHHIB10L47V') => array( (TypeEcho) => 'Echo Dot', (commandSupport) => 'true', (icon) => 'echo_dot'), 
			('A3C9PE6TNYLTCH') => array( (TypeEcho) => 'Multiroom', (commandSupport) => 'true', (icon) => 'multiroom'), 
			('A3H674413M2EKB') => array( (TypeEcho) => 'echosim.io', (commandSupport) => 'false', (icon) => 'other'),
			('A3HF4YRA2L7XGC') => array( (TypeEcho) => 'Fire TV Cube', (commandSupport) => 'true', (icon) => 'other'), 
			('A3NPD82ABCPIDP') => array( (TypeEcho) => 'Sonos Beam', (commandSupport) => 'true', (icon) => 'sonos'), 
			('A3R9S4ZZECZ6YL') => array( (TypeEcho) => 'Fire Tab HD 10', (commandSupport) => 'true', (icon) => 'firetab'), 
			('A3S5BH2HU6VAYF') => array( (TypeEcho) => 'Echo Dot 2.Gen', (commandSupport) => 'true', (icon) => 'echo_dot'), 
			('A3SSG6GR8UU7SN') => array( (TypeEcho) => 'Echo Sub', (commandSupport) => 'true', (icon) => 'echo_sub'), 
			('A7WXQPH584YP') => array( (TypeEcho) => 'Echo 2.Gen', (commandSupport) => 'true', (icon) => 'echo2'), 
			('AB72C64C86AW2') => array( (TypeEcho) => 'Echo', (commandSupport) => 'true', (icon) => 'echo'), 
			('ADVBD696BHNV5') => array( (TypeEcho) => 'Fire TV Stick V1', (commandSupport) => 'false', (icon) => 'firetv'), 
			('AILBSA2LNTOYL') => array( (TypeEcho) => 'reverb App', (commandSupport) => 'false', (icon) => 'reverb'),
			('AVE5HX13UR5NO') => array( (TypeEcho) => 'Logitech Zero Touch', (commandSupport) => 'false', (icon) => 'other'), 
			('AWZZ5CVHX2CD') => array( (TypeEcho) => 'Echo Show 2.Gen', (commandSupport) => 'true', (icon) => 'echo_show2')
		);
		return $knownDeviceType;
	}
*/
