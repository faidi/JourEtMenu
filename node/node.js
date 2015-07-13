var http = require('http'),
    faye = require('faye');
 
var server = http.createServer(),
    bayeux = new faye.NodeAdapter({mount: '/', timeout: 45});
 
bayeux.attach(server);
server.listen(5057);