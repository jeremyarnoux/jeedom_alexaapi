<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class alexaapi extends eqLogic
{
	/*     * *************************Attributs pour autoriser les onglets Affichage et Disposition****************************** */
	public static $_widgetPossibility = array('custom' => true, 'custom::layout' => true);

    // A supprimer dans quelques temps, tous les listePluginsAlexa sont remplacés par listePluginsAlexaArray
    public static function listePluginsAlexa($withAPI = false, $withSmartHome = false)
    {
        $liste = array();
        if ($withAPI) array_push($liste, "alexaapi");
        try {
            $test = plugin::byId('alexaamazonmusic');
            if ($test->isActive()) array_push($liste, "alexaamazonmusic");
        } catch (Exception $e) {
        }
        try {
            $test = plugin::byId('alexadeezer');
            if ($test->isActive()) array_push($liste, "alexadeezer");
        } catch (Exception $e) {
        }
        try {
            $test = plugin::byId('alexaspotify');
            if ($test->isActive()) array_push($liste, "alexaspotify");
        } catch (Exception $e) {
        }
        if ($withSmartHome) {
            try {
                $test = plugin::byId('alexasmarthome');
                if ($test->isActive()) array_push($liste, "alexasmarthome");
            } catch (Exception $e) {
            }
        }
        return $liste;
    }

    public static function listePluginsAlexaArray($withAPI = false, $withSmartHome = false, $all = false, $withFireTV = false)
    {
        //log::add('alexaapi', 'debug', 'Test  : listePluginsAlexaArray');
		$liste = array();
        if ($withAPI) array_push($liste, array("pluginId" => "alexaapi", "nom" => "Alexa-API", "actif" => true));
        if ($withSmartHome) {
            try {
                $test = plugin::byId('alexasmarthome');
				$eqLogics = eqLogic::byType($test->getId());
                //array_push($liste, self::listePluginsAlexaArray_controle('alexasmarthome', 'smartHome', '3914', config::byKey('numsmartHome', 'alexaapi')));
                array_push($liste, self::listePluginsAlexaArray_controle('alexasmarthome', 'smartHome', '3914', count($eqLogics)));
            } catch (Exception $e) {
                if ($all) {
                    array_push($liste, self::listePluginsAlexaArray_controle('alexasmarthome', 'smartHome', '3914'));
                }
            }
        }
        if ($withFireTV) {
            try {
                $test = plugin::byId('alexafiretv');
				$eqLogics = eqLogic::byType($test->getId());
                //array_push($liste, self::listePluginsAlexaArray_controle('alexafiretv', 'FireTV', '4064', config::byKey('numFireTV', 'alexaapi')));
                array_push($liste, self::listePluginsAlexaArray_controle('alexafiretv', 'FireTV', '4064', count($eqLogics)));
            } catch (Exception $e) {
                if ($all) {
                    array_push($liste, self::listePluginsAlexaArray_controle('alexafiretv', 'FireTV', '4064'));
                }
            }
        }        
			try {
			$test = plugin::byId('alexaamazonmusic');
			$eqLogics = eqLogic::byType($test->getId());
            //array_push($liste, self::listePluginsAlexaArray_controle('alexaamazonmusic', 'Amazon Music', '3910', config::byKey('numAudioPlayer', 'alexaapi')));
            array_push($liste, self::listePluginsAlexaArray_controle('alexaamazonmusic', 'Amazon Music', '3910', intval(count($eqLogics)/2)));
        } catch (Exception $e) {
            if ($all) {
                array_push($liste, self::listePluginsAlexaArray_controle('alexaamazonmusic', 'Amazon Music', '3910'));
            }
        }
        try {
            $test = plugin::byId('alexadeezer');
			$eqLogics = eqLogic::byType($test->getId());
            //array_push($liste, self::listePluginsAlexaArray_controle('alexadeezer', 'Deezer', '3911', config::byKey('numAudioPlayer', 'alexaapi')));
            array_push($liste, self::listePluginsAlexaArray_controle('alexadeezer', 'Deezer', '3911', intval(count($eqLogics)/2)));
        } catch (Exception $e) {
            if ($all) {
                array_push($liste, self::listePluginsAlexaArray_controle('alexadeezer', 'Deezer', '3911'));
            }
        }
        try {
            $test = plugin::byId('alexaspotify');
			$eqLogics = eqLogic::byType($test->getId());
            //array_push($liste, self::listePluginsAlexaArray_controle('alexaspotify', 'Spotify', '3913', config::byKey('numAudioPlayer', 'alexaapi')));
            array_push($liste, self::listePluginsAlexaArray_controle('alexaspotify', 'Spotify', '3913', intval(count($eqLogics)/2)));
        } catch (Exception $e) {
            if ($all) {
                array_push($liste, self::listePluginsAlexaArray_controle('alexaspotify', 'Spotify', '3913'));
            }
        }
        return $liste;
    }

    public static function listePluginsAlexaArray_controle($pluginId = "", $plugin = "inconnu", $idMarket = "3910", $nb = "0")
    {
        $valeurs = array();
		        //log::add('alexaapi', 'debug', 'Test  : listePluginsAlexaArray_controle' . $pluginId.$idMarket);

        try {
            $valeurs = array(
                "pluginId" => $pluginId,
                "idMarket" => $idMarket,
                "nb" => $nb,
                "nom" => $plugin,
                "install" => true,
                "actif" => plugin::byId($pluginId)->isActive()
            );
        } catch (Exception $e) {
            $valeurs = array(
                "pluginId" => $pluginId,
                "idMarket" => $idMarket,
                "nb" => $nb,
                "nom" => $plugin,
                "install" => false,
                "actif" => false
            );
        }
		//log::add('alexaapi', 'debug', 'Test1  : listePluginsAlexaArray_controle' . $pluginId.":".plugin::byId($pluginId)->isActive());
		//log::add('alexaapi', 'debug', 'Test2  : listePluginsAlexaArray_controle' . $pluginId.":".plugin::byId($pluginId)->getName());
        //	array_push($liste, $valeurs);
        return $valeurs;
    }

    /*
public static function templateWidget(){
        $return = array('info' => array('string' => array()));
        $return['info']['string']['state'] = array(
            'template' => 'tmplmultistate','test' => array(
                array('operation' => '#value# == 2','state' => '<i class="icon maison-vacuum6"></i>'),
                array('operation' => '#value# == 3','state' => '<i class="fas fa-pause"></i>'),
                array('operation' => '#value# > 3 || #value# < 2','state' => '<i class="fas fa-home"></i>')
            )
        );
        return $return;
    }*/
    public static function templateWidget()
    {
        $return = array('info' => array('string' => array(), 'numeric' => array()));
        $return = array('action' => array('select' => array(), 'slider' => array()));
        $return['action']['other']['repeat'] = array(
            'template' => 'tmplicon',
            'replace' => array("#_icon_off_#" => "<i class='fas fa-redo' style='opacity:0.3'></i>", "#_icon_on_#" => "<i class='fas fa-redo'></i>", "#hide_name#" => "hidden", "#message_disable#" => "1")
        );
        $return['info']['binary']['isMutedinfo'] = array(
            'template' => 'isMutedinfo',
            'replace' => array("#_icon_on_#" => "<i class='icon jeedomapp-audiomute icon_orange'></i>", "#_icon_off_#" => "<i class=''></i>", "#hide_name#" => "hidden", "#message_disable#" => "1")
        );      
		$return['action']['other']['shuffle'] = array(
            'template' => 'tmplicon',
            'replace' => array("#_icon_off_#" => "<i class='fas fa-random fa-ld' style='opacity:0.3'></i>", "#_icon_on_#" => "<i class='fas fa-random fa-ld'></i>", "#hide_name#" => "hidden", "#message_disable#" => "1")
        );
        $return['info']['string']['subText2'] = array('template' => 'album');
        $return['info']['string']['alarmmusicalmusic'] = array('template' => 'alarmmusicalmusic', 'replace' => array("#hide_name#" => "hidden"));
        $return['info']['string']['title'] = array('template' => 'title');
        $return['info']['string']['url'] = array('template' => 'image');
        $return['info']['string']['interaction'] = array('template' => 'cadre');
        $return['action']['message']['message'] = array(
            'template' => 'message',
            'replace' => array("#_desktop_width_#" => "100", "#_mobile_width_#" => "50", "#title_disable#" => "1", "#message_disable#" => "0")
        );
        $return['action']['select']['list'] = array(
            'template' => 'table',
            'replace' => array("#_desktop_width_#" => "100", "#_mobile_width_#" => "50", "#hide_name#" => "whidden")
        );
        $return['action']['slider']['volume'] = array(
            'template' => 'nooSliderAlexa'
        );
        $return['action']['slider']['volume_legacy'] = array(
            'template' => 'bouton',
            'replace' => array("#hide_name#" => "hidden", "#step#" => "10")
        );
        $return['info']['string']['state'] = array(
            'template' => 'tmplmultistate_alexaapi',
            'replace' => array("#hide_name#" => "hidden", "#hide_state#" => "hidden", "#marge_gauche#" => "5px", "#marge_haut#" => "-15px"),
            'test' => array(
                array(
                    'operation' => "#value# == 'PLAYING'", 'state_light' => "<img src='plugins/alexaapi/core/img/playing.png'  title ='" . __('Playing', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/playing.png' title ='" . __('En charge', __FILE__) . "'>"),
					
                array('operation' => "#value# != 'PLAYING'", 'state_light' => "<img src='plugins/alexaapi/core/img/paused.png' title ='" . __('En Pause', __FILE__) . "'>")
            )
        );
        $return['info']['string']['alarm'] = array(
            'template' => 'alarm',
            'replace' => array("#hide_name#" => "hidden", "#marge_gauche#" => "55px", "#marge_haut#" => "15px"),
            'test' => array(
                array(
                    'operation' => "#value# == ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-Off.png' title ='" . __('Playing', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-Off_dark.png' title ='" . __('En charge', __FILE__) . "'>"
                ),
                array(
                    'operation' => "#value# != ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-On.png' title ='" . __('En Pause', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Clock-Icon-On_dark.png' title ='" . __('En Pause', __FILE__) . "'>"
                )
            )
        );
        $return['info']['string']['alarmmusical'] = array(
            'template' => 'alarm',
            'replace' => array("#hide_name#" => "hidden", "#marge_gauche#" => "55px", "#marge_haut#" => "15px"),
            'test' => array(
                array(
                    'operation' => "#value# == ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-Off.png' title ='" . __('Playing', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-Off_dark.png' title ='" . __('En charge', __FILE__) . "'>"
                ),
                array(
                    'operation' => "#value# != ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-On.png' title ='" . __('En Pause', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Musical-Icon-On_dark.png' title ='" . __('En Pause', __FILE__) . "'>"
                )
            )
        );
        $return['info']['string']['timer'] = array(
            'template' => 'alarm',
            'replace' => array("#hide_name#" => "hidden", "#marge_gauche#" => "55px", "#marge_haut#" => "15px"),
            'test' => array(
                array(
                    'operation' => "#value# == ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Timer-Icon-Off.png' title ='" . __('Playing', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Timer-Icon-Off_dark.png' title ='" . __('En charge', __FILE__) . "'>"
                ),
                array(
                    'operation' => "#value# != ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Timer-Icon-On.png' title ='" . __('En Pause', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Timer-Icon-On_dark.png' title ='" . __('En Pause', __FILE__) . "'>"
                )
            )
        );
        $return['info']['string']['reminder'] = array(
            'template' => 'alarm',
            'replace' => array("#hide_name#" => "hidden", "#marge_gauche#" => "55px", "#marge_haut#" => "4px"),
            'test' => array(
                array(
                    'operation' => "#value# == ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-Off.png' title ='" . __('Playing', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-Off_dark.png' title ='" . __('En charge', __FILE__) . "'>"
                ),
                array(
                    'operation' => "#value# != ''",
                    'state_light' => "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-On.png' title ='" . __('En Pause', __FILE__) . "'>",
                    'state_dark' => "<img src='plugins/alexaapi/core/img/Alarm-Reminder-Icon-On_dark.png' title ='" . __('En Pause', __FILE__) . "'>"
                )
            )
        );
        return $return;
    }


    public static function callProxyAlexaapi($_url)
    {
        //$url = 'http://' . config::byKey('internalAddr') . ':3456/' . trim($_url, '/') . '&apikey=' . jeedom::getApiKey('openzwave');
        $url = 'http://' . config::byKey('internalAddr') . ':3456/' . trim($_url, '/') . '&apikey=' . jeedom::getApiKey('alexaapi');
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

    public static function deamon_info()
    {
        $return = array();
        $return['log'] = 'alexaapi_node';
        $return['state'] = 'nok';
        $return['stateCookies'] = 'non lancé';

        // Regarder si alexaapi.js est lancé
        $pid = trim(shell_exec('ps ax | grep "resources/alexaapi.js" | grep -v "grep" | wc -l'));
        if ($pid != '' && $pid != '0') $return['state'] = 'ok';

        // Regarder si alexaapi.js est lancé
        $pid = trim(shell_exec('ps ax | grep "resources/initCookie.js" | grep -v "grep" | wc -l'));
        if ($pid != '' && $pid != '0') $return['stateCookies'] = 'lancé';

        // Regarder si le cookie existe :alexa-cookie.json
        $request = realpath(dirname(__FILE__) . '/../../resources/data/alexa-cookie.json');
        if (file_exists($request)) $return['launchable'] = 'ok';
        else {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = "Cookie Amazon ABSENT ";
        }
        return $return;
    }

    public static function deamon_start($_debug = false)
    {
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        log::add('alexaapi', 'info', ' Lancement du démon alexaapi');
        $url = network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/alexaapi/core/api/jeealexaapi.php?apikey=' . jeedom::getApiKey('alexaapi');
        $log = $_debug ? '1' : '0';
        $sensor_path = realpath(dirname(__FILE__) . '/../../resources');
        $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/alexaapi.js ' . network::getNetworkAccess('internal') . ' ' . config::byKey('amazonserver', 'alexaapi', 'amazon.fr') . ' ' . config::byKey('alexaserver', 'alexaapi', 'alexa.amazon.fr') . ' ' . jeedom::getApiKey('alexaapi') . ' ' . log::getLogLevel('alexaapi');
        log::add('alexaapi', 'debug', 'Lancement démon alexaapi : ' . $cmd);
        $result = exec('NODE_ENV=production nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_node') . ' 2>&1 &');
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
        log::add('alexaapi', 'info', ' Démon alexaapi lancé');
        return true;
    }

    public static function deamon_stop()
    {
        log::add('alexaapi', 'info', ' Arrêt du service alexaapi');
        @file_get_contents("http://" . config::byKey('internalAddr') . ":3456/stop");
        sleep(3);
        if (shell_exec('ps aux | grep "resources/alexaapi.js" | grep -v "grep" | wc -l') == 1) {
            exec('sudo kill $(ps aux | grep "resources/alexaapi.js" | grep -v "grep" | awk \'{print $2}\') &>/dev/null');
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                sleep(1);
                exec('sudo kill -9 $(ps aux | grep "resources/alexaapi.js" | grep -v "grep" | awk \'{print $2}\') &>/dev/null');
            }
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                sleep(1);
                exec('sudo kill -9 $(ps aux | grep "resources/alexaapi.js" | grep -v "grep" | awk \'{print $2}\') &>/dev/null');
            }
        }
        self::deamonCookie_stop();
    }

    public static function reinstallNodeJS()
    { // Reinstall NODEJS from scratch (to use if there is errors in dependancy install)
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


    public static function deamonCookie_start($_debug = false)
    { //*********** Demon Cookie***************
        self::deamonCookie_stop();
        $deamon_info = self::deamon_info();
        log::add('alexaapi_cookie', 'info', ' Lancement du démon cookie');
        $log = $_debug ? '1' : '0';
        $sensor_path = realpath(dirname(__FILE__) . '/../../resources');

        $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/initCookie.js ' . config::byKey('internalAddr') . ' ' . config::byKey('amazonserver', 'alexaapi', 'amazon.fr') . ' ' . config::byKey('alexaserver', 'alexaapi', 'alexa.amazon.fr');
        log::add('alexaapi', 'debug', '---- Lancement démon Alexa-API-Cookie sur port 3457 : ' . $cmd);
        $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('alexaapi_cookie') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
            log::add('alexaapi', 'error', $result);
            return false;
        }
        message::removeAll('alexaapi', 'unableStartDeamonCookie');
        log::add('alexaapi_cookie', 'info', ' Démon cookie lancé');
        return true;
    }

    public static function deamonCookie_stop()
    {
        if (shell_exec('ps aux | grep "resources/initCookie.js" | grep -v "grep" | wc -l') == 1) {
            log::add('alexaapi', 'info', 'Arrêt du service cookie');
            exec('sudo kill $(ps aux | grep "resources/initCookie.js" | grep -v "grep" | awk \'{print $2}\') &>/dev/null');
            sleep(3);
            if (shell_exec('ps aux | grep "resources/initCookie.js" | grep -v "grep" | wc -l') == 1) {
                exec('sudo kill -9 $(ps aux | grep "resources/initCookie.js" | grep -v "grep" | awk \'{print $2}\') &>/dev/null');
            }
        }
    }

    public static function dependancy_info()
    {    //************Dépendances ***********
        $return = array();
        $return['log'] = 'alexaapi_dep';
        $resources = realpath(dirname(__FILE__) . '/../../resources/');
        $packageJson = json_decode(file_get_contents($resources . '/package.json'), true);
        $state = 'ok';
        foreach ($packageJson["dependencies"] as $dep => $ver) {
            if (!file_exists($resources . '/node_modules/' . $dep . '/package.json')) {
                $state = 'nok';
            }
        }
        $return['progress_file'] = jeedom::getTmpFolder('alexaapi') . '/dependance';
        //$return['state'] = is_dir($resources.'/node_modules') ? 'ok' : 'nok';
        $return['state'] = $state;
        return $return;
    }

    public static function supprimeTouslesDevices()
    {

        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Suppression en cours ...', __FILE__)));
        foreach (self::listePluginsAlexa(true, true) as $pluginAlexaUnparUn) {
            foreach (eqLogic::byType(plugin::byId($pluginAlexaUnparUn)->getId()) as $eqLogic) {
                $eqLogic->remove();
            }
        }
        self::scanAmazonAlexa();
    }
	
    public static function supprimeTouslesDevicesSmartHome()
    {
//$test = plugin::byId('alexasmarthome');
        
		//$eqLogics = ($_eqlogic_id !== null) ? array(eqLogic::byId($_eqlogic_id)) : eqLogic::byType('alexasmarthome', true);
		
	//	foreach (self::listePluginsAlexa(false, true) as $pluginAlexaUnparUn) {
            foreach (eqLogic::byType('alexasmarthome', false) as $eqLogic) {
					//log::add('alexasmarthome_scan', 'debug', 'TEST---------->>>>> ' . $eqLogic->getName());
                $eqLogic->remove();
            }
        }
      /*  event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Suppression en cours ...', __FILE__)));
        foreach (self::listePluginsAlexa(true, true) as $pluginAlexaUnparUn) {
            foreach (eqLogic::byType(plugin::byId($pluginAlexaUnparUn)->getId()) as $eqLogic) {
                $eqLogic->remove();
            }
        }*/
        //self::scanAmazonAlexa();
 //   }
	
    public static function cron($_eqlogic_id = null)
    {
        // Toutes les minutes, on cherche les players en lecture et on les actualise
        $dd = new Cron\CronExpression('* * * * *', new Cron\FieldFactory);
        $deamon_info = self::deamon_info();
        if ($dd->isDue() && $deamon_info['state'] == 'ok') {
            $plugin = plugin::byId('alexaapi');
            $eqLogics = eqLogic::byType($plugin->getId());
            foreach ($eqLogics as $eqLogic) {
                if ($eqLogic->getStatus('Playing')) { // On va chercher un Device en "Playing"
                    log::add('alexaapi', 'debug', 'Refresh automatique (CRON) de ' . $eqLogic->getName());
                    $eqLogic->refresh();
                }
            }
        }

        $d = new Cron\CronExpression('*/15 * * * *', new Cron\FieldFactory);
        //$d = new Cron\CronExpression('* * * * *', new Cron\FieldFactory);
        $deamon_info = self::deamon_info();
        if ($d->isDue() && $deamon_info['state'] == 'ok') {
            //log::add('alexaapi', 'debug', '---------------------------------------------DEBUT CRON-'.$autorefresh.'-----------------------');
            $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/devices");
            $json = json_decode($json, true);
            $status = [];
            foreach ($json as $item) {
                if ($item['name'] == 'This Device') continue;

                $eq = eqLogic::byLogicalId($item['serial'], 'alexaapi');
                if (is_object($eq)) {
                    log::add('alexaapi', 'debug', 'mise à jour Online status of ' . $item['name'] . ' to ' . (($item['online']) ? 'true' : 'false'));
                    $eq->setStatus('online', (($item['online']) ? true : false)); //status online
                    $eq->checkAndUpdateCmd('onLine', (($item['online']) ? true : false)); // commande info onLine

                }
            }
        }

        $c = new Cron\CronExpression('*/6 * * * *', new Cron\FieldFactory);
        if ($c->isDue() && $deamon_info['state'] == 'ok') {
            //self::checkAuth(); 20/09/2020 on désactive ce test pour voir s'il est utile ou pas
        }

		//Relance non obligatoire depuis la stabilité du cookie. Déconseillé, ajout de la condition "réservé aux utilisateurs expérimentés" Modif Sigalou 21/03/2021
        if (config::byKey('utilisateurExperimente', 'alexaapi', 0) != "0") {
			//$autorefreshRR = checkAndFixCron(config::byKey('autorefresh', 'alexaapi', '33 3 * * *'));/* boucle qui relance la connexion au serveur*/
			$autorefreshRR = checkAndFixCron(config::byKey('autorefresh', 'alexaapi', '51 20 31 01 0 2021'));/* Par défaut désactivé également Modif Sigalou 21/03/2021*/
			$cc = new Cron\CronExpression($autorefreshRR, new Cron\FieldFactory);
			if ($cc->isDue() && $deamon_info['state'] == 'ok') {
				self::restartServeurPHP();
			}
		}

        $r = new Cron\CronExpression('*/16 * * * *', new Cron\FieldFactory); // boucle refresh
        //		$r = new Cron\CronExpression('* * * * *', new Cron\FieldFactory);// boucle refresh
        if ($r->isDue() && $deamon_info['state'] == 'ok') {
            $eqLogics = ($_eqlogic_id !== null) ? array(eqLogic::byId($_eqlogic_id)) : eqLogic::byType('alexaapi', true);
            config::save("listRoutinesProchain", time() + 960, "alexaapi");
                //log::add('alexaapi_node', 'debug', 'Prochain CRON calé à : '.time() + 960);
            foreach ($eqLogics as $alexaapi) {
                //log::add('alexaapi_node', 'debug', 'CRON Refresh: '.$alexaapi->getName());
                $alexaapi->refresh();
                sleep(2);
            }
        }
        //log::add('alexaapi', 'debug', '---------------------------------------------FIN CRON------------------------');
    }

    public static function checkAuth()
    {
        $result = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/checkAuth");
        $resultjson = json_decode($result, true);
        $value = $resultjson['authenticated'];
        if ($value == 1)
            log::add('alexaapi', 'debug', 'Résultat du checkAuth  OK (' . $value . ')');
        else {
            log::add('alexaapi', 'debug', 'Résultat du checkAuth NOK (' . $value . ') ==> Relance Serveur');
            self::restartServeurPHP();
            //message::add('alexaapi', '(Beta Alexa-api) Authentification Amazon revalidée, tout va bien');
        }
    }

    public static function restartServeurPHP()
    {
        $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/restart");
        sleep(2);
    }

    public static function forcerDefaultCmd($_id = null)
    {
        if (!is_null($_id)) {
            $device = alexaapi::byId($_id);
            if (is_object($device)) {
                $device->setStatus('forceUpdate', true);
                $device->save();
            }
        }
    }

    public static function forcerDefaultAllCmd()
    {
        $plugin = plugin::byId('alexaapi');
        $eqLogics = eqLogic::byType($plugin->getId());
        foreach ($eqLogics as $eqLogic) {
            $eqLogic->setStatus('forceUpdate', true);
            $eqLogic->save();
        }
        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Mise à jour terminée', __FILE__)));
    }

    public static function scanAmazonAlexa()
    {
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != "ok") {
            event::add('jeedom::alert', array('level' => 'danger', 'page' => 'alexaapi', 'message' => __('Cookie Amazon Absent, allez dans la Configuration du plugin', __FILE__),));
            return;
        }
        log::add('alexaapi_scan', 'debug', '*************************** Lancement du Scan Alexa-API ***********************************');
        // --- Mise à jour des Amazon Echo
        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan en cours...', __FILE__),));
        $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/devices");
		//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $json = json_decode($json, true);
        $numDevices = 0;
        $numNewDevices = 0;
        $numAudioPlayer = 0;
        foreach ($json as $item) {
            // Skip the special device named "This Device"
            if ($item['name'] == 'This Device') continue;
            //log::add('alexaapi_scan', 'debug', 'Niveau 1');
            log::add('alexaapi_scan', 'debug', '------------------------------------------------------------------------------');
            log::add('alexaapi_scan', 'debug', '-> Ajout du device ' . $item['name']);
            log::add('alexaapi_scan', 'debug', '------------------------------------------------------------------------------');

            foreach (self::listePluginsAlexa() as $pluginAlexaUnparUn) {

                log::add('alexaapi_scan', 'debug', '*** Détection pour le plugin '.$pluginAlexaUnparUn);

                if (in_array("AUDIO_PLAYER", $item['capabilities'])) {
                    $numAudioPlayer++;
                    if ($pluginAlexaUnparUn == 'alexaamazonmusic') {
                        // Device PLAYLIST -----------------------------------------------------------------------------------------------------------------
                        $device = $pluginAlexaUnparUn::byLogicalId($item['serial'] . "_playlist", $pluginAlexaUnparUn);
                        if (!is_object($device)) {
                            $device = $pluginAlexaUnparUn::createNewDevice($item['name'] . " PlayList", $item['serial'] . "_playlist");
                            $device->setIsVisible(0);
                        }
                        log::add('alexaapi_scan', 'debug', '*** [Plugin ' . $pluginAlexaUnparUn . '] ->> détection1 de ' . $device->getName());
                        // Update device configuration
                        $device->setConfiguration('device', $item['name']);
                        $device->setConfiguration('type', $item['type']);
                        $device->setConfiguration('devicetype', "PlayList");
                        $device->setConfiguration('family', $item['family']);
                        $device->setConfiguration('members', $item['members']);
                        $device->setIsVisible(1);
                        //$device->setIsEnable(0);
                        $device->setConfiguration('capabilities', $item['capabilities']);
                        try {
                            $device->save();
                        } catch (Exception $e) {
                            $device->setName($device->getName() . ' doublon ' . rand(0, 9999));
                            $device->save();
                        }
                        $device->setStatus('online', (($item['online']) ? true : false));
                    }
                    // Device PLAYER -----------------------------------------------------------------------------------------------------------------
                    $device = $pluginAlexaUnparUn::byLogicalId($item['serial'] . "_player", $pluginAlexaUnparUn);
                    if (!is_object($device)) {
                        $device = $pluginAlexaUnparUn::createNewDevice($item['name'] . " Player", $item['serial'] . "_player");
                        //$numNewDevices++;
                        $device->setConfiguration('widgetPlayListEnable', 0);
                    }
                    log::add('alexaapi_scan', 'debug', '*** [Plugin ' . $pluginAlexaUnparUn . '] ->> détection2 de ' . $device->getName());
                    // Update device configuration
                    $device->setConfiguration('device', $item['name']);
                    $device->setConfiguration('type', $item['type']);
                    $device->setConfiguration('devicetype', "Player");
                    $device->setConfiguration('family', $item['family']);
                    $device->setConfiguration('members', $item['members']);
                    $device->setConfiguration('capabilities', $item['capabilities']);
                    try {
                        $device->save();
                    } catch (Exception $e) {
                        $device->setName($device->getName() . ' doublon ' . rand(0, 9999));
                        $device->save();
                    }
                    $device->setStatus('online', (($item['online']) ? true : false));
                    //$numDevices++;
                }
            }


			// On teste si Alexa-FireTV est installé et actif
			$alexaFireTVActif=false;
			$listePluginsAlexaArray=self::listePluginsAlexaArray(false, false, false, true);
			if (in_array("alexafiretv", array_column($listePluginsAlexaArray, 'pluginId')))
			{
				// Ici Alexa-FireTV installé, on va tester si actif.
				$keyAlexaFireTV=array_search("alexafiretv", array_column($listePluginsAlexaArray, 'pluginId'));
				$alexaFireTVActif=$listePluginsAlexaArray[$keyAlexaFireTV]['actif']; // 0=inactif 1 =actif
			}

			if (($item['family'] == 'FIRE_TV') && ($alexaFireTVActif))
			{
			// On a le plugin Alexa Fire TV présent et activé et la famille est FIRE_TV
				$device = self::byLogicalId($item['serial'], 'alexafiretv');
				if (!is_object($device)) {
					$device = alexafiretv::createNewDevice($item['name'], $item['serial']);
					$numNewDevices++;
				}
				// Update device configuration
				log::add('alexaapi_scan', 'debug', '*** [Plugin Alexa-Fire TV       ] ->> détection de ' . $device->getName());
				$numFireTV++;
			} else {

				// Détection des devices d'Alexa-API qui ne sont pas des Fire TV
				$device = self::byLogicalId($item['serial'], 'alexaapi');
				if (!is_object($device)) {
					$device = self::createNewDevice($item['name'], $item['serial']);
					$numNewDevices++;
				}
				// Update device configuration
				log::add('alexaapi_scan', 'debug', '*** [Plugin Alexa-API       ] ->> détection de ' . $device->getName());
			}
				$device->setConfiguration('device', $item['name']);
				$device->setConfiguration('type', $item['type']);
				$device->setConfiguration('devicetype', "Echo");
				$device->setConfiguration('family', $item['family']);
				$device->setConfiguration('members', $item['members']);
				$device->setConfiguration('capabilities', $item['capabilities']);
				try {
					$device->save();
				} catch (Exception $e) {
					$device->setName($device->getName() . ' doublon ' . rand(0, 9999));
					$device->save();
				}
				$device->setStatus('online', (($item['online']) ? true : false)); //SetStatus doit être lancé après Save et Save après inutile

            $numDevices++;
        }

        // --- Mise à jour des SmartHome Devices
        $familleDisable = array(); //Famille qui sont exclues
        //array_push($familleDisable, "GROUP");
        $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/smarthomeEntities");
        $json = json_decode($json, true);
        $numsmartHome = count($json);
        if (in_array("alexasmarthome", self::listePluginsAlexa(false, true))) {
            foreach ($json as $item) {
                if (!in_array($item['providerData']['categoryType'], $familleDisable)) {
                    // Retireve the device (if already registered in Jeedom)
                    $device = alexasmarthome::byLogicalId($item['id'], 'alexasmarthome');
                    if (!is_object($device)) {
                        $device = alexasmarthome::createNewDevice($item['displayName'], $item['id']);
                        $numNewDevices++;
                    }
                    log::add('alexaapi_scan', 'debug', '[Plugin AlexasmartHome  ] ->> détection de [' . $item['id'] . "]" . $device->getName());
                    // Update device configuration
                    $device->setConfiguration('device', $item['displayName']);
                    //$device->setConfiguration('type', $item['description']); a voir si on utilise ou pas descriotion
                    $device->setConfiguration('type', $item['providerData']['deviceType']);
                    $device->setConfiguration('icon', $item['icon']['value']);
                    $device->setConfiguration('devicetype', "Smarthome");
                    $device->setConfiguration('family', $item['providerData']['categoryType']);
                    //$device->setConfiguration('members', $item['members']);
                    $device->setConfiguration('capabilities', $item['supportedProperties']);
                    // pas certain de cette partie, pas pu tester un device avec plus de deux triggers
                    if ((count($item['supportedTriggers'])) == 1)
                        $device->setConfiguration('triggers', $item['supportedTriggers'][0]);
                    else if ((count($item['supportedTriggers'])) > 1)
                        $device->setConfiguration('triggers', $item['supportedTriggers']);
                    else
                        $device->setConfiguration('triggers', "");
                    //On va mettre dispo, on traite plus tard.
                    //$device->setStatus('online', (($item['online'])?true:false));
                    try {
                        $device->save();
                    } catch (Exception $e) {
                        $device->setName($device->getName() . ' doublon ' . rand(0, 9999));
                        $device->save();
                    }
                    $device->setStatus('online', 'true');
                    $numDevices++;
                }
            }

            log::add('alexaapi_scan', 'debug', '*************************** Lancement du Scan Alexa-smartHome (cf. log smartHome_scan)***');
            self::scanAmazonSmartHome();
        }


        log::add('alexaapi_scan', 'debug', '*************************** Fin       du Scan Alexa-API ***********************************');
        config::save("numsmartHome", $numsmartHome, "alexaapi");
        config::save("numFireTV", $numFireTV, "alexaapi");
        config::save("numAudioPlayer", $numAudioPlayer, "alexaapi");
        config::save("numDevices", $numDevices, "alexaapi");
        log::add('alexaapi_scan', 'debug', $numAudioPlayer . ' Players / ' . $numFireTV . ' Fire TV / ' . $numsmartHome . ' smartHome/ ' . $numDevices . ' numDevices  -> enregistré dans Config');

        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Scan terminé. ' . $numDevices . ' équipements mis a jour dont ' . $numNewDevices . " ajouté(s). Appuyez sur F5 si votre écran ne s'est pas actualisé", __FILE__)));
    }

    public static function ajouteAmazonSmartHome($item)
    { // Permet d'ajouter un device non détecté par le scan (type un thermomètre)

        //log::add('alexasmarthome_scan', 'debug', "************************************Ajout d'un device smartHome non détecté par le scan ***********************************");
        //$json = json_decode($json, true);
        //log::add('alexasmarthome_scan', 'debug', 'json:' . json_encode($item));
		
		
		                    // Retireve the device (if already registered in Jeedom)
                    $device = alexasmarthome::byLogicalId($item['entityId'], 'alexasmarthome');

                    if (!is_object($device)) {
                        $device = alexasmarthome::createNewDevice($item['friendlyName'], $item['entityId']);
                        $numNewDevices++;
						log::add('alexasmarthome_scan', 'debug', 'Ajout sup de [' . $item['entityId'] . "]" );// ou applianceKey
                    }
                    log::add('alexaapi_scan', 'debug', '[Plugin AlexasmartHome  ] ->> détection2 de [' . $item['entityId'] . "]" . $device->getName());// ou applianceKey

                    //$device->setConfiguration('device', $item['displayName']);
                    //$device->setConfiguration('type', $item['description']); a voir si on utilise ou pas descriotion
                    $device->setConfiguration('type', $item['deviceType']);
                    $device->setConfiguration('applianceId', $item['applianceId']);
                    //$device->setConfiguration('icon', $item['icon']['value']);
                    $device->setConfiguration('devicetype', "Smarthome");
                    $device->setConfiguration('typeSmartHome', $item['applianceTypes']['0']); // faudra voir s'il y a plusieurs types
                    //$device->setConfiguration('members', $item['members']);
					$capabilitiesjson=$item['capabilities'];
					$capabilities=[];        
					        foreach ($capabilitiesjson as $value) {
								//log::add('alexasmarthome_scan', 'debug', '[interfaceName  ] ->> [' . $value['interfaceName'] . "]");
								array_push($capabilities, $value['interfaceName']);
							}
					//log::add('alexasmarthome_scan', 'debug', '[capabilities  ] ->> [' . json_encode($capabilities) . "]" . $device->getName());// ou applianceKey
                    $device->setConfiguration('capabilitiesSmartHome', $capabilities);
					$manufacturerName=$item['manufacturerName'];
					if ($manufacturerName=="Amazon Inc.") $manufacturerName="Amazon";
					$device->setConfiguration('manufacturerName', $manufacturerName);
					$device->setConfiguration('friendlyDescription', $item['friendlyDescription']);

					try {
						$device->save();
					} catch (Exception $e) {
						$device->setName($device->getName() . ' doublon ' . rand(0, 9999));
						$device->save();
					}
			
					
    }

    public static function scanAmazonSmartHome()
    { // Permet de faire le lien entre entityId et applianceId

        log::add('alexasmarthome_scan', 'debug', '************************************Lancement du Scan Device smartHome***********************************');
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != "ok") {
            event::add('jeedom::alert', array('level' => 'danger', 'page' => 'alexaapi', 'message' => __('Cookie Amazon Absent, allez dans la Configuration du plugin', __FILE__),));
            return;
        }
        $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/smarthomeDevices");
        log::add('alexasmarthome_scan', 'debug', 'On récupère : '."http://" . config::byKey('internalAddr') . ":3456/smarthomeDevices");
        $json = json_decode($json, true);
        log::add('alexasmarthome_scan', 'debug', 'json:' . $json);
        foreach ($json as $key => $value) {
            log::add('alexasmarthome_scan', 'debug', 'item:' . json_encode($value));
            foreach ($value as $key2 => $value2) {
                log::add('alexasmarthome_scan', 'debug', '	item2:' . json_encode($value2));
                foreach ($value2 as $key3 => $value3) {
                    log::add('alexasmarthome_scan', 'debug', '		item3:' . json_encode($value3));
                    // On traite la boucle de amazonBridgeDetails
                    if (isset($value3['amazonBridgeDetails'])) {
                        log::add('alexasmarthome_scan', 'debug', '			item3bis:' . json_encode($value3['amazonBridgeDetails']));
                        foreach ($value3['amazonBridgeDetails'] as $key4 => $value4) {
                            log::add('alexasmarthome_scan', 'debug', '				item4:' . json_encode($value4));
                            foreach ($value4 as $value5) {
                                log::add('alexasmarthome_scan', 'debug', '					item5:' . json_encode($value5));
                                foreach ($value5 as $key6 => $value6) {
                                    log::add('alexasmarthome_scan', 'debug', '						item6:' . json_encode($value6));
                                    foreach ($value6 as $value7) {
                                        log::add('alexasmarthome_scan', 'debug', '							item7:' . json_encode($value7));
                                        log::add('alexasmarthome_scan', 'debug', '							==> applianceId:' . json_encode($value7['applianceId']));
                                        log::add('alexasmarthome_scan', 'debug', '							==> entityId:' . json_encode($value7['entityId']));
                                        log::add('alexasmarthome_scan', 'debug', '							==> friendlyName:' . json_encode($value7['friendlyName']));
                                        log::add('alexaapi_scan', 'debug', 'smarthomeDevices ==> ' . json_encode($value7['friendlyName']) . '=[' . json_encode($value7['entityId']) . ']');
                                        $Onatrouvelelien = false;
                                        foreach (eqLogic::byType('alexasmarthome', true) as $alexasmarthome) {
                                            if ($alexasmarthome->getLogicalId() == $value7['entityId']) {
                                                $alexasmarthome->setConfiguration('applianceId', $value7['applianceId']);
                                                log::add('alexasmarthome_scan', 'info', ' ╔══════════════════════['.$alexasmarthome->getName().']═══════════════════════════════════════════════════════════════════════════');
                                                log::add('alexasmarthome_scan', 'info', ' ║ Lien entre Alexa-API ('.$alexasmarthome->getName().') et smarHome ('.json_encode($value7['friendlyName']).') trouvé et mis à jour');
                                                log::add('alexasmarthome_scan', 'info', ' ║ Equipement trouvé et mis à jour');
												//Détection du lien entre entityId (protocole Alexa-API) et applianceId (protocole smartHome)
                                                log::add('alexasmarthome_scan', 'info', ' ║ '.json_encode($value7['entityId']) . ' <═> ' . json_encode($value7['applianceId']));
                                                log::add('alexasmarthome_scan', 'info', ' ║         protocole Alexa-API            <═>             protocole smartHome');
                                                log::add('alexasmarthome_scan', 'info', ' ╚═════════════════════════════════════════════════════════════════════════════════════════════════════════════════════');
												$manufacturerName=$value7['manufacturerName'];
												if ($manufacturerName=="Amazon Inc.") $manufacturerName="Amazon";
												if ($manufacturerName=="FREEBOX") $manufacturerName="Freebox";
												$alexasmarthome->setConfiguration('manufacturerName', $manufacturerName);
												$alexasmarthome->setConfiguration('friendlyDescription', $value7['friendlyDescription']);
												// Accessible sur le réseau sera : object►applianceNetworkState►reachability donne la valeur REACHABLE
												$capabilitiesjson=$value7['capabilities'];
												$capabilities=[];        
													foreach ($capabilitiesjson as $value) {
														//log::add('alexasmarthome_scan', 'debug', '[interfaceName  ] ->> [' . $value['interfaceName'] . "]");
														array_push($capabilities, $value['interfaceName']);
													}
												//log::add('alexasmarthome_scan', 'debug', '%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%[capabilities] ->> [' . json_encode($capabilities) . "]" . $alexasmarthome->getName());// ou applianceKey
												$alexasmarthome->setConfiguration('capabilitiesSmartHome', $capabilities);
												//$alexasmarthome->setConfiguration('capabilitiesSmartHome', "coucou");
												//$capabilitiesrecup=$alexasmarthome->getConfiguration('capabilities');
												//$alexasmarthome->setConfiguration('capabilitiesSmartHome', $capabilitiesrecup);
												
												
												$alexasmarthome->setConfiguration('typeSmartHome', $value7['applianceTypes']['0']); // faudra voir s'il y a plusieurs types

												
                                                $alexasmarthome->save();
                                                $Onatrouvelelien = true;
                                            }
                                        }
                                        if (!($Onatrouvelelien)) {
                                                log::add('alexasmarthome_scan', 'info', ' ╔══════════════════════['.json_encode($value7['friendlyName']).']═══════════════════════════════════════════════════════════════════════════');
                                                log::add('alexasmarthome_scan', 'info', ' ║ Equipement ajouté.');
												//Détection du lien entre entityId (protocole Alexa-API) et applianceId (protocole smartHome)
                                                log::add('alexasmarthome_scan', 'info', ' ║ '.json_encode($value7['entityId']) . ' <═> ' . json_encode($value7['applianceId']));
                                                log::add('alexasmarthome_scan', 'info', ' ║         protocole Alexa-API            <═>             protocole smartHome');
                                                log::add('alexasmarthome_scan', 'info', ' ╚═════════════════════════════════════════════════════════════════════════════════════════════════════════════════════');
                                            //log::add('alexasmarthome_scan', 'info', '!!!!!!!!!!!!!!!!FAUT AJOUTER UN DEVICE !!!!!!!!!!');
											self::ajouteAmazonSmartHome($value7);
										}
										if (json_encode($value7['applianceId']) != json_encode($value7['mergedApplianceIds']['0'])) {
                                                log::add('alexasmarthome_scan', 'info', ' ╔══════════════════════['.json_encode($value7['friendlyName']).']═══════════════════════════════════════════════════════════════════════════');
                                                log::add('alexasmarthome_scan', 'info', ' ║ ' . json_encode($value7['applianceId']) <═> json_encode($value7['mergedApplianceIds']['0']));
                                                log::add('alexasmarthome_scan', 'info', ' ╚═════════════════════════════════════════════════════════════════════════════════════════════════════════════════════');
                                            //log::add('alexasmarthome_scan', 'info', '!!!!!!!!!!!!!!!!FAUT AJOUTER UN DEVICE !!!!!!!!!!');
											self::ajouteAmazonSmartHome($value7['mergedApplianceIds']['0']);
										}											
                                    }
                                }
                            }
                        }
                    }
                    // On traite la boucle de applianceGroups
                    if (isset($value3['applianceGroups'])) {
                        log::add('alexasmarthome_scan', 'debug', 'item3bis:' . json_encode($value3['applianceGroups']));
                    }
                    //foreach ($value3 as $key4 => $value4) {
                }
            }
        }
	alexasmarthome::metAjourFabriquantsDesactives();	
    }

    private static function createNewDevice($deviceName, $deviceSerial)
    {
        $defaultRoom = intval(config::byKey('defaultParentObject', "alexaapi", '', true));
        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Ajout de "' . $deviceName . '"', __FILE__),));
        $newDevice = new alexaapi();
        $newDevice->setName($deviceName);
        $newDevice->setLogicalId($deviceSerial);
        $newDevice->setEqType_name('alexaapi');
        $newDevice->setIsVisible(1);
        if ($defaultRoom) $newDevice->setObject_id($defaultRoom);
        // JUSTE pour SIGALOU pour aider au dev
        if (substr($deviceName, 0, 7) == "Piscine")
            $newDevice->setObject_id('15');
        $newDevice->setDisplay('height', '500');
        $newDevice->setConfiguration('device', $deviceName);
        $newDevice->setConfiguration('serial', $deviceSerial);
        $newDevice->setIsEnable(1);
        return $newDevice;
    }

    public function hasCapaorFamilyorType($thisCapa)
    {

        // Si c'est la bonne famille, on dit OK tout de suite
        $family = $this->getConfiguration('family', "");
        if ($thisCapa == $family) return true; // ajouté pour filtrer sur la famille (pour les groupes par exemple)
        // Si c'est le bon type, on dit OK tout de suite
        $type = $this->getConfiguration('type', "");
        if ($thisCapa == $type) return true; //
        $capa = $this->getConfiguration('capabilities', "");
        if (((gettype($capa) == "array" && in_array($thisCapa, $capa))) || ((gettype($capa) == "string" && strpos($capa, $thisCapa) !== false))) {
            if ($thisCapa == "REMINDERS" && $type == "A15ERDAKK5HQQG") return false;
            return true;
        } else {
            return false;
        }
    }

    public function sortByPRECEDENTAVIRER($field, &$array, $direction = 'asc')
    {
        usort($array, create_function('$a, $b', '
		$a = $a["' . $field . '"];
		$b = $b["' . $field . '"];
		if ($a == $b) return 0;
		$direction = strtolower(trim($direction));
		return ($a ' . ($direction == 'desc' ? '>' : '<') . ' $b) ? -1 : 1;
    	'));
        return true;
    }

    /*
    public function sortBy($field, &$array) {
        usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];
        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
        '));
        return true;
    }*/

    public function refresh()
    { //$_routines c'est pour éviter de charger les routines lors du scan
        $deamon_info = alexaapi::deamon_info();
        if ($deamon_info['state'] != 'ok') return false;
        $devicetype = $this->getConfiguration('devicetype');
        log::add('alexaapi', 'info', 'Refresh du device ' . $this->getName() . ' (' . $devicetype . ')');
        $widgetPlayer = ($devicetype == "Player");
        $widgetSmarthome = ($devicetype == "Smarthome");
        $widgetPlaylist = ($devicetype == "PlayList");
        $widgetEcho = (!($widgetPlayer || $widgetSmarthome || $widgetPlaylist));
        $device = str_replace("_player", "", $this->getConfiguration('serial'));

        if ($widgetEcho) {
            $trouveRoutines = false;
            if (config::byKey("listRoutinesValidFin", "alexaapi", time() - 20) < time()) { // On regarde si on va chercher la liste des routines sur Amazon ou dans la Config
                log::add('alexaapi', 'debug', 'execute : refresh routines (' . $this->getName() . ') avec la requète http://' . config::byKey('internalAddr') . ':3456/routines');
                $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/routines");
                $json = json_decode($json, true);    // Met à jour la liste des routines des commandes action "routine"
                //self::sortBy('utterance', $json); //Supprimé car j'arrive pas à trier sans erreur

                $ListeDesRoutines = [];
                foreach ($json as $item) {
                    //if ($item['utterance'] != '')
                    if (isset($item['utterance']))
                        $ListeDesRoutines[] = $item['creationTimeEpochMillis'] . '|' . $item['utterance'];
                    else {
                        if (isset($item['triggerTime']) && ($item['triggerTime'] != '')) {
                            $resultattriggerTime = substr($item['triggerTime'], 0, 2) . ":" . substr($item['triggerTime'], 2, 2);
                            $ListeDesRoutines[] = $item['creationTimeEpochMillis'] . '|' . $resultattriggerTime;
                        }
                    }
                }
                $ListeDesRoutines_string = join(';', $ListeDesRoutines);
                config::save("listRoutines", $ListeDesRoutines_string, "alexaapi");
                config::save("listRoutinesValidDebut", time(), "alexaapi");
                config::save("listRoutinesValidFin", time() + 86400, "alexaapi");
                log::add('alexaapi', 'debug', '╠═ Enregistre Routines dans Config sur plugin: ' . $ListeDesRoutines_string);
                $ouRecupRoutines = "Amazon";
            } else {
                $ListeDesRoutines_string = config::byKey("listRoutines", "alexaapi", "");
                //log::add('alexaamazonmusic', 'debug', '╚═> ListeDesPlaylists_string : '.$ListeDesPlaylists_string);
                if ($ListeDesRoutines_string != "") $trouveRoutines = true;
                $ouRecupRoutines = "Configuation";
            }


            $cmd = $this->getCmd(null, 'routine');
            if (is_object($cmd)) {
                $cmd->setConfiguration('listValue', $ListeDesRoutines_string);
                $cmd->save();
            }


            // On va sauvegarder la valeur de chaque ANCIENNE prochaine Alarme/Rappel/Minuteur ...
            $maintenant = date("i H d m w Y"); //40 18 20 11 3 2019

            $cmd = $this->getCmd(null, 'whennextalarminfo');
            if (is_object($cmd)) $whennextalarminfo_derniereValeur = $cmd->execCmd();
            $cmd = $this->getCmd(null, 'whennextmusicalalarminfo');
            if (is_object($cmd)) $whennextmusicalalarminfo_derniereValeur = $cmd->execCmd();
            $cmd = $this->getCmd(null, 'whennextreminderinfo');
            if (is_object($cmd)) $whennextreminderinfo_derniereValeur = $cmd->execCmd();
            $cmd = $this->getCmd(null, 'whennexttimerinfo');
            if (is_object($cmd)) $whennexttimerinfo_derniereValeur = $cmd->execCmd();
            //log::add('alexaapi_node', 'debug', '--------------------------------------------->>maintenant:'.$maintenant);
            //log::add('alexaapi_node', 'debug', '---->whennextalarminfo:'.$whennextalarminfo_derniereValeur);
            //log::add('alexaapi_node', 'debug', '---->whennexttimerinfo:'.$whennexttimerinfo_derniereValeur);

            try {
                //log::add('alexaapi', 'info', 'coucou');
                foreach ($this->getCmd('action') as $cmd) {
                    //log::add('alexaapi', 'info', 'Test refresh de la commande '.$cmd->getName().' valeur -> '.$cmd->getConfiguration('RunWhenRefresh', 0));

                    if ($cmd->getConfiguration('RunWhenRefresh', 0) != '1') {
                        //log::add('alexaapi', 'info', 'NON pour '.$cmd->getName());
                        continue; // si le lancement n'est pas prévu, ça va au bout de la boucle foreach
                    }
                    //log::add('alexaapi', 'info', 'OUI pour ' . $cmd->getName());
                    $value = $cmd->execute();
                }
            } catch (Exception $exc) {
                log::add('alexaapi', 'warning', __('Erreur pour ', __FILE__) . $this->getHumanName() . ' : ' . $exc->getMessage());
            }

            /*
            // On va sauvegarder la valeur de chaque NOUVELLE prochaine Alarme/Rappel/Minuteur ...
            $cmd = $this->getCmd(null, 'whennextalarminfo'); if (is_object($cmd)) $whennextalarminfo_actuelleValeur=$cmd->execCmd();
            $cmd = $this->getCmd(null, 'whennextmusicalalarminfo'); if (is_object($cmd)) $whennextmusicalalarminfo_actuelleValeur=$cmd->execCmd();
            $cmd = $this->getCmd(null, 'whennextreminderinfo'); if (is_object($cmd)) $whennextreminderinfo_actuelleValeur=$cmd->execCmd();
            $cmd = $this->getCmd(null, 'whennexttimerinfo'); if (is_object($cmd)) $whennexttimerinfo_actuelleValeur=$cmd->execCmd();
            //log::add('alexaapi_node', 'debug', '---->whennextalarminfo2:'.$whennextalarminfo_actuelleValeur);
            //log::add('alexaapi_node', 'debug', '---->whennexttimerinfo2:'.$whennexttimerinfo_actuelleValeur);

                if (($whennextalarminfo_derniereValeur != $whennextalarminfo_actuelleValeur) && ($whennextalarminfo_derniereValeur==$maintenant))
                {
                        log::add('alexaapi_node', 'debug', '-------------------------------->today:ALLLLLAAARRRRMMMMMMEEEE'.$today);

                }

                if (($whennexttimerinfo_derniereValeur != $whennexttimerinfo_actuelleValeur) && ($whennexttimerinfo_derniereValeur==$maintenant))
                {
                        log::add('alexaapi_node', 'debug', '-------------------------------->today:TTTIIIIMMMMMEEEERRRR'.$today);

                }
*/
        }
    }

    public function updateCmd($forceUpdate, $LogicalId, $Type, $SubType, $RunWhenRefresh, $Name, $IsVisible, $title_disable, $setDisplayicon, $infoNameArray, $setTemplate_lien, $request, $infoName, $listValue, $Order, $Test)
    {
        if ($Test) {
            //log::add('alexaapi', 'info', 'ajout commande FORCAGE de ' . $LogicalId);
            try {
                $cmd = $this->getCmd(null, $LogicalId);


                if ((!is_object($cmd)) || $forceUpdate) {
                    //log::add('alexaapi', 'info', 'ajout commande FORCAGE forceUpdate :' . $forceUpdate);
                    if (!is_object($cmd)) $cmd = new alexaapiCmd();
                    $cmd->setType($Type);
                    $cmd->setLogicalId($LogicalId);
                    $cmd->setSubType($SubType);
                    $cmd->setEqLogic_id($this->getId());
                    if (empty($Name)) $Name = $LogicalId; // déplacé le 19/09/2020
                    $cmd->setName($Name);
                    $cmd->setIsVisible((($IsVisible) ? 1 : 0));
                    if (!empty($setTemplate_lien)) {
                        $cmd->setTemplate("dashboard", $setTemplate_lien);
                        $cmd->setTemplate("mobile", $setTemplate_lien);
                    }
                    if (!empty($setDisplayicon)) $cmd->setDisplay('icon', '<i class="' . $setDisplayicon . '"></i>');
                    if (!empty($request)) $cmd->setConfiguration('request', $request);
                    if (!empty($infoName)) $cmd->setConfiguration('infoName', $infoName);
                    if (!empty($infoNameArray)) $cmd->setConfiguration('infoNameArray', $infoNameArray);
                    if (!empty($listValue)) $cmd->setConfiguration('listValue', $listValue);
                    $cmd->setConfiguration('RunWhenRefresh', $RunWhenRefresh);
                    $cmd->setDisplay('title_disable', $title_disable);
					$cmd->setDisplay('showNameOndashboard', !$title_disable);

                    $cmd->setOrder($Order);
                    //cas particulier
                    if (($LogicalId == 'speak') || ($LogicalId == 'announcement')) {
                        //$cmd->setDisplay('title_placeholder', 'Options');
                        $cmd->setDisplay('message_placeholder', 'Phrase à faire lire par Alexa');
                    }
                    if (($LogicalId == 'reminder')) {
                        //$cmd->setDisplay('title_placeholder', 'Options');
                        $cmd->setDisplay('message_placeholder', 'Texte du rappel');
                    }
                    if (($LogicalId == 'volumeinfo') || ($LogicalId == 'volume')) {
                        $cmd->setConfiguration('minValue', '0');
                        $cmd->setConfiguration('maxValue', '100');
                        $cmd->setDisplay('forceReturnLineBefore', true);
                    }
                    $cmd->save(); // déplacé le 19/09/2020
                    //log::add('alexaapi', 'info', 'Enregistre Logical ID :' . $cmd->getLogicalId());
                }
            } catch (Exception $exc) {
                log::add('alexaapi', 'error', __('Erreur1 pour ', __FILE__) . ' : ' . $exc->getMessage());
            }
        } else {
            //log::add('alexaapi', 'debug', 'PAS de **'.$LogicalId.'*********************************');

            $cmd = $this->getCmd(null, $LogicalId);
            if (is_object($cmd)) {
                $cmd->remove();
            }
        }
    }


    public function postSave()
    {
        //log::add('alexaapi', 'debug', '-------------------------------postSave '.$this->getName().'***********************************');
        $F = $this->getStatus('forceUpdate'); // forceUpdate permet de recharger les commandes à valeur d'origine, mais sans supprimer/recréer les commandes
        $capa = $this->getConfiguration('capabilities', '');
        $type = $this->getConfiguration('type', '');
        if (!empty($capa)) {
            if (strstr($this->getName(), "Alexa Apps")) {
                self::updateCmd($F, 'push', 'action', 'message', false, 'Push', true, true, 'fa jeedomapp-audiospeak', null, null, 'push?text=#message#', null, null, 1, true);
                return;
            }
            $widgetEcho = ($this->getConfiguration('devicetype') == "Echo");
            $widgetPlayer = ($this->getConfiguration('devicetype') == "Player");
            $widgetSmarthome = ($this->getConfiguration('devicetype') == "Smarthome");
            $widgetPlaylist = ($this->getConfiguration('devicetype') == "PlayList");

            $cas1 = (($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && $widgetPlayer);
            $cas1bis = (($this->hasCapaorFamilyorType("AUDIO_PLAYER")) && !$widgetPlayer);
            $cas2 = (($this->hasCapaorFamilyorType("TIMERS_AND_ALARMS")) && !$widgetPlayer);
            $cas3 = (($this->hasCapaorFamilyorType("REMINDERS")) && !$widgetPlayer);
            $cas4 = (($this->hasCapaorFamilyorType("REMINDERS")) && !$widgetSmarthome);
            $cas5 = (($this->hasCapaorFamilyorType("VOLUME_SETTING")) || ($this->hasCapaorFamilyorType("SOUND_SETTINGS")));
            $cas6 = ($cas5 && (!$this->hasCapaorFamilyorType("WHA")));
            $cas7 = ((!$this->hasCapaorFamilyorType("WHA")) && ($this->getConfiguration('devicetype') != "Player") && ((!$this->hasCapaorFamilyorType("FIRE_TV")) || ($this->hasCapaorFamilyorType("A2JKHJ0PX4J3L3"))) && !$widgetSmarthome && (!$this->hasCapaorFamilyorType("AMAZONMOBILEMUSIC_ANDROID")));
            $cas8 = (($this->hasCapaorFamilyorType("turnOff")) && $widgetSmarthome);
            $cas9 = ($this->hasCapaorFamilyorType("WHA") && $widgetEcho);
            $cas10 = ($this->hasCapaorFamilyorType("CLOCK_FORMAT_24_HR") && $widgetEcho); // Echo Dot avec Horloge
            $false = false;

            // Volume on traite en premier car c'est fonction de WHA
            if ($cas6) self::updateCmd($F, 'volume', 'action', 'slider', false, 'Volume', true, true, null, null, 'alexaapi::volume', 'volume?value=#slider#', null, null, 27, $cas6);
            else       self::updateCmd($F, 'volume', 'action', 'slider', false, 'Volume', false, true, 'fas fa-volume-up', null, 'alexaapi::volume', 'volume?value=#slider#', null, null, 27, $cas9);

            self::updateCmd($F, 'allDeviceVolumes', 'action', 'other', false, 'Actualise tous les volumes', false, true, 'fas fa-sync', null, null, 'allDeviceVolumes', null, null, 80, $cas9);


			// Pour l'echo avec horloge
            self::updateCmd($F, 'clockon', 'action', 'other', false, 'Afficher Heure', true, true, 'fas fa-toggle-on', null, null, 'DisplayPower?value=ON', null, null, 80, $cas10);
            self::updateCmd($F, 'clockoff', 'action', 'other', false, 'Eteindre Heure', true, true, 'fas fa-toggle-off', null, null, 'DisplayPower?value=OFF', null, null, 81, $cas10);

            //Anciennes functions a supprimer si elles existent encore
            self::updateCmd($F, 'musicalalarmmusicentity', 'action', 'other', true, 'musicalalarmmusicentity', false, false, null, null, null, 'musicalalarmmusicentity?position=1', 'Musical Alarm Music', null, 1, $false);
            self::updateCmd($F, 'whennextmusicalalarm', 'action', 'other', true, 'Next Musical Alarm When', false, false, 'fas fa-bell', null, null, 'whennextmusicalalarm?position=1', 'Next Musical Alarm Hour', null, 1, $false);
            self::updateCmd($F, 'whennextreminder', 'action', 'other', true, 'Next Reminder When', false, false, null, null, null, 'whennextreminder?position=1', 'Next Reminder Hour', null, 33, $false);
            self::updateCmd($F, 'whennextreminderlabel', 'action', 'other', true, 'whennextreminderlabel', false, false, null, null, null, 'whennextreminderlabel?position=1', 'Reminder Label', null, 34, $false);

            self::updateCmd($F, 'interactioninfo', 'info', 'string', false, 'Dernier dialogue avec Alexa', true, false, null, null, 'alexaapi::interaction', null, null, null, 2, $cas7);
            self::updateCmd($F, 'bluetoothDevice', 'info', 'string', false, 'Est connecté en Bluetooth', true, false, null, null, 'alexaapi::interaction', null, null, null, 2, $cas7);
            self::updateCmd($F, 'updateallalarms', 'action', 'other', true, 'UpdateAllAlarms', false, false, null, ["musicalalarmmusicentityinfo", "whennextalarminfo", "whennextmusicalalarminfo", "whennextreminderinfo", "whennexttimerinfo", "whennextreminderlabelinfo"], null, 'updateallalarms', null, null, 2, $cas2);
            self::updateCmd($F, 'deleteReminder', 'action', 'message', false, 'Supprimer un rappel', false, false, 'maison-poubelle', null, null, 'deleteReminder?id=#id#', null, null, 2, $cas3);
            self::updateCmd($F, 'subText2', 'info', 'string', false, null, true, false, null, null, 'alexaapi::subText2', null, null, null, 2, $cas1);
            self::updateCmd($F, 'subText1', 'info', 'string', false, null, true, false, null, null, 'alexaapi::title', null, null, null, 4, $cas1);
            self::updateCmd($F, 'url', 'info', 'string', false, null, true, false, null, null, 'alexaapi::image', null, null, null, 5, $cas1);
            self::updateCmd($F, 'title', 'info', 'string', false, null, true, false, null, null, 'alexaapi::title', null, null, null, 9, $cas1);
            self::updateCmd($F, 'previous', 'action', 'other', false, 'Previous', true, true, 'fas fa-step-backward', null, null, 'command?command=previous', null, null, 16, $cas1);
            self::updateCmd($F, 'pause', 'action', 'other', false, 'Pause', true, true, 'fas fa-pause', null, null, 'command?command=pause', null, null, 17, $cas1);
            self::updateCmd($F, 'play', 'action', 'other', false, 'Play', true, true, 'fas fa-play', null, null, 'command?command=play', null, null, 18, $cas1);
            self::updateCmd($F, 'next', 'action', 'other', false, 'Next', true, true, 'fas fa-step-forward', null, null, 'command?command=next', null, null, 19, $cas1);
            self::updateCmd($F, 'multiplenext', 'action', 'other', false, 'Multiple Next', true, true, 'fas fa-step-forward', null, null, 'multiplenext?text=#message#', null, null, 19, $cas1);
            self::updateCmd($F, 'providerName', 'info', 'string', false, 'Fournisseur de musique :', true, true, 'loisir-musical7', null, null, null, null, null, 20, $cas1);
            self::updateCmd($F, 'contentId', 'info', 'string', false, 'Amazon Music Id', false, true, 'loisir-musical7', null, null, null, null, null, 21, $cas1);
            self::updateCmd($F, 'routine', 'action', 'select', false, 'Lancer une routine', true, false, null, null, 'alexaapi::list', 'routine?routine=#select#', null, 'Lancer Refresh|Lancer Refresh', 21, $cas3);
            self::updateCmd($F, 'playList', 'action', 'select', false, 'Ecouter une playlist', true, false, null, null, 'alexaapi::list', 'playlist?playlist=#select#', null, 'Lancer Refresh|Lancer Refresh', 24, $cas1);
            self::updateCmd($F, 'radio', 'action', 'select', false, 'Ecouter une radio', true, false, null, null, 'alexaapi::list', 'radio?station=#select#', null, 's2960|Nostalgie;s6617|RTL;s6566|Europe1', 25, $cas1);
            self::updateCmd($F, 'playMusicTrack', 'action', 'select', false, 'Ecouter une piste musicale', true, false, null, null, 'alexaapi::list', 'playmusictrack?trackId=#select#', null, '53bfa26d-f24c-4b13-97a8-8c3debdf06f0|Piste1;7b12ee4f-5a69-4390-ad07-00618f32f110|Piste2', 26, $cas1);

            self::updateCmd($F, '0', 'action', 'other', false, '0', true, true, null, null, null, 'volume?value=0', null, null, 1, $cas9);
            self::updateCmd($F, 'volume20', 'action', 'other', false, '20', true, true, null, null, null, 'volume?value=20', null, null, 2, $cas9);
            self::updateCmd($F, 'volume40', 'action', 'other', false, '40', true, true, null, null, null, 'volume?value=40', null, null, 3, $cas9);
            self::updateCmd($F, 'volume60', 'action', 'other', false, '60', true, true, null, null, null, 'volume?value=60', null, null, 4, $cas9);
            self::updateCmd($F, 'volume80', 'action', 'other', false, '80', true, true, null, null, null, 'volume?value=80', null, null, 5, $cas9);
            self::updateCmd($F, 'volume100', 'action', 'other', false, '100', true, true, null, null, null, 'volume?value=100', null, null, 6, $cas9);
            self::updateCmd($F, 'volumeinfo', 'info', 'string', false, 'Volume Info', false, false, 'fas fa-volume-up', null, null, null, null, null, 28, $cas5);
            self::updateCmd($F, 'isMutedinfo', 'info', 'binary', false, 'Muet Info', false, false, 'fas fa-volume-up', null, 'alexaapi::isMutedinfo', null, null, null, 29, $cas5);
            self::updateCmd($F, 'whennextalarminfo', 'info', 'string', false, 'Prochaine Alarme', true, false, null, null, 'alexaapi::alarm', null, null, null, 29, $cas2);
            self::updateCmd($F, 'whennextmusicalalarminfo', 'info', 'string', false, 'Prochaine Alarme Musicale', true, false, null, null, 'alexaapi::alarmmusical', null, null, null, 30, $cas2);
            self::updateCmd($F, 'musicalalarmmusicentityinfo', 'info', 'string', false, 'Musical Alarm Music', true, false, 'loisir-musical7', null, 'alexaapi::alarmmusicalmusic', null, null, null, 31, $cas2);
            self::updateCmd($F, 'whennextreminderinfo', 'info', 'string', false, 'Prochain Rappel', true, false, null, null, 'alexaapi::reminder', null, null, null, 32, $cas3);
            self::updateCmd($F, 'whennexttimerinfo', 'info', 'string', false, 'Prochain Minuteur', true, false, null, null, 'alexaapi::timer', null, null, null, 32, $cas3);
            self::updateCmd($F, 'whennextreminderlabelinfo', 'info', 'string', false, 'Reminder Label', true, false, 'loisir-musical7', null, 'alexaapi::alarmmusicalmusic', null, null, null, 35, $cas2);
            self::updateCmd($F, 'alarm', 'action', 'select', false, 'Lancer une alarme', true, false, null, null, 'alexaapi::list', 'alarm?when=#when#&recurring=#recurring#&sound=#sound#', null, 'system_alerts_melodic_01|Alarme simple;system_alerts_melodic_01|Timer simple;system_alerts_melodic_02|A la dérive;system_alerts_atonal_02|Métallique;system_alerts_melodic_05|Clarté;system_alerts_repetitive_04|Comptoir;system_alerts_melodic_03|Focus;system_alerts_melodic_06|Lueur;system_alerts_repetitive_01|Table de chevet;system_alerts_melodic_07|Vif;system_alerts_soothing_05|Orque;system_alerts_atonal_03|Lumière du porche;system_alerts_rhythmic_02|Pulsar;system_alerts_musical_02|Pluvieux;system_alerts_alarming_03|Ondes carrées', 36, $cas3);
            //self::updateCmd ($F, 'rwd', 'action', 'other', false, 'Rwd', true, true, 'fas fa-fast-backard', null, null, 'command?command=rwd', null, null, 80, $cas1);

            //self::updateCmd ($F, 'fwd', 'action', 'other', false, 'Fwd', true, true, 'fas fa-step-forward', null, null, 'command?command=fwd', null, null, 20, $cas1);
            //self::updateCmd ($F, 'repeat', 'action', 'other', false, 'Repeat', true, true, 'fas fa-refresh', null, null, 'command?command=repeat', null, null, 25, $cas1);
            //self::updateCmd ($F, 'shuffle', 'action', 'other', false, 'Shuffle', true, true, 'fas fa-random', null, null, 'command?command=shuffle', null, null, 26, $cas1);
            self::updateCmd($F, 'playlistName', 'info', 'string', false, null, true, true, null, null, null, null, null, null, 79, $widgetPlaylist);
            //self::updateCmd ($F, 'playlistName', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 2, $widgetPlayer);
            //self::updateCmd ($F, 'songName', 'info', 'string', false, null, true, false, null, null, null, null, null, null, 79, ($widgetPlaylist || $widgetPlayer));
            self::updateCmd($F, 'playlisthtml', 'info', 'string', false, null, true, true, null, null, null, null, null, null, 79, $widgetPlaylist);
            self::updateCmd($F, 'turnOn', 'action', 'other', false, 'turnOn', true, true, "fas fa-circle", null, null, 'SmarthomeCommand?command=turnOn', null, null, 79, $cas8);
            self::updateCmd($F, 'turnOff', 'action', 'other', false, 'turnOff', true, true, "far fa-circle", null, null, 'SmarthomeCommand?command=turnOff', null, null, 79, $cas8);

            self::updateCmd($F, 'command', 'action', 'message', false, 'Command', false, true, "fas fa-play-circle", null, null, 'command?command=#select#', null, null, 79, $cas1);
            self::updateCmd($F, 'textCommand', 'action', 'message', false, 'Parler à Alexa', true, false, null, null, 'alexaapi::message', 'textCommand?text=#message#', null, null, 39, $cas1bis);
            self::updateCmd($F, 'speak', 'action', 'message', false, 'Faire parler Alexa', true, false, null, null, 'alexaapi::message', 'speak?text=#message#&volume=#volume#', null, null, 40, $cas1bis);
            self::updateCmd($F, 'speaklegacy', 'action', 'message', false, 'Faire parler Alexa (legacy)', false, false, null, null, 'alexaapi::message', 'speak?text=#message#&volume=#volume#&legacy=1', null, null, 43, $cas1bis);
            self::updateCmd($F, 'announcement', 'action', 'message', false, 'Lancer une annonce', true, true, null, null, 'alexaapi::message', 'speak?text=#message#&volume=#volume#&jingle=1', null, null, 44, $cas1bis);
            self::updateCmd($F, 'speakssml', 'action', 'message', false, 'Faire parler Alexa en SSML', false, true, null, null, 'alexaapi::message', 'speak?text=#message#&volume=#volume#&ssml=1', null, null, 42, $cas1bis);
            self::updateCmd($F, 'mediaLength', 'info', 'string', false, null, false, false, null, null, null, null, null, null, 79, $cas1);
            self::updateCmd($F, 'mediaProgress', 'info', 'string', false, null, false, false, null, null, null, null, null, null, 79, $cas1);
            self::updateCmd($F, 'state', 'info', 'string', false, null, true, false, null, null, 'alexaapi::state', null, null, null, 79, $cas1);
            //self::updateCmd ($F, 'playlistName', 'info', 'string', false, null, false, false, null, null, null, null, null, null, 2, $cas1);
            self::updateCmd($F, 'nextState', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 79, $cas1);
            self::updateCmd($F, 'previousState', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 79, $cas1);
            self::updateCmd($F, 'playPauseState', 'info', 'string', false, null, false, true, null, null, null, null, null, null, 79, $cas1);
            //self::updateCmd ($F, 'loopMode', 'info', 'string', false, null, true, false, null, null, null, null, null, null, 79, $cas1);
            //self::updateCmd ($F, 'playBackOrder', 'info', 'string', false, null, true, false, null, null, null, null, null, null, 79, $cas1);
            //self::updateCmd ($F, 'alarm', 'action', 'message', false, 'Alarm', false, true, 'fas fa-bell', null, null, 'alarm?when=#when#&recurring=#recurring#&sound=#sound#', null, null, 79, $cas2);
            self::updateCmd($F, 'deleteallalarms', 'action', 'message', false, 'Delete All Alarms', false, false, 'maison-poubelle', null, null, 'deleteallalarms?type=#type#&status=#status#', null, null, 79, $cas2);
            if ($type == "A15ERDAKK5HQQG") {
                log::add('alexaapi', 'warning', '****Rencontre du type A15ERDAKK5HQQG = Sonos Première Génération sur : ' . $this->getName());
                log::add('alexaapi', 'warning', '****On ne crée pas les commandes REMINDERS dessus car bug!');
            }
            self::updateCmd($F, 'reminder', 'action', 'message', false, 'Envoyer un rappel', true, false, null, null, 'alexaapi::message', 'reminder?text=#message#&when=#when#&recurring=#recurring#', null, null, 79, $cas3);

            self::updateCmd($F, 'onLine', 'info', 'binary', false, "En ligne", false, true, null, null, null, null, null, null, 99, true); //ajouté aout 2020


            $volinfo = $this->getCmd(null, 'volumeinfo');
            $vol = $this->getCmd(null, 'volume');
            if ((is_object($volinfo)) && (is_object($vol))) {
                $vol->setValue($volinfo->getId()); // Lien entre volume et volumeinfo
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
                        $refresh->setDisplay('icon', '<i class="fas fa-sync"></i>');
                        $refresh->setName(__('Refresh', __FILE__));
                    }
                    $refresh->setType('action');
                    $refresh->setSubType('other');
                    $refresh->setEqLogic_id($this->getId());
                    $refresh->save();
                }
            }
        } else {
            log::add('alexaapi', 'warning', 'Pas de capacité détectée sur ' . $this->getName() . ' , assurez-vous que le démon est OK');
        }

        event::add('jeedom::alert', array('level' => 'success', 'page' => 'alexaapi', 'message' => __('Mise à jour de "' . $this->getName() . '"', __FILE__),));
        $this->refresh();

        if ($widgetPlayer) {
            $device_playlist = str_replace("_player", "", $this->getConfiguration('serial')) . "_playlist"; //Nom du device de la playlist
            // Si la case "Activer le widget Playlist" est cochée, on rend le device _playlist visible sinon on le passe invisible
            $eq = eqLogic::byLogicalId($device_playlist, 'alexaapi');
            if (is_object($eq)) {
                $eq->setIsVisible((($this->getConfiguration('widgetPlayListEnable')) ? 1 : 0));
                $eq->setIsEnable((($this->getConfiguration('widgetPlayListEnable')) ? 1 : 0));
                $eq->setObject_id($this->getObject_id()); // Attribue au widget Playlist la même pièce que son Player
                $eq->save();
            }
        }


        $this->setStatus('forceUpdate', false); //dans tous les cas, on repasse forceUpdate à false

    }

    public static function dependancy_install($verbose = "false")
    {
        if (file_exists(jeedom::getTmpFolder('alexaapi') . '/dependance')) {
            return;
        }
        log::remove('alexaapi_dep');
        $_debug = 0;
        if (log::getLogLevel('alexaapi') == 100 || $verbose === "true" || $verbose === true) $_debug = 1;
        log::add('alexaapi', 'info', 'Installation des dépendances : ');
        $resource_path = realpath(dirname(__FILE__) . '/../../resources');
        return array('script' => $resource_path . '/nodejs.sh', 'log' => log::getPathToLog('alexaapi_dep'));
    }

    public function preUpdate()
    {
        //log::add('alexaapi', 'debug', '-------------------------------preUpdate '.$this->getName().'***********************************');
    }

    public function preRemove()
    {
        if ($this->getConfiguration('devicetype') == "Player") { // Si c'est un type Player, il faut supprimer le Device Playlist
            $device_playlist = str_replace("_player", "", $this->getConfiguration('serial')) . "_playlist"; //Nom du device de la playlist
            $eq = eqLogic::byLogicalId($device_playlist, 'alexaapi');
            if (is_object($eq)) $eq->remove();
        }
        //log::add('alexaapi', 'debug', '-------------------------------preRemove '.$this->getName().'***********************************');
    }

    public function preSave()
    {
        //log::add('alexaapi', 'debug', '-------------------------------preSave '.$this->getName().'***********************************');

    }

    // https://github.com/NextDom/NextDom/wiki/Ajout-d%27un-template-a-votre-plugin
    // https://jeedom.github.io/documentation/dev/fr_FR/widget_plugin

	public function toHtml($_version = 'dashboard') 
    {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$_version = jeedom::versionAlias($_version);

        //log::add('alexaapi','debug','************Début génération du Widget de '.$this->getName());
		switch ($this->getDisplay('layout::' . $_version)) {
			case 'table':
			$replace['#eqLogic_class#'] = 'eqLogic_layout_table';
			$table = self::generateHtmlTable($this->getDisplay('layout::'.$_version.'::table::nbLine', 1), $this->getDisplay('layout::'.$_version.'::table::nbColumn', 1), $this->getDisplay('layout::'.$_version.'::table::parameters'));
			$br_before = 0;
			foreach ($this->getCmd(null, null, true) as $cmd) {
				if (isset($replace['#refresh_id#']) && $cmd->getId() == $replace['#refresh_id#']) {
					continue;
				}
				$tag = '#cmd::' . $this->getDisplay('layout::'.$_version.'::table::cmd::' . $cmd->getId() . '::line', 1) . '::' . $this->getDisplay('layout::'.$_version.'::table::cmd::' . $cmd->getId() . '::column', 1) . '#';
				if ($br_before == 0 && $cmd->getDisplay('forceReturnLineBefore', 0) == 1) {
					$table['tag'][$tag] .= '<br/>';
				}
				$table['tag'][$tag] .= $cmd->toHtml($_version, '');
				$br_before = 0;
				if ($cmd->getDisplay('forceReturnLineAfter', 0) == 1) {
					$table['tag'][$tag] .= '<br/>';
					$br_before = 1;
				}
			}
			$replace['#cmd#'] = template_replace($table['tag'], $table['html']);
			break;

			default:
			$replace['#eqLogic_class#'] = 'eqLogic_layout_default';
			$cmd_html = '';
			$br_before = 0;
			$isMutedPresent=false;
			foreach ($this->getCmd(null, null, true) as $cmd) {
				//$replace['#mute#'] = "";
				if ($cmd->getLogicalId() == "isMutedinfo") {	
				//log::add('alexaapi','debug','**************************************************>>>'.$cmd->toHtml($_version, ''));
				$replace['#mute#'] = $cmd->toHtml($_version, '');
				$isMutedPresent=true;
				continue;	// Pour désactiver l'icone de isMuted 		
				}
				
				if (isset($replace['#refresh_id#']) && $cmd->getId() == $replace['#refresh_id#']) {
					continue;
				}
				if ($_version == 'dashboard' && $br_before == 0 && $cmd->getDisplay('forceReturnLineBefore', 0) == 1) {
					$cmd_html .= '<br/>';
				}
				$cmd_html .= $cmd->toHtml($_version, '');
				$br_before = 0;
				if ($_version == 'dashboard' && $cmd->getDisplay('forceReturnLineAfter', 0) == 1) {
					$cmd_html .= '<br/>';
					$br_before = 1;
				}
				
//if ($cmd->getLogicalId() == "isMutedinfo") log::add('alexaapi','debug','**************************************************-->'.$cmd->getValue());

//$replace['#mute#'] = "fas fa-volume-mute";
	//log::add('alexaapi','debug','--Début génération du Widget de *'.$cmd->getLogicalId().'*');
				
				
			}
			if (!$isMutedPresent) $replace['#mute#'] = "";
			$replace['#cmd#'] = $cmd_html;
			break;
		}

			//$replace['#mute#'] = "fas fa-volume-mute";


		$html = template_replace($replace, getTemplate('core', $_version, 'eqLogic','alexaapi'));
		return $html;
		}


}

class alexaapiCmd extends cmd
{

    public function dontRemoveCmd()
    {
        if ($this->getLogicalId() == 'refresh') {
            return true;
        }
        return false;
    }

    public function postSave()
    {
        //log::add('alexaapi', 'debug', '**********************postSave '.$this->getName().'***********************************'.$this->getLogicalId());

    }


    public function preSave()
    {
        //log::add('alexaapi', 'debug', '**********************preSave '.$this->getName().'***********************************'.$this->getLogicalId());


        if ($this->getLogicalId() == 'refresh') {
            return;
        }
        if ($this->getType() == 'action') {
            $eqLogic = $this->getEqLogic();
            $this->setConfiguration('value', 'http://' . config::byKey('internalAddr') . ':3456/' . $this->getConfiguration('request') . "&device=" . $eqLogic->getConfiguration('serial'));
        }
        $actionInfo = alexaapiCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getName());
        if (is_object($actionInfo)) $this->setId($actionInfo->getId());
        if (($this->getType() == 'action') && ($this->getConfiguration('infoName') != '')) { //Si c'est une action et que Commande info est renseigné
            $actionInfo = alexaapiCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getConfiguration('infoName'));
            if (!is_object($actionInfo)) { //C'est une commande qui n'existe pas
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

    public function execute($_options = null)
    {
        if ($this->getLogicalId() == 'refresh') {
            $this->getEqLogic()->refresh();
            return;
        }
		
		// Protection du sommeil
		if (config::byKey('dodo', 'alexaapi', 0) != "0") {
			$debut=config::byKey('dododebut', 'alexaapi', 22);	
			$fin=config::byKey('dodofin', 'alexaapi', 7);		
			$maintenant=date("H");								
			if ($fin<$debut) {
				$fin=$fin+24;
				if ($maintenant<$fin) $maintenant=$maintenant+24;
			}
			if (($maintenant>=$debut) && ($maintenant<$fin)) {
				log::add('alexaapi', 'info', ' ╔══════════════════════[Opion Protection du sommeil ACTIVEE de '.config::byKey('dododebut', 'alexaapi', 22).'h à '.config::byKey('dodofin', 'alexaapi', 7).'h]═══════════════════════════════════════════════════════════════════════════');
				log::add('alexaapi', 'info', ' ║ Commande annulée');
				log::add('alexaapi', 'info', ' ╚═════════════════════════════════════════════════════════════════════════════════════════════════════════════════════');
				return;
			}
		}

		
		

        $request = $this->buildRequest($_options);
        log::add('alexaapi', 'info', 'Envoi de ' . $request); //Request : http://192.168.0.21:3456/volume?value=50&device=G090LF118173117U
        $request_http = new com_http($request);
        $request_http->setAllowEmptyReponse(true); //Autorise les réponses vides
        if ($this->getConfiguration('noSslCheck') == 1) $request_http->setNoSslCheck(true);
        if ($this->getConfiguration('doNotReportHttpError') == 1) $request_http->setNoReportError(true);
        if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true) { // option non activée
            $request_http->setNoReportError(true);
            $request_http->exec(0.1, 1);
            return;
        }
        $result = $request_http->exec($this->getConfiguration('timeout', 3), $this->getConfiguration('maxHttpRetry', 3)); //Time out à 3s 3 essais
        if (!$result) throw new Exception(__('Serveur injoignable', __FILE__));
        // On traite la valeur de resultat (dans le cas de whennextalarm par exemple)
        $resultjson = json_decode($result, true);
        if (isset($resultjson['value'])) $value = $resultjson['value'];
        else $value = "";
        if (isset($resultjson['detail'])) $detail = $resultjson['detail'];
        else $detail = "";
        //log::add('alexaapi', 'info', 'resultjson:'.json_encode($resultjson));
        // Ici, on va traiter une commande qui n'a pas été executée correctement (erreur type "Connexion Close")
        if (($value == "Connexion Close") || ($detail == "Unauthorized")) {
            //$value = $resultjson['value'];
            //$detail = $resultjson['detail'];
            log::add('alexaapi', 'debug', '**On traite ' . $value . $detail . ' Connexion Close** dans la Class');
            sleep(6);
            if (ob_get_length()) {
                ob_end_flush();
                flush();
            }
            log::add('alexaapi', 'debug', '**On relance ' . $request);
            $result = $request_http->exec($this->getConfiguration('timeout', 2), $this->getConfiguration('maxHttpRetry', 3));
            if (!result) throw new Exception(__('Serveur injoignable', __FILE__));
            $jsonResult = json_decode($json, true);
            if (!empty($jsonResult)) throw new Exception(__('Echec de l\'execution: ', __FILE__) . '(' . $jsonResult['title'] . ') ' . $jsonResult['detail']);
            $resultjson = json_decode($result, true);
            $value = $resultjson['value'];
        }

		if (isset($resultjson['volumes'])) self::decodeTousLesVolumes($resultjson['volumes']); //Lancé dans le cas de la commande allDeviceVolumes

        if (($this->getType() == 'action') && (is_array($this->getConfiguration('infoNameArray')))) {
            foreach ($this->getConfiguration('infoNameArray') as $LogicalIdCmd) {
                $cmd = $this->getEqLogic()->getCmd(null, $LogicalIdCmd);
                if (is_object($cmd)) {
                    if(array_key_exists('0', $resultjson)){$this->getEqLogic()->checkAndUpdateCmd($LogicalIdCmd, $resultjson[0][$LogicalIdCmd]);}
                    //log::add('alexaapi', 'info', $LogicalIdCmd.' prévu dans infoNameArray de '.$this->getName().' trouvé ! '.$resultjson[0]['whennextmusicalalarminfo'].' OK !');
                } else {
                    log::add('alexaapi', 'warning', $LogicalIdCmd . ' prévu dans infoNameArray de ' . $this->getName() . ' mais non trouvé ! donc ignoré');
                }
            }
        } elseif (($this->getType() == 'action') && ($this->getConfiguration('infoName') != '')) {
            // Boucle non testée ici mais fonctionne sur Alexa-SmartHome!!
            $LogicalIdCmd = $this->getConfiguration('infoName');
            $cmd = $this->getEqLogic()->getCmd(null, $LogicalIdCmd);
            if (is_object($cmd)) {
                $this->getEqLogic()->checkAndUpdateCmd($LogicalIdCmd, $resultjson[$LogicalIdCmd]);
            } else {
                log::add('alexaapi', 'warning', $LogicalIdCmd . ' prévu dans infoName de ' . $this->getName() . ' mais non trouvé ! donc ignoré');
            }
        }
 
 return true;
    }

    private function decodeTousLesVolumes($_resultjson = array())
	{
					
	//$listePluginsAlexaArray=self::listePluginsAlexaArray(false, false, false, true);

		
		
		
		
		
		
		
		
		
		
		foreach ($_resultjson as $equip) {
			//log::add('alexaapi', 'info', 'equip:'.json_encode($equip));
			//log::add('alexaapi', 'info', 'dsn:'.$equip['dsn']);
			
						foreach (eqLogic::byType('alexaapi') as $eqLogic) {
						//log::add('alexaapi', 'info', 'eqlogic::'.$eqLogic->getLogicalId());
						
						if ($eqLogic->getLogicalId()==$equip['dsn']) {
							log::add('alexaapi', 'info', '-----!!!!!!!!! Trouvé :'.$equip['dsn'].' son volume est de '.$equip['speakerVolume']);
							$eqLogic->checkAndUpdateCmd('volumeinfo', $equip['speakerVolume']);
						}
						
							}
            }
		

		
	}

    private function buildRequest($_options = array())
    {
        if ($this->getType() != 'action') return $this->getConfiguration('request');
        $cmdANDarg = explode('?', $this->getConfiguration('request'), 2);
        if (count($cmdANDarg) > 1)
            list($command, $arguments) = $cmdANDarg;
        else {
            $command = $this->getConfiguration('request');
            $arguments = "";
        }
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
            case 'textCommand':
            case 'announcement':
            case 'push':
            case 'multiplenext':
                $request = $this->build_ControledeSliderSelectMessage($_options);
                break;
            case 'reminder':
            case 'alarm':
                $now = date("Y-m-d H:i:s", strtotime('+3 second'));
                $request = $this->build_ControleWhenTextRecurring($now, "Ceci est un essai", $_options);
                break;
            case 'radio':
                $request = $this->build_ControledeSliderSelectMessage($_options, 's2960');
                break;
            case 'SmarthomeCommand':
            case 'DisplayPower':
            case 'allDeviceVolumes':
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
            case 'updateallalarms':
                $request = $this->build_ControleRien($_options);
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
        //log::add('alexaapi', 'debug', '!!!!----RequestFinale:'.$request);
        $request = scenarioExpression::setTags($request);
        if (trim($request) == '') throw new Exception(__('Commande inconnue ou requête vide : ', __FILE__) . print_r($this, true));
        $device = str_replace("_player", "", $this->getEqLogic()->getConfiguration('serial'));
		if ($request =="allDeviceVolumes") return 'http://' . config::byKey('internalAddr') . ':3456/' . $request; // cas particulier pour cette commande
			else return 'http://' . config::byKey('internalAddr') . ':3456/' . $request . '&device=' . $device;
    }


    private function build_ControledeSliderSelectMessage($_options = array(), $default = "Ceci est un message de test")
    {
       $cmd = $this->getEqLogic()->getCmd(null, 'volumeinfo');
        if (is_object($cmd))
            $lastvolume = $cmd->execCmd();

        $request = $this->getConfiguration('request');
        //log::add('alexaapi_node', 'info', '---->Request2:'.$request);
        //log::add('alexaapi_node', 'debug', '---->getName:'.$this->getEqLogic()->getCmd(null, 'volumeinfo')->execCmd());
        if ((isset($_options['slider'])) && ($_options['slider'] == "")) $_options['slider'] = $default;
        if ((isset($_options['select'])) && ($_options['select'] == "")) $_options['select'] = $default;
        if ((isset($_options['message'])) && ($_options['message'] == "")) $_options['message'] = $default;
        if (!(isset($_options['slider']))) $_options['slider'] = "";
        if (!(isset($_options['select']))) $_options['select'] = "";
        if (!(isset($_options['message']))) $_options['message'] = "";
        if ((!(isset($_options['volume']))) || ($_options['volume']=="#volume#")) $_options['volume'] = ""; //2eme partie du test : Rustine pour supprimer un volume qui serait resté sur #volume# sur un scénario
        //log::add('alexaapi_node', 'info', '---->!!!!!!!!!!!!!!!!!!!!!!!:'.$_options['volume']);
		
        if (!(isset($_options['volume']))) $_options['volume'] = "";
		
        //log::add('alexaapi', 'info', 'xxxxxxxxxxxxx---->_options:'.json_encode($_options));
        // Si on est sur une commande qui utilise volume, on va remettre après execution le volume courant
        if (strstr($request, '&volume=')) $request = $request . '&lastvolume=' . $lastvolume;
        $request = str_replace(
            array('#slider#', '#select#', '#message#', '#volume#'),
            array($_options['slider'], $_options['select'], urlencode(self::decodeTexteAleatoire($_options['message'])), $_options['volume']),
            $request
        );
        //log::add('alexaapi', 'info', '---->RequestFinale:'.$request);
        return $request;
    }

    //private function trouveVolumeDevice() {
    //	$logical_id = $this->getEqLogic()->getCmd(null, 'volumeinfo')->getValue();
    //	$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');getValue
    //}


    public static function decodeTexteAleatoire($_text)
    {
		// Pour le décodage des interjections et de la librairie de sons
		// https://developer.amazon.com/en-US/docs/alexa/custom-skills/ask-soundlibrary.html
		// https://developer.amazon.com/en-US/docs/alexa/custom-skills/speechcon-reference-interjections-french.html
		$_text= preg_replace("~#([^/#\[\]]+/[^/#\[\]]+)#~i",'<audio src="soundbank://soundlibrary/$1" />',$_text);
		$_text= preg_replace("~#([^/#\[\]]+/[^/#\[\]]+/[^/#\[\]]+)#~i",'<audio src="soundbank://soundlibrary/$1" />',$_text);
		$return= preg_replace("~#([^/#\[\]]+)#~i",'<say-as interpret-as="interjection">$1</say-as>',$_text);


        if (strpos($_text, '|') !== false && strpos($_text, '[') !== false && strpos($_text, ']') !== false) {
            $replies = interactDef::generateTextVariant($_text);
            $random = rand(0, count($replies) - 1);
            $return = $replies[$random];
        }
        preg_match_all('/{\((.*?)\) \?(.*?):(.*?)}/', $return, $matches, PREG_SET_ORDER, 0);
        $replace = array();
        if (is_array($matches) && count($matches) > 0) {
            foreach ($matches as $match) {
                if (count($match) != 4) {
                    continue;
                }
                $replace[$match[0]] = (jeedom::evaluateExpression($match[1])) ? trim($match[2]) : trim($match[3]);
            }
        }
        return str_replace(array_keys($replace), $replace, $return);
    }


    private function build_ControleWhenTextRecurring($defaultWhen, $defaultText, $_options = array())
    {
        $request = $this->getConfiguration('request');
        //log::add('alexaapi', 'debug', '----build_ControledeSliderSelectMessage RequestFinale:'.$request);
        //log::add('alexaapi', 'debug', '----build_ControledeSliderSelectMessage _optionsAVANT:'.json_encode($_options));
        if ((!isset($_options['sound'])) && (!isset($_options['message'])) && (!isset($_options['when']))) {
            if (isset($_options['select'])) { // On est dans le cas d'un son d'alarme envoyé depuis le widget
                $_options['sound'] = urlencode($_options['select']);
                $_options['select'] = "";
            }
        }
        if ($_options['when'] == "") $_options['when'] = $defaultWhen;
        if ($_options['message'] == "") $_options['message'] = $defaultText;
        if ($_options['sound'] == "") $_options['sound'] = 'system_alerts_melodic_01';
        $request = str_replace(array('#when#', '#message#', '#recurring#', '#sound#'), array(urlencode($_options['when']), urlencode($_options['message']), urlencode($_options['select']), $_options['sound']), $request);
        return $request;
    }

    private function build_ControlePosition($_options = array())
    {
        $request = $this->getConfiguration('request');
        $request = str_replace('#position#', urlencode($_options['position']), $request);
        return $request;
    }

    private function build_ControleRien($_options = array())
    {
        return $this->getConfiguration('request') . "?truc=vide";
    }

    private function buildDeleteAllAlarmsRequest($_options = array())
    {
        $request = $this->getConfiguration('request');
        log::add('alexaapi', 'debug', '----buildDeleteAllAlarmsRequest RequestFinale:' . $request);
        if ($_options['type'] == "") $_options['type'] = "alarm";
        if ($_options['status'] == "") $_options['status'] = "ON";
        return str_replace(array('#type#', '#status#'), array($_options['type'], $_options['status']), $request);
    }

    private function builddeleteReminderRequest($_options = array())
    {
        $request = $this->getConfiguration('request');
        if ($_options['id'] == "") $_options['id'] = "ManqueID";
        if ($_options['status'] == "") $_options['status'] = "ON";
        return str_replace(array('#id#', '#status#'), array($_options['id'], $_options['status']), $request);
    }

    private function buildRestartRequest($_options = array())
    {
        log::add('alexaapi_debug', 'debug', '------buildRestartRequest---UTILISE QUAND ???--A simplifier--------------------------------------');
        $request = $this->getConfiguration('request') . "?truc=vide";
        return str_replace('#volume#', $_options['slider'], $request);
    }

    public function getWidgetTemplateCode($_version = 'dashboard', $_clean = true, $_widgetName = '')
    {
        $data = null;
        if ($_version != 'scenario') return parent::getWidgetTemplateCode($_version, $_clean, $_widgetName);
        list($command, $arguments) = explode('?', $this->getConfiguration('request'), 2);
        if (($command == 'speak') || ($command == 'announcement'))
            $data = getTemplate('core', 'scenario', 'cmd.speak.volume', 'alexaapi');
        if ($command == 'reminder')
            $data = getTemplate('core', 'scenario', 'cmd.reminder', 'alexaapi');
        if ($command == 'deleteReminder')
            $data = getTemplate('core', 'scenario', 'cmd.deletereminder', 'alexaapi');
        if ($command == 'deleteallalarms')
            $data = getTemplate('core', 'scenario', 'cmd.deleteallalarms', 'alexaapi');
        if ($command == 'command' && strpos($arguments, '#select#'))
            $data = getTemplate('core', 'scenario', 'cmd.command', 'alexaapi');
        if ($command == 'alarm')
            $data = getTemplate('core', 'scenario', 'cmd.alarm', 'alexaapi');
        if (!is_null($data)) {
            if (version_compare(jeedom::version(),'4.2.0','>=')) {
                if(!is_array($data)) return array('template' => $data, 'isCoreWidget' => false);
            } else return $data;
        }
        return parent::getWidgetTemplateCode($_version, $_clean, $_widgetName);
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
