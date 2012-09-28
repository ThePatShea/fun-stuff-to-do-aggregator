<?php

	include_once("../../php/background.php");

	function storeSortedEvents($events, $bubbleID_bubble)
	{
		foreach($events as $post)
       		{
			$commentsCount = 0;
			
       			mysql_query
                    	("
                        	INSERT INTO             bubbles_front_new_temp
                        	(`bubbleID_bubble`, `bubbleID_post`, `timeframe`, `comments`, `emory_joins`, `score`, `person0_accountFacebookID`, `person1_accountFacebookID`, `person2_accountFacebookID`, `person3_accountFacebookID`, `person4_accountFacebookID`, `person5_accountFacebookID`, `person0_name`, `person1_name`, `person2_name`, `person3_name`, `person4_name`, `person5_name`, `person0_pic_square`, `person1_pic_square`, `person2_pic_square`, `person3_pic_square`, `person4_pic_square`, `person5_pic_square`, `type`, `name`, `venue_name`, `venue_pic_square`,  `venue_accountFacebookID`, `subtitle`, `atlanta_joins`, `post_pic_big`, `location`, `start_time`)

                        	VALUES
                        	( '".$bubbleID_bubble."', '".$post["bubbleID"]."', '".$post["timeframe"]."', '".$commentsCount."', '".$emoryAttendees."', '".$post["score"]."', '".$post["person0"."_accountFacebookID"]."', '".$post["person1"."_accountFacebookID"]."', '".$post["person2"."_accountFacebookID"]."', '".$post["person3"."_accountFacebookID"]."', '".$post["person4"."_accountFacebookID"]."', '".$post["person5"."_accountFacebookID"]."', '".$post["person0"."_name"]."', '".$post["person1"."_name"]."', '".$post["person2"."_name"]."', '".$post["person3"."_name"]."', '".$post["person4"."_name"]."', '".$post["person5"."_name"]."', '".$post["person0"."_pic_square"]."', '".$post["person1"."_pic_square"]."', '".$post["person2"."_pic_square"]."', '".$post["person3"."_pic_square"]."', '".$post["person4"."_pic_square"]."', '".$post["person5"."_pic_square"]."', '".$post["type"]."', '".$post["post_name"]."', '".$post["account_name"]."', '".$post["pic_square"]."', '".$post["venue_accountFacebookID"]."', '".date("M j | g:iA",$post["start_time"])."', ".$post["count_attending"].", '".$post["pic_big"]."', '".$post["location"]."', '".$post["start_time"]."')
                    	");
        	}
	}	

	



	function generateBubblePosts($where_clause, $bubbleID_bubble)
        {
                $events = generateQueryArray
                ("
			SELECT posts.name AS post_name, posts.pic_big, posts.type, location, posts.bubbleID, start_time, end_time, location, accounts.pic_square, accounts.name AS account_name, events_invited_count.count_attending, accounts.accountFacebookID AS venue_accountFacebookID
               	     	FROM events_info
                    	LEFT JOIN events_invited_count
                   	ON events_info.bubbleID = events_invited_count.bubbleID
                    	LEFT JOIN accounts
                    	ON events_info.accountFacebookID_venue = accounts.accountFacebookID
			LEFT JOIN pages_info
			ON accounts.accountFacebookID = pages_info.accountFacebookID
                    	LEFT JOIN posts
                    	ON events_info.bubbleID = posts.bubbleID
                        WHERE           $where_clause
                        AND             events_info.start_time > ".time()."
                        AND             events_info.start_time < ".strtotime("12/15/2012")."
                        ORDER BY        start_time ASC
                ");

                storeSortedEvents($events, $bubbleID_bubble);
        }	







	function generateBubblePosts_studentDeals()
        {
                    $bubbleID_bubble  =  "8475045d-ed4f-11e1-bf61-aafbeaa37357";


                    $currentTime  =  time();

                    $deals  =  generateQueryArray_flat
                    ("
                        SELECT          posts.bubbleID
                        FROM            posts
                        LEFT JOIN       deals_info
                        ON              posts.bubbleID       =  deals_info.bubbleID
                        WHERE           type                 =  'deal'
                        AND             deals_info.end_time  >  $currentTime
                        ORDER BY        deals_info.end_time     ASC
                    ");


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

                                if ($numDays == 0)
                                {
                                        $expires   =  "less than 1 day";
                                        $subtitle  =  "expires tonight";
                                }

                        $commentsCount = 0;

                        if ($atlanta_joins == "") $atlanta_joins = 0;

                        // Inserts deals into table with really high score so they always so up first in their section
                                mysql_query
                                ("
                                        INSERT INTO             bubbles_front_new_temp
                                        (bubbleID_bubble, bubbleID_post, timeframe, score, type, name, post_pic_big, price, value, discount, expires, subtitle, atlanta_joins, venue_accountFacebookID, venue_name, venue_pic_square, comments)

                                        VALUES
                                        ('$bubbleID_bubble', '".$deals[$i]."', 'tomorrow', 100000, 'deal', '".$postInfo[0]["name"]."', '".$postInfo[0]["pic_big"]."', '".$dealsInfo[0]["price"]."', '".$dealsInfo[0]["value"]."', '".$dealsInfo[0]["discount"]."', '$expires', '$subtitle', '$atlanta_joins', '".$dealVenueInfo[0]["accountFacebookID"]."', '".$dealVenueInfo[0]["name"]."', '".$dealVenueInfo[0]["pic_square"]."', $commentsCount)
                                ");

                   }
        } 





/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function generateBubblePosts_campusEvents()
	{
		generateBubblePosts("accountFacebookID = 160908570699694", "49438594-ed4c-11e1-bf61-aafbeaa37357");	

		generateBubblePosts("posts.description LIKE '% Emory%'", "49438594-ed4c-11e1-bf61-aafbeaa37357");	
		generateBubblePosts("posts.description LIKE 'Emory%'", "49438594-ed4c-11e1-bf61-aafbeaa37357");
	
		generateBubblePosts("posts.name LIKE '% Emory%'", "49438594-ed4c-11e1-bf61-aafbeaa37357");	
		generateBubblePosts("posts.name LIKE 'Emory%'", "49438594-ed4c-11e1-bf61-aafbeaa37357");
	}
	
	function generateBubblePosts_concerts()
	{
		generateBubblePosts("posts.description LIKE '%concert%' AND pages_info.state = 'GA'", "2809077e-d5ef-11e1-b249-002590605566");
		generateBubblePosts("posts.name LIKE '%concert%' AND pages_info.state = 'GA'", "2809077e-d5ef-11e1-b249-002590605566");
		generateBubblePosts("pages_info.type LIKE '%concert%' AND pages_info.state = 'GA'", "2809077e-d5ef-11e1-b249-002590605566");	
	}

	function generateBubblePosts_nightLife()
        {
                generateBubblePosts("pages_info.type LIKE '%night%' AND pages_info.city = 'Atlanta'", "ee14bc9e-d5ed-11e1-b249-002590605566");
		generateBubblePosts("posts.description LIKE '%party%' AND pages_info.city = 'Atlanta'", "ee14bc9e-d5ed-11e1-b249-002590605566");
		generateBubblePosts("posts.name LIKE '%party%' AND pages_info.city = 'Atlanta'", "ee14bc9e-d5ed-11e1-b249-002590605566");
        }	

	function generateBubblePosts_atlantaSports()
        {
                generateBubblePosts("pages_info.type LIKE '%sport%' AND pages_info.city = 'Atlanta'", "ee14d968-d5ed-11e1-b249-002590605566");
        }

	function generateBubblePosts_greekLife()
        {
                generateBubblePosts("posts.description LIKE '%greek%' AND pages_info.city = 'Atlanta'", "ee14c716-d5ed-11e1-b249-002590605566");
		generateBubblePosts("posts.description LIKE '%frat%' AND pages_info.city = 'Atlanta'", "ee14c716-d5ed-11e1-b249-002590605566");
		generateBubblePosts("posts.description LIKE '%sorority%' AND pages_info.city = 'Atlanta'", "ee14c716-d5ed-11e1-b249-002590605566");
        }

	function generateBubblePosts_artsAndMusic()
        {       
		generateBubblePosts("posts.description LIKE '% art%' AND pages_info.city = 'Atlanta'", "e099ace7-ed4b-11e1-bf61-aafbeaa37357");
	}


	function generateBubblePosts_getFood()        
	{       
                generateBubblePosts("posts.description LIKE '%food%' AND pages_info.city = 'Atlanta'", "ee14ab0a-d5ed-11e1-b249-002590605566");
		generateBubblePosts("pages_info.type LIKE '%restaurant%' AND pages_info.city = 'Atlanta'", "ee14ab0a-d5ed-11e1-b249-002590605566");
		generateBubblePosts("pages_info.type LIKE '%food%' AND pages_info.city = 'Atlanta'", "ee14ab0a-d5ed-11e1-b249-002590605566");
        }


	mysql_query("DELETE FROM bubbles_front_new_temp WHERE 1");	

	generateBubblePosts_getFood();	
	generateBubblePosts_concerts();
	generateBubblePosts_greekLife();
	generateBubblePosts_nightLife();
	generateBubblePosts_campusEvents();
	generateBubblePosts_studentDeals();
	generateBubblePosts_atlantaSports();
	generateBubblePosts_artsAndMusic();
?>
