var http = require('http');

var s = http.createServer(function(req, res) {
	res.writeHead(200, { 'content-type': 'text-plain' });
	res.end("hello world \n");
});

s.listen(8000);
