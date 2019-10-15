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
 * GNU General  Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/
/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class alexaapi extends eqLogic {
	/*     * ***********************Methode static*************************** */

	public static function getKnownDeviceType() {
		//---------------------------------------------------------------------------------------
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

	public static function callProxyAlexaapi($_url) {
		//if (strpos($_url, '?') !== false) {
		$url = 'http://' . config::byKey('internalAddr') . ':3456/' . trim($_url, '/') . '&apikey=' . jeedom::getApiKey('openzwave');
		//} else {
		//	$url = 'http://127.0.0.1:' . config::byKey('port_server', 'openzwave', 8083) . '/' . trim($_url, '/') . '?apikey=' . jeedom::getApiKey('openzwave');
		//}
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

	//*********** Demon ***************
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'alexaapi_node';
		$return['state'] = 'nok'; // bien ecrire en municules
		// Regarder si alexaapi.js est lancé
		$pid = trim(shell_exec('ps ax | grep "alexaapi/resources/alexaapi.js" | grep -v "grep" | wc -l'));
		if ($pid != '' && $pid != '0') $return['state'] = 'ok';

		// Regarder si le cookie existe :alexa-cookie.json
		$request = realpath(dirname(__FILE__) . '/../../resources/data/alexa-cookie.json');
		if (file_exists($request)) {
			$return['launchable'] = 'ok';
		}
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

		// Ferme le serveur de cookie sur port 3457 s'il est lancé (supprimé se ferme à la fin de l'identification)
		/*$pid = trim(shell_exec('ps ax | grep "/initCookie.js" | grep -v "grep" | wc -l'));
		      if ($pid != '' && $pid != '0')
		{
		$cmd = 'kill $(ps aux | grep "/initCookie.js" | awk \'{print $2}\')';
		      log::add('alexaapi', 'debug', 'Fermeture serveur Cookie : ' . $cmd);
		      $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_node') . ' 2>&1 &');
		}
		*/
		   // $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/Alexa-Remote-http/index.js ' . config::byKey('internalAddr') . ' ' . $url . ' ' . $log;
		$cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexaapi.js ' . network::getNetworkAccess('internal') . ' ' . config::byKey('amazonserver', 'alexaapi', 'amazon.fr') . ' ' . config::byKey('alexaserver', 'alexaapi', 'alexa.amazon.fr').' '.jeedom::getApiKey('alexaapi');
//network::getNetworkAccess('internal') . '/plugins/blea/core/php/jeeBlea.php';

		log::add('alexaapi', 'debug', 'Lancement démon alexaapi : ' . $cmd);

		$result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_node') . ' 2>&1 &');
		//$cmdStart='nohup ' . $cmd . ' | tee >(grep "WS-MQTT">>'.log::getPathToLog('alexaapi_mqtt').') >(grep -v "WS-MQTT">>'. log::getPathToLog('alexaapi_node') . ')';
		//log::add('alexaapi','debug','cmd executee : '.$cmdStart);
		//$result = exec($cmdStart);
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
	// Reinstall NODEJS from scratch (to use if there is errors in dependancy install)
	public static function reinstallNodeJS() {
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
	//*********** Demon Cookie***************
	public static function deamonCookie_start($_debug = false) {
		self::deamonCookie_stop();
		$deamon_info = self::deamon_info();

		log::add('alexaapi_cookie', 'info', 'Lancement du démon cookie');
		$log = $_debug ? '1' : '0';

		$sensor_path = realpath(dirname(__FILE__) . '/../../resources');
		//Par sécurité, on Kill un éventuel précédent proessus initCookie.js
		$cmd = "kill $(ps aux | grep 'initCookie.js' | awk '{print $2}')";
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

	//************Dépendances ***********
	public static function dependancy_info() {
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
	
	log::add('alexaapi', 'debug', '---------------------------------------------supprimer tous les devices-----------------------');

	}
		
		
	/*   public static function VerifiePresenceCookie()
	   {
	           //return true;
	       $return = array();
	       $request = realpath(dirname(__FILE__) . '/../../resources/node_modules');
	       $return['state'] = is_dir($request) ? 'ok' : 'nok';
	       return $return;
	   }
	*/
	//public static function cron15($_eqlogic_id = null) {

	public static function cron($_eqlogic_id = null) {		
		$autorefresh = '*/15 * * * *';
		//$autorefresh = '* * * * *';
		
		$d = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
		$deamon_info = self::deamon_info();
		
		if ($d->isDue() && $deamon_info['state'] == 'ok') {
			log::add('alexaapi', 'debug', '---------------------------------------------DEBUT CRON-'.$autorefresh.'-----------------------');

			
			#Update all status
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
			}
		}
		
		
		
		/* boucle Test checkAuth ==> basculé dans le cron des routines dessous*/
		$autorefreshC = '*/6 * * * *'; //caractère / supprimé

		$c = new Cron\CronExpression($autorefreshC, new Cron\FieldFactory);
		if ($c->isDue() && $deamon_info['state'] == 'ok') {
		self::checkAuth();		
		}	
			
		/* boucle qui relance la connexion au serveur*/
		$autorefreshRR = config::byKey('autorefresh', 'alexaapi', '33 3 * * *');

		$c = new Cron\CronExpression($autorefreshRR, new Cron\FieldFactory);
		if ($c->isDue() && $deamon_info['state'] == 'ok') {
		self::restartServeurPHP();		
		}
		
		
		

		// boucle refresh
		$autorefreshR = '*/15 * * * *';

		$r = new Cron\CronExpression($autorefreshR, new Cron\FieldFactory);
		if ($r->isDue() && $deamon_info['state'] == 'ok') {
			$eqLogics = ($_eqlogic_id !== null) ? array(eqLogic::byId($_eqlogic_id)) : eqLogic::byType('alexaapi', true);

			$premierdelaboucle=true; // premierdelaboucle cest pour ne pas lancer autant de fois le test sur routines que de devices, ne sera lancé qu'une fois.
			foreach ($eqLogics as $alexaapi) {
				$alexaapi->refresh($premierdelaboucle); 				
				if ($premierdelaboucle) $premierdelaboucle=false;
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




	public static function scanAmazonAlexa($_logical_id = null, $_exclusion = 0) {

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
			if (in_array("AUDIO_PLAYER",$item['capabilities'])) {
				
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
					$device->setStatus('online', (($item['online'])?true:false));
					$device->save();
					
			
					// Device PLAYER
					$device = alexaapi::byLogicalId($item['serial']."_player", 'alexaapi');
						if (!is_object($device)) {
							$device = self::createNewDevice($item['name']." Player", $item['serial']."_player");
							$numNewDevices++;
						}
					// Update device configuration
					$device->setConfiguration('device', $item['name']);
					$device->setConfiguration('type', $item['type']);
					$device->setConfiguration('devicetype', "Player");
					$device->setConfiguration('family', $item['family']);
					$device->setConfiguration('widgetPlayListEnable', 0);
					$device->setConfiguration('members', $item['members']);
					$device->setConfiguration('capabilities', $item['capabilities']);
					$device->setStatus('online', (($item['online'])?true:false));
					$device->save();
					$numDevices++;
			}



			// Retireve the device (if already registered in Jeedom)
			$device = alexaapi::byLogicalId($item['serial'], 'alexaapi');
			if (!is_object($device)) {
				$device = self::createNewDevice($item['name'], $item['serial']);
				$numNewDevices++;
			}

			// Update device configuration
			$device->setConfiguration('device', $item['name']);
			$device->setConfiguration('type', $item['type']);
			$device->setConfiguration('devicetype', "Echo");
			$device->setConfiguration('family', $item['family']);
			$device->setConfiguration('members', $item['members']);
			$device->setConfiguration('capabilities', $item['capabilities']);
			$device->setStatus('online', (($item['online'])?true:false));
			$device->save();


			$numDevices++;
		}
		
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
			
			

	//log::add('alexaapi', 'debug', '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>'.json_encode($item['providerData']).'<<<<<<<<<<<<<<<<<<<');
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
			$device->setStatus('online', 'true');
			$device->save();

			$numDevices++;
		}






		
		// A voir s'il faut ou pas actualiser les routines ici apres le scan (cela se fera au premier refresh)

		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan terminé. ' . $numDevices . ' équipements mis a jour dont ' . $numNewDevices . ' ajouté(s)', __FILE__)));

	}

	private static function createNewDevice($deviceName, $deviceSerial) {

		$defaultRoom = intval(config::byKey('defaultParentObject','alexaapi','',true));
		log::add('alexaapi', 'debug', '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>0>>>>>>>>>>>>>>>>>>>>>>>>>>defaultRoom:'.$defaultRoom);

		$newDevice = new alexaapi();
		$newDevice->setName($deviceName);
		$newDevice->setLogicalId($deviceSerial);
		$newDevice->setEqType_name('alexaapi');
		$newDevice->setIsVisible(1);
		if($defaultRoom) $newDevice->setObject_id($defaultRoom);
		$newDevice->setConfiguration('device', $deviceName);
		$newDevice->setConfiguration('serial', $deviceSerial);
		$newDevice->setIsEnable(1);
		$newDevice->save();

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
	//	log::add('alexaapi', 'debug', 'capabilitiesarray : '.$thisCapa.'/'.json_encode($capa));


		//log::add('alexaapi', 'debug', 'capabilities : '.json_encode($capa));
//		if(((gettype($capa) == "array" && array_search($thisCapa,$capa))) || ((gettype($capa) == "string" && strpos($capa, $thisCapa) !== false))) {
		
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


	/*     * *********************Methode d'instance************************* */
	public function refresh($_routines=true) { //$_routines c'est pour éviter de charger les routines lors du scan
	//log::add('alexaapi', 'debug', '-----Lancement refresh1---**-----');
		$deamon_info = alexaapi::deamon_info();
		if ($deamon_info['state'] != 'ok') return false;
	//log::add('alexaapi', 'debug', '-----Lancement refresh2---*'.$this->getName().'*-----');

//

//log::add('alexaapi', 'debug', '-----Lancement refresh2---*'.$this->getConfiguration('devicetype').'*-----');
$device=str_replace("_player", "", $this->getConfiguration('serial'));

// Refresh d'un player
if ($this->getConfiguration('devicetype') == "Player") {
	$_routines=false;
	//$envoicommandeHTTP=file_get_contents(network::getNetworkAccess('internal') . "/plugins/alexaapi/core/php/jeeAlexaapi.php?apikey=".jeedom::getApiKey('alexaapi')."&nom=refreshPlayer");
	
// Send JSON data via POST with PHP cURL
$url = network::getNetworkAccess('internal') . "/plugins/alexaapi/core/php/jeeAlexaapi.php?apikey=".jeedom::getApiKey('alexaapi')."&nom=refreshPlayer";

//create a new cURL resource
$ch = curl_init($url);

//setup request to send json via POST
$data = array(
    'deviceSerialNumber' => $device,
    'audioPlayerState' => 'REFRESH'
);
//$payload = json_encode(array("user" => $data));
$payload = json_encode($data);

//attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

//set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute the POST request
$result = curl_exec($ch);

//close cURL resource
curl_close($ch);

//TEST
$_playlists=true;
	
	
}



	if ($_playlists)
	{

//log::add('alexaapi', 'debug', 'execute : refresh'."http://" . config::byKey('internalAddr') . ":3456/playlists?device=".$device);
			// Met à jour la liste des routines des commandes action "routine"
			$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/playlists?device=".$device);
			$json = json_decode($json, true);
			//self::sortBy('utterance', $json, 'asc');

			$ListeDesRoutines = [];
		//log::add('alexaapi', 'debug', '-->'.json_encode($json));


        foreach ($json as $key => $value) {
			foreach ($value as $key2 => $playlist) {
				foreach ($playlist as $key3 => $value2) {
				//log::add('alexaapi', 'debug', '-----------------v:'.$value2);
				//log::add('alexaapi', 'debug', '-----------------playlistId:'.$value2['playlistId']);
				//log::add('alexaapi', 'debug', '-----------------title:'.$value2['title']);
				//log::add('alexaapi', 'debug', '-----------------trackCount:'.$value2['trackCount']);
				$ListeDesPlaylists[]= $value2['playlistId'] . '|' . $value2['title']." (".$value2['trackCount'].")";
				}	
			}
		}		
				
				/*
				//if ($playlists['playlists'] != '') {
		log::add('alexaapi', 'debug', '-----------------playlists:'.json_encode($playlists['playlists']));
//					$ListeDesRoutines[]= $item['playlist'][0] . '|' . $item['playlistId'];
//§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§
			foreach ($playlists as $item) {
				//if ($item["Coralie"] != '') {
		log::add('alexaapi', 'debug', '-----------------playlist:');//.json_encode($item["Coralie"]));
//					$ListeDesRoutines[]= $item['playlist'][0] . '|' . $item['playlistId'];
				//}
			}


				}
			}*/		
			$cmd = $this->getCmd(null, 'playList');
			if (is_object($cmd)) {
				//log::add('alexaapi', 'debug', '----------------->Enregistrement PlayLists');
				//routine existe on  met à jour la liste des routines
				$cmd->setConfiguration('listValue', join(';',$ListeDesPlaylists));
				$cmd->save();
				log::add('alexaapi', 'debug', 'Mise à jour de la liste des Playlists de '.$this->getName());
			}
			//else
			//log::add('alexaapi', 'debug', '----------------->Cmd inconnue PlayLists');
		
			//log::add('alexaapi', 'debug', '----------------->name'.$this->getName());
				
			// Fin mise à jour de la liste des routines
			
	}



	if ($_routines)
	{

			//	log::add('alexaapi', 'debug', 'execute : refresh');
			// Met à jour la liste des routines des commandes action "routine"
			$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/routines");
			$json = json_decode($json, true);
			self::sortBy('utterance', $json, 'asc');

			$ListeDesRoutines = [];
		//log::add('alexaapi', 'debug', '---------------------------------------------Lancement refresh3------------------------');

			foreach ($json as $item) {
				if ($item['utterance'] != '') {
					$ListeDesRoutines[]= $item['creationTimeEpochMillis'] . '|' . $item['utterance'];
				}
				else {
					if ($item['triggerTime'] != '') $resultattriggerTime = substr($item['triggerTime'], 0, 2) . ":" . substr($item['triggerTime'], 2, 2);
					$ListeDesRoutines[]= $item['creationTimeEpochMillis'] . '|' . $resultattriggerTime;
				}
			}
			$cmd = $this->getCmd(null, 'routine');
			if (is_object($cmd)) {
				//routine existe on  met à jour la liste des routines
				$cmd->setConfiguration('listValue', join(';',$ListeDesRoutines));
				$cmd->save();
			}
			// Fin mise à jour de la liste des routines
			
	}
		
		try {
			foreach ($this->getCmd('action') as $cmd) {
				//log::add('alexaapi', 'debug', 'Refresh: Test '.$cmd->getName()."/".$cmd->getConfiguration('RunWhenRefresh', 0));
				if ($cmd->getConfiguration('RunWhenRefresh', 0) != '1') {
					continue; // si le lancement n'est pas prévu, ça va au bout de la boucle foreach
				}
				//log::add('alexaapi', 'debug', 'Refresh: Execute '.$cmd->getName());
				$value = $cmd->execute();
				//if ($cmd->execCmd() != $cmd->formatValue($value)) { ???????
				//	$cmd->event($value);
				//}
				
			}
		}
		catch(Exception $exc) {
			log::add('alexaapi', 'error', __('Erreur pour ', __FILE__) . $this->getHumanName() . ' : ' . $exc->getMessage());
		}
		//log::add('alexaapi', 'debug', 'execute (fini) : refresh');
		
			
	}
		
	public function test2060() {
		$deamon_info = alexaapi::deamon_info();
		if ($deamon_info['state'] != 'ok') {
			log::add('alexaapi', 'debug', '-----------------------------Demon non OK, Test annulé------------------------');
			return 0;
		}
		
	//log::add('alexaapi', 'debug', '---------------------------------------------Lancement test2060Phase1------------------------');
	//log::add('alexaapi', 'debug', '------------------------------test2060 *'.$this->getName().'*---------------------');
		
		
		// Rustine d'anti-connexion close Partie 1/2
		// On va aller ajouter un rappel en 2060 et on va aller vérifier si elle a bien été ajoutée.
		
		$cmd = $this->getCmd(null, 'reminder');
		if (is_object($cmd)) {
			// Nous sommes sur un équipement qui a la function reminder, sinon on ne fait pas le test du rappel en 2060
			$options['when']="2060-12-31 23:59:00";
			$options['text']="test Alexa-api";
			$value = $cmd->execute($options);
			//log::add('alexaapi', 'debug', '---------------------------------------------Lancement refresh2------------------------');

			// Rustine d'anti-connexion close Partie 2/2
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
					//break; 
				}
			}
			
			if ($trouveReminder) {
				// C'est bon, on a  trouvé le rappel de 2060, on le supprime et tout va bien
				log::add('alexaapi', 'debug', '********************** TROUVE le Reminder 2060 donc c\'est OK**********************************');
				//$options['node_id']=$idReminderaSupprimer;
				//log::add('alexaapi', 'debug', '**********************Suppression Reminder id**'.$idReminderaSupprimer.'*********************************');
				//echo '<script>startServer55();</script>';

				//log::add('alexaapi', 'debug', '********************** AVANT RESTART***********************************');


				//	log::add('alexaapi', 'debug', '********************** APRES RESTART***********************************');
				return true ;
			}
			else {
				log::add('alexaapi', 'debug', '**********************PAS TROUVE**'.$cmd->getName().'*********************************');
				return false;
			}
		}		
	}

	public function postSave() {


				//log::add('alexaapi', 'debug', '**********************postSave DEBUT***********************************');



		/*       if ($this->getName() == 'Tous les appareils')
			  {
				  return;
		}*/


		// On va chercher le contenu de "capabilities" qui donne les capacité du device, on va donc créer les commandes en fonction de ses capacités
		// Pour une raison inconnue, certains utilisateurs se retrouvent avec des "capabilites" vides, dans ce cas, on créera toutes les commandes
		//  http://sigalou-domotique.fr/images/Sigalou/capabilites.jpg
		$capa=$this->getConfiguration('capabilities','');
		$type=$this->getConfiguration('type','');
		if(!empty($capa)) {
			// Pas trouvé le capabilities qui correspond au PUSH
			if (strstr($this->getName(), "Alexa Apps")) {
				// Push command
				$cmd = $this->getCmd(null, 'push');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('push');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Push');
					$cmd->setConfiguration('request', 'push?text=#message#');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setIsVisible(1);
				}
				$cmd->save();
				return;
			}


			//if((array_search("AUDIO_PLAYER",$capa)) || (empty($capa))) { // empty($capa) est utilisé car chez certains utilisateurs capabilities ne remonte pas
			
		
			if (($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && ($this->getConfiguration('devicetype') == "Player")) { 

				

				// SmartHome

			if ($this->hasCapaorFamilyorType("turnOff")) { 
				$cmd = $this->getCmd(null, 'turnOff');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('turnOff');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('turnOff');
					$cmd->setConfiguration('request', 'SmarthomeCommand?command=turnOff');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setDisplay('icon', '<i class="fa jeedomapp-audiospeak"></i>');
					$cmd->setIsVisible(1);
				}
				$cmd->save();
			} else {
					$cmd = $this->getCmd(null, 'turnOff');
					if (is_object($cmd)) {
						$cmd->remove();
					}
				}
				
			if ($this->hasCapaorFamilyorType("turnOn")) { 
				$cmd = $this->getCmd(null, 'turnOn');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('turnOn');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('turnOn');
					$cmd->setConfiguration('request', 'SmarthomeCommand?command=turnOn');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setDisplay('icon', '<i class="fa jeedomapp-audiospeak"></i>');
					$cmd->setIsVisible(1);
				}
				$cmd->save();
			} else {
					$cmd = $this->getCmd(null, 'turnOn');
					if (is_object($cmd)) {
						$cmd->remove();
					}
				}			
}
			//if((array_search("AUDIO_PLAYER",$capa)) || (empty($capa))) { // empty($capa) est utilisé car chez certains utilisateurs capabilities ne remonte pas
			if (($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && ($this->getConfiguration('devicetype') == "Player")) { 

		
				// Radio command
				$cmd = $this->getCmd(null, 'radio');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('radio');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Radio');
					$cmd->setConfiguration('request', 'radio?station=#station#');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setIsVisible(0);
					$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
				}
				$cmd->save();
				
				
				// Command command
				$cmd = $this->getCmd(null, 'command');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('command');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Command');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=#command#');
					$cmd->setDisplay('icon', '<i class="fa fa-play-circle"></i>');
					$cmd->setIsVisible(0);
				}
				$cmd->save();
			} else {
				$cmd = $this->getCmd(null, 'radio');
				if (is_object($cmd)) $cmd->remove();
				
			
				$cmd = $this->getCmd(null, 'command');
				if (is_object($cmd)) $cmd->remove();
				
			}



			if (($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && ($this->getConfiguration('devicetype') != "Player")) { 
			
							// Speak command
				$cmd = $this->getCmd(null, 'speak');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('speak');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Speak');
					$cmd->setConfiguration('request', 'speak?text=#message#');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setDisplay('icon', '<i class="fa jeedomapp-audiospeak"></i>');
					$cmd->setIsVisible(1);
				}
				$cmd->save();
			} else {
					$cmd = $this->getCmd(null, 'speak');
					if (is_object($cmd)) {
						$cmd->remove();
					}
				}
			
			
			
			if (($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && ($this->getConfiguration('devicetype') == "Player")) { 
			
				// playlistName info
				$cmd = $this->getCmd(null, 'playlistName');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('playlistName');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('playlistName');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();	
				
				/*
				// songName info
				$cmd = $this->getCmd(null, 'songName');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('songName');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('songName');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();
*/

			
				// subText1 info
				$cmd = $this->getCmd(null, 'subText1');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('subText1');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('subText1');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();
				
					// subText2 info
				$cmd = $this->getCmd(null, 'subText2');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('subText2');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('subText2');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();		

				
					// title info
				$cmd = $this->getCmd(null, 'title');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('title');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('title');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();			
				
					// url info
				$cmd = $this->getCmd(null, 'url');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('url');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('url');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();		

					// mediaLength info
				$cmd = $this->getCmd(null, 'mediaLength');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('mediaLength');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('mediaLength');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();

					// mediaProgress info
				$cmd = $this->getCmd(null, 'mediaProgress');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('mediaProgress');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('mediaProgress');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();

					// state info
				$cmd = $this->getCmd(null, 'state');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('state');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('state');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();


				
					// playlistName info récupéré en MQTT (playlist)
				$cmd = $this->getCmd(null, 'playlistName');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('playlistName');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('playlistName');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();
				
					// nextstate 
				$cmd = $this->getCmd(null, 'nextState');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('nextState');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('nextState');
					$cmd->setIsVisible(0);
					$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();

					// nextstate 
				$cmd = $this->getCmd(null, 'previousState');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('previousState');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('previousState');
					$cmd->setIsVisible(0);
					$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();

					// nextstate 
				$cmd = $this->getCmd(null, 'playPauseState');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('playPauseState');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('playPauseState');
					$cmd->setIsVisible(0);
					$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();



				
				// Playlists
				$cmd = $this->getCmd(null, 'playList');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('playList');
					$cmd->setSubType('select');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Play List');
					$cmd->setConfiguration('request', 'playlist?playlist=#select#');
					$cmd->setConfiguration('listValue', 'Lancer Refresh|Lancer Refresh');
					//$cmd->setDisplay('title_disable', 1);
					$cmd->setIsVisible(1);
					$cmd->setDisplay('icon', '<i class="divers-viral"></i>');
				}
				$cmd->save();
				
				
				// playMusicTrack 
				$cmd = $this->getCmd(null, 'playMusicTrack');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('playMusicTrack');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Play Music Track');
					$cmd->setConfiguration('request', 'playmusictrack?trackId=#trackid#');
					//$cmd->setDisplay('title_disable', 1);
					$cmd->setIsVisible(0);
					$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
				}
				$cmd->save();				

/*
				// RWD
				$cmd = $this->getCmd(null, 'rwd');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('rwd');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Rwd');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=rwd');
					$cmd->setDisplay('icon', '<i class="fa fa-fast-backward"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(15);
				}
				$cmd->save();	
				*/
				// PREVIOUS
				$cmd = $this->getCmd(null, 'previous');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('previous');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Previous');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=previous');
					$cmd->setDisplay('icon', '<i class="fa fa-step-backward"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(16);
				}
				$cmd->save();
				
				// PAUSE
				$cmd = $this->getCmd(null, 'pause');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('pause');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Pause');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=pause');
					$cmd->setDisplay('icon', '<i class="fa fa-pause"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(17);
				}
				$cmd->save();	
				
				// PLAY
				$cmd = $this->getCmd(null, 'play');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('play');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Play');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=play');
					$cmd->setDisplay('icon', '<i class="fa fa-play"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(18);
				}
				$cmd->save();				
							
				// NEXT
				$cmd = $this->getCmd(null, 'next');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('next');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Next');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=next');
					$cmd->setDisplay('icon', '<i class="fa fa-step-forward"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(19);
				}
				$cmd->save();
				
				/*// FWD
				$cmd = $this->getCmd(null, 'fwd');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('fwd');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Fwd');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=fwd');
					$cmd->setDisplay('icon', '<i class="fa fa-fast-forward"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(20);
				}
				$cmd->save();			
					// §§§§§ ON REPRENDRA PLUS TARD REPEAT ET SHUFFLE CAR L ENVOI NECESSITE UNE OPTION ON OU OFF CONTRAIREMENT A PAUSE OU NEXT		
				// REPEAT
				$cmd = $this->getCmd(null, 'repeat');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('repeat');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Repeat');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=repeat');
					$cmd->setDisplay('icon', '<i class="fa fa-refresh"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(25);
				}
				$cmd->save();
				
				// loopMode info récupéré en MQTT
				$cmd = $this->getCmd(null, 'loopMode');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('loopMode');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('loopMode');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();
				
				// SHUFFLE
				$cmd = $this->getCmd(null, 'shuffle');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('shuffle');
					$cmd->setSubType('other');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Shuffle');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setConfiguration('request', 'command?command=shuffle');
					$cmd->setDisplay('icon', '<i class="fa fa-random"></i>');
					$cmd->setIsVisible(1);
					$cmd->setOrder(26);
				}
				$cmd->save();	
				
				// playBackOrder info récupéré en MQTT
				$cmd = $this->getCmd(null, 'playBackOrder');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('playBackOrder');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('playBackOrder');
					$cmd->setIsVisible(1);
					//$cmd->setOrder(79);
					//$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
					//$cmd->setDisplay('title_disable', 1);
				}
				$cmd->save();


				
			*/	
			
		
				} 

			
			

			if (($this->hasCapaorFamilyorType("TIMERS_AND_ALARMS")) && !($this->getConfiguration('devicetype') == "Player")) { 
			
				// alarm command
				$cmd = $this->getCmd(null, 'alarm');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('alarm');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Alarm');
					$cmd->setConfiguration('request', 'alarm?when=#when#&recurring=#recurring#');
					$cmd->setDisplay('title_disable', 1);
					$cmd->setDisplay('icon', '<i class="fa fa-bell"></i>');
					$cmd->setIsVisible(0);
				}
				$cmd->save();

				// delete all alarms command
				$cmd = $this->getCmd(null, 'deleteallalarms');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('deleteallalarms');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Delete All Alarms');
					$cmd->setConfiguration('request', 'deleteallalarms?type=#type#&status=#status#');
					$cmd->setIsVisible(0);
					$cmd->setDisplay('icon', '<i class="maison-poubelle"></i>');
				}
				$cmd->save();
				
				// whennextalarm command
				$cmd = $this->getCmd(null, 'whennextalarm');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setIsVisible(0);
					$cmd->setLogicalId('whennextalarm');
					$cmd->setSubType('other');
					$cmd->setConfiguration('infoName', 'Next Alarm Hour');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Next Alarm When');
					$cmd->setOrder(200);
					$cmd->setDisplay('icon', '<i class="fa fa-bell"></i>');
					$cmd->setConfiguration('RunWhenRefresh', 1);
					$cmd->setConfiguration('request', 'whennextalarm?position=1');
				}
				$cmd->save();
				
				// whennextmusicalalarm command
				$cmd = $this->getCmd(null, 'whennextmusicalalarm');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setIsVisible(0);
					$cmd->setLogicalId('whennextmusicalalarm');
					$cmd->setSubType('other');
					$cmd->setConfiguration('infoName', 'Next Musical Alarm Hour');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setOrder(205);
					$cmd->setName('Next Musical Alarm When');
					$cmd->setDisplay('icon', '<i class="fa fa-bell"></i>');
					$cmd->setConfiguration('RunWhenRefresh', 1);
					$cmd->setConfiguration('request', 'whennextmusicalalarm?position=1');
				}
				$cmd->save();
				
			} else {
				$cmd = $this->getCmd(null, 'alarm');
				if (is_object($cmd)) {
					$cmd->remove();
				}
				$cmd = $this->getCmd(null, 'deleteallalarms');
				if (is_object($cmd)) {
					$cmd->remove();
				}
				$cmd = $this->getCmd(null, 'whennextalarm');
				if (is_object($cmd)) {
					$cmd->remove();
				}
				$cmd = $this->getCmd(null, 'whennextmusicalalarm');
				if (is_object($cmd)) {
					$cmd->remove();
				}
			}

			if($type == "A15ERDAKK5HQQG") {
				log::add('alexaapi', 'warning', '****Rencontre du type A15ERDAKK5HQQG = Sonos Première Génération sur : '.$this->getName());
				log::add('alexaapi', 'warning', '****On ne crée pas les commandes REMINDERS dessus car bug!');
			}
			if (($this->hasCapaorFamilyorType("REMINDERS")) && !($this->getConfiguration('devicetype') == "Player")) { 
				// delete reminder
				$cmd = $this->getCmd(null, 'deleteReminder');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('deleteReminder');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Delete Reminder');
					$cmd->setConfiguration('request', 'deleteReminder?id=#id#');
					$cmd->setIsVisible(0);
					$cmd->setDisplay('icon', '<i class="maison-poubelle"></i>');
				}
				$cmd->save();
				
				// whennextreminder command
				
				$cmd = $this->getCmd(null, 'whennextreminder');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('whennextreminder');
					$cmd->setIsVisible(0);
					$cmd->setSubType('other');
					$cmd->setConfiguration('infoName', 'Next Reminder Hour');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Next Reminder When');
					$cmd->setConfiguration('RunWhenRefresh', 1);
					$cmd->setOrder(210);
					$cmd->setDisplay('icon', '<i class="fa divers-circular114"></i>');
					$cmd->setConfiguration('request', 'whennextreminder?position=1');
				}
				$cmd->save();

				// Reminder command
				$cmd = $this->getCmd(null, 'reminder');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('reminder');
					$cmd->setSubType('message');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Reminder');
					$cmd->setConfiguration('request', 'reminder?text=#text#&when=#when#');
					$cmd->setDisplay('icon', '<i class="fa divers-circular114"></i>');
					$cmd->setIsVisible(0);
				}
				$cmd->save();
				
				// Routine command (lié à Reminder car pas de capability pour Routine)
				$cmd = $this->getCmd(null, 'routine');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('routine');
					$cmd->setSubType('select');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Routine');
					$cmd->setConfiguration('request', 'routine?routine=#select#');
					$cmd->setConfiguration('listValue', 'Lancer Refresh|Lancer Refresh');
					//$cmd->setDisplay('title_disable', 1);
					$cmd->setIsVisible(0);
					$cmd->setDisplay('icon', '<i class="divers-viral"></i>');
				}
				$cmd->save();
				
				
				

			} else {
				$cmd = $this->getCmd(null, 'deleteReminder');
				if (is_object($cmd)) {
					$cmd->remove();
				}
				$cmd = $this->getCmd(null, 'whennextreminder');
				if (is_object($cmd)) {
					$cmd->remove();
				}
				$cmd = $this->getCmd(null, 'reminder');
				if (is_object($cmd)) {
					$cmd->remove();
				}
				$cmd = $this->getCmd(null, 'routine');
				if (is_object($cmd)) {
					$cmd->remove();
				}
			}


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
				
			} else {
				$cmd = $this->getCmd(null, 'refresh');
				if (is_object($cmd)) {
					$cmd->remove();
				}
			}

			if (($this->hasCapaorFamilyorType("VOLUME_SETTING")) && (!$this->hasCapaorFamilyorType("WHA"))) { 


				// Volume command
				$vol = $this->getCmd(null, 'volumeinfo');
				if (!is_object($vol)) {
					$vol = new alexaapiCmd();
					$vol->setType('info');
					$vol->setLogicalId('volumeinfo');
					$vol->setSubType('string');
					$vol->setEqLogic_id($this->getId());
					$vol->setName('Volume Info');
					$vol->setConfiguration('minValue', '0');
					$vol->setConfiguration('maxValue', '100');
					$vol->setIsVisible(1);
					$vol->setOrder(79);
					$vol->setDisplay('icon', '<i class="fa fa-volume-up"></i>');
					$vol->setDisplay('forceReturnLineBefore', true);
				}
				$vol->save();
			} else {
				$vol = $this->getCmd(null, 'volumeinfo');
				if (is_object($vol)) {
					$vol->remove();
				}
			}			
			
			
			if ($this->hasCapaorFamilyorType("VOLUME_SETTING")) { 

				// Volume command
				$cmd = $this->getCmd(null, 'volume');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setLogicalId('volume');
					$cmd->setSubType('slider');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Volume');
					$cmd->setConfiguration('request', 'volume?value=#volume#');
					$cmd->setConfiguration('minValue', '0');
					$cmd->setConfiguration('maxValue', '100');
					$cmd->setIsVisible(1);
					$cmd->setOrder(80);
					$cmd->setDisplay('title_disable', true);
					$cmd->setDisplay('icon', '<i class="fa fa-volume-up"></i>');
				}
				if(is_object($vol)) {
					$cmd->setValue($vol->getId());
				}
				$cmd->save();
			} else {
				$cmd = $this->getCmd(null, 'volume');
				if (is_object($cmd)) {
					$cmd->remove();
				}
			}
			
			
			
			
			
			if ((!$this->hasCapaorFamilyorType("WHA")) && (!$this->hasCapaorFamilyorType("FIRE_TV")) && (!$this->hasCapaorFamilyorType("AMAZONMOBILEMUSIC_ANDROID"))) { 
				// Dernière Intéraction
				$cmd = $this->getCmd(null, 'interactioninfo');
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId('interactioninfo');
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($this->getId());
					$cmd->setName('Last Interaction');
					$cmd->setIsVisible(1);
					$cmd->setDisplay('icon', '<i class="fa jeedomapp-audiospeak"></i>');
					}
				$cmd->save();
			} else {
				$cmd = $this->getCmd(null, 'interactioninfo');
				if (is_object($cmd)) {
					$cmd->remove();
				}
			}		
	
					
			
		} else {
			log::add('alexaapi', 'warning', 'Pas de capacité détectée sur , assurez-vous que le démon est OK');
		}

		$this->refresh(false); //false c'est pour ne pas lancer l'actualisation des routines au scan




$device_playlist=str_replace("_player", "", $this->getConfiguration('serial'))."_playlist"; //Nom du device de la playlist
// Si la case "Activer le widget Playlist" est cochée, on rend le device _playlist visible sinon on le passe invisible		
/*	if ($this->getConfiguration('widgetPlayListEnable'))
		log::add('alexaapi', 'warning', 'Rendre Visible'.$device_playlist);
	else
		log::add('alexaapi', 'warning', 'Rendre Invisible'.$device_playlist);
*/
		$eq=eqLogic::byLogicalId($device_playlist,'alexaapi');
				if(is_object($eq)) {
					log::add('alexaapi', 'warning', '*********************'.$device_playlist);
					log::add('alexaapi', 'warning', '*********0****0********'.$eq->getName());
					//$eq->setIsVisible($this->getConfiguration('widgetPlayListEnable'));
					$eq->setIsVisible($this->getConfiguration('widgetPlayListEnable'));
					$eq->setIsEnable($this->getConfiguration('widgetPlayListEnable'));
					$eq->save();
				}


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

	public function preSave() {
	}
}

class alexaapiCmd extends cmd {




	public function dontRemoveCmd() {
		if ($this->getLogicalId() == 'refresh') {
			return true;
		}
		return false;
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
		if (is_object($actionInfo)) {
			$this->setId($actionInfo->getId());
			//log::add('alexaapi', 'info', 'preSave : ' . '******************************************************************************');
			//log::add('alexaapi', 'info', 'TROUVE ' );
			
		}
		//log::add('alexaapi', 'info', 'preSave : ' . '$this->getConfiguration(virtualAction)='.$this->getConfiguration('virtualAction'));
		//log::add('alexaapi', 'info', 'preSave : ' . '$this->getConfiguration(infoName)='.$this->getConfiguration('infoName'));
		//log::add('alexaapi', 'info', 'preSave : ' . 'id='.$this->getID());
		

		if (($this->getType() == 'action') && ($this->getConfiguration('infoName') != ''))
		//Si c'est une action et que Commande info est renseigné
		{
			//$eqLogic = $this->getEqLogic();
			//log::add('alexaapi', 'info', 'preSave : ' . '$this->getConfiguration(infoName)='.$this->getConfiguration('infoName'));
			//log::add('alexaapi', 'info', 'preSave : ' . '$this->getEqLogic_id()='.$this->getEqLogic_id());
			//log::add('alexaapi', 'info', 'preSave : ' . '$this->getName='.$this->getName());
			//On regarde s'il existe déja une commande avec ce nom
			//$cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('infoName')));
			$actionInfo = alexaapiCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getConfiguration('infoName'));
			if (!is_object($actionInfo))
			//C'est une commande qui n'existe pas
			{
				//log::add('alexaapi', 'info', 'preSave : ' . '!is_object($actionInfo) OUI //'. $this->getConfiguration('infoName')." // ".$this->getEqLogic_id());
				$actionInfo = new alexaapiCmd();
				$actionInfo->setType('info');
				$actionInfo->setSubType('string');
				$actionInfo->setConfiguration('taskid', $this->getID());
				$actionInfo->setConfiguration('taskname', $this->getName());
				//$actionInfo->setConfiguration('virtualAction', 1);
				
			}
			$actionInfo->setName($this->getConfiguration('infoName'));
			$actionInfo->setEqLogic_id($this->getEqLogic_id());
			$actionInfo->save();
			$this->setConfiguration('infoId', $actionInfo->getId());
			//log::add('alexaapi', 'info', 'preSave : ' . 'Fin');
		}
	}

	public function execute($_options = null) {
		if ($this->getLogicalId() == 'refresh') {
			$this->getEqLogic()->refresh();
			return;
		}

		//log::add('alexaapi', 'debug', 'execute : Début555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555');
		//On construit la requete 
		//log::add('alexaapi', 'info', 'Request AVANT : ' . $request);//Request : http://192.168.0.21:3456/volume?value=50&device=G090LF118173117U
		$request = $this->buildRequest($_options);
		log::add('alexaapi', 'info', 'Request : ' . $request);//Request : http://192.168.0.21:3456/volume?value=50&device=G090LF118173117U

		// On déclare la requete $request_http en tant que com_http 
		// Archive version avec user/pass
		//if ($this->getConfiguration('http_username') != '' && $this->getConfiguration('http_password') != '') $request_http = new com_http($request, $this->getConfiguration('http_username'), $this->getConfiguration('http_password'));
		//else $request_http = new com_http($request);
		$request_http = new com_http($request);

		//Autorise les réponses vides
		$request_http->setAllowEmptyReponse(true);

		if ($this->getConfiguration('noSslCheck') == 1) $request_http->setNoSslCheck(true);

		if ($this->getConfiguration('doNotReportHttpError') == 1) $request_http->setNoReportError(true);

		// option non activée 
		if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true) {
			$request_http->setNoReportError(true);
			$request_http->exec(0.1, 1);
			return;
		}
		//Lance la requete avec un time out à 2s et 3 essais
		$result = $request_http->exec($this->getConfiguration('timeout', 2), $this->getConfiguration('maxHttpRetry', 3));
		if (!result) throw new Exception(__('Serveur injoignable', __FILE__));

		// json doit etre un retour d'erreur (probablement)
		$jsonResult = json_decode($json, true);
		if (!empty($jsonResult)) throw new Exception(__('Echec de l\'execution: ', __FILE__) . '(' . $jsonResult['title'] . ') ' . $jsonResult['detail']);
		// On traite la valeur de resultat (dans le cas de whennextalarm par exemple)
		$resultjson = json_decode($result, true);
		$value = $resultjson['value'];
		//log::add('alexaapi', 'debug', '** Résultat retour via JSON value=' . $value);
		
		//*******************************************************************************
		// Ici, on va traiter une commande qui n'a pas été executée correctement (erreur type "Connexion Close")
		//log::add('alexaapi', 'debug', '**TEST Connexion Close** dans la Class:'.$value);
		if ($value =="Connexion Close")
		{
		//message::add('alexaapi', 'Attention, Connexion close sur Alexa-API, Lien réinitialisé');
		log::add('alexaapi', 'debug', '**On traite Connexion Close** dans la Class');
		sleep(6);
			if (ob_get_length()) {
			ob_end_flush();
			flush();
			}	
		log::add('alexaapi', 'debug', '**On relance '.$request);
		//message::add('alexaapi', 'Connexion close détectée donc relance de la dernière commande :'.$request);
		//Lance la requete avec un time out à 2s et 3 essais
		$result = $request_http->exec($this->getConfiguration('timeout', 2), $this->getConfiguration('maxHttpRetry', 3));
		if (!result) throw new Exception(__('Serveur injoignable', __FILE__));

		// json doit etre un retour d'erreur (probablement)
		$jsonResult = json_decode($json, true);
		if (!empty($jsonResult)) throw new Exception(__('Echec de l\'execution: ', __FILE__) . '(' . $jsonResult['title'] . ') ' . $jsonResult['detail']);
		// On traite la valeur de resultat (dans le cas de whennextalarm par exemple)
		$resultjson = json_decode($result, true);
		$value = $resultjson['value'];
		
		}
		
		
		

		if (($this->getType() == 'action') && ($this->getConfiguration('infoName') != '')) {
			// On enregistre la valeur de retour dans le champ info
			foreach ($this->getEqLogic()->getCmd('info') as $cmd) {
				//log::add('alexaapi', 'debug', 'getName : ' . $cmd->getName());
				if ($cmd->getName() == $this->getConfiguration('infoName')) {
					$cmd->setConfiguration('value', $value);
					$cmd->event($value);
					$cmd->save();
				}

				/*
				            $value = $cmd->execute();
				            if ($cmd->execCmd(null, 2) != $cmd->formatValue($value))
				                $cmd->event($value);
				*/
			}
		}

		log::add('alexaapi', 'debug', 'Result : ' . $result);
		return true;
	}

	private function buildRequest($_options = array()) {
		//log::add('alexaapi', 'debug', 'buildRequest : $this->getType()='.$this->getType());
		//log::add('alexaapi', 'debug', 'buildRequest : $this->getConfiguration(request)='.$this->getConfiguration('request'));
		if ($this->getType() != 'action') return $this->getConfiguration('request');

		//log::add('alexaapi', 'debug', 'buildRequest : suite');
		list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);
		log::add('alexaapi', 'debug', 'buildRequest : suite1:'.$command);
		switch ($command) {
			case 'volume':
				$request = $this->buildVolumeRequest($_options);
			break;
			case 'playlist':
				$request = $this->buildPlayListRequest($_options);
			break;			
			case 'playmusictrack':
				$request = $this->buildplayMusicTrackRequest($_options);
			break;				
			case 'speak':
				$request = $this->buildSpeakRequest($_options);
			break;
			case 'routine':
				$request = $this->buildRoutineRequest($_options);
			break;
			case 'push':
				$request = $this->buildPushRequest($_options);
			break;
			case 'reminder':
				$request = $this->buildReminderRequest($_options);
			break;
			case 'radio':
				$request = $this->buildRadioRequest($_options);
			break;
			case 'SmarthomeCommand':
				$request = $this->buildSmarthomeCommandRequest($_options);
			break;			
			case 'command':
				$request = $this->buildCommandRequest($_options);
			break;
			case 'alarm':
				$request = $this->buildAlarmRequest($_options);
			break;
			case 'whennextalarm':
				$request = $this->buildNextAlarmRequest($_options);
			break;
			case 'whennextmusicalalarm':
				$request = $this->buildNextMusicalAlarmRequest($_options);
			break;			
			case 'whennextreminder':
				$request = $this->buildNextReminderRequest($_options);
			break;
			case 'deleteallalarms':
				$request = $this->buildDeleteAllAlarmsRequest($_options);
			break;
			case 'deleteReminder':
				$request = $this->buildDeleteReminderRequest($_options);
			break;			
			case 'restart':
				$request = $this->buildRestartRequest($_options);
			break;				default:
				$request = '';
			break;
		}
		//log::add('alexaapi', 'debug', 'buildRequest : suite2/'.$request);
		$request = scenarioExpression::setTags($request);
		//log::add('alexaapi', 'debug', 'buildRequest : suite3');

		if (trim($request) == '') throw new Exception(__('Commande inconnue ou requête vide : ', __FILE__) . print_r($this, true));

		$device=str_replace("_player", "", $this->getEqLogic()->getConfiguration('serial'));
		
		return 'http://' . config::byKey('internalAddr') . ':3456/' . $request . '&device=' . $device;
	}

	private function buildVolumeRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildVolumeRequest');
		$request = $this->getConfiguration('request');
		if (!isset($_options['slider'])) throw new Exception(__('Le slider ne peut pas être vide', __FILE__));

		if ($_options['volume'] == "" && $_options['slider'] == "") $_options['volume'] = "50";

		return str_replace('#volume#', $_options['slider'], $request);
	}
	
	private function buildRestartRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildRestartRequest');
		$request = $this->getConfiguration('request')."?truc=vide";
		return str_replace('#volume#', $_options['slider'], $request);
	}
	
	private function buildSmarthomeCommandRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildSmarthomeCommandRequest');
		$request = $this->getConfiguration('request');
		return $request;
	}	
	
	private function buildRadioRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildRadioRequest');
		$request = $this->getConfiguration('request');
		//if (!isset($_options['station']))
		//   throw new Exception(__('La station ne peut pas être vide', __FILE__));
		if ($_options['station'] == "") $_options['station'] = "s2960";

		//if ($_options['volume'] == "" && $_options['slider'] == "") $_options['volume'] = "50";

		return str_replace(array('#station#', '#volume#'), array(urlencode($_options['station']), isset($_options['volume']) ? $_options['volume'] : $_options['slider']), $request);
	}

	private function buildSpeakRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildSpeakRequest');
		$request = $this->getConfiguration('request');
		if (!isset($_options['message']) || $_options['message'] == '') throw new Exception(__('Le message ne peut pas être vide', __FILE__));

		return str_replace(array('#message#', '#volume#'), array(urlencode($_options['message']), isset($_options['volume']) ? $_options['volume'] : $_options['slider']), $request);
	}
	private function buildReminderRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildReminderRequest');
		$request = $this->getConfiguration('request');
		//if (!isset($_options['text']) || $_options['text'] == '')
		// throw new Exception(__('Le titre ne peut pas être vide', __FILE__));
		if ($_options['when'] == "") $_options['when'] = "2023-01-01 10:00:00";

		//            str_replace(" ", "+", $_options['when']),
		return str_replace(array('#when#', '#text#'), array(urlencode($_options['when']), urlencode($_options['text'])), $request);
	}

	private function buildAlarmRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildalarmRequest');
		$request = $this->getConfiguration('request');
		//		        if (!isset($_options['message']) || $_options['message'] == '')
		//            throw new Exception(__('Le message ne peut pas être vide', __FILE__));
		if ($_options['when'] == "") $_options['when'] = "2023-01-01 10:00:00";

		//            str_replace(" ", "+", $_options['when']),
		return str_replace(array('#when#', '#recurring#'), array(urlencode($_options['when']), urlencode($_options['select']),), $request);
	}

	private function buildPushRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildPushRequest');
		$request = $this->getConfiguration('request');
		if (!isset($_options['message']) || $_options['message'] == '') throw new Exception(__('Le message ne peut pas être vide', __FILE__));

		return str_replace(array('#message#'), array(urlencode($_options['message'])), $request);
	}
	private function buildRoutineRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildRoutineRequest');
		$request = $this->getConfiguration('request');
		//if (!isset($_options['routine']) || $_options['routine'] == '')
		//     throw new Exception(__('La routine ne peut pas être vide', __FILE__));
		return str_replace(array('#select#'), array(urlencode($_options['select'])), $request);
	}
	private function buildPlayListRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildPlayListRequest');
		$request = $this->getConfiguration('request');
		//return str_replace(array('#select#'), array($_options['select'])), $request);
		return str_replace(array('#select#', '#name#'), array(urlencode($_options['select']), urlencode($_options['name'])), $request);
	}	
	private function buildplayMusicTrackRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildplayMusicTrackRequest');
		$request = $this->getConfiguration('request');
		if ($_options['trackid'] == "") $_options['trackid'] = "53bfa26d-f24c-4b13-97a8-8c3debdf06f0";
		//return str_replace(array('#select#'), array($_options['select'])), $request);
		return str_replace(array('#trackid#'), array(urlencode($_options['trackid'])), $request);
	}	
	private function buildNextAlarmRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildNextAlarmRequest sur '.$this->getName());
		$request = $this->getConfiguration('request');

		return str_replace(array('#position#'), array($_options['position']), $request);
	}
	
	private function buildNextMusicalAlarmRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildNextMusicalAlarmRequest');
		$request = $this->getConfiguration('request');

		return str_replace(array('#position#'), array($_options['position']), $request);
	}
	
	private function buildDeleteAllAlarmsRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildDeleteAllAlarmsRequest');
		$request = $this->getConfiguration('request');

		if ($_options['type'] == "") $_options['type'] = "alarm";

		if ($_options['status'] == "") $_options['status'] = "ON";

		return str_replace(array('#type#', '#status#'), array($_options['type'], $_options['status']), $request);
	}
	
	private function builddeleteReminderRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'builddeleteReminderRequest');
		$request = $this->getConfiguration('request');

		if ($_options['id'] == "") $_options['id'] = "coucou";

		if ($_options['status'] == "") $_options['status'] = "ON";

		return str_replace(array('#id#', '#status#'), array($_options['id'], $_options['status']), $request);
	}	
	
	private function buildCommandRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildCommandRequest');
		$request = $this->getConfiguration('request');

		if ($_options['command'] == "") $_options['command'] = "pause";
		//faudra corriger ici ************************position inutile
		return str_replace(array('#command#'), array($_options['command']), $request);
	}
	private function buildNextReminderRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildNextReminderRequest');
		$request = $this->getConfiguration('request');

		return str_replace(array('#position#'), array($_options['position']), $request);
	}
	/***********************Methode d'instance************************* */
	public function getWidgetTemplateCode($_version = 'dashboard', $_noCustom = false) {
		//echo '** ' + $_version + ' **';
		if ($_version != 'scenario') return parent::getWidgetTemplateCode($_version, $_noCustom);

		list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);

		//log::add('alexaapi', 'debug', '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . $arguments);

		if ($command == 'speak' && strpos($arguments, '#volume#') !== false) 
			return getTemplate('core', 'scenario', 'cmd.speak.volume', 'alexaapi');
		if ($command == 'radio' && (strpos($arguments, '#volume#') || strpos($arguments, 'volume')) !== false) 
			return getTemplate('core', 'scenario', 'cmd.radio.volume', 'alexaapi');
		if ($command == 'radio' && (!strpos($arguments, '#volume#'))) 
			return getTemplate('core', 'scenario', 'cmd.radio', 'alexaapi');
		if ($command == 'playmusictrack') 
			return getTemplate('core', 'scenario', 'cmd.playmusictrack', 'alexaapi');		
		if ($command == 'reminder') 
			return getTemplate('core', 'scenario', 'cmd.reminder', 'alexaapi');
		if ($command == 'deleteallalarms') 
			return getTemplate('core', 'scenario', 'cmd.deleteallalarms', 'alexaapi');
		if ($command == 'command') 
			return getTemplate('core', 'scenario', 'cmd.command', 'alexaapi');
		if ($command == 'alarm') 
			return getTemplate('core', 'scenario', 'cmd.alarm', 'alexaapi');
		return parent::getWidgetTemplateCode($_version, $_noCustom);
	}
}

