<?php

	// Universal commands
		// Sets timezone
			ini_set('date.timezone', 'America/New_York');		

		// Activates session variables
			session_start();
		
		// Connects to the database
			mysql_connect("bubbledata.cuhe5ywnmpir.us-east-1.rds.amazonaws.com", "campus", "F302pinpulse");
			mysql_select_db("bubbleDataSandbox5");
		
		// Sets the top bubbles(no longer needed)
			$topBubbleArray  =  generateQueryArray_flat
			("
				SELECT		bubbleID_bubble
				FROM		bubbles_list
				WHERE		orderID > -1
			");
		

			$topBubbleList  =  generateCommaList($topBubbleArray, 0, "true", "true");

	//Elegant functions
		function cURL_standard($url) { $ch = curl_init($url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $curl_scraped_page = curl_exec($ch); curl_close($ch); $data = $curl_scraped_page; return $data; }
		function get_string_between($string, $start, $end) { $string	= " ".$string; $ini = strpos($string, $start); if ($ini == 0) { return ""; } $ini += strlen($start); $len = strpos($string, $end, $ini) - $ini; return substr($string, $ini, $len); }
		function stricount($s, $n) { $st = " ".$s; $i = 0; $c = 0; while(stripos($st, $n, $i) != "") { $i	= stripos($st, $n, $i)+1; $c++; } return $c; }
	
		
	// Returns an array of information from the database
		function generateQueryArray($query)
		{
			$result = mysql_query($query);
			
			$i = 0;
			
			while ($row = mysql_fetch_array($result))
			{
				$queryArray[$i] = $row;
				$i++;
			}
			
			return ($queryArray);
		}
	
	
	// Returns an array of information from the database with only one parameter
		function generateQueryArray_flat($query)
		{
			$result = mysql_query($query);
			
			$i = 0;
			
			while ($row = mysql_fetch_array($result))
			{
				$queryArray[$i] = $row[0];
				$i++;
			}
			
			return ($queryArray);
		}
		
	
	// Returns an array of information from the database with only one parameter
		function generateQuery_singleVar($query)
		{
			$queryArray      =       generateQueryArray_flat($query);
			return $queryArray[0];
		}
		
		
	// Returns an array of comma separated lists of information from the database with only one parameter
		function generateQueryCommaList($query, $division_line = 0, $type = "full", $quotes = "true")
		{
			$array           =       generateQueryArray_flat($query);
			$commaList       =       generateCommaList($array, $division_line, $quotes);
			
			if   ($type == "flat")   return $commaList[0];
			else				     return $commaList;
		}	
	
	
	// Turns a flat array into a set of comma separated lists. Prevents dividing by 0 if $div = 0.
		function generateCommaList($array, $div = 0, $quotes = "false", $flat = "false", $slashes = "false")
		{
			$count__array   =  count($array);
			for ($i = 0; $i < $count__array ; $i++)
			{
				if  ($div     !=      0) { $num   =   floor($i/$div); $chk   =   $i % $div; }
				else                     { $num   =                0; $chk   =   $i       ; }
			    
			    if  ($chk     !=      0)   $commaList[$num]  .=  ",";
			    						   
			    if  ($quotes  == "true")   $commaList[$num]  .=  "'";
			    						   
			    if  ($slashes == "true")   $commaList[$num]  .=  addslashes($array[$i]);
			    else                       $commaList[$num]  .=	            $array[$i] ;
			    						   
			    if  ($quotes  == "true")   $commaList[$num]  .=  "'";
			 }
				
				if  ($flat    == "true")   return $commaList[0];
				else                       return $commaList   ;
		}
	
	
	// Generates a array of comma lists for all IDs from the queue, sorted by type
		function idList($vars, $division_line, $increment, $current = 0)
		{
			if       ($vars  ==  "'subcomments_trn'")   $idList["entries"]  =  idList_comments_truncated("subcomment", $division_line);
			else if  ($vars  ==  "'comments_trn'"   )   $idList["entries"]  =  idList_comments_truncated("comment"   , $division_line);
			else if  ($vars  ==  "'users_reg'"      )   $idList["entries"]  =  generateQueryCommaList("SELECT accountFacebookID FROM access_tokens"       , $division_line);
			else
			{
				$idList["count"]    =  generateQuery_singleVar("SELECT count(*) FROM queue WHERE type IN ($vars) AND added != -1");
				$startingPoint      =  ($division_line)*($current - $increment);
				
				if($startingPoint < 0) $startingPoint = 0;
								
				$idList["entries"]  =  generateQueryCommaList("SELECT facebookID FROM queue WHERE type IN ($vars) AND added != -1 LIMIT $startingPoint, 100000", $division_line);
			}
			
			return $idList;
		}
		
		
		// Function idList_comments_truncated
			function idList_comments_truncated($commentType, $division_line)
			{
				$idArray		      =  generateQueryArray_flat("SELECT facebookID FROM queue WHERE type = '$commentType' AND added != -1");
				
				$count__idArray       =  count($idArray);
				for ($i = 0; $i       <  $count__idArray; $i++)
				{
					$comment_trn      =  substr($idArray[$i], strpos($idArray[$i],"_")+1, strlen($idArray[$i]) );
					$idArray_trn[$i]  =  $comment_trn;
				}
				
				$idList               =  generateCommaList($idArray_trn, $division_line, "true");
				
				return $idList;
			}
		
		
		// Searches a multidimsensional array for specified values
			function searchForId($id, $array, $currentComment = 0)
			{
			   foreach ($array as $key => $val)
			   {
			       if ($val["bubbleID_parent"] === $id)
			       {
			       		$infoArray[$currentComment++] = $key;
			       }
			   }
			   
			   return $infoArray;
			}

?>
