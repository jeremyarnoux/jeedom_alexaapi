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
/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class alexaapi extends eqLogic
{
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
            throw new Exception(__('Veuillez vÃ©rifier la configuration', __FILE__));

        log::add('alexaapi', 'info', 'Lancement du dÃ©mon alexaapi');
        $url = network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/alexaapi/core/api/jeealexaapi.php?apikey=' . jeedom::getApiKey('alexaapi');
        $log = $_debug ? '1' : '0';

        $sensor_path = realpath(dirname(__FILE__) . '/../../resources');
        //    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexa-remote-http/index.js ' . config::byKey('internalAddr') . ' ' . $url . ' ' . $log;
        $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexaapi.js ';
        log::add('alexaapi', 'debug', 'Lancement dÃ©mon alexaapi : ' . $cmd);
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
            log::add('alexaapi', 'error', 'Impossible de lancer le dÃ©mon alexaapi, vÃ©rifiez le port', 'unableStartDeamon');
            return false;
        }
        message::removeAll('alexaapi', 'unableStartDeamon');
        log::add('alexaapi', 'info', 'DÃ©mon alexaapi lancÃ©');
        return true;
    }

    public static function deamon_stop()
    {
        exec('kill $(ps aux | grep "/alexaapi.js" | awk \'{print $2}\')');
        log::add('alexaapi', 'info', 'ArrÃªt du service alexaapi');
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

        log::add('alexaapi_cookie', 'info', 'Lancement du dÃ©mon cookie');
        $log = $_debug ? '1' : '0';

        $sensor_path = realpath(dirname(__FILE__) . '/../../resources');
        //Par sécurité, on Kill un éventuel précédent proessus initCookie.js
        $cmd = "kill $(ps aux | grep 'initCookie.js' | awk '{print $2}')";
        log::add('alexaapi', 'debug', '---- Kill initCookie.js: ' . $cmd);
        $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/initCookie.js ' . config::byKey('internalAddr');
        log::add('alexaapi', 'debug', '---- Lancement dÃ©mon Alexa-API-Cookie sur port 3457 : ' . $cmd);
        $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_cookie') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false)
        {
            log::add('alexaapi', 'error', $result);
            return false;
        }

        message::removeAll('alexaapi', 'unableStartDeamonCookie');
        log::add('alexaapi_cookie', 'info', 'DÃ©mon cookie lancÃ©');
        return true;
    }

    public static function deamonCookie_stop()
    {
        exec('kill $(ps aux | grep "/initCookie.js" | awk \'{print $2}\')');
        log::add('alexaapi', 'info', 'ArrÃªt du service cookie');
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

    public static function reinstallNodeJS() {
	      $pluginalexaapi = plugin::byId('alexaapi');
	      log::add('alexaapi', 'info', 'Suppression du Code NodeJS');
	      $cmd = system::getCmdSudo() . 'rm -rf '.dirname(__FILE__) . '/../../resources/node_modules &>/dev/null';
	      log::add('alexaapi', 'info', 'Suppression de NodeJS');
	      $cmd = system::getCmdSudo() . 'apt-get -y --purge autoremove nodejs npm';
	      exec($cmd);
	      log::add('alexaapi', 'info', 'RÃ©installation des dependances');
	      $pluginalexaapi->dependancy_install();
    }
  
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
          'message' => __('Scan terminÃ©. ' . $numDevices . ' Ã©quipements mis a jour dont ' . $numNewDevices . ' ajoutÃ©(s)', __FILE__)
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
        log::add('alexaapi', 'info', 'Installation des dÃ©pÃ©ndances : alexa-remote-http');
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

        if ($this->getConfiguration('allowEmptyResponse') == 1)
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

        $result = $request_http->exec($this->getConfiguration('timeout', 2), $this->getConfiguration('maxHttpRetry', 3));
        if (!result)
          throw new Exception(__('Serveur injoignable', __FILE__));

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
            default:
                $request = '';
        }
        $request = scenarioExpression::setTags($request);

        if (trim($request) == '')
            throw new Exception(__('La requÃªte ne peut pas Ãªtre vide : ', __FILE__) . print_r($this, true));

        return 'http://' . config::byKey('internalAddr') . ':3456/' . $request . '&device=' . $this->getEqLogic()->getConfiguration('serial');
    }

    private function buildVolumeRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildVolumeRequest');
        $request = $this->getConfiguration('request');
        if (!isset($_options['slider']))
            throw new Exception(__('Le slider ne peut pas Ãªtre vide', __FILE__));

        return str_replace('#volume#', $_options['slider'], $request);
    }

    private function buildSpeakRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildSpeakRequest');
        $request = $this->getConfiguration('request');
        if (!isset($_options['message']) || $_options['message'] == '')
            throw new Exception(__('Le message ne peut pas Ãªtre vide', __FILE__));

        return str_replace(
          array(
            '#message#',
            '#volume#'
          ), array(
            urlencode($_options['message']),
            isset($_options['volume']) ? $_options['volume'] : $_options['slider']
          ), $request);
    }

    private function buildPushRequest($_options = array())
    {
        log::add('alexaapi', 'debug', 'buildPushRequest');
        $request = $this->getConfiguration('request');
        if (!isset($_options['message']) || $_options['message'] == '')
            throw new Exception(__('Le message ne peut pas Ãªtre vide', __FILE__));

        return str_replace(
          array(
            '#message#'
          ), array(
            urlencode($_options['message'])
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
      return parent::getWidgetTemplateCode($_version, $_noCustom);
    }
}