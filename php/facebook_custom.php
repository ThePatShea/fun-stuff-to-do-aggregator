<?php
	
	// Gets the access token of the user with read_stream permission
		function stream_access_token()
		{
			$access_token  =  generateQuery_singleVar(" SELECT access_token FROM access_tokens WHERE accountFacebookID = '100004017743877' LIMIT 1 ");
			return $access_token;
		}
	
	
	//Adds a Facebook ID to the queue
		function add_to_queue($facebookID, $type)
		{
		    $sql_query = "INSERT INTO queue (facebookID, type) VALUES ('".addslashes($facebookID)."', '".addslashes($type)."')";
		    echo "<br>$sql_query<br>";
		    mysql_query($sql_query);
		}
	
	
	// Performs a batch call on the Facebook Graph API
		function graph_batch($idList, $query, $fql_name, $inc = 50)
		{
			// Defines initial variables
				$access_token      =  stream_access_token();
				$arraySize         =  count($idList);
			
			
			// Determines the current position of the queries in cron
				$current       =  generateQuery_singleVar("SELECT current FROM sync_queries WHERE name = '$fql_name' LIMIT 1");
				if ($current  >=  $arraySize - 1)  $current = 0;
						
			for( $ii = $current; $ii < $arraySize; $ii += $inc)
			{
				echo "<br>---graph---<br>";
				echo "Graph batch $ii of $arraySize";
				echo "<br>---graph---<br>";
				
				// Creates batch request for a group of users' events
					$batched_request = "";
				
			    	for( $i = 0; ($i < $inc && ($i+$ii)<$arraySize); $i++)
			    	{
			    	    if ($i != 0) { $batched_request .= ','; }
			    	  	$batched_request .= "{'method':'GET','relative_url':'".$idList[($i+$ii)]."$query'}";
			    	}
			    
			    
			    // Converts JSON returned from the batch request into an array
			    	$dataJSON			 =	cURL_standard("https://graph.facebook.com/?batch=[$batched_request]&access_token=$access_token&method=post");
			    	$tempArrayJSON		 =	json_decode($dataJSON, true);
			  		$tempArrayJSONCount  =	count($tempArrayJSON);
			  		
		
			    // Adds values to $infoArray
			    	$currentNum = 0;
			    
			    	for($b = 0; $b < $tempArrayJSONCount; $b++)
			    	{
			    		$temp2		= json_decode($tempArrayJSON[$b]["body"], true);
			    		$temp2Count = count($temp2["data"]);
			    		
			    		for ($a = 0; $a < $temp2Count; $a++) $infoArray[$currentNum++] = $temp2["data"][$a];
			    	}
			    
			    
			    // Adds the data from infoArray to the database
			    	mysql_query("UPDATE sync_queries SET current = '".($ii+$inc)."' WHERE name = '$fql_name'");
			    	
			    	$queryArray["fql_result_set"]  =  $infoArray;
					$queryArray["name"]			   =  "$fql_name"."_$ii";
				
					echo "<br>---graph $ii of $arraySize ---<br>";
					sync_sql($queryArray);
			}
			
			return $infoArray;
		}


	// Performs the specified FQL Query
		function fql_query($query, $access_token = "none")
		{
			global $app_access_token;
			global $facebook;
			
			if ($access_token == "none") $access_token = $app_access_token;
			
			$query      =  str_replace("\\", "", $query); // Removes backslashes from idList. They only work in multiQuery and are only needed there.
			
			try{
				$infoArray  =  $facebook->api(array('method' => 'fql.query', 'access_token' => $access_token, 'query' => $query));
			}
			catch (FacebookApiException $e)
			{
				echo "<br>----- Caught an error -----<br>";
				print_r($result);
				
				if($result["error_msg"] == "Service temporarily unavailable")
					remove_errors_from_query($query, $access_token);
				else
					die();
					
				
				echo "<br>----- Done handling the error -----<br>";

			}
			
			return $infoArray;
		}
	
	
		// Cycles through a given FQL query on the friends of everyone we have access tokens for
			function fql_friends($fqlQuery, $fqlTable, $fqlName)
			{
				// Selects all users who we have access tokens for
					$userList		 =	generateQueryArray("SELECT accountFacebookID, access_token FROM access_tokens");
					$count_userList	 =	count($userList);
				
				
				// Determines the current position of the queries in cron
					$current         =  generateQuery_singleVar("SELECT current FROM sync_queries WHERE name = '$fqlName' LIMIT 1");
					if ($current >= $count_userList - 1)  $current = 0;
				
				
				// Loops through each user, getting his friends' information
					for ($i = $current; $i < $count_userList; $i++)
					{
						// Updates current cron position
							mysql_query("UPDATE sync_queries SET current = '$i' WHERE name = '$fqlName'");
						
						
						// Creates a comma list of one user's friends
							$friendsCommaList	=	generateQueryCommaList
							("
								SELECT	connection_FacebookID
								FROM	users_connections
								WHERE	accountFacebookID	=	".$userList[$i]["accountFacebookID"]."
								AND		type				=	'friend'
							");
						
						
						// If the user has no friends listed, move on to the next user. This prevents an error.
							if ($friendsCommaList[0] == "") continue;
						
						
						// Gets the information of all the user's friends
							$infoArray = fql_query
							("
								SELECT	$fqlQuery
								FROM	$fqlTable
								WHERE	uid IN (".$userList[$i]["accountFacebookID"].",".$friendsCommaList[0].")",
								$userList[$i]["access_token"]
							);
							
														
						// Runs SQL queries to add info from FWL results to the database
							$queryArray["fql_result_set"]  =  $infoArray;
							$queryArray["name"]			   =  "$fqlName"."_$i";
							
							echo "<br>-----".$queryArray["name"]."-----<br>";
							
							sync_sql($queryArray);
					}
			}


	// Performs the specified FQL Multiquery
		function fql_multiquery($multiquery, $access_token = "none")
		{
			global $app_access_token;
			global $facebook;
			
			if ($access_token	==	"none") $access_token = $app_access_token;
			$multiquery			=	"{".$multiquery."}";
			
			$param = array
			(       
				"method"		=>	"fql.multiquery",
				"access_token"	=>	$access_token,   
				"queries"		=>	$multiquery
			);
			
			echo "<br>---multiQuery---<br>";
			print_r($multiquery);
			echo "<br>---multiQuery---<br>";
			
			try
			{
				$infoArray = $facebook->api($param);
			}
			catch (FacebookApiException $e)
			{
				echo "<br>----- Caught an error -----<br>";
				$error = $e->getResult();
								
				
				if($error['error_msg'] == "Service temporarily unavailable")
					remove_errors_from_multiquery($multiquery, $access_token);
				else if($error['error_msg'] == "An unknown error occurred" || $error['error_msg'] == "This API call could not be completed due to resource limits")
					$infoArray = split_and_merge($multiquery, $access_token);
				else
					die();
				
				echo "<br>----- Done fixing the error -----<br>";
			}
			
			if($infoArray == "")
				remove_errors_from_multiquery($multiquery, $access_token);
							
			return $infoArray;
		}
				
		
		// Given an array of queries, performs a multiquery containing all the queries in the array 
			function fql_multiquery_array($multiquery_array, $endingPoint, $access_token = "none")
			{
				$newLength		          =  strlen($multiquery_array[0]["name"]) - strlen(strrchr($multiquery_array[0]["name"],"_"));
				$nameWithoutNumber        =  substr($multiquery_array[0]["name"], 0, $newLength);
				
				$current                  =  generateQuery_singleVar("SELECT current FROM sync_queries WHERE name = '$nameWithoutNumber' LIMIT 1");
				$count__multiquery_array  =  count($multiquery_array);
				
				if ($current >= $endingPoint - 1)  $current = 0;
				
				$arrayEnd                 =  $current + $count__multiquery_array;
							
				for ($j = $current;  $j < $arrayEnd;  $j += $multiquery_array[0]["increment"])
				{
					for ($i = 0; ($i < $multiquery_array[0]["increment"]) && (($j + $i) < $arrayEnd); $i++)
					{
						$multiquery[$j] .= "'".$multiquery_array[($i + $j - $current)]["name"]."':'".addslashes($multiquery_array[($i + $j - $current)]["query"])."'".",";
					}
					
					set_time_limit(600); // Continuously sets a high timeout limit to prevent exceeding PHP resource limits
					
					$returnArray         =   fql_multiquery($multiquery[$j],$access_token);
					
					mysql_query("UPDATE sync_queries SET current = '$j' WHERE name = '$nameWithoutNumber'");
					
					$count__returnArray  =   count($returnArray);
					for ($k = 0;     $k  <  $count__returnArray;     $k++)
					{
						sync_sql($returnArray[$k]);
					}
					
				}
				
			}
		
		
			// Runs a set of SQL queries on an array of results from one FQL query
				function sync_sql($insertArray)
				{
					$newLength		     =  strlen($insertArray["name"]) - strlen(strrchr($insertArray["name"],"_"));
					$nameWithoutNumber   =  substr($insertArray["name"], 0, $newLength);
					$currentNumber       =  substr($insertArray["name"],$newLength+1,strlen(strrchr($insertArray["name"],"_")));
					
					$queryArray			 =	$insertArray["fql_result_set"];
					$count__queryArray   =   count($queryArray);
					for ($i = 0;    $i   <  $count__queryArray ;    $i++)
					{
						if ($nameWithoutNumber == "sync_users_basic_info")
						{
							$cols_array  =  array("accountFacebookID","name","pic_cover","pic_big","pic_square","accountFacebookUsername","email","type");
							$vars_array  =  array($queryArray[$i]["uid"],$queryArray[$i]["name"],$queryArray[$i]["pic_cover"]["source"],$queryArray[$i]["pic_big"],$queryArray[$i]["pic_square"],$queryArray[$i]["username"],$queryArray[$i]["email"],"user");
							sql_insert_update("accounts", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							$cols_array  =  array("accountFacebookID","first_name","middle_name","last_name","gender");
							$vars_array  =  array($queryArray[$i]["uid"],$queryArray[$i]["first_name"],$queryArray[$i]["middle_name"],$queryArray[$i]["last_name"],$queryArray[$i]["sex"]);
							sql_insert_update("users_info", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							mysql_query("UPDATE queue SET added = '1' WHERE facebookID = '".$queryArray[$i]["uid"]."'");
						}
						else if ($nameWithoutNumber == "sync_users_friends")
						{
							$cols_array  =  array("accountFacebookID","type","connection_facebookID","connection_bubbleID");
							$vars_array  =  array($queryArray[$i]["uid1"],"friend",$queryArray[$i]["uid2"],$queryArray[$i]["uid2"]);
							sql_insert_update("users_connections", $cols_array, $vars_array, "insert_only");
							
							add_to_queue($queryArray[$i]["uid2"],"user");
						}
						else if ($nameWithoutNumber == "sync_users_groups")
						{
							add_to_queue($queryArray[$i]["gid"],"group");
						}
						else if ($nameWithoutNumber == "sync_users_likes")
						{	
							$cols_array  =  array("accountFacebookID","type","connection_facebookID","connection_bubbleID");
							$vars_array  =  array($queryArray[$i]["uid"],"like",$queryArray[$i]["page_id"],$queryArray[$i]["page_id"]);
							sql_insert_update("users_connections", $cols_array, $vars_array, "insert_only");
							
							add_to_queue($queryArray[$i]["page_id"],"page");
						}
						else if ($nameWithoutNumber == "agg_events_from_users")
						{
							add_to_queue($queryArray[$i]["eid"],"event");
						}
						else if ($nameWithoutNumber == "sync_events_basic_info")
						{
							$cols_array  =  array("bubbleID","accountFacebookID","type","name","description","pic_square","pic_big","created_time");
							$vars_array  =  array($queryArray[$i]["eid"],$queryArray[$i]["creator"],"event",$queryArray[$i]["name"],$queryArray[$i]["description"],$queryArray[$i]["pic_square"],$queryArray[$i]["pic_big"],$queryArray[$i]["update_time"]);
							sql_insert_update("posts", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							$cols_array  =  array("bubbleID","postFacebookID","start_time","end_time","location","accountFacebookID_venue","privacy");
							$vars_array  =  array($queryArray[$i]["eid"],$queryArray[$i]["eid"],$queryArray[$i]["start_time"],$queryArray[$i]["end_time"],$queryArray[$i]["location"],$queryArray[$i]["venue"]["id"],$queryArray[$i]["privacy"]);
							sql_insert_update("events_info", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							$cols_array  =  array("bubbleID","count_attending","count_declined","count_unsure","count_noreply");
							$vars_array  =  array($queryArray[$i]["eid"], $queryArray[$i]["attending_count"], $queryArray[$i]["declined_count"], $queryArray[$i]["unsure_count"], $queryArray[$i]["not_replied_count"]);
							sql_insert_update("events_invited_count", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							add_to_queue($queryArray[$i]["venue"]["id"],"page");
							
							mysql_query("UPDATE queue SET added = '1' WHERE facebookID = '".$queryArray[$i]["eid"]."'");
						}
						else if ($nameWithoutNumber == "sync_events_invited")
						{
							$cols_array  =  array("bubbleID","accountFacebookID","rsvp_status");
							$vars_array  =  array($queryArray[$i]["eid"],$queryArray[$i]["uid"],$queryArray[$i]["rsvp_status"]);
							
							$cols_prime  =  array("accountFacebookID","bubbleID");
							$vars_prime  =  array($queryArray[$i]["uid"],$queryArray[$i]["eid"]);
							
							sql_insert_update("events_invited", $cols_array, $vars_array, $cols_prime, $vars_prime);
							
							add_to_queue($queryArray[$i]["uid"],"user");
						}
						else if ($nameWithoutNumber == "agg_comments")
						{
							add_to_queue($queryArray[$i]["post_id"],"comment");
						}
						else if ($nameWithoutNumber == "sync_comments")
						{
							$cols_array  =  array("bubbleID","bubbleID_parent","description","accountFacebookID","commentFacebookID","likes","created_time","attachment","attachment_type");
							$vars_array  =  array($queryArray[$i]["post_id"],$queryArray[$i]["source_id"],$queryArray[$i]["message"],$queryArray[$i]["actor_id"],$queryArray[$i]["post_id"],$queryArray[$i]["likes"]["count"],$queryArray[$i]["created_time"],$queryArray[$i]["attachment"]["media"][0]["src"],$queryArray[$i]["attachment"]["media"][0]["type"]);
							sql_insert_update("comments", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							mysql_query("UPDATE queue SET added = '1' WHERE facebookID = '".$queryArray[$i]["post_id"]."'");
						}
						else if ($nameWithoutNumber == "sync_subcomments")
						{
							$commentID   =  substr($queryArray[$i]["id"], 0, strpos($queryArray[$i]["id"],"_"));
							$parentID    =  generateQuery_singleVar("SELECT facebookID FROM queue WHERE facebookID LIKE '%_$commentID' LIMIT 1");
							$firstID	 =  substr($parentID, 0, strpos($parentID,"_"));
							$full_subID  =  $firstID."_".$queryArray[$i]["id"];
							
							
							$cols_array  =  array("bubbleID","bubbleID_parent","description","accountFacebookID","commentFacebookID","likes","created_time");
							$vars_array  =  array($full_subID,$parentID,$queryArray[$i]["text"],$queryArray[$i]["fromid"],$full_subID,$queryArray[$i]["likes"],$queryArray[$i]["time"]);
							sql_insert_update("comments", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							add_to_queue($full_subID,"subcomment");
							add_to_queue($queryArray[$i]["fromid"],"user");
							
							mysql_query("UPDATE queue SET added = '1' WHERE facebookID = '$full_subID'");
						}
						else if ($nameWithoutNumber == "sync_likes_from_comments_and_subcomments")
						{
							$cols_array  =  array("accountFacebookID","type","connection_facebookID","connection_bubbleID");
							$vars_array  =  array($queryArray[$i]["user_id"],"like",$queryArray[$i]["post_id"],$queryArray[$i]["post_id"]);
							sql_insert_update("users_connections", $cols_array, $vars_array, "insert_only");
							
							add_to_queue($queryArray[$i]["user_id"],"user");
						}
						else if ($nameWithoutNumber == "sync_groups_basic_info")
						{
							$cols_array  =  array("accountFacebookID","name","pic_big","pic_square","type");
							$vars_array  =  array($queryArray[$i]["gid"],$queryArray[$i]["name"],$queryArray[$i]["pic_big"],$queryArray[$i]["pic_square"],"group");
							sql_insert_update("accounts", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							$cols_array  =  array("accountFacebookID","description","privacy");
							$vars_array  =  array($queryArray[$i]["gid"],$queryArray[$i]["description"],$queryArray[$i]["privacy"]);
							sql_insert_update("groups_info", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							mysql_query("UPDATE queue SET added = '1' WHERE facebookID = '".$queryArray[$i]["gid"]."'");
						}
						else if ($nameWithoutNumber == "sync_groups_members")
						{
							$cols_array  =  array("accountFacebookID","type","connection_facebookID","connection_bubbleID");
							$vars_array  =  array($queryArray[$i]["uid"],"group",$queryArray[$i]["gid"],$queryArray[$i]["gid"]);
							sql_insert_update("users_connections", $cols_array, $vars_array, "insert_only");
							
							add_to_queue($queryArray[$i]["uid"],"user");
						}
						else if ($nameWithoutNumber == "sync_pages_basic_info")
						{
							$cols_array  =  array("accountFacebookID","name","pic_cover","pic_big","pic_square","accountFacebookUsername","type");
							$vars_array  =  array($queryArray[$i]["page_id"],$queryArray[$i]["name"],$queryArray[$i]["pic_cover"]["source"],$queryArray[$i]["pic_big"],$queryArray[$i]["pic_square"],$queryArray[$i]["username"],"page");
							sql_insert_update("accounts", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							$cols_array  =  array("accountFacebookID","mission","products","description","about","type","street","city","state","country","zip","latitude","longitude","phone","likes");
							$vars_array  =  array($queryArray[$i]["page_id"], $queryArray[$i]["mission"], $queryArray[$i]["products"], $queryArray[$i]["description"], $queryArray[$i]["about"], $queryArray[$i]["type"], $queryArray[$i]["location"]["street"], $queryArray[$i]["location"]["city"], $queryArray[$i]["location"]["state"], $queryArray[$i]["location"]["country"], $queryArray[$i]["location"]["zip"], $queryArray[$i]["location"]["latitude"], $queryArray[$i]["location"]["longitude"], $queryArray[$i]["phone"], $queryArray[$i]["fan_count"]);
							sql_insert_update("pages_info", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
							
							
							$count__categories  =   count($queryArray[$i]["categories"]);
							for ($j = 0;    $j  <  $count__categories;    $j++)
							{
								$cols_array  =  array("accountFacebookID","category");
								$vars_array  =  array($queryArray[$i]["page_id"],$queryArray[$i]["categories"][$j]["name"]);
								sql_insert_update("pages_categories", $cols_array, $vars_array, "insert_only");
							}
							
							//add venue info to posts table
								if ($queryArray[$i]["location"]["street"] != "")
								{
									$cols_array  =  array("bubbleID","accountFacebookID","type","name","description","pic_big","pic_square");
									$vars_array  =  array($queryArray[$i]["page_id"],$queryArray[$i]["page_id"],"venue",$queryArray[$i]["name"],$queryArray[$i]["description"],$queryArray[$i]["pic_big"],$queryArray[$i]["pic_square"]);
									sql_insert_update("posts", $cols_array, $vars_array, $cols_array[0], $vars_array[0]);
								}
								
														
							
							//add hours to database
								$temp_id = $queryArray[$i]["page_id"];
								mysql_query("DELETE FROM pages_hours WHERE accountFacebookID = '$temp_id'");
									
								$count__hours  =   count($queryArray[$i]["hours"]);
								$keys = array_keys($queryArray[$i]["hours"]);
								for ($j = 0;    $j  <  $count__hours;    $j+=2)
								{
									$day = substr($keys[$j],0,3);
									$cols_array  =  array("accountFacebookID","day","open","close");
									$vars_array  =  array($queryArray[$i]["page_id"],$day,$queryArray[$i]["hours"][($keys[$j])],$queryArray[$i]["hours"][($keys[($j+1)])]);
									sql_insert_update("pages_hours", $cols_array, $vars_array, "insert_only");
								}
								
							//add parking to the database
								$temp_id = $queryArray[$i]["page_id"];
								mysql_query("DELETE FROM pages_parking WHERE accountFacebookID = '$temp_id'");
								
								$keys = array_keys($queryArray[$i]["parking"]);
								for($p = 0; $p < 3; $p++){
									
									if($queryArray[$i]["parking"][($keys[$p])] == 1){
										$key = $keys[$p];
										mysql_query("INSERT INTO pages_parking (accountFacebookID,parking_type) VALUES ('$temp_id','$key')");
									}
								}
								
								
							
							
							mysql_query("UPDATE queue SET added = '1' WHERE facebookID = '".$queryArray[$i]["page_id"]."'");
						}
						else if ($nameWithoutNumber == "sync_users_education")
						{
							$count__education   =   count($queryArray[$i]["education"]);
							for ($j = 0;    $j  <  $count__education;    $j++)
							{
								$cols_array  =  array("accountFacebookID","type","connection_facebookID","connection_bubbleID");
								$vars_array  =  array($queryArray[$i]["uid"],$queryArray[$i]["education"][$j]["type"],$queryArray[$i]["education"][$j]["school"]["id"],$queryArray[$i]["education"][$j]["school"]["id"]);
								sql_insert_update("users_connections", $cols_array, $vars_array, "insert_only");
								
								add_to_queue($queryArray[$i]["education"][$j]["school"]["id"],"page");
							}
						}
						else if ($nameWithoutNumber == "agg_events_from_pages_and_groups")
						{
							add_to_queue($queryArray[$i]["id"],"event");
						}
					}
				}
			 	
				
				// Checks if an ID already exists in an FQL table. If so, updates. If not, inserts.
				    function sql_insert_update($table, $cols_array, $vars_array, $cols_primary, $vars_primary = "")
				    {
				    	if (count($cols_primary) > 1)
				    	{
				    		$count__cols_primary  =   count($cols_primary);
				    		for ($i = 0;      $i  <  $count__cols_primary ;    $i++)
				    		{
				    			if ($i != 0) $exist_query  .=  " AND ";
				    			$exist_query  .=  $cols_primary[$i] . " = " . "'". addslashes($vars_primary[$i]) ."' ";
				    		}
				    		
				    		$exist_check  =  generateQuery_singleVar("SELECT count(*) FROM $table WHERE $exist_query");
				    	}
				    	else if ($cols_primary != "insert_only")
				    	{
				    		$exist_check  =  generateQuery_singleVar("SELECT count(*) FROM $table WHERE $cols_primary = '$vars_primary'");
				    	}
				    	
				    	if ($exist_check > 0 && $cols_primary != "insert_only")
				    	{
				    		$sql_query   =  "UPDATE $table SET ";
				    		
				    		$count__cols_array  =   count($cols_array);
				    		for ($i = 0;    $i  <  $count__cols_array ;    $i++)
				    		{
				    			if ($i != 0) $sql_query  .=  ", ";
				    			$sql_query  .=  $cols_array[$i] . " = " . "'". addslashes($vars_array[$i]) ."' ";
				    		}
				    		
				    		$sql_query  .=  "WHERE $cols_primary = '$vars_primary'";
				    	}
				    	else
				    	{
				    		$cols_comma  =  generateCommaList($cols_array, 0, "false", "true", "true");
				    		$vars_comma  =  generateCommaList($vars_array, 0, "true" , "true", "true");
				    		$sql_query   =  "INSERT INTO $table ($cols_comma) VALUES ($vars_comma)";
				    	}
				    	
				    	echo "<br>$sql_query<br>";
				    	
				    	mysql_query($sql_query);
				    }
		
		
			// Given an array of queries split into sections, performs a multiquery containing all the queries in the array 
				function fql_multiquery_splitArray($multiquery_splitArray, $access_token = "none", $insertArrayPos = 0)
				{
					$count__splitArray  =   count($multiquery_splitArray);
					for ($i = 0;    $i  <  $count__splitArray; $i++)
					{
						$multiquery_array[$i]["name"]	 =	$multiquery_splitArray[$i]["name"];
						$multiquery_array[$i]["query"]	.=	" SELECT ".$multiquery_splitArray[$i]["fql_vars"]." FROM ".$multiquery_splitArray[$i]["fql_table"]." WHERE ";
						
						$current              =  generateQuery_singleVar("SELECT current FROM sync_queries WHERE name = '".$multiquery_splitArray[$i]["name"]."' LIMIT 1");
						$vars                 =  get_string_between($multiquery_splitArray[$i]["fql_where"], "IN (", ")");
						$idList				  =  idList($vars,$multiquery_splitArray[$i]["division_line"],$multiquery_splitArray[$i]["increment"],$current);
						$inCommaArray         =  $idList["entries"];
						
						$count__inCommaArray  =   count($inCommaArray);
						for ($j = 0;      $j  <  $count__inCommaArray ;   $j++)
						{
						    $newQuery[$j] = str_replace("$vars", $inCommaArray[$j], $multiquery_splitArray[$i]["fql_where"]);
						    
						    $multiquery_array_insert[$insertArrayPos]["name"]		=  $multiquery_splitArray[$i]["name"]."_$j";
						    $multiquery_array_insert[$insertArrayPos]["increment"]	=  $multiquery_splitArray[$i]["increment"];
						    $multiquery_array_insert[$insertArrayPos++]["query"]	=  $multiquery_array[$i]["query"].$newQuery[$j];
						}
					}
					
					fql_multiquery_array($multiquery_array_insert, $idList["count"], $access_token);
				}
			
			
			// Given an sql query to call an array of fql queries, performs a multiquery containing all the queries in the array 
				function fql_multiquery_sql($sqlQuery, $access_token = "none")
				{
					$multiquery_splitArray	=  generateQueryArray($sqlQuery);
					$infoArray				=  fql_multiquery_splitArray($multiquery_splitArray,$access_token);
					
					return $infoArray;
				}
		
		
		// Selects a set of FQL queries from the database, then runs them all
			function fql_friends_sql($sqlQuery)
			{
				$queryArray			=	generateQueryArray($sqlQuery);
				$count_queryArray	=	count($queryArray);
				
				for ($i = 0; $i < $count_queryArray; $i++) fql_friends($queryArray[$i]["fql_vars"],$queryArray[$i]["fql_table"],$queryArray[$i]["name"]);
			}
		
		
		// Selects a set of Graph API batch calls from the database, then runs them all
		    function graph_sql($sqlQuery)
		    {
		    	$queryArray		    =   generateQueryArray($sqlQuery);
		    	
		    	$count__queryArray  =   count($queryArray);
		    	for ($i = 0;    $i  <  $count__queryArray ;    $i++)
		    	{
		    		$vars           =  get_string_between($queryArray[$i]["fql_where"], "IN (", ")");
		    		$idList         =  generateQueryArray_flat("SELECT facebookID FROM queue WHERE type IN ($vars)");
		    								
		    		graph_batch($idList,$queryArray[$i]["fql_vars"],$queryArray[$i]["name"]);
		    	}
		    }
		
		
		// Uses a set of FQL and SQL queries to sync Facebook info to Campus Bubble where we can use the app access token
			function sync_query($inputQuery, $type)
			{
			    $query = "SELECT * FROM sync_queries WHERE name = '$inputQuery'";
			    
				if      ($type == "full"   )   fql_multiquery_sql($query);
				else if ($type == "stream" )   fql_multiquery_sql($query,stream_access_token());
				else if ($type == "friends")   fql_friends_sql($query);
				else if ($type == "graph")     graph_sql($query);
			}
	
	
	// Syncs whichever set of info you specify from Facebook
		function sync_facebook($set)
		{
			if ($set == "sync_pages_groups_and_events_basic_info")
			{
				sync_query("sync_events_basic_info" , "full"   );
				sync_query("sync_groups_basic_info" , "full"   );
				sync_query("sync_pages_basic_info"  , "full"   );
			}
			else if ($set == "agg_events_from_pages_and_groups")
			{
				sync_query("agg_events_from_pages_and_groups" , "graph");
			}
			else if ($set == "sync_users_connections")
			{
				sync_query("sync_users_friends"     , "full"   );
				sync_query("sync_users_education"   , "friends");
				sync_query("sync_users_groups"      , "friends");
				sync_query("sync_users_likes"       , "friends");
				sync_query("agg_events_from_users"  , "friends");
			}
			else if ($set == "sync_groups_members")
			{
				sync_query("sync_groups_members"    , "full");
			}
			else if ($set == "sync_users_basic_info")
			{
				sync_query("sync_users_basic_info"  , "full");
			}
			else if ($set == "sync_events_invited")
			{
				sync_query("sync_events_invited"    , "full");
			}
			else if ($set == "sync_feed")
			{
				sync_query("sync_comments"          , "stream");
				sync_query("sync_subcomments"       , "full"  );
				sync_query("sync_likes_from_comments_and_subcomments" , "full");
			}
			else if ($set == "agg_comments")
			{
				sync_query("agg_comments"           , "stream");
			}
		}		
	
?>