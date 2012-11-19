var base_mongo = require("../../curate/javascripts/base_mongo.js");

			exports.bubble = function(req, res) {
base_mongo.db_open(function() {
	base_mongo.get_schema("bubble", function (mongo_model) {
		mongo_model.find({ }).exec(function (err, mongo_model) {
			var bubbles = mongo_model;

			  /*  var input_slug = req.params.bubble_slug;
				console.log(input_slug) //TESTING
			    if (bubbles[input_slug])
			      var selected_bubble = bubbles[input_slug];
			    else
			      var selected_bubble = bubbles['night_life'];
			    
			    if (selected_bubble.events[req.params.post_id])
			      var selected_post = selected_bubble.events[req.params.post_id];
			    else
			      var selected_post = '';

			    var name = selected_bubble.name;
			    var slug = selected_bubble.slug;

			    if (selected_post.name)
			      var display_name = ' - ' + selected_post.name;
			    else
			      var display_name = '';
*/
			    res.render('bubble', { title: 'Emory Bubble' });
		});
	});
});
			};
