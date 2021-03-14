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
    /*$eqLogics = alexasmarthome::byType('alexasmarthome');
    foreach ($eqLogics as $eqLogic) {
    log::add('alexasmarthome', 'info', $eqLogic->getConfiguration('ip'));
    }
    */
    if (!isConnect('admin')) {
        throw new \Exception('401 Unauthorized');
    }
//            $('.deamonCookieState').empty().append('<span class="label label-success" style="font-size:1em;">00012300</span>');
    //log::add('alexasmarthome', 'info', 'Lancement Serveur pour Cookie - action='.init('action'));
    switch (init('action')) {
        case 'metAjourFabriquantsDesactives':
            $ret = alexasmarthome::metAjourFabriquantsDesactives();
            ajax::success($ret);
            break;
        case 'forcerRefresh':
            $ret = alexasmarthome::forcerRefresh(init('id'));
            ajax::success($ret);
            break;    
	}
    throw new \Exception('Aucune methode correspondante');
} catch (\Exception $e) {
    ajax::error(displayException($e), $e->getCode());
    log::add('alexasmarthome', 'error', $e);
}
