Auto-upgrade Multiple WordPress sites with Node via XML-RPC
===

AUX is a Node app in combination with a WordPress plugin, which can be used to upgrade WordPress using the built in auto-upgrade feature. 

## Requirements

[node.js](http://nodejs.org/)

## Installation

1. Add the aux.php plugin file to your WordPress sites, and activate the AUX plugin.
2. Go into your WordPress admin > settings > writing and enable XML-RPC
3. Clone a copy of AUX to where you want to run the upgrades from.
    `git clone git://github.com/karmadude/AUX`
4. Install xmlrpc module
    `npm install xmlrpc`
4. Open config.js and add settings for each site you want to upgrade.
5. Run AUX `node aux.js`



