// Require base scripts
        var base_facebook = require("./javascripts/base_facebook.js");
        var base_mongo = require("./javascripts/base_mongo.js");


// Sync Facebook attributes scripts
	function sync_pages() { base_facebook.facebook_query_loop("page","page","SELECT page_id,name,description,categories,pic_square,pic_big,pic_cover,type,mission,products,location,phone,username,about,fan_count,hours,parking FROM page WHERE page_id IN"); }


// Run the sync function
	sync_pages();
