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
					
					

if (!jeedom::apiAccess(init('apikey'), 'alexaapi')) {
	echo __('Vous n\'êtes pas autorisé à effectuer cette action', __FILE__);
	log::add('alexaapi_mqtt', 'debug',  'Clé Plugin Invalide');
	die();
}

	log::add('alexaapi', 'debug',  'Réception données sur jeeAlexaapi');
	//log::add('alexaapi_mqtt', 'debug',  'Clé Plugin Valide');

if (init('test') != '') {
	echo 'OK';
	die();
}

$chaineRecuperee=file_get_contents("php://input");
$nom=$_GET["nom"];
log::add('alexaapi_mqtt', 'info',  " -----[".$nom."]-----" );


$debut=strpos($chaineRecuperee, "{");
$fin=strrpos($chaineRecuperee, "}");
$longeur=1+intval($fin)-intval($debut);
$chaineRecupereeCorrigee=substr($chaineRecuperee, $debut, $longeur);

$chaineRecupereeCorrigee=str_replace ("[", "", $chaineRecupereeCorrigee);
$chaineRecupereeCorrigee=str_replace ("]", "", $chaineRecupereeCorrigee);

log::add('alexaapi_mqtt', 'debug',  $chaineRecupereeCorrigee);

$result = json_decode($chaineRecupereeCorrigee, true);



if (!is_array($result)) {
	log::add('alexaapi_mqtt', 'debug', 'Format Invalide');
	die();
}
log::add('alexaapi_mqtt', 'debug',  'deviceSerialNumber:'.$result['deviceSerialNumber']);
$logical_id = $result['deviceSerialNumber']."_player";
$alexaapi=alexaapi::byLogicalId($logical_id, 'alexaapi');
$alexaapi2=alexaapi::byLogicalId($result['deviceSerialNumber'], 'alexaapi'); // Le device Amazon Echo

$alexaapi3=alexaapi::byLogicalId($result['deviceSerialNumber']."_playlist", 'alexaapi'); // Le device PlayList
			
	switch ($nom) {
		
		
			case 'ws-volume-change':
				metAJour("Volume", $result['volume'], 'volumeinfo', false , $alexaapi);
				metAJour("Volume", $result['volume'], 'volumeinfo', false , $alexaapi2);
			break;	
			
			case 'ws-media-queue-change':
				metAJour("loopMode", $result['loopMode'], 'loopMode', false , $alexaapi);
				metAJour("playBackOrder", $result['playBackOrder'], 'playBackOrder', false , $alexaapi);
			break;	
			
			case 'ws-device-activity':
		

				metAJour("Interaction", $result['description']['summary'], 'interactioninfo', true , $alexaapi);
				metAJour("Interaction", $result['description']['summary'], 'interactioninfo', true , $alexaapi2);
				
				metAJour("activityStatus", $result['activityStatus'], 'activityStatus', true , $alexaapi);

				metAJour("Radio", $result['domainAttributes']['nBestList']['stationCallSign'], 'radioinfo', false , $alexaapi);
				
				metAJour("Radio", $result['domainAttributes']['nBestList']['stationName'], 'radioinfo', false , $alexaapi);
				
				metAJour("playlistName", $result['domainAttributes']['nBestList']['playlistName'], 'playlistName', true , $alexaapi);
				
				//metAJour("songName", $result['domainAttributes']['nBestList']['songName'], 'songName', true , $alexaapi);
				
			break;			
		
			case 'ws-audio-player-state-change':
				metAJour("Audio Player State", $result['audioPlayerState'], 'audioPlayerState', true , $alexaapi);
			case 'refreshPlayer':
				metAJourPlayer($logical_id, $result['audioPlayerState'], $alexaapi);
				metAJourPlayList($logical_id, $result['audioPlayerState'], $alexaapi3);

			break;
			
			default:

				if (!is_object($alexaapi)) {
				log::add('alexaapi_mqtt', 'debug',  'Device non trouvé: '.$logical_id);
				die();
				}
				else{
				log::add('alexaapi_mqtt', 'debug',  'Device trouvé: '.$logical_id);
				}
		
	}
	/*
// ----------------- VOLUME ------------------
			
if ($result['volume']!=null)
{
log::add('alexaapi_mqtt', 'debug',  'Volume trouvé: '.$result['volume']);
				$alexaapi->checkAndUpdateCmd('volumeinfo', $result['volume']);
				die();
}

// ----------------- INTERACTION ------------------
	
			
if ($result['description']['summary']!=null)
{
log::add('alexaapi_mqtt', 'debug',  'Intéraction trouvée: '.$result['description']['summary']);
				$alexaapi->checkAndUpdateCmd('interactioninfo', $result['description']['summary']);
				die();
}

// ----------------- audioPlayerState ------------------
	
			
if ($result['audioPlayerState']!=null)
{
log::add('alexaapi_mqtt', 'debug',  'Changement état Audio Player: '.$result['audioPlayerState']);
				$alexaapi->checkAndUpdateCmd('audioPlayerState', $result['audioPlayerState']);
				die();
}
*/

function metAJour($nom, $variable, $commandejeedom, $effaceSiNull, $_alexaapi) {
	try {
		if (isset($variable)) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] find: '.json_encode($variable));
			$_alexaapi->checkAndUpdateCmd($commandejeedom, $variable);
			}
			else {
			log::add('alexaapi_mqtt', 'debug',  '['.$nom.':'.$commandejeedom.'] non trouvé: '.$variable);
				if ($effaceSiNull) {
					$_alexaapi->checkAndUpdateCmd($commandejeedom, null);
					log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] non trouvé et vidé');
				}
			}	
	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
		} catch (Error $e) {
				log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

			}	
}

function metAJourBoutonPlayer($nom, $variable, $commandejeedom, $nomBouton, $_alexaapi) {
	try {
		if (isset($variable)) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] find: '.json_encode($variable));
			$_alexaapi->checkAndUpdateCmd($commandejeedom, $variable);
			if ($variable=='ENABLED') $visible=1; else $visible=0;
				$cmd = $_alexaapi->getCmd(null, $nomBouton);
				if (is_object($cmd)) {
				log::add('alexaapi_mqtt', 'info',  ' ok invisible');
				$cmd->setIsVisible($visible);
				$cmd->save();
				}
			}
	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
		} catch (Error $e) {
				log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

			}	
}

function metAJourImage($nom, $variable, $commandejeedom, $effaceSiNull, $_alexaapi) {
	
	try {
		
		
		//if ($variable!=null)
		if (isset($variable)) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] find: '.json_encode($variable));
			//$_alexaapi->checkAndUpdateCmd($commandejeedom, $variable);
			$_alexaapi->checkAndUpdateCmd($commandejeedom, "<img width='150' height='150' src='".$variable."' />");
			//die();
			}
			else
			{
			log::add('alexaapi_mqtt', 'debug',  '['.$nom.':'.$commandejeedom.'] non trouvé: '.$variable);
			if ($effaceSiNull) {
				$_alexaapi->checkAndUpdateCmd($commandejeedom, null);
				log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] non trouvé et vidé');
			}
			//die();
			}	
	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
	} catch (Error $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

	}	
}	

function metAJourPlayer($serialdevice, $audioPlayerState, $alexaapi) {
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzz metAJourPlayer:');

	try {
		
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzzzz metAJourPlayer:'.$audioPlayerState);
		//if (($audioPlayerState=="PLAYING") || ($audioPlayerState=="REFRESH") || ($audioPlayerState=="PAUSED"))	{
		if ($audioPlayerState!="FINISHED") 	{
		//log::add('alexaapi_mqtt', 'debug',  ' metAJourPlayer:'.$serialdevice);
		$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/playerInfo?device=".str_replace ("_player", "", $serialdevice));
		$result = json_decode($json,true);		
		log::add('alexaapi_mqtt', 'debug',  ' JSON:'.$json);
		
		}
		else {
	metAJour("state", $audioPlayerState, 'state', false , $alexaapi);		
	// Pour supprimer les éléments MQTT qui étaient arrivés précédemment
		//metAJour("playlistName", "", 'playlistName', true , $alexaapi);
		}
		
metAJour("subText1", $result['playerInfo']['infoText']['subText1'], 'subText1', true , $alexaapi);
metAJour("subText2", $result['playerInfo']['infoText']['subText2'], 'subText2', true , $alexaapi);
metAJour("title", $result['playerInfo']['infoText']['title'], 'title', true , $alexaapi);
metAJourImage("url", $result['playerInfo']['mainArt']['url'], 'url', true , $alexaapi);
metAJour("mediaLength", $result['playerInfo']['progress']['mediaLength'], 'mediaLength', true , $alexaapi);
metAJour("mediaProgress", $result['playerInfo']['progress']['mediaProgress'], 'mediaProgress', true , $alexaapi);

metAJour("state", $result['playerInfo']['state'], 'state', false , $alexaapi);

// NEXT ET PREVIOUS MIS A JOUR PAR requete Player Info

metAJourBoutonPlayer("nextState", $result['playerInfo']['transport']['next'], 'nextState', 'next' , $alexaapi);
metAJourBoutonPlayer("previousState", $result['playerInfo']['transport']['previous'], 'previousState', 'previous' , $alexaapi);

// Play et Pause Mis à jour en fonction de $audioPlayerState
	if ($audioPlayerState=="PLAYING") {
			$etatdePlay='DISABLED'; 
			$etatdePause='ENABLED';
		}
		else {
			$etatdePlay='ENABLED';
			$etatdePause='DISABLED';
		}

metAJourBoutonPlayer("playPauseState", $etatdePause , 'playPauseState', 'pause' , $alexaapi);
metAJourBoutonPlayer("playPauseState", $etatdePlay, 'playPauseState', 'play' , $alexaapi);

// Ancienne mise à jour par Amazon
//metAJourBoutonPlayer("playPauseState", $result['playerInfo']['transport']['playPause'], 'playPauseState', 'pause' , $alexaapi);//PAR requete Player Info
// metAJourBoutonPlayer("playPauseState", $result['playerInfo']['transport']['playPause'], 'playPauseState', 'play' , $alexaapi); //PAR requete Player Info
	

	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
	} catch (Error $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

	}	
}

function metAJourPlaylist($serialdevice, $audioPlayerState, $alexaapi) {
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzz metAJourPlayer:');

	try {
		
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzzzz metAJourPlayer:'.$audioPlayerState);
		//if (($audioPlayerState=="PLAYING") || ($audioPlayerState=="REFRESH") || ($audioPlayerState=="PAUSED"))	{
		if ($audioPlayerState!="FINISHED") 	{
		//log::add('alexaapi_mqtt', 'debug',  ' metAJourPlayer:'.$serialdevice);
		$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/media?device=".str_replace ("_player", "", $serialdevice));
		$result = json_decode($json,true);		
		log::add('alexaapi_mqtt', 'debug',  '++++++++++++++++++++++++++++++++++ JSON:'.$json);
		
		}
		else {
	//metAJour("state", $audioPlayerState, 'state', false , $alexaapi);		
	// Pour supprimer les éléments MQTT qui étaient arrivés précédemment
		//metAJour("playlistName", "", 'playlistName', true , $alexaapi);
		}


//$image=$result['queue']['0']['imageURL'];
//log::add('alexaapi_mqtt', 'debug',  '++++++>+++++++++>+++++++++>++++++++++ $image:'.$image);
log::add('alexaapi_mqtt', 'debug', '-->'.json_encode($result));
$html="<table border='0' cellspacing=4 cellpadding=4 width='100%'>";
        foreach ($result['queue'] as $key => $value) {
				log::add('alexaapi_mqtt', 'debug', '-----------------album:'.$value['album']);
				log::add('alexaapi_mqtt', 'debug', '-----------------artist:'.$value['artist']);
				log::add('alexaapi_mqtt', 'debug', '-----------------imageURL:'.$value['imageURL']);			
				log::add('alexaapi_mqtt', 'debug', '-----------------title:'.$value['title']);			
				log::add('alexaapi_mqtt', 'debug', '-----------------durationSeconds:'.$value['durationSeconds']);			

$html.="    <tr>
        <td rowspan='2' width='50'><img width=50 height=50 src='".$value['imageURL']."' /></td>
        <td width='100%'>".$value['title']."</td>
    </tr>
    <tr>
        <td width='100%'><small>".$value['artist']." - <font size=1><em>".date('i:s', $value['durationSeconds'])."</em></font></small></td>
    </tr>";


//$html.=" <p align=left> <img width=50 height=50 src='".$value['imageURL']."' /> ".$value['title']."<br><small>".$value['artist']."</small> ".$value['durationSeconds']."s</p>";		
}	
$html.="</table>";

metAJour("test", $html, 'test', true , $alexaapi);



	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
	} catch (Error $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

	}	
}	

	
	
?>