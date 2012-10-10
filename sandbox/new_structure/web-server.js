var http = require('http');

http.createServer(function(req, res) {
	res.writeHead(200, { 'content-type': 'text-plain' });
	res.end("hello world \n");
}).listen(8124, "127.0.0.1");
