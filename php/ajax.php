<?php  include_once("general.php");  ?>

<?php
   
   	$cover_photo["outdoor adventures"]	=  "img/cover_photo/outdoor_adventures.jpg"; 
    $cover_photo["weekend escapes"]	    =     "img/cover_photo/weekend_escapes.jpg"; 
    $cover_photo["campus events"]	    =       "img/cover_photo/campus_events.jpg"; 
    $cover_photo["student deals"]	    =       "img/cover_photo/student_deals.jpg"; 
    $cover_photo["night clubs"]  	    =         "img/cover_photo/night_clubs.jpg";
    $cover_photo["night life"]   	    =          "img/cover_photo/night_life.jpg";
    $cover_photo["greek life"]   	    =          "img/cover_photo/greek_life.jpg";
    $cover_photo["freshmen"]     	    =            "img/cover_photo/freshmen.jpg";
    $cover_photo["concerts"]     	    =            "img/cover_photo/concerts.jpg";
    $cover_photo["sports"]	     	    =              "img/cover_photo/sports.jpg";
    $cover_photo["food"]	     	    =                "img/cover_photo/food.jpg";
    $cover_photo["bars"]	     	    =                "img/cover_photo/bars.jpg";
	    
    
    
    function getTopBubbles()
    {
    	//global $topBubbleList;
    	
    	$bubbleInfo = generateQueryArray
    	("
    		SELECT 		*
    		FROM		bubbles_list
		    
    		ORDER BY	orderID
    	");
	//print_r($bubbleInfo);    	
    	return $bubbleInfo;
    }
    
    
    function getSelector()
    {
    	echo "<section id='featured_mobile'>";
			echo "<h3 id='featured_mobile_text'>";
		    	echo "Tap a bubble, swipe for more.";
		    echo "</h3>";
		    
		    echo "<img id='shiftDown' onclick='selectorClick()' src='img/mobile/shiftDown.png'/>";
		echo "</section>";
		
		echo "<div id='selector' onclick='selectorClick()'>";
			echo "<img class='selectorImage'       src='img/phase1/selector.png'/>";
			echo "<div class='selectedBubbleImage cover_photo' style='background: url(\"img/cover_photo/default.jpg\")'></div>";
			echo "<h1 id='selectorText_top'>";
				echo "<div style='font-size: 20px; position: relative; top: 10px;'>select a</div>";
				echo "<div style='font-size: 24px; position: relative; top: 15px;'>bubble</div>";
			echo "</h1>";
			echo "<h1 id='selectorText_bottom'>";
			echo "</h1>";
		echo "</div>";
    }
    
    
    
    function getBubbles()
    {
    	global $cover_photo;
    	
    	$bubbleInfo = getTopBubbles();
		
		echo "<section class='smallBubbleContainer'>";
		
			$currentBubble = 0;
			
			$count__bubbleInfo  =   count($bubbleInfo) + 1;
			for ($i = 0;    $i  <  $count__bubbleInfo ;    $i++)
			{
				$displayInfo[$currentBubble]["bubbleID"] = $bubbleInfo[$i-1]["bubbleID_bubble"];
				
				if ( $bubbleInfo[$i]["bubbleID_bubble"] != $bubbleInfo[($i-1)]["bubbleID_bubble"]  ||  ($i == $count__bubbleInfo - 1) )
			    {
			    	if ($i != 0)
			    	{	
			    		$displayInfo[$currentBubble]["cover_photo"] = $bubbleInfo[$i-1]["background_url"];
			    		$currentBubble++;
			    	}
			    	
			    }	
			}
			
			
			for ($i = 0; $i < $currentBubble; $i++)
			{
				echo "<li onclick='switchBubble(\"".$displayInfo[$i]["bubbleID"]."\");'";
			    
			    	echo "class='bubble conveyorBubble' id='smallBubble_".($i+1)."'>";
			    
			    	echo "<div class='bubbleInfo'>";
			    	    echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
				        	echo "<h2>";
				        		if ($displayInfo[$i]["bubbleID"] == "ee14bc9e-d5ed-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: -3px;'>";
										echo "<div style='font-size: 22px;'>night</div>";
										echo "<div style='font-size: 32px; position: relative; top: -1px;'>life</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "ee14c716-d5ed-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: -3px;'>";
										echo "<div style='font-size: 20px;'>greek</div>";
										echo "<div style='font-size: 32px; position: relative; top: -1px;'>life</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "ee14d044-d5ed-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: 0px;'>";
										echo "<div style='font-size: 23px;'>emory</div>";
										echo "<div style='font-size: 14px; position: relative; top: -6px;'>academics</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "ee14d968-d5ed-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: 1px;'>";
										echo "<div style='font-size: 16px;'>atlanta</div>";
										echo "<div style='font-size: 18px; position: relative; top: -8px;'>sports</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "ee14ab0a-d5ed-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: 1px;'>";
										echo "<div style='font-size: 32px;'>get</div>";
										echo "<div style='font-size: 22px; position: relative; top:-1px;'>food</div>";
									echo "</div>";
				        		}
				        		
				        		else if ($displayInfo[$i]["bubbleID"] == "138814bc-d5ee-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: 0px;'>";
										echo "<div style='font-size: 16px;'>atlanta</div>";
										echo "<div style='font-size: 28px; position: relative; top: -2px;'>bars</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "1385e7e6-d5ee-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: 0px;'>";
										echo "<div style='font-size: 26px;'>night</div>";
										echo "<div style='font-size: 23px; position: relative; top: 1px;'>clubs</div>";
									echo "</div>";
				        		}else if ($displayInfo[$i]["bubbleID"] == "2809077e-d5ef-11e1-b249-002590605566")
				        		{
				        			echo "<div style='position: relative; top: 3px;'>";
										echo "<div style='font-size: 14px;'>concerts</div>";
										echo "<div style='font-size: 17px; position: relative; top: -7px;'>&shows</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "713d4d27-ed49-11e1-bf61-aafbeaa37357")
				        		{
				        			echo "<div style='position: relative; top: 0px;'>";
										echo "<div style='font-size: 18px;'>weekend</div>";
										echo "<div style='font-size: 18px; position: relative; top: -5px;'>escapes</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "e099ace7-ed4b-11e1-bf61-aafbeaa37357")
				        		{
				        			echo "<div style='position: relative; top: 3px;'>";
										echo "<div style='font-size: 14px;'>freshmen</div>";
										echo "<div style='font-size: 20px; position: relative; top: -8px;'>events</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "49438594-ed4c-11e1-bf61-aafbeaa37357")
				        		{
				        			echo "<div style='position: relative; top: 2px;'>";
										echo "<div style='font-size: 18px;'>campus</div>";
										echo "<div style='font-size: 19px; position: relative; top: -7px;'>events</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "8475045d-ed4f-11e1-bf61-aafbeaa37357")
				        		{
				        			echo "<div style='position: relative; top: 1px;'>";
										echo "<div style='font-size: 16px;'>student</div>";
										echo "<div style='font-size: 23px; position: relative; top: -6px;'>deals</div>";
									echo "</div>";
				        		}
				        		else if ($displayInfo[$i]["bubbleID"] == "647659d4-ed50-11e1-bf61-aafbeaa37357")
				        		{
				        			echo "<div style='position: relative; top: 0px;'>";
										echo "<div style='font-size: 18px;'>outdoor</div>";
										echo "<div style='font-size: 13px; position: relative; top: -6px;'>adventures</div>";
									echo "</div>";
				        		}
				        		
				        		
				        	echo "</h2>";
			    	    echo "</div></div>";
			    	echo "</div>";
			    	
			    	echo "<div class='smallBubbleBG' style='background: url(".$displayInfo[$i]["cover_photo"]."); background-size: auto 134px; background-position: center;'></div>";
			    	
			    echo "</li>";
			}
			
			echo "<button onclick='shiftSmallBubbles(\"right\")' id='conveyorShift_prev'></button>";
			echo "<button onclick='shiftSmallBubbles(\"left\")' id='conveyorShift_next'></button>";
			
		echo "</section>";
    }
    
    function populateFollowBar($bubbleID)
    {
    	$bubbleInfo = generateQueryArray
    	("
    		SELECT DISTINCT		bubbleID_tag, accounts.name, tags_list.tag, nicknames.nickname
    		FROM				bubbles_connections
    							
    		LEFT JOIN			accounts
		    ON					bubbles_connections.bubbleID_tag  =  accounts.accountFacebookID
		    					
		    LEFT JOIN			tags_list
		    ON					bubbles_connections.bubbleID_tag  =  tags_list.bubbleID
		    					
		    LEFT JOIN			nicknames
		    ON					bubbles_connections.bubbleID_tag  =  nicknames.bubbleID
		    					
		    LEFT JOIN			places_current
		    ON					bubbles_connections.bubbleID_tag  =  places_current.bubbleID
		    					
		    WHERE				bubbleID_bubble		IN (SELECT bubbleID_child FROM bubbles_families WHERE bubbleID_parent = '$bubbleID')
		    					
		    AND					bubbleID_tag	NOT IN (SELECT bubbleID FROM places_current)
		    		    				    		    
    		ORDER BY			bubbleID_bubble
    	");
    	
    	echo "<ul class='relatedBubbles'>";
    	
    		$count__bubbleInfo  =   count($bubbleInfo);
    		for ($i = 0;    $i  <  $count__bubbleInfo;    $i++)
    		{
			 	if      (isset($bubbleInfo[$i]["nickname"]))	$insertWord  =  $bubbleInfo[$i]["nickname"];
			    else if (isset($bubbleInfo[$i]["name"]))		$insertWord  =  $bubbleInfo[$i]["name"];
			    else if (isset($bubbleInfo[$i]["tag"]))			$insertWord  =  $bubbleInfo[$i]["tag"];
			 	
			 	echo "<li onclick='switchBubble(\"".$bubbleInfo[$i]["bubbleID_bubble"]."\")'";
			    	//if ($bubbleInfo["bubbleID_bubble"] == $bubbleID) echo "class='selected'";			    
			    echo ">";
			    	echo $insertWord;
			    echo "</li>";
    		}
    		
		echo "</ul>";
    }
    
    function getBubbleQuery($select, $bubbleID, $order = "true")
    {
    	$bubbleQuery =
    	"
    		SELECT $select
		    FROM bubbles_front
		    LEFT JOIN events_info
		    ON bubbles_front.bubbleID_post = events_info.bubbleID
		    LEFT JOIN events_invited_count
		    ON events_info.bubbleID = events_invited_count.bubbleID
		    LEFT JOIN accounts
		    ON events_info.accountFacebookID_venue = accounts.accountFacebookID
		    LEFT JOIN posts
		    ON events_info.bubbleID = posts.bubbleID
		    WHERE bubbles_front.bubbleID_bubble = '$bubbleID'
		";
		    
		 if ($order == "true")  $bubbleQuery .=  "ORDER BY bubbles_front.rank_outer ASC";
		    
		 return $bubbleQuery;
    }
    
    
	function displayBubble($bubbleID, $start, $postLoad, $filter_type = "")
	{
		echo "<div id='shiftUpContainer'>";
    		echo "<img onclick='selectorClick()' id='shiftUp' src='img/mobile/shiftUp.png'/>";
    		
    		echo "<h1 id='shiftUp_bubbleName'>";
    			displaySmallestBubble($bubbleID);
    		echo "</h1>";
    	echo "</div>";
		
		// Resets session variables to make wookmark and time labels work
		if ($start == 0)
		{
		    $_SESSION["prevTimeframe"]  =  "";
		    $_SESSION["sectionNum"]     =  "";
		    $_SESSION["iteration"]      =  "";
		    
		    $_SESSION["sectionExists"]["now"]               =  0;
	        $_SESSION["sectionExists"]["today"]             =  0;
            $_SESSION["sectionExists"]["tonight"]           =  0;
      	    $_SESSION["sectionExists"]["tomorrow"]          =  0;
      	    $_SESSION["sectionExists"]["this week"]         =  0;
      	    $_SESSION["sectionExists"]["this weekend"]      =  0;
      	    $_SESSION["sectionExists"]["next week"]         =  0;
      	    $_SESSION["sectionExists"]["next weekend"]      =  0;
      	    $_SESSION["sectionExists"]["upcoming"]          =  0;
      	}
		
		
		$timeframeArray[0]               =  "now";
	    $timeframeArray[1]               =  "today";
	    $timeframeArray[2]               =  "tonight";
	    $timeframeArray[3]               =  "tomorrow";
	    $timeframeArray[4]               =  "this week";
	    $timeframeArray[5]               =  "this weekend";
	    $timeframeArray[6]               =  "next week";
	    $timeframeArray[7]               =  "next weekend";
	    $timeframeArray[8]               =  "upcoming";
	    
	    
	    $timeframeClass["now"]           =  "now";
	    $timeframeClass["today"]         =  "today";
	    $timeframeClass["tonight"]       =  "tonight";
	    $timeframeClass["tomorrow"]      =  "tomorrow";
	    $timeframeClass["this week"]     =  "this_week";
	    $timeframeClass["this weekend"]  =  "this_weekend";
	    $timeframeClass["next week"]     =  "next_week";
	    $timeframeClass["next weekend"]  =  "next_weekend";
		$timeframeClass["upcoming"]      =  "upcoming";
	
	
		if ($_SESSION["prevTimeframe"] == "")  $_SESSION["prevTimeframe"]  =  -1;
	    if ($_SESSION["sectionNum"]    == "")  $_SESSION["sectionNum"]     =  0;
		if ($_SESSION["iteration"]     == "")  $_SESSION["iteration"]      =  0;
	
	
		$postInfo     =  generateQueryArray
		("
			SELECT		*
			FROM		bubbles_front
			WHERE		bubbleID_bubble = '$bubbleID' 
			ORDER BY	rank_outer
			LIMIT		$start, $postLoad
		");
				
		if ($postInfo == "") die();
		
		
		foreach ($postInfo as $post)
	    {	    	
	    	if ($post["timeframe"] != $_SESSION["prevTimeFrame"])
	    	{
				$_SESSION["sectionNum"]++;
				
				$_SESSION["sectionExists"][$post["timeframe"]] = 1;
					
	    		echo "<div class='timeLabel' id='";
	    		
	    			echo "timeLabel_".$timeframeClass[$post["timeframe"]];
	    		
	    		echo "'>";
	    		    echo "<div class='headingLine_tile'></div>";
	    		    echo "<h2>".$post["timeframe"]."</h2>";
	    		    echo "<div class='headingLine_tile'></div>";
	    		echo "</div>";
	    	}
	    	
	    	echo "<li class='postBox mainPostBox ";
	    	
	    		for ($i = 0; $i <= 8; $i++)
	    		{
	    			if ($post["timeframe"] != $_SESSION["prevTimeFrame"])
	    			{
	    				echo "section_".$timeframeClass[$timeframeArray[$i]]."_first"." ";
	    			}
	    			
	    			echo "section_".$timeframeClass[$timeframeArray[$i]]." ";
	    			
	    			if ($timeframeArray[$i] == $post["timeframe"])
	    			{
	    				break;
	    			}
	    		}
	    	
	    	echo "' onclick=\"trackAction("."'User clicked the postBox in the main window to open the modal window for the post:'".", "."'".$post['bubbleID_post']."'"."); startModalLoader(); TINY.box.show({url:'php/ajax.php?do=getPostInfo&bubble=".$post['bubbleID_post']."&bubbleID=".$bubbleID."',boxid:'bubblebox',width:950,height:500,fixed:true,maskid:'texturemask',animate:false,openjs:function(){modalWindowOpenJS()},closejs:function(){modalWindowCloseJS()}});\">";
	    
	    		echo "<section class='postImageContainer'>";
	    			$pic_big = $post["post_pic_big"];
   					if ($pic_big == "http://profile.ak.fbcdn.net/static-ak/rsrc.php/v2/yn/r/5uwzdFmIMKQ.png" || $pic_big == "http://profile.ak.fbcdn.net/static-ak/rsrc.php/v2/y-/r/qXbA9JmRIZi.png")
   						$pic_big =  "img/other/defaultPost_mainWindow.jpg";
   						
	    			echo "<img class='lazy_$start postImage' data-original='".$pic_big."'>";
	    			
	    			echo "<div class='postInfoOverlay'>";
	    			
	    				echo "<img class='postIcon' src='img/phase2/ribbon_".$post["type"].".png'/>";
	    				
	    				echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
	    			 		echo "<h3 class='postName'>".$post["name"]."</h3>";
	    			   		echo "<h4 class='subtitle_".$post["type"]."'>".$post["subtitle"]."</h4>";
					    echo "</div></div>";
					    
					echo "</div>";
	    			
	    			echo "<div class='imageOverlay'></div>";
	    			echo "<div class='innerBorder'></div>";
	    		echo "</section>";
	    		
	    		echo "<section class='postInfoAndButtonsContainer'>";
		    		echo "<section class='postInfoContainer'>";
		    			if ($post["venue_name"] != "")
   	    				{
		    				echo "<section class='postInfoSection'>";
		    					echo "<div class='postInfoHeading'>";
		    						echo "<h5>venue</h5>";
		    						echo "<div class='headingLine'></div>";
		    					echo "</div>";
		    					
		    					if ($post["pic_square"] == "http://profile.ak.fbcdn.net/static-ak/rsrc.php/v2/y5/r/j258ei8TIHu.png")
		    						$post["pic_square"] =  "img/other/default_square.png";
		    					
		    					echo "<div class='pic_circle'>   <img class='lazy_$start postImage' data-original='".$post["venue_pic_square"]."' />   </div>";
		    					echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
			    					echo "<p class='accountName'>".$post["venue_name"]."</p>";
								echo "</div></div>";
								
								//echo "<div class='action'>follow</div>";
		    				echo "</section>";
		    			}
		    			else if ($post["location"] != "")
   	    				{
		    				echo "<section class='postInfoSection' style='min-height: 85px;'>";
		    					echo "<div class='postInfoHeading'>";
		    						echo "<h5>venue</h5>";
		    						echo "<div class='headingLine'></div>";
		    					echo "</div>";
		    				
		    					
		    					echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
			    					echo "<p class='accountName moreWidth'>".$post["location"]."</p>";
								echo "</div></div>";
		    				echo "</section>";
		    			}
		    			
		    			
		    			if ($post["price"] != "")
   	    				{
		    				echo "<section class='postInfoSection'>";
		    					echo "<div class='postInfoHeading'>";
		    						echo "<h5>deal</h5>";
		    						echo "<div class='headingLine' style='float: right; width: 193px;'></div>";
		    					echo "</div>";
		    				
		    					
		    					echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
			    					echo "<p class='accountName moreWidth'>"."$".$post["price"]." | ".$post["discount"]."% off"." | "."Value: $".$post["value"]."</p>";
								echo "</div></div>";
		    				echo "</section>";
		    			}
		    			
		    			if ($post["expires"] != "")
   	    				{
		    				echo "<section class='postInfoSection'>";
		    					echo "<div class='postInfoHeading'>";
		    						echo "<h5>expires</h5>";
		    						echo "<div class='headingLine' style='float: right; width: 175px;'></div>";
		    					echo "</div>";
		    				
		    					
		    					echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
			    					echo "<p class='accountName moreWidth'>"."In ".$post["expires"]."</p>";
								echo "</div></div>";
		    				echo "</section>";
		    			}
		    			
		    			if ($post["hours"] != "")
   	    				{
		    				echo "<section class='postInfoSection' style='height: 30px;'>";
		    					echo "<div class='postInfoHeading'>";
		    						echo "<h5>hours</h5>";
		    						echo "<div class='headingLine'></div>";
		    					echo "</div>";
		    					
		    					
		    					echo "<div style='position: relative; top: -15px;' class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
			    					echo "<p class='accountName moreWidth'>".$post["hours"]."</p>";
								echo "</div></div>";
								
		    				echo "</section>";
		    			}
		    			
		    			if ($post["street"] != "")
   	    				{
		    				echo "<section class='postInfoSection'>";
		    					echo "<div class='postInfoHeading'>";
		    						echo "<h5>where</h5>";
		    						echo "<div class='headingLine'></div>";
		    					echo "</div>";
		    					
		    					if ($post["pic_square"] == "http://profile.ak.fbcdn.net/static-ak/rsrc.php/v2/y5/r/j258ei8TIHu.png")
		    						$post["pic_square"] =  "img/other/default_square.png";
		    					
		    					echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
			    					echo "<p class='accountName moreWidth'>".$post["street"]."</p>";
			    					
			    					if ($post["city"] != "")
			    					{
			    						echo "<p class='accountName moreWidth'>";
			    							echo $post["city"];
			    							
			    							if ($post["state"] != "")
			    							{
			    								echo ", ".$post["state"];
			    							}
			    						echo "</p>";
			    					}
								echo "</div></div>";
								
								//echo "<div class='action'>map</div>";
		    				echo "</section>";
		    			}
		    			
		    			if ($post["atlanta_joins"] >= 5 && $post["person5"."_pic_square"] != "")
		    			{
		    				echo "<div class='headingLine'></div>";
		    				
		    				echo "<section class='postInfoSection postPeople'>";
		    				
		    					echo "<div class='peoplePics'>";

		    						for ($i = 0; $i < 6; $i++)
		    						{		   								
		   								echo "<div class='pic_circle' style='left: ".(35*$i)."px; z-index: ".(7-$i).";'>   <img class='lazy_$start postImage' data-original='".$post["person$i"."_pic_square"]."' />   </div>";
		    						}
		    						
		    					echo "</div>";
		    					
		    					echo "<p>";
		    						if ($post["emory_joins"] > 1)  echo $post["emory_joins"]    ." Emory students are going";
		    						else                           echo $post["atlanta_joins"]  ." people in Atlanta are going";
		    					echo "</p>";
		    					
		    					//echo "<div class='action'>join</div>";
		    				echo "</section>";
		    			}
		    			
		    			echo "<div class='headingLine'></div>";
		    			echo "<div class='headingLine'></div>";
		    		
		    		echo "</section>";
		    		
		    		echo "<section class='postButtonsContainer'>";
		    			
		    			echo "<button class='postButton commentsButton'>";
		    				echo "<img src='img/icons/icon_comments.png'/>";
		    				
		    				echo  $post["comments"];
		    				
		    			echo "</button>";
		    					    			
		    			echo "<button class='postButton joinsButton'>";
		    				echo "<img src='img/icons/icon_joins.png'/>";
		    				echo $post["atlanta_joins"];
		    			echo "</button>";
		    			
		    		echo "</section>";

		    	echo "</section>";
	    		    												
	    	echo "</li>";
	    	
	    	$_SESSION["prevTimeFrame"] = $post["timeframe"];
	    	//$_SESSION["iteration"]++;
	    	//$innerIteration++;
	    }
	    
	    //echo "</section>";
	  
	    echo "<script>";
	    
	    echo
	    "
	    	var section_now_exists           =  ".$_SESSION["sectionExists"]["now"].";
	    	var section_today_exists         =  ".$_SESSION["sectionExists"]["today"].";
            var section_tonight_exists       =  ".$_SESSION["sectionExists"]["tonight"].";
      		var section_tomorrow_exists      =  ".$_SESSION["sectionExists"]["tomorrow"].";
      		var section_this_week_exists     =  ".$_SESSION["sectionExists"]["this week"].";
      		var section_this_weekend_exists  =  ".$_SESSION["sectionExists"]["this weekend"].";
      		var section_next_week_exists     =  ".$_SESSION["sectionExists"]["next week"].";
      		var section_next_weekend_exists  =  ".$_SESSION["sectionExists"]["next weekend"].";
      		var section_upcoming_exists      =  ".$_SESSION["sectionExists"]["upcoming"].";
	    ";
	    
	    echo "</script>";
	  
	}

	function getPostInfo($id, $bubbleID)
	{
		$bubbleQuery     =  getBubbleQuery("posts.bubbleID", $bubbleID);
		
		
		$postArray  	 =  generateQueryArray_flat
		("
			SELECT		bubbleID_post
			FROM		bubbles_front
			WHERE		bubbleID_bubble = '$bubbleID' 
			ORDER BY	rank_outer
		");
				
		
		$infoArray       =  generateQueryArray
		("
		    SELECT 	posts.accountFacebookID, posts.bubbleID, posts.type, posts.name, posts.description AS event_description, posts.created_time, posts.pic_square, posts.pic_big,
		    		events_info.start_time, events_info.end_time, events_info.location,
		    		events_invited_count.count_attending, events_invited_count.count_declined, events_invited_count.count_unsure, events_invited_count.count_noreply,
		    		accounts.name AS venue_name, accounts.pic_square AS venue_pic_square,
		    		pages_info.type, pages_info.likes, pages_info.mission, pages_info.products, pages_info.description AS venue_description, pages_info.about, pages_info.street, pages_info.city, pages_info.state, pages_info.country, pages_info.zip		    		
		    				    		
		    FROM posts
		    LEFT JOIN events_info
		    ON posts.bubbleID = events_info.bubbleID
		    LEFT JOIN events_invited_count
		    ON posts.bubbleID = events_invited_count.bubbleID
		    LEFT JOIN accounts
		    ON events_info.accountFacebookID_venue = accounts.accountFacebookID
		    LEFT JOIN pages_info
		    ON events_info.accountFacebookID_venue = pages_info.accountFacebookID
		    WHERE  posts.bubbleID = '$id'
		    LIMIT  1
		");
		
		
		if ($infoArray[0]["type"] != "event")
		{
			$venueArray  =  generateQueryArray
			("
				SELECT		type, likes, about
				FROM		pages_info
				WHERE		accountFacebookID = '$id'
			");
		
			$hoursArray  =  generateQueryArray
			("
				SELECT		day,open,close
				FROM		pages_hours
				WHERE		accountFacebookID = '$id'
			");
			
			$dealsArray  =  generateQueryArray
			("
				SELECT		bubbles_front.expires, bubbles_front.subtitle, bubbles_front.price, bubbles_front.value, bubbles_front.discount, bubbles_front.venue_name, bubbles_front.venue_pic_square, bubbles_front.venue_accountFacebookID, bubbles_front.atlanta_joins, deals_info.url, deals_info.end_time
				FROM		bubbles_front
				LEFT JOIN	deals_info
				ON			bubbles_front.bubbleID_post = deals_info.bubbleID
				WHERE		bubbleID_post = '$id'
				LIMIT		1
			");
			
			if ($dealsArray[0]["venue_pic_square"] != "")
			{
				$infoArray[0]["venue_pic_square"]  =  $dealsArray[0]["venue_pic_square"];
				$infoArray[0]["venue_name"]        =  $dealsArray[0]["venue_name"];
			}
				
		}
				
		$commentArray = generateQueryArray
		("
			SELECT 	comments.bubbleID_parent, comments.description, comments.attachment, comments.attachment_type, comments.accountFacebookID, comments.likes, comments.created_time,
					accounts.name, accounts.pic_square
					
			FROM comments
			LEFT JOIN accounts
			ON comments.bubbleID_parent = accounts.accountFacebookID
			
			
			WHERE bubbleID_parent = '$id'
		");
		
		/*
			pages_info.latitude, pages_info.longitude, pages_info.phone,
		    pages_hours.day, pages_hours.open, pages_hours.close,
			pages_parking.parking_type
			
			LEFT JOIN pages_hours
		    ON posts.bubbleID = pages_hours.accountFacebookID
		    LEFT JOIN pages_parking
		    ON posts.bubbleID = pages_parking.accountFacebookID
		*/
		
		
		$numComments = count($commentArray);		
		
		$index     =  array_search($_GET["bubble"], $postArray);
		$lastPost  =  count($postArray) - 1;
		$firstPost =  0;
    	    		
    	echo "<section class='modalContainer' id='modalContainer'>";
    		if ($index < $lastPost)
    			echo "<button class='next changePost active' onclick='loadIntoModal( \"". $postArray[($index+1)] ."\" , \"". $_GET["bubbleID"] ."\" );'></button>" ;
    		else 
    			echo "<button class='next changePost inactive'></button>";
		
			if ($index > $firstPost)
				echo "<button class='prev changePost active' onclick='loadIntoModal( \"". $postArray[($index-1)] ."\" , \"". $_GET["bubbleID"] ."\" );' ></button>" ;
			else 
    			echo "<button class='prev changePost inactive'></button>";
			    		
   			echo "<li class='postBox modalPostBox' style='display: block;'>";
       			echo "<section class='postInfoAndButtonsContainer'>";
   	    			echo "<section class='postInfoContainer'>";
   	    				
   	    				echo "<section class='postInfoSection'>";
   	    				    echo "<div class='postInfoHeading'>";
   	    				    	echo "<h5>venue</h5>";
   	    				    	echo "<div class='headingLine'></div>";
   	    				    echo "</div>";
   	    				    
   	    				    if ($infoArray[0]["venue_pic_square"] != "")
   	    					{
   	    				    	if ($infoArray[0]["venue_pic_square"] == "http://profile.ak.fbcdn.net/static-ak/rsrc.php/v2/y5/r/j258ei8TIHu.png")
		    						$infoArray[0]["venue_pic_square"] =  "img/other/default_square.png";
   	    				    	
   	    				    	echo "<div class='pic_circle'>   <img src='".$infoArray[0]["venue_pic_square"]."'/>   </div>";
   	    				    }
   	    				    
   	    				    echo "<div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
   	    				    	if ($infoArray[0]["venue_name"] != "")
   	    				    	{
   	    				    		echo "<p class='accountName'>".$infoArray[0]["venue_name"]."</p>";
   	    				    	}
   	    				    	else if ($infoArray[0]["location"] != "")
   		    					{
   		    						echo "<p class='accountName'>".$infoArray[0]["location"]."</p>";
   		    					}
   			    		    echo "</div></div>";
   			    		    
   						    //echo "<div class='action'>follow</div>";
   						    
   	    				echo "</section>";
   	    				
   	    				echo "<div class='headingLine'></div>";
   	    				echo "<div class='headingLine'></div>";
   	    			
   	    			echo "</section>";
   	    			
   	    			echo "<section class='postButtonsContainer'>";
   	    				
   	    				echo "<button class='postButton commentsButton'>";
   	    					echo "<img src='img/icons/icon_comments.png'/>";
   	    					echo $numComments;
   	    				echo "</button>";
   	    				
   	    				echo "<button class='postButton joinsButton'>";
   	    					echo "<img src='img/icons/icon_joins.png'/>";
   	    					if ($infoArray[0]["count_attending"] != "")
   	    					{
   	    						echo $infoArray[0]["count_attending"];
   	    					}
   	    					else if ($venueArray[0]["likes"] != "")
   	    					{
   	    						echo $venueArray[0]["likes"];
   	    					}
   	    					else if ($dealsArray[0]["atlanta_joins"] != "")
   	    					{
   	    						echo $dealsArray[0]["atlanta_joins"];
   	    					}
   	    					
   	    				echo "</button>";
   	    				
   	    			echo "</section>";
   	    		echo "</section>";
   	   		echo "</li>";
   	   		
   			echo "<section class='infoContainer'>";
   				
   				echo "<div class='nameAndTimeContainer'>";
   					echo "<h1>".$infoArray[0]["name"]."</h1>";
   					echo "<h2>";
   						if ($infoArray[0]["start_time"] != "")
   						{
   							echo date("l, F jS | g:iA - ",$infoArray[0]["start_time"]).date("g:iA",$infoArray[0]["end_time"]);
   						}
   						else if ($venueArray[0]["type"] != "")
   						{
   							echo $venueArray[0]["type"];
   						}
   						else if ($dealsArray[0]["subtitle"] != "")
   						{
   							echo $dealsArray[0]["subtitle"];
   						}
   					echo "</h2>";
   				echo "</div>";
   				
   				echo "<section class='sideContainer' id='sideContainer'>";
   				//$hoursArray
   				/*
   					echo "<button class='going'>";
   						
   						echo "<div class='objectContainer'>";
   							echo "<img class='plus' src='img/modal/plus.png'/>";
   							echo "<div class='imGoing'>I'm going</div>";
   						echo "</div>";
   						
   					echo "</button>";
   				*/
   				   				
   				if ($dealsArray[0]["url"] != "")
   				{
	   				echo "<a onclick='trackLinkClick(\"".$dealsArray[0]["url"]."\")' href='".$dealsArray[0]["url"]."' target='_blank'>";
	   					echo "<button class='going'>";
   							
   							echo "<div class='objectContainer'>";
   								echo "<img class='plus' src='img/modal/plus.png'/>";
   								echo "<div class='imGoing'>get it</div>";
   							echo "</div>";
   							
   						echo "</button>";
   					echo "</a>";
   				}
   				else
   				{
   					echo "<div style='height: 19px; width: 100%'></div>"; //TEMPORARY UNTIL WE IMPLEMENT SIGN UPS
   				}
   					
   					$pic_big = $infoArray[0]["pic_big"];
   					if ($pic_big == "http://profile.ak.fbcdn.net/static-ak/rsrc.php/v2/yn/r/5uwzdFmIMKQ.png")
   						$pic_big =  "img/other/defaultPost_modalWindow.jpg";
   						
   					echo "<img class='postImage' src='".$pic_big."'/>";
   					
   					if (($infoArray[0]["venue_name"] != "" || $infoArray[0]["location"] != "") && $dealsArray[0]["price"] == "")
   					{
   						echo "<div class='additionalInfo'>";
   						
   							echo "<div class='postInfoHeading'>";
   		    				    echo "<h5>where</h5>";
   		    				    echo "<div class='headingLine'></div>";
   		    				echo "</div>";
   		    				
   		    				if ($infoArray[0]["venue_name"] != "")
   		    				{
   		    					if ($infoArray[0]["venue_name"] != "")  echo "<p>".$infoArray[0]["venue_name"]."</p>";
   		    					if ($infoArray[0]["street"] != "")      echo "<p>".$infoArray[0]["street"]."</p>";
   		    					
   		    					echo "<p>";
   		    						if ($infoArray[0]["city"] != "")    echo $infoArray[0]["city"];
   		    						if ($infoArray[0]["state"] != "")   echo ", ".$infoArray[0]["state"];
   		    						if ($infoArray[0]["zip"] != "")     echo ", ".$infoArray[0]["zip"];
   		    					echo "</p>";
   		    				}
   		    				else if ($infoArray[0]["location"] != "")
   		    				{
   		    					echo "<p>".$infoArray[0]["location"]."</p>";
   		    				}
   		    		
   						echo "</div>";
   					}
   					
   					if ($hoursArray != "")
   					{
   						echo "<div class='additionalInfo'>";
   						
   							echo "<div class='postInfoHeading'>";
   		    				    echo "<h5>hours</h5>";
   		    				    echo "<div class='headingLine'></div>";
   		    				echo "</div>";
   		    				
   		    				$count_hoursArray = count($hoursArray);
   		    				
   		    				for ($i = 0; $i < $count_hoursArray; $i++)
   		    				{
   		    					echo "<p>".date("D", strtotime($hoursArray[$i]["day"])).": ".date("g:ia", strtotime($hoursArray[$i]["open"]))." -- ".date("g:ia", strtotime($hoursArray[$i]["close"]))."</p>";
   		    				}
   		    		
   						echo "</div>";
   					}
   					
   					
   					if ($dealsArray[0]["price"] != "")
   					{
   						echo "<div class='additionalInfo'>";
   						
   							echo "<div class='postInfoHeading'>";
   		    				    echo "<h5>deal</h5>";
   		    				    echo "<div class='headingLine'></div>";
   		    				echo "</div>";
   		    				
   		    				echo "<p>"."$".$dealsArray[0]["price"]." | ".$dealsArray[0]["discount"]."% off"." | "."Value: $".$dealsArray[0]["value"]."</p>";
   		    				
   		    				echo "<div style='height: 30px'></div>";
   		    				
   		    				
   		    				echo "<div class='postInfoHeading'>";
   		    				    echo "<h5>expires</h5>";
   		    				    echo "<div class='headingLine' style='width: 105px'></div>";
   		    				echo "</div>";
   		    				
   		    				echo "<p>"."Expires on ".date("l, F jS",$dealsArray[0]["end_time"])." (in ".$dealsArray[0]["expires"].")"."</p>";
   		    		
   						echo "</div>";
   					}
   					
   				echo "</section>";
   				
   				echo "<section class='descriptionContainer'>";
   					$description = $infoArray[0]["event_description"];
   					$description = detectLinks($description);
   					echo "<pre id='postDescription'>";
   						echo $description;
   						   						   						
   						if ($venueArray[0]["about"] != "")
   						{
   							echo $venueArray[0]["about"];
   						}
   						
   					echo "<br/><br/></pre>";
   					
   				echo "</section>";
   				
   			echo "</section>";
   			
   			echo "<section class='commentDetailBox' id='modalCommentDetailBox'>";
   			
   				echo "<div id='commentContentWrapper'>";

   		    	for ($i = 0; $i < $numComments; $i++)
   		    	{
   		    	    if ($commentArray[$i]["description"] != "" && $commentArray[$i]["name"] != "" && $commentArray[$i]["pic_square"] != "")
   		    	    {
   		    	    	echo "<div class='postComment'>";
   		    	    	
   		    	    		echo "<div class='pic_circle'>   <img src='".$commentArray[$i]["pic_square"]."'/>   </div>";
   		    	    		
   		    	    		echo "<div class='nameAndDescriptionContainer'>";
   				    			echo "<h4 class='comment_accountName'>".$commentArray[$i]["name"]       ."</h4>";
   				    			$description = $commentArray[$i]["description"];
   								$description = detectLinks($description);
   				    			echo "<p class='comment_description'>" .$description."</p>";
   				    		echo "</div>";
   		    	    	echo "</div>";
   		    	    	
   		    	    	echo "<div class='headingLine'></div>";
   		    	    }
   		    	}
   		    	echo "<br/>";//prevents bottom of content from being cut off by scroll bar script
   		    	
   		    	echo "</div>";
   		           		    
   		    echo "</section>";
   		    
   		       			
		echo "</section>";
	}
	
	function displaySmallestBubble($leftID)
	{
		if ($leftID == "ee14bc9e-d5ed-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 2px;'>";
		    	echo "<div style='font-size: 16px;'>night</div>";
		    	echo "<div style='font-size: 24px; position: relative; top: -6px;'>life</div>";
		    echo "</div>";
		}
		else if ($leftID == "ee14c716-d5ed-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 1px;'>";
		    	echo "<div style='font-size: 14px;'>greek</div>";
		    	echo "<div style='font-size: 24px; position: relative; top: -6px;'>life</div>";
		    echo "</div>";
		}
		else if ($leftID == "ee14d044-d5ed-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 3px;'>";
		    	echo "<div style='font-size: 18px;'>emory</div>";
		    	echo "<div style='font-size: 11px; position: relative; top: -10px;'>academics</div>";
		    echo "</div>";
		}
		else if ($leftID == "ee14d968-d5ed-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 4px;'>";
		    	echo "<div style='font-size: 14px;'>atlanta</div>";
		    	echo "<div style='font-size: 15px; position: relative; top: -9px;'>sports</div>";
		    echo "</div>";
		}
		else if ($leftID == "ee14ab0a-d5ed-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 4px;'>";
		    	echo "<div style='font-size: 24px;'>get</div>";
		    	echo "<div style='font-size: 17px; position: relative; top: -5px;'>food</div>";
		    echo "</div>";
		}
		
		else if ($leftID == "138814bc-d5ee-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 3px;'>";
		    	echo "<div style='font-size: 10px;'>atlanta</div>";
		    	echo "<div style='font-size: 22px; position: relative; top: -3px;'>bars</div>";
		    echo "</div>";
		}
		else if ($leftID == "1385e7e6-d5ee-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 3px;'>";
		    	echo "<div style='font-size: 19px;'>night</div>";
		    	echo "<div style='font-size: 17px; position: relative; top: -4px;'>clubs</div>";
		    echo "</div>";
		}else if ($leftID == "2809077e-d5ef-11e1-b249-002590605566")
		{
		    echo "<div style='position: relative; top: 5px;'>";
		    	echo "<div style='font-size: 10px;'>concerts</div>";
		    	echo "<div style='font-size: 13px; position: relative; top: -12px;'>&shows</div>";
		    echo "</div>";
		}
		else if ($leftID == "713d4d27-ed49-11e1-bf61-aafbeaa37357")
		{
		    echo "<div style='position: relative; top: 3px;'>";
		    	echo "<div style='font-size: 13px;'>weekend</div>";
		    	echo "<div style='font-size: 13px; position: relative; top: -7px;'>escapes</div>";
		    echo "</div>";
		}
		else if ($leftID == "e099ace7-ed4b-11e1-bf61-aafbeaa37357")
		{
		    echo "<div style='position: relative; top: 6px;'>";
		    	echo "<div style='font-size: 10px;'>freshmen</div>";
		    	echo "<div style='font-size: 14px; position: relative; top: -11px;'>events</div>";
		    echo "</div>";
		}
		else if ($leftID == "49438594-ed4c-11e1-bf61-aafbeaa37357")
		{
		    echo "<div style='position: relative; top: 4px;'>";
		    	echo "<div style='font-size: 13px;'>campus</div>";
		    	echo "<div style='font-size: 14px; position: relative; top: -10px;'>events</div>";
		    echo "</div>";
		}
		else if ($leftID == "8475045d-ed4f-11e1-bf61-aafbeaa37357")
		{
		    echo "<div style='position: relative; top: 4px;'>";
		    	echo "<div style='font-size: 12px;'>student</div>";
		    	echo "<div style='font-size: 16px; position: relative; top: -8px;'>deals</div>";
		    echo "</div>";
		}
		else if ($leftID == "647659d4-ed50-11e1-bf61-aafbeaa37357")
		{
		    echo "<div style='position: relative; top: 3px;'>";
		    	echo "<div style='font-size: 13px;'>outdoor</div>";
		    	echo "<div style='font-size: 10px; position: relative; top: -8px;'>adventures</div>";
		    echo "</div>";
		}

	}
	
	function generateArrows($bubbleTag){
		global $topBubbleList;
		global $cover_photo;
			
		
		$bubbleInfo = generateQueryArray
    	("
    		SELECT 		bubbles_connections.bubbleID_bubble, bubbleID_tag, accounts.name, tags_list.tag, nicknames.nickname, places_current.type
    		FROM		bubbles_connections
    		
    		LEFT JOIN	accounts
		    ON			bubbles_connections.bubbleID_tag  =  accounts.accountFacebookID
		    
		    LEFT JOIN	tags_list
		    ON			bubbles_connections.bubbleID_tag  =  tags_list.bubbleID
		    
		    LEFT JOIN	nicknames
		    ON			bubbles_connections.bubbleID_tag  =  nicknames.bubbleID
		    
		    LEFT JOIN	places_current
		    ON			bubbles_connections.bubbleID_tag  =  places_current.bubbleID
		    
		    LEFT JOIN 	bubbles_list
		    ON			bubbles_connections.bubbleID_bubble = bubbles_list.bubbleID_bubble
		    
		    WHERE		bubbles_list.orderID > -1
		    AND			bubbleID_tag    NOT IN (SELECT bubbleID FROM places_current)
		    
    		ORDER BY	bubbles_list.orderID
    	");
    	
    	//flaten the array for searching
    		$numBubbles = count($bubbleInfo);
    		for($b = 0; $b < $numBubbles; $b++)
 			{
    			$bubbleList[$b] = $bubbleInfo[$b]["bubbleID_bubble"];
    		}
    	
    	$index = array_search($bubbleTag, $bubbleList);
    	
    	//calculate the indexes for the arrows
    		$rightIndex = $index + 1;
    		$leftIndex = $index - 1;
    		
    		if($leftIndex < 0)
    			$leftIndex = $numBubbles-1;
    		if($rightIndex >= $numBubbles)
    			$rightIndex = 0;
    	
    	$leftID		= $bubbleInfo[$leftIndex][0];
    	$leftName 	= $bubbleInfo[$leftIndex][3];
    	
    	
		echo "<button id='prevMain' class='prevMain changeBubble active' onclick='switchBubble(\"".$leftID."\")';>";
		
			echo 	"<li class='bubble'>
				   <div class='smallestBubble prevBubble'>
				       <div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
				           	echo "<h2>";
				           		displaySmallestBubble($leftID);
				           	echo "</h2>
				       </div></div>
				   </div>
				   <div class='smallestBubbleBG smallestBubbleBG_prev' style='background-image: url(".$cover_photo[$leftName]."); background-size: auto 97px; background-position: center;'></div>
				</li>";
				
		echo "</button>";
    	
    	
    	$rightID 	= $bubbleInfo[$rightIndex][0];
    	$rightName 	= $bubbleInfo[$rightIndex][3];
  		
		echo "<button id='nextMain' class='nextMain changeBubble active' onclick='switchBubble(\"".$rightID."\")';>";
		
			echo 	"<li class='bubble'>
				   <div class='smallestBubble nextBubble'>
				       <div class='verticalAlign_wrapper'><div class='verticalAlign_container'>";
				           	echo "<h2>";
				           		displaySmallestBubble($rightID);
				           	echo "</h2>
				       </div></div>
				   </div>
				   <div class='smallestBubbleBG smallestBubbleBG_next' style='background-image: url(".$cover_photo[$rightName]."); background-size: auto 97px; background-position: center;'></div>
				</li>";
				
		echo "</button>";
    	
	}
	
	
	function generateSlider()
	{				
			echo "<div class='verticalAlign_wrapper verticalAlign_wrapper_phase1'><div class='verticalAlign_container verticalAlign_container_phase1'>";
				
				echo "<section id='featuredContainer'>";
				echo "<div id='featured'>";
			
					$currentTime = date("H");
									
					generateSingleSlide
					("e099ace7-ed4b-11e1-bf61-aafbeaa37357", "Welcome freshmen. Get the rundown.", "Find everything an Emory freshman needs to know",
					  array
					  ("http://profile.ak.fbcdn.net/hprofile-ak-snc4/41566_131848706844940_8282_q.jpg",
					   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/373047_65545003959_1044067095_q.jpg",
					   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/373049_27087023869_732786793_q.jpg",
					   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/157979_175734356409_499653500_q.jpg")
					);
					
					if ($currentTime >= 18 || $currentTime <= 6)
					{		
						generateSingleSlide
						("ee14bc9e-d5ed-11e1-b249-002590605566", "Bored tonight? Go out.", "See concerts, clubs, and bars that matter to Emory students",
						  array
						  ("http://profile.ak.fbcdn.net/hprofile-ak-ash2/41605_128298896566_1433195272_q.jpg",
						   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/373018_100183642159_1333960991_q.jpg",
						   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/187859_218760225176_583529037_q.jpg",
						   "http://profile.ak.fbcdn.net/hprofile-ak-ash2/162032_24133094143_927846148_q.jpg")
						);
						
						generateSingleSlide
						("ee14ab0a-d5ed-11e1-b249-002590605566", "Cheap Eats. Open Now.", "Staples of Emory's late-night delivery food scene",
						  array
						  ("http://druid-hills.eat24hours.com/files/logo/8282.jpg?e24v=50",
						   "http://druid-hills.eat24hours.com/files/logo/3194.jpg?e24v=50",
						   "http://druid-hills.eat24hours.com/files/logo/8279.jpg",
						   "http://druid-hills.eat24hours.com/files/logo/6022.jpg")
						);
					}
					else
					{
						generateSingleSlide
						("49438594-ed4c-11e1-bf61-aafbeaa37357", "See what's happening on campus now.", "Whether it's your organization's event, a concert, or just some pickup soccer",
						  array
						  ("http://sphotos-a.xx.fbcdn.net/hphotos-ash3/7530_132767243557_127398_n.jpg",
						   "http://external.ak.fbcdn.net/safe_image.php?d=AQBK4Y2FsRIALlM2&w=180&h=540&url=http%3A%2F%2Fupload.wikimedia.org%2Fwikipedia%2Fen%2Fa%2Fae%2FEmory_University_Seal.png&fallback=hub_education&prefix=d",
						   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/174668_17750124821_77308860_q.jpg",
						   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/203536_16713069882_4374442_q.jpg")
						);
						
						generateSingleSlide
						("8475045d-ed4f-11e1-bf61-aafbeaa37357", "Great discounts for Emory students.", "Deals on food, nightlife, retail, events, and more",
						  array
						  ("http://profile.ak.fbcdn.net/hprofile-ak-snc4/157901_336291693114166_48056016_q.jpg",
						   "http://profile.ak.fbcdn.net/hprofile-ak-snc4/188095_109991049035002_1589219368_q.jpg",
						   "http://photos-a.ak.fbcdn.net/photos-ak-snc7/v85006/131/7829106395/app_1_7829106395_435245248.gif",
						   "http://photos-h.ak.fbcdn.net/photos-ak-snc7/v43/100/70858204300/app_1_70858204300_7128.gif")
						);
					}
			
				echo "</div>";
				echo "</section>";
				
			echo "</div></div>";
		
	}
		
		function generateSingleSlide($bubbleID_link, $title, $subtitle, $pic_circle_array)
		{
			echo "<div onclick='switchBubble(\"$bubbleID_link\"); trackAction(\"User clicked the slider to switch to the bubble:\", \"$bubbleID_link\");' class='content'>";
				echo "<h3>$title</h3>";
			    echo "<h4>$subtitle</h4>";
			    echo "<div class='slider_venuePicsContainer' style='margin: 0 auto; background-color:#000066; width: 155px; position:relative; top: 20px;'>";
			        echo "<div class='pic_circle' style='position: absolute; left: 0;'>     <img src='".$pic_circle_array[0]."'/>   </div>";
			        echo "<div class='pic_circle' style='position: absolute; left: 38px;'>  <img src='".$pic_circle_array[1]."'/>  </div>";
			        echo "<div class='pic_circle' style='position: absolute; left: 72px;'>  <img src='".$pic_circle_array[2]."'/>   </div>";
        	        echo "<div class='pic_circle' style='position: absolute; left: 110px;'> <img src='".$pic_circle_array[3]."'/>    </div>";
			     echo "</div>";
			echo "</div>";
		}
		
		
		function detectLinks($description)
		{
		
			$description = str_replace("https://", "http://", $description);
   			$description = str_replace("HTTP://", "http://", $description);
   	    	$description = preg_replace( "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/", '<a href="\0" target="_blank" onclick="trackLinkClick(\'\0\')">\0</a>', $description);
   	    	$description = str_replace('<a href="', '<a href="http://', $description);
   	    	$description = str_replace('http://http://', 'http://', $description);
		
		
			return $description;
		}
		
	
	
	

	function run_bubble_command($command)
	{		
		if      ($command == "populate"  )      displayBubble($_GET["bubble"], $_GET["start"], $_GET["postLoad"]);
		else if	($command == "getPostInfo")     getPostInfo($_GET["bubble"], $_GET["bubbleID"]);
		else if ($command == "generateArrows")  generateArrows($_GET["bubbleTag"]);
		//else if ($command == "followBar" )  populateFollowBar($_GET["bubble"]);
		else if ($command == "generateSlider")  generateSlider();
		else if ($command == "getSelector")     getSelector();
		else if ($command == "getBubbles")      getBubbles();
		
	}
	
	$postArray;
	run_bubble_command($_GET["do"]);
	
	if ( $_GET["do"] == "populate")
	{   ?>

		<script>
		
			var loadCount = <?php echo $_GET["start"]; ?>;
			
			$("img.lazy_"+loadCount).lazyload({ threshold : 500, effect : "fadeIn" });
		
			if(loadCount == 0) $("img.lazy_"+loadCount).trigger("appear");
		
		</script>

<?php } ?>
