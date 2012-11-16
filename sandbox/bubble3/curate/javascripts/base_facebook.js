// Require base scripts
	var base_mongo = require("./base_mongo.js");

// Base Facebook functions
	var default_access_token = "AAADH49iOK2kBAHooMaZACcmuFK4PawNV14kqcYo5fGrIug7yzAoNevrIA4aO0ZAVSt1BcskxFKRbjwZAyLBp09sz0DPPbe5lJBxE1kqjNG95jVtrXeC";

	var get_from_facebook = exports.get_from_facebook = function(query, access_token)
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
				base_mongo.store_facebook_info(returnInfo);
			});

		}).on('error', function(e) {
			console.log("Got error: " + e.message);
		});
	}


	exports.agg_from_users = function(query, end_parens)
	{
		if(typeof(end_parens) === 'undefined') end_parens = "";

		base_mongo.get_user_array(function(users) {
			var users_length  =  users.length;

			for (i = 0; i < users_length; i++) {
				get_from_facebook("fql?q={"+query+" WHERE uid IN (SELECT uid2 FROM friend WHERE uid1="+users[i]["facebook_user_id"]+") OR uid="+users[i]["facebook_user_id"]+end_parens+"'}",users[i]["access_token"]);
			}
		});
	}
