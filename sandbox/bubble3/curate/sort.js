// Require base scripts
        var base_mongo = require("./javascripts/base_mongo.js");

	base_mongo.find_mongo_info("event", function(mongo_model) {
		console.log(mongo_model);
	});
