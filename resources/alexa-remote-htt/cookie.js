let Alexa = require('./alexa-remote');
let alexa = new Alexa();

let cookie = {/* private */};
var fs = require('fs');

//console.log ('---------------------------------------------------------------');
//console.log (process.argv[2]);
//console.log ('---------------------------------------------------------------');

alexa.init({
        // Initialement, nous n'avons pas de token, il passera alors par le user/mot de passse.
        // En fin de script, nous avons un object JSON dans la console. Ca sera notre cookie.
        //cookie: cookie,  // cookie if already known, else can be generated using email/password
        email: '',    // optional, amazon email for login to get new cookie
        password: '', // optional, amazon password for login to get new cookieprocess.argv[2]
        proxyOnly: true,
        proxyOwnIp: process.argv[2] ,
        proxyPort: 3457,
        proxyLogLevel: 'info',
        bluetooth: true,
        logger: console.log, // optional
        alexaServiceHost: 'alexa.amazon.fr', // optional, e.g. "pitangui.amazon.com" for amazon.com, default is "layla.amazon.de"
//        userAgent: '...', // optional, override used user-Agent for all Requests and Cookie determination
//        acceptLanguage: '...', // optional, override Accept-Language-Header for cookie determination
//        amazonPage: '...', // optional, override Amazon-Login-Page for cookie determination and referer for requests
        useWsMqtt: false, // optional, true to use the Websocket/MQTT direct push connection
        cookieRefreshInterval: 7*24*60*1000 // optional, cookie refresh intervall, set to 0 to disable refresh
    },
    function (err)
    {
        if (err) {
            console.log (err);
            return;
        }
console.log ('-------------xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-----------------');

        fs.writeFile('/tmp/alexa-cookie.json', JSON.stringify(alexa.cookieData), 'utf8', function(err) {stop();});

console.log ('---------------------------------------------------------------');
console.log ('--Cookie bien recuperé, il a été copié dans /tmp/alexa-cookie.json--');
      console.log(JSON.stringify(alexa.cookie));
        //console.log(JSON.stringify(alexa.csrf));
        //console.log(JSON.stringify(alexa.cookieData));
        //for (let deviceSerial of Object.keys(alexa.serialNumbers)) {
        // console.log (deviceSerial);
console.log ('---------------------------------------------------------------');

        //}
    }
);


function stop()
{
  process.exit();
}


