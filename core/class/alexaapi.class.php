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

  public static function sendCommand( $ip, $value ) {
    $url = 'http://' . $ip . '/control?cmd=' . $value;
    $retour = file_get_contents($url);
  }

  public static function deamon_info() {
    $return = array();
    $return['log'] = 'alexaapi_node';
    $return['state'] = 'nok';
    $pid = trim( shell_exec ('ps ax | grep "alexaapi/resources/alexaapi.js" | grep -v "grep" | wc -l') );
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


	public static function ScanAmazonAlexa($_logical_id = null, $_exclusion = 0)
{
			
$json=file_get_contents("http://192.168.0.21:3456/devices");
$json = json_decode($json,true);

foreach($json as $item)
	{



		$device = $item['name'];
		$serial = $item['serial'];
		$type = $item['type'];
		$online = $item['online'];

		$alexaapi = alexaapi::byLogicalId($serial, 'alexaapi');
		if (!is_object($alexaapi)) {
			$alexaapi = new alexaapi();
			$alexaapi->setName($device);
			$alexaapi->setLogicalId($serial); 
			$alexaapi->setEqType_name('alexaapi');
			$alexaapi->setIsEnable(1);
			$alexaapi->setIsVisible(1);
		}
		$alexaapi->setConfiguration('serial',$serial); 
		$alexaapi->setConfiguration('device',$device);
		$alexaapi->setConfiguration('type',$type);
		$alexaapi->setStatus('online',$online);
		$alexaapi->save();
 }			
			event::add('jeedom::alert', array(
				'level' => 'success',
				'page' => 'alexaapi',
				'message' => __('Scan en cours...', __FILE__),
			));
			return;
}


  public static function dependancy_install() {
    log::add('alexaapi','info','Installation des dépéndances : alexa-remote-http');
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
  public function execute($_options = null) {
    switch ($this->getType()) {
      case 'info' :
      return $this->getConfiguration('value');
      break;
      case 'action' :
      $request = $this->getConfiguration('request');
      switch ($this->getSubType()) {
        case 'slider':
        $request = str_replace('#slider#', $_options['slider'], $request);
        break;
        case 'color':
        $request = str_replace('#color#', $_options['color'], $request);
        break;
        case 'message':
        if ($_options != null)  {
          $replace = array('#title#', '#message#');
          $replaceBy = array($_options['title'], $_options['message']);
          if ( $_options['title'] == '') {
            throw new Exception(__('Le sujet ne peuvent être vide', __FILE__));
          }
          $request = str_replace($replace, $replaceBy, $request);
        }
        else
        $request = 1;
        break;
        default : $request == null ?  1 : $request;
      }

      $eqLogic = $this->getEqLogic();

      alexaapi::sendCommand(
      $eqLogic->getConfiguration('ip') ,
      $request );

      return $request;
    }
    return true;
  }

  public function preSave() {
    if ($this->getType() == "action") {
      $eqLogic = $this->getEqLogic();
      log::add('alexaapi','info','http://' . $eqLogic->getConfiguration('ip') . '/control?cmd=' . $this->getConfiguration('request'));
      $this->setConfiguration('value', 'http://' . $eqLogic->getConfiguration('ip') . '/control?cmd=' . $this->getConfiguration('request'));
      //$this->save();
    }
  }
}
