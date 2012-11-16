// Base mongoDB functions
	var mongoose = require('mongoose'), db = mongoose.createConnection('localhost', 'test');
	db.on('error', console.error.bind(console, 'connection error:'));


	exports.db_open = function(callback) {
		db.once('open', callback);
	}	


	var get_schema = exports.get_schema = function(schema, callback) {
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

	exports.get_user_array = function(callback)
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

	exports.get_default_access_token = function(callback) {
		get_schema("access_tokens", function (mongo_model) {
                        mongo_model.findOne({ },"access_token",function (err, mongo_model) {
				if (err) { } // TODO handle err

				var default_access_token = mongo_model.access_token;
				callback(default_access_token);
			 });
                });
	}

	exports.get_id_list = function(id_type, callback)
	{
		get_schema("agg_facebook", function (mongo_model) {
			mongo_model.find({ type: id_type },function (err, mongo_model) {
				if (err) { } // TODO handle err

				var page_object = mongo_model;
				var page_object_length = page_object.length;

				var page_list = new Array();
				page_list[0] = "";
				var a = 0;

				for (i = 0; i < page_object_length; i++) {
					if (i != 0  &&  i%1000 == 0) {
						a++;
						page_list[a] = "";
					}

					if (i%1000 != 0)
						page_list[a] += ",";

					page_list[a] += page_object[i].facebook_id;
				}

				callback(page_list);
			});
		});
	}


  exports.store_facebook_info = function(returnInfo) {
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
                        var insertInfo = returnInfo.data[i];
			var insert_id = insertInfo.id;

			if (insertInfo["start_time"]) {   // If it's an event
				var insert_type = "event";
			} else {   // If it's a page
				var insert_type = "page";
			}

			var insert_agg_facebook = new mongo_model({facebook_id: insert_id, type: insert_type});
			insert_agg_facebook.save();
                }
        }
    });

  }

