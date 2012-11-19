// Require base scripts
        var base_mongo = require("./javascripts/base_mongo.js");


// Sort bubbles
	//TODO: Campus Events: Make it fit the parameters I wrote down on the sheet of paper
	//TODO: Concerts/Night Life: Look for the words "concert/night life/nightlife" in their page names/descriptions AND their event names/descriptions/locations
	//TODO: Fix the query for the Arts & Music Bubble so it gets more relevant events
	base_mongo.sort_bubbles("night life", {$or : [{"type" : /night/i} , {"categories.name" : /night/i}] , "location.state" : "GA"});
	base_mongo.sort_bubbles("concerts", {$or : [{"type" : /concert/i} , {"categories.name" : /concert/i}] , "location.state" : "GA"});
	base_mongo.sort_bubbles("campus events", {$or : [{name : /Emory/} , {description : /Emory/}]}, {$or : [{name : /Emory/} , {description : /Emory/} , {location : /Emory/}]});
	

	base_mongo.sort_bubbles("arts and music", {$or : [{"type" : /art/i} , {"categories.name" : /art/i} , {"name" : /art/i}] , "location.state" : "GA"});




