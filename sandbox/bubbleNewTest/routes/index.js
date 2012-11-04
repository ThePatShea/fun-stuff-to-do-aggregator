var bubbles = {
    night_life:{
      name:'night life',
      slug: 'night_life',
      events: {
        1: {
          bubble_id: '1',
          slug: 'big_party_at_opera',
          name: 'big party at opera',
          location: 'opera nightclub',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am',
          description: 'College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people. College Night is Wednesdays ultimate destination for the citys most beautiful party people.',
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash4/276429_103881343106322_1983802727_n.jpg'
        },
        2: {
          bubble_id: '2',
          slug: 'butch_clancy_terminal_west',
          name: 'butch clancy @ terminal west',
          location: 'terminal west',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am', 
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc6/188168_418693628185481_517348357_n.jpg'
        },
        3: {
          bubble_id: '3',
          slug: 'back_to_school_bash',
          name: 'back to school bash',
          location: 'mansion elan',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am',
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc6/276674_124771344342841_1247972548_n.jpg'
        },
        4: {
          bubble_id: '4',
          slug: 'college_night_with_the_bros',
          name: 'college night with the bros',
          location: 'flip flops',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am',
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc6/188148_123889957765084_764900719_n.jpg'
        },
        5: {
          bubble_id: '5',
          slug: 'big_party_at_opera',
          name: 'big party at opera',
          location: 'opera nightclub',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am',
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash4/276429_103881343106322_1983802727_n.jpg'
        },
        6: {
          bubble_id: '6',
          slug: 'butch_clancy_terminal_west',
          name: 'butch clancy @ terminal west',
          location: 'terminal west',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am',
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc6/188168_418693628185481_517348357_n.jpg'
        },
        7: {
          bubble_id: '7',
          slug: 'back_to_school_bash',
          name: 'back to school bash',
          location: 'mansion elan',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am',
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc6/276674_124771344342841_1247972548_n.jpg'
        },
        8: {
          bubble_id: '8',
          slug: 'college_night_with_the_bros',
          name: 'college night with the bros',
          location: 'flip flops',
          full_time: 'thursday, november 1st | 10:00pm - 1:00am',
          pic_big: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc6/188148_123889957765084_764900719_n.jpg'
        },
      }
    },
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
      var selected_bubble = bubbles[req.params.bubble_slug];
    else
      var selected_bubble = bubbles['night_life'];

    var name = selected_bubble.name;
    var slug = selected_bubble.slug;
    
    var selected_post = "";

    res.render('bubble', { title: 'Emory Bubble - ' + name, bubbles_list: bubbles, name: name, slug: slug, post: selected_post});
};

exports.post = function(req, res) {
    var selected_bubble = bubbles[req.params.bubble_slug];
    var selected_post = bubbles[req.params.bubble_slug].events[req.params.post_id];

    var name = selected_bubble.name;
    var slug = selected_bubble.slug;

    res.render('bubble', { title: 'Emory Bubble - ' + name, bubbles_list: bubbles, name: name, slug: slug, post: selected_post});
};
