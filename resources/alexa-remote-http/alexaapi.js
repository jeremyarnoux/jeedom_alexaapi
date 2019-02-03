const express = require('express');
const fs = require('fs');
const Alexa = require('./alexa-remote.js');
let alexa = new Alexa();

/* Configuration */
const config =
{
  cookieLocation: '/tmp/alexa-cookie.json',
  cookieRefreshInterval: 7*24*60*1000,
  logger: console.log,
  alexaServiceHost: 'alexa.amazon.fr',
  listeningPort: 3456
};

/* Routing */
const app = express();
let server = null;

/**** Alexa.Speak *****/
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

/***** Alexa.DeviceControls.Volume *****/
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

/***** Alexa.Notifications.SendMobilePush *****/
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

/***** create a reminder *****/
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
    if (err)
    {
      config.logger && config.logger('Alexa-API: Error while initializing alexa');
      config.logger && config.logger('Alexa-API: ' + err);
      process.exit(-1);
    }

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