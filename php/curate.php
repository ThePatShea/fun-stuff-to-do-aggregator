<?php include_once("background.php") ?>

<?php
		
	function curate_all(){
		curate_tags();
		curate_trending();
	}
	
	function curate_tags(){		
		curate_tags_geographic();
		curate_tags_interest();
		curate_tags_school();
	}
	
	function curate_tags_geographic(){
		curate_tags_geographic_events();
		curate_tags_geographic_pages();
	}
	
	function curate_tags_geographic_events(){
	
		$currentTime = time();
	
		$eventsArray = generateQueryArray
			("
				SELECT accountFacebookID_venue, bubbleID, location, postFacebookID
				FROM events_info
				WHERE end_time > $currentTime
			");
		
		$numEvents = count($eventsArray);
		for ($i=0; $i < $numEvents; $i++)
		{
			$currentID = $eventsArray[$i]["accountFacebookID_venue"];
			if($currentID=="")
			{
			
				$currentID = generateQuery_singleVar
				("
				    SELECT	accountFacebookID
				    FROM	posts
				    WHERE	bubbleID = '".$eventsArray[$i]['bubbleID']."'
				    LIMIT	1
				");
			
			}
			
		$currentInfo = generateQueryArray
			("
				SELECT city, state
				FROM pages_info
				WHERE accountFacebookID = '$currentID'
				LIMIT 1
			");
			tagCityState($eventsArray[$i]["bubbleID"], $currentInfo[0]);
		}
	}
	
	function tagCityState($bubbleIDAdding, $cityStateArray){
		if ($cityStateArray["city"]!=""){
		mysql_query
		("
			INSERT INTO tags_list (bubbleID,tag, type)
			VALUES (uuid(), '".$cityStateArray['city']."', 'geographic')
		");
		$cBID = generateQuery_singleVar("SELECT bubbleID FROM tags_list WHERE tag = '".addslashes($cityStateArray['city'])."'");
		tagBID($bubbleIDAdding, $cBID);
		}
		if ($cityStateArray["state"]!=""){
		mysql_query
		("
			INSERT INTO tags_list (bubbleID,tag, type)
			VALUES (uuid(), '".$cityStateArray['state']."', 'geographic')
		");
		$sBID = generateQuery_singleVar("SELECT bubbleID FROM tags_list WHERE tag = '".addslashes($cityStateArray['state'])."'");
		tagBID($bubbleIDAdding, $sBID);
		}
	}
	function tagBID($bubbleIDToAddTo, $tagToAdd){
	
		mysql_query
		("
			INSERT INTO tags (bubbleID,tag)
			VALUES ('$bubbleIDToAddTo', '$tagToAdd')
		");
		
		echo "<br>----------<br>";
		echo "$bubbleIDToAddTo got the tag $tagToAdd";
		echo "<br>----------<br>";
	
	}
	
	function untagBID($bubbleIDToRemove, $tagToRemove){
		mysql_query
	   	    ("
	   	        DELETE FROM	tags
	   	        WHERE	bubbleID= '$bubbleIDToRemove'
	   	        AND		tag		= '$tagToRemove'
	   	    ");
	}
	
	function curate_tags_geographic_pages(){
	
		$pagesArray = generateQueryArray
			("
				SELECT city, state, accountFacebookID
				FROM pages_info
			");
	
		$numPages = count($pagesArray);
		for ($i=0; $i < $numPages; $i++)
		{
			tagCityState($pagesArray[$i]["accountFacebookID"], $pagesArray[$i]);
		}
	}
	
	



	
	function curate_tags_account(){
	
		$currentTime = time();
	
		curate_tags_account_users();
		curate_tags_account_pages();
	
		$organizationsArray = generateQueryArray
			("
				SELECT name, accountFacebookID
				FROM accounts
			");
		
		$organizationsArray = generateQueryArray
			("
				SELECT name, accountFacebookID
				FROM accounts
			");
			
		$organizationsList = generateQueryCommaList("SELECT accountFacebookID FROM accounts",0,"flat","false");
			
		$eventsByOrganizations = generateQueryArray
		("
		    SELECT posts.bubbleID, posts.accountFacebookID
		    FROM posts
		    LEFT JOIN events_info
		    ON posts.bubbleID = events_info.bubbleID
		    WHERE accountFacebookID IN ($organizationsList)
		    AND accountFacebookID != 0
		    AND end_time > $currentTime
		");
			
		$numEvents = count($eventsByOrganizations);
		for ($i=0; $i < $numEvents; $i++)
		{
			echo $eventsByOrganizations[$i]["bubbleID"]." and ".$eventsByOrganizations[$i]["accountFacebookID"]."</br>";
			tagBID($eventsByOrganizations[$i]["bubbleID"], $eventsByOrganizations[$i]["accountFacebookID"]);
		}
	}
	
		function curate_tags_account_users(){
		$colleges = generateQueryCommaList
			("
				SELECT bubbleID
				FROM places_current
			", 0, "flat", "true");
		$students = generateQueryCommaList
			("
				SELECT accountFacebookID
				FROM users_connections
				WHERE type = 'college'
				AND connection_bubbleID IN ($colleges)
			", 0, "flat", "true");
		$nameArray = generateQueryArray
			("
				SELECT first_name, middle_name, last_name, accountFacebookID
				FROM users_info
				WHERE first_name != ''
				AND last_name != ''
				AND accountFacebookID IN ($students)
			");
		
		$postList = generateQueryArray
			("
				SELECT name, description, bubbleID
				FROM posts
			");
		
		$numNames = count($nameArray);
		$numPosts = count($postList);
		
		for($i=0; $i<$numPosts; $i++)
		{
			for($j=0; $j<$numNames; $j++)
			{
				$numMentions = 0;
				$numMentions += stricount(	$postList[$i]["name"]			,	$nameArray[$j]["first_name"]." ".$nameArray[$j]["last_name"]	);
				$numMentions += stricount(	$postList[$i]["description"]	,	$nameArray[$j]["first_name"]." ".$nameArray[$j]["last_name"]	);
				if ($numMentions > 0)
				{
					tagBID($postList[$i]["bubbleID"], $nameArray[$j]["accountFacebookID"]);
					echo "Event called: ".$postList[$i]["name"]." with bubbleID: ".$postList[$i]["bubbleID"]." is being tagged with the accountFacebookID: ".$nameArray[$j]["accountFacebookID"]." for mentioning the name: ".$nameArray[$j]["first_name"]." ".$nameArray[$j]["last_name"].".</br>";
				}
			}
		}
	}
	
	function curate_tags_account_pages(){
		echo "</br></br>TEMPORARY curate_tags_account_pages() function declaration! Make sure to impliment the real one and delete this!!</br></br>";
		
		//
/*											ALL OF THIS (Except the stupid double commented out code)
											WILL BECOME THE PAGE TAGGING STUFF.
											ONLY generate_clean_page_names() FOR PAGES THAT HAVE BEEN DEEMED RELEVENT.
	function generate_clean_page_names(){
		$nameOfPagesArray = generateQueryArray
			("
				SELECT name
				FROM accounts
				WHERE type = 'page'
				OR type = 'group'
			");
		$removeWordsArray = generateQueryArray
			("
				SELECT remove_word
				FROM tags_remove_words
			");
		print_r($nameOfPagesArray);
		foreach($nameOfPagesArray as $pageName)
		{
			foreach($removeWordsArray as $wordToRemove){
				echo "Trying to remove word: ".$wordToRemove[0]." from: ".$pageName[0];
				$cleanName=str_ireplace($wordToRemove[0], "", $pageName[0]);
				echo " and resulted in: ".$cleanName.".</br>";
			}
		}
	}
	
	
	function curate_tags_account_pages(){
		generate_clean_page_names();
		
		/*
		
		$postList = generateQueryArray
			("
				SELECT name, description, bubbleID
				FROM posts
			");
		
		$numNames = count($nameArray);
		$numPosts = count($postList);
		
		for($i=0; $i<$numPosts; $i++)
		{
			for($j=0; $j<$numNames; $j++)
			{
				$numMentions = 0;
				$numMentions += stricount(	$postList[$i]["name"]			,	$nameArray[$j]["first_name"]." ".$nameArray[$j]["last_name"]	);
				$numMentions += stricount(	$postList[$i]["description"]	,	$nameArray[$j]["first_name"]." ".$nameArray[$j]["last_name"]	);
				if ($numMentions > 0)
				{
					tagBID($postList[$i]["bubbleID"], $nameArray[$j]["accountFacebookID"]);
					echo "Event called: ".$postList[$i]["name"]." with bubbleID: ".$postList[$i]["bubbleID"]." is being tagged with the accountFacebookID: ".$nameArray[$j]["accountFacebookID"]." for mentioning the name: ".$nameArray[$j]["first_name"]." ".$nameArray[$j]["last_name"].".</br>";
				}
			}
		}*/
	//}
	}	
		
	function curate_tags_interest(){
		curate_tags_interest_events();
		curate_tags_interest_pages();
		curate_tags_interest_deals();
		curate_tags_interest_pull_points(100);
	}
	
	function curate_tags_interest_events(){		
	
		$currentTime = time();
	
		$postListEvents = generateQueryArray
		("
			SELECT		posts.name, posts.description, posts.bubbleID, posts.accountFacebookID
			FROM		posts
			LEFT JOIN	events_info
			ON			posts.bubbleID  =	events_info.bubbleID
			WHERE		posts.type		=	'event'
			AND			end_time > $currentTime
		");
		
		tagAll("event",$postListEvents);
		$events_and_times = generateQueryArray
		("
			SELECT		bubbleID, start_time, end_time
			FROM		events_info
			WHERE		end_time > $currentTime
		");
		foreach ($events_and_times as $oneEvent){
			$start_time = getdate($oneEvent['start_time']);
			$end_time 	= getdate($oneEvent['end_time']);
		  if ($start_time['hours']>3 && $start_time['hours']<18 && $end_time['hours']<18 && $start_time['yday'] == $end_time['yday']){
		  	echo $oneEvent['bubbleID']." is not nightlife because it does not occur after 6PM or before 4AM.</br>";
		  	removeFromSQL(0, "8132730c-d028-11e1-b249-002590605566", $oneEvent['bubbleID']);
		  	untagBID("8132730c-d028-11e1-b249-002590605566", $oneEvent['bubbleID']);
		  }
		}
		
	}
	
	function curate_tags_interest_pages(){
		
		echo "</br> Now tagging pages: </br></br>";
		
		$postListVenues = generateQueryArray
	    ("
	    	SELECT		pages_info.accountFacebookID, pages_info.mission, pages_info.about, pages_info.description, pages_info.products, pages_info.type, accounts.name
	    	FROM		pages_info
	    	LEFT JOIN	accounts
	    	ON			pages_info.accountFacebookID = accounts.accountFacebookID
	    ");	
	    
		tagAll("page",$postListVenues);	
			
		$postListPages_categories = generateQueryArray
			("
		    	SELECT	category, accountFacebookID
		    	FROM	pages_categories
		    ");
		    
		$tagList = generateQueryArray
			("
				SELECT	word, points, tag
				FROM	tags_words
			");
			
		//print_r($postListPages_categories);
		//echo "</br></br>";
		//print_r($tagList);
		foreach ($postListPages_categories as $pageCategory){
			foreach($tagList as $tagWord){
				$matches = stricount(	$pageCategory["category"]	,	$tagWord["word"]);
				//echo $matches." ".$pageCategory["name"]." ".$tagWord["word"]."</br>";
				if ($matches*$tagWord["points"]>75){
					tagBID($pageCategory["accountFacebookID"], $tagWord["tag"]);
				}
				//echo "</br></br>";
			}
		}
	}
	
	function curate_tags_interest_deals(){
		$postListDeals = generateQueryArray
		("
			SELECT	name, description, bubbleID
			FROM	posts
			WHERE	type			=	'deal'
		");
		
		if ($postListDeals == "")  return;
		
		if ($postListDeals != "")  tagAll("deal", $postListDeals);
		
		$postListDeals_categories = generateQueryArray
			("
		    	SELECT	name, bubbleID
		    	FROM	deals_categories
		    ");
		
		if ($postListDeals_categories == "")  return;
		
		$tagList = generateQueryArray
			("
				SELECT	word, points, tag
				FROM	tags_words
			");
		//print_r($postListDeals_categories);
		//echo "</br></br>";
		//print_r($tagList);
		
		if ($postListDeals_categories != "")
		{
			foreach ($postListDeals_categories as $dealCategory){
				foreach($tagList as $tagWord){
					$matches = stricount(	$dealCategory["name"]	,	$tagWord["word"]);
					//echo $matches." ".$dealCategory["name"]." ".$tagWord["word"]."</br>";
					if ($matches*$tagWord["points"]>75){
						tagBID($dealCategory["bubbleID"], $tagWord["tag"]);
						//echo $dealCategory["bubbleID"]." got tagged with ".$tagWord["tag"];
					}
					//echo "</br></br>";
					
					tagBID($dealCategory["bubbleID"], "8474f610-ed4f-11e1-bf61-aafbeaa37357"); // Gives the deal the tag "student deals" for the Student Deals Bubble
					tagBID($dealCategory["bubbleID"], "43bf8b06-d071-11e1-b249-002590605566"); // Gives the deal the tag "atlanta"					
				}
			}
		}
	}
	
	
	
	function curate_tags_interest_pull_points($tagPointThreshold){
		// The parameter $tagPointThreshold is the number of points something must EXCEED in order to be awarded the tag.
		$tagPointArray = generateQueryArray
			("
				SELECT bubbleID, points, tag
				FROM tags_points
			");
		$numTagPoints = count($tagPointArray);
		for ($i=0; $i < $numTagPoints; $i++)
		{
			if($tagPointArray[$i]["points"]>$tagPointThreshold)
			{
				tagBID($tagPointArray[$i]["bubbleID"], $tagPointArray[$i]["tag"]);
			}
			else
			{
				untagBID($tagPointArray[$i]["bubbleID"], $tagPointArray[$i]["tag"]);
			}
		}
	}
	
	
	function curate_tags_school(){
		echo "Start!";
		$school_threshold = 3;
		
		$colleges = generateQueryArray_flat
			("
				SELECT bubbleID
				FROM places_current
				WHERE type = 'school'
			");
		
		foreach($colleges as $cCollege)
		{
			$students = generateQueryCommaList
				("
					SELECT accountFacebookID
					FROM users_connections
					WHERE type = 'college'
					AND connection_bubbleID = '$cCollege'
				", 0, "flat", "true");
			
			$student_invites = generateQueryArray
				("
					SELECT 		events_invited.bubbleID 
					FROM 		events_invited
					LEFT JOIN	events_info
					ON			events_invited.bubbleID  = events_info.bubbleID
					WHERE		accountFacebookID IN ($students)
					AND			end_time > $currentTime
					ORDER BY	bubbleID
				");
			
			//print_r($student_invites);
			$first = true;
			if ($student_invites != "")
			{
				foreach($student_invites as $cInvite){
					//echo $cInvite[0]." with count of $c and previous invite of $pInvite</br>";
					if ($first) {$pInvite = $cInvite[0]; $first = false; $c=0;}
					if($pInvite == $cInvite[0]) $c++;
					else{
						if($c>$school_threshold)
						{
							tagBID($pInvite, $cCollege); echo "</br>$pInvite was tagged with the college $cCollege, and here's a <a HREF='http://www.facebook.com/$pInvite'>link</a>";
						}
						$c=0;
					}
					$pInvite = $cInvite[0];
				}
				if($c>$school_threshold)
				{ 
					tagBID($pInvite, $cCollege);
				} // Makes sure we don't forget to try to tag the last event
			}	
		}
	}
	
	function one_event_tag_school($incomingBubbleID, $incoming_threshold){
		//echo "Hello world</br>";
		$oneEventInvitesArray = generateQueryArray
			("
				SELECT	innerID, accountFacebookID, bubbleID
				FROM	events_invited
				WHERE	bubbleID = $incomingBubbleID
			");
		$numInvites = count($oneEventInvitesArray);
		//echo "</br></br>";
		
		for($p=0; $p < $numInvites; $p++){
			$currentUser = $oneEventInvitesArray[$p]["accountFacebookID"];
			$currentCollege = generateQueryArray
								("
							        SELECT	connection_facebookID
							        FROM	users_connections
							        WHERE	accountFacebookID = '$currentUser'
							        AND		type = 'College'
							    ");
			//echo "</br>".$currentUser." goes to ".$currentCollege[0]['connection_facebookID'];
			$collegeInvitesArray[$currentCollege[0]['connection_facebookID']] = $collegeInvitesArray[$currentCollege[0]['connection_facebookID']]+1;
		}
		//echo "</br></br>";
		//print_r($collegeInvitesArray);
		//echo "</br>";
		$numColleges = count($collegeInvitesArray);
		if($numColleges>0)
		foreach(array_keys($collegeInvitesArray) as $collegeID){
			if ($collegeInvitesArray[$collegeID] < $incoming_threshold || $collegeID=="")
				continue;
			$currentCollege = generateQueryArray
								("
							        SELECT	name
							        FROM	accounts
							        WHERE	accountFacebookID = '$collegeID'
							    ");
			echo $incomingBubbleID." should be tagged with the college: ".$currentCollege[0]['name']."</br>";
			tagBID($incomingBubbleID, $currentCollege[0]['name']);
		}
		echo "</br>----</br>";
	}
	
	
	function curate_trending(){
		curate_trending_general();
		curate_trending_deals();
	}
	
	function curate_trending_general(){
		curate_trending_general_pages();
		curate_trending_general_events();
	}
	
	function curate_trending_general_pages(){
		$relevent_tags = generateQueryCommaList
			("
				SELECT bubbleID
				FROM places_current
			", 0, "flat", "true");
		$relevent_pages = generateQueryCommaList
			("
				SELECT bubbleID
				FROM tags
				WHERE tag IN ($relevent_tags)
			", 0, "flat", "true");
	    $pages_info = generateQueryArray
	    	("
				SELECT	accountFacebookID, likes, mission, description, about, phone
	    		FROM	pages_info
	    		WHERE	accountFacebookID IN ($relevent_pages)
	    	");

	    foreach($pages_info as $currentPage){
		    $cID = $currentPage["accountFacebookID"];
		    
		    $score = $currentPage["likes"];
		    
		    if ($currentPage["mission"]!="") $score+=10;
		    if ($currentPage["description"]!="") $score+=10;
		    if ($currentPage["about"]!="") $score+=10;
		    if ($currentPage["phone"]!="") $score+=10;
		    
		    
	    	echo "</br>The page: $cID has ".$currentPage["likes"]." likes. </br>Mission: '".$currentPage["mission"]."' </br>Description: '".$currentPage["description"]."'</br>About: '".$currentPage["about"]."'</br>Phone number: '".$currentPage["phone"]."'.";
	    	echo "</br>And it got a score of: $score.</br>--------</br>";
		    
			mysql_query
				("
				    INSERT INTO posts_trending (bubbleID,subjectID, score)
				    VALUES (".$cID.", 'general', $score)
				");
			mysql_query
				("
				    UPDATE	posts_trending
				    SET		score = '$score'
				    WHERE	bubbleID = '$cID'
				    AND		subjectID = 'general'
				    LIMIT	1
				");
	    }
	}
	
	function curate_trending_general_events(){
		$relevent_tags = generateQueryCommaList
			("
				SELECT bubbleID
				FROM places_current
			", 0, "flat", "true");
		$relevent_events = generateQueryArray_flat
			("
				SELECT bubbleID
				FROM tags
				WHERE tag IN ($relevent_tags)
			");
			
		$aMult = 2;//4;
		$dMult = -1;//-1;
		$uMult = 1.5;//3;
		$nMult = 1;//2;
		$vMult = 1;
		$currentTime = time();
		//echo $currentTime."</br>";
		foreach($relevent_events as $currentEvent){
			$score = 0;
			$aCount = generateQueryArray
			("
				SELECT	*
				FROM	events_invited_count
				WHERE	bubbleID		=	$currentEvent
			");
			$eventInfo = generateQueryArray
			("
				SELECT	start_time, end_time, accountFacebookID_venue
				FROM	events_info
				WHERE	bubbleID		=	$currentEvent
			");
			if($eventInfo[0]['accountFacebookID_venue']!=""){
				$venueLikes = generateQuery_singleVar
	    		("
					SELECT	score
	    			FROM	posts_trending
	    			WHERE	bubbleID IN (".$eventInfo[0]['accountFacebookID_venue'].")
	    		");
	    		if 		($venueLikes<10) 	$vScore = 0;
	    		elseif	($venueLikes<100) 	$vScore = 100;
	    		elseif	($venueLikes<1000) 	$vScore = 200;
	    		elseif	($venueLikes<5000) 	$vScore = 300;
	    		elseif	($venueLikes<10000) $vScore = 400;
	    		else						$vScore = 500;
	    		echo "The event ".$currentEvent."'s venue had this many likes: ".$venueLikes." and therefore adds ".$vScore." points to the event score.</br>";
	    	}
	    	
	    	//echo "Event info!</br>";
	    	//print_r($eventInfo);
	    	//echo "Venue Info!:</br>";
	    	//print_r($venueInfo);
	    	//echo"</br></br>";
			
			$eventPic = generateQuery_singleVar
				("
				    SELECT	pic_big
				    FROM	posts
				    WHERE	bubbleID = $currentEvent
				    LIMIT	1
				");
			if($eventPic == "http://profile.ak.fbcdn.net/static-ak/rsrc.php/v2/yn/r/5uwzdFmIMKQ.png") //the event doesn't have a pic
				$score += -75;
			$score += $vMult*$vScore + $aMult*$aCount[0]["count_attending"] + $dMult*$aCount[0]["count_declined"] +
					 $uMult*$aCount[0]["count_unsure"] + $nMult*$aCount[0]["count_noreply"];

			// Uncomment this line VV if we want to limit events by number of total invitees. 
			//if (($aCount[0]["count_attending"] + $aCount[0]["count_declined"] + $aCount[0]["count_unsure"] + $aCount[0]["count_noreply"])<50) $score = -1;
			if($eventInfo[0]['start_time']!=0){
			echo "</br>Current time is: $currentTime";
			echo "</br>Start time is: ".$eventInfo[0]['start_time'];
			echo "</br>Before time closeness addition: $score";
			
			
			
			$t1 = abs($eventInfo[0]['start_time']-$currentTime);
			$t2 = sqrt(($t1)/10);
			$timeScore = 10000-(20*$t2);
			
			
			echo "</br>Getting a time closeness addition will add: $timeScore";
			$score += $timeScore;
			echo "</br>After time closeness multiplier: $score";
			$tempStime = getdate($eventInfo[0]['start_time']);
			echo "</br> The current time is: $currentTime and the event starts at ".$tempStime['mon']."/".$tempStime['mday']."/".$tempStime['year']." and ends at".$eventInfo[0]['end_time']." and got a time score of $timeScore</br>";}
			
			if ($eventInfo[0]['end_time']<$currentTime) $score = -2147483648; 							// Check to see if the event has passed.
			if ($eventInfo[0]['privacy']!='OPEN' && $eventInfo[0]['privacy']!='') $score = -2147483648;	// Check to see if the event isn't OPEN.
			
			//echo "$currentEvent got the score of $score and starts at ".$eventInfo[0]['start_time']."</br>";
			mysql_query
				("
				    INSERT INTO posts_trending (bubbleID,subjectID, score)
				    VALUES (".$currentEvent.", 'general', $score)
				");
			mysql_query
				("
				    UPDATE	posts_trending
				    SET		score = '$score'
				    WHERE	bubbleID = '$currentEvent'
				    AND		subjectID = 'general'
				    LIMIT	1
				");
			
			//print_r($aCount);
			
			//echo "</br></br>";
		}
	}
	
	function curate_trending_school(){
		
	}
	
	function curate_trending_user(){
		
	}
	
	function curate_trending_deals(){
		$postListDeals = generateQueryArray
		("
			SELECT	bubbleID, innerID
			FROM	posts
			WHERE	type			=	'deal'
		");
		print_r($postListDeals);
		if ($postListDeals != "")
		{
			foreach ($postListDeals as $aDeal){
				echo "</br> ".$aDeal['bubbleID']." deal ".$aDeal['innerID'];
				addTrending($aDeal['bubbleID'], "general", $aDeal['innerID']);
			}
		}
	}
	
	function addTrending($bubbleID, $subjectID, $score){
		mysql_query
			("
			    INSERT INTO posts_trending (bubbleID,subjectID, score)
			    VALUES ('".$bubbleID."', '".$subjectID."', ".$score.")
			");
		mysql_query
		    ("
		        UPDATE	posts_trending
		        SET		score = '$score'
		        WHERE	bubbleID = '$bubbleID'
		        AND		subjectID = '$subjectID'
		        LIMIT	1
		    ");
	}
	
	function addToSQL($pointValue, $key, $bID)
			{
				mysql_query
				    ("
				        INSERT INTO	tags_points
				        (	bubbleID, points, tag	)
				        VALUES
				        (
				        	'$bID',
				        	'$pointValue',
				        	'$key'
				        )
				    ");
			}
			
			
	function removeFromSQL($pointValue, $key, $bID)
	   {
	   	mysql_query
	   	    ("
	   	        DELETE FROM	tags_points
	   	        WHERE	bubbleID= '$bID'
	   	        AND		tag		= '$key'
	   	    ");
	   }
	
	function tagAll($type, $array){
			$tagList = generateQueryArray
			("
				SELECT	word, points, tag
				FROM	tags_words
			");
			
			$postList = $array;
			
			/*
				print_r($tagList[0]["word"]);
				echo "<br/><br/><br/>";
				print_r($postList[0]["description"]);
			*/
			

			$descriptionMult = 1;
			$nameMult = 2;
			$aboutMult = 1;
			$productsMult = 1;
			$missionMult = 1;
			$typeMult = 2;
			$postListCount = count($postList);
			for ($i = 0; $i < $postListCount; $i++)
			{
				if ($postList[$i]["accountFacebookID"] != "")
				{
					$authorTags = generateQueryArray_flat("SELECT tag FROM tags WHERE bubbleID = ".$postList[$i]["accountFacebookID"]);
					
					$count_authorTags = count($authorTags);
					for ($j = 0; $j < $count_authorTags; $j++)
					{
						tagBID($postList[$i]["bubbleID"], $authorTags[$j]);
					}
				}
				
				$tagListCount = count($tagList);
				for ($j = 0; $j < $tagListCount; $j++)
				{
					//Org: mission, about, description, products
					//Event: name, description
					$stringMatch[$i]["description"] = stricount(	$postList[$i]["description"]	,	$tagList[$j]["word"]	);
					$stringMatch[$i]["name"]		= stricount(	$postList[$i]["name"]			,	$tagList[$j]["word"]	);
					$stringMatch[$i]["mission"]		= stricount(	$postList[$i]["mission"]		,	$tagList[$j]["word"]	);
					$stringMatch[$i]["about"]		= stricount(	$postList[$i]["about"]			,	$tagList[$j]["word"]	);
					$stringMatch[$i]["products"]	= stricount(	$postList[$i]["products"]		,	$tagList[$j]["word"]	);
					$stringMatch[$i]["type"]		= stricount(	$postList[$i]["type"]			,	$tagList[$j]["word"]	);
					
					
					if ( $stringMatch[$i]["description"] != 0 || $stringMatch[$i]["name"] != 0 || $stringMatch[$i]["mission"] != 0 || $stringMatch[$i]["about"] != 0 || $stringMatch[$i]["products"] != 0 || $stringMatch[$i]["type"] != 0)
					{	
						//echo "stricount description: ".$stringMatch[$i]["description"]."<br/>";
						//echo "stricount name: ".$stringMatch[$i]["name"]."<br/>";
						//echo "points: ".$tagList[$j]["points"]."<br/>";
						
						$multipliedPoints = (($stringMatch[$i]["description"] * $descriptionMult) + ($stringMatch[$i]["name"] * $nameMult) + ($stringMatch[$i]["mission"] * $missionMult) + ($stringMatch[$i]["about"] * $aboutMult) + ($stringMatch[$i]["products"] * $productsMult) + ($stringMatch[$i]["type"] * $typeMult)) * $tagList[$j]["points"];
						
						//echo "multiplication: ".$multipliedPoints."<br/>";
						
						$postList[$i]["allTags"][$tagList[$j]["tag"]] += $multipliedPoints;
										
						$matchNum++;
						
						echo "<b>$matchNum -- ".$postList[$i]["name"]." -- ".$tagList[$j]["word"].": </b>".$multipliedPoints." points for ".$tagList[$j]["tag"];
						echo "<br/>";
						
					}
					
				}
				$currentID = $postList[$i]["bubbleID"];
				if ($currentID == "")
				{
					$currentID = $postList[$i]["accountFacebookID"];
				}
				if ( isset($postList[$i]["allTags"]) )
					{
						array_walk($postList[$i]["allTags"], 'removeFromSQL', $currentID);
						array_walk($postList[$i]["allTags"], 'addToSQL', $currentID);
					}
				echo "Total scores: ";
				print_r($postList[$i]["allTags"]);
				echo "<br/> bubbleID: ".$currentID;
				// We commented this part out so music wouldn't play...
				echo "<br/> Description of post:<br/>";
				print_r($postList[$i]["description"]);
				// This section ^
				
				echo "<br/><br/>";
				
				//echo "<br/>";
				//print_r($postList[$i]$tagList[$j]);
				echo "<br/>";
				
				
			}
			//print_r($postList);
			
			echo "<br/><br/>";
		}

	
	
	curate_all();	
	
?>