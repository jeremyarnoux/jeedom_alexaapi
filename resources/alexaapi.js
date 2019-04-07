/*jshint esversion: 6,node: true,-W041: false */
const express = require('express');
const fs = require('fs');
const Alexa = require('./lib/alexa-remote.js');
let alexa = new Alexa();
var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;

const amazonserver = process.argv[3];
const alexaserver = process.argv[4];

//const debug=1; //mettre 1 pour debug

// Références :
// https://openclassrooms.com/fr/courses/1173401-les-closures-en-javascript

/* Configuration */
const config = {
	cookieLocation: __dirname + '/data/alexa-cookie.json',
	cookieRefreshInterval: 7 * 24 * 60 * 1000,
	logger: consoleSigalou,
	alexaServiceHost: alexaserver,
	listeningPort: 3456
};



var FiledesCommandes = [];  //Utiliser pour surveiller que les commandes sont bien envoyées (pour le souci de Connexion Close)
var FiledesCommandes2 = [];  //Utiliser pour surveiller que les commandes sont bien envoyées (pour le souci de Connexion Close)

// Par sécurité pour détecter un éventuel souci :
if (!amazonserver) config.logger('Alexa-Config: *********************amazonserver NON DEFINI*********************');
if (!alexaserver) config.logger('Alexa-Config: *********************alexaserver NON DEFINI*********************');

function consoleSigalou() {
	var today = new Date();
	try {
		console.log("[" + today.toLocaleString() + "] " + arguments[0].concat(Array.prototype.slice.call(arguments, 1)));
	} catch (e) {
		console.log(arguments[0]);
	}
}

//config.logger('Alexa-Config (alexaapi.js): amazonserver=' + amazonserver);
//config.logger('Alexa-Config (alexaapi.js): alexaserver=' + alexaserver);

/* Routing */
const app = express();
let server = null;
/* Objet contenant les commandes pour appeler via chaine */
var CommandAlexa = {};

/* Apply callback on every cluster's membre (for multiroom device) */
function forEachDevices(nameOrSerial, callback) {
	
	var device = alexa.find(nameOrSerial);
	if (device === undefined)
		return;

	if (device.clusterMembers.length == 0)
		callback(device.serialNumber);

	for (var i in device.clusterMembers) {
		if (device.clusterMembers.hasOwnProperty(i)) {
			// We are sure that obj[key] belongs to the object and was not inherited.
			callback(device.clusterMembers[i]);
		}
	}
}


function LancementCommande(commande, req) 
{
	config.logger('Alexa-API: Lancement /'+commande);
	config.logger('Alexa-API: Lancement /'+req.query.tagId);
			//config.logger(req);
/*
	FiledesCommandes.push([commande, req, Date.now()]);
	FiledesCommandes2.push([commande, req, Date.now()]);
	config.logger && config.logger('Taille au lancement:'+FiledesCommandes.length);
	config.logger && config.logger('Taille au lancement:'+FiledesCommandes.length);
	config.logger && config.logger('heure:'+Date.now());*/
}
/*
function FinCommandeBienExecutee() 
{
	if (debug!=1) FiledesCommandes.pop(); 
	//config.logger && config.logger('Taille après lancement :'+FiledesCommandes.length);
}

function AllerVoirSilYaDesCommandesenFileAttente()		
{
	config.logger && config.logger("Alexa-API: Il reste "+FiledesCommandes.length+" commande(s) en file d'attente");
	FiledesCommandes.forEach(function (element) {
		config.logger && config.logger('Alexa-API: RE-Lancement de '+element[0]+'('+element[1]+')');
		config.logger && config.logger('Test Horodatage : Ancien lancement: '+element[2]+' et maintenant : '+Date.now()+ 'Difference :'+(Date.now()-element[2]));
		//config.logger(element[1]);
		//element[1].fresh="132";
		//if (element[1].fresh) {
  // The user-agent is asking for a more up-to-date version of the requested resource.
  // Let's hit the database to get some stuff and send it back.
  
  
	req=element[1];
	
		CommandAlexa[element[0]](req,app.response);//}
	})
}


///pour debug, c'est pour attendre 4s
function traitement()
{
//Traitement à effectuer sur la page
setTimeout(suiteTraitement, 4000) //Attendez 10 secondes avant de continuer dans la fonction suivante
}
function suiteTraitement()
{
//Continuez le traitement après la pause dans cette fonction
//AllerVoirSilYaDesCommandesenFileAttente();
config.logger && config.logger('test 4s');
/*var req = new XMLHttpRequest();
req.open("POST", "http://192.168.0.21:3456/volume?value=50&device=G090LF118173117U");
// Envoi de la requête en y incluant l'objet
req.send(identite);

// *****************************************


var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var req = new XMLHttpRequest();
req.open("POST", "http://192.168.0.21:3456/volume?value=50&device=G090LF118173117U");
// Envoi de la requête en y incluant l'objet
req.send();






const req = new XMLHttpRequest();
req.open('POST', 'http://192.168.0.21:3456/volume?value=50&device=G090LF118173117U', false);
req.send(null);

// if (req.status === 0) {}

//console.log(req.responseText);


}
*/
/***** checkAuth *****
  URL: /checkAuth

  Return the status of the Auth
  [{
    auth - binary - authentified or not
  }]

*/
app.get('/checkAuth', (req, res) => {
	config.logger('Alexa-API: checkAuth');
	res.type('json');

	alexa.checkAuthentication(function(auth) {
		res.status(200).json({
			authenticated: auth
		});
	});
});


/**** Alexa.Speak *****
  URL: /speak?device=?&text=?
    device - String - name of the device
    text - String - Text to speech
    volume - Integer - Determine the volume level between 0 to 100 (0 is mute and 100 is max).
                       This parameter is optional. If not specified, the volume level will not be altered.

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
  FIXME: Currently, the librarie returns an "false" error when the command succeed but no body was returned by Amazon
*/
CommandAlexa.Speak = function(req,res){
	LancementCommande("Speak",req);
	res.type('json');
	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Speak', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('text' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Speak', 'Missing parameter "text"'));
	config.logger('Alexa-API: text: ' + req.query.text);

	forEachDevices(req.query.device, (serial) =>
	{
		alexa.sendSequenceCommand(serial, 'speak', req.query.text, GestionRetour);
	});

	res.status(200).json({});	
}

/**** Alexa.Radio *****
  URL: /radio?device=?&text=?
    device - String - name of the device
    text - String - Text to speech
    volume - Integer - Determine the volume level between 0 to 100 (0 is mute and 100 is max).
                       This parameter is optional. If not specified, the volume level will not be altered.

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
  FIXME: Currently, the librarie returns an "false" error when the command succeed but no body was returned by Amazon
*/
CommandAlexa.Radio = function(req,res){
	LancementCommande("Radio",req);
	res.type('json');

	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Radio', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('station' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Radio', 'Missing parameter "station"'));
	config.logger('Alexa-API: station: ' + req.query.station);

	forEachDevices(req.query.device, (serial) => {
		alexa.setTunein(serial, req.query.station, GestionRetour);
	});

	res.status(200).json({});
}


/***** Alexa.Volume *****
  URL: /volume?device=?&value=?
    device - String - name of the device
    value - Integer - Determine the volume level between 0 to 100 (0 is mute and 100 is max)

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
  FIXME: Currently, the librarie returns an "false" error when the command succeed but no body was returned by Amazon
*/

CommandAlexa.Volume = function(req,res){
	
	LancementCommande("Volume", req);
	//req.fresh="";


	// fullUrl = req.protocol + '://' + req.get('host') + req.originalUrl;
	//config.logger('Alexa-API: Traitement de : '+fullUrl);
	res.type('json');


	//Quand Volume est lancé par une autre fonction, la valeur du volume n'est pas value mais volume
	if ('volume' in req.query) 
		req.query.value=req.query.volume

	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Volume', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('value' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Volume', 'Missing parameter "value"'));
	config.logger('Alexa-API: value: ' + req.query.value);
	
	
	
	var err = forEachDevices(req.query.device, (serial) => {
		
		alexa.sendSequenceCommand(serial, 'volume', req.query.value, 
				
			function(testErreur){
				if (testErreur) {
				traiteErreur(testErreur);		
				res.status(500).json({value: testErreur.message});
				} else {
				   res.status(200).json({value: "OK"});
				}
			}
		);
	});
}



/***** Alexa.Command *****
  URL: /command?device=?&command=?
    device - String - name of the device
    command - String - command : pause|play|next|prev|fwd|rwd|shuffle|repeat

*/
CommandAlexa.Command = function(req,res){
	LancementCommande("Command",req);
	res.type('json');

	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Command', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('command' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Command', 'Missing parameter "command"'));
	config.logger('Alexa-API: command: ' + req.query.command);

	var err = forEachDevices(req.query.device, (serial) => {
		alexa.sendCommand(serial, req.query.command, GestionRetour);
	});
	res.status(200).json({});
}
app.get('/command', CommandAlexa.Command);
app.get('/volume', CommandAlexa.Volume);
app.get('/speak', CommandAlexa.Speak);
app.get('/radio', CommandAlexa.Radio);


/***** Alexa.Routine *****
  URL /routine?device=?&name=?
    device - String - name of the device
    routine - String - name of routine

*/
//app.get('/routine', (req, res) => {

CommandAlexa.Routine = function(req,res){
	LancementCommande("Routine",req);
	res.type('json');

	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Routine', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('routine' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Routine', 'Missing parameter "routine"'));
	config.logger('Alexa-API: routine: ' + req.query.routine);


	alexa.getAutomationRoutines2(function(niveau0) {
		var toReturn = [];
		var routineaexecuter = "";

		for (var serial in niveau0) {
			if (niveau0.hasOwnProperty(serial)) {
				var routine = niveau0[serial];

				if (routine.creationTimeEpochMillis == req.query.routine)
					routineaexecuter = routine;
			}
		}
		if (routineaexecuter != '') 
			alexa.executeAutomationRoutine(req.query.device, routineaexecuter, GestionRetour);
		else
			config.logger('Alexa-API: routine - ECHEC (introuvable) - Lancement routine: ' + req.query.routine);

		res.status(200).json({});
	});
}
app.get('/routine', CommandAlexa.Routine);


/***** Alexa.Notifications.SendMobilePush *****
  URL /push?device=?&text=?
    device - String - name of the device
    text - String - Text to display in the push notification

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
*/
app.get('/push', (req, res) => {
	config.logger('Alexa-API: Alexa.Notifications.SendMobilePush');
	res.type('json');

	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Notifications.SendMobilePush', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('text' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Notifications.SendMobilePush', 'Missing parameter "text"'));
	config.logger('Alexa-API: text: ' + req.query.text);

	alexa.sendSequenceCommand(req.query.device, 'notification', req.query.text, function(err) {
		if (err)
			return res.status(500).json(error(500, req.route.path, 'Alexa.Notifications.SendMobilePush', err));
		res.status(200).json({});
	});
});


/***** Create a reminder *****
  URL /reminder?device=?&text=?&when=?
    device - String - name of the device
    text - String - Content of the reminder
    when - String - Date at which the reminder should occur. Date format: YYYY-MM-DD HH24:MI:SS

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
*/
app.get('/reminder', (req, res) => {
	config.logger('Alexa-API: Alexa.Reminder');
	res.type('json');

	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Reminder', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('text' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Reminder', 'Missing parameter "text"'));
	config.logger('Alexa-API: text: ' + req.query.text);

	if ('when' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Reminder', 'Missing parameter "when"'));
	config.logger('Alexa-API: when: ' + req.query.when);

	// when: YYYY-MM-DD HH:MI:SS
	let dateValues = req.query.when.match(/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/);
	if (dateValues === null)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Reminder', 'Invalid "when" format. Expected: YYYY-MM-DD HH:MI:SS'));
	let when = new Date(dateValues[1], dateValues[2] - 1, dateValues[3], dateValues[4], dateValues[5], dateValues[6]);
	config.logger('Alexa-API: when: ' + when);

	alexa.setReminder(req.query.device, when.getTime(), req.query.text, function(err) {
		if (err)
			return res.status(500).json(error(500, req.route.path, 'createReminder', err));
		res.status(200).json({});
	});
});


/***** Create a alarm *****
  URL /alarm?device=?&text=?&when=?
    device - String - name of the device
    text - String - Content of the alarm
    when - String - Date at which the alarm should occur. Date format: YYYY-MM-DD HH24:MI:SS

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
*/
app.get('/alarm', (req, res) => {
	config.logger('Alexa-API: Alexa.Alarm');
	res.type('json');

	if ('device' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Alarm', 'Missing parameter "device"'));
	config.logger('Alexa-API: device: ' + req.query.device);

	if ('when' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Alarm', 'Missing parameter "when"'));
	config.logger('Alexa-API: when: ' + req.query.when);

	if ('recurring' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Alarm', 'Missing parameter "recurring"'));
	config.logger('Alexa-API: recurring: ' + req.query.recurring);


	// when: YYYY-MM-DD HH:MI:SS
	let dateValues = req.query.when.match(/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/);
	if (dateValues === null)
		return res.status(500).json(error(500, req.route.path, 'Alexa.Alarm', 'Invalid "when" format. Expected: YYYY-MM-DD HH:MI:SS'));
	let when = new Date(dateValues[1], dateValues[2] - 1, dateValues[3], dateValues[4], dateValues[5], dateValues[6]);
	config.logger('Alexa-API: when: ' + when);

	alexa.setAlarm(req.query.device, when.getTime(), req.query.recurring, function(err) {
		if (err)
			return res.status(500).json(error(500, req.route.path, 'createReminder', err));
		res.status(200).json({});
	});
});


/***** Get devices *****
  URL: /devices

  Return the list of Alexa devices
  [{
    serial - String - Serial number of the device (unique identifier)
    name: String - name of the device. Use this name (or serial) to call as "device" parameter of others methods
    type: String - Device family as defined by Amazon. Known type: TABLET (for tablet device), ECHO (for ECHO device), WHA (for group of devices), VOX (for smartphone? Webpage?)
    online: Boolean - true when the device is connected, false otherwise,
    capabilities: [String] - List of available capabilties of the device, few example: VOLUME_SETTING, REMINDERS, MICROPHONE, TUNE_IN, ...
  }]
*/
app.get('/devices', (req, res) => {
	config.logger('Alexa-API: Devices');
	res.type('json');

	alexa.getDevices(function(devices) {
		var toReturn = [];
		for (var serial in devices) {
			if (devices.hasOwnProperty(serial)) {
				var device = devices[serial];
				toReturn.push({
					'serial': serial,
					'name': device.accountName,
					'type': device.deviceFamily,
					'online': device.online,
					'capabilities': device.capabilities,
					'members': device.clusterMembers
				});
			}
		}
		res.status(200).json(toReturn);
	});
});


/***** Get devices *****
  URL: /devices

  Return the list of Alexa devices
  [{
    serial - String - Serial number of the device (unique identifier)
    name: String - name of the device. Use this name (or serial) to call as "device" parameter of others methods
    type: String - Device family as defined by Amazon. Known type: TABLET (for tablet device), ECHO (for ECHO device), WHA (for group of devices), VOX (for smartphone? Webpage?)
    online: Boolean - true when the device is connected, false otherwise,
    capabilities: [String] - List of available capabilties of the device, few example: VOLUME_SETTING, REMINDERS, MICROPHONE, TUNE_IN, ...
  }]
*/
app.get('/wakewords', (req, res) => {
	config.logger('Alexa-API: WakeWords');
	res.type('json');

	alexa.getWakeWords2(function(devices) {
		var toReturn = [];
		for (var serial in devices) {
			if (devices.hasOwnProperty(serial)) {
				var device = devices[serial];
				toReturn.push({
					'serial': serial,
					'active': device.active,
					'deviceSerialNumber': device.deviceSerialNumber,
					'wakeWord': device.wakeWord
				});
			}
		}
		res.status(200).json(toReturn);
	});
});


app.get('/history', (req, res) => {
	config.logger('Alexa-API: History');
	res.type('json');


	if ('size' in req.query === false)
		req.query.size = 1;
	if ('offset' in req.query === false)
		req.query.offset = 1;

	const options = {
		size: req.query.size,
		offset: req.query.offset
	};

	alexa.getHistory2(options, function(devices) {
		var toReturn = [];

		//console.log(devices);

		for (var serial in devices) {
			if (devices.hasOwnProperty(serial)) {
				var activities = devices[activities];
				var device = devices[serial];
				toReturn.push({
					'serial': serial,
					'activityStatus': device.activityStatus,
					'deviceSerialNumber': device.deviceSerialNumber,
					'creationTimestamp': device.creationTimestamp,
					'summary': device.description.summary
				});
			}
		}
		res.status(200).json(toReturn);
	});
});


/***** routines *****
  URL: /routines

*/
app.get('/routines', (req, res) => {
	config.logger('Alexa-API: routines');
	res.type('json');

	//config.logger('Alexa-API: type of devices : '+typeof devices);

	alexa.getAutomationRoutines2(function(niveau0) {
		//devices='{"notifications":'+devices+'}';

		//config.logger('Alexa-API: routines2');
		//  config.logger(JSON.stringify(devices));
		//  config.logger(devices);

		//config.logger('Alexa-API: type of devices : '+typeof devices);
		var resultatutterance;
		var resultatlocale;
		var resultattriggerTime;
		var resultattimeZoneId;
		var resultatrecurrence;
		//config.logger('Alexa-API: routines3b2');
		var toReturn = [];
		//config.logger('************DEBUG DE ROUTINES*******************');
		for (var serial in niveau0) {
			if (niveau0.hasOwnProperty(serial)) {
				//config.logger('************************************************');

				var routine = niveau0[serial];

				//config.logger('(general)----- '+routine.status);
				//config.logger('(general)----- '+routine.creationTimeEpochMillis);

				if (routine.status === 'ENABLED') {
					//config.logger('(SUPPRESSION)----- '+routine.creationTimeEpochMillis);
					alexa.executeAutomationRoutine("", routine, function(err) {
						//config.logger('(SUPPRESSION DEDANS)----- '+routine.creationTimeEpochMillis);
						//executeAutomationRoutine(serialOrName, routine, callback)
						//res.status(200).json({});
					});
					//config.logger('(SUPPRESSION)----- '+routine.creationTimeEpochMillis);
				}



				for (var serial2 in routine.triggers) {
					if (routine.triggers.hasOwnProperty(serial2)) {
						var niveau2 = routine.triggers[serial2];

						resultatutterance = "";
						resultatlocale = "";
						resultattriggerTime = "";
						resultattimeZoneId = "";
						resultatrecurrence = "";

						for (var triggers in niveau2.payload) { //Partie PAYLOAD
							if (niveau2.payload.hasOwnProperty(triggers)) {
								var niveau3 = niveau2.payload[triggers];
								//config.logger('(triggers1)----- '+triggers.locale);
								//config.logger('(triggers2)----- '+niveau3.locale);	
								//config.logger('(triggers3)----- '+triggers+' : '+niveau3);

								switch (triggers) {

									case 'utterance':
										resultatutterance = niveau3;
										break;

									case 'locale':
										resultatlocale = niveau3;
										break;

									case 'schedule':
										for (var schedule in niveau3) { //Partie schedule
											if (niveau3.hasOwnProperty(schedule)) {
												var niveau4 = niveau3[schedule];
												//config.logger('(schedule)----- '+schedule+' : '+niveau4);

												switch (schedule) {

													case 'triggerTime':
														resultattriggerTime = niveau4;
														break;

													case 'timeZoneId':
														resultattimeZoneId = niveau4;
														break;
													case 'recurrence':
														resultatrecurrence = niveau4;
														break;
												}
											}
										}
										break;


								}
							}
						}
					}
				}
				/*
						for (Var serial2 in routine.sequence) //partie SEQUENCE non utilisé à ce stade
						{
							  var niveau2 = routine[serial2];
								
								config.logger('(sequence)----- '+serial2+' : '+niveau2);


						}
						
						
						
				*/




				toReturn.push({
					'status': routine.status,
					'locale': resultatlocale,
					'utterance': resultatutterance,
					'triggerTime': resultattriggerTime,
					'timeZoneId': resultattimeZoneId,
					'recurrence': resultatrecurrence,

					'creationTimeEpochMillis': routine.creationTimeEpochMillis,
					'lastUpdatedTimeEpochMillis': routine.lastUpdatedTimeEpochMillis
					// 'members': device.clusterMembers
				});

				
			}
		}
	res.status(200).json(toReturn);
	});
});


/***** Reminders *****
  URL: /reminders

  Return the list of reminders
  [{
    serial - String - Serial nu=mber of the device (unique identifier)
    name: String - name of the device. Use this name (or serial) to call as "device" parameter of others methods
    type: String - Device family as defined by Amazon. Known type: TABLET (for tablet device), ECHO (for ECHO device), WHA (for group of devices), VOX (for smartphone? Webpage?)
    online: Boolean - true when the device is connected, false oe,
    capabilities: [String] - List of available capabilties of the device, few example: VOLUME_SETTING, REMINDERS, MICROPHONE, TUNE_IN, ...
  }]

*/
app.get('/reminders', (req, res) => {
	config.logger('Alexa-API: Reminders');
	res.type('json');

	config.logger('Alexa-API: (reminders) Lancement');

	alexa.getNotifications2(function(notifications) {
		config.logger('Alexa-API: (reminders) function');
		var toReturn = [];

		for (var serial in notifications) {
			if (notifications.hasOwnProperty(serial)) {
				var device = notifications[serial];
				toReturn.push({
					'serial': serial,
					'deviceSerialNumber': device.deviceSerialNumber,
					'type': device.type,
					'originalTime': device.originalTime,
					'musicEntity': device.musicEntity,
					'originalDate': device.originalDate,
					'remainingTime': device.remainingTime,
					'status': device.status,
					'recurringPattern': device.recurringPattern,
					'reminderLabel': device.reminderLabel,
					'id': device.id
				});
			}
		}
		res.status(200).json(toReturn);
	});
});


/***** DeleteReminder *****
  URL: /deletereminder

  Return the list of reminders
  [{
    id - String - id of the reminder (unique identifier)
  }]

*/
app.get('/deletereminder', (req, res) => {
	config.logger('Alexa-API: DeleteReminder');
	res.type('json');

	if ('id' in req.query === false)
		return res.status(500).json(error(500, req.route.path, 'Alexa.DeleteReminder', 'Missing parameter "id"'));

	const notification = {
		'id': req.query.id
	};

	alexa.deleteNotification(notification, function(err) {
		if (err)
			return res.status(500).json(error(500, req.route.path, 'Alexa.Notifications.DeleteReminder', err));
		res.status(200).json({});
	});
});


/***** DeleteAllAlarms *****
  URL: /deleteallalarms

  Supprime toutes les alamrmes et/ou tous les rappels
  [{
    
  }]

*/
app.get('/deleteallalarms', (req, res) => {
	config.logger('Alexa-API: DeleteAllAlarms');
	res.type('json');

	alexa.getNotifications2(function(notifications) {
		//var toReturn = [];

		// Filtre et ne garde que les enregistrements du device selctionné
		const notificationsfiltrees = notifications.filter(tmp => tmp.deviceSerialNumber == req.query.device);
		notifications = notificationsfiltrees;

		config.logger('Alexa-API - deleteallalarms req.query.type: ' + req.query.type);

		if ((req.query.type != 'all') && (req.query.type != 'ALL')) {
			var notificationsfiltrees1;
			if ((req.query.type == 'reminders') || (req.query.type == 'REMINDERS'))
				notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Reminder");
			else
				notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Alarm"); //Par défaut donc
			notifications = notificationsfiltrees1;
		}


		// Filtre et ne garde que les enregistrements qui ont un status qui correspond à req.query.status
		if ((req.query.status != 'all') && (req.query.status != 'ALL')) {
			var FiltreSurStatus = 'ON';
			if ((req.query.status == 'off') || (req.query.status == 'OFF')) FiltreSurStatus = 'OFF';
			const notificationsfiltrees2 = notifications.filter(tmp => tmp.status == FiltreSurStatus);
			notifications = notificationsfiltrees2;
		}


		for (var serial in notifications) {
			if (notifications.hasOwnProperty(serial)) {
				// On va parcourir les résultats et supprimer chaque enregistrement

				var device = notifications[serial];
				config.logger('Alexa-API - DeleteAllAlarms delete id: ' + device.id);

				const notification = {
					'id': device.id
				};
				alexa.deleteNotification(notification, function(err) {});
			}
		}
	});

	res.status(200).json({
		value: "Fini"
	});
});


/***** WhenNextAlarm *****
  URL: /whennextalarm

  Return la prochaine alarme
  [{
    position => 1= prochaine 2=suivante ...
	status => Filtre sur le status (active=ON, désactive=OFF, Tous =ALL)
	format => Format du résultat (HOUR=réduit HH:SS)
  }]

*/
app.get('/whennextalarm', (req, res) => {
	config.logger('Alexa-API: WhenNextAlarm');
	res.type('json');

	alexa.getNotifications2(function(notifications) {
		//config.logger('Alexa-API: (WhenNextAlarm) function' );
		//var toReturn = [];

		// Filtre et ne garde que les enregistrements du device selctionné
		const notificationsfiltrees = notifications.filter(tmp => tmp.deviceSerialNumber == req.query.device);
		notifications = notificationsfiltrees;

		// Filtre et ne garde que les enregistrements qui ont le type ALARM
		const notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Alarm");
		notifications = notificationsfiltrees1;


		// Filtre et ne garde que les enregistrements qui sont supérieure à l'heure du jour
		//Maintenant :
		var d = new Date();
		var date_format_str = d.getFullYear().toString() + "-" + ((d.getMonth() + 1).toString().length == 2 ? (d.getMonth() + 1).toString() : "0" + (d.getMonth() + 1).toString()) + "-" + (d.getDate().toString().length == 2 ? d.getDate().toString() : "0" + d.getDate().toString()) + " " + (d.getHours().toString().length == 2 ? d.getHours().toString() : "0" + d.getHours().toString()) + ":" + ((parseInt(d.getMinutes() / 5) * 5).toString().length == 2 ? (parseInt(d.getMinutes() / 5) * 5).toString() : "0" + (parseInt(d.getMinutes() / 5) * 5).toString()) + ":00";
		const notificationsfiltrees4 = notifications.filter(tmp => (tmp.originalDate + ' ' + tmp.originalTime > date_format_str));
		notifications = notificationsfiltrees4;

		// Filtre et ne garde que les enregistrements qui ont un status qui correspond à req.query.status
		if ((req.query.status != 'all') && (req.query.status != 'ALL')) {
			var FiltreSurStatus = 'ON';
			if ((req.query.status == 'off') || (req.query.status == 'OFF')) FiltreSurStatus = 'OFF';
			const notificationsfiltrees2 = notifications.filter(tmp => tmp.status == FiltreSurStatus);
			notifications = notificationsfiltrees2;
		}

		// Trie par Date/Heure
		const notificationsfiltrees3 = notifications.sort(function(a, b) {
			var x = a.originalDate + a.originalTime;
			var y = b.originalDate + b.originalTime;
			return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		});
		notifications = notificationsfiltrees3;


		var compteurdePosition = 1;
		var compteurdePositionaTrouver = 1;
		var stringarenvoyer = 'none';
		//config.logger('Alexa-API - WhenNextAlarm req.query.position: ' + req.query.position);
		if (req.query.position > 1) {
			compteurdePositionaTrouver = req.query.position;
		}

		for (var serial in notifications) {
			if (notifications.hasOwnProperty(serial)) {
				// On va parcourir les résultats en allant à la position demandée.
				//config.logger('Alexa-API - Position à trouver : ' + compteurdePositionaTrouver);
				//config.logger('Alexa-API - Position de la boucle : ' + compteurdePosition);

				if (compteurdePositionaTrouver == compteurdePosition) {
					var device = notifications[serial];

					//config.logger('Alexa-API - WhenNextAlarm serial: ' + serial);
					//config.logger('Alexa-API - WhenNextAlarm device.deviceSerialNumber: ' + device.deviceSerialNumber);//deviceSerialNumber
					//config.logger('Alexa-API - WhenNextAlarm device.type: ' + device.type);
					//config.logger('Alexa-API - WhenNextAlarm device.originalDate: ' + device.originalDate);
					//config.logger('Alexa-API - WhenNextAlarm device.originalTime: ' + device.originalTime);
					//config.logger('Alexa-API - WhenNextAlarm device.status: ' + device.status);


					//C'est bon, on est sur la bonne position, on renvoie le résultat


					switch (req.query.format) {
						case 'hour':
						case 'HOUR':
							stringarenvoyer = device.originalTime.substring(0, 5);
							break;
						case 'full':
						case 'FULL':
							stringarenvoyer = device.originalDate + " " + device.originalTime;
							break;
						default: //ou HHMM
							stringarenvoyer = device.originalTime.substring(0, 5).replace(':', ''); // Utilisation du format HH:MM
					}

				}
				compteurdePosition++;
			}
		}
		//  res.status(200).json(toReturn);
		res.status(200).json({
			value: stringarenvoyer
		});

	});
});


/***** WhenNextReminder *****
  URL: /whennextreminder

  Return le prochain rappel
  [{
    position => 1= prochaine 2=suivante ...
	status => Filtre sur le status (active=ON, désactive=OFF, Tous =ALL)
	format => Format du résultat (HOUR=réduit HH:SS)
  }]

*/
app.get('/whennextreminder', (req, res) => {
	config.logger('Alexa-API: WhenNextReminder');
	res.type('json');


	alexa.getNotifications2(function(notifications) {
		//config.logger('Alexa-API: (WhenNextAlarm) function' );
		//var toReturn = [];

		// Filtre et ne garde que les enregistrements du device selctionné
		const notificationsfiltrees = notifications.filter(tmp => tmp.deviceSerialNumber == req.query.device);
		notifications = notificationsfiltrees;

		// Filtre et ne garde que les enregistrements qui ont le type ALARM
		const notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Reminder");
		notifications = notificationsfiltrees1;


		// Filtre et ne garde que les enregistrements qui sont supérieure à l'heure du jour
		//Maintenant :
		var d = new Date();
		var date_format_str = d.getFullYear().toString() + "-" + ((d.getMonth() + 1).toString().length == 2 ? (d.getMonth() + 1).toString() : "0" + (d.getMonth() + 1).toString()) + "-" + (d.getDate().toString().length == 2 ? d.getDate().toString() : "0" + d.getDate().toString()) + " " + (d.getHours().toString().length == 2 ? d.getHours().toString() : "0" + d.getHours().toString()) + ":" + ((parseInt(d.getMinutes() / 5) * 5).toString().length == 2 ? (parseInt(d.getMinutes() / 5) * 5).toString() : "0" + (parseInt(d.getMinutes() / 5) * 5).toString()) + ":00";
		const notificationsfiltrees4 = notifications.filter(tmp => (tmp.originalDate + ' ' + tmp.originalTime > date_format_str));
		notifications = notificationsfiltrees4;

		// Filtre et ne garde que les enregistrements qui ont un status qui correspond à req.query.status
		if ((req.query.status != 'all') && (req.query.status != 'ALL')) {
			var FiltreSurStatus = 'ON';
			if ((req.query.status == 'off') || (req.query.status == 'OFF')) FiltreSurStatus = 'OFF';
			const notificationsfiltrees2 = notifications.filter(tmp => tmp.status == FiltreSurStatus);
			notifications = notificationsfiltrees2;
		}

		// Trie par Date/Heure
		const notificationsfiltrees3 = notifications.sort(function(a, b) {
			var x = a.originalDate + a.originalTime;
			var y = b.originalDate + b.originalTime;
			return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		});
		notifications = notificationsfiltrees3;


		var compteurdePosition = 1;
		var compteurdePositionaTrouver = 1;
		var stringarenvoyer = 'none';
		if (req.query.position > 1) {
			compteurdePositionaTrouver = req.query.position;
		}

		for (var serial in notifications) {
			if (notifications.hasOwnProperty(serial)) {
				if (compteurdePositionaTrouver == compteurdePosition) {
					var device = notifications[serial];

					//C'est bon, on est sur la bonne position, on renvoie le résultat

					switch (req.query.format) {
						case 'hour':
						case 'HOUR':
							stringarenvoyer = device.originalTime.substring(0, 5);
							break;
						case 'full':
						case 'FULL':
							stringarenvoyer = device.originalDate + " " + device.originalTime;
							break;
						default: //ou HHMM
							stringarenvoyer = device.originalTime.substring(0, 5).replace(':', ''); // Utilisation du format HH:MM
					}
				}
				compteurdePosition++;
			}
		}
		res.status(200).json({
			value: stringarenvoyer
		});

	});
});


/***** Stop the server *****/
app.get('/stop', (req, res) => {
	config.logger('Alexa-API: Shuting down');
	res.status(200).json({});
	server.close(() => {
		process.exit(0);
	});
});


/* Main */
fs.readFile(config.cookieLocation, 'utf8', (err, data) => {
	if (err) {
		config.logger('Alexa-API: Error while loading the file: ' + config.cookieLocation);
		config.logger('Alexa-API: ' + err);
		process.exit(-1);
	}

	config.cookie = JSON.parse(data);
	startServer();
});

function startServer() {
	config.logger('Alexa-API: ******************** Lancement Serveur ***********************');
	alexa.init({
			cookie: config.cookie,
			logger: config.logger,
			alexaServiceHost: config.alexaServiceHost,
			cookieRefreshInterval: config.cookieRefreshInterval
		},
		(err) => {
			// Unable to init alexa
			if (err) {
				config.logger('Alexa-API: Error while initializing alexa');
				config.logger('Alexa-API: ' + err);
				process.exit(-1);
			}

			if (alexa.cookieData) {
				fs.writeFile(config.cookieLocation, JSON.stringify(alexa.cookieData), 'utf8', (err) => {
					if (err) {
						config.logger('Alexa-API - Error while saving the cookie to: ' + config.cookieLocation);
						config.logger('Alexa-API - ' + err);
					}
					config.logger('Alexa-API - New cookie saved to:' + config.cookieLocation);

					// Start the server
					if (server) {
						config.logger('Alexa-API: *******************************************');
						config.logger('Alexa-API: *Server is already listening on port ' + server.address().port + ' *');
						config.logger('Alexa-API: *******************************************');
					} else {
						server = app.listen(config.listeningPort, () => {
							config.logger('Alexa-API: **************************************************************');
							config.logger('Alexa-API: ************** Server OK listening on port ' + server.address().port + ' **************');
							config.logger('Alexa-API: **************************************************************');

						});
					}
					//AllerVoirSilYaDesCommandesenFileAttente();
				});
			}
		});
}

//alexa.sendSequenceCommand(serial, 'speak', req.query.text, GestionErreur);

//Gestion des erreurs et surtout pour détecter les ConnexionClose

function GestionRetour(err) {
		
	
//message::add('alexaapi', 'Sigalou est le meilleur');
	//var hasError = false;
	if (err) {
		config.logger('Alexa-API: ******************************************************************');
		config.logger('Alexa-API: *****************************ERROR********************************');
		config.logger('Alexa-API: ******************************************************************');
		config.logger(err.message);
		config.logger('Alexa-API: ******************************************************************');
		config.logger(err);
		config.logger('Alexa-API: ******************************************************************');
		config.logger('Alexa-API: ******************************************************************');

		//var errorMessage = err.message;
		//hasError = true;
		if (err.message == "Connexion Close") {
			config.logger("Connexion Close détectée dans la détection d'erreur et donc relance de l'initialisation");
			
			
				//************ Faut arreter mieux le serveur et relancer
				//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			startServer();
			
			
			//AllerVoirSilYaDesCommandesenFileAttente();
		}
		
	hasError = true;
	errorMessage = err.message;
	}
	//else
	//FinCommandeBienExecutee();

//if(typeof(next) == "function") next(); Le garder si un jour on enchaine les commandes, pour avoir la synthaxe.
		config.logger('Alexa-API: 2222222EEEEEEEEERRRRRRREEEEEEEEEEUUUUUUUUUUUURRRRR : '+err);
		
}

function traiteErreur(err) {
		
	
//message::add('alexaapi', 'Sigalou est le meilleur');
	//var hasError = false;

		config.logger('Alexa-API: ******************************************************************');
		config.logger('Alexa-API: *****************************ERROR********************************');
		config.logger('Alexa-API: ******************************************************************');
		config.logger(err.message);
		config.logger('Alexa-API: ******************************************************************');
		config.logger(err);
		config.logger('Alexa-API: ******************************************************************');
		config.logger('Alexa-API: ******************************************************************');

		//var errorMessage = err.message;
		//hasError = true;
		if (err.message == "Connexion Close") {
			config.logger("Connexion Close détectée dans la détection d'erreur et donc relance de l'initialisation");
			startServer();
			//AllerVoirSilYaDesCommandesenFileAttente();
		}
		
	//hasError = true;
	//errorMessage = err.message;


//if(typeof(next) == "function") next(); Le garder si un jour on enchaine les commandes, pour avoir la synthaxe.
		//config.logger('Alexa-API: 2222222EEEEEEEEERRRRRRREEEEEEEEEEUUUUUUUUUUUURRRRR : '+err);
		
}
function error(status, source, title, detail) {
	let error = {
		'status': status,
		'title': title,
		'detail': detail
	};

	config.logger('Alexa-API: ' + title + ': ' + detail);
	return error;
}
