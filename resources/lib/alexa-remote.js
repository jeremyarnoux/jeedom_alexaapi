/* jshint -W097 */
/* jshint -W030 */
/* jshint strict: false */
/* jslint node: true */
/* jslint esversion: 6 */
'use strict';

// Source : https://github.com/Apollon77/alexa-remote/blob/master/alexa-remote.js

const https = require('https');
const querystring = require('querystring');
const os = require('os');
const extend = require('extend');

const EventEmitter = require('events');

const amazonserver = process.argv[3];
const alexaserver = process.argv[4];

function _00(val) {
    let s = val.toString();
    while (s.length < 2) s = '0' + s;
    return s;
}


class AlexaRemote extends EventEmitter {

    constructor() {
        super();

        this.serialNumbers = {};
        this.names = {};
        this.friendlyNames = {};
        this.lastAuthCheck = null;
        this.cookie = null;
        this.csrf = null;
        this.cookieData = null;

        this.baseUrl = alexaserver; //alexa.amazon.fr
   }

    setCookie(_cookie)
    {
        if (!_cookie)
          return;

        if (typeof _cookie === 'string')
        {
            this.cookie = _cookie;
        }
        else if(_cookie && _cookie.cookie && typeof _cookie.cookie === 'string')
        {
            this.cookie = _cookie.cookie;
        }
        else if(_cookie && _cookie.localCookie && typeof _cookie.localCookie === 'string')
        {
            this.cookie = _cookie.localCookie;
            this._options.formerRegistrationData = this.cookieData = _cookie;
        }
        else if(_cookie && _cookie.cookie && typeof _cookie.cookie === 'object')
        {
            return this.setCookie(_cookie.cookie);
        }

        let ar = this.cookie.match(/csrf=([^;]+)/);
        if (!ar || ar.length < 2)
          ar = this.cookie.match(/csrf=([^;]+)/);

        if (!this.csrf && ar && ar.length >= 2)
            this.csrf = ar[1];

        this._options.csrf = this.csrf;
        this._options.cookie = this.cookie;
    }

    init(cookie, callback)
    {
        if (typeof cookie === 'object')
        {
            this._options = cookie;
            if (!this._options.userAgent)
            {
                let platform = os.platform();
                if (platform === 'win32') 
                    this._options.userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:99.0) Gecko/20100101 Firefox/99.0';
                else 
                    this._options.userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36';
            }
        //this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): this._options.amazonPage=' + this._options.amazonPage);
            this._options.amazonPage = this._options.amazonPage || amazonserver;
            //this.baseUrl = 'alexa.' + this._options.amazonPage;
            this.baseUrl = alexaserver;

            cookie = this._options.cookie;
        }
//        this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): amazonserver=' + amazonserver);
//        this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): alexaserver=' + alexaserver);
//        this._options.logger && this._options.logger('Alexa-Remote: Use as User-Agent: ' + this._options.userAgent);
//        this._options.logger && this._options.logger('Alexa-Remote: Use as Login-Amazon-URL: ' + this._options.amazonPage);
        if (this._options.alexaServiceHost)
            this.baseUrl = this._options.alexaServiceHost;
//        this._options.logger && this._options.logger('Alexa-Remote: Use as Base-URL: ' + this.baseUrl);
        this._options.alexaServiceHost = this.baseUrl;
        if (this._options.refreshCookieInterval !== 0)
            this._options.refreshCookieInterval = this._options.refreshCookieInterval || 7*24*60*1000; // Auto Refresh after 7 days

        const self = this;
        function getCookie(callback)
        {
            if (!self.cookie)
            {
                self._options.logger && self._options.logger('Alexa-Remote: No cookie given, generate one');
                self._options.cookieJustCreated = true;
                self.generateCookie(self._options.email, self._options.password, function(err, res)
                {
                    if (!err && res)
                    {
                        self.setCookie(res); // update
                        self.alexaCookie.stopProxyServer();
                        return callback (null);
                    }
                    callback(err);
                });
            }
            else
            {
                self._options.logger && self._options.logger('Alexa-Remote: cookie was provided');
                if (self._options.formerRegistrationData)
                {
                    const tokensValidSince = Date.now() - self._options.formerRegistrationData.tokenDate;
                    if (tokensValidSince < 24 * 60 * 60 * 1000)
                        return callback(null);

                    self._options.logger && self._options.logger('Alexa-Remote: former registration data exist, try refresh');
                    self.refreshCookie(function(err, res)
                    {
                        if (err || !res)
                        {
                            self._options.logger && self._options.logger('Alexa-Remote: Error from refreshing cookies');
                            self.cookie = null;
                            return getCookie(callback); // error on refresh
                        }

                        self.setCookie(res); // update
                        return callback(null);
                    });
                }
                else
                {
                    callback(null);
                }
            }
        }

        this.setCookie(cookie); // set initial cookie
        getCookie((err) =>
        {
            if (typeof callback === 'function')
                callback = callback.bind(this);

            if (err)
            {
                this._options.logger && this._options.logger('Alexa-Remote: Error from retrieving cookies');
                return callback && callback(err);
            }

            if (!this.csrf)
                return callback && callback(new Error('no csrf found'));

            this.checkAuthentication((authenticated) =>
            {
                this._options.logger && this._options.logger('Alexa-Remote: Authentication checked: ' + authenticated);
                if (! authenticated && !this._options.cookieJustCreated)
                {
                    this._options.logger && this._options.logger('Alexa-Remote: Cookie was set, but authentication invalid');
                    delete this._options.cookie;
                    delete this._options.csrf;
                    return this.init(this._options, callback);
                }

                this.lastAuthCheck = new Date().getTime();
                if (this.cookieRefreshTimeout)
                {
                    clearTimeout(this.cookieRefreshTimeout);
                    this.cookieRefreshTimeout = null;
                }

                if (this._options.cookieRefreshInterval)
                {
                    this.cookieRefreshTimeout = setTimeout(() =>
                    {
                        this.cookieRefreshTimeout = null;
                        this._options.cookie = this.cookieData;
                        delete this._options.csrf;
                        this.init(this._options, callback);
                    }, this._options.cookieRefreshInterval);
                }
                this.prepare(() => {
                    callback && callback();
                });
            });
        });
    }

    prepare(callback) {
        this.getAccount((err, result) => {
			//this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: getAccount');
            if (!err && result && Array.isArray(result)) {
                result.forEach ((account) => {
                    if (!this.commsId) this.commsId = account.commsId;
					//this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: getAccount:'+account.commsId);
                    //if (!this.directedId) this.directedId = account.directedId;
                });
            }

            this.initDeviceState(() =>
                this.initWakewords(() =>
                    this.initBluetoothState(() =>
                        this.initNotifications(callback)
                    )
                )
            );
            
        });
        return this;
    }

    initNotifications(callback) {
		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: initNotifications');

        //if (!this._options.notifications) return callback && callback();
        this.getNotifications((err, res) => {
		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: 0');

            if (err || !res || !res.notifications || !Array.isArray(res.notifications)) return callback && callback();

            for (var serialNumber in this.serialNumbers) {
		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: boucle serialNumbers');
                if (this.serialNumbers.hasOwnProperty(serialNumber)) {
                    this.serialNumbers[serialNumber].notifications = [];
                }
            }
		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: 1');

            res.notifications.forEach((noti) => {
                let device = this.find(noti.deviceSerialNumber);
                if (!device) {
                    //TODO: new stuff
 		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: 2');
                   return;
                }
                if (noti.alarmTime && !noti.originalTime && noti.originalDate && noti.type !== 'Timer') {
                    const now = new Date(noti.alarmTime);
 		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: 3');
                    noti.originalTime = `${_00(now.getHours())}:${_00(now.getMinutes())}:${_00(now.getSeconds())}.000`;
                }
  		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: 4');
               noti.set = this.changeNotification.bind(this, noti);
                device.notifications.push(noti);
            });
  		                        //this._options.logger && this._options.logger('Alexa-xxxxxxxxxxxxx: 5');
            callback && callback();
        });
    }

    initWakewords(callback) {
        this.getWakeWords((err, wakeWords) => {
            if (err || !wakeWords || !Array.isArray(wakeWords.wakeWords)) return callback && callback();

            wakeWords.wakeWords.forEach((o) => {
                let device = this.find(o.deviceSerialNumber);
                if (!device) {
                    //TODO: new stuff
                    return;
                }
                if (typeof o.wakeWord === 'string') {
                    device.wakeWord = o.wakeWord.toLowerCase();
                }
            });
            callback && callback();
        });
    }

    initDeviceState(callback)
    {
      this.refreshDeviceState(callback);
    }

    refreshDeviceState(callback)
    {
        this.httpsGet('/api/devices-v2/device?cached=true&_=%t', ((err, result) =>
        {
          if (err || !result || !Array.isArray(result.devices))
          {
            callback && callback();
            return;
          }

          // Clean device states
          this.serialNumbers = {};
          this.names = {};
          this.friendlyNames = {};

          let customerIds = {};
          result.devices.forEach((device) =>
          {
            // Add devices to mapping array
            this.serialNumbers[device.serialNumber] = device;
            let name = device.accountName;
            this.names[name] = device;
            this.names[name.toLowerCase()] = device;

            if (device.deviceTypeFriendlyName)
            {
              name += ' (' + device.deviceTypeFriendlyName + ')';
              this.names[name] = device;
              this.names[name.toLowerCase()] = device;
            }

            if (device.deviceTypeFriendlyName)
              this.friendlyNames[device.deviceTypeFriendlyName] = device;

            // Configure device properties
            device._orig = JSON.parse(JSON.stringify(device));
            device._name = name;

            if (customerIds[device.deviceOwnerCustomerId] === undefined)
              customerIds[device.deviceOwnerCustomerId] = 0;
            customerIds[device.deviceOwnerCustomerId] += 1;
            
            device.isControllable =
            (
                device.capabilities.includes('AUDIO_PLAYER') ||
                device.capabilities.includes('AMAZON_MUSIC') ||
                device.capabilities.includes('TUNE_IN')
            );

            device.hasMusicPlayer =
            (
                device.capabilities.includes('AUDIO_PLAYER') ||
                device.capabilities.includes('AMAZON_MUSIC')
            );

            device.isMultiroomDevice = (device.clusterMembers.length > 0);
            device.isMultiroomMember = (device.parentClusters.length > 0);

            // Bind functions
            device.sendCommand = this.sendCommand.bind(this, device);
            device.setTunein = this.setTunein.bind(this, device);
            device.rename = this.renameDevice.bind(this, device);
            device.setDoNotDisturb = this.setDoNotDisturb.bind(this, device);
            device.delete = this.deleteDevice.bind(this, device);
          });
          this.ownerCustomerId = Object.keys(customerIds)[0];

          callback && callback();
        }));
    }

    initBluetoothState(callback) {
        if (this._options.bluetooth) {
            this.getBluetooth((err, res) => {
                if (err || !res || !Array.isArray(res.bluetoothStates)) {
                    this._options.bluetooth = false;
                    return callback && callback ();
                }
                const self = this;
                res.bluetoothStates.forEach((bt) => {
                    if (bt.pairedDeviceList && this.serialNumbers[bt.deviceSerialNumber]) {
                        this.serialNumbers[bt.deviceSerialNumber].bluetoothState = bt;
                        bt.pairedDeviceList.forEach((d) => {
                            bt[d.address] = d;
                            d.connect = function (on, cb) {
                                self[on ? 'connectBluetooth' : 'disconnectBluetooth'] (self.serialNumbers[bt.deviceSerialNumber], d.address, cb);
                            };
                            d.unpaire = function (val, cb) {
                                self.unpaireBluetooth (self.serialNumbers[bt.deviceSerialNumber], d.address, cb);
                            };
                        });
                    }
                });
                callback && callback();
            });
        } else {
            callback && callback();
        }
    }

    stop() {
        if (this.cookieRefreshTimeout) {
            clearTimeout(this.cookieRefreshTimeout);
            this.cookieRefreshTimeout = null;
        }
    }

    generateCookie(email, password, callback) {
        if (!this.alexaCookie) this.alexaCookie = require('./alexa-cookie.js');
        this.alexaCookie.generateAlexaCookie(email, password, this._options, callback);
    }

    refreshCookie(callback) {
        if (!this.alexaCookie) this.alexaCookie = require('./alexa-cookie.js');
        this.alexaCookie.refreshAlexaCookie(this._options, callback);
    }

    httpsGet(noCheck, path, callback, flags = {}) {
        if (typeof noCheck !== 'boolean') {
            flags = callback;
            callback = path;
            path = noCheck;
            noCheck = false;
        }
        // bypass check because set or last check done before less then 10 mins
        if (noCheck || (new Date().getTime() - this.lastAuthCheck) < 600000) {
            this._options.logger && this._options.logger('Alexa-Remote: No authentication check needed (time elapsed ' + (new Date().getTime() - this.lastAuthCheck) + ')');
            return this.httpsGetCall(path, callback, flags);
        }
        this.checkAuthentication((authenticated) => {
            if (authenticated) {
                this._options.logger && this._options.logger('Alexa-Remote: Authentication check successfull');
                this.lastAuthCheck = new Date().getTime();
                return this.httpsGetCall(path, callback, flags);
            }
            if (this._options.email && this.options.password) {
                this._options.logger && this._options.logger('Alexa-Remote: Authentication check Error, but email and password, get new cookie');
                delete this._options.csrf;
                delete this._options.cookie;
                this.init(this._options, function(err) {
                    if (err) {
                        this._options.logger && this._options.logger('Alexa-Remote: Authentication check Error and renew unsuccessfull. STOP');
                        return callback(new Error('Cookie invalid, Renew unsuccessfull'));
                    }
                    return this.httpsGet(path, callback, flags);
                });
            }
            this._options.logger && this._options.logger('Alexa-Remote: Authentication check Error and no email and password. STOP');
            callback(new Error('Cookie invalid'));
        });
    }

    httpsGetCall(path, callback, flags = {}) {
        let options = {
            host: this.baseUrl,
            path: '',
            method: 'GET',
            timeout: 10000,
            headers: {
                'User-Agent' : this._options.userAgent,
                'Content-Type': 'application/json; charset=UTF-8',
                'Referer': `https://${this.baseUrl}/spa/index.html`,
                'Origin': `https://${this.baseUrl}`,
                'Content-Type': 'application/json',
                //'Connection': 'keep-alive', // new
                'csrf' : this.csrf,
                'Cookie' : this.cookie
            }
        };

        path = path.replace(/[\n ]/g, '');
        if (!path.startsWith('/')) {
            path = path.replace(/^https:\/\//, '');
            let ar = path.match(/^([^\/]+)([\/]*.*$)/);
            options.host = ar[1];
            path = ar[2];
        } else {
            options.host = this.baseUrl;
        }
        let time = new Date().getTime();
        path = path.replace(/%t/g, time);

        options.path = path;
        options.method = flags.method? flags.method : flags.data ? 'POST' : 'GET';

        if (flags.headers) Object.keys(flags.headers).forEach(n => {
            options.headers [n] = flags.headers[n];
        });

        const logOptions = JSON.parse(JSON.stringify(options));
        delete logOptions.headers.Cookie;
        delete logOptions.headers.csrf;
        delete logOptions.headers['User-Agent'];
        delete logOptions.headers['Content-Type'];
        delete logOptions.headers.Referer;
        delete logOptions.headers.Origin;
        this._options.logger && this._options.logger('Alexa-Remote: Sending Request with ' + JSON.stringify(logOptions) + ((options.method === 'POST' || options.method === 'PUT') ? 'and data=' + flags.data : ''));
		let req = https.request(options, (res) => {
        //console.log(res+"FIN de RES");
            let body  = '';
        //this._options.logger && this._options.logger('DEBUG1');
			
//SIMULER UN BUG !!!
//if (flags.data !=undefined) body="Connection: close";


            res.on('data', (chunk) => {
                body += chunk;
            });
        //this._options.logger && this._options.logger('DEBUG2');

            res.on('end', () =>
            {
        //this._options.logger && this._options.logger('DEBUG3');
                let ret;
                if (typeof callback === 'function')
                {
                    if (!body)
                    {
                        this._options.logger && this._options.logger('Alexa-Remote: Response: No body = OK');
                        return callback && callback(null, null);
                    }
                    try
                    {
                        ret = JSON.parse(body);
                    } catch(e)
                    {
                        this._options.logger && this._options.logger('******************************************************');
                        this._options.logger && this._options.logger('*********************DEBUG****************************');
                        this._options.logger && this._options.logger('******************************************************');
                        this._options.logger && this._options.logger('**DEBUG**DEBUG*Alexa-Remote: Response: No/Invalid JSON');
                        this._options.logger && this._options.logger(body);
                        this._options.logger && this._options.logger('**DEBUG**DEBUG* Message Exception :'+e.message);
                        this._options.logger && this._options.logger('******************************************************');
                        this._options.logger && this._options.logger('******************************************************');
						var ValeurdelErreur='no JSON';
						//if (body.includes("authenticated"))
						if (body.includes("Connection: close"))
                        {
							this._options.logger && this._options.logger('******************************************************');
							this._options.logger && this._options.logger('***************FIND**CONNEXION CLOSE *****************');
							this._options.logger && this._options.logger('******************************************************');
						ValeurdelErreur='Connexion Close';
						}
                       return callback && callback(new Error(ValeurdelErreur), body);
                    }
                    this._options.logger && this._options.logger('Alexa-Remote: Response: ' + JSON.stringify(ret));
                    return callback && callback (null, ret);
                }
            });
        });

        req.on('error', function(e)
        {
            if(typeof callback === 'function')
                return callback (e, null);
        });

        if (flags && flags.data)
            req.write(flags.data);
        req.end();
    }


/// Public
    checkAuthentication(callback) {
        this.httpsGetCall ('/api/bootstrap?version=0', function (err, res) {
            if (res && res.authentication && res.authentication.authenticated !== undefined) {
                return callback(res.authentication.authenticated);
            }
            return callback(false);
        });
    }

    getDevices(callback)
    {
      var isReady = false;
      this.refreshDeviceState(() =>
      {
        callback && callback(this.serialNumbers);
      });
    }

    getCards(limit, beforeCreationTime, callback) {
        if (typeof limit === 'function') {
            callback = limit;
            limit = 10;
        }
        if (typeof beforeCreationTime === 'function') {
            callback = beforeCreationTime;
            beforeCreationTime = '%t';
        }
        if (beforeCreationTime === undefined) beforeCreationTime = '%t';
        this.httpsGet (`/api/cards?limit=${limit}&beforeCreationTime=${beforeCreationTime}000&_=%t`, callback);
    }

    getMedia(serialOrName, callback) {
 		        //this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): getMedia1 avant boucle '+serialOrName);
       let dev = this.find(serialOrName);
 		        //this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): getMedia1 avant boucle '+dev);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));
		
		        //this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): getMedia1 dans boucle');

        this.httpsGet (`/api/media/state?deviceSerialNumber=${dev.serialNumber}&deviceType=${dev.deviceType}&screenWidth=1392&_=%t`, callback);
    }


	getMedia2(serialOrName,callback) 
	{
		        //this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): getMedia2');

		this.getMedia(serialOrName,(err, res) => 
		{
		        //this._options.logger && this._options.logger('Alexa-Config (alexa-remote.js): getMedia2 dans boucle');
			//console.log(res);	
			//if (err || !res || !res.volume || !Array.isArray(res.volume)) return callback && callback();
			if (err || !res ) return callback && callback();
			callback && callback(res);
		});
	}





    getPlayerInfo(serialOrName, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        this.httpsGet (`/api/np/player?deviceSerialNumber=${dev.serialNumber}&deviceType=${dev.deviceType}&screenWidth=1392&_=%t`, callback);
    }

    getList(serialOrName, listType, options, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        if (typeof options === 'function') {
            callback = options;
            options = {};
        }
        this.httpsGet (`
            /api/todos?size=${options.size || 100}
            &startTime=${options.startTime || ''}
            &endTime=${options.endTime || ''}
            &completed=${options.completed || false}
            &type=${listType}
            &deviceSerialNumber=${dev.serialNumber}
            &deviceType=${dev.deviceType}
            &_=%t`,
            callback);
    }

    getLists(serialOrName, options, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        this.getList(dev, 'TASK', options, function(err, res) {
            let ret = {};
            if (!err && res) {
                ret.tasks = res;
            }
            this.getList(dev, 'SHOPPING_ITEM', options, function(err, res) {
                ret.shoppingItems = res;
                callback && callback(null, ret);
            });
        });
    }


    getReminders(cached, callback) {
        return this.getNotifications(cached, callback);
    }
    getNotifications(cached, callback) {

        if (typeof cached === 'function') {
            callback = cached;
            cached = true;
        }
        if (cached === undefined) cached = true;
        this.httpsGet (`/api/notifications?cached=${cached}&_=%t`, callback);
    }

	getNotifications2(callback) 
	{

		this.getNotifications((err, res) => 
		{
			if (err || !res || !res.notifications || !Array.isArray(res.notifications)) return callback && callback();
			callback && callback(res.notifications);
		});
	}
	
    getWakeWords(callback) {
        this.httpsGet (`/api/wake-word?_=%t`, callback);
    }

	getWakeWords2(callback) 
	{
		this.getWakeWords((err, res) => 
		{
			if (err || !res || !res.wakeWords || !Array.isArray(res.wakeWords)) return callback && callback();
			callback && callback(res.wakeWords);
		});
	}

    createNotificationObject(serialOrName, type, label, value, status, sound) { // type = Reminder, Alarm
        if (status && typeof status === 'object') {
            sound = status;
            status = 'ON';
        }
        if (value === null || value === undefined) {
            value = new Date().getTime() + 5000;
        }

        let dev = this.find(serialOrName);
        if (!dev) return null;

        //const now = new Date(); **Modif Sigalou 2019.02.28
        const notification = {
            'alarmTime': value.getTime(), // will be overwritten **Modif Sigalou 2019.02.28
            'createdDate': value.getTime(),// **Modif Sigalou 2019.02.28
            'type': type, // Alarm ...
            'deviceSerialNumber': dev.serialNumber,
            'deviceType': dev.deviceType,
            'reminderLabel': label || null,
            'sound': sound || null,
            /*{
                'displayName': 'Countertop',
                'folder': null,
                'id': 'system_alerts_repetitive_04',
                'providerId': 'ECHO',
                'sampleUrl': 'https://s3.amazonaws.com/deeappservice.prod.notificationtones/system_alerts_repetitive_04.mp3'
            }*/
            'originalDate': `${value.getFullYear()}-${_00(value.getMonth() + 1)}-${_00(value.getDate())}`,// **Modif Sigalou 2019.02.28
            'originalTime': `${_00(value.getHours())}:${_00(value.getMinutes())}:${_00(value.getSeconds())}.000`,// **Modif Sigalou 2019.02.28
            'id': 'create' + type,

            'isRecurring' : false,
            'recurringPattern': null,

            'timeZoneId': null,
            'reminderIndex': null,

            'isSaveInFlight': true,

            'status': 'ON' // OFF
        };
        /*if (type === 'Timer') {
            notification.originalDate = null;
            notification.originalTime = null;
            notification.alarmTime = 0;
        }*/
        return this.parseValue4Notification(notification, value);
    }

    createNotificationAlarmObject(serialOrName, recurring, label, value, status, sound) { // type = Reminder, Alarm
                 //console.log('-->-->--recurring: ' + recurring + '-----------------');
      const type="Alarm";

		if (status && typeof status === 'object') {
            sound = status;
            status = 'ON';
        }
        if (value === null || value === undefined) {
            value = new Date().getTime() + 5000;
        }

		var recurringtruefalse='true';
        if (recurring === null || recurring === undefined || recurring === "") {
            recurringtruefalse='false';
        }

        let dev = this.find(serialOrName);
        if (!dev) return null;
		const now = new Date();
		const notification = {
            'alarmTime': now.getTime(), // will be overwritten
            'createdDate': now.getTime(),
            'type': type, // Alarm ...
            'deviceSerialNumber': dev.serialNumber,
            'deviceType': dev.deviceType,
            'reminderLabel': label || null,
            'sound': sound || null,
            /*{
                'displayName': 'Countertop',
                'folder': null,
                'id': 'system_alerts_repetitive_04',
                'providerId': 'ECHO',
                'sampleUrl': 'https://s3.amazonaws.com/deeappservice.prod.notificationtones/system_alerts_repetitive_04.mp3'
            }*/
            'originalDate': `${now.getFullYear()}-${_00(now.getMonth() + 1)}-${_00(now.getDate())}`,
            'originalTime': `${_00(now.getHours())}:${_00(now.getMinutes())}:${_00(now.getSeconds())}.000`,
            'id': 'create' + type,

            'isRecurring' : recurringtruefalse,
            'recurringPattern': recurring,

            'timeZoneId': null,
            'reminderIndex': null,

            'isSaveInFlight': true,

            'status': 'ON' // OFF
        };
        /*if (type === 'Timer') {
            notification.originalDate = null;
            notification.originalTime = null;
            notification.alarmTime = 0;
        }*/
return this.parseValue4Notification(notification, value);    }

    parseValue4Notification(notification, value) {
      
                //console.log('--------Typeof: ' + (typeof value) + 'Value: ' + value + '-----------------');
        switch (typeof value) {
            case 'object':
                if (value instanceof Date)
                {
                  if (notification.type !== 'Timer')
                  {
                    notification.alarmTime = value.getTime();
                    notification.originalTime = `${_00 (value.getHours ())}:${_00 (value.getMinutes ())}:${_00 (value.getSeconds ())}.000`;
                  }
                }
                notification = extend(notification, value); // we combine the objects
                
                break;
            case 'number':
                if (notification.type !== 'Timer') {
                    value = new Date(value);
                    notification.alarmTime = value.getTime();
                    notification.originalTime = `${_00 (value.getHours ())}:${_00 (value.getMinutes ())}:${_00 (value.getSeconds ())}.000`;
                }
                /*else {
                    //notification.remainingTime = value;
                }*/
                break;
            case 'boolean':
                notification.status = value ? 'ON' : 'OFF';
                break;
            case 'string':
                let ar = value.split(':');
                if (notification.type !== 'Timer') {
                    let date = new Date(notification.alarmTime);
                    date.setHours(parseInt(ar[0], 10), ar.length>1 ? parseInt(ar[1], 10) : 0, ar.length > 2 ? parseInt(ar[2], 10) : 0);
                    notification.alarmTime = date.getTime();
                    notification.originalTime = `${_00(date.getHours())}:${_00(date.getMinutes())}:${_00(date.getSeconds())}.000`;
                }
                /*else {
                    let duration = 0;
                    let multi = 1;
                    for (let i = ar.length -1; i > 0; i--) {
                        duration += ar[i] * multi;
                        multi *= 60;
                    }
                    notification.remainingTime = duration;
                }*/
                break;
        }

        const originalDateTime = notification.originalDate + ' ' + notification.originalTime;
        const bits = originalDateTime.split(/\D/);
        let date = new Date(bits[0], --bits[1], bits[2], bits[3], bits[4], bits[5]);
        if (date.getTime() < Date.now()) {
            date = new Date(date.getTime() + 24 * 60 * 60 * 1000);
            notification.originalDate =  `${date.getFullYear()}-${_00(date.getMonth() + 1)}-${_00(date.getDate())}`;
            notification.originalTime = `${_00(date.getHours())}:${_00(date.getMinutes())}:${_00(date.getSeconds())}.000`;
        }

        return notification;
    }

    createNotification(notification, callback) {
        let flags = {
            data: JSON.stringify(notification),
            method: 'PUT'
        };
        this.httpsGet (`/api/notifications/createReminder`, function(err, res) {
                //  {'Message':null}
                callback && callback(err, res);
            },
            flags
        );
    }

    changeNotification(notification, value, callback) {
        notification = this.parseValue4Notification(notification, value);
        let flags = {
            data: JSON.stringify(notification),
            method: 'PUT'
        };
        this.httpsGet (`/api/notifications/${notification.id}`, function(err, res) {
                //  {'Message':null}
                callback && callback(err, res);
            },
            flags
        );
    }




    deleteNotification(notification, callback) {
        let flags = {
            data: JSON.stringify (notification),
            method: 'DELETE'
        };
        this.httpsGet (`/api/notifications/${notification.id}`, function(err, res) {
                //  {'Message':null}
                callback && callback(err, res);
            },
            flags
        );
    }


test(callback)
	{


        const notification = {
 /*           'alarmTime': '1551798300000', 
            'createdDate': '1551744071809',
            'type': 'Alarm', 
            'deviceSerialNumber': 'G070RQ13812407G6',
            'originalDate': '2019-03-05',
            'originalTime': '15:05:00.000',*/
            'id': 'A3S5BH2HU6VAYF-G090LF118173117U-fc56efd2-d9b7-46ab-800a-52f70c8433d9'
//			'notificationIndex':'03907eaa-bc3b-4afe-be90-62b52ef626bf'
/*            'isRecurring' : false,
            'recurringPattern': null,
            'timeZoneId': null,
            'reminderIndex': null,
            'isSaveInFlight': true,
            'status': 'OFF' // OFF*/
        };

this.deleteNotification(notification, callback);

	}

    getDoNotDisturb(callback) {
        return this.getDeviceStatusList(callback);
    }
    getDeviceStatusList(callback) {
        this.httpsGet (`/api/dnd/device-status-list?_=%t`, callback);
    }

    // alarm volume
    getDeviceNotificationState(serialOrName, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        this.httpsGet (`/api/device-notification-state/${dev.deviceType}/${dev.softwareVersion}/${dev.serialNumber}&_=%t`, callback);
    }

    getBluetooth(cached, callback) {
        if (typeof cached === 'function') {
            callback = cached;
            cached = true;
        }
        if (cached === undefined) cached = true;
        this.httpsGet (`/api/bluetooth?cached=${cached}&_=%t`, callback);
    }

    tuneinSearchRaw(query, callback) {
        this.httpsGet (`/api/tunein/search?query=${query}&mediaOwnerCustomerId=${this.ownerCustomerId}&_=%t`, callback);
    }

    tuneinSearch(query, callback) {
        query = querystring.escape(query);
        this.tuneinSearchRaw(query, callback);
    }

    setTunein(serialOrName, guideId, contentType, callback) {
        if (typeof contentType === 'function') {
            callback = contentType;
            contentType = 'station';
        }
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        this.httpsGet (`/api/tunein/queue-and-play
           ?deviceSerialNumber=${dev.serialNumber}
           &deviceType=${dev.deviceType}
           &guideId=${guideId}
           &contentType=${contentType}
           &callSign=
           &mediaOwnerCustomerId=${dev.deviceOwnerCustomerId}`,
            callback,
            { method: 'POST' });
    }

    getHistory(options, callback) {
        return this.getActivities(options, callback);
    }
	
	getHistory2(options,callback) 
	{
		this.getHistory(options, (err, res) => 
		{
			        //this._options.logger && this._options.logger('coucou'+res);

			if (err || !res || !res || !Array.isArray(res)) return callback && callback();
			callback && callback(res);
		});
	}
	
    getActivities(options, callback) {
        if (typeof options === 'function') {
            callback = options;
            options = {};
        }
        this.httpsGet (`/api/activities` +
            `?startTime=${options.startTime || ''}` +
            `&size=${options.size || 1}` +
            `&offset=${options.offset || 1}`,
            (err, result) => {
                if (err || !result) return callback/*.length >= 2*/ && callback(err, result);

                let ret = [];
                for (let r = 0; r < result.activities.length; r++) {
                    let res = result.activities[r];
                    let o = {
                        data: res
                    };
                    try {
                        o.description = JSON.parse(res.description);
                    }
                    catch (e) {
                        if (res.description) {
                            o.description = {'summary': res.description};
                        }
                        else {
                            return;
                        }
                    }
                    if (!o.description) continue;
                    o.description.summary = (o.description.summary || '').trim ();
                    if (options.filter) {
                        switch (o.description.summary) {
                            case 'stopp':
                            case 'alexa':
                            case 'echo':
                            case 'computer':
                            case 'amazon':
                            case ',':
                            case '':
                                continue;
                        }
                    }
                    for (let i = 0; i < res.sourceDeviceIds.length; i++) {
                        o.deviceSerialNumber = res.sourceDeviceIds[i].serialNumber;
                        if (!this.serialNumbers[o.deviceSerialNumber]) continue;
                        o.name = this.serialNumbers[o.deviceSerialNumber].accountName;
                        const dev = this.find(o.deviceSerialNumber);
                        let wakeWord = (dev && dev.wakeWord) ? dev.wakeWord : null;
                        if (wakeWord && o.description.summary.startsWith(wakeWord)) {
                            o.description.summary = o.description.summary.substr(wakeWord.length).trim();
                        }
                        o.deviceType = res.sourceDeviceIds[i].deviceType || null;
                        o.deviceAccountId = res.sourceDeviceIds[i].deviceAccountId || null;

                        o.creationTimestamp = res.creationTimestamp || null;
                        o.activityStatus = res.activityStatus || null; // DISCARDED_NON_DEVICE_DIRECTED_INTENT, SUCCESS, FAIL, SYSTEM_ABANDONED
                        try {
                            o.domainAttributes = res.domainAttributes ? JSON.parse(res.domainAttributes) : null;
                        }
                        catch(e) {
                            o.domainAttributes = res.domainAttributes || null;
                        }
                        if (o.description.summary || !options.filter) ret.push (o);
                    }
                }
                if (typeof callback === 'function') return callback (err, ret);
            }
        );
    }

    getAccount(callback) {
        this.httpsGet (`https://alexa-comms-mobile-service.amazon.com/accounts`, callback);
    }

    getConversations(options, callback) {
        if (typeof options === 'function') {
            callback = options;
            options = undefined;
        }
        if (options === undefined) options = {};
        if (options.latest === undefined) options.latest = true;
        if (options.includeHomegroup === undefined) options.includeHomegroup = true;
        if (options.unread === undefined) options.unread = false;
        if (options.modifiedSinceDate === undefined) options.modifiedSinceDate = '1970-01-01T00:00:00.000Z';
        if (options.includeUserName === undefined) options.includeUserName = true;

        this.httpsGet (
            `https://alexa-comms-mobile-service.amazon.com/users/${this.commsId}/conversations
            ?latest=${options.latest}
            &includeHomegroup=${options.includeHomegroup}
            &unread=${options.unread}
            &modifiedSinceDate=${options.modifiedSinceDate}
            &includeUserName=${options.includeUserName}`,
            function (err, result) {
                callback (err, result);
            });
    }

    connectBluetooth(serialOrName, btAddress, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let flags = {
            data: JSON.stringify({ bluetoothDeviceAddress: btAddress}),
            method: 'POST'
        };
        this.httpsGet (`/api/bluetooth/pair-sink/${dev.deviceType}/${dev.serialNumber}`, callback, flags);
    }

    disconnectBluetooth(serialOrName, btAddress, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let flags = {
            //data: JSON.stringify({ bluetoothDeviceAddress: btAddress}),
            method: 'POST'
        };
        this.httpsGet (`/api/bluetooth/disconnect-sink/${dev.deviceType}/${dev.serialNumber}`, callback, flags);
    }

    setDoNotDisturb(serialOrName, enabled, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let flags = {
            data: JSON.stringify({
                deviceSerialNumber: dev.serialNumber,
                deviceType: dev.deviceType,
                enabled: enabled
            }),
            method: 'PUT'
        };
        this.httpsGet (`/api/dnd/status`, callback, flags);
    }

    find(serialOrName, callback) {
        if (typeof serialOrName === 'object') return serialOrName;
        if (!serialOrName) return null;
        let dev = this.serialNumbers[serialOrName];
        if (dev !== undefined) return dev;
        dev = this.names[serialOrName];
        if (!dev && typeof serialOrName === 'string') dev = this.names [serialOrName.toLowerCase()];
        if (!dev) dev = this.friendlyNames[serialOrName];
        return dev;
    }

    setAlarmVolume(serialOrName, volume, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let flags = {
            data: JSON.stringify ({
                deviceSerialNumber: dev.serialNumber,
                deviceType: dev.deviceType,
                softwareVersion: dev.softwareVersion,
                volumeLevel: volume
            }),
            method: 'PUT'
        };
        this.httpsGet (`/api/device-notification-state/${dev.deviceType}/${dev.softwareVersion}/${dev.serialNumber}`, callback, flags);
    }

    sendCommand(serialOrName, command, value, callback) {
        return this.sendMessage(serialOrName, command, value, callback);
    }
    sendMessage(serialOrName, command, value, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        const commandObj = { contentFocusClientId: null };
        switch (command) {
            case 'play':
            case 'pause':
            case 'next':
            case 'previous':
            case 'forward':
            case 'rewind':
                commandObj.type = command.substr(0, 1).toUpperCase() + command.substr(1) + 'Command';
                break;
            case 'volume':
                commandObj.type = 'VolumeLevelCommand';
                commandObj.volumeLevel = ~~value;
                if (commandObj.volumeLevel < 0 || commandObj.volumeLevel > 100) {
                    return callback(new Error('Volume needs to be between 0 and 100'));
                }
                break;
            case 'shuffle':
                commandObj.type = 'ShuffleCommand';
                commandObj.shuffle = (value === 'on' || value === true);
                break;
            case 'repeat':
                commandObj.type = 'RepeatCommand';
                commandObj.repeat = (value === 'on' || value === true);
                break;
            default:
                return;
        }

        this.httpsGet (`/api/np/command?deviceSerialNumber=${dev.serialNumber}&deviceType=${dev.deviceType}`,
            callback,
            {
                method: 'POST',
                data: JSON.stringify(commandObj)
            }
        );
    }

    createSequenceNode(command, value, callback) {
        const seqNode = {
            '@type': 'com.amazon.alexa.behaviors.model.OpaquePayloadOperationNode',
            'operationPayload': {
                'deviceType': 'ALEXA_CURRENT_DEVICE_TYPE',
                'deviceSerialNumber': 'ALEXA_CURRENT_DSN',
                'locale': 'ALEXA_CURRENT_LOCALE',
                'customerId':'ALEXA_CUSTOMER_ID'
            }
        };
        switch (command) {
            case 'weather':
                seqNode.type = 'Alexa.Weather.Play';
                break;
            case 'traffic':
                seqNode.type = 'Alexa.Traffic.Play';
                break;
            case 'flashbriefing':
                seqNode.type = 'Alexa.FlashBriefing.Play';
                break;
            case 'goodmorning':
                seqNode.type = 'Alexa.GoodMorning.Play';
                break;
            case 'singasong':
                seqNode.type = 'Alexa.SingASong.Play';
                break;
            case 'tellstory':
                seqNode.type = 'Alexa.TellStory.Play';
                break;
            case 'calendarToday':
                seqNode.type = 'Alexa.Calendar.PlayToday';
                break;
            case 'calendarTomorrow':
                seqNode.type = 'Alexa.Calendar.PlayTomorrow';
                break;
            case 'calendarNext':
                seqNode.type = 'Alexa.Calendar.PlayNext';
                break;
            case 'volume':
                seqNode.type = 'Alexa.DeviceControls.Volume';
                value = ~~value;
                if (value < 0 || value > 100) {
                    return callback(new Error('Volume needs to be between 0 and 100'));
                }
                seqNode.operationPayload.value = value;
                break;
            case 'deviceStop':
                seqNode.type = 'Alexa.DeviceControls.Stop';
                seqNode.operationPayload.devices = [
                    {
                        "deviceSerialNumber": "ALEXA_CURRENT_DSN",
                        "deviceType": "ALEXA_CURRENT_DEVICE_TYPE"
                    }
                ];
                seqNode.operationPayload.isAssociatedDevice = false;
                delete seqNode.operationPayload.deviceType;
                delete seqNode.operationPayload.deviceSerialNumber;
                delete seqNode.operationPayload.locale;
                break;
            case 'speak':
                seqNode.type = 'Alexa.Speak';
                if (typeof value !== 'string') value = String(value);
                if (!this._options.amazonPage || !this._options.amazonPage.endsWith('.com')) {
                    value = value.replace(/([^0-9]?[0-9]+)\.([0-9]+[^0-9])?/g, '$1,$2');
                }
                /*value = value
                    .replace(/Â|À|Å|Ã/g, 'A')
                    .replace(/á|â|à|å|ã/g, 'a')
                    .replace(/Ä/g, 'Ae')
                    .replace(/ä/g, 'ae')
                    .replace(/Ç/g, 'C')
                    .replace(/ç/g, 'c')
                    .replace(/É|Ê|È|Ë/g, 'E')
                    .replace(/é|ê|è|ë/g, 'e')
                    .replace(/Ó|Ô|Ò|Õ|Ø/g, 'O')
                    .replace(/ó|ô|ò|õ/g, 'o')
                    .replace(/Ö/g, 'Oe')
                    .replace(/ö/g, 'oe')
                    .replace(/Š/g, 'S')
                    .replace(/š/g, 's')
                    .replace(/ß/g, 'ss')
                    .replace(/Ú|Û|Ù/g, 'U')
                    .replace(/ú|û|ù/g, 'u')
                    .replace(/Ü/g, 'Ue')
                    .replace(/ü/g, 'ue')
                    .replace(/Ý|Ÿ/g, 'Y')
                    .replace(/ý|ÿ/g, 'y')
                    .replace(/Ž/g, 'Z')
                    .replace(/ž/, 'z')
                    .replace(/&/, 'und')
                    .replace(/[^-a-zA-Z0-9_,.?! ]/g,'')
                    .replace(/ /g,'_');*/
                value = value.replace(/[ ]+/g, ' ');
                if (value.length === 0) {
                    return callback && callback(new Error('Can not speak empty string', null));
                }
                if (value.length > 250) {
                    return callback && callback(new Error('text too long, limit are 250 characters', null));
                }
                seqNode.operationPayload.textToSpeak = value;
                break;
            case 'notification':
                seqNode.type = 'Alexa.Notifications.SendMobilePush';
                if (typeof value !== 'string') value = String(value);
                if (value.length === 0) {
                    return callback && callback(new Error('Can not notify empty string', null));
                }
                seqNode.operationPayload.notificationMessage = value;
                seqNode.operationPayload.alexaUrl = '#v2/behaviors';
                seqNode.operationPayload.title = 'Jeedom';
                delete seqNode.operationPayload.deviceType;
                delete seqNode.operationPayload.deviceSerialNumber;
                delete seqNode.operationPayload.locale;
                break;
            case 'announcement':
            case 'ssml':
                seqNode.type = 'AlexaAnnouncement';
                if (typeof value !== 'string') value = String(value);
                if (command === 'announcement') {
                    if (!this._options.amazonPage || !this._options.amazonPage.endsWith('.com')) {
                        value = value.replace(/([^0-9]?[0-9]+)\.([0-9]+[^0-9])?/g, '$1,$2');
                    }
                    value = value.replace(/[ ]+/g, ' ');
                    if (value.length === 0) {
                        return callback && callback(new Error('Can not speak empty string', null));
                    }
                }
                else if (command === 'ssml') {
                    if (!value.startsWith('<speak>')) {
                        return callback && callback(new Error('Vlue needs to be a valid SSML XML string', null));
                    }
                }
                seqNode.operationPayload.expireAfter = 'PT5S';
                seqNode.operationPayload.content = [
                    {
                        "locale": "fr-FR",
                        "display": {
                            "title": "ioBroker",
                            "body": value
                        },
                        "speak": {
                            "type": (command === 'ssml') ? 'ssml' : 'text',
                            "value": value
                        }
                    }
                ];
                seqNode.operationPayload.target = {
                    "customerId": "ALEXA_CUSTOMER_ID",
                    "devices": [
                        {
                            "deviceSerialNumber": "ALEXA_CURRENT_DSN",
                            "deviceTypeId": "ALEXA_CURRENT_DEVICE_TYPE"
                        }
                    ]
                };
                delete seqNode.operationPayload.deviceType;
                delete seqNode.operationPayload.deviceSerialNumber;
                delete seqNode.operationPayload.locale;
                break;
            default:
                return;
        }
        return seqNode;
    }

    sendMultiSequenceCommand(serialOrName, commands, sequenceType, callback) {
        if (typeof sequenceType === 'function') {
            callback = sequenceType;
            sequenceType = null;
        }
        if (!sequenceType) sequenceType = 'SerialNode'; // or ParallelNode

        let nodes = [];
        for (let command of commands) {
            const commandNode = this.createSequenceNode(command.command, command.value, callback);
            if (commandNode) nodes.push(commandNode);
        }

        const sequenceObj = {
            'sequence': {
                '@type': 'com.amazon.alexa.behaviors.model.Sequence',
                'startNode': {
                    '@type': 'com.amazon.alexa.behaviors.model.' + sequenceType,
                    'name': null,
                    'nodesToExecute': nodes
                }
            }
        };

        this.sendSequenceCommand(serialOrName, sequenceObj, callback);
    }

    sendSequenceCommand(serialOrName, command, value, callback)
    {
       
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: 1 '+command);

	   let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: 2');

        if (typeof value === 'function') {
			//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: function');

            callback = value;
            value = null;
        }

        let seqCommandObj;
        if (typeof command === 'object') {
			//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: object');

            seqCommandObj = command.sequence || command;
        }
        else {
				//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: else');
            seqCommandObj = {
                '@type': 'com.amazon.alexa.behaviors.model.Sequence',
                'startNode': this.createSequenceNode(command, value)
            };
        }
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: 3');
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: deviceType '+dev.deviceType);
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: serialNumber '+dev.serialNumber);
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: deviceOwnerCustomerId '+dev.deviceOwnerCustomerId);
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: seqCommandObj.sequenceId '+seqCommandObj.sequenceId);
//this._options.logger && this._options.logger('Alexa-sendSequenceCommand: command.automationId '+command.automationId);

//this._options.logger && this._options.logger('1'+JSON.stringify(seqCommandObj));
//this._options.logger && this._options.logger('2'+seqCommandObj);

        const reqObj = {
            'behaviorId': seqCommandObj.sequenceId ? command.automationId : 'PREVIEW',
            'sequenceJson': JSON.stringify(seqCommandObj),
            'status': 'ENABLED'
        };
        reqObj.sequenceJson = reqObj.sequenceJson.replace(/"deviceType":"ALEXA_CURRENT_DEVICE_TYPE"/g, `"deviceType":"${dev.deviceType}"`);
        reqObj.sequenceJson = reqObj.sequenceJson.replace(/"deviceTypeId":"ALEXA_CURRENT_DEVICE_TYPE"/g, `"deviceTypeId":"${dev.deviceType}"`);
        reqObj.sequenceJson = reqObj.sequenceJson.replace(/"deviceSerialNumber":"ALEXA_CURRENT_DSN"/g, `"deviceSerialNumber":"${dev.serialNumber}"`);
        reqObj.sequenceJson = reqObj.sequenceJson.replace(/"customerId":"ALEXA_CUSTOMER_ID"/g, `"customerId":"${dev.deviceOwnerCustomerId}"`);
        reqObj.sequenceJson = reqObj.sequenceJson.replace(/"locale":"ALEXA_CURRENT_LOCALE"/g, `"locale":"fr-FR"`);

//this._options.logger && this._options.logger(reqObj.sequenceJson);

        this.httpsGet (`/api/behaviors/preview`,
            callback,
            {
                method: 'POST',
                data: JSON.stringify(reqObj)
            }
        );
    }
	    
		
		
		getRoutines(limit, callback) 
		{
		return this.getAutomationRoutines(callback);
		}
	
	
	
    getAutomationRoutines(limit, callback) { //equivalent de getNotifications
        if (typeof limit === 'function') {
            callback = limit;
            limit = 0;
        }
        limit = limit || 2000;
        this.httpsGet (`/api/behaviors/automations?limit=${limit}`, callback);
    }

getAutomationRoutines2(callback) { //**ajouté SIGALOU 23/03/2019

    this.getAutomationRoutines((err, res) => {
					//this._options.logger && this._options.logger('Alexa-Remote: >>>>>>>>>>>>>>>>>>>>>>>: ' + JSON.stringify(res));
		        callback && callback(res);
    });
}

    executeAutomationRoutine(serialOrName, routine, callback) {
        return this.sendSequenceCommand(serialOrName, routine, callback);
    }

    getMusicProviders(callback) {
        this.httpsGet ('/api/behaviors/entities?skillId=amzn1.ask.1p.music',
            callback,
            {
                headers: {
                    'Routines-Version': '1.1.210292'
                }
            }
        );
    }

    playMusicProvider(serialOrName, providerId, searchPhrase, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));
        if (searchPhrase === '') return callback && callback(new Error ('Searchphrase empty', null));

        const operationPayload = {
            'deviceType': dev.deviceType,
            'deviceSerialNumber': dev.serialNumber,
            'locale': 'fr-FR', // TODO!!
            'customerId': dev.deviceOwnerCustomerId,
            'musicProviderId': providerId,
            'searchPhrase': searchPhrase
        };

        const validateObj = {
            'type': 'Alexa.Music.PlaySearchPhrase',
            'operationPayload': JSON.stringify(operationPayload)
        };

        this.httpsGet (`/api/behaviors/operation/validate`,
            (err, res) => {
                if (err) {
                    return callback && callback(err, res);
                }
                if (res.result !== 'VALID') {
                    return callback && callback(new Error('Request invalid'), res);
                }
                validateObj.operationPayload = res.operationPayload;

                const seqCommandObj = {
                    '@type': 'com.amazon.alexa.behaviors.model.Sequence',
                    'startNode': validateObj
                };
                seqCommandObj.startNode['@type'] = 'com.amazon.alexa.behaviors.model.OpaquePayloadOperationNode';

                return this.sendSequenceCommand(serialOrName, seqCommandObj, callback);
            },
            {
                method: 'POST',
                data: JSON.stringify(validateObj)
            }
        );
    }

    sendTextMessage(conversationId, text, callback) {
        let o = {
            type: 'message/text',
            payload: {
                text: text
            }
        };

        this.httpsGet (`https://alexa-comms-mobile-service.amazon.com/users/${this.commsId}/conversations/${conversationId}/messages`,
            callback,
            {
                method: 'POST',
                data: JSON.stringify (o),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                }
                // Content-Type: application/x-www-form-urlencoded;
                // charset=UTF-8',#\r\n
                // Referer: https://alexa.amazon.de/spa/index.html'
            }
        );
    }

    setList(serialOrName, listType, value, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let o = {
            type: listType,
            text: value,
            createdDate: new Date().getTime(),
            complete: false,
            deleted: false
        };

        this.httpsGet (`/api/todos?deviceSerialNumber=${dev.serialNumber}&deviceType=${dev.deviceType}`,
            callback,
            {
                method: 'POST',
                data: JSON.stringify (o),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                }
                // Content-Type: application/x-www-form-urlencoded;
                // charset=UTF-8',#\r\n
                // Referer: https://alexa.amazon.de/spa/index.html'
            }
        );
    }

    setReminder(serialOrName, timestamp, label, callback) {
        //const notification = this.createNotificationObject(serialOrName, 'Reminder', label, new Date(timestamp)); **Modif Sigalou 2019.02.28
        const notification = this.createNotificationObject(serialOrName, 'Reminder', label, new Date(Number(timestamp)));
        this.createNotification(notification, callback);
    }
    setAlarm(serialOrName, timestamp, recurring, callback) {        // **Modif Sigalou 2019.02.28

        const notification = this.createNotificationAlarmObject(serialOrName, recurring, '', new Date(Number(timestamp)));
        this.createNotification(notification, callback);
    }

    getHomeGroup(callback) {
        this.httpsGet (`https://alexa-comms-mobile-service.amazon.com/users/${this.commsId}/identities?includeUserName=true`, callback);
    }

    getDevicePreferences(callback) {
        this.httpsGet ('https://${this.baseUrl}/api/device-preferences?cached=true&_=%t', callback);
    }

    getSmarthomeDevices(callback) {
        this.httpsGet ('https://${this.baseUrl}/api/phoenix?_=%t', function (err, res) {
            if (err || !res || !res.networkDetail) return callback(err, res);
            try {
                res = JSON.parse(res.networkDetail);
            } catch(e) {
                return callback('invalid JSON');
            }
            if (!res.locationDetails) return callback('locationDetails not found');
            callback (err, res.locationDetails);
        });
    }

    getSmarthomeGroups(callback) {
        this.httpsGet ('https://${this.baseUrl}/api/phoenix/group?_=%t', callback);
    }

    getSmarthomeEntities(callback) {
        this.httpsGet ('/api/behaviors/entities?skillId=amzn1.ask.1p.smarthome',
            callback,
            {
                headers: {
                    'Routines-Version': '1.1.210292'
                }
            }
        );
    }

    getSmarthomeBehaviourActionDefinitions(callback) {
        this.httpsGet ('/api/behaviors/actionDefinitions?skillId=amzn1.ask.1p.smarthome',
            callback,
            {
                headers: {
                    'Routines-Version': '1.1.210292'
                }
            }
        );
    }


    renameDevice(serialOrName, newName, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let o = {
            accountName: newName,
            serialNumber: dev.serialNumber,
            deviceAccountId: dev.deviceAccountId,
            deviceType: dev.deviceType,
            //deviceOwnerCustomerId: oo.deviceOwnerCustomerId
        };
        this.httpsGet (`https://${this.baseUrl}/api/devices-v2/device/${dev.serialNumber}`,
            callback,
            {
                method: 'PUT',
                data: JSON.stringify (o),
            }
        );
    }

    deleteSmarthomeDevice(smarthomeDevice, callback) {
        let flags = {
            method: 'DELETE'
            //data: JSON.stringify (o),
        };
        this.httpsGet (`https://${this.baseUrl}/api/phoenix/appliance/${smarthomeDevice}`, callback, flags);
    }

    deleteSmarthomeGroup(smarthomeGroup, callback) {
        let flags = {
            method: 'DELETE'
            //data: JSON.stringify (o),
        };
        this.httpsGet (`https://${this.baseUrl}/api/phoenix/group/${smarthomeGroup}`, callback, flags);
    }

    deleteAllSmarthomeDevices(callback) {
        let flags = {
            method: 'DELETE'
            //data: JSON.stringify (o),
        };
        this.httpsGet (`/api/phoenix`, callback, flags);
    }

    discoverSmarthomeDevice(callback) {
        let flags = {
            method: 'POST'
            //data: JSON.stringify (o),
        };
        this.httpsGet ('/api/phoenix/discovery', callback, flags);
    }

    querySmarthomeDevices(applicanceIds, entityType, callback) {
        if (typeof entityType === 'function') {
            callback = entityType;
            entityType = 'APPLIANCE'; // other value 'GROUP'
        }

        let reqArr = [];
        if (!Array.isArray(applicanceIds)) applicanceIds = [applicanceIds];
        for (let id of applicanceIds) {
            reqArr.push({
                'entityId': id,
                'entityType': entityType
            });
        }

        let flags = {
            method: 'POST',
            data: JSON.stringify ({
                'stateRequests': reqArr
            })
        };
        this.httpsGet (`/api/phoenix/state`, callback, flags);
        /*
        {
            'stateRequests': [
                {
                    'entityId': 'AAA_SonarCloudService_00:17:88:01:04:1D:4C:A0',
                    'entityType': 'APPLIANCE'
                }
            ]
        }
        {
        	'deviceStates': [],
        	'errors': [{
        		'code': 'ENDPOINT_UNREACHABLE',
        		'data': null,
        		'entity': {
        			'entityId': 'AAA_SonarCloudService_00:17:88:01:04:1D:4C:A0',
        			'entityType': ''
        		},
        		'message': null
        	}]
        }
        */
    }

    executeSmarthomeDeviceAction(entityIds, parameters, entityType, callback) {
        if (typeof entityType === 'function') {
            callback = entityType;
            entityType = 'APPLIANCE'; // other value 'GROUP'
        }

        let reqArr = [];
        if (!Array.isArray(entityIds)) entityIds = [entityIds];
        for (let id of entityIds) {
            reqArr.push({
                'entityId': id,
                'entityType': entityType,
                'parameters': parameters
            });
        }

        let flags = {
            method: 'PUT',
            data: JSON.stringify ({
                'controlRequests': reqArr
            })
        };
        this.httpsGet (`/api/phoenix/state`, callback, flags);
        /*
        {
            'controlRequests': [
                {
                    'entityId': 'bbd72582-4b16-4d1f-ab1b-28a9826b6799',
                    'entityType':'APPLIANCE',
                    'parameters':{
                        'action':'turnOn'
                    }
                }
            ]
        }
        {
        	'controlResponses': [],
        	'errors': [{
        		'code': 'ENDPOINT_UNREACHABLE',
        		'data': null,
        		'entity': {
        			'entityId': 'bbd72582-4b16-4d1f-ab1b-28a9826b6799',
        			'entityType': 'APPLIANCE'
        		},
        		'message': null
        	}]
        }
        */
    }


    unpaireBluetooth(serialOrName, btAddress, callback) {
        let dev = this.find(serialOrName);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let flags = {
            method: 'POST',
            data: JSON.stringify ({
                bluetoothDeviceAddress: btAddress,
                bluetoothDeviceClass: 'OTHER'
            })
        };
        this.httpsGet (`https://${this.baseUrl}/api/bluetooth/unpair-sink/${dev.deviceType}/${dev.serialNumber}`, callback, flags);
    }

    deleteDevice(serialOrName, callback) {
        let dev = this.find(serialOrName, callback);
        if (!dev) return callback && callback(new Error ('Unknown Device or Serial number', null));

        let flags = {
            method: 'DELETE',
            data: JSON.stringify ({
                deviceType: dev.deviceType
            })
        };
        this.httpsGet (`https://${this.baseUrl}/api/devices/device/${dev.serialNumber}?deviceType=${dev.deviceType}`, callback, flags);
    }
}

module.exports = AlexaRemote;
