const express = require('express');
const fs = require('fs');
const Alexa = require('./lib/alexa-remote.js');
let alexa = new Alexa();

/* Configuration */
const config =
{
  cookieLocation: __dirname + '/data/alexa-cookie.json',
  cookieRefreshInterval: 7*24*60*1000,
  logger: console.log,
  alexaServiceHost: 'alexa.amazon.fr',
  listeningPort: 3456
};

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
  });

  if (err.length != 0)
    return res.status(500).json(error(500, req.route, 'Alexa.DeviceControls.Volume', err));
  res.status(200).json({});
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

function printObject(o) {
  var out = '';
  for (var p in o) {
    out += p + ': ' + o[p] + '\n';
  }
  //alert(out);
  config.logger && config.logger('Alexa-API: (reminders) boucle = '+ out );
}



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
        'reminderLabel': device.reminderLabel
      });
    }
    res.status(200).json(toReturn);

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
          config.logger && config.logger('Alexa-API: Server is already listening on port ' + server.address().port);
          return;
        }

        server = app.listen(config.listeningPort, () =>
        {
          config.logger && config.logger('Alexa-API: Server listening on port ' + server.address().port);
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