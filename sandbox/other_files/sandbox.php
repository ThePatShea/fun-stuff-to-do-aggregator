<?php
	include_once("../../php/background.php");
	
	$campusEvents = generateQueryArray_flat("SELECT bubbleID FROM tags WHERE tag = 'b1e09bc2-03ff-11e2-92b4-12313b074e30'");
	
	print_r($campusEvents);

	for ($i = 0; $i < count($campusEvents); $i++)
	{
		mysql_query
		("
			INSERT INTO posts_trending
			(bubbleID, subjectID, score)
			VALUES
			('".$campusEvents[$i]."', 'general', 10
		");
	}

?>
