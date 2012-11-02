var bubbles = {
    night_life:{name:'night life', slug: 'night_life'},
    campus_events:{name:'campus events', slug: 'campus_events'},
    student_deals:{name:'student deals', slug: 'student_deals'},
    concerts:{name:'concerts', slug: 'concerts'},
    arts_music:{name:'arts & music', slug: 'arts_music'},
    food:{name:'food', slug: 'food'},
    greek_life:{name:'greek life', slug: 'greek_life'},
    sports:{name:'sports', slug: 'sports'}
};

exports.index = function(req, res){
  res.render('index', { title: 'Emory Bubble', bubbles_list: bubbles })
};

// handler for form submitted from homepage
exports.index_post_handler = function(req, res){
  // if the username is not submitted, give it a default of "Anonymous"
    username = req.body.username || 'Anonymous';
  // redirect the user to homepage
    res.redirect('/bubbles');
};





// handler for displaying list of bubbles
exports.bubbles = function(req, res) {
    res.render('bubbles', { title: 'Emory Bubble - Select a Bubble', bubbles_list: bubbles });
};

// handler for displaying individual bubble
exports.bubble = function(req, res) {
    var name = bubbles[req.params.id].name;
    var slug = bubbles[req.params.id].slug;
    res.render('bubble', { title: 'Emory Bubble - ' + name, bubbles_list: bubbles, name: name, slug: slug });
};
