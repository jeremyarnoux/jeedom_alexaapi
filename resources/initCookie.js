let Alexa = require('./lib/alexa-remote');
let alexa = new Alexa();

let cookieLocation = __dirname + '/data/alexa-cookie.json';
var fs = require('fs');

const amazonserver = process.argv[3];
const alexaserver = process.argv[4];


alexa.init({
  proxyOnly: true,
  proxyOwnIp: process.argv[2],
  proxyPort: 3457,
  proxyLogLevel: 'info',
  logger: console.log,
  alexaServiceHost: alexaserver
},
function (err)
{
  if (err)
  {
    console.log('initCookie - ' + err);
    return; // Wait next call
  }
  
 console.log('Alexa-Config (initCookie.js): amazonserver=' + amazonserver );
 console.log('Alexa-Config (initCookie.js): alexaserver=' + alexaserver );

  if (!alexa.cookieData)
    return; // Wait next call

 
 console.log ('initCookie - Cookie successfully retrieved from Amazon');
  fs.writeFile(cookieLocation, JSON.stringify(alexa.cookieData), 'utf8', (err) =>
  {
    if (err)
    {
      console.log('initCookie - Error while saving the cookie to: ' + cookieLocation);
      console.log('initCookie - ' + err);
      process.exit();
    }

    console.log ('initCookie - Cookie saved to:' + cookieLocation);
    process.exit();
  });
});

