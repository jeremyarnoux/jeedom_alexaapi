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
		//    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/Alexa-Remote-http/index.js ' . config::byKey('internalAddr') . ' ' . $url . ' ' . $log;
		$cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexaapi.js ' . config::byKey('internalAddr') . ' ' . config::byKey('amazonserver', 'alexaapi', 'amazon.fr') . ' ' . config::byKey('alexaserver', 'alexaapi', 'alexa.amazon.fr');
		log::add('alexaapi', 'debug', 'Lancement démon alexaapi : ' . $cmd);
		$result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_node') . ' 2>&1 &');
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
		$cmd = system::getCmdSudo() . 'apt-get -y --purge autoremove nodejs npm';
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
		//log::add('alexaapi', 'debug', '---------------------------------------------DEBUT CRON------------------------');
		$d = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
		$deamon_info = self::deamon_info();
		
		if ($d->isDue() && $deamon_info['state'] == 'ok') {

			//log::add('alexaapi', 'debug', '---------------------------------------------AVANT AVANT AVANT Boucle CRON------------------------');

			$eqLogics = ($_eqlogic_id !== null) ? array(eqLogic::byId($_eqlogic_id)) : eqLogic::byType('alexaapi', true);
			$test2060NOK=true;
			foreach ($eqLogics as $alexaapi) {
				$cmd = $alexaapi->getCmd(null, 'reminder');
				log::add('alexaapi', 'debug', '-----------------------------Boucle CRON de *'.$alexaapi->getName().'*------------------------');
				//log::add('alexaapi', 'debug', '---------------------------------------------AVANT Boucle CRON-'.$alexaapi->getName().'-----------------------');
				//log::add('alexaapi', 'debug', '---------------------------------------------AVANT Boucle CRON2-'.$alexaapi->getName().'-----------------------');
				//log::add('alexaapi', 'debug', '---------------------------------------------compteurNbTest2060OK-1*'.$compteurNbTest2060OK.'*-----------------------');
				if ($test2060NOK && $alexaapi->test2060()) {
					$test2060NOK=false;
				} else {
					break;	
				}

				//log::add('alexaapi', 'debug', '---------------------------------------------compteurNbTest2060OK-2*'.$compteurNbTest2060OK.'*-----------------------');
				//log::add('alexaapi', 'debug', '---------------------------------------------FIN Boucle CRON------------------------');
				sleep(2);
			}

			// On va tester si la connexion est active à l'aide d'un rappel en 2060 qu'on retire derrière.
			// $compteurNbTest2060OK correspond au nb de test qui on été OK, si =0 faut relancer le serveur
			if ($test2060NOK) {
				self::restartServeurPHP();
				message::add('alexaapi', 'Connexion close détectée dans le CRON, relance transparente du serveur '.date("Y-m-d H:i:s").' OK !');
			}
			else {//pourra $etre supprimé quand stable
				log::add('alexaapi', 'debug', 'Connexion close non détectée dans le CRON. Tout va bien.');
			}
		}
		
		// boucle refresh
		$autorefreshR = '*/15 * * * *';
		$r = new Cron\CronExpression($autorefreshR, new Cron\FieldFactory);
		if ($r->isDue() && $deamon_info['state'] == 'ok') {
			$eqLogics = ($_eqlogic_id !== null) ? array(eqLogic::byId($_eqlogic_id)) : eqLogic::byType('alexaapi', true);

			foreach ($eqLogics as $alexaapi) {
				$alexaapi->refresh();
				sleep(2);
			}			
		}
						//log::add('alexaapi', 'debug', '---------------------------------------------FIN CRON------------------------');
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

		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan en cours...', __FILE__),));
		$json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/devices");
		$json = json_decode($json, true);

		$numDevices = 0;
		$numNewDevices = 0;
		foreach ($json as $item) {
			// Skip the special device named "This Device"
			if ($item['name'] == 'This Device') continue;

			// Retireve the device (if already registered in Jeedom)
			$device = alexaapi::byLogicalId($item['serial'], 'alexaapi');
			if (!is_object($device)) {
				$device = self::createNewDevice($item['name'], $item['serial']);
				$numNewDevices++;
			}

			// Update device configuration
			$device->setConfiguration('device', $item['name']);
			$device->setConfiguration('type', $item['type']);
			$device->setConfiguration('family', $item['family']);
			$device->setConfiguration('members', $item['members']);
			$device->setStatus('online', $item['online']);
			$device->save();

			$numDevices++;
		}

		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan terminé. ' . $numDevices . ' équipements mis a jour dont ' . $numNewDevices . ' ajouté(s)', __FILE__)));
	}

	private static function createNewDevice($deviceName, $deviceSerial) {
		$newDevice = new alexaapi();
		$newDevice->setName($deviceName);
		$newDevice->setLogicalId($deviceSerial);
		$newDevice->setEqType_name('alexaapi');
		$newDevice->setIsVisible(1);
		$newDevice->setConfiguration('device', $deviceName);
		$newDevice->setConfiguration('serial', $deviceSerial);
		$newDevice->setIsEnable(1);
		$newDevice->save();

		return $newDevice;
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
	public function refresh() {
	//log::add('alexaapi', 'debug', '-----Lancement refresh1---**-----');
		$deamon_info = alexaapi::deamon_info();
		if ($deamon_info['state'] != 'ok') return false;
	//log::add('alexaapi', 'debug', '-----Lancement refresh2---*'.$this->getName().'*-----');


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
		if ($deamon_info['state'] != 'ok') return 0;
		
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
				if ($item['type']!="Reminder") break;
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

		/*       if ($this->getName() == 'Tous les appareils')
		      {
		          return;
		}*/

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

		// Routine command
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
/*
		// Speak + Volume command
		$cmd = $this->getCmd(null, 'speak-volume');
		if (!is_object($cmd)) {
			$cmd = new alexaapiCmd();
			$cmd->setType('action');
			$cmd->setLogicalId('speak-volume');
			$cmd->setSubType('message');
			$cmd->setEqLogic_id($this->getId());
			$cmd->setName('Speak+Volume');
			$cmd->setConfiguration('request', 'speak?text=#message#&volume=#volume#');
			$cmd->setDisplay('title_disable', 1);
			$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
			$cmd->setIsVisible(0);
		}
		$cmd->save();

		// Radio + Volume command
		$cmd = $this->getCmd(null, 'radio-volume');
		if (!is_object($cmd)) {
			$cmd = new alexaapiCmd();
			$cmd->setType('action');
			$cmd->setLogicalId('radio-volume');
			$cmd->setSubType('message');
			$cmd->setEqLogic_id($this->getId());
			$cmd->setName('Radio+Volume');
			$cmd->setConfiguration('request', 'radio?station=#station#&volume=#volume#');
			$cmd->setDisplay('title_disable', 1);
			$cmd->setDisplay('icon', '<i class="loisir-musical7"></i>');
			$cmd->setIsVisible(0);
		}
		$cmd->save();
*/
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
			$cmd->setName('Next Musical Alarm When');
			$cmd->setDisplay('icon', '<i class="fa fa-bell"></i>');
			$cmd->setConfiguration('RunWhenRefresh', 1);
			$cmd->setConfiguration('request', 'whennextmusicalalarm?position=1');
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
			$cmd->setDisplay('icon', '<i class="fa fa-volume-up"></i>');
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

		$this->refresh();
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
		//log::add('alexaapi', 'debug', 'buildRequest : Début');
		//log::add('alexaapi', 'debug', 'buildRequest : $this->getType()='.$this->getType());
		//log::add('alexaapi', 'debug', 'buildRequest : $this->getConfiguration(request)='.$this->getConfiguration('request'));
		if ($this->getType() != 'action') return $this->getConfiguration('request');

		//log::add('alexaapi', 'debug', 'buildRequest : suite');
		list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);
		//log::add('alexaapi', 'debug', 'buildRequest : suite1');
		switch ($command) {
			case 'volume':
				$request = $this->buildVolumeRequest($_options);
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

		return 'http://' . config::byKey('internalAddr') . ':3456/' . $request . '&device=' . $this->getEqLogic()->getConfiguration('serial');
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
	private function buildRadioRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildRadioRequest');
		$request = $this->getConfiguration('request');
		//if (!isset($_options['station']))
		//   throw new Exception(__('La station ne peut pas être vide', __FILE__));
		if ($_options['station'] == "") $_options['station'] = "s2960";

		if ($_options['volume'] == "" && $_options['slider'] == "") $_options['volume'] = "50";

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
	private function buildNextAlarmRequest($_options = array()) {
		log::add('alexaapi', 'debug', 'buildNextAlarmRequest');
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

