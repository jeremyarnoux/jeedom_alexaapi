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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');


/*$eqLogics = alexaapi::byType('alexaapi');
foreach ($eqLogics as $eqLogic) {
    log::add('alexaapi', 'info', $eqLogic->getConfiguration('ip'));
}
*/

   if (!isConnect('admin')) {
        throw new \Exception('401 Unauthorized');
    }
    log::add('alexaapi', 'info', 'Lancement Serveur pour Cookie');

    switch (init('action')){
        
		
		case 'createCookie':
    //log::add('alexaapi', 'info', 'Debut');

    $sensor_path = realpath(dirname(__FILE__) . '/../../resources');

//Par sécurité, on Kill un éventuel précédent proessus cookie.js
	$cmd = 'kill $(ps aux | grep "/cookie.js" | awk \'{print $2}\')';
    log::add('alexaapi', 'debug', '---- Kill cookie.js: ' . $cmd);
    $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_cookie') . ' 2>&1 &');
//    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexa-remote-http/index.js ' . config::byKey('internalAddr') . ' ' . $url . ' ' . $log;
    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/initCookie.js '.config::byKey('internalAddr');

    log::add('alexaapi', 'debug', '---- Lancement dÃ©mon Alexa-API-Cookie sur port 3457 : ' . $cmd);
    
	    $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_cookie') . ' 2>&1 &');
    
	if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) 
	{
      log::add('alexaapi', 'error', $result);
      return false;
    }


     log::add('alexaapi', 'info', 'Fin lancement Serveur pour Cookie');
                //throw new \Exception(__('Impossible', __FILE__));
           ajax::success();
        break;

		case 'closeCookie':
    //log::add('alexaapi', 'info', 'Debut');

    $sensor_path = realpath(dirname(__FILE__) . '/../../resources');

//Par sécurité, on Kill un éventuel précédent proessus cookie.js
	$cmd = 'kill $(ps aux | grep "/initCookie.js" | awk \'{print $2}\')';
    log::add('alexaapi', 'debug', '---- Kill cookie.js: ' . $cmd);
    $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_cookie') . ' 2>&1 &');


     log::add('alexaapi', 'info', 'Fin lancement Serveur pour Cookie');
                //throw new \Exception(__('Impossible', __FILE__));
           ajax::success();
        break;

		case 'ScanAmazonAlexa':
		alexaapi::ScanAmazonAlexa();
		//throw new \Exception(__('Impossible', __FILE__));
		ajax::success();
        break;

    }
    throw new \Exception('Aucune methode correspondante');
} catch (\Exception $e) {
    ajax::error(displayException($e), $e->getCode());
      log::add('alexaapi', 'error', $e);
}






