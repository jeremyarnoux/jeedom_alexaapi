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
class alexaapi extends eqLogic
{
	/*     * ***********************Methode static*************************** */

	public static function callProxyAlexaapi($_url) {
		//if (strpos($_url, '?') !== false) {
			$url = 'http://' . config::byKey('internalAddr') . ':3456/' . trim($_url, '/') . '&apikey=' . jeedom::getApiKey('openzwave');
		//} else {
		//	$url = 'http://127.0.0.1:' . config::byKey('port_server', 'openzwave', 8083) . '/' . trim($_url, '/') . '?apikey=' . jeedom::getApiKey('openzwave');
		//}
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
		));
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
    public static function deamon_info()
    {
        $return = array();
        $return['log'] = 'alexaapi_node';
        $return['state'] = 'nok'; // bien ecrire en municules
        // Regarder si alexaapi.js est lancé
        $pid = trim(shell_exec('ps ax | grep "alexaapi/resources/alexaapi.js" | grep -v "grep" | wc -l'));
        if ($pid != '' && $pid != '0')
            $return['state'] = 'ok';

        // Regarder si le cookie existe :alexa-cookie.json
        $request = realpath(dirname(__FILE__) . '/../../resources/data/alexa-cookie.json');
        if (file_exists($request))
        {
            $return['launchable'] = 'ok';
        }
        else
        {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = "Cookie Amazon ABSENT ";
        }

        return $return;
    }

    public static function deamon_start($_debug = false)
    {
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok')
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));

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
        $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexaapi.js ';
        log::add('alexaapi', 'debug', 'Lancement démon alexaapi : ' . $cmd);
        $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_node') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false)
        {
            log::add('alexaapi', 'error', $result);
            return false;
        }
        $i = 0;
        while ($i < 30)
        {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok')
                break;

            sleep(1);
            $i++;
        }
        if ($i >= 30)
        {
            log::add('alexaapi', 'error', 'Impossible de lancer le démon alexaapi, vérifiez le port', 'unableStartDeamon');
            return false;
        }
        message::removeAll('alexaapi', 'unableStartDeamon');
        log::add('alexaapi', 'info', 'Démon alexaapi lancé');
        return true;
    }

    public static function deamon_stop()
    {
        exec('kill $(ps aux | grep "/alexaapi.js" | awk \'{print $2}\')');
        log::add('alexaapi', 'info', 'Arrêt du service alexaapi');
        $deamon_info = self::deamon_info();
        if ($deamon_info['state'] == 'ok')
        {
            sleep(1);
            exec('kill -9 $(ps aux | grep "/alexaapi.js" | awk \'{print $2}\')');
        }

        $deamon_info = self::deamon_info();
        if ($deamon_info['state'] == 'ok')
        {
            sleep(1);
            exec('sudo kill -9 $(ps aux | grep "/alexaapi.js" | awk \'{print $2}\')');
        }
    }

    //*********** Demon Cookie***************
    public static function deamonCookie_start($_debug = false)
    {
        self::deamonCookie_stop();
        $deamon_info = self::deamon_info();

        log::add('alexaapi_cookie', 'info', 'Lancement du démon cookie');
        $log = $_debug ? '1' : '0';

        $sensor_path = realpath(dirname(__FILE__) . '/../../resources');
        //Par sécurité, on Kill un éventuel précédent proessus initCookie.js
        $cmd = "kill $(ps aux | grep 'initCookie.js' | awk '{print $2}')";
        log::add('alexaapi', 'debug', '---- Kill initCookie.js: ' . $cmd);
        $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/initCookie.js ' . config::byKey('internalAddr');
        log::add('alexaapi', 'debug', '---- Lancement démon Alexa-API-Cookie sur port 3457 : ' . $cmd);
        $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_cookie') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false)
        {
            log::add('alexaapi', 'error', $result);
            return false;
        }

        message::removeAll('alexaapi', 'unableStartDeamonCookie');
        log::add('alexaapi_cookie', 'info', 'Démon cookie lancé');
        return true;
    }

    public static function deamonCookie_stop()
    {
        exec('kill $(ps aux | grep "/initCookie.js" | awk \'{print $2}\')');
        log::add('alexaapi', 'info', 'Arrêt du service cookie');
        $deamon_info = self::deamon_info();
        if ($deamon_info['stateCookie'] == 'ok')
        {
            sleep(1);
            exec('kill -9 $(ps aux | grep "/initCookie.js" | awk \'{print $2}\')');
        }
    }

    //************Dépendances ***********
    public static function dependancy_info()
    {
        log::add('alexaapi','info','Controle dependances');
        $return = array();
        $return['log'] = 'alexaapi_dep';
        $request = realpath(dirname(__FILE__) . '/../../resources/node_modules');
        $return['progress_file'] = '/tmp/alexaapi_dep';
        $return['state'] = is_dir($request) ? 'ok' : 'nok';
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
    public static function scanAmazonAlexa($_logical_id = null, $_exclusion = 0)
    {
        
        $deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != "ok"){
		event::add('jeedom::alert', array('level' => 'danger', 'page' => 'alexaapi', 'message' => __('Cookie Amazon Absent, allez dans la Configuration du plugin', __FILE__),));
		return;
		}
		
		event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan en cours...', __FILE__),));
        $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/devices");
        $json = json_decode($json, true);

        $numDevices = 0;
        $numNewDevices = 0;
        foreach ($json as $item)
        {
            // Skip the special device named "This Device"
            if ($item['name'] == 'This Device')
                continue;

            // Retireve the device (if already registered in Jeedom)
            $device = alexaapi::byLogicalId($item['serial'], 'alexaapi');
            if (!$device)
            {
                $device = self::createNewDevice($item['name'], $item['serial']);
                self::importDefaultCommandTo($device);
                $numNewDevices++;
            }

            // Update device configuration
            $device->setConfiguration('device', $item['name']);
            $device->setConfiguration('type', $item['type']);
            $device->setConfiguration('members', $item['members']);
			$device->setStatus('online', $item['online']);
            $device->save();

            $numDevices++;
        }

        event::add('jeedom::alert', array(
          'level' => 'success',
          'page' => 'alexaapi',
          'message' => __('Scan terminé. ' . $numDevices . ' équipements mis a jour dont ' . $numNewDevices . ' ajouté(s)', __FILE__)
        ));
    }

    private static function createNewDevice($deviceName, $deviceSerial)
    {
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

    private static function importDefaultCommandTo($device)
    {


     /*       if ($device->getName() == 'Tous les appareils')
        {
            return;
		}*/
	if (strstr($device->getName(), "Alexa Apps"))
        {
      // Push command
      $cmd = new alexaapiCmd();
      $cmd->setType('action');
      $cmd->setSubType('message');
      $cmd->setEqLogic_id($device->getId());
      $cmd->setName('Push');
      $cmd->setConfiguration('request', 'push?text=#message#');
      $cmd->setDisplay('title_disable', 1);
      $cmd->save();
            return;
		}

      // Speak command
      $cmd = new alexaapiCmd();
      $cmd->setType('action');
      $cmd->setSubType('message');
      $cmd->setEqLogic_id($device->getId());
      $cmd->setName('Speak');
      $cmd->setConfiguration('request', 'speak?text=#message#');
      $cmd->setDisplay('title_disable', 1);
      $cmd->save();

      // Speak + Volume command
      $cmd = new alexaapiCmd();
      $cmd->setType('action');
      $cmd->setSubType('message');
      $cmd->setEqLogic_id($device->getId());
      $cmd->setName('Speak+Volume');
      $cmd->setConfiguration('request', 'speak?text=#message#&volume=#volume#');
      $cmd->setIsVisible(false);
      $cmd->setDisplay('title_disable', 1);
      $cmd->save();

      // alarm command
      $cmd = new alexaapiCmd();
      $cmd->setType('action');
      $cmd->setSubType('message');
      $cmd->setEqLogic_id($device->getId());
      $cmd->setDisplay('title_disable', 1);
      $cmd->setName('Alarm');
	  $cmd->setIsVisible(false);
      $cmd->setConfiguration('request', 'alarm?when=#when#&recurring=#recurring#');
      $cmd->save();
	  
      // alarm command
      $cmd = new alexaapiCmd();
      $cmd->setType('action');
      $cmd->setSubType('other');
      $cmd->setEqLogic_id($device->getId());
      $cmd->setName('Next Alarm');
	  $cmd->setIsVisible(false);
      $cmd->setConfiguration('request', 'whennextalarm?position=0');
      $cmd->save();

      // Reminder command
      $cmd = new alexaapiCmd();
      $cmd->setType('action');
      $cmd->setSubType('message');
      $cmd->setEqLogic_id($device->getId());
      $cmd->setName('Reminder');
	  $cmd->setIsVisible(false);
      $cmd->setConfiguration('request', 'reminder?text=#message#&when=#when#');
      $cmd->save();

      // Volume command
      $cmd = new alexaapiCmd();
      $cmd->setType('action');
      $cmd->setSubType('slider');
      $cmd->setEqLogic_id($device->getId());
      $cmd->setName('Volume');
      $cmd->setConfiguration('request', 'volume?value=#volume#');
      $cmd->setConfiguration('minValue', '0');
      $cmd->setConfiguration('maxValue', '100');
      $cmd->save();

    }

    public static function dependancy_install()
    {
        log::add('alexaapi', 'info', 'Installation des dépendances : Alexa-Remote-http');
        $resource_path = realpath(dirname(__FILE__) . '/../../resources');
        passthru('/bin/bash ' . $resource_path . '/nodejs.sh ' . $resource_path . ' alexaapi > ' . log::getPathToLog('alexaapi_dep') . ' 2>&1 &');
    }

    public function preUpdate() {}

    public function postSave() {}

    public function preSave() {}
}

class alexaapiCmd extends cmd
{
    public function preSave()
    {
		if ($this->getType() == 'action')
        {
            $eqLogic = $this->getEqLogic();
            $this->setConfiguration('value', 'http://' . config::byKey('internalAddr') . ':3456/' . $this->getConfiguration('request') . "&device=" . $eqLogic->getConfiguration('serial'));
        }
		
		
			$actionInfo = virtualCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getName());
			if (is_object($actionInfo)) {
				$this->setId($actionInfo->getId());
        log::add('alexaapi', 'info', 'preSave : ' . '******************************************************************************');
        log::add('alexaapi', 'info', 'TROUVE ' );
			}        
        log::add('alexaapi', 'info', 'preSave : ' . '$this->getConfiguration(virtualAction)='.$this->getConfiguration('virtualAction'));
			
        log::add('alexaapi', 'info', 'preSave : ' . '$this->getConfiguration(infoName)='.$this->getConfiguration('infoName'));
        log::add('alexaapi', 'info', 'preSave : ' . 'id='.$this->getID());
			
			
		if (($this->getType() == 'action') && ($this->getConfiguration('infoName') != '')) 
			//Si c'est une action et que Commande info est renseigné
        {
			
            //$eqLogic = $this->getEqLogic();

        log::add('alexaapi', 'info', 'preSave : ' . '$this->getConfiguration(infoName)='.$this->getConfiguration('infoName'));
        log::add('alexaapi', 'info', 'preSave : ' . '$this->getEqLogic_id()='.$this->getEqLogic_id());
        log::add('alexaapi', 'info', 'preSave : ' . '$this->getName='.$this->getName());
			//On regarde s'il existe déja une commande avec ce nom
			//$cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('infoName')));
		$actionInfo = alexaapiCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getConfiguration('infoName'));
				if (!is_object($actionInfo)) 
					//C'est une commande qui n'existe pas
				{
		        log::add('alexaapi', 'info', 'preSave : ' . '!is_object($actionInfo) OUI //'. $this->getConfiguration('infoName')." // ".$this->getEqLogic_id());
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
		        log::add('alexaapi', 'info', 'preSave : ' . 'Fin');
		

        }
    }

    public function execute($_options = null)
    {
        if ($this->getLogicalId() == 'refresh')
        {
            $this->getEqLogic()->refresh();
            return;
        }
          

        $request = $this->buildRequest($_options);
        log::add('alexaapi', 'info', 'Request : ' . $request);

        // Execute request
        if ($this->getConfiguration('http_username') != '' && $this->getConfiguration('http_password') != '')
            $request_http = new com_http($request, $this->getConfiguration('http_username'), $this->getConfiguration('http_password'));
        else
            $request_http = new com_http($request);

        //if ($this->getConfiguration('allowEmptyResponse') == 1)
            $request_http->setAllowEmptyReponse(true);

        if ($this->getConfiguration('noSslCheck') == 1)
            $request_http->setNoSslCheck(true);

        if ($this->getConfiguration('doNotReportHttpError') == 1)
            $request_http->setNoReportError(true);

        if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true)
        {
            $request_http->setNoReportError(true);
            $request_http->exec(0.1, 1);
            return;
        }
        //log::add('alexaapi', 'info', 'Request : ' . $request_http);
        $result = $request_http->exec($this->getConfiguration('timeout', 2), $this->getConfiguration('maxHttpRetry', 3));
        //$result = $request_http->exec();
        if (!result)
          throw new Exception(__('Serveur injoignable', __FILE__));

        log::add('alexaapi', 'debug', 'Result : ' . $result);

        $jsonResult = json_decode($json, true);
        if (!empty($jsonResult))
            throw new Exception(__('Echec de l\'execution: ', __FILE__) . '(' . $jsonResult['title'] . ') ' . $jsonResult['detail']);

        // Update info
        if ($this->getType() == 'action')
        {
            foreach ($this->getEqLogic()->getCmd('info') as $cmd)
            {
                $value = $cmd->execute();
                if ($cmd->execCmd(null, 2) != $cmd->formatValue($value))
                    $cmd->event($value);
            }
        }

        log::add('alexaapi', 'debug', 'Result : ' . $result);
        return true;
    }

    private function buildRequest($_options = array())
    {
        if ($this->getType() != 'action')
          return $this->getConfiguration('request');

        list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);
        switch ($command)
        {
            case 'volume':
                $request = $this->buildVolumeRequest($_options);
                break;
            case 'speak':
                $request = $this->buildSpeakRequest($_options);
                break;
            case 'push':
                $request = $this->buildPushRequest($_options);
                break;
            case 'reminder':
                $request = $this->buildReminderRequest($_options);
                break;
            case 'alarm':
                $request = $this->buildAlarmRequest($_options);
                break;
            case 'whennextalarm':
                $request = $this->buildNextAlarmRequest($_options);
                break;
            default:
                $request = '';
        }
        $request = scenarioExpression::setTags($request);

        if (trim($request) == '')
            throw new Exception(__('Commande inconnue ou requête vide : ', __FILE__) . print_r($this, true));

        return 'http://' . config::byKey('internalAddr') . ':3456/' . $request . '&device=' . $this->getEqLogic()->getConfiguration('serial');
    }

    private function buildVolumeRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildVolumeRequest');
        $request = $this->getConfiguration('request');
        if (!isset($_options['slider']))
            throw new Exception(__('Le slider ne peut pas être vide', __FILE__));

        return str_replace('#volume#', $_options['slider'], $request);
    }

    private function buildSpeakRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildSpeakRequest');
        $request = $this->getConfiguration('request');
        if (!isset($_options['message']) || $_options['message'] == '')
            throw new Exception(__('Le message ne peut pas être vide', __FILE__));

        return str_replace(
          array(
            '#message#',
            '#volume#'
          ), array(
            urlencode($_options['message']),
            isset($_options['volume']) ? $_options['volume'] : $_options['slider']
          ), $request);
    }
    private function buildReminderRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildReminderRequest');
        $request = $this->getConfiguration('request');
        if (!isset($_options['message']) || $_options['message'] == '')
            throw new Exception(__('Le message ne peut pas être vide', __FILE__));

        return str_replace(
          array(
            '#when#',
            '#message#'
          ), array(
//            str_replace(" ", "+", $_options['when']),
            urlencode($_options['when']),
            urlencode($_options['message'])
          ), $request);
    }

    private function buildAlarmRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildalarmRequest');
        $request = $this->getConfiguration('request');
		        if (!isset($_options['message']) || $_options['message'] == '')
            throw new Exception(__('Le message ne peut pas être vide', __FILE__));

        return str_replace(
          array(
            '#when#',
            '#recurring#'
          ), array(
//            str_replace(" ", "+", $_options['when']),
            urlencode($_options['message']),
            urlencode($_options['select']),
          ), $request);
    }

    private function buildPushRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildPushRequest');
        $request = $this->getConfiguration('request');
        if (!isset($_options['message']) || $_options['message'] == '')
            throw new Exception(__('Le message ne peut pas être vide', __FILE__));

        return str_replace(
          array(
            '#message#'
          ), array(
            urlencode($_options['message'])
          ), $request);
    }
    private function buildNextAlarmRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildNextAlarmRequest');
        $request = $this->getConfiguration('request');

        return str_replace(
          array(
            '#position#'
          ), array(
            $_options['position']
          ), $request);
    }

    /***********************Methode d'instance************************* */
    public function getWidgetTemplateCode($_version = 'dashboard', $_noCustom = false)
    {
      //echo '** ' + $_version + ' **';
      if ($_version != 'scenario')
        return parent::getWidgetTemplateCode($_version, $_noCustom);

      list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);

      if ($command == 'speak' && strpos($arguments, '#volume#') !== false)
        return getTemplate('core', 'scenario', 'cmd.speak.volume', 'alexaapi');
      if ($command == 'reminder')
        return getTemplate('core', 'scenario', 'cmd.reminder', 'alexaapi');
      if ($command == 'alarm')
        return getTemplate('core', 'scenario', 'cmd.alarm', 'alexaapi');
      return parent::getWidgetTemplateCode($_version, $_noCustom);
    }
}