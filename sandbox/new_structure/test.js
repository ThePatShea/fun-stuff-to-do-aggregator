var MongoDBSessionStore = require('connect-mongodb');

var app = module.exports = express.createServer(
	express.bodyParser(),
	express.methodOverride(),
	express.cookieParser(),
	express.session({store: new MongoDBSessionStore({ }), secret: 'foobar'})
);
