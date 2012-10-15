var mongoose = require('mongoose'), db = mongoose.createConnection('localhost', 'test');
db.on('error', console.error.bind(console, 'connection error:'));

db.once('open', function () {

	function syncFacebook(fql_query, access_token)
	{	
		var https = require('https');
		
		https.get("https://graph.facebook.com/fql?q={"+fql_query+"}&access_token="+access_token, function(res) {
		
			var returnData = "";
			
			res.on("data", function(chunk) {
				returnData += chunk;
			});
			
			res.on("end", function() {
				var returnInfo         =  JSON.parse(returnData);
				var returnInfo_length  =  returnInfo.data.length;

				for (i = 0; i < returnInfo_length; i++)
				{
					var resultName =  returnInfo.data[i].name;
					
					if (resultName == "user" || resultName == "event" || resultName == "page")
					{
						var insertInfo         =  returnInfo.data[i].fql_result_set;
						var insertInfo_length  =  insertInfo.length;
						
						for (j = 0; j < insertInfo_length; j++)
							db.collection(resultName).update({uid: insertInfo[j].uid}, insertInfo[j], {upsert: true});
					}
				}

				console.log('finished');
			});
			
		}).on('error', function(e) {
			console.log("Got error: " + e.message);
		});
	}



	syncFacebook(
		"'users_ids':'SELECT+uid+FROM+user+WHERE+uid+IN+(SELECT+uid2+FROM+friend+WHERE+uid1=681701524)+OR+uid=681701524','event':'SELECT+eid+FROM+event_member+WHERE+uid+IN+(SELECT+uid+FROM+%23users_ids)','page':'SELECT+page_id+FROM+page_fan+WHERE+uid+IN+(SELECT+uid+FROM+%23users_ids)','user':'SELECT+pic_square,first_name,last_name,email,uid+FROM+user+WHERE+uid=681701524'",
		"AAADH49iOK2kBALIei28zWyqqeoZAE52GTSBXmpZC5weYxQT9LlHEDqPNF9vVpDle7XKT68ROcZATCFmrrFAKIBpb044J5aFZA1OvCEtGvAZDZD"
	);
});
