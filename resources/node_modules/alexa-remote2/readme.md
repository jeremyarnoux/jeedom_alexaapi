
# alexa-remote2

Library to remote control an Alexa (Amazon Echo) device via LAN/WLAN.

Early code version.

<!--
[![NPM version](http://img.shields.io/npm/v/alexa-remote.svg)](https://www.npmjs.com/package/alexa-remote)
[![Tests](http://img.shields.io/travis/soef/alexa-remote/master.svg)](https://travis-ci.org/soef/alexa-remote)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](https://github.com/soef/alexa-remote/blob/master/LICENSE)
-->


```js
let Alexa = require('alexa-remote2');
let alexa = new Alexa();

/***************************************************************/
// see: https://www.gehrig.info/alexa/Alexa.html
// cookie starts with x-amzn-dat and ends with =" csrf=12345780
let cookie = 'x-amzn-dat.../ /...=" csrf=12345780';

alexa.init({
        cookie: cookie,  // cookie if already known, else can be generated using email/password
        email: '...',    // optional, amazon email for login to get new cookie
        password: '...', // optional, amazon password for login to get new cookie
        bluetooth: true,
        logger: console.log, // optional
        alexaServiceHost: 'layla.amazon.de', // optional, e.g. "pitangui.amazon.com" for amazon.com, default is "layla.amazon.de"
        userAgent: '...', // optional, override used user-Agent for all Requests and Cookie determination
        acceptLanguage: '...', // optional, override Accept-Language-Header for cookie determination
        amazonPage: '...', // optional, override Amazon-Login-Page for cookie determination and referer for requests
        useWsMqtt: true // optional, true to use the Websocket/MQTT direct push connection
    },
    function (err) {
        if (err) {
            console.log (err);
            return;
        }
        for (let deviceSerial of Object.keys(alexa.serialNumbers)) {
            console.log (deviceSerial);
        }
    }
);
````

## Thanks:
Partly based on [Amazon Alexa Remote Control](http://blog.loetzimmer.de/2017/10/amazon-alexa-hort-auf-die-shell-echo.html) (PLAIN shell) and [alexa-remote-control](https://github.com/thorsten-gehrig/alexa-remote-control) and [OpenHab-Addon](https://github.com/openhab/openhab2-addons/blob/f54c9b85016758ff6d271b62d255bbe41a027928/addons/binding/org.openhab.binding.amazonechocontrol)
Thank you for that work.

## Known issues/Todos
* getNotification works, changeNotification not ... maybe change is DELETE +Create :-) (+ source for createNotification: https://github.com/noelportugal/alexa-reminders/blob/master/alexa-reminders.js#L75, and Delete/create: https://github.com/openhab/openhab2-addons/blob/f54c9b85016758ff6d271b62d255bbe41a027928/addons/binding/org.openhab.binding.amazonechocontrol/src/main/java/org/openhab/binding/amazonechocontrol/internal/Connection.java#L829)
* docu docu docu (sorry ... will come)

## Changelog:

### 1.0.3 (2018-11-17)
* (Apollon77) upgrade amazon-cookie lib
* (Apollon77) better handle ws errors and upgrade ws version to still support nodejs 6

### 1.0.2 (2018-11-17)
* (Apollon77) upgrade amazon-cookie lib

### 1.0.1 (2018-11-09)
* (Apollon77) upgrade amazon-cookie lib
* (Apollon77) small fix for strange history summary content

### 1.0.0 (2018-09-06)
* (Apollon77) polishing and finalization and make it 1.0.0

### 0.6.1 (2018-08-28)
* (Apollon77) rework scenes and add option  to send Parallel or Sequencial commands
* (Apollon77) enhance methods for smart home device and group handling

### 0.6.0 (2018-08-24)
* (Apollon77) several fixes and optimizations
* (Apollon77) enhance methods for smart home device and group handling

### 0.5.2 (2018-08-16)
* (Apollon77) also allow new reminder on next day :-)

### 0.5.0 (2018-08-16)
* (Apollon77) fix an error when getting new cookie
* (Apollon77) Add Reminder and Alarms support.
* (Apollon77) Enhance Push Connection
* (Apollon77) Added some more deviceTypes

### 0.3.0 (2018-08-13)
* (Apollon77) Added Websocket/MQTT connection class and also initialize it when requested via alexa-remote class.
* (Apollon77) Websocet/MQTT class and also Alexa-Remote are now event emitters to be able to notify on push changes
* (Apollon77) many fixes and optimizations, changed code to be an ES6 class
* (Apollon77) reworked the "prepare" step and only initialize what's really needed and allow extra "init" methods also to update Devices, Bluetooth and such. Docs will follow
* (Apollon77) API breaking: executeAutomationRoutine is not expecting a routineId anymore, but the complete routine definition.

### 0.2.x
* (Apollon77) 0.2.8: fixes, fix shuffle/repeat commands, make sure speak object is a string
* (Apollon77) 0.2.7: speak maximum are 250 characters, routines will now queried 2000 at once (instead of only 20 before)
* (Apollon77) 0.2.6: fixes
* (Apollon77) 0.2.5: new functions to read musicproviders and send searchphrases for them
* (Apollon77) 0.2.5: by default direct all calls to "alexa."+amazonPage to be more generic, overwritable
* (Apollon77) 0.2.4: several smaller bugfixes
* (Apollon77) 0.2.4: an speak call with empty string will return an error
* (Apollon77) 0.2.4: add infos if multiroom device or member
* (Apollon77) 0.2.3: in non .com replace numbers with points into numbers with comma
* (Apollon77) 0.2.0: several optimizations and publish as alexa-remote2 on npm
* (Apollon77) 0.2.0: use alexa-cookie@0.2.0 library to also offer proxy support
* (Apollon77) 0.2.0: retrieve automation routines in prepare
* (Apollon77) 0.2.0: enhanced sendCommand and added support for Routines and sequencial Commands

### 0.1.x
* (Apollon77) 0.1.3: Use specific User-Agents for Win32, MacOS and linux based platforms
* (Apollon77) 0.1.2: add logging for used Alexa-URL and user-Agent once at init
* (Apollon77) 0.1.1: rename "shuffle" to "ShuffleCommand" and repeat to RepeatCommand)

### 0.1.0
* (Apollon77) added automatic cookie renewal when email and password are provided
* (Apollon77) added authentication checks by bootstrap call (like [alexa-remote-control](https://github.com/thorsten-gehrig/alexa-remote-control))
* (Apollon77) several fixes
* (Apollon77) added logger option

### 0.0.x
* Versions by soef
