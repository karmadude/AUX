// https://github.com/baalexander/node-xmlrpc
// npm install xmlrpc
var xmlrpc = require('xmlrpc');

var config = require('./config');

autoUpgradeSites();

function autoUpgradeSites() {
    for (var i in config.sites) {
        var site = config.mergeWithDefaults(config.sites[i], config.siteDefaults);

        console.log(site.host + site.path + " : Upgrading to " + site.version + " ...");
        var client = xmlrpc.createClient(site);

        client.methodCall('aux.autoUpgradeWP',
            [site.username, site.password, site.version, site.locale],
            function(error, value) {
                if (error) {
                    console.log(site.host + site.path + " : " + error);
                } else {
                    console.log(site.host + site.path + " : " + value.status);
                }
            });
    }
}