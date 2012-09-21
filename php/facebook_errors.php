<?php
		

		function remove_errors_from_multiquery($multiquery, $access_token){
			
			echo "<br/><br/>finding errors in this multiquery:<br/>".$multiquery;
			
			$errorArray = array();
			
		//distribute the multi query into an array of single queries
			$queries = explode(':', $multiquery);
			$queries[0] = null;
			
		//loop through each query and test for an error
			$num = count($queries);
			for($q = 0; $q < $num; $q++){
				//the 0th entry is blank
				if($q==0)continue;
				
				//clean the query up
				$index = strrpos($queries[$q], ',', -0);
				$queries[$q] = substr($queries[$q], 1, $index-2);
				$queries[$q] = stripslashes($queries[$q]);
				
				//test the query for an error
				if(!(test_query_for_error($queries[$q], $access_token))){
					//there was an error in the query
					echo "<br/><br/>found an error in this query:<br/>".$queries[$q];
					
					//find the errors in the query and add them to error array
					$errorArray_addition = remove_errors_from_query($queries[$q], $access_token);
					if(isset($errorArray_addition)){
						$errorArray = array_merge($errorArray_addition, $errorArray);
					}
				}
					
			}
			
			
			echo "<br/><br/>summary:<br/><br/>searched these queries for errors:<br/>";
			print_r($queries);			
			echo "<br/><br/>and found these id's to be possible suspects:<br/>";
			print_r($errorArray);
			echo "<br/><br/><br/>";
			
			
			
			global $app_access_token;
			$access_token = $app_access_token;
			$arraySize = count($errorArray);
			
			$inc = 50;
			
			for( $ii = 0; $ii < $arraySize; $ii += $inc){
			
				$batched_request = "";
					
					for( $i = 0; ($i < $inc && ($i+$ii)< $arraySize); $i++)
				    {
				        if ($i != 0) { $batched_request .= ','; }
				      	$batched_request .= "{'method':'GET','relative_url':'".$errorArray[$i+$ii]."'}";
				    }
				
				
				// Converts JSON returned from the batch request into an array
				    $dataJSON			 =	cURL_standard("https://graph.facebook.com/?batch=[$batched_request]&access_token=$access_token&method=post");
				    $tempArrayJSON		 =	json_decode($dataJSON, true);
				    
				    $numResults = count($tempArrayJSON);
				    for($r = 0; $r < $numResults; $r++){
				    
				    	$data = JSON_decode($tempArrayJSON[$r]["body"]);
				    	echo "<br/><br/>error #";
				    	print_r($r+$ii);
				    	echo " returned this data:<br/>";
				    	print_r($data);
				    	echo "<br/><br/>";
				    	
				    	
				    	//get variables for stdClass object
				    	try{
				    		$object = get_object_vars($data);
				    	}catch(Exception $e){
				    		echo "<br/>Ignore this error<br/>";
				    	}
				    		
				    	
				    	if($object['error'] != null){
				    		try{
				    			$error = get_object_vars($object['error']);
				    			$message = $error['message'];
				    		}catch(Exception $e){
				    			echo "<br/>Ignore this error<br/>";
				    		}
				    		
				    	}
				    	
				    	
				    	if($tempArrayJSON[$r]["body"] == "false" || $message == "Unsupported get request."){
				    		$id = $errorArray[($r+$ii)];
				    		mysql_query("
				    					UPDATE queue
				    					SET added = -1
				    					WHERE facebookID = '$id'
				    		");
				    		echo "<br/>changed added to -1 on id: ";
				    		print_r($id);
				    	}
				    			    
				    }
				    
			}
			    
			    echo "<br>-----batch data-----<br>";
			   	echo "batched_request: ";
			   	print_r($batched_request);
			   	echo "<br>-----------<br>";
			   	echo "dataJSON: ";
			    print_r($dataJSON);
			    echo "<br>-----batch data-----<br>";
			    echo "tempArrayJSON: ";
			    print_r($tempArrayJSON);
			    echo "<br>-----tempArray-----<br>";
			    
			    echo "<br/><br/><br/>done<br/><br/><br/>";
		}
				
			
			
			
		//tests one query for an error (returns false if there was an error or if it returns blank
			function test_query_for_error($query, $access_token)
			{
				global $facebook;
				
				try{
				    $infoArray = $facebook->api(array('method' => 'fql.query', 'access_token' => $access_token, 'query' => $query));
				}
				catch(FacebookApiException $e){
					return false;
				}
				if($infoArray == "")
					return false;
					
				return true;
			}
			
		
		//returns false if there was an error..returns the info if it worked
			function test_multiquery_for_error($multiquery, $access_token){
				global $facebook;
				$param = array
				(   "method"		=>	"fql.multiquery",
					"access_token"	=>	$access_token,   
					"queries"		=>	$multiquery
				);
				
				try{
					$infoArray = $facebook->api($param);
				}
				catch(FacebookApiException $e){
					return false;
				}
				return $infoArray;
			}
			
			
			
			
			
		//find errors in query; pre-condition: the query already caused an error once before being passed to this function
			function remove_errors_from_query($query, $access_token)
			{
				echo "<br/><br/>removing errors from this query:<br/>";
				print_r($query);
				echo "<br/>with access token:<br/>";
				print_r($access_token);
				
				
				//first create the query prefix string
					$pos = strpos($query, "(");
					$queryPrefix = substr($query, 0, $pos);
					echo "<br/>query prefix:<br/>";
					print_r($queryPrefix);
					
				
				$query = stripslashes($query);
				$query = str_replace("'", "", $query);
				$idArray = explode(",", $query);
				$num = count($idArray);
				
				//fix first
					$idArray[0] = substr($idArray[0], strpos($idArray[0], "(", -0) +1);
				//fix last
					$num = count($idArray)-1;
					$idArray[$num] = str_replace(")","",$idArray[$num]);
					$idArray[$num] = trim($idArray[$num]);
					
				//delete empty entries
					$num = count($idArray);
					$counter = 0;
					for($e = 0; $e <$num; $e++){
						if($idArray[$e] != "")
							$idArrayNew[$counter++] = $idArray[$e];
					}
					
					$idArray = $idArrayNew;
					$idArrayNew = null;
				
									
					echo "<br/>testing these id's for errors:<br/>";
					print_r($idArray);
					echo "<br/>";
				
				//run the binary search and return the error array that results		
					return binary_test($idArray, $queryPrefix, $access_token);
			}
			
			
			
			
		//take in an array of id's and test top and bottom half for error
			function binary_test($idList, $queryPrefix, $access_token){
			
				global $facebook;
				$errorArray = array();
			
			//if there's only one id left, add it to the error array and return
				$num = count($idList);
				if($num == 1){
					$errorArray[0] = $idList[0];
					return $errorArray;
				}
				
				
				
			//cut the array in half
				$half = $num/2;
				$bottomArray = array_slice($idList, 0, $half);
				$topArray = array_slice($idList, $half);
						
				
				//create top query
				    $topQuery = create_query($queryPrefix, $topArray);
				
				//create bottom query
				    $bottomQuery = create_query($queryPrefix, $bottomArray);
				

								
				
				//test top
					echo "<br/>trying top query:<br/>".$topQuery;
					$topWorked = test_query_for_error($topQuery, $access_token);
					if(!$topWorked)
						echo "<br/>found an error in the top query<br/>";
				    
				    
				//test bottom
					echo "<br/>trying bottom query:<br/>".$bottomQuery;
					$bottomWorked = test_query_for_error($bottomQuery, $access_token);
					if(!$bottomWorked)
						echo "<br/>found an error in the bottom query<br/>";
					
					
				    
				    				    
				//if the errors went away, add top and bottom to the error array
				    if($topWorked && $bottomWorked){
				    	
				    	echo "<br/>the errors went away in both the top and bottom.. add both sets of id's to error array<br/>";
				    	if(!isset($errorArray))
				    		$errorArray = array_merge($bottomArray, $topArray);
				    	else
				    		$errorArray = array_merge($errorArray, $bottomArray, $topArray);
				    }
				    
				    
				    if(!$topWorked){
				    	$errorArray_addition = binary_test($topArray, $queryPrefix, $access_token);
				    	$errorArray = array_merge($errorArray_addition, $errorArray);
				    }
				    	
				   	if(!$bottomWorked){
				   		$errorArray_addition = binary_test($bottomArray, $queryPrefix, $access_token);
				    	$errorArray = array_merge($errorArray_addition, $errorArray);
					}
				

					
					return $errorArray;
			}
			
			
			
			function create_query($queryPrefix, $idList){
				$query = $queryPrefix." (";
				$num = count($idList);
			    for($p = 0; $p < $num; $p++){
			    	if($p != 0) $query .= ",";
			    	$query .= "'".$idList[$p]."'";
			    }
			    $query .= ")";
			
				return $query;
			}
			
		
		//queries should contain a name and \' rather than just '
		//also the wrapped single quotes should be included around the query and the name
			function create_multiquery($queryArray){
			
				$multiquery = "{";
				
				$num = count($queryArray);
				for($q = 0; $q < $num; $q++){
					if($queryArray[$q] == "") continue;
					
					$multiquery .= $queryArray[$q];
					$multiquery .= ",";
				}
				$multiquery .= "}";

				return $multiquery;
			}
			
			
					
			
			function bubble_error($function = "", $text = ""){
				$function	= addslashes($function);
				$text		= addslashes($text);
				mysql_query("INSERT INTO errors (function,error) VALUES ('$function', '$text')");
			}
			
			
			
			
		//takes in a too-large multi query and splits it up into two and returns it's info array
			function split_and_merge($multiquery, $access_token){
				
			
				//distribute the multi query into an array of single queries
					$queries = explode(')', $multiquery);
					//$queries[0] = null;
					$queries[0] = str_replace("{", "", $queries[0]);
					
				//loop through each query and test for an error
					$num = count($queries);
					for($q = 0; $q < $num; $q++){
					    if($q != 0)
					    	$queries[$q] = substr($queries[$q], 2);
					    $queries[$q] .= ")'";
					}
					
					
				//split into two multiqueries
					$half		 = count($queries)/2;
					$topArray	 = array_slice($queries, 0, $half);
					$bottomArray = array_slice($queries, $half);
					
					$topMultiquery = create_multiquery($topArray, $access_token);
					$bottomMultiquery = create_multiquery($bottomArray, $access_token);
				
					
				//test top and bottom for errors
					$topInfoArray = test_multiquery_for_error($topMultiquery, $access_token);
					$bottomInfoArray = test_multiquery_for_error($bottomMultiquery, $access_token);
					
					
				//decide what to put into the info array
					if($topInfoArray != false){
						if($bottomInfoArray != false){
							$infoArray = array_merge($topInfoArray, $bottomInfoArray);
							echo "split_and_merge broke up the big multi query into 2 parts and they both worked";
						}
						else{
							$infoArray = $topInfoArray;
							bubble_error("split_and_merge","the multi query was broken up, but the bottom multiquery still returned an error");
						}
					}
					else if($bottomInfoArray != false){
						$infoArray = $bottomInfoArray;
						bubble_error("split_and_merge","the multi query was broken up, but the top multiquery still returned an error");
					}
					else
						bubble_error("split_and_merge","the multi query was broken up, but both the top and bottom multiqueries returned an error");

														
				return $infoArray;
			}
			
			


?>