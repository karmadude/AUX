this.sites = [
    {
        host: 'your-site-1.com',
        path: '/xmlrpc.php'
    },
    {
        host: 'your-site-2.com',
        path: '/xmlrpc.php'
    },
    {
        host: 'your-site-3.com',
        port: 80,
        path: '/xmlrpc.php',
        username: 'admin',
        password: 'xyz789'
    }
];

this.siteDefaults = {
    host: 'localhost',
    port: 80,
    path: '/xmlrpc.php',
    username: 'admin',
    password: 'abc123',
    version: '3.3',
    locale: 'en_US'
};


this.mergeWithDefaults = function(site, defaults) {
    for (var key in defaults) {
        if(!site[key]) {
            site[key] =  defaults[key];
        }
    }

    return site;
};