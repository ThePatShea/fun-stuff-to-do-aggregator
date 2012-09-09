<?php  include_once("general.php");  ?>

<?php


	function sortBubbles_deals_single($bubbleID)
	{
		$children     =  generateQueryCommaList
		("
			SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
			)
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				)
			)
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				)
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
					OR
					bubbleID_parent IN
					(
						SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
					)
				)
			)
		", 0, "flat");
		
		$bubbleList    =  "'$bubbleID'";
		if ($children != "") $bubbleList .=  ", $children";
		
		$connections   =  generateQueryArray
		("
			SELECT		bubbleID_tag,bubbleID_bubble
			FROM		bubbles_connections
			WHERE		bubbleID_bubble IN ($bubbleList)
			ORDER BY	bubbleID_bubble
		");	
		
		$query  =  "SELECT DISTINCT tags.bubbleID FROM posts_trending ";
		$query .=  "LEFT JOIN tags ON posts_trending.bubbleID = tags.bubbleID ";
		$query .=  "LEFT JOIN events_info ON posts_trending.bubbleID = events_info.bubbleID ";
		$query .=  "WHERE tags.bubbleID IN (";
		$query .=  "SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[0]["bubbleID_tag"]."'";
		
		$count__connections   =   count($connections);
		for ($i = 1;     $i   <  $count__connections ; $i++)
		{
			if ($connections[$i]["bubbleID_bubble"] == $connections[$i-1]["bubbleID_bubble"])
				$query  .=  " AND tags.bubbleID IN (SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[$i]["bubbleID_tag"]."')";
			else
				$query  .=  ") OR tags.bubbleID IN (SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[$i]["bubbleID_tag"]."'";
						
			if ($i == $count__connections - 1)  $query .= ") ";
		}
		
						
		global $postArray;
		$postArray              =  generateQueryArray_flat($query);
		$postCommaList_notFlat  =  generateCommaList($postArray,0,"true");
		$postCommaList          =  $postCommaList_notFlat[0];
				
		if ($postCommaList == "") die();

		$currentTime  =  time();
		$deals        =  generateQueryArray_flat
		("
		    SELECT		 posts.bubbleID
		    FROM		 posts
		    LEFT JOIN	 deals_info
		    ON			 posts.bubbleID  =  deals_info.bubbleID
		    WHERE		 posts.bubbleID IN ($postCommaList)
		    AND			 posts.type = 'deal'
		    AND			 deals_info.end_time > $currentTime
		");
					
		return $deals;		
	}
	
	
	
	
	
	
	
	function sortBubbles_venues_single($bubbleID)
	{
		$children     =  generateQueryCommaList
		("
			SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
			)
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				)
			)
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				)
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
					OR
					bubbleID_parent IN
					(
						SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
					)
				)
			)
		", 0, "flat");
		
		$bubbleList    =  "'$bubbleID'";
		if ($children != "") $bubbleList .=  ", $children";
		
		$connections   =  generateQueryArray
		("
			SELECT		bubbleID_tag,bubbleID_bubble
			FROM		bubbles_connections
			WHERE		bubbleID_bubble IN ($bubbleList)
			ORDER BY	bubbleID_bubble
		");	
		
		$query  =  "SELECT DISTINCT tags.bubbleID FROM posts_trending ";
		$query .=  "LEFT JOIN tags ON posts_trending.bubbleID = tags.bubbleID ";
		$query .=  "LEFT JOIN events_info ON posts_trending.bubbleID = events_info.bubbleID ";
		$query .=  "WHERE tags.bubbleID IN (";
		$query .=  "SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[0]["bubbleID_tag"]."'";
		
		$count__connections   =   count($connections);
		for ($i = 1;     $i   <  $count__connections ; $i++)
		{
			if ($connections[$i]["bubbleID_bubble"] == $connections[$i-1]["bubbleID_bubble"])
				$query  .=  " AND tags.bubbleID IN (SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[$i]["bubbleID_tag"]."')";
			else
				$query  .=  ") OR tags.bubbleID IN (SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[$i]["bubbleID_tag"]."'";
						
			if ($i == $count__connections - 1)  $query .= ") ";
		}
		
						
		global $postArray;
		$postArray              =  generateQueryArray_flat($query);
		$postCommaList_notFlat  =  generateCommaList($postArray,0,"true");
		$postCommaList          =  $postCommaList_notFlat[0];
				
		if ($postCommaList == "") return;

		$venues     =   generateQueryArray
		("
		    SELECT		posts.name, posts_trending.score
		    FROM		posts_trending
		    LEFT JOIN	posts
		    ON			posts_trending.bubbleID = posts.bubbleID
		    WHERE		posts.bubbleID IN ($postCommaList)
		    AND			posts.type = 'venue'
		    ORDER BY	posts_trending.score DESC
		");
		
		$count_venues  =  count($venues);
		
		
		$venueHours  =  generateQueryArray
		("
			SELECT		accountFacebookID, day, open, close
			FROM		pages_hours
			WHERE		accountFacebookID IN ($postCommaList)
			ORDER BY	accountFacebookID
		");
		
		
		$currentTime	     =  time();
		$today_start         =  strtotime("today 00:00");
	    $tonight_start       =  strtotime("today 18:00");
	    $tomorrow_start      =  strtotime("tomorrow");
	    $tomorrow_end        =  strtotime("tomorrow 23:59");
	    $this_week_start     =  strtotime("monday this week");
	    $this_weekend_start  =  strtotime("friday this week");
	    $next_week_start     =  strtotime("monday next week");
	    $next_weekend_start  =  strtotime("friday next week");
	    $next_weekend_end    =  strtotime("friday next week + 3 days");
				
				
		$count_venueHours  =  count($venueHours);
		for ($i = 0; $i < $count_venueHours; $i++)
		{
			$unixOpen  = strtotime("this ".$venueHours[$i]["day"]." ".$venueHours[$i]["open"]);
			$unixClose = strtotime("this ".$venueHours[$i]["day"]." ".$venueHours[$i]["open"]);
			
			if ($unixOpen <= $currentTime  && $currentTime   <= $unixClose)
			{
				$timeframeGroups["now"][++$timeframeIteration["now"]] = $venueHours[$i]["accountFacebookID"];
			}
			
			if ( ($unixOpen <= $today_start  &&  $today_start <= $unixClose) || ($today_start <= $unixOpen  &&  $unixOpen <= $tonight_start) )
			{
				$timeframeGroups["today"][++$timeframeIteration["today"]] = $venueHours[$i]["accountFacebookID"];				
			}
			
			if ( ($unixOpen <= $tonight_start  &&  $tonight_start <= $unixClose) || ($tonight_start <= $unixOpen  &&  $unixOpen <= $tomorrow_start) )
			{
				$timeframeGroups["tonight"][++$timeframeIteration["tonight"]] = $venueHours[$i]["accountFacebookID"];
			}
			
			if ( ($unixOpen <= $tomorrow_start  &&  $tomorrow_start <= $unixClose) || ($tomorrow_start <= $unixOpen  &&  $unixOpen <= $tomorrow_end) )
			{
				$timeframeGroups["tomorrow"][++$timeframeIteration["tomorrow"]] = $venueHours[$i]["accountFacebookID"];
			}
						
			if ( ($unixOpen <= $this_week_start  &&  $this_week_start <= $unixClose) || ($this_week_start <= $unixOpen  &&  $unixOpen <= $this_weekend_start) )
			{
				$timeframeGroups["this week"][++$timeframeIteration["this week"]] = $venueHours[$i]["accountFacebookID"];
				$timeframeGroups["next week"][++$timeframeIteration["next week"]] = $venueHours[$i]["accountFacebookID"];
			}
				
			if ( ($unixOpen <= $this_weekend_start  &&  $this_weekend_start <= $unixClose) || ($this_weekend_start <= $unixOpen  &&  $unixOpen <= $next_week_start) )
			{				
				$timeframeGroups["this weekend"][++$timeframeIteration["this weekend"]] = $venueHours[$i]["accountFacebookID"];
				$timeframeGroups["next weekend"][++$timeframeIteration["next weekend"]] = $venueHours[$i]["accountFacebookID"];
			}
						
			$timeframeGroups["upcoming"][++$timeframeIteration["upcoming"]]  =  $venueHours[$i]["accountFacebookID"];				
		}
		
		
		
		if ($timeframeGroups != "")   $timeframeKeys  =  array_keys($timeframeGroups);
		
		$count_timeframeKeys  =  count($timeframeKeys);
		
		for ($i = 0; $i < $count_timeframeKeys; $i++)
		{
			array_unshift($timeframeGroups[$timeframeKeys[$i]], 0);
			array_shift($timeframeGroups[$timeframeKeys[$i]]);
			
			$timeframeGroupCommas  =  generateCommaList($timeframeGroups[$timeframeKeys[$i]],0,"true","true");
			
			$venues_trendingSorted[$timeframeKeys[$i]] = generateQueryArray
			("
				SELECT		bubbleID, score
				FROM		posts_trending
				WHERE		bubbleID IN ($timeframeGroupCommas)
				ORDER BY	score DESC
			");
						
		}
		
				
		$timeframeOrder        =  array("now", "today", "tonight", "tomorrow", "this week", "this weekend", "next week", "next weekend", "upcoming");
		$count_timeframeOrder  =  count($timeframeOrder);
		
		for ($i = 0; $i < $count_timeframeOrder; $i++)
		{
			echo "<br><b>".$timeframeOrder[$i]."</b><br>";
			
			$j_max = 4;
			
			if ($timeframeOrder[$i] == "upcoming")  count($venues_trendingSorted[$timeframeOrder[$i]]);
			
			for ($j = 0; $j < $j_max; $j++)
			{
				if (isset($alreadyUsed))
				{
					$alreadyUsedCheck  =  array_search($venues_trendingSorted[$timeframeOrder[$i]][$j]["bubbleID"], $alreadyUsed);
				}
				else
				{
					$alreadyUsedCheck  =  "";
				}
				
				if ($alreadyUsedCheck == "")
				{
					if ($venues_trendingSorted[$timeframeOrder[$i]][$j]["bubbleID"] != "")
					{
						$finalVenues[$timeframeOrder[$i]][++$finalCount[$timeframeOrder[$i]]] = $venues_trendingSorted[$timeframeOrder[$i]][$j]["bubbleID"];
						$alreadyUsed[++$alreadyUsedCount]  =  $venues_trendingSorted[$timeframeOrder[$i]][$j]["bubbleID"];
						
						echo $venues_trendingSorted[$timeframeOrder[$i]][$j]["bubbleID"]."<br>";
					}
				}
				else
				{
					$j_max++;
				}
			}
			echo "<br>";
		}
		
		return $finalVenues;		
	}










	function sortBubbles_single($bubbleID)
	{		
		$currentTime  =  time();
		$yesterday    =  strtotime("yesterday");
		
		$children     =  generateQueryCommaList
		("
			SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
			)
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				)
			)
			OR
			bubbleID_parent IN
			(
				SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
				)
				OR
				bubbleID_parent IN
				(
					SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
					OR
					bubbleID_parent IN
					(
						SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID'
					)
				)
			)
		", 0, "flat");
		
		$bubbleList    =  "'$bubbleID'";
		if ($children != "") $bubbleList .=  ", $children";
		
		$connections   =  generateQueryArray
		("
			SELECT		bubbleID_tag,bubbleID_bubble
			FROM		bubbles_connections
			WHERE		bubbleID_bubble IN ($bubbleList)
			ORDER BY	bubbleID_bubble
		");	
		
		$query  =  "SELECT DISTINCT tags.bubbleID FROM posts_trending ";
		$query .=  "LEFT JOIN tags ON posts_trending.bubbleID = tags.bubbleID ";
		$query .=  "LEFT JOIN events_info ON posts_trending.bubbleID = events_info.bubbleID ";
		$query .=  "WHERE tags.bubbleID IN (";
		$query .=  "SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[0]["bubbleID_tag"]."'";
		
		$count__connections   =   count($connections);
		for ($i = 1;     $i   <  $count__connections ; $i++)
		{
			if ($connections[$i]["bubbleID_bubble"] == $connections[$i-1]["bubbleID_bubble"])
				$query  .=  " AND tags.bubbleID IN (SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[$i]["bubbleID_tag"]."')";
			else
				$query  .=  ") OR tags.bubbleID IN (SELECT tags.bubbleID FROM tags WHERE tag = '".$connections[$i]["bubbleID_tag"]."'";
				
			$query .=  " AND end_time   >  $currentTime ";
			$query .=  " AND start_time >  $yesterday ";	
			
			if ($i == $count__connections - 1)  $query .= ") ";
		}
		
		
		$query .=  "ORDER BY start_time ASC ";
				
		global $postArray;
		$postArray              =  generateQueryArray_flat($query);
		$postCommaList_notFlat  =  generateCommaList($postArray,0,"true");
		$postCommaList          =  $postCommaList_notFlat[0];
				
		if ($postCommaList == "") return;
				
		$postInfo       =  generateQueryArray
		("
		    SELECT posts.name AS post_name, posts.pic_big, posts.type, location, posts.bubbleID, start_time, end_time, location, score, accounts.pic_square, accounts.name AS account_name, events_invited_count.count_attending, accounts.accountFacebookID AS venue_accountFacebookID
		    FROM posts_trending
		    LEFT JOIN events_info
		    ON posts_trending.bubbleID = events_info.bubbleID
		    LEFT JOIN events_invited_count
		    ON events_info.bubbleID = events_invited_count.bubbleID
		    LEFT JOIN accounts
		    ON events_info.accountFacebookID_venue = accounts.accountFacebookID
		    LEFT JOIN posts
		    ON events_info.bubbleID = posts.bubbleID
		    WHERE posts.bubbleID IN ($postCommaList)
		    ORDER BY start_time ASC
		");
		
		
	
		if ($postInfo == "") return;		
	
		$commentsCountInfo    =  generateQueryArray
		("
			SELECT		bubbleID_parent, Count(*)
			FROM		comments
			WHERE		bubbleID_parent  IN  ($postCommaList)
			GROUP BY	bubbleID_parent
		");
		
		$today_start                   =  strtotime("today 00:00");
	    $tonight_start                 =  strtotime("today 18:00");
	    $tomorrow_start                =  strtotime("tomorrow");
	    $tomorrow_end                  =  strtotime("tomorrow 23:59");
	    $this_week_start               =  strtotime("monday this week");
	    $this_weekend_start            =  strtotime("friday this week");
	    $next_week_start               =  strtotime("monday next week");
	    $next_weekend_start            =  strtotime("friday next week");
	    $next_weekend_end              =  strtotime("friday next week + 3 days");
		
		
		$emoryStudents  =  generateQueryCommaList("SELECT accountFacebookID FROM users_connections WHERE connection_facebookID = '110034522347806' AND type = 'College'", 0, "flat");
		
		
		$numPosts       =  count($postArray);
		$numMorePeople  =  20 * $numPosts;
		$curMorePerson  =  0;
		
		$morePeople     =  generateQueryArray
		("
		    SELECT		accounts.name, accounts.pic_square, events_invited.accountFacebookID, events_invited.bubbleID
		    FROM 		events_invited
		    RIGHT JOIN 	accounts
		    ON 			events_invited.accountFacebookID = accounts.accountFacebookID
		    WHERE		events_invited.bubbleID IN ($postCommaList)
		    AND			events_invited.rsvp_status = 'attending'
		    AND			accounts.pic_square != ''
		    LIMIT		$numMorePeople
		");
		
		
		foreach ($postInfo as $post)
	    {
	    	if      ($post["start_time"] < $currentTime         &&  $post["end_time"]   >= $currentTime)         $post["timeframe"]  =  "now";
	    	else if ($post["start_time"] < $tonight_start       &&  $post["start_time"] >= $today_start)         $post["timeframe"]  =  "today";
	    	else if ($post["start_time"] < $tomorrow_start      &&  $post["start_time"] >= $tonight_start)       $post["timeframe"]  =  "tonight";
	    	else if ($post["start_time"] < $tomorrow_end        &&  $post["start_time"] >= $tomorrow_start)      $post["timeframe"]  =  "tomorrow";
	    	else if ($post["start_time"] < $this_weekend_start  &&  $post["start_time"] >= $this_week_start)     $post["timeframe"]  =  "this week";
	    	else if ($post["start_time"] < $next_week_start     &&  $post["start_time"] >= $this_weekend_start)  $post["timeframe"]  =  "this weekend";
	    	else if ($post["start_time"] < $next_weekend_start  &&  $post["start_time"] >= $next_week_start)     $post["timeframe"]  =  "next week";
	    	else if ($post["start_time"] < $next_weekend_end    &&  $post["start_time"] >= $next_weekend_start)  $post["timeframe"]  =  "next weekend";
	    	else                                                                                                 $post["timeframe"]  =  "upcoming";
	    	
	    	
	    	$emoryAttendees  =  generateQuery_singleVar
			("
				SELECT		count(*)
				FROM 		events_invited
				WHERE		bubbleID = '".$post["bubbleID"]."'
				AND			rsvp_status = 'attending'
				AND			accountFacebookID IN ($emoryStudents)
			");
	    	
	    		    
			$peopleAttending  =  generateQueryArray
			("
				SELECT		accounts.name, accounts.pic_square, events_invited.accountFacebookID, events_invited.bubbleID
				FROM 		events_invited
				RIGHT JOIN 	accounts
				ON 			events_invited.accountFacebookID = accounts.accountFacebookID
				WHERE		events_invited.bubbleID = '".$post["bubbleID"]."'
				AND			events_invited.rsvp_status = 'attending'
				AND			events_invited.accountFacebookID IN ($emoryStudents)
				AND			accounts.pic_square != ''
				LIMIT		6
			");
			
			
			$count__peopleAttending  =   count($peopleAttending);
			for ($i = 0;         $i  <  $count__peopleAttending ;        $i++)
			{
				$post["person$i"."_accountFacebookID"]  =  $peopleAttending[$i]["accountFacebookID"];	
				$post["person$i"."_pic_square"]         =  $peopleAttending[$i]["pic_square"];
				$post["person$i"."_name"]               =  $peopleAttending[$i]["name"];	
			}
			
			if ($count__peopleAttending < 6)
			{
				$count_morePeople = 6 - $count__peopleAttending;
				
				for($i = $count__peopleAttending; $i <= 6; $i++)
				{
					$post["person$i"."_accountFacebookID"]  =  $morePeople[$curMorePerson++]["accountFacebookID"];	
					$post["person$i"."_pic_square"]         =  $morePeople[$curMorePerson++]["pic_square"];
					$post["person$i"."_name"]               =  $morePeople[$curMorePerson++]["name"];	
				}
			}
			
			
			if   ($commentsCountInfo != "")  $commentsKeys  =  searchForId($post["bubbleID"], $commentsCountInfo);
		    				
		    if   ($commentsKeys      != "")
		    {
		          $commentsCount  =  $commentsCountInfo[$commentsKeys[0]]["Count(*)"];
		    }
		    else
		    {
		          $commentsCount  =  0;
		    }
		    
		// QUICK FIX BANDAID UNTIL WE GET THE BAD VENUES OUT OF THE STUDENT DEALS BUBBLE. THIS STATEMENT APPEARS IN TWO PLACES.
	    if ($bubbleID != "8475045d-ed4f-11e1-bf61-aafbeaa37357")
	    {    
		    mysql_query
		    ("
		    	INSERT INTO		bubbles_front_temp
		    	(`bubbleID_bubble`, `bubbleID_post`, `timeframe`, `comments`, `emory_joins`, `score`, `person0_accountFacebookID`, `person1_accountFacebookID`, `person2_accountFacebookID`, `person3_accountFacebookID`, `person4_accountFacebookID`, `person5_accountFacebookID`, `person0_name`, `person1_name`, `person2_name`, `person3_name`, `person4_name`, `person5_name`, `person0_pic_square`, `person1_pic_square`, `person2_pic_square`, `person3_pic_square`, `person4_pic_square`, `person5_pic_square`, `type`, `name`, `venue_name`, `venue_pic_square`,  `venue_accountFacebookID`, `subtitle`, `atlanta_joins`, `post_pic_big`, `location`)
		    	
		    	VALUES
		    	( '".$bubbleID."', '".$post["bubbleID"]."', '".$post["timeframe"]."', '".$commentsCount."', '".$emoryAttendees."', '".$post["score"]."', '".$post["person0"."_accountFacebookID"]."', '".$post["person1"."_accountFacebookID"]."', '".$post["person2"."_accountFacebookID"]."', '".$post["person3"."_accountFacebookID"]."', '".$post["person4"."_accountFacebookID"]."', '".$post["person5"."_accountFacebookID"]."', '".$post["person0"."_name"]."', '".$post["person1"."_name"]."', '".$post["person2"."_name"]."', '".$post["person3"."_name"]."', '".$post["person4"."_name"]."', '".$post["person5"."_name"]."', '".$post["person0"."_pic_square"]."', '".$post["person1"."_pic_square"]."', '".$post["person2"."_pic_square"]."', '".$post["person3"."_pic_square"]."', '".$post["person4"."_pic_square"]."', '".$post["person5"."_pic_square"]."', '".$post["type"]."', '".$post["post_name"]."', '".$post["account_name"]."', '".$post["pic_square"]."', '".$post["venue_accountFacebookID"]."', '".date("M j | g:iA",$post["start_time"])."', ".$post["count_attending"].", '".$post["pic_big"]."', '".$post["location"]."')
		    ");
		    
		    echo "<br>"."<img src='".$post["pic_big"]."'/>"."<b>".$post["bubbleID"]."</b>"." (score: ".$post["score"].", emory attendees: ".$emoryAttendees.", comments: ".$commentsCount.")".": ".$post["timeframe"]." -- ".date("l, F jS, g:iA - ",$post["start_time"]).date("g:iA",$post["end_time"])." | ".$post["post_name"]."<br>";
		 }
					
	    }
	    
	    
	    echo "<br><br><br>-----------------<br><br><br>";
	    
	    $timeframes[0]  =  "now";
	    $timeframes[1]  =  "today";
	    $timeframes[2]  =  "tonight";
	    $timeframes[3]  =  "tomorrow";
	    $timeframes[4]  =  "this week";
	    $timeframes[5]  =  "this weekend";
	    $timeframes[6]  =  "next week";
	    $timeframes[7]  =  "next weekend";
	    $timeframes[8]  =  "upcoming";
	    
	    $rank_outer = 0;
	    
	    $count_timeframes  =  count($timeframes);
	    
	    $finalVenues       =  sortBubbles_venues_single($bubbleID);
	    
	    
	    
	    
	    
	    
	    
	    
	  	//Adds the deals
	  		$currentTime = time();
	  		
	  		$deals  =   sortBubbles_deals_single($bubbleID);
	  	    
	  	    $count_deals = count($deals);
	  	    
	  	    for($i = 0; $i < $count_deals; $i++)
	  	    {
	  	    	$postInfo       =  generateQueryArray("SELECT * FROM posts WHERE bubbleID = '".$deals[$i]."' LIMIT 1");
	  	    			        
	  	    	$dealsInfo      =  generateQueryArray("SELECT * FROM deals_info WHERE bubbleID = '".$deals[$i]."' LIMIT 1");
	  	    	
	  	    	$dealVenueInfo  =  generateQueryArray("SELECT * FROM accounts WHERE accountFacebookID = '".$dealsInfo[0]["accountFacebookID_venue"]."' LIMIT 1");
	  	    	
	  	    	$atlanta_joins  =  generateQuery_singleVar("SELECT likes FROM pages_info WHERE accountFacebookID = '".$dealsInfo[0]["accountFacebookID_venue"]."' LIMIT 1");
	  	    	
	  	    	//Calculate expiration
	  	    		$numDays   =  intval(($dealsInfo[0]["end_time"] - $currentTime)/60/60/24);
	  	    		$expires   =  "$numDays days";
	  	    		$subtitle  =  "$expires left";
	  	    	
	  	    	// Inserts deals into table with really high score so they always so up first in their section
	  	    		mysql_query
		    		("
		    			INSERT INTO		bubbles_front_temp
		    			(bubbleID_bubble, bubbleID_post, timeframe, score, type, name, post_pic_big, price, value, discount, expires, subtitle, atlanta_joins, venue_accountFacebookID, venue_name, venue_pic_square)
		    			
		    			VALUES
		    			('$bubbleID', '".$deals[$i]."', 'tomorrow', 100000, 'deal', '".$postInfo[0]["name"]."', '".$postInfo[0]["pic_big"]."', '".$dealsInfo[0]["price"]."', '".$dealsInfo[0]["value"]."', '".$dealsInfo[0]["discount"]."', '$expires', '$subtitle', '$atlanta_joins', '".$dealVenueInfo[0]["accountFacebookID"]."', '".$dealVenueInfo[0]["name"]."', '".$dealVenueInfo[0]["pic_square"]."')
		    		");
				
				echo "<br>"."added deal ".$deals[$i]."<br>";
	  	    }
	    
	    
	    // QUICK FIX BANDAID UNTIL WE GET THE BAD VENUES OUT OF THE STUDENT DEALS BUBBLE. THIS STATEMENT APPEARS IN TWO PLACES.
	    if ($bubbleID != "8475045d-ed4f-11e1-bf61-aafbeaa37357")
	    {
	    
	    
	    for ($j = 0; $j < $count_timeframes; $j++)
	    {
	    	$postGroup  =  generateQueryArray("SELECT bubbleID_post, score FROM bubbles_front_temp WHERE bubbleID_bubble = '$bubbleID' AND timeframe = '".$timeframes[$j]."' ORDER BY score DESC");
	    	
	    	$count_postGroup = count($postGroup);
	    	
	    	for ($i = 0; $i < $count_postGroup; $i++)
	    	{
	    		mysql_query("UPDATE bubbles_front_temp SET rank_inner = $i, rank_outer = ".$rank_outer++." WHERE bubbleID_bubble = '$bubbleID' AND bubbleID_post = '".$postGroup[$i]["bubbleID_post"]."'");
	    	}
	    	
	    	$currentVenues        =  $finalVenues[$timeframes[$j]];
	    	
	    	$count_currentVenues  =  count($currentVenues);
	    	
	    	for ($i = 0; $i < $count_currentVenues; $i++)
	    	{
	    	
	    		$venueComments        =  generateQuery_singleVar("SELECT count(*) FROM comments WHERE bubbleID_parent = '".$currentVenues[($i+1)]."'");
	    		$venueInfo_accounts   =  generateQueryArray(" SELECT * FROM accounts WHERE accountFacebookID = '".$currentVenues[($i+1)]."' LIMIT 1");
	    		$venueInfo_pages      =  generateQueryArray(" SELECT * FROM pages_info WHERE accountFacebookID = '".$currentVenues[($i+1)]."' LIMIT 1");
	    							  
	    							  
	    		$currentDay           =  date("D");
	    		$currentTime		  =  time();
	    		$venueInfo_hours      =  generateQueryArray(" SELECT * FROM pages_hours WHERE accountFacebookID = '".$currentVenues[($i+1)]."' AND day = '$currentDay' LIMIT 1");
	    		
	    		if ($venueInfo_hours !=  "")
	    		{
	    			$unixOpen     	  =  strtotime($venueInfo_hours[0]["day"]." ".$venueInfo_hours[0]["open"]);
	    			$unixClose    	  =  strtotime($venueInfo_hours[0]["day"]." ".$venueInfo_hours[0]["close"]);
	    			
	    			if ($unixClose < $unixOpen)
	    			{
	    				$openVar     =  "yesterday";
	    			}
	    			else
	    			{
	    				$openVar     =  "today";
	    			}
	    			
	    			$todayOpen     	  =  strtotime("$openVar ".$venueInfo_hours[0]["open"]);
	    			$todayClose    	  =  strtotime("today ".$venueInfo_hours[0]["close"]);
	    			
	    				    			
	    			if ($todayClose > $currentTime && $currentTime > $todayOpen)
	    			{
	    				$insertHours  =  "Open Now (until ".date("g:ia",$unixClose).")";
	    			}
	    			else
	    			{
		    			$insertHours  =  "Open Today (".date("g:ia",$unixOpen)." - ".date("g:ia",$unixClose).")";
	    			}	    			
	    		}
	    		else
	    		{
	    			$insertHours = "Not Open Today";
	    		}
	    		
	    		echo "<br>--------- hours ----------<br>";
	    			echo $insertHours;
	    		echo "<br>--------- hours ----------<br>";
				
	    		
	    		mysql_query
	    		("
		    		INSERT INTO		bubbles_front_temp
		    		(bubbleID_bubble, bubbleID_post, timeframe, rank_inner, rank_outer, comments, type,
		    		name, post_pic_big, subtitle, atlanta_joins, street, city, state, hours)
		    		VALUES
		    		('".$bubbleID."', '".$currentVenues[($i+1)]."', '".$timeframes[$j]."', ".($count_postGroup + $i).", ".$rank_outer++.", ".$venueComments.", 'venue',
		    		'".$venueInfo_accounts[0]["name"]."', '".$venueInfo_accounts[0]["pic_big"]."', '".$venueInfo_pages[0]["type"]."', '".$venueInfo_pages[0]["likes"]."', '".$venueInfo_pages[0]["street"]."', '".$venueInfo_pages[0]["city"]."', '".$venueInfo_pages[0]["state"]."', '$insertHours')
	    		");  		    		
	    	
	    	}
	    	
	    	
	    }
	    
	    }
	    	
	    print_r($postGroup);
	    
	    echo "<br><br><br>-----------------<br><br><br>";
	    
	    
	    
	    echo "<br>----- finalVenues -----<br>";
	    print_r($finalVenues);
	    echo "<br>----- finalVenues -----<br>";
	    
	    
	}
	
	function sortBubbles()
	{
		mysql_query("DELETE FROM bubbles_front_temp WHERE 1");
		
		$topBubbleArray  =  generateQueryArray_flat
		("
			SELECT		bubbleID_bubble
			FROM		bubbles_list
			WHERE		orderID > -1
		");
		
		$count_topBubbleArray = count($topBubbleArray);
		
		for($i = 0; $i < $count_topBubbleArray; $i++)
		{
			echo "<br><u><b>Bubble ".($i + 1)." of ".($count_topBubbleArray)."</b></u><br>";
			
			sortBubbles_single($topBubbleArray[$i]);
			
			echo "<br><u><b>Finished with Bubble ".($i + 1)." of ".($count_topBubbleArray)."</b></u><br><br>";
		}
		
		
		mysql_query("DELETE FROM bubbles_front WHERE 1");
		
		mysql_query(" SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' "); // Necessary to copy data from one table to another
		
		mysql_query
		("
			INSERT INTO		bubbles_front
			SELECT			* 
			FROM			bubbles_front_temp
		");
	}
		
	sortBubbles();

?>