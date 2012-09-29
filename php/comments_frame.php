<?php

	$bubbleID_post = $_GET["bubbleID_post"];


	echo "
	<div style='margin: 0 0 0 -8px;'>
		<div id='fb-root'></div><script src='http://connect.facebook.net/en_US/all.js#xfbml=1'></script><fb:comments href='http://emorybubble.com/$bubbleID_post' num_posts='3' width='250' data-colorscheme='dark'></fb:comments>
	</div>
		";

?>