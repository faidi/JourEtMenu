  console.log('oklk,k,klk,lk,lk,lk');
 //var faye = require('faye');
var client = new faye.Client('http://loalhost:5057/');
console.log('hlkhlklh');
client.publish('/messages', {
	  text: 'Hello world'
	});

client.subscribe('/messages', function (message) {
  alert('Nouveau message : ' + message.text);
  
});