/* jshint -W097 */
/* jshint -W030 */
/* jshint strict: false */
/* jslint node: true */
/* jslint esversion: 6 */

alexaCookie = require('../alexa-cookie');

const config = {
    logger: console.log,
    setupProxy: true,           // optional: should the library setup a proxy to get cookie when automatic way did not worked? Default false!
    proxyOwnIp: 'localhost',          // required if proxy enabled: provide own IP or hostname to later access the proxy. needed to setup all rewriting and proxy stuff internally
    proxyPort: 3001,            // optional: use this port for the proxy, default is 0 means random port is selected
    proxyListenBind: '0.0.0.0', // optional: set this to bind the proxy to a special IP, default is '0.0.0.0'
    proxyLogLevel: 'info'
};

alexaCookie.generateAlexaCookie('amazon@email.de', 'amazon-password', config, (err, result) => {
    console.log('RESULT: ' + err + ' / ' + JSON.stringify(result));
    if (result && result.csrf) {
        alexaCookie.stopProxyServer();
    }
});
