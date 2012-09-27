<?php include_once("../../php/background.php"); ?>

<?php
	
	// Takes information from agg_students and adds it to relevant tables
		function students_to_tables()
		{
			students_to_connections();
			students_to_queue();
		}
	
	
		// Takes information from agg_students and adds it to users_connections
			function students_to_connections()
			{
				$emoryUsers = generateQueryArray("SELECT * FROM agg_students");
												
				foreach($emoryUsers as $userID)
				{
					mysql_query("INSERT INTO users_connections (accountFacebookID, type, connection_facebookID	) VALUES ('".$userID["accountFacebookID"]."', 'College', '".$userID["schoolFacebookID"]."')");
				}
			}
		
		
		// Takes information from agg_students and adds it to users_connections
			function students_to_queue()
			{
				$emoryUsers = generateQueryArray("SELECT * FROM agg_students");
				
				foreach($emoryUsers as $userID)
				{
					add_to_queue($userID["accountFacebookID"],"user");
				}
			}
	
	students_to_tables();	
	
?>