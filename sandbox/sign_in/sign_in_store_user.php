<?php	

	include_once("../../back_end/background/background.php");
	include_once("../../back_end/login/functions/login_all.php");
	
	//Make a new function called store_unregistered_user that just saves the info for accounts and users_info of an unregistered user ID. This new function belongs in cron_general.php, because that's where it will be used.

	// Takes in a user's ID and adds all information to the database
	//if their name is in accounts and/or users_info and they are Unregistered, it will update their info and register them
		function store_registered_user($userID)
		{
			//get access token
				$accessTokenArray	=	generateQueryArray("SELECT access_token FROM access_tokens WHERE accountFacebookID = '$userID' LIMIT 1");
				$accessToken		=	$accessTokenArray[0]["access_token"];
				

					
			//batch call to get user's info
		    	$batched_request    =	'{"method":"GET","relative_url":"'.$userID.'"},';
		    	$batched_request   .=	'{"method":"GET","relative_url":"'.$userID.'/likes"},';
		    	$batched_request   .=	'{"method":"GET","relative_url":"'.$userID.'?fields=picture"},';
		    	$batched_request   .=	'{"method":"GET","relative_url":"'.$userID.'/groups"},';
		    	$batched_request   .=	'{"method":"GET","relative_url":"'.$userID.'/friends"},';
		    	
		    					
								
			//Converts JSON returned from the batch request into an array
				$userJSON = cURL_standard("https://graph.facebook.com/?batch=[$batched_request]&access_token=".$accessToken."&method=post");
				$tempArrayJSON		=	json_decode($userJSON, true);

				$userInfo 		= json_decode($tempArrayJSON[0]["body"], true);
				$userLikes 		= json_decode($tempArrayJSON[1]["body"], true);
				$userPic 		= json_decode($tempArrayJSON[2]["body"], true);
				$userGroups 	= json_decode($tempArrayJSON[3]["body"], true);
				$userFriends 	= json_decode($tempArrayJSON[4]["body"], true);
				print_r($userInfo);
				print_r($userLikes);
				print_r($userPic);
				print_r($userGroups);
				print_r($userFriends);
				
				
				
			
			
			//add users info to accounts first
				$facebookID = addSlashes($userInfo["id"]);
				$name 		= addSlashes($userInfo["name"]);
				$email		= addSlashes($userInfo["email"]);
				$pic_square	= addSlashes($userPic["picture"]);
				$pic_big 	= $pic_square;
					if(strlen($pic_square) != 0){ 
						$pic_big[strlen($pic_square)-5] = 'n'; }
				$pic_big	= addSlashes($pic_big);
				$username	= addSlashes($userInfo["username"]);
				
				
				//see if they're already in accounts
				$query = generateQueryArray("
							SELECT count(*)
							FROM accounts
							WHERE accountFacebookID = '$facebookID'
						");
				
				if($query[0]["count(*)"] > 0){
					//update their info
					$query = mysql_query
			    	("
			    	    UPDATE accounts 
			    	    SET name = '$name', type = 'user', email = '$email', pic_square = '$pic_square', pic_big = '$pic_big', accountFacebookUsername = '$username'
			    	    WHERE accountFacebookID = '$facebookID'
			    	");
				}
				
				else{
					//add their info
					$query = mysql_query
			    	("
			    	    INSERT INTO 
			    	    accounts 
			    	    (name, type, email, pic_square, pic_big, accountFacebookUsername, accountFacebookID)
			    	    VALUES 
			    	    ('$name', 'user', '$email', '$pic_square', '$pic_big', '$username', '$facebookID')
			    	");

				}
					
				
				
			//add users info to users_info
				$first_name 	= addSlashes($userInfo["first_name"]);
				$middle_name 	= addSlashes($userInfo["middle_name"]);
				$last_name 		= addSlashes($userInfo["last_name"]);
				$gender 		= addSlashes($userInfo["gender"]);
				
				
				//see if they're already in accounts
				$query = generateQueryArray("
							SELECT count(*)
							FROM users_info
							WHERE accountFacebookID = '$facebookID'
						");
				
				if($query[0]["count(*)"] > 0){
					//update their info
					$query = mysql_query
			    	("
			    	    UPDATE users_info 
			    	    SET accountFacebookID = '$facebookID', first_name = '$first_name', middle_name = '$middle_name', last_name = '$last_name', gender = '$gender', registered = 1
			    	    WHERE accountFacebookID = '$facebookID'
			    	");
			    }
							
				else{
					//add them to the database
					$query = mysql_query
			    	("
			    	    INSERT INTO 
			    	    users_info 
			    	    (accountFacebookID, first_name, middle_name, last_name, gender, registered)
			    	    VALUES 
			    	    ('$facebookID', '$first_name', '$middle_name', '$last_name', '$gender', 1)
			    	");	
			    }
			
			    
			    
			//add users likes to likes table
			//if the like is a bubble, subscribe them to that bubble
				$numLikes = count($userLikes["data"]);
				
				for($l = 0; $l < $numLikes; $l++){
					$objectFacebookID = addSlashes($userLikes["data"][$l]["id"]);
					$createdTime = addSlashes(strtotime($userLikes["data"][$l]["created_time"]));
					
					$query = mysql_query
			    	("
			    	    INSERT INTO 
			    	    likes 
			    	    (accountFacebookID, objectFacebookID, created_time)
			    	    VALUES 
			    	    ('$facebookID', '$objectFacebookID', '$createdTime')
			    	");
			    	
			    	subscribe_to_bubbles_with_facebookID($facebookID, $objectFacebookID);
					
					
				}
				
				
			//add users education info to users_connections
				$numEducations = count($userInfo["education"]);
				
				for($e = 0; $e < $numEducations; $e++){
				
					$schoolID	= addslashes($userInfo["education"][$e]["school"]["id"]);
					$schoolName	= addslashes($userInfo["education"][$e]["school"]["name"]);
					$year		= addslashes($userInfo["education"][$e]["year"]["name"]);
					$type		= addslashes($userInfo["education"][$e]["type"]);
					
					$query = mysql_query
			    		("
			    	    	INSERT INTO 
			    	    	users_connections 
			    	    	(accountFacebookID, type, connection_name, connection_facebookID)
			    	    	VALUES 
			    	    	('$facebookID', '$type', '$schoolName', '$schoolID')
			    		");
			    		
			    	//subscribe to any existing bubbles with the same Facebook id
			    	subscribe_to_bubbles_with_facebookID($facebookID, $schoolID);
			    	
			    	
			    	//get concentrations
			    	$numConcentrations = count($userInfo["education"][$e]["concentration"]);
			    	for($c = 0; $c < $numConcentrations; $c++){
			    		$concentrationID = addSlashes($userInfo["education"][$e]["concentration"][$c]["id"]);
			    		$concentrationName = addSlashes($userInfo["education"][$e]["concentration"][$c]["name"]);
			    		
			    		$query = mysql_query
			    		("
			    	    	INSERT INTO 
			    	    	users_connections 
			    	    	(accountFacebookID, type, connection_name, connection_facebookID)
			    	    	VALUES 
			    	    	('$facebookID', 'Major', '$concentrationName', '$concentrationID')
			    		");
			    		
			    		//subscribe to any existing bubbles with the same Facebook id
			    		subscribe_to_bubbles_with_facebookID($facebookID, $concentrationID);
			    	
			    	}
				}
				
				
				
			//add users friends list
				$numFriends = count($userFriends["data"]);
				
				for($f = 0; $f < $numFriends; $f++){
					$friendName = 	addSlashes($userFriends["data"][$f]["name"]);
					$friendID = 	addSlashes($userFriends["data"][$f]["id"]);
					
					$query = mysql_query
			    		("
			    	    	INSERT INTO 
			    	    	users_connections 
			    	    	(accountFacebookID, type, connection_name, connection_facebookID)
			    	    	VALUES 
			    	    	('$facebookID', 'Friend', '$friendName', '$friendID')
			    		");
				
				
				}
				
				
				
			//add users groups
				$numGroups = count($userGroups["data"]);
				
				for($g = 0; $g < $numGroups; $g++){
					$groupName = 	addSlashes($userGroups["data"][$g]["name"]);
					$groupID = 		addSlashes($userGroups["data"][$g]["id"]);
					
					$query = mysql_query
			    		("
			    	    	INSERT INTO 
			    	    	users_connections 
			    	    	(accountFacebookID, type, connection_name, connection_facebookID)
			    	    	VALUES 
			    	    	('$facebookID', 'Group', '$groupName', '$groupID')
			    		");

				}
				
				
				
				
				
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
						
		
		
		store_registered_user("681701524");
		//subscribe_to_bubbles_with_bubbleID('681701524', '444444444')


?>