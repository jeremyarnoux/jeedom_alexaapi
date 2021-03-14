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

require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";


if (!jeedom::apiAccess(init('apikey'), 'alexasmarthome')) {
    echo __('Vous n\'êtes pas autorisé à effectuer cette action', __FILE__);
    log::add('alexasmarthome_mqtt', 'debug', 'Clé Plugin Invalide');
    die();
}

//log::add('alexasmarthome_mqtt', 'debug',  'Clé Plugin Valide');

if (init('test') != '') {
    echo 'OK';
    die();
}

$chaineRecuperee = file_get_contents("php://input");
$nom = $_GET["nom"];
log::add('alexasmarthome', 'debug', 'Réception données sur jeealexasmarthome [' . $nom . ']');
log::add('alexasmarthome_mqtt', 'info', " -------------------------------------------------------------------------------------------------------------");
log::add('alexasmarthome_widget', 'info', " -------------------------------------------------------------------------------------------------------------");

log::add('alexasmarthome_mqtt', 'debug', "chaineRecuperee: " . $chaineRecuperee);

$debut = strpos($chaineRecuperee, "{");
$fin = strrpos($chaineRecuperee, "}");
$longeur = 1 + intval($fin) - intval($debut);
$chaineRecupereeCorrigee = substr($chaineRecuperee, $debut, $longeur);

if ($nom != "commandesEnErreur") {
    $chaineRecupereeCorrigee = str_replace("[", "", $chaineRecupereeCorrigee);
    $chaineRecupereeCorrigee = str_replace("]", "", $chaineRecupereeCorrigee);
}

log::add('alexasmarthome_mqtt', 'debug', "chaineRecupereeCorrigee: " . $chaineRecupereeCorrigee);
log::add('alexasmarthome_mqtt', 'debug', "nom: " . $nom);

$result = json_decode($chaineRecupereeCorrigee, true);


if (!is_array($result)) {
    log::add('alexasmarthome_mqtt', 'debug', 'Format Invalide');
    die();
}
log::add('alexasmarthome_mqtt', 'debug', 'deviceSerialNumber:' . $result['deviceSerialNumber']);
$logical_id = $result['deviceSerialNumber'] . "_player";
$alexasmarthome = alexasmarthome::byLogicalId($logical_id, 'alexasmarthome');
$alexasmarthome2 = alexasmarthome::byLogicalId($result['deviceSerialNumber'], 'alexasmarthome'); // Le device Amazon Echo
$alexasmarthome3 = alexasmarthome::byLogicalId($result['deviceSerialNumber'] . "_playlist", 'alexasmarthome'); // Le device PlayList

/*$alexasmarthome->emptyCacheWidget();	
$alexasmarthome2->emptyCacheWidget();
$alexasmarthome3->emptyCacheWidget();

clearCacheWidget();
*/

log::add('alexasmarthome_node', 'info', 'Alexa-jee: ' . $nom);

switch ($nom) {

    case 'commandesEnErreur':
        log::add('alexasmarthome_node', 'warning', "Alexa-jee: Il va falloir relancer: " . $chaineRecupereeCorrigee . " Pause 8s");
        sleep(8);
        $commandeaRelancer = json_decode($chaineRecupereeCorrigee, true);
        $queryEnErreur = $commandeaRelancer['queryEnErreur'];
        $listeCommandesEnErreur = $commandeaRelancer['listeCommandesEnErreur'];
        $listeCommandesEnErreur = str_replace("[", "", $listeCommandesEnErreur);
        $listeCommandesEnErreur = str_replace("]", "", $listeCommandesEnErreur);

        if (is_array($listeCommandesEnErreur)) { // s'il y a un groupe de commandes à relancer
            foreach ($listeCommandesEnErreur as $CommandesEnErreur) {
                $url = "http://" . config::byKey('internalAddr') . ":3456/" . $CommandesEnErreur['command'] . "?replay=1&" . http_build_query($queryEnErreur);
                $json = file_get_contents($url);
            }
        } else {                                // s'il n'y a qu'une commande à relancer
            //faudra surement ajouter un test ici pour voir si c'ets pas vide
            $url = "http://" . config::byKey('internalAddr') . ":3456/" . $listeCommandesEnErreur . "?replay=1&" . http_build_query($queryEnErreur);
            $json = file_get_contents($url);
        }
        break;

    case 'ws-bluetooth-state-change':
        if ($result['bluetoothEvent'] == 'DEVICE_CONNECTED') metAJour("bluetoothDevice", "Connexion en cours", 'bluetoothDevice', false, $alexasmarthome2);
        if ($result['bluetoothEvent'] == 'DEVICE_DISCONNECTED') metAJour("bluetoothDevice", "Déconnexion en cours", 'bluetoothDevice', false, $alexasmarthome2);
        metAJourBluetooth($result['deviceSerialNumber'], $result['audioPlayerState'], $alexasmarthome2, $alexasmarthome);
        break;

    case 'ws-volume-change':
        metAJour("Volume", $result['volume'], 'volumeinfo', false, $alexasmarthome);
        metAJour("Volume", $result['volume'], 'volumeinfo', false, $alexasmarthome2);
        break;

    case 'ws-notification-change': //changement d'une alarme/rappel
        log::add('alexasmarthome_node', 'info', 'Alexa-jee: notificationVersion: ' . $result['notificationVersion']);

        $alexasmarthome2->refresh();    // Lance un refresh du device principal
        break;

    case 'ws-media-queue-change':
        metAJour("loopMode", $result['loopMode'], 'loopMode', false, $alexasmarthome);
        metAJour("playBackOrder", $result['playBackOrder'], 'playBackOrder', false, $alexasmarthome);

        metAJourPlayList($logical_id, $result['audioPlayerState'], $alexasmarthome3, $alexasmarthome);

    //break; // il ne faut pas s'arrêter mais aller tout mettre à jour.

    case 'ws-device-activity':

        metAJour("Interaction", $result['description']['summary'], 'interactioninfo', true, $alexasmarthome);
        metAJour("Interaction", $result['description']['summary'], 'interactioninfo', true, $alexasmarthome2);

        metAJour("activityStatus", $result['activityStatus'], 'activityStatus', true, $alexasmarthome);

        metAJour("Radio", $result['domainAttributes']['nBestList']['stationCallSign'], 'radioinfo', false, $alexasmarthome);

        metAJour("Radio", $result['domainAttributes']['nBestList']['stationName'], 'radioinfo', false, $alexasmarthome);

        metAJour("playlistName", $result['domainAttributes']['nBestList']['playlistName'], 'playlistName', false, $alexasmarthome);
        metAJour("playlistName", $result['domainAttributes']['nBestList']['playlistName'], 'playlistName', false, $alexasmarthome3);

        metAJourPlayer($logical_id, $result['audioPlayerState'], $alexasmarthome);
        metAJourPlayList($logical_id, $result['audioPlayerState'], $alexasmarthome3, $alexasmarthome);
        metAJourPlayer($logical_id, $result['audioPlayerState'], $alexasmarthome); //par sécurité

        //metAJour("songName", $result['domainAttributes']['nBestList']['songName'], 'songName', true , $alexasmarthome);

        break;

    case 'ws-audio-player-state-change': // elle a visiblement disparue cette balise des logs mqtt
        metAJour("Audio Player State", $result['audioPlayerState'], 'audioPlayerState', true, $alexasmarthome);
    case 'refreshPlayer':
        metAJourPlayer($logical_id, $result['audioPlayerState'], $alexasmarthome);
        metAJourPlayList($logical_id, $result['audioPlayerState'], $alexasmarthome3, $alexasmarthome);
        break;

    default:

        if (!is_object($alexasmarthome)) {
            log::add('alexasmarthome_mqtt', 'debug', 'Device non trouvé: ' . $logical_id);
            die();
        } else {
            log::add('alexasmarthome_mqtt', 'debug', 'Device trouvé: ' . $logical_id);
        }

}
log::add('alexasmarthome_mqtt', 'info', " ----------------------------------------------------------------------------------------------------------------------------------------------");
log::add('alexasmarthome_widget', 'info', " ----------------------------------------------------------------------------------------------------------------------------------------------");
if (is_object($alexasmarthome)) $alexasmarthome->refreshWidget();
/*
// ----------------- VOLUME ------------------

if ($result['volume']!=null)
{
log::add('alexasmarthome_mqtt', 'debug',  'Volume trouvé: '.$result['volume']);
            $alexasmarthome->checkAndUpdateCmd('volumeinfo', $result['volume']);
            die();
}

// ----------------- INTERACTION ------------------


if ($result['description']['summary']!=null)
{
log::add('alexasmarthome_mqtt', 'debug',  'Intéraction trouvée: '.$result['description']['summary']);
            $alexasmarthome->checkAndUpdateCmd('interactioninfo', $result['description']['summary']);
            die();
}

// ----------------- audioPlayerState ------------------


if ($result['audioPlayerState']!=null)
{
log::add('alexasmarthome_mqtt', 'debug',  'Changement état Audio Player: '.$result['audioPlayerState']);
            $alexasmarthome->checkAndUpdateCmd('audioPlayerState', $result['audioPlayerState']);
            die();
}
*/

function metAJour($nom, $variable, $commandejeedom, $effaceSiNull, $_alexasmarthome)
{
    try {
        if (isset($variable)) {
            log::add('alexasmarthome_widget', 'info', '   [' . $nom . ':' . $commandejeedom . '] find: ' . json_encode($variable) . " sur {" . $_alexasmarthome->getName() . "}");
            $_alexasmarthome->checkAndUpdateCmd($commandejeedom, $variable);
        } else {
            log::add('alexasmarthome_widget', 'info', '   [' . $nom . ':' . $commandejeedom . '] non trouvé: ' . $variable);
            if ($effaceSiNull) {
                $_alexasmarthome->checkAndUpdateCmd($commandejeedom, null);
                log::add('alexasmarthome_widget', 'info', '   [' . $nom . ':' . $commandejeedom . '] non trouvé et vidé');
            }
        }
    } catch (Exception $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur1: ' . $e);

    } catch (Error $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur2: ' . $e);

    }
}

function metAJourBoutonPlayer($nom, $variable, $commandejeedom, $nomBouton, $_alexasmarthome)
{
    try {
        if (isset($variable)) {
            log::add('alexasmarthome_widget', 'info', '   [' . $nom . ':' . $commandejeedom . ':' . $nomBouton . '] find: ' . json_encode($variable));
            $_alexasmarthome->checkAndUpdateCmd($commandejeedom, $variable);
            if ($variable == 'ENABLED') $visible = 1; else $visible = 0;
            $cmd = $_alexasmarthome->getCmd(null, $nomBouton);
            if (is_object($cmd)) {
                //log::add('alexasmarthome_widget', 'info',  ' ok invisible');
                $cmd->setIsVisible($visible);
                $cmd->save();
            }
        }
    } catch (Exception $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur1: ' . $e);

    } catch (Error $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur2: ' . $e);

    }
}

function metAJourImage($nom, $variable, $commandejeedom, $effaceSiNull, $_alexasmarthome)
{

    try {


        //if ($variable!=null)
        if (isset($variable)) {
            log::add('alexasmarthome_widget', 'info', '   [' . $nom . ':' . $commandejeedom . '] find: ' . json_encode($variable));
            //$_alexasmarthome->checkAndUpdateCmd($commandejeedom, $variable);
            //$_alexasmarthome->checkAndUpdateCmd($commandejeedom, "<img width='150' height='150' src='".$variable."' />");
            $_alexasmarthome->checkAndUpdateCmd($commandejeedom, $variable);
            //die();
        } else {
            log::add('alexasmarthome_widget', 'debug', '[' . $nom . ':' . $commandejeedom . '] non trouvé');
            $_alexasmarthome->checkAndUpdateCmd($commandejeedom, "plugins/alexasmarthome/core/img/vide.gif");
        }
    } catch (Exception $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur1: ' . $e);

    } catch (Error $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur2: ' . $e);

    }
}

function metAJourPlayer($serialdevice, $audioPlayerState, $alexasmarthome)
{
    //log::add('alexasmarthome_widget', 'debug',  'zzzzzzzzzzzzzzzzz metAJourPlayer:');
//log::add('alexasmarthome_node', 'info',  " ***********************[metAJourPlayer]*********************************" );

    try {

        //log::add('alexasmarthome_widget', 'debug',  'zzzzzzzzzzzzzzzzzzz metAJourPlayer:'.$audioPlayerState);
        //if (($audioPlayerState=="PLAYING") || ($audioPlayerState=="REFRESH") || ($audioPlayerState=="PAUSED"))	{
        //if ($audioPlayerState!="FINISHED") 	{
        //log::add('alexasmarthome_widget', 'debug',  ' metAJourPlayer:'.$serialdevice);

        $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/playerInfo?device=" . str_replace("_player", "", $serialdevice));
        $result = json_decode($json, true);
        log::add('alexasmarthome_widget', 'debug', ' JSON:' . $json);


        //}
        //else {
//	metAJour("state", $audioPlayerState, 'state', false , $alexasmarthome);		
        // Pour supprimer les éléments MQTT qui étaient arrivés précédemment
        //metAJour("playlistName", "", 'playlistName', true , $alexasmarthome);
        //	}

        metAJour("subText1", $result['playerInfo']['infoText']['subText1'], 'subText1', true, $alexasmarthome);
        metAJour("subText2", $result['playerInfo']['infoText']['subText2'], 'subText2', true, $alexasmarthome);
        metAJour("title", $result['playerInfo']['infoText']['title'], 'title', true, $alexasmarthome);
        metAJourImage("url", $result['playerInfo']['mainArt']['url'], 'url', true, $alexasmarthome);
        metAJour("mediaLength", $result['playerInfo']['progress']['mediaLength'], 'mediaLength', true, $alexasmarthome);
        metAJour("mediaProgress", $result['playerInfo']['progress']['mediaProgress'], 'mediaProgress', true, $alexasmarthome);
        metAJour("providerName", $result['playerInfo']['provider']['providerName'], 'providerName', true, $alexasmarthome);
        metAJour("state", $result['playerInfo']['state'], 'state', false, $alexasmarthome);


//log::add('alexasmarthome_widget', 'debug',  '5>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> resultplayerInfo:'.json_encode($result['playerInfo']['provider']['providerName']));


// Affecte le statut Playing du device Player
        $alexasmarthome->setStatus('Playing', ($result['playerInfo']['state'] == "PLAYING"));


        /*
        // NEXT ET PREVIOUS MIS A JOUR PAR requete Player Info
        metAJourBoutonPlayer("nextState", $result['playerInfo']['transport']['next'], 'nextState', 'next' , $alexasmarthome);
        metAJourBoutonPlayer("previousState", $result['playerInfo']['transport']['previous'], 'previousState', 'previous' , $alexasmarthome);
        // Play et Pause Mis à jour en fonction de state et plus $audioPlayerState
            //if ($audioPlayerState=="PLAYING") {
            if (isset($result['playerInfo']['state'])) {
                if ($result['playerInfo']['state']=="PLAYING") {
                        $etatdePlay='DISABLED';
                        $etatdePause='ENABLED';
                }
                    else {
                        $etatdePlay='ENABLED';
                        $etatdePause='DISABLED';
                    }
            }
        metAJourBoutonPlayer("playPauseState", $etatdePause , 'playPauseState', 'pause' , $alexasmarthome);
        metAJourBoutonPlayer("playPauseState", $etatdePlay, 'playPauseState', 'play' , $alexasmarthome);
        // Ancienne mise à jour par Amazon
        //metAJourBoutonPlayer("playPauseState", $result['playerInfo']['transport']['playPause'], 'playPauseState', 'pause' , $alexasmarthome);//PAR requete Player Info
        // metAJourBoutonPlayer("playPauseState", $result['playerInfo']['transport']['playPause'], 'playPauseState', 'play' , $alexasmarthome); //PAR requete Player Info
            */

    } catch (Exception $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur1: ' . $e);

    } catch (Error $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur2: ' . $e);

    }
//log::add('alexasmarthome_node', 'info',  " ************************************************************************" );
    if (is_object($alexasmarthome)) $alexasmarthome->refreshWidget(); //refresh Tuile Player
    log::add('alexasmarthome_widget', 'debug', '** Mise à jour Tuile du Player **');

}

function metAJourPlaylist($serialdevice, $audioPlayerState, $alexasmarthome3, $alexasmarthome)
{
    //log::add('alexasmarthome_widget', 'debug',  'zzzzzzzzzzzzzzzzz metAJourPlayer:');

    try {
        if ($audioPlayerState != "FINISHED") {

            //Pour avoir la piste en cours, on va aller chercher la valeur de playerinfo/mainArt/url pour pouvoir la comparer aux images de la playlist
            $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/playerinfo?device=" . str_replace("_player", "", $serialdevice));
            $result = json_decode($json, true);
            $imageURLenCoursdeLecture = $result['playerInfo']['miniArt']['url']; //Modif 09/12/2019 proposée par Aidom, annulée 10/12/2019
            $etatPlayer = $result['playerInfo']['state'];

            //log::add('alexasmarthome_widget', 'debug',  'zzzzzzzzzzzzzzzzzzz metAJourPlayer:'.$audioPlayerState);
            //if (($audioPlayerState=="PLAYING") || ($audioPlayerState=="REFRESH") || ($audioPlayerState=="PAUSED"))	{

            //log::add('alexasmarthome_widget', 'debug',  ' metAJourPlayer:'.$serialdevice);
            $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/media?device=" . str_replace("_player", "", $serialdevice));
            $result = json_decode($json, true);
            //log::add('alexasmarthome_widget', 'debug',  '++++++++++++++++++++++++++++++++++ JSON:'.$json);
            //$imageURLenCoursdeLecture=$result['imageURL'];

        } else {
            //metAJour("state", $audioPlayerState, 'state', false , $alexasmarthome);
            // Pour supprimer les éléments MQTT qui étaient arrivés précédemment
            //metAJour("playlistName", "", 'playlistName', true , $alexasmarthome);
        }

//ON RECUPERE CE QUIE ST AU D2BUT DE MEDIA
        metAJour("contentId", $result['contentId'], 'contentId', true, $alexasmarthome);
//log::add('alexasmarthome_widget', 'debug',  '++++++>+++++++++>+++++++++>++++++++++ $contentId:'.$result['contentId']);


        //$image=$result['queue']['0']['imageURL'];
        //log::add('alexasmarthome_widget', 'debug',  '++++++>+++++++++>+++++++++>++++++++++ $image:'.$image);
        //log::add('alexasmarthome_widget', 'debug', '-->'.json_encode($result));
        $html = "<table style='border-collapse: separate; border-spacing : 10px; ' border='0' width='100%'>";
        $compteurQueue = 1;
        foreach ($result['queue'] as $key => $value) {
            log::add('alexasmarthome_widget', 'debug', '-----------------album:' . $value['album']);
            log::add('alexasmarthome_widget', 'debug', '-----------------artist:' . $value['artist']);
            log::add('alexasmarthome_widget', 'debug', '-----------------imageURL:' . $value['imageURL']);
            log::add('alexasmarthome_widget', 'debug', '-----------------title:' . $value['title']);
            log::add('alexasmarthome_widget', 'debug', '-----------------durationSeconds:' . $value['durationSeconds']);

            if (($value['imageURL'] == $imageURLenCoursdeLecture) && $compteurQueue > 3) {
                $html = "<table style='border-collapse: separate; border-spacing : 10px; ' border='0' width='100%'>";
            }

            $html .= "<tr><td style='padding: 8px;'  rowspan='2' width='50'>";
            //log::add('alexasmarthome_widget', 'debug',  '++++++++++++++++++++++++++++++++++ '.$value['imageURL']."//".$imageURLenCoursdeLecture);
            if (($value['imageURL'] == $imageURLenCoursdeLecture) && $etatPlayer == "PLAYING") $html .= "<img style='position:absolute' src='plugins/alexasmarthome/core/img/playing_petit.gif' />";
            $html .= "<img style='height: 60px;width: 60px;border-radius: 30%;' src='" . $value['imageURL'] . "'/></td>
        <td width='100%'>" . $value['title'] . "</td>
    </tr>
    <tr>
        <td width='100%'><small>" . $value['artist'] . " - <font size=1><em>" . date('i:s', $value['durationSeconds']) . "</em></font></small></td>
    </tr>
	
	";

            $compteurQueue++;
        }
        $html .= "</table>";

        metAJour("playlisthtml", $html, 'playlisthtml', true, $alexasmarthome3);

        $alexasmarthome3->refreshWidget(); //refresh Tuile Playlist


    } catch (Exception $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur1: ' . $e);

    } catch (Error $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur2: ' . $e);

    }

}


function metAJourBluetooth($serialdevice, $audioPlayerState, $alexasmarthome2, $alexasmarthome)
{
    //log::add('alexasmarthome_widget', 'debug',  'zzzzzzzzzzzzzzzzz metAJourPlayer:');

    try {

        //Pour avoir la piste en cours, on va aller chercher la valeur de playerinfo/mainArt/url pour pouvoir la comparer aux images de la playlist
        $json = file_get_contents("http://" . config::byKey('internalAddr') . ":3456/bluetooth");
        $result = json_decode($json, true);

//log::add('alexasmarthome_widget', 'debug', '-->--->--->--->--deviceSerialNumber:'.$result['bluetoothStates']['0']['deviceSerialNumber']);		


        //$result=array_filter($result, "odd");

        //$imageURLenCoursdeLecture=$result['playerInfo']['miniArt']['url'];
        //$etatPlayer=$result['playerInfo']['state'];

        //log::add('alexasmarthome_widget', 'debug',  '------------->'.json_encode($result));

        //if (($audioPlayerState=="PLAYING") || ($audioPlayerState=="REFRESH") || ($audioPlayerState=="PAUSED"))	{

        foreach ($result['bluetoothStates'] as $key => $value) {
            //log::add('alexasmarthome_widget', 'debug', '-------------------------------------------------------------------------------');
            //log::add('alexasmarthome_widget', 'debug', '-----------------deviceType:'.$value['deviceType']);
            //log::add('alexasmarthome_widget', 'debug', '-----------------friendlyName:'.$value['friendlyName']);
            //log::add('alexasmarthome_widget', 'debug', '-----------------online:'.$value['online']);
            //log::add('alexasmarthome_widget', 'debug', '-----------------pairedDeviceList:'.$value['pairedDeviceList']);
            if (is_array($value['pairedDeviceList'])) {
                foreach ($value['pairedDeviceList'] as $key2 => $value2) {
                    if ($value['deviceSerialNumber'] == $serialdevice) {
                        //log::add('alexasmarthome_widget', 'debug', '-----------------$serialdevice:'.$serialdevice);
                        //log::add('alexasmarthome_widget', 'debug', '-----------------deviceSerialNumber:'.$value['deviceSerialNumber']);
                        //log::add('alexasmarthome_widget', 'debug', '********** friendlyName:'.$value2['friendlyName']);
                        //log::add('alexasmarthome_widget', 'debug', '********** connected:'.$value2['connected']);
                        if (isset($value2['connected']) && (($value2['connected']) == '1')) {
                            metAJour("bluetoothDevice", $value2['friendlyName'], 'bluetoothDevice', false, $alexasmarthome2);
                        } else {
                            metAJour("bluetoothDevice", "", 'bluetoothDevice', false, $alexasmarthome2);
                        }
                    }
                }


            }

        }


    } catch (Exception $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur1: ' . $e);

    } catch (Error $e) {
        log::add('alexasmarthome_widget', 'info', ' [' . $nom . ':' . $commandejeedom . '] erreur2: ' . $e);

    }

}


?>
