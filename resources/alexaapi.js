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

/**** Alexa.Speak *****
  URL: /speak?device=?&text=?
    device - String - name of the device
    text - String - Text to speech

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

  alexa.sendSequenceCommand(req.query.device, 'speak', req.query.text, function(err)
  {
    if (err)
      return res.status(500).json(error(500, req.route.path, 'Alexa.Speak', err));
    res.status(200).json({});
  });
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

  alexa.sendSequenceCommand(req.query.device, 'volume', req.query.value, function(err)
  {
    if (err)
      return res.status(500).json(error(500, req.route, 'Alexa.DeviceControls.Volume', err));
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
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Volume', 'Missing parameter "device"'));
  config.logger && config.logger('Alexa-API: device: ' + req.query.device);

  if ('text' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Volume', 'Missing parameter "text"'));
  config.logger && config.logger('Alexa-API: text: ' + req.query.text);

  if ('when' in req.query === false)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Volume', 'Missing parameter "when"'));
  config.logger && config.logger('Alexa-API: when: ' + req.query.when);

  // when: YYYY-MM-DD HH:MI:SS
  let dateValues = req.query.when.match(/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/);
  if (dateValues === null)
    return res.status(500).json(error(500, req.route.path, 'Alexa.DeviceControls.Volume', 'Invalid "when" format. Expected: YYYY-MM-DD HH:MI:SS'));
  let when = new Date(dateValues[1], dateValues[2], dateValues[3], dateValues[4], dateValues[5], dateValues[6])
  config.logger && config.logger('Alexa-API: when: ' + when);

  alexa.setReminder(req.query.device, when.getTime(), req.query.text, function(err)
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
    name: String - name of the device. Use this name to call as "device" parameter of others methods
    type: String - Device family as defined by Amazon. Known type: TABLET (for tablet device), ECHO (for ECHO device), WHA (for group of devices), VOX (for smartphone? Webpage?)
    online: Boolean - true when the device is connected, false otherwise,
    capabilities: [String] - List of available capabilties of the device, few example: VOLUME_SETTING, REMINDERS, MICROPHONE, TUNE_IN, ...
  }]
*/
app.get('/devices', (req, res) =>
{
  config.logger && config.logger('Alexa-API: Devices');
  res.type('json');

  alexa.getDevices(function(err, data)
  {
    if (err)
      return res.status(500).json(error(500, req.route, 'Devices', err));

    var toReturn = [];
    // FIXME: It should be better to use alexa.getDevices and force this method
    // to refresh internal state of alexa-remote like it is done in initDeviceState
    // Here, we qre sync with alexa-remote but alexa-remote is maybe unsync with reality.
    // It require to restart the server to refresh the list of devices.
    for (var name in alexa.names)
    {
      toReturn.push({
        'name': name,
        'type': alexa.names[name].deviceFamily,
        'online': alexa.names[name].online,
        'capabilities' : alexa.names[name].capabilities
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

    // Start the server
    server = app.listen(config.listeningPort, () =>
    {
      config.logger && config.logger('Alexa-API: Server listening on port ' + server.address().port);
    });
  });
}

function error(status, source, title, detail)
{
  let error =
  {
    'status': status,
    'source': {pointer: source},
    'title': title,
    'detail': detail
  };

  config.logger && config.logger('Alexa-API: ' + title + ': ' + detail);
  return error;
}