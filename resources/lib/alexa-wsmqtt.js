/* jshint -W097 */
/* jshint -W030 */
/* jshint strict: false */
/* jslint node: true */
/* jslint esversion: 6 */
const WebSocket = require('ws');
const EventEmitter = require('events');
const crypto = require('crypto');

class AlexaWsMqtt extends EventEmitter {

    constructor(options, cookie, macDms) {
        super();

        this._options = options;
        this.stop = false;
        let serialArr = null;
        this.cookie = cookie;
        this.macDms = macDms;
        if (cookie) serialArr = cookie.match(/ubid-[a-z]+=([^;]+);/);
        if (!serialArr || !serialArr[1]) {
            this._options.logger && this._options.logger('{MQTT}   ║ Cookie incomplete : ' + JSON.stringify(serialArr)  ,'DEBUG');
            return undefined;
        }
        this.accountSerial = serialArr[1];
        this.websocket = null;
        this.pingPongInterval = null;
        this.errorRetryCounter = 0;
        this.reconnectTimeout = null;
        this.pongTimeout = null;
        this.connectionActive = false;

        this.messageId = Math.floor(1E9 * Math.random());
    }

    connectType1() {
        const urlTime = Date.now();
        let amazonPage = '.' + this._options.amazonPage;
        if (amazonPage === '.amazon.com') amazonPage = '-js.amazon.com'; // Special Handling for US!
        const url = `wss://dp-gw-na${amazonPage}/?x-amz-device-type=ALEGCNGL9K0HM&x-amz-device-serial=${this.accountSerial}-${urlTime}`;

        this.websocket = new WebSocket(url, [],
            {
                'perMessageDeflate': true,
                'protocolVersion': 13,

                'timeout': 5000,

                'headers': {
                    'Connection': 'keep-alive, Upgrade',
                    'Upgrade': 'websocket',
                    'Host': 'dp-gw-na.' + this._options.amazonPage,
                    'Origin': 'https://alexa.' + this._options.amazonPage,
                    'Pragma': 'no-cache',
                    'Cache-Control': 'no-cache',
                    //'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    //'Accept-Language': 'de,en-US;q=0.7,en;q=0.3',
                    //'Sec-WebSocket-Key': 'aV/ud2q+G4pTtOhlt/Amww==',
                    //'Sec-WebSocket-Extensions': 'permessage-deflate', // 'x-webkit-deflate-frame',
                    //'User-Agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15G77 PitanguiBridge/2.2.219248.0-[HARDWARE=iPhone10_4][SOFTWARE=11.4.1]',
                    'Cookie': this.cookie,
                }
            }
        );
        return url;
    }

    /**
     * @param method The http request method (GET, POST, DELETE, ...).
     * @param path The requested http url path and query.
     * @param body The http message body.
     * @return {string}
     */
    createRequestSignature(method, path, body) {
        /*
            date = datetime.utcnow().isoformat("T") + "Z"
            body = body.decode("utf-8")

            data = f"{method}\n{path}\n{date}\n{body}\n{adp_token}"

            key = rsa.PrivateKey.load_pkcs1(private_key.encode("utf-8"))
            cipher = rsa.pkcs1.sign(data.encode(), key, "SHA-256")
            signed_encoded = base64.b64encode(cipher)

            signature = f"{signed_encoded.decode()}:{date}"

            return {
                "x-adp-token": adp_token,
                "x-adp-alg": "SHA256withRSA:1.0",
                "x-adp-signature": signature
            }

            <base64>:2021-10-08T23:49:33Z
        */
        const now = new Date().toISOString();

        const sign = crypto.createSign('SHA256');
        sign.write(method + '\n');
        sign.write(path + '\n');
        sign.write(now + '\n');
        sign.write(body.toString('utf-8') + '\n');
        sign.write(this.macDms.adp_token);
        sign.end();

        const privateKey = '-----BEGIN PRIVATE KEY-----\n' + this.macDms.device_private_key + '\n-----END PRIVATE KEY-----';

        return `${sign.sign(privateKey, 'base64')}:${now}`;
    }

    connectType2() {
        let amazonPage = '.' + this._options.amazonPage;
        if (amazonPage === '.amazon.com') amazonPage = '-js.amazon.com'; // Special Handling for US!
        const url = `wss://dp-gw-na${amazonPage}/tcomm/`;

        this.websocket = new WebSocket(url, [],
            {
                'perMessageDeflate': true,
                'protocolVersion': 13,

                'timeout': 5000,

                'headers': {
                    'Connection': 'keep-alive, Upgrade',
                    'Upgrade': 'websocket',
                    'Host': 'dp-gw-na.' + this._options.amazonPage,
                    'Origin': 'https://alexa.' + this._options.amazonPage,
                    'Pragma': 'no-cache',
                    'Cache-Control': 'no-cache',
                    //'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    //'Accept-Language': 'de,en-US;q=0.7,en;q=0.3',
                    //'User-Agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15G77 PitanguiBridge/2.2.219248.0-[HARDWARE=iPhone10_4][SOFTWARE=11.4.1]',
                    'Cookie': this.cookie,
                    'x-dp-comm-tuning': 'A:F;A:H',
                    'x-dp-reason': 'ClientInitiated;1',
                    'x-dp-tcomm-purpose': 'Regular',
                    //'x-dp-deviceVersion': 'motorola/osprey_reteu_2gb/osprey_u2:6.0.1/MPI24.107-55/33:user/release-keys',
                    //'x-dp-networkType': 'WIFI',
                    //'x-dp-tcomm-versionCode': '894920010',
                    //'x-dp-oui': 'dca632',
                    'x-dp-obfuscatedBssid': '-2019514039',
                    'x-dp-tcomm-versionName': '2.2.443692.0',
                    'x-adp-signature': this.createRequestSignature('GET', '/tcomm/', ''),
                    'x-adp-token': this.macDms.adp_token,
                    'x-adp-alg': 'SHA256WithRSA:1.0',
                }
            }
        );
        return url;
    }

    sendWs(data) {
        return new Promise(resolve => {
            if (!this.websocket) {
                resolve();
                return;
            }
            this.websocket.send(data, () => resolve());
        });
    }

    wait(delay) {
        return new Promise(resolve => {
            setTimeout(() => resolve(), delay);
        });
    }

    connect() {
        let url;
        try {
            if (!this.macDms || !this.macDms.adp_token || !this.macDms.device_private_key) {
                this._options.logger && this._options.logger('{MQTT}   ║ Try to initialize old style push connection because macDms data missing'  ,'DEBUG');
                url = this.connectType1();
            } else {
                url = this.connectType2();
            }
        }
        catch (err) {
            this._options.logger && this._options.logger('{MQTT}   ║ Error on Init ' + err.message  ,'DEBUG');
            this._options.logger && this._options.logger(err.stack);
            this.emit('error', err);
            return;
        }
        let msgCounter = 0;
        let initTimeout = null;

        const onWebsocketClose = (code, reason) => {
            if (reason) {
                reason = reason.toString();
            }
            this.websocket = null;
            this.connectionActive = false;
            this._options.logger && this._options.logger('{MQTT}   ║ Close: ' + code + ': ' + reason  ,'DEBUG');
            if (initTimeout) {
                clearTimeout(initTimeout);
                initTimeout = null;
            }
            if (this.pingPongInterval) {
                clearInterval(this.pingPongInterval);
                this.pingPongInterval = null;
            }
            if (this.pongTimeout) {
                clearTimeout(this.pongTimeout);
                this.pongTimeout = null;
            }
            if (code === 4001 && reason.startsWith('before - Could not find any')) { // code = 40001, reason = "before - Could not find any vali"
                this._options.logger && this._options.logger('{MQTT}   ║ Cookie invalid!'  ,'DEBUG');
                this.emit('disconnect', false, 'Cookie invalid');
                return;
            }
            if (this.stop) return;
            if (this.errorRetryCounter > 100) {
                this.emit('disconnect', false, 'Too many failed retries. Check cookie and data');
                return;
            } else {
                this.errorRetryCounter++;
            }

            let retryDelay = Math.min(60, (this.errorRetryCounter * 5) + 5);
            this._options.logger && this._options.logger('{MQTT}   ║ Retry Connection in ' + retryDelay + 's'  ,'DEBUG');
            this.emit('disconnect', true, 'Retry Connection in ' + retryDelay + 's');
            this.reconnectTimeout && clearTimeout(this.reconnectTimeout);
            this.reconnectTimeout = setTimeout(() => {
                this.reconnectTimeout = null;
                this.connect();
            }, retryDelay * 1000);
        };

        initTimeout = setTimeout(() => {
            this._options.logger && this._options.logger('{MQTT}   ║ Initialization not done within 30s'  ,'DEBUG');
            try {
                this.websocket && this.websocket.close();
            } catch (err) {
                //just make sure
            }
            if (this.websocket || !this.reconnectTimeout) { // seems no close was emmitted so far?!
                onWebsocketClose();
            }
        }, 30000);

        this.websocket.on('open', () => {
            if (! this.websocket) return;
            if (this.stop) {
                this.websocket.close();
                return;
            }
            this._options.logger && this._options.logger('{MQTT}   ║ Open: ' + url  ,'DEBUG');
            this.connectionActive = false;

            if (!this.macDms) {
                // tell Tuning Service that we support "A:H" protocol = AlphaPrococol
                const msg = Buffer.from('0x99d4f71a 0x0000001d A:HTUNE');
                //console.log('SEND: ' + msg.toString('ascii'));
                this._options.logger && this._options.logger('{MQTT}   ║ A:H Initialization Msg 1 sent'  ,'DEBUG');
                if (this.websocket.readyState !== 1 /* OPEN */) return;
                this.websocket.send(msg);
            }
        });

        this.websocket.on('close', onWebsocketClose);

        this.websocket.on('error', (error) => {
            this._options.logger && this._options.logger('{MQTT}   ║ Error: ' + error  ,'DEBUG');
            this.emit('error', error);
            this.websocket && this.websocket.terminate();
        });

        this.websocket.on('unexpected-response', (request, response) => {
            this._options.logger && this._options.logger('{MQTT}   ║ Unexpected Response: ' + JSON.stringify(response)  ,'DEBUG');
        });

        this.websocket.on('message', async (data) => {
            if (!this.websocket || this.websocket.readyState !== 1 /* OPEN */) {
                return;
            }
            this._options.logger && this._options.logger('{MQTT}   ║ Incoming RAW message: ' + data.toString('hex')  ,'DEBUG');
            let message = this.parseIncomingMessage(data);
            this._options.logger && this._options.logger('{MQTT}   ║ Incoming message: ' + JSON.stringify(message)  ,'DEBUG');

            if (msgCounter === 0) { // initialization
                if (message.content.protocolName) {
                    this.protocolName = message.content.protocolName;
                    if (this.protocolName !== 'A:H' && this.protocolName !== 'A:F') {
                        this._options.logger && this._options.logger('{MQTT}   ║ Server requests unknown protocol: ' + this.protocolName  ,'DEBUG');
                    }
                }
                else {
                    this._options.logger && this._options.logger('{MQTT}   ║ Unexpected Response: ' + JSON.stringify(message)  ,'DEBUG');
                    this.protocolName = this.macDms ? 'A:F' : 'A:H';
                }
                this._options.logger && this._options.logger('{MQTT}   ║ Detected protocol ' + this.protocolName  ,'DEBUG');

                let msg;
                if (this.protocolName === 'A:F') { // A:F
                    msg = Buffer.from('0xfe88bc52 0x0000009c {"protocolName":"A:F","parameters":{"AlphaProtocolHandler.receiveWindowSize":"16","AlphaProtocolHandler.maxFragmentSize":"16000"}}TUNE');
                    await this.sendWs(msg);
                   this._options.logger && this._options.logger('{MQTT}   ║ A:F Initialization Msg 2 sent: ' + msg.toString('hex')  ,'DEBUG');
                    await this.wait(50);
                    msg = this.encodeGWRegisterAF();
                   this._options.logger && this._options.logger('{MQTT}   ║ A:F Initialization Msg 3 (Register Connection) sent: ' + msg.toString('hex')  ,'DEBUG');
                    //console.log('SEND: ' + msg.toString('ascii'));
                    await this.sendWs(msg);
                    msgCounter++;
                } else { // A:H
                    msg = Buffer.from('0xa6f6a951 0x0000009c {"protocolName":"A:H","parameters":{"AlphaProtocolHandler.receiveWindowSize":"16","AlphaProtocolHandler.maxFragmentSize":"16000"}}TUNE');
                    //console.log('SEND: ' + msg.toString('ascii'));
                    await this.sendWs(msg);

                    await this.wait(50);
                    //msg = new Buffer('MSG 0x00000361 0x0e414e45 f 0x00000001 0xd7c62f29 0x0000009b INI 0x00000003 1.0 0x00000024 ff1c4525-c036-4942-bf6c-a098755ac82f 0x00000164d106ce6b END FABE');
                    msg = this.encodeGWHandshake();
                    //console.log('SEND: ' + msg.toString('ascii'));
                    await this.sendWs(msg);
                    this._options.logger && this._options.logger('{MQTT}   ║ A-H Initialization Msg 2+3 sent'  ,'DEBUG');
                }
            }
            if (msgCounter === 1) {
                if (this.protocolName === 'A:H') {
                    //let msg = new Buffer('MSG 0x00000362 0x0e414e46 f 0x00000001 0xf904b9f5 0x00000109 GWM MSG 0x0000b479 0x0000003b urn:tcomm-endpoint:device:deviceType:0:deviceSerialNumber:0 0x00000041 urn:tcomm-endpoint:service:serviceName:DeeWebsiteMessagingService {"command":"REGISTER_CONNECTION"}FABE');
                    let msg = this.encodeGWRegisterAH();
                    this._options.logger && this._options.logger('{MQTT}   ║ Initialization Msg 4 (Register Connection) sent'  ,'DEBUG');
                    //console.log('SEND: ' + msg.toString('ascii'));
                    await this.sendWs(msg);
                }

                setTimeout(() => {
                    if (!this.websocket || this.websocket.readyState !== 1 /* OPEN */) {
                        return;
                    }

                    //msg = new Buffer('4D53472030783030303030303635203078306534313465343720662030783030303030303031203078626332666262356620307830303030303036322050494E00000000D1098D8CD1098D8C000000070052006500670075006C0061007246414245', 'hex'); // "MSG 0x00000065 0x0e414e47 f 0x00000001 0xbc2fbb5f 0x00000062 PIN" + 30 + "FABE"
                    let msg = this.encodePing();
                    this._options.logger && this._options.logger('{MQTT}   ║ Send First Ping MQTT'  ,'INFO');
                    //console.log('SEND: ' + msg.toString('hex'));
                    this.websocket.send(msg);

                    this.pingPongInterval = setInterval(() => {
                        if (!this.websocket) return;
                        //let msg = new Buffer('4D53472030783030303030303635203078306534313465343720662030783030303030303031203078626332666262356620307830303030303036322050494E00000000D1098D8CD1098D8C000000070052006500670075006C0061007246414245', 'hex'); // "MSG 0x00000065 0x0e414e47 f 0x00000001 0xbc2fbb5f 0x00000062 PIN" + 30 + "FABE"
                        let msg = this.encodePing();
                        this._options.logger && this._options.logger('{MQTT}   ║ Send Ping'  ,'DEBUG');
                        //console.log('SEND: ' + msg.toString('hex'));
                        this.websocket.send(msg);

                        this.pongTimeout = setTimeout(() => {
                            this.pongTimeout = null;
                            this._options.logger && this._options.logger('{MQTT}   ║ No Pong received after 30s'  ,'DEBUG');
                            this.websocket && this.websocket.close();
                        }, 30000);
                    }, 180000);
                }, 100);
            }
            msgCounter++;
            if (msgCounter < 3 || !this.pingPongInterval) return;

            const incomingMsg = data.toString('ascii');
            //if (incomingMsg.includes('PON') && incomingMsg.includes('\u0000R\u0000e\u0000g\u0000u\u0000l\u0000a\u0000r')) {
            if (message.service === 'FABE' && message.content && message.content.messageType === 'PON' && message.content.payloadData && message.content.payloadData.includes('\u0000R\u0000e\u0000g\u0000u\u0000l\u0000a\u0000r')) {
                this._options.logger && this._options.logger('{MQTT}   ║ Received Pong'  ,'INFO');
                if (initTimeout) {
                    clearTimeout(initTimeout);
                    initTimeout = null;
                    this._options.logger && this._options.logger('{MQTT}   ║ Initialization MQTT completed','INFO');
                    this._options.logger && this._options.logger('{MQTT}   ╚═══════════════════════════════════════════════════════════════════════════════','INFO');
                    this.emit('connect');
                }
                if (this.pongTimeout) {
                    clearTimeout(this.pongTimeout);
                    this.pongTimeout = null;
                }
                this.connectionActive = true;
                return;
            }
            else if (message.content && message.content.payload) {
                let command = message.content.payload.command;
                let payload = message.content.payload.payload;

                //this._options.logger && this._options.logger('{MQTT}   ║ Command ' + command + ': ' + JSON.stringify(payload, null, 4));
                this._options.logger && this._options.logger('{MQTT}   ╠═══> Command --{' + command + '}-- : détail désactivé' ,'DEBUG');
                this.emit('command', command, payload);
                return;
            }
            this._options.logger && this._options.logger('{MQTT}   ║ Unknown Data (' + msgCounter + '): ' + incomingMsg ,'DEBUG');
            this.emit('unknown', incomingMsg);
        });
    }

    encodeNumber(val, byteLen) {
        if (!byteLen) byteLen = 8;
        let str = val.toString(16);
        while (str.length < byteLen) str = '0' + str;
        return '0x' +str;
    }

    generateUUID() {
        for (var a = [], b = 0; 36 > b; b++) {
            var c = "rrrrrrrr-rrrr-4rrr-srrr-rrrrrrrrrrrr".charAt(b);
            if ("r" === c || "s" === c) {
                var d = Math.floor(16 * Math.random());
                "s" === c && (d = d & 3 | 8);
                a.push(d.toString(16));
            }
            else a.push(c);
        }
        return a.join("");
    }

    encodeGWHandshake() {
        //pubrelBuf = new Buffer('MSG 0x00000361 0x0e414e45 f 0x00000001 0xd7c62f29 0x0000009b INI 0x00000003 1.0 0x00000024 ff1c4525-c036-4942-bf6c-a098755ac82f 0x00000164d106ce6b END FABE');
        this.messageId++;
        let msg = 'MSG 0x00000361 '; // Message-type and Channel = GW_HANDSHAKE_CHANNEL;
        msg += this.encodeNumber(this.messageId) + ' f 0x00000001 ';
        let idx1 = msg.length;
        msg += '0x00000000 '; // Checksum!
        let idx2 = msg.length;
        msg += '0x0000009b '; // length content
        msg += 'INI 0x00000003 1.0 0x00000024 '; // content part 1
        msg += this.generateUUID();
        msg += ' ';
        msg += this.encodeNumber(Date.now(), 16);
        msg += ' END FABE';
        let completeBuffer = Buffer.from(msg, 'ascii');

        let checksum = this.computeChecksum(completeBuffer, idx1, idx2);
        let checksumBuf = Buffer.from(this.encodeNumber(checksum));
        checksumBuf.copy(completeBuffer, 39);
        return completeBuffer;
    }

    encodeGWRegisterAH() {
        //pubrelBuf = new Buffer('MSG 0x00000362 0x0e414e46 f 0x00000001 0xf904b9f5 0x00000109 GWM MSG 0x0000b479 0x0000003b urn:tcomm-endpoint:device:deviceType:0:deviceSerialNumber:0 0x00000041 urn:tcomm-endpoint:service:serviceName:DeeWebsiteMessagingService {"command":"REGISTER_CONNECTION"}FABE');
        this.messageId++;
        let msg = 'MSG 0x00000362 '; // Message-type and Channel = GW_CHANNEL;
        msg += this.encodeNumber(this.messageId) + ' f 0x00000001 ';
        let idx1 = msg.length;
        msg += '0x00000000 '; // Checksum!
        let idx2 = msg.length;
        msg += '0x00000109 '; // length content
        msg += 'GWM MSG 0x0000b479 0x0000003b urn:tcomm-endpoint:device:deviceType:0:deviceSerialNumber:0 0x00000041 urn:tcomm-endpoint:service:serviceName:DeeWebsiteMessagingService {"command":"REGISTER_CONNECTION"}FABE';
        let completeBuffer = Buffer.from(msg, 'ascii');

        let checksum = this.computeChecksum(completeBuffer, idx1, idx2);
        let checksumBuf = Buffer.from(this.encodeNumber(checksum));
        checksumBuf.copy(completeBuffer, 39);
        return completeBuffer;
    }

    encodeGWRegisterAF() {
        this.messageId++;
        const completeBuffer = Buffer.alloc(0xe4);
        completeBuffer.write('MSG', 0,'ascii');
        completeBuffer.writeUInt32BE(0x00000362, 3); // Message-type and Channel = GW_CHANNEL;
        completeBuffer.writeUInt32BE(this.messageId, 7);
        completeBuffer.write('f', 11, 'ascii');
        completeBuffer.writeUInt32BE(0x00000001, 12);
        completeBuffer.writeUInt32BE(0x00000000, 16); // Checksum!
        completeBuffer.writeUInt32BE(0x000000e4, 20); // length content
        completeBuffer.write('GWM MSG 0x0000b479 0x0000003b urn:tcomm-endpoint:device:deviceType:0:deviceSerialNumber:0 0x00000041 urn:tcomm-endpoint:service:serviceName:DeeWebsiteMessagingService {"command":"REGISTER_CONNECTION"}FABE', 24, 'ascii');

        let checksum = this.computeChecksum(completeBuffer, 16, 20);
        completeBuffer.writeUInt32BE(checksum, 16); // Checksum!
        return completeBuffer;
    }

    encodePing() {
        if (this.protocolName === 'A:H') {
            return this.encodePingAH();
        } else {
            return this.encodePingAF();
        }
    }

    encodePingAH() {
        function encode(a, b, c, h) {
            a = new Uint8Array(a, c, h);
            for (c = 0; c < h; c++) a[c] = b >> 8 * (h - 1 - c) & 255;
        }

        this.messageId++;
        let msg = 'MSG 0x00000065 '; // Message-type and Channel = CHANNEL_FOR_HEARTBEAT;
        msg += this.encodeNumber(this.messageId) + ' f 0x00000001 ';
        let idx1 = msg.length;
        msg += '0x00000000 '; // Checksum!
        let idx2 = msg.length;
        msg+= '0x00000062 '; // length content

        let completeBuffer = Buffer.alloc(0x62, 0);
        let startBuffer = Buffer.from(msg, 'ascii');
        startBuffer.copy(completeBuffer);

        const header = 'PIN';
        const payload = 'Regular'; // g = h.length
        let n = new ArrayBuffer(header.length + 4 + 8 + 4 + 2 * payload.length);
        let idx = 0;
        let u = new Uint8Array(n, idx, header.length);
        let l = 0;
        let e = Date.now();

        for (let q = 0; q < header.length; q++) u[q] = header.charCodeAt(q);
        idx += header.length;
        encode(n, l, idx, 4);
        idx += 4;
        encode(n, e, idx, 8);
        idx += 8;
        encode(n, payload.length, idx, 4);
        idx += 4;
        u = new Uint8Array(n, idx, payload.length * 2);
        let q;
        for (q = 0; q < payload.length; q++) {
            u[q * 2] = 0;
            u[q * 2 + 1] = payload.charCodeAt(q);
        }
        let buf = Buffer.from(n);
        buf.copy(completeBuffer, msg.length);

        let buf2End = Buffer.from('FABE', 'ascii');
        buf2End.copy(completeBuffer, msg.length + buf.length);

        let checksum = this.computeChecksum(completeBuffer, idx1, idx2);
        let checksumBuf = Buffer.from(this.encodeNumber(checksum));
        checksumBuf.copy(completeBuffer, 39);
        return completeBuffer;
    }

    encodePingAF() {
        function encode(a, b, c, h) {
            a = new Uint8Array(a, c, h);
            for (c = 0; c < h; c++) a[c] = b >> 8 * (h - 1 - c) & 255;
        }

        this.messageId++;
        const completeBuffer = Buffer.alloc(0x3d);
        completeBuffer.write('MSG', 0,'ascii');
        completeBuffer.writeUInt32BE(0x00000065, 3); // Message-type and Channel = CHANNEL_FOR_HEARTBEAT;
        completeBuffer.writeUInt32BE(this.messageId, 7);
        completeBuffer.write('f', 11, 'ascii');
        completeBuffer.writeUInt32BE(0x00000001, 12);
        completeBuffer.writeUInt32BE(0x00000000, 16); // Checksum!
        completeBuffer.writeUInt32BE(0x0000003d, 20); // length content

        const header = 'PIN';
        const payload = 'Regular'; // g = h.length
        let n = new ArrayBuffer(header.length + 4 + 8 + 4 + 2 * payload.length);
        let idx = 0;
        let u = new Uint8Array(n, idx, header.length);
        let l = 0;
        let e = Date.now();

        for (let q = 0; q < header.length; q++) u[q] = header.charCodeAt(q);
        idx += header.length;
        encode(n, l, idx, 4);
        idx += 4;
        encode(n, e, idx, 8);
        idx += 8;
        encode(n, payload.length, idx, 4);
        idx += 4;
        u = new Uint8Array(n, idx, payload.length * 2);
        let q;
        for (q = 0; q < payload.length; q++) {
            u[q * 2] = 0;
            u[q * 2 + 1] = payload.charCodeAt(q);
        }
        let buf = Buffer.from(n);
        buf.copy(completeBuffer, 24);

        completeBuffer.write('FABE', buf.length + 24, 'ascii');

        let checksum = this.computeChecksum(completeBuffer, 16, 20);
        completeBuffer.writeUInt32BE(checksum, 16); // Checksum!
        return completeBuffer;
    }

    computeChecksum(a, f, k) {
        function b(a, b) {
            for (a = c(a); 0 != b && 0 != a;) a = Math.floor(a / 2), b--;
            return a;
        }

        function c(a) {
            0 > a && (a = 4294967295 + a + 1);
            return a;
        }

        if (k < f) throw "Invalid checksum exclusion window!";
        a = new Uint8Array(a);
        for (var h = 0, l = 0, e = 0; e < a.length; e++) e != f ? (l += c(a[e] << ((e & 3 ^ 3) << 3)), h += b(l, 32), l = c(l & 4294967295)) : e = k - 1;
        for (; h;) l += h, h = b(l, 32), l &= 4294967295;
        return c(l);
    }

    parseIncomingMessage(data) {
        if (this.protocolName === 'A:H') {
            return this.parseIncomingMessageAH(data);
        } else {
            return this.parseIncomingMessageAF(data);
        }
    }

    parseIncomingMessageAH(data) {
        function readHex(index, length) {
            let str = data.toString('ascii', index, index + length);
            if (str.startsWith('0x')) str = str.substr(2);
            return parseInt(str, 16);
        }

        function readString(index, length) {
            return data.toString('ascii', index, index + length);
        }

        let idx = 0;
        const message = {};
        message.service = readString(data.length - 4, 4);

        if (message.service === 'TUNE') {
            message.checksum = readHex(idx, 10);
            idx += 11; // 10 + delimiter;
            let contentLength = readHex(idx, 10);
            idx += 11; // 10 + delimiter;
            message.content = readString(idx, contentLength - 4 - idx);
            if (message.content.startsWith('{') && message.content.endsWith('}')) {
                try {
                    message.content = JSON.parse(message.content);
                }
                catch (e) {}
            }
        }
        else if (message.service === 'FABE') {
            message.messageType = readString(idx, 3);
            idx += 4;
            message.channel = readHex(idx, 10);
            idx += 11; // 10 + delimiter;
            message.messageId = readHex(idx, 10);
            idx += 11; // 10 + delimiter;
            message.moreFlag = readString(idx, 1);
            idx += 2; // 1 + delimiter;
            message.seq = readHex(idx, 10);
            idx += 11; // 10 + delimiter;
            message.checksum = readHex(idx, 10);
            idx += 11; // 10 + delimiter;

            let contentLength = readHex(idx, 10);
            idx += 11; // 10 + delimiter;

            message.content = {};
            message.content.messageType = readString(idx, 3);
            idx += 4;

            if (message.channel === 0x361) { // GW_HANDSHAKE_CHANNEL
                if (message.content.messageType === 'ACK') {
                    let length = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;
                    message.content.protocolVersion = readString(idx, length);
                    idx += length + 1;
                    length = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;
                    message.content.connectionUUID = readString(idx, length);
                    idx += length + 1;
                    message.content.established = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;
                    message.content.timestampINI = readHex(idx, 18);
                    idx += 19; // 18 + delimiter;
                    message.content.timestampACK = readHex(idx, 18);
                    idx += 19; // 18 + delimiter;
                }
            }
            else if (message.channel === 0x362) { // GW_CHANNEL
                if (message.content.messageType === 'GWM') {
                    message.content.subMessageType = readString(idx, 3);
                    idx += 4;
                    message.content.channel = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;

                    if (message.content.channel === 0xb479) { // DEE_WEBSITE_MESSAGING
                        let length = readHex(idx, 10);
                        idx += 11; // 10 + delimiter;
                        message.content.destinationIdentityUrn = readString(idx, length);
                        idx += length + 1;

                        length = readHex(idx, 10);
                        idx += 11; // 10 + delimiter;
                        let idData = readString(idx, length);
                        idx += length + 1;

                        idData = idData.split(' ');
                        message.content.deviceIdentityUrn = idData[0];
                        message.content.payload = idData[1];
                        if (!message.content.payload) {
                            message.content.payload = readString(idx, data.length - 4 - idx);
                        }
                        if (message.content.payload.startsWith('{') && message.content.payload.endsWith('}')) {
                            try {
                                message.content.payload = JSON.parse(message.content.payload);
                                if (message.content.payload && message.content.payload.payload && typeof message.content.payload.payload === 'string') {
                                    message.content.payload.payload = JSON.parse(message.content.payload.payload);
                                }
                            }
                            catch (e) {}
                        }
                    }
                }
            }
            else if (message.channel === 0x65) { // CHANNEL_FOR_HEARTBEAT
                idx -= 1; // no delimiter!
                message.content.payloadData = data.slice(idx, data.length - 4);
            }
        }
        //console.log(JSON.stringify(message, null, 4));
        return message;
    }

    parseIncomingMessageAF(data) {
        function readHex(index, length) {
            let str = data.toString('ascii', index, index + length);
            if (str.startsWith('0x')) str = str.substr(2);
            return parseInt(str, 16);
        }

        function readString(index, length) {
            return data.toString('ascii', index, index + length);
        }

        let idx = 0;
        const message = {};
        message.service = readString(data.length - 4, 4);

        //this._options.logger && this._options.logger('Alexa-Remote WS-MQTT: Incoming message Service: ' + message.service);

        if (message.service === 'TUNE') {
            message.checksum = readHex(idx, 10);
            idx += 11; // 10 + delimiter;
            let contentLength = readHex(idx, 10);
            idx += 11; // 10 + delimiter;
            message.content = readString(idx, contentLength - 4 - idx);
            if (message.content.startsWith('{') && message.content.endsWith('}')) {
                try {
                    message.content = JSON.parse(message.content);
                }
                catch (e) {}
            }
        }
        else if (message.service === 'FABE') {
            message.messageType = readString(0, 3);
            message.channel = data.readUInt32BE(3);
            message.messageId = data.readUInt32BE(7);
            message.moreFlag = data.readUInt32BE(11);
            message.seq = data.readUInt32BE(12);
            message.checksum = data.readUInt32BE(16);

            let contentLength = data.readUInt32BE(20);

            message.content = {};
            message.content.messageType = readString(24, 3);
            idx = 28;

            //this._options.logger && this._options.logger('Alexa-Remote WS-MQTT: Incoming message Service Channel: ' + message.channel);

            if (message.channel === 0x361) { // GW_HANDSHAKE_CHANNEL
                if (message.content.messageType === 'ACK') {
                    let length = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;
                    message.content.protocolVersion = readString(idx, length);
                    idx += length + 1;
                    length = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;
                    message.content.connectionUUID = readString(idx, length);
                    idx += length + 1;
                    message.content.established = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;
                    message.content.timestampINI = readHex(idx, 18);
                    idx += 19; // 18 + delimiter;
                    message.content.timestampACK = readHex(idx, 18);
                    idx += 19; // 18 + delimiter;
                }
            }
            else if (message.channel === 0x362) { // GW_CHANNEL
                if (message.content.messageType === 'GWM') {
                    message.content.subMessageType = readString(idx, 3);
                    idx += 4;
                    message.content.channel = readHex(idx, 10);
                    idx += 11; // 10 + delimiter;

                    if (message.content.channel === 0xb479) { // DEE_WEBSITE_MESSAGING
                        let length = readHex(idx, 10);
                        idx += 11; // 10 + delimiter;
                        message.content.destinationIdentityUrn = readString(idx, length);
                        idx += length + 1;

                        length = readHex(idx, 10);
                        idx += 11; // 10 + delimiter;
                        let idData = readString(idx, length);
                        idx += length + 1;

                        idData = idData.split(' ');
                        message.content.deviceIdentityUrn = idData[0];
                        message.content.payload = idData[1];
                        if (!message.content.payload) {
                            message.content.payload = readString(idx, data.length - 4 - idx);
                        }
                        if (message.content.payload.startsWith('{') && message.content.payload.endsWith('}')) {
                            try {
                                message.content.payload = JSON.parse(message.content.payload);
                                if (message.content.payload && message.content.payload.payload && typeof message.content.payload.payload === 'string') {
                                    message.content.payload.payload = JSON.parse(message.content.payload.payload);
                                }
                            }
                            catch (e) {}
                        }
                    }
                }
            }
            else if (message.channel === 0x65) { // CHANNEL_FOR_HEARTBEAT
                message.content.payloadData = data.slice(idx, data.length - 4);
            }
        }
        //console.log(JSON.stringify(message, null, 4));
        //this._options.logger && this._options.logger('Alexa-Remote WS-MQTT: Parsed Message: ' + JSON.stringify(message));
        return message;
    }

    disconnect() {
        if (this.reconnectTimeout) {
            clearTimeout(this.reconnectTimeout);
            this.reconnectTimeout = null;
        }
        this.stop = true;
        if (!this.websocket) return;
        try {
            this.websocket.close();
        } catch (e) {
            this._options.logger && this._options.logger('{MQTT}   ║ Disconnect error: ' +e ,'DEBUG');
        }
    }
}


module.exports = AlexaWsMqtt;
