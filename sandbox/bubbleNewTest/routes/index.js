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


// handler for displaying individual bubble
exports.bubble = function(req, res) {
    if (bubbles[req.params.id])
      var selected_bubble = bubbles[req.params.id];
    else
      var selected_bubble = bubbles['night_life'];

    var name = selected_bubble.name;
    var slug = selected_bubble.slug;

    res.render('bubble', { title: 'Emory Bubble - ' + name, bubbles_list: bubbles, name: name, slug: slug });
};
