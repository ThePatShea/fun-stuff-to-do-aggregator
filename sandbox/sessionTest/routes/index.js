
/*
 * GET home page.
 */

exports.index = function(req, res){
  res.render('index', { title: 'Emory Bubble' })
};

// handler for form submitted from homepage
exports.index_post_handler = function(req, res){
  // if the username is not submitted, give it a default of "Anonymous"
    username = req.body.username || 'Anonymous';
  // store the username as a session variable
    req.session.username = username;
  // redirect the user to homepage
    res.redirect('/');
};
