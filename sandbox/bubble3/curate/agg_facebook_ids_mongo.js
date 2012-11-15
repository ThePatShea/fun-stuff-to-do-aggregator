	// Database functions
		function getUserArray()
		{
			// Temporary code to set some access tokens. Will be replaced with MongoDB.
				var users  =  new Array();
				users[0]   =  new Array();		
				users[1]   =  new Array();
		
				users[0]["facebook_user_id"]  =  "681701524";
				users[0]["access_token"]      =	 "AAADH49iOK2kBAMYTZCTZCUkQH9KvdfWOu4I7uRQ1nUWpHFJ36X5UxsY8etbxZBisIbZBhtZAiI42XoTMVIX9PWtS5ETDUstZAe3Fe9rFZCLeQZDZD";
				
				users[1]["facebook_user_id"]  =  "100004362973816";
				users[1]["access_token"]      =  "AAADH49iOK2kBAPKp6z5ZCcWtc7OwRg2aL7ypv9YODbQ5MDI2OeZBzJd568hKxxeNDkzsmjrhbZCuCBxEuR0xHJrPNB3TGpLbl3qPvtQPl58uzqWsyLZA";
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
		var default_access_token = "AAADH49iOK2kBAHGelWBieDaJhLv9x5UDHC3nAZAZAj4m2qiZBDaLHXeY25VJWfyhSeSdfZAkNApgMgif033OPSLO2Rc86pCZALwZCOvsB6ogVsVCxTpUdj"; //Robert Woodruff Access Token

		function agg_facebook(query, access_token, callback)
		{
			if(typeof(access_token) === 'undefined') access_token = default_access_token;

			var https = require('https');

			https.get("https://graph.facebook.com/"+query+"&access_token="+access_token, function(res) {

				var returnData = "";

				res.on("data", function(chunk) {
					returnData += chunk;
				});

				res.on("end", function() {
					var returnInfo = JSON.parse(returnData);
					callback(returnInfo);
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
		function agg_from_events_venues_creators(callback) { agg_facebook("fql?q={'page':'SELECT page_id FROM page WHERE page_id IN (SELECT venue.id,creator FROM event WHERE eid IN ("+getEventList()+"))'}", default_access_token, callback); }
		function agg_from_pages_likes() { agg_facebook("fql?q={'page':'SELECT page_id FROM page_fan WHERE uid IN ("+getPageList()+")'}"); }
		function agg_from_users_likes() { agg_from_users("SELECT page_id FROM page_fan"); }

		function agg_from_search_pages()
		{
			agg_facebook("search?q=%20&type=place&fields=id&center=33.755,-84.39&distance=10000&limit=5000");
			agg_facebook("search?q=Georgia&fields=id&type=place&limit=5000");
			agg_facebook("search?q=Atlanta&fields=id&type=place&limit=5000");
			agg_facebook("search?q=Decatur&fields=id&type=place&limit=5000");
			agg_facebook("search?q=Emory&fields=id&type=place&limit=5000");
			agg_facebook("search?q=GA&fields=id&type=place&limit=5000");
			
			agg_facebook("search?q=Georgia&fields=id&type=page&limit=5000");
			agg_facebook("search?q=Atlanta&fields=id&type=page&limit=5000");
			agg_facebook("search?q=Decatur&fields=id&type=page&limit=5000");
			agg_facebook("search?q=Emory&fields=id&type=page&limit=5000");
			agg_facebook("search?q=GA&fields=id&type=page&limit=5000");
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
			// agg_from_events_venues_creators(function (returnData){console.log(returnData)});    // Gets pages from events' venues and creators
			// agg_from_search_pages();              // Gets pages from our search queries
			// agg_from_users_likes();               // Gets pages from users' likes
			// agg_from_pages_likes();               // Gets pages from pages' likes

		// Aggregate events
			// agg_from_search_events();             // Gets events from our search queries
			// agg_from_users_events();              // Gets events users are invited to
			// agg_from_pages_events();              // Gets events posted by pages



  function store_facebook_info(returnInfo) {
    var agg_page_schema = new mongoose.Schema({
      page_id: { type: Number, index: {unique: true} }
    });
  
    var agg_page = db.model('agg_page', agg_page_schema);

	var returnInfo_length  =  returnInfo.data.length;

	for (i = 0; i < returnInfo_length; i++)
	{
		var resultName =  returnInfo.data[i].name;

		if (resultName == "user" || resultName == "event" || resultName == "page")
		{
			var insertInfo         =  returnInfo.data[i].fql_result_set;
			var insertInfo_length  =  insertInfo.length;

			for (j = 0; j < insertInfo_length; j++) {
				var insert_agg_page = new agg_page({page_id: insertInfo[j].page_id});
				insert_agg_page.save();
			}
		}
	}

    console.log("finished inserting");

    
    agg_page.find(function (err, agg_pages) {
      if (err) {} // TODO handle err
      console.log(agg_pages);
    });
  }




var mongoose = require('mongoose'), db = mongoose.createConnection('localhost', 'test');
db.on('error', console.error.bind(console, 'connection error:'));

db.once('open', function () {

  agg_from_events_venues_creators(function(returnInfo){ store_facebook_info(returnInfo) } );

});
