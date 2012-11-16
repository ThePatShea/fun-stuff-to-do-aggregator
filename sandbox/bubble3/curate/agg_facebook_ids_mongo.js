	// Database functions
		function get_schema(schema, callback) {
			if (schema == "agg_facebook") {
				var mongo_schema = new mongoose.Schema({
					facebook_id: { type: Number, index: {unique: true}},
					type: String
				}, { collection: 'agg_facebook' });
			} else if (schema == "access_tokens") {
				var mongo_schema = new mongoose.Schema({
                                        uid: { type: Number, index: {unique: true}},
                                        access_token: { type: String, index: {unique: true}}
                                }, { collection: 'access_tokens' });
			}
	
			var mongo_model = db.model(schema, mongo_schema);
		
			callback(mongo_model);
		}

		function get_user_array(callback)
		{
			get_schema("access_tokens", function (mongo_model) {
				mongo_model.find({ },function (err, mongo_model) {
					if (err) { } // TODO handle err
					
					var user_object = mongo_model;
					var user_object_length = user_object.length;

					var users = new Array();

					for (i = 0; i < user_object_length; i++) {
						users[i] = new Array();
						users[i]["facebook_user_id"] = user_object[i].uid;
						users[i]["access_token"] = user_object[i].access_token;
					}
	
					callback(users);
				});
			});
		}
	
		function get_id_list(id_type, callback)
		{
			get_schema("agg_facebook", function (mongo_model) {
				mongo_model.find({ type: id_type },function (err, mongo_model) {
					if (err) { } // TODO handle err

					var page_object = mongo_model;
					var page_object_length = page_object.length;

					var page_list = "";

					for (i = 0; i < page_object_length; i++) {
						if (i != 0)
							page_list += ",";

						page_list += page_object[i].facebook_id;
					}

					callback(page_list);
				});
			});
		}
	
	// Facebook base functions
		var default_access_token = "AAADH49iOK2kBABc5T2CuUwJkLrghXRPHFpqafW3CLZBnYU2FX1pYVKm6OHoXJHpZCZCToz6tta392tRP4o7xpymI2Ov8AQzDhFzw1iZAlOCzTYHN6ZBrr";

		function get_from_facebook(query, access_token)
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
			
			get_user_array(function(users) {
				var users_length  =  users.length;
				
				for (i = 0; i < users_length; i++)
				{
					get_from_facebook("fql?q={"+query+" WHERE uid IN (SELECT uid2 FROM friend WHERE uid1="+users[i]["facebook_user_id"]+") OR uid="+users[i]["facebook_user_id"]+end_parens+"'}",users[i]["access_token"]);
				}
			});
		}
	
	
	// Aggregate Pages	
		function agg_from_events_venues_creators() {
			get_id_list("event", function(page_list){
				get_from_facebook("fql?q={'page':'SELECT page_id FROM page WHERE page_id IN (SELECT venue.id,creator FROM event WHERE eid IN ("+page_list+"))'}"); 
			});
		}

		function agg_from_pages_likes() {
			get_id_list("page", function(page_list){
				get_from_facebook("fql?q={'page':'SELECT page_id FROM page_fan WHERE uid IN ("+page_list+")'}"); 
			});
		}
		function agg_from_users_likes() { agg_from_users("'page':'SELECT page_id FROM page_fan"); }

		function agg_from_search_pages()
		{
			get_from_facebook("search?q=%20&type=place&fields=id&center=33.755,-84.39&distance=10000&limit=5000");
			get_from_facebook("search?q=Georgia&fields=id&type=place&limit=5000");
			get_from_facebook("search?q=Atlanta&fields=id&type=place&limit=5000");
			get_from_facebook("search?q=Decatur&fields=id&type=place&limit=5000");
			get_from_facebook("search?q=Emory&fields=id&type=place&limit=5000");
			get_from_facebook("search?q=GA&fields=id&type=place&limit=5000");
			
			get_from_facebook("search?q=Georgia&fields=id&type=page&limit=5000");
			get_from_facebook("search?q=Atlanta&fields=id&type=page&limit=5000");
			get_from_facebook("search?q=Decatur&fields=id&type=page&limit=5000");
			get_from_facebook("search?q=Emory&fields=id&type=page&limit=5000");
			get_from_facebook("search?q=GA&fields=id&type=page&limit=5000");
		}
	
	
	// Aggregate Events
		function agg_from_pages_events() {
			get_id_list("page", function(page_list){
				get_from_facebook("fql?q={'event':'SELECT eid,start_time,end_time FROM event WHERE eid IN (SELECT eid FROM event_member WHERE uid IN ("+page_list+"))'}");
			}); 
		}

		function agg_from_users_events() { agg_from_users("'event':'SELECT eid,start_time,end_time FROM event WHERE eid IN (SELECT eid FROM event_member",")"); }

		function agg_from_search_events()
		{
			get_from_facebook("search?q=Georgia%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			get_from_facebook("search?q=Atlanta%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			get_from_facebook("search?q=Decatur%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			get_from_facebook("search?q=Emory%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
			get_from_facebook("search?q=GA%20&type=event&fields=id,start_time,end_time&center=33.755,-84.39&distance=10000&limit=5000");
	
			get_from_facebook("search?q=Georgia&fields=id,start_time,end_time&type=event&limit=5000");
			get_from_facebook("search?q=Atlanta&fields=id,start_time,end_time&type=event&limit=5000");
			get_from_facebook("search?q=Decatur&fields=id,start_time,end_time&type=event&limit=5000");
			get_from_facebook("search?q=Emory&fields=id,start_time,end_time&type=event&limit=5000");
			get_from_facebook("search?q=GA&fields=id,start_time,end_time&type=event&limit=5000");
		}
	
	



  function store_facebook_info(returnInfo) {
    get_schema("agg_facebook", function (mongo_model) {
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

				var insert_agg_facebook = new mongo_model({facebook_id: insert_id, type: insert_type});
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

				var insert_agg_facebook = new mongo_model({facebook_id: insert_id, type: insert_type});
                                insert_agg_facebook.save();
			}
		}
	}
    });
    
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
      // agg_from_pages_events();              // Gets events posted by pages
});
