	// Database functions
		function getUserArray()
		{
			// Temporary code to set some access tokens. Will be replaced with MongoDB.
				var users  =  new Array();
				users[0]   =  new Array();		
				users[1]   =  new Array();
		
				users[0]["facebook_user_id"]  =  "681701524"; // Pat Shea
				users[0]["access_token"]      =	 "AAADH49iOK2kBABlNIwZBy9j9DM0DrFgWZAbU4wd9syGCDZBwunZBWIlBUG4r8ScjcHBw5vXybwdjCBGnh1SZCFlheh1dvMqxvZAgrzgj6ngwZDZD";
				
				users[1]["facebook_user_id"]  =  "100004362973816"; // Robert Woodruff
				users[1]["access_token"]      =  default_access_token;
			// Temporary code to set some access tokens. Will be replaced with MongoDB.
		
			return users;
		}
	
		function getPageList(callback)
		{
			var agg_facebook_schema = new mongoose.Schema({
				facebook_id: { type: Number, index: {unique: true}},
				type: String
			}, { collection: 'agg_facebook' });

			var agg_facebook = db.model('agg_facebook', agg_facebook_schema);
			
			agg_facebook.find({ type: "page" },function (err, agg_facebook) {
				if (err) { } // TODO handle err

				var page_object = agg_facebook;
				var page_object_length = page_object.length;

				var page_list = "";

				for (i = 0; i < page_object_length; i++) {
					if (i != 0)
						page_list += ",";

					page_list += page_object[i].facebook_id;
				}

				callback(page_list);
			});
		}
	
		function getEventList()
		{
			// Temporary code to set comma separated list of events. Will be replaced with MongoDB.
				var eventList = "475278835846444,430989373603184,406648182735726,415456078512593,282325598539108,231163617006735";
			// Temporary code to set comma separated list of events. Will be replaced with MongoDB.
	
			return eventList;
		}

	
	// Facebook base functions
		var default_access_token = "AAADH49iOK2kBANbeT1M19f3BWctGfVN87ZCwDMxwpDgPsnur9WYzxnCA2a28mXyGIPvBg0W6GnLZCK3vIgQYO9rt6UbPggpSvkWMGoXIrGL4NnafNg"; //Robert Woodruff Access Token

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
					var returnInfo = JSON.parse(returnData);
					store_facebook_info(returnInfo);
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
				agg_facebook("fql?q={"+query+" WHERE uid IN (SELECT uid2 FROM friend WHERE uid1="+users[i]["facebook_user_id"]+") OR uid="+users[i]["facebook_user_id"]+end_parens+"'}",users[i]["access_token"]);
			}
		}
	
	
	// Aggregate Pages	
		function agg_from_events_venues_creators() { agg_facebook("fql?q={'page':'SELECT page_id FROM page WHERE page_id IN (SELECT venue.id,creator FROM event WHERE eid IN ("+getEventList()+"))'}"); }
		function agg_from_pages_likes() { agg_facebook("fql?q={'page':'SELECT page_id FROM page_fan WHERE uid IN ("+getPageList()+")'}"); }
		function agg_from_users_likes() { agg_from_users("'page':'SELECT page_id FROM page_fan"); }

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
		function agg_from_pages_events() {
			getPageList(function(page_list){
				agg_facebook("fql?q={'event':'SELECT eid,start_time,end_time FROM event WHERE eid IN (SELECT eid FROM event_member WHERE uid IN ("+page_list+"))'}");
			}); 
		}

		function agg_from_users_events() { agg_from_users("'event':'SELECT eid,start_time,end_time FROM event WHERE eid IN (SELECT eid FROM event_member",")"); }

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
	
	



  function store_facebook_info(returnInfo) {
    var agg_facebook_schema = new mongoose.Schema({
      facebook_id: { type: Number, index: {unique: true}},
      type: String
    }, { collection: 'agg_facebook' });
  
    var agg_facebook = db.model('agg_facebook', agg_facebook_schema);

	console.log(returnInfo);

	var returnInfo_length  =  returnInfo.data.length;

	for (i = 0; i < returnInfo_length; i++)
	{
		var resultName =  returnInfo.data[i].name;

		if (resultName == "user" || resultName == "event" || resultName == "page")
		{
			var insertInfo         =  returnInfo.data[i].fql_result_set;
			var insertInfo_length  =  insertInfo.length;

			for (j = 0; j < insertInfo_length; j++) {
				if (resultName == "user") {
					var insert_id = insertInfo[j].uid;
					var insert_type = "user";
				} else if (resultName == "event") {
					var insert_id = insertInfo[j].eid;
                                        var insert_type = "event";
				} else if (resultName == "page") {
					var insert_id = insertInfo[j].page_id;
                                        var insert_type = "page";
				}

				var insert_agg_facebook = new agg_facebook({facebook_id: insert_id, type: insert_type});
				insert_agg_facebook.save();
			}
		} else if (!resultName) {   // Special case for Facebook Graph API search queries
			var insertInfo         =  returnInfo.data;
                        var insertInfo_length  =  insertInfo.length;
			
			for (j = 0; j < insertInfo_length; j++) {
				var insert_id = insertInfo[j].id;

				if (insertInfo[j]["start_time"]) {   // If it's an event
					var insert_type = "event";
				} else {   // If it's a page
					var insert_type = "page";
				}

				var insert_agg_facebook = new agg_facebook({facebook_id: insert_id, type: insert_type});
                                insert_agg_facebook.save();
			}
		}
	}

    
  }




var mongoose = require('mongoose'), db = mongoose.createConnection('localhost', 'test');
db.on('error', console.error.bind(console, 'connection error:'));

db.once('open', function () {

  // Aggregation Functions
    // Aggregate pages
      // agg_from_events_venues_creators();    // Gets pages from events' venues and creators
      // agg_from_search_pages();              // Gets pages from our search queries
      // agg_from_users_likes();               // Gets pages from users' likes
      // agg_from_pages_likes();               // Gets pages from pages' likes

    // Aggregate events
      // agg_from_search_events();             // Gets events from our search queries
      // agg_from_users_events();              // Gets events users are invited to
       agg_from_pages_events();              // Gets events posted by pages
});
