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
class alexaapi extends eqLogic {
    public static function sendCommand($name, $value) {
        $url = 'http://' . config::byKey('internalAddr') . ':3456/' . $value . "&device=" . $name;
        $retour = file_get_contents($url);
        throw new Exception(__('>>' . $url . '>>' . $retour, __FILE__));
    }
    public static function deamon_info() {
        $return = array();
        $return['log'] = 'alexaapi_node';
        $return['state'] = 'nok';
        $pid = trim(shell_exec('ps ax | grep "alexaapi/resources/alexaapi.js" | grep -v "grep" | wc -l'));
        if ($pid != '' && $pid != '0') {
            $return['state'] = 'ok';
        }
        $return['launchable'] = 'ok';
        return $return;
    }
    public static function deamon_start($_debug = false) {
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }
        log::add('alexaapi', 'info', 'Lancement du démon alexaapi');
        $url = network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/alexaapi/core/api/jeealexaapi.php?apikey=' . jeedom::getApiKey('alexaapi');
        if ($_debug = true) {
            $log = "1";
        } else {
            $log = "0";
        }
        $sensor_path = realpath(dirname(__FILE__) . '/../../resources');
        //    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexa-remote-http/index.js ' . config::byKey('internalAddr') . ' ' . $url . ' ' . $log;
        $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexaapi.js ';
        log::add('alexaapi', 'debug', 'Lancement démon alexaapi : ' . $cmd);
        $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_node') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
            log::add('alexaapi', 'error', $result);
            return false;
        }
        $i = 0;
        while ($i < 30) {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                break;
            }
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
    public static function dependancy_info() {
        $return = array();
        $return['log'] = 'alexaapi_dep';
        //$serialport = realpath(dirname(__FILE__) . '/../../resources/node_modules/http');
        $request = realpath(dirname(__FILE__) . '/../../resources/node_modules');
        //$request = realpath(dirname(__FILE__) . '/../../resources/node_modules/request');
        $return['progress_file'] = '/tmp/alexaapi_dep';
        //   if (is_dir($serialport) && is_dir($request)) {
        if (is_dir($request)) {
            $return['state'] = 'ok';
        } else {
            $return['state'] = 'nok';
        }
        return $return;
    }
    public static function reinstallNodeJS() {
	$pluginalexaapi = plugin::byId('alexaapi');
	log::add('alexaapi', 'info', 'Suppression du Code NodeJS');
	$cmd = system::getCmdSudo() . 'rm -rf '.dirname(__FILE__) . '/../../resources/node_modules &>/dev/null';
	log::add('alexaapi', 'info', 'Suppression de NodeJS');
	$cmd = system::getCmdSudo() . 'apt-get -y --purge autoremove nodejs npm';
	exec($cmd);
	log::add('alexaapi', 'info', 'Réinstallation des dependances');
	$pluginalexaapi->dependancy_install();
		
	return true;
    }		
    public static function ScanAmazonAlexa($_logical_id = null, $_exclusion = 0) {
        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan en cours...', __FILE__),));
        $json = file_get_contents("http://".config::byKey('internalAddr').":3456/devices");
        $json = json_decode($json, true);
		$nbdedevice=0;
		$nbdedevicenouveau=0;
        foreach ($json as $item) {
			$nbdedevice++;
            $device = $item['name'];
            $serial = $item['serial'];
            $type = $item['type'];
            $online = $item['online'];
            $alexaapi = alexaapi::byLogicalId($serial, 'alexaapi');
            if (!is_object($alexaapi)) {
			$nbdedevicenouveau++;
                $alexaapi = new alexaapi();
                $alexaapi->setName($device);
                $alexaapi->setLogicalId($serial);
                $alexaapi->setEqType_name('alexaapi');
                $alexaapi->setIsEnable(1);
                $alexaapi->setIsVisible(1);
            }
            $alexaapi->setConfiguration('serial', $serial);
            $alexaapi->setConfiguration('device', $device);
            $alexaapi->setConfiguration('type', $type);
            $alexaapi->setStatus('online', $online);
            $alexaapi->save();
        }
        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan terminé. '.$nbdedevice.' équipements mis a jour dont '.$nbdedevicenouveau.' ajouté(s)', __FILE__),));
        return;
    }
    public static function dependancy_install() {
        log::add('alexaapi', 'info', 'Installation des dépéndances : alexa-remote-http');
        $resource_path = realpath(dirname(__FILE__) . '/../../resources');
        passthru('/bin/bash ' . $resource_path . '/nodejs.sh ' . $resource_path . ' alexaapi > ' . log::getPathToLog('alexaapi_dep') . ' 2>&1 &');
    }
    public function preUpdate() {
        //if ($this->getConfiguration('ip') == '') {throw new Exception(__('L\'adresse ne peut etre vide',__FILE__)); }
        
    }
    public function postSave() {
        // $this->refresh();
        //throw new Exception(__('L\'adresse ne peut etre vide',__FILE__));
        
    }
    public function preSave() {
        //$this->setLogicalId($this->getConfiguration('ip'));
        
    }
}
class alexaapiCmd extends cmd {
    public function executex($_options = null) {
        switch ($this->getType()) {
            case 'info':
                return $this->getConfiguration('value');
            break;
            case 'action':
                $request = $this->getConfiguration('request');
                switch ($this->getSubType()) {
                    case 'slider':
                        $request = str_replace('#slider#', $_options['slider'], $request);
                    break;
                    case 'color':
                        $request = str_replace('#color#', $_options['color'], $request);
                    break;
                    case 'message':
                        if ($_options != null) {
                            $replace = array('#title#', '#message#');
                            $replaceBy = array($_options['title'], $_options['message']);
                            if ($_options['title'] == '') {
                                throw new Exception(__('Le sujet ne peuvent être vide', __FILE__));
                            }
                            $request = str_replace($replace, $replaceBy, $request);
                        } else $request = 1;
                        break;
                    default:
                        $request == null ? 1 : $request;
                    }
                    $eqLogic = $this->getEqLogic();
                    alexaapi::sendCommand($eqLogic->getName(), $request);
                    return $request;
            }
            return true;
    }
    public function preSave() {
        if ($this->getType() == "action") {
            $eqLogic = $this->getEqLogic();
            log::add('alexaapi', 'info', 'http://' . config::byKey('internalAddr') . ':3456/' . $this->getConfiguration('request') . "&device=" . $eqLogic->getName());
            $this->setConfiguration('value', 'http://' . config::byKey('internalAddr') . ':3456/' . $this->getConfiguration('request') . "&device=" . $eqLogic->getName());
            //$this->save();
            
        }
    }
    public function execute($_options = null) {
        if ($this->getLogicalId() == 'refresh') {
            $this->getEqLogic()->refresh();
            return;
        }
        $eqLogic = $this->getEqLogic();
		$device=$eqLogic->getName();
		//throw new Exception(__('On est LA -->>>>>>'.$device."*".$this->getConfiguration('request')."<<<<<<<<--", __FILE__) . print_r($this, true));
        $result = false;
        $request = str_replace('#API#', config::byKey('api'), $this->getConfiguration('request'));
        if (trim($request) == '') {
            throw new Exception(__('La requête ne peut pas être vide : ', __FILE__) . print_r($this, true));
        }
       if ($_options != null) {
            switch ($this->getType()) {
                case 'action':
                    switch ($this->getSubType()) {
                        case 'slider':
                            $request = str_replace('#slider#', $_options['slider'], $request);
                        break;
                        case 'color':
                            if ($this->getConfiguration('requestType') != 'http') {
                                $request = str_replace('#color#', $_options['color'], $request);
                            } else {
                                $request = str_replace('#color#', substr($_options['color'], 1), $request);
                            }
                        break;
                        case 'select':
                            $request = str_replace('#select#', $_options['select'], $request);
                        break;
                        case 'message':
                            $replace = array('#title#', '#message#');
                            if ($this->getConfiguration('requestType') == 'http') {
                                $replaceBy = array(urlencode($_options['title']), urlencode($_options['message']));
                            } elseif ($this->getConfiguration('requestType') == 'script') {
                                $replaceBy = array($_options['title'], $_options['message']);
                            } else {
                                $replaceBy = array(escapeshellcmd($_options['title']), escapeshellcmd($_options['message']));
                            }
                            if ($_options['message'] == '' && $_options['title'] == '') {
                                throw new Exception(__('Le message et le sujet ne peuvent pas être vide', __FILE__));
                            }
                            $request = str_replace($replace, $replaceBy, $request);
                        break;
                    }
                break;
            }
        }
        $request = scenarioExpression::setTags($request);
        $replace = array('\'' => '', '#eqLogic_id#' => $this->getEqLogic_id(), '#cmd_id#' => $this->getId(),);
        $request = str_replace(array_keys($replace), $replace, $request);
		//******************************
		$request ="http://".config::byKey('internalAddr').":3456/".$request."&device=".$device;
		//******************************
        log::add('alexaapi', 'debug', 'Request : ' . $request);

				$request = str_replace('"', '%22', $request);
                $request = str_replace(' ', '%20', $request);
                if ($this->getConfiguration('http_username') != '' && $this->getConfiguration('http_password') != '') {
                    $request_http = new com_http($request, $this->getConfiguration('http_username'), $this->getConfiguration('http_password'));
                } else {
                    $request_http = new com_http($request);
                }
                if ($this->getConfiguration('allowEmptyResponse') == 1) {
                    $request_http->setAllowEmptyReponse(true);
                }
                if ($this->getConfiguration('noSslCheck') == 1) {
                    $request_http->setNoSslCheck(true);
                }
                if ($this->getConfiguration('doNotReportHttpError') == 1) {
                    $request_http->setNoReportError(true);
                }
                if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true) {
                    $request_http->setNoReportError(true);
                    $request_http->exec(0.1, 1);
                    return;
                }
                $result = trim($request_http->exec($this->getConfiguration('timeout', 2), $this->getConfiguration('maxHttpRetry', 3)));
                if (trim($this->getConfiguration('reponseMustContain')) != '' && strpos($result, trim($this->getConfiguration('reponseMustContain'))) === false) {
                    throw new Exception(__('La réponse ne contient pas "', __FILE__) . $this->getConfiguration('reponseMustContain') . '" : "' . $result . '"');
                }

        if ($this->getType() == 'action') {
            foreach ($this->getEqLogic()->getCmd('info') as $cmd) {
                $value = $cmd->execute();
                if ($cmd->execCmd(null, 2) != $cmd->formatValue($value)) {
                    $cmd->event($value);
                }
            }
        }
        log::add('alexaapi', 'debug', 'Result : ' . $result);
        return $result;
    }
}
