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

	//log::add('alexaapi_mqtt', 'debug',  'Clé Plugin Valide');

if (init('test') != '') {
	echo 'OK';
	die();
}

$chaineRecuperee=file_get_contents("php://input");
$nom=$_GET["nom"];
log::add('alexaapi', 'debug',  'Réception données sur jeeAlexaapi ['.$nom.']');
log::add('alexaapi_mqtt', 'info',  " -------------------------------------------------------------------------------------------------------------" );


$debut=strpos($chaineRecuperee, "{");
$fin=strrpos($chaineRecuperee, "}");
$longeur=1+intval($fin)-intval($debut);
$chaineRecupereeCorrigee=substr($chaineRecuperee, $debut, $longeur);

$chaineRecupereeCorrigee=str_replace ("[", "", $chaineRecupereeCorrigee);
$chaineRecupereeCorrigee=str_replace ("]", "", $chaineRecupereeCorrigee);

log::add('alexaapi_mqtt', 'debug',  "chaineRecupereeCorrigee: ".$chaineRecupereeCorrigee);

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

/*$alexaapi->emptyCacheWidget();	
$alexaapi2->emptyCacheWidget();
$alexaapi3->emptyCacheWidget();

clearCacheWidget();
*/
log::add('alexaapi_mqtt', 'debug',  'nom:'.$nom);
	switch ($nom) {
		
			case 'ws-volume-change':
				metAJour("Volume", $result['volume'], 'volumeinfo', false , $alexaapi);
				metAJour("Volume", $result['volume'], 'volumeinfo', false , $alexaapi2);
			break;
			
			case 'ws-notification-change': //changement d'une alarme/rappel
				$alexaapi2->refresh();	// Lance un refresh du device principal
			break;	
			
			case 'ws-media-queue-change':
				metAJour("loopMode", $result['loopMode'], 'loopMode', false , $alexaapi);
				metAJour("playBackOrder", $result['playBackOrder'], 'playBackOrder', false , $alexaapi);
				
				metAJourPlayList($logical_id, $result['audioPlayerState'], $alexaapi3);

			//break; // il ne faut pas s'arrêter mais aller tout mettre à jour.	
			
			case 'ws-device-activity':

				metAJour("Interaction", $result['description']['summary'], 'interactioninfo', true , $alexaapi);
				metAJour("Interaction", $result['description']['summary'], 'interactioninfo', true , $alexaapi2);
				
				metAJour("activityStatus", $result['activityStatus'], 'activityStatus', true , $alexaapi);

				metAJour("Radio", $result['domainAttributes']['nBestList']['stationCallSign'], 'radioinfo', false , $alexaapi);
				
				metAJour("Radio", $result['domainAttributes']['nBestList']['stationName'], 'radioinfo', false , $alexaapi);
				
				metAJour("playlistName", $result['domainAttributes']['nBestList']['playlistName'], 'playlistName', false , $alexaapi);
				metAJour("playlistName", $result['domainAttributes']['nBestList']['playlistName'], 'playlistName', false , $alexaapi3);
				
				metAJourPlayer($logical_id, $result['audioPlayerState'], $alexaapi);
				metAJourPlayList($logical_id, $result['audioPlayerState'], $alexaapi3);
				metAJourPlayer($logical_id, $result['audioPlayerState'], $alexaapi); //par sécurité

				//metAJour("songName", $result['domainAttributes']['nBestList']['songName'], 'songName', true , $alexaapi);
				
			break;			
		
			case 'ws-audio-player-state-change': // elle a visiblement disparue cette balise des logs mqtt
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
	log::add('alexaapi_mqtt', 'info',  " -------------------------------------------------------------------------------------------------------------------------------------------------" );
	if (is_object($alexaapi)) $alexaapi->refreshWidget();
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
			log::add('alexaapi_mqtt', 'info',  '   ['.$nom.':'.$commandejeedom.'] find: '.json_encode($variable). " sur {".$_alexaapi->getName()."}");
			$_alexaapi->checkAndUpdateCmd($commandejeedom, $variable);
			}
			else {
			log::add('alexaapi_mqtt', 'info',  '   ['.$nom.':'.$commandejeedom.'] non trouvé: '.$variable);
				if ($effaceSiNull) {
					$_alexaapi->checkAndUpdateCmd($commandejeedom, null);
					log::add('alexaapi_mqtt', 'info',  '   ['.$nom.':'.$commandejeedom.'] non trouvé et vidé');
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
			log::add('alexaapi_mqtt', 'info',  '   ['.$nom.':'.$commandejeedom.':'.$nomBouton.'] find: '.json_encode($variable));
			$_alexaapi->checkAndUpdateCmd($commandejeedom, $variable);
			if ($variable=='ENABLED') $visible=1; else $visible=0;
				$cmd = $_alexaapi->getCmd(null, $nomBouton);
				if (is_object($cmd)) {
				//log::add('alexaapi_mqtt', 'info',  ' ok invisible');
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
			log::add('alexaapi_mqtt', 'info',  '   ['.$nom.':'.$commandejeedom.'] find: '.json_encode($variable));
			//$_alexaapi->checkAndUpdateCmd($commandejeedom, $variable);
			//$_alexaapi->checkAndUpdateCmd($commandejeedom, "<img width='150' height='150' src='".$variable."' />");
			$_alexaapi->checkAndUpdateCmd($commandejeedom, $variable);
			//die();
			}
			else
			{
			log::add('alexaapi_mqtt', 'debug',  '['.$nom.':'.$commandejeedom.'] non trouvé');
			$_alexaapi->checkAndUpdateCmd($commandejeedom, "plugins/alexaapi/core/img/vide.gif");
			}	
	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
	} catch (Error $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

	}	
}	

function metAJourPlayer($serialdevice, $audioPlayerState, $alexaapi) {
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzz metAJourPlayer:');
log::add('alexaapi_mqtt', 'info',  " ***********************[metAJourPlayer]*********************************" );

	try {
		
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzzzz metAJourPlayer:'.$audioPlayerState);
		//if (($audioPlayerState=="PLAYING") || ($audioPlayerState=="REFRESH") || ($audioPlayerState=="PAUSED"))	{
		//if ($audioPlayerState!="FINISHED") 	{
		//log::add('alexaapi_mqtt', 'debug',  ' metAJourPlayer:'.$serialdevice);

		$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/playerInfo?device=".str_replace ("_player", "", $serialdevice));
		$result = json_decode($json,true);		
		log::add('alexaapi_mqtt', 'debug',  ' JSON:'.$json);
	
		
		//}
		//else {
//	metAJour("state", $audioPlayerState, 'state', false , $alexaapi);		
	// Pour supprimer les éléments MQTT qui étaient arrivés précédemment
		//metAJour("playlistName", "", 'playlistName', true , $alexaapi);
	//	}
		
metAJour("subText1", $result['playerInfo']['infoText']['subText1'], 'subText1', true , $alexaapi);
metAJour("subText2", $result['playerInfo']['infoText']['subText2'], 'subText2', true , $alexaapi);
metAJour("title", $result['playerInfo']['infoText']['title'], 'title', true , $alexaapi);
metAJourImage("url", $result['playerInfo']['mainArt']['url'], 'url', true , $alexaapi);
metAJour("mediaLength", $result['playerInfo']['progress']['mediaLength'], 'mediaLength', true , $alexaapi);
metAJour("mediaProgress", $result['playerInfo']['progress']['mediaProgress'], 'mediaProgress', true , $alexaapi);


metAJour("state", $result['playerInfo']['state'], 'state', false , $alexaapi);

// Affecte le statut Playing du device Player
$alexaapi->setStatus('Playing', ($result['playerInfo']['state']=="PLAYING"));




/*
// NEXT ET PREVIOUS MIS A JOUR PAR requete Player Info
metAJourBoutonPlayer("nextState", $result['playerInfo']['transport']['next'], 'nextState', 'next' , $alexaapi);
metAJourBoutonPlayer("previousState", $result['playerInfo']['transport']['previous'], 'previousState', 'previous' , $alexaapi);
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
metAJourBoutonPlayer("playPauseState", $etatdePause , 'playPauseState', 'pause' , $alexaapi);
metAJourBoutonPlayer("playPauseState", $etatdePlay, 'playPauseState', 'play' , $alexaapi);
// Ancienne mise à jour par Amazon
//metAJourBoutonPlayer("playPauseState", $result['playerInfo']['transport']['playPause'], 'playPauseState', 'pause' , $alexaapi);//PAR requete Player Info
// metAJourBoutonPlayer("playPauseState", $result['playerInfo']['transport']['playPause'], 'playPauseState', 'play' , $alexaapi); //PAR requete Player Info
	*/

	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
	} catch (Error $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

	}
log::add('alexaapi_mqtt', 'info',  " ************************************************************************" );
if (is_object($alexaapi)) $alexaapi->refreshWidget(); //refresh Tuile Player
log::add('alexaapi_mqtt', 'debug',  '** Mise à jour Tuile du Player **');

}

function metAJourPlaylist($serialdevice, $audioPlayerState, $alexaapi) {
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzz metAJourPlayer:');

	try {
		if ($audioPlayerState!="FINISHED") 	{		
		
		//Pour avoir la piste en cours, on va aller chercher la valeur de playerinfo/mainArt/url pour pouvoir la comparer aux images de la playlist
		$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/playerinfo?device=".str_replace ("_player", "", $serialdevice));
		$result = json_decode($json,true);		
		$imageURLenCoursdeLecture=$result['playerInfo']['miniArt']['url'];
		$etatPlayer=$result['playerInfo']['state'];
		
		//log::add('alexaapi_mqtt', 'debug',  'zzzzzzzzzzzzzzzzzzz metAJourPlayer:'.$audioPlayerState);
		//if (($audioPlayerState=="PLAYING") || ($audioPlayerState=="REFRESH") || ($audioPlayerState=="PAUSED"))	{

		//log::add('alexaapi_mqtt', 'debug',  ' metAJourPlayer:'.$serialdevice);
		$json=file_get_contents("http://" . config::byKey('internalAddr') . ":3456/media?device=".str_replace ("_player", "", $serialdevice));
		$result = json_decode($json,true);		
		//log::add('alexaapi_mqtt', 'debug',  '++++++++++++++++++++++++++++++++++ JSON:'.$json);
		//$imageURLenCoursdeLecture=$result['imageURL'];
	
		}
		else {
	//metAJour("state", $audioPlayerState, 'state', false , $alexaapi);		
	// Pour supprimer les éléments MQTT qui étaient arrivés précédemment
		//metAJour("playlistName", "", 'playlistName', true , $alexaapi);
		}


			//$image=$result['queue']['0']['imageURL'];
			//log::add('alexaapi_mqtt', 'debug',  '++++++>+++++++++>+++++++++>++++++++++ $image:'.$image);
			//log::add('alexaapi_mqtt', 'debug', '-->'.json_encode($result));
			$html="<table style='border-collapse: separate; border-spacing : 10px; ' border='0' width='100%'>";
			$compteurQueue=1;		
	foreach ($result['queue'] as $key => $value) {
				log::add('alexaapi_mqtt', 'debug', '-----------------album:'.$value['album']);
				log::add('alexaapi_mqtt', 'debug', '-----------------artist:'.$value['artist']);
				log::add('alexaapi_mqtt', 'debug', '-----------------imageURL:'.$value['imageURL']);			
				log::add('alexaapi_mqtt', 'debug', '-----------------title:'.$value['title']);			
				log::add('alexaapi_mqtt', 'debug', '-----------------durationSeconds:'.$value['durationSeconds']);			
	
	if (($value['imageURL']==$imageURLenCoursdeLecture) && $compteurQueue>3){
			$html="<table style='border-collapse: separate; border-spacing : 10px; ' border='0' width='100%'>";
		}

	$html.="<tr><td style='padding: 8px;'  rowspan='2' width='50'>";
	//log::add('alexaapi_mqtt', 'debug',  '++++++++++++++++++++++++++++++++++ '.$value['imageURL']."//".$imageURLenCoursdeLecture);
	if (($value['imageURL']==$imageURLenCoursdeLecture) && $etatPlayer=="PLAYING") $html.="<img style='position:absolute' src='plugins/alexaapi/core/img/playing_petit.gif' />";
	$html.="<img style='height: 60px;width: 60px;border-radius: 30%;' src='".$value['imageURL']."'/></td>
        <td width='100%'>".$value['title']."</td>
    </tr>
    <tr>
        <td width='100%'><small>".$value['artist']." - <font size=1><em>".date('i:s', $value['durationSeconds'])."</em></font></small></td>
    </tr>
	
	";

	$compteurQueue++;
	}	
$html.="</table>";

metAJour("playlisthtml", $html, 'playlisthtml', true , $alexaapi);

$alexaapi->refreshWidget(); //refresh Tuile Playlist


	} catch (Exception $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur1: '.$e);
				
	} catch (Error $e) {
			log::add('alexaapi_mqtt', 'info',  ' ['.$nom.':'.$commandejeedom.'] erreur2: '.$e);

	}	
	
}	

	
	
?>
