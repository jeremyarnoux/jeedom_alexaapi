const express = require('express');
const fs = require('fs');
const Alexa = require('./lib/alexa-remote.js');
let alexa = new Alexa();

const amazonserver = process.argv[3];
const alexaserver = process.argv[4];


/* Configuration */
const config =
{
  cookieLocation: __dirname + '/data/alexa-cookie.json',
  cookieRefreshInterval: 7*24*60*1000,
  logger: console.log,
  alexaServiceHost: alexaserver,
  listeningPort: 3456
};

// Par sécurité pour détecter un éventuel souci :
if (!amazonserver) config.logger && config.logger('Alexa-Config: *********************amazonserver NON DEFINI*********************');
if (!alexaserver) config.logger && config.logger('Alexa-Config: *********************alexaserver NON DEFINI*********************');

config.logger && config.logger('Alexa-Config (alexaapi.js): amazonserver='+amazonserver);
config.logger && config.logger('Alexa-Config (alexaapi.js): alexaserver='+alexaserver);

/* Routing */
const app = express();
let server = null;

/* Apply callback on every cluster's membre (for multiroom device) */
function forEachDevices(nameOrSerial, callback)
{
  var device = alexa.find(nameOrSerial);
  if (device === undefined)
    return;

  if (device.clusterMembers.length == 0)
    callback(device.serialNumber);

  for(var i in device.clusterMembers)
    callback(device.clusterMembers[i]);
}

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
app.get('/speak', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Alexa.Speak');
  res.type('json');

  if ('device' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.Speak', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('text' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.Speak', 'Missing parameter "text"'));
  config.logger && config.logger('Alexa-API: text: ' + req.query.text);

  if ('volume' in req.query)
  {
    config.logger && config.logger('Alexa-API: volume: ' + req.query.volume);
    var hasError = false;
    forEachDevices(req.query.device, (serial) =>
    {
      alexa.sendSequenceCommand(serial, 'volume', req.query.volume, (err) =>
      {
        if (err)
          hasError = true;
      });
    });
    if (hasError)
      return res.status(500).json(error(500, req.route, 'Alexa.DeviceControls.Volume', err.message));
  }

  var hasError = false;
  var errorMessage = '';
  forEachDevices(req.query.device, (serial) =>
  {
    alexa.sendSequenceCommand(serial, 'speak', req.query.text, (err) =>
    {
      if (err)
      {
        errorMessage = err.message;
        hasError = true;
      }
    });
  });

  if (hasError)
    res.status(500).json(error(500, req.route.path, 'Alexa.Speak', errorMessage));
  else
    res.status(200).json({});
});

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
app.get('/radio', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Alexa.Speak');
  res.type('json');

  if ('device' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.Speak', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('station' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.Speak', 'Missing parameter "station"'));
  config.logger && config.logger('Alexa-API: station: ' + req.query.station);

  if ('volume' in req.query)
  {
    config.logger && config.logger('Alexa-API: volume: ' + req.query.volume);
    var hasError = false;
    forEachDevices(req.query.device, (serial) =>
    {
      alexa.sendSequenceCommand(serial, 'volume', req.query.volume, (err) =>
      {
        if (err)
          hasError = true;
      });
    });
    if (hasError)
      return res.status(500).json(error(500, req.route, 'Alexa.DeviceControls.Volume', err.message));
  }

  var hasError = false;
  var errorMessage = '';
  forEachDevices(req.query.device, (serial) =>
  {
	  //     setTunein(serialOrName, guideId, contentType, callback) {

    alexa.setTunein(serial, req.query.station, (err) =>
    {
      if (err)
      {
        errorMessage = err.message;
        hasError = true;
      }
    });
  });

  if (hasError)
    res.status(500).json(error(500, req.route.path, 'Alexa.Speak', errorMessage));
  else
    res.status(200).json({});
});


/***** Alexa.DeviceControls.Volume *****
  URL: /volume?device=?&value=?
    device - String - name of the device
    value - Integer - Determine the volume level between 0 to 100 (0 is mute and 100 is max)

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
  FIXME: Currently, the librarie returns an "false" error when the command succeed but no body was returned by Amazon
*/
app.get('/volume', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Alexa.DeviceControls.Volume');
  res.type('json');

  if ('device' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Volume', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('value' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Volume', 'Missing parameter "value"'));
  config.logger && config.logger('Alexa-API: value: ' + req.query.value);

  var err = forEachDevices(req.query.device, (serial) =>
  {
    alexa.sendSequenceCommand(serial, 'volume', req.query.value, (err) => {return err;});
 
 if (err)
    return res.status(500).json(error(500, req.route, 'Alexa.DeviceControls.Volume', err));
  res.status(200).json({});  });


});

/***** Alexa.DeviceControls.Command *****
  URL: /command?device=?&command=?
    device - String - name of the device
    command - String - command : pause|play|next|prev|fwd|rwd|shuffle|repeat

*/
app.get('/command', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Alexa.DeviceControls.Command');
  res.type('json');

  if ('device' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Command', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('command' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Command', 'Missing parameter "command"'));
  config.logger && config.logger('Alexa-API: command: ' + req.query.command);

  var err = forEachDevices(req.query.device, (serial) =>
	  {	
		alexa.sendCommand(serial, req.query.command, (err) => {return err;});

	  if (err)
		return res.status(500).json(error(500, req.route, 'Alexa.DeviceControls.Command', err));
	  res.status(200).json({});
	  });
});



/***** Alexa.Notifications.SendMobilePush *****
  URL /push?device=?&text=?
    device - String - name of the device
    text - String - Text to display in the push notification

  Return an empty object if the function succeed.
  Otherwise, an error object is returned.
*/
app.get('/push', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Alexa.Notifications.SendMobilePush');
  res.type('json');

  if ('device' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.Notifications.SendMobilePush', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('text' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.Notifications.SendMobilePush', 'Missing parameter "text"'));
  config.logger && config.logger('Alexa-API: text: ' + req.query.text);

  alexa.sendSequenceCommand(req.query.device, 'notification', req.query.text, function(err)
  {
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
app.get('/reminder', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Alexa.Reminder');
  res.type('json');

  if ('device' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Reminder', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('text' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Reminder', 'Missing parameter "text"'));
  config.logger && config.logger('Alexa-API: text: ' + req.query.text);

  if ('when' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Reminder', 'Missing parameter "when"'));
  config.logger && config.logger('Alexa-API: when: ' + req.query.when);

  // when: YYYY-MM-DD HH:MI:SS
  let dateValues = req.query.when.match(/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/);
  if (dateValues === null)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Reminder', 'Invalid "when" format. Expected: YYYY-MM-DD HH:MI:SS'));
  let when = new Date(dateValues[1], dateValues[2]-1, dateValues[3], dateValues[4], dateValues[5], dateValues[6])
  config.logger && config.logger('Alexa-API: when: ' + when);

  alexa.setReminder(req.query.device, when.getTime(), req.query.text, function(err)
  {
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
app.get('/alarm', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Alexa.Alarm');
  res.type('json');

  if ('device' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Alarm', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('when' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Alarm', 'Missing parameter "when"'));
  config.logger && config.logger('Alexa-API: when: ' + req.query.when);

  if ('recurring' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Alarm', 'Missing parameter "recurring"'));
  config.logger && config.logger('Alexa-API: recurring: ' + req.query.recurring);


  // when: YYYY-MM-DD HH:MI:SS
  let dateValues = req.query.when.match(/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/);
  if (dateValues === null)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Alarm', 'Invalid "when" format. Expected: YYYY-MM-DD HH:MI:SS'));
  let when = new Date(dateValues[1], dateValues[2]-1, dateValues[3], dateValues[4], dateValues[5], dateValues[6])
  config.logger && config.logger('Alexa-API: when: ' + when);

 alexa.setAlarm(req.query.device, when.getTime(), req.query.recurring, function(err)
  {
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
app.get('/devices', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Devices');
  res.type('json');

  alexa.getDevices(function(devices)
  {
    var toReturn = [];
    for (var serial in devices)
    {
      var device = devices[serial];
      toReturn.push({
        'serial': serial,
        'name': device.accountName,
        'type': device.deviceFamily,
        'online': device.online,
        'capabilities' : device.capabilities,
        'members': device.clusterMembers
      });
    }
    res.status(200).json(toReturn);
  });
});

/***** Reminders *****
  URL: /reminders

  Return the list of reminders
  [{
    serial - String - Serial number of the device (unique identifier)
    name: String - name of the device. Use this name (or serial) to call as "device" parameter of others methods
    type: String - Device family as defined by Amazon. Known type: TABLET (for tablet device), ECHO (for ECHO device), WHA (for group of devices), VOX (for smartphone? Webpage?)
    online: Boolean - true when the device is connected, false otherwise,
    capabilities: [String] - List of available capabilties of the device, few example: VOLUME_SETTING, REMINDERS, MICROPHONE, TUNE_IN, ...
  }]

*/

app.get('/reminders', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Reminders');
  res.type('json');

config.logger && config.logger('Alexa-API: (reminders) Lancement' );

  alexa.getNotifications2(function(notifications)
  {
config.logger && config.logger('Alexa-API: (reminders) function' );
    var toReturn = [];

    for (var serial in notifications)
    {
      var device = notifications[serial];
      toReturn.push({
        'serial': serial,
        'deviceSerialNumber': device.deviceSerialNumber,
        'type': device.type,
        'originalTime': device.originalTime,
        'originalDate': device.originalDate,
        'status' : device.status,
        'recurringPattern' : device.recurringPattern,
        'reminderLabel': device.reminderLabel,
        'id': device.id
      });
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

app.get('/deletereminder', (req, res) =>
{
  config.logger && config.logger('Alexa-API: DeleteReminder');
  res.type('json');

  if ('id' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.DeleteReminder', 'Missing parameter "id"'));

        const notification = {
            'id': req.query.id
        };

  alexa.deleteNotification(notification, function(err)
  {
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

app.get('/deleteallalarms', (req, res) =>
{
  config.logger && config.logger('Alexa-API: DeleteAllAlarms');
  res.type('json');

alexa.getNotifications2(function(notifications)
  {
    var toReturn = [];

	// Filtre et ne garde que les enregistrements du device selctionné
	const notificationsfiltrees = notifications.filter(tmp => tmp.deviceSerialNumber == req.query.device);
	notifications=notificationsfiltrees;

		config.logger && config.logger('Alexa-API - deleteallalarms req.query.type: ' + req.query.type);

	if ((req.query.type!='all') && (req.query.type!='ALL'))
	{
		if ((req.query.type=='reminders') || (req.query.type=='REMINDERS'))
			notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Reminder");
		else
			notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Alarm"); //Par défaut donc
	notifications=notificationsfiltrees1;
	}


	// Filtre et ne garde que les enregistrements qui ont un status qui correspond à req.query.status
	if ((req.query.status!='all') && (req.query.status!='ALL'))
	{
	$FiltreSurStatus='ON';	
	if ((req.query.status=='off') || (req.query.status=='OFF')) $FiltreSurStatus='OFF';	
	const notificationsfiltrees2 = notifications.filter(tmp => tmp.status == $FiltreSurStatus);
	notifications=notificationsfiltrees2;	
	}


    for (var serial in notifications)
    {
      // On va parcourir les résultats et supprimer chaque enregistrement
					  
				var device = notifications[serial];
				config.logger && config.logger('Alexa-API - DeleteAllAlarms delete id: ' + device.id);

				const notification = {'id': device.id};
					  alexa.deleteNotification(notification, function(err)
					  {});
				
    }

  });

  res.status(200).json({value: "Fini"});
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

app.get('/whennextalarm', (req, res) =>
{
  config.logger && config.logger('Alexa-API: WhenNextAlarm');
  res.type('json');

  alexa.getNotifications2(function(notifications)
  {
//config.logger && config.logger('Alexa-API: (WhenNextAlarm) function' );
    var toReturn = [];

	// Filtre et ne garde que les enregistrements du device selctionné
	const notificationsfiltrees = notifications.filter(tmp => tmp.deviceSerialNumber == req.query.device);
	notifications=notificationsfiltrees;

	// Filtre et ne garde que les enregistrements qui ont le type ALARM
	const notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Alarm");
	notifications=notificationsfiltrees1;
	

	// Filtre et ne garde que les enregistrements qui sont supérieure à l'heure du jour
		//Maintenant :
		d = new Date();
		var date_format_str = d.getFullYear().toString()+"-"+((d.getMonth()+1).toString().length==2?(d.getMonth()+1).toString():"0"+(d.getMonth()+1).toString())+"-"+(d.getDate().toString().length==2?d.getDate().toString():"0"+d.getDate().toString())+" "+(d.getHours().toString().length==2?d.getHours().toString():"0"+d.getHours().toString())+":"+((parseInt(d.getMinutes()/5)*5).toString().length==2?(parseInt(d.getMinutes()/5)*5).toString():"0"+(parseInt(d.getMinutes()/5)*5).toString())+":00";
	const notificationsfiltrees4 = notifications.filter(tmp => (tmp.originalDate+' '+tmp.originalTime > date_format_str));
	notifications=notificationsfiltrees4;

	// Filtre et ne garde que les enregistrements qui ont un status qui correspond à req.query.status
	if ((req.query.status!='all') && (req.query.status!='ALL'))
	{
	$FiltreSurStatus='ON';	
	if ((req.query.status=='off') || (req.query.status=='OFF')) $FiltreSurStatus='OFF';	
	const notificationsfiltrees2 = notifications.filter(tmp => tmp.status == $FiltreSurStatus);
	notifications=notificationsfiltrees2;	
	}

	// Trie par Date/Heure
	const notificationsfiltrees3 = notifications.sort(function (a,b) {
    var x = a.originalDate+a.originalTime; 
    var y = b.originalDate+b.originalTime;
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));});
	notifications=notificationsfiltrees3;	


	var compteurdePosition=1;
	var compteurdePositionaTrouver=1;
	var stringarenvoyer='none';
		//config.logger && config.logger('Alexa-API - WhenNextAlarm req.query.position: ' + req.query.position);
	if (req.query.position>1)
	{
	compteurdePositionaTrouver=req.query.position;
	}

    for (var serial in notifications)
    {
      // On va parcourir les résultats en allant à la position demandée.
          //config.logger && config.logger('Alexa-API - Position à trouver : ' + compteurdePositionaTrouver);
          //config.logger && config.logger('Alexa-API - Position de la boucle : ' + compteurdePosition);
  
			  if (compteurdePositionaTrouver==compteurdePosition)
			  {
				  var device = notifications[serial];
				  
					  //config.logger && config.logger('Alexa-API - WhenNextAlarm serial: ' + serial);
					  //config.logger && config.logger('Alexa-API - WhenNextAlarm device.deviceSerialNumber: ' + device.deviceSerialNumber);//deviceSerialNumber
					  //config.logger && config.logger('Alexa-API - WhenNextAlarm device.type: ' + device.type);
					  //config.logger && config.logger('Alexa-API - WhenNextAlarm device.originalDate: ' + device.originalDate);
					  //config.logger && config.logger('Alexa-API - WhenNextAlarm device.originalTime: ' + device.originalTime);
					  //config.logger && config.logger('Alexa-API - WhenNextAlarm device.status: ' + device.status);


						//C'est bon, on est sur la bonne position, on renvoie le résultat
						if ((req.query.format=="hour") || (req.query.format=="hour")) // Utilisation du format HH:MM
							stringarenvoyer=device.originalTime.substring(0, 5);	
							else
							stringarenvoyer=device.originalDate+" "+device.originalTime;
			  }
	compteurdePosition++;
 
 
    }
  //  res.status(200).json(toReturn);
  res.status(200).json({value: stringarenvoyer});

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

app.get('/whennextreminder', (req, res) =>
{
  config.logger && config.logger('Alexa-API: WhenNextReminder');
  res.type('json');


  alexa.getNotifications2(function(notifications)
  {
//config.logger && config.logger('Alexa-API: (WhenNextAlarm) function' );
    var toReturn = [];

	// Filtre et ne garde que les enregistrements du device selctionné
	const notificationsfiltrees = notifications.filter(tmp => tmp.deviceSerialNumber == req.query.device);
	notifications=notificationsfiltrees;

	// Filtre et ne garde que les enregistrements qui ont le type ALARM
	const notificationsfiltrees1 = notifications.filter(tmp => tmp.type == "Reminder");
	notifications=notificationsfiltrees1;
	

	// Filtre et ne garde que les enregistrements qui sont supérieure à l'heure du jour
		//Maintenant :
		d = new Date();
		var date_format_str = d.getFullYear().toString()+"-"+((d.getMonth()+1).toString().length==2?(d.getMonth()+1).toString():"0"+(d.getMonth()+1).toString())+"-"+(d.getDate().toString().length==2?d.getDate().toString():"0"+d.getDate().toString())+" "+(d.getHours().toString().length==2?d.getHours().toString():"0"+d.getHours().toString())+":"+((parseInt(d.getMinutes()/5)*5).toString().length==2?(parseInt(d.getMinutes()/5)*5).toString():"0"+(parseInt(d.getMinutes()/5)*5).toString())+":00";
	const notificationsfiltrees4 = notifications.filter(tmp => (tmp.originalDate+' '+tmp.originalTime > date_format_str));
	notifications=notificationsfiltrees4;

	// Filtre et ne garde que les enregistrements qui ont un status qui correspond à req.query.status
	if ((req.query.status!='all') && (req.query.status!='ALL'))
	{
	$FiltreSurStatus='ON';	
	if ((req.query.status=='off') || (req.query.status=='OFF')) $FiltreSurStatus='OFF';	
	const notificationsfiltrees2 = notifications.filter(tmp => tmp.status == $FiltreSurStatus);
	notifications=notificationsfiltrees2;	
	}

	// Trie par Date/Heure
	const notificationsfiltrees3 = notifications.sort(function (a,b) {
    var x = a.originalDate+a.originalTime; 
    var y = b.originalDate+b.originalTime;
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));});
	notifications=notificationsfiltrees3;	


	var compteurdePosition=1;
	var compteurdePositionaTrouver=1;
	var stringarenvoyer='none';
	if (req.query.position>1)
	{
	compteurdePositionaTrouver=req.query.position;
	}

    for (var serial in notifications)
    {
  
			  if (compteurdePositionaTrouver==compteurdePosition)
			  {
				  var device = notifications[serial];
				  

						//C'est bon, on est sur la bonne position, on renvoie le résultat
						if ((req.query.format=="hour") || (req.query.format=="hour")) // Utilisation du format HH:MM
							stringarenvoyer=device.originalTime.substring(0, 5);	
							else
							stringarenvoyer=device.originalDate+" "+device.originalTime;
			  }
	compteurdePosition++;
 
 
    }
  res.status(200).json({value: stringarenvoyer});

  });
});


/***** Stop the server *****/
app.get('/stop', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Shuting down');
  res.status(200).json({});
  server.close(() =>
  {
    process.exit(0);
  });
});

/* Main */
fs.readFile(config.cookieLocation, 'utf8', (err, data) =>
{
  if (err)
  {
    config.logger && config.logger('Alexa-API: Error while loading the file: ' + config.cookieLocation);
    config.logger && config.logger('Alexa-API: ' + err);
    process.exit(-1);
  }

  config.cookie = JSON.parse(data);
  startServer();
});

function startServer()
{
  alexa.init(
  {
    cookie: config.cookie,
    logger: config.logger,
    alexaServiceHost: config.alexaServiceHost,
    cookieRefreshInterval: config.cookieRefreshInterval
  },
  (err) =>
  {
    // Unable to init alexa
    if (err)
    {
      config.logger && config.logger('Alexa-API: Error while initializing alexa');
      config.logger && config.logger('Alexa-API: ' + err);
      process.exit(-1);
    }

    if (alexa.cookieData)
    {
      fs.writeFile(config.cookieLocation, JSON.stringify(alexa.cookieData), 'utf8', (err) =>
      {
        if (err)
        {
          config.logger && config.logger('Alexa-API - Error while saving the cookie to: ' + config.cookieLocation);
          config.logger && config.logger('Alexa-API - ' + err);
        }
        config.logger && config.logger('Alexa-API - New cookie saved to:' + config.cookieLocation);

        // Start the server
        if (server)
        {
          config.logger && config.logger('Alexa-API: *******************************************');
          config.logger && config.logger('Alexa-API: *Server is already listening on port ' + server.address().port +' *');
          config.logger && config.logger('Alexa-API: *******************************************');
          return;
        }

        server = app.listen(config.listeningPort, () =>
        {
			config.logger && config.logger('Alexa-API: *********************************');
			config.logger && config.logger('Alexa-API: * Server listening on port ' + server.address().port+' *');
			config.logger && config.logger('Alexa-API: *********************************');
        });
      });
    }
  });
}

function error(status, source, title, detail)
{
  let error =
  {
    'status': status,
    'title': title,
    'detail': detail
  };

  config.logger && config.logger('Alexa-API: ' + title + ': ' + detail);
  return error;
}