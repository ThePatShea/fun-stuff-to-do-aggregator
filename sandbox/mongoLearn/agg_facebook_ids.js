var mongoose = require('mongoose'), db = mongoose.createConnection('localhost', 'test');
db.on('error', console.error.bind(console, 'connection error:'));

db.once('open', function () {

	// Database functions
		function getUserArray()
		{
			// Temporary code to set some access tokens. Will be replaced with MongoDB.
				var users  =  new Array();
				users[0]   =  new Array();		
				users[1]   =  new Array();
		
				users[0]["facebook_user_id"]  =  "681701524";
				users[0]["access_token"]      =	 "AAADH49iOK2kBALIei28zWyqqeoZAE52GTSBXmpZC5weYxQT9LlHEDqPNF9vVpDle7XKT68ROcZATCFmrrFAKIBpb044J5aFZA1OvCEtGvAZDZD";
				
				users[1]["facebook_user_id"]  =  "638611498";
				users[1]["access_token"]      =  "AAADH49iOK2kBANtFE6jaZAZAmfTZCNGaS3ghksDYAqAiOVflzYTx54AAeCQIXTaSOQY1GVGQ1kenKCGoJriDyqpPt3PEIPlNvRaAYQmfQZDZD";
			// Temporary code to set some access tokens. Will be replaced with MongoDB.
		
			return users;
		}
	
		function getPageList()
		{
			// Temporary code to set comma separated list of pages. Will be replaced with MongoDB.
				var pageList = "218760225176,128298896566,132765608557";
			// Temporary code to set comma separated list of pages. Will be replaced with MongoDB.
	
			return pageList;
		}
	
		function getEventList()
		{
			// Temporary code to set comma separated list of events. Will be replaced with MongoDB.
				var eventList = "475278835846444,430989373603184,406648182735726,415456078512593,282325598539108,231163617006735";
			// Temporary code to set comma separated list of events. Will be replaced with MongoDB.
	
			return eventList;
		}

	
	// Facebook base functions
		var default_access_token = "AAADH49iOK2kBAAF7v79vAZCAnOLrKJmZAieZB8ZBcJgizqqH4L6Ey3HxckdjNgnGfk0kkvqYBmJELzx8JmZBPYHxmDXdvl0KXsGY7YTSOSwZDZD"; //John Campus Access Token

		function agg_facebook(query, access_token)
		{
			if(typeof(access_token) === 'undefined') access_token = default_access_token;

			var https = require('https');

			https.get("https://graph.facebook.com/"+query+"&access_token="+access_token, function(res) {

				var returnData = "";

				res.on("data", function(chunk) {
					returnData += chunk;
				});

				res.on("end", function() {
					console.log(returnData);
					console.log('finished');
				});

			}).on('error', function(e) {
				console.log("Got error: " + e.message);
			});
		}


		function agg_from_users(query, end_parens)
		{
			if(typeof(end_parens) === 'undefined') end_parens = "";
			
			var users         =  getUserArray();
			var users_length  =  users.length;
			
			for (i = 0; i < users_length; i++)
			{
				agg_facebook("fql?q={'results':'"+query+" WHERE uid IN (SELECT uid2 FROM friend WHERE uid1="+users[i]["facebook_user_id"]+") OR uid="+users[i]["facebook_user_id"]+end_parens+"'}",users[i]["access_token"]);
			}
		}
	
	
	// Aggregate Pages	
		function agg_from_events_venues_creators() { agg_facebook("fql?q={'page':'SELECT page_id FROM page WHERE page_id IN (SELECT venue.id,creator FROM event WHERE eid IN ("+getEventList()+"))'}"); }
		function agg_from_pages_likes() { agg_facebook("fql?q={'page':'SELECT page_id FROM page_fan WHERE uid IN ("+getPageList()+")'}"); }
		function agg_from_users_likes() { agg_from_users("SELECT page_id FROM page_fan"); }

		function agg_from_search_pages()
		{
			agg_facebook("search?q=%20&type=place&center=33.755,-84.39&distance=10000&limit=5000");
			agg_facebook("search?q=Georgia&fields=id,name&type=place&limit=5000");
			agg_facebook("search?q=Atlanta&fields=id,name&type=place&limit=5000");
			agg_facebook("search?q=Decatur&fields=id,name&type=place&limit=5000");
			agg_facebook("search?q=Emory&fields=id,name&type=place&limit=5000");
			agg_facebook("search?q=GA&fields=id,name&type=place&limit=5000");
			
			agg_facebook("search?q=Georgia&fields=id,name&type=page&limit=5000");
			agg_facebook("search?q=Atlanta&fields=id,name&type=page&limit=5000");
			agg_facebook("search?q=Decatur&fields=id,name&type=page&limit=5000");
			agg_facebook("search?q=Emory&fields=id,name&type=page&limit=5000");
			agg_facebook("search?q=GA&fields=id,name&type=page&limit=5000");
		}
	
	
	// Aggregate Events
		function agg_from_pages_events() { agg_facebook("fql?q={'event':'SELECT eid,start_time,end_time FROM event WHERE eid IN (SELECT eid FROM event_member WHERE uid IN ("+getPageList()+"))'}"); }
		function agg_from_users_events() { agg_from_users("SELECT eid,start_time,end_time FROM event WHERE eid IN (SELECT eid FROM event_member",")"); }

		function agg_from_search_events()
		{
			agg_facebook("search?q=Georgia%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			agg_facebook("search?q=Atlanta%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			agg_facebook("search?q=Decatur%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			agg_facebook("search?q=Emory%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			agg_facebook("search?q=GA%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
	
			agg_facebook("search?q=Georgia&fields=id,start_time,end_time&type=event&limit=5000");
			agg_facebook("search?q=Atlanta&fields=id,start_time,end_time&type=event&limit=5000");
			agg_facebook("search?q=Decatur&fields=id,start_time,end_time&type=event&limit=5000");
			agg_facebook("search?q=Emory&fields=id,start_time,end_time&type=event&limit=5000");
			agg_facebook("search?q=GA&fields=id,start_time,end_time&type=event&limit=5000");
		}
	
	
	// Aggregation Functions
		// Aggregate pages
			// agg_from_events_venues_creators();    // Gets pages from events' venues and creators
			// agg_from_search_pages();              // Gets pages from our search queries
			// agg_from_users_likes();               // Gets pages from users' likes
			// agg_from_pages_likes();               // Gets pages from pages' likes

		// Aggregate events
			// agg_from_search_events();             // Gets events from our search queries
			// agg_from_users_events();              // Gets events users are invited to
			// agg_from_pages_events();              // Gets events posted by pages

});
