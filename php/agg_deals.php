<?php include_once("background.php"); ?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<!--CSS STYLES-->
<section>
			<style>

.nav {
	float: middle;
	background-color: #9de0f4;
	text-transform: uppercase;
	text-align: center;
	color: #298fd8 ;
	font-family: "Apple LiGothic";
	font-size: x-large;
}

ol.deals {
	list-style: none;
	list-style-type: none;
	list-style-position: initial;
	list-style-image: initial;
	margin-bottom: 30px;
}

	ol.deals li .content {
		margin-left: 150px;
	}
	ol.deals li h4 {
		font-size: 1em;
		font-weight: bold;
		line-height: 1.25em;
		font-family:"Lucida Sans";
	}
	
	ol.deals li p {
		margin-bottom: 1em;
		font-family: "Apple LiGothic";

	}

a {
	color: #0981BE;
}

a {
	text-decoration: none;
}

a {
	margin: 0;
	padding: 0;
	font-size: 100%;
	vertical-align: baseline;
	background: transparent;
}

.row_dark{
	background:	#e5e9ec;
}
											
.row_bright{
	background:	#f1eeee;
}
</style>
</section>


<?php
	$yipitAPIKey= "RhS4k27uuUcxY6wv";
	$yipitJSON = cURL_standard("http://api.yipit.com/v1/deals?key=" . $yipitAPIKey . "&lat=33.792148&lon=-84.323673&division=atlanta&limit=5000");
	$yipitArray = json_decode($yipitJSON);
	
	
	//Convert the stdClass into an Array object
	$arr = (array) $yipitArray;
	
	//Delete the first object in this array, which has no usable information.
	//Only applicable to Yipit API.
	array_shift($arr);
	
	
	
	echo "<section><div class=\"nav\"><h2><br />Yipit  Deals  In  Atlanta !<br /><br /></div><ol id=\"local-deals\" class=\"deals\">";
	
	//Count row number, use different styles for odd and even rows.
	$row_num = 0;
	
	foreach ($arr as $child0)
	{
		$arr0 = (array) $child0;
		
		foreach ($arr0 as $child1)
		{
			$arr1 = (array) $child1;
			
			foreach ($arr1 as $child2)
			{
				$arr2 = (array) $child2;
				
				$url = $arr2["url"];
				
				$title = $arr2["title"];
				
				$yipit_title=$arr2["yipit_title"];
				
				$date_added = $arr2["date_added"];
				
				$end_time = $arr2["end_date"];
				
				$bubbleID = "yipit-" . $arr2['id'];
						
				$images = (array) $arr2["images"];
				$image_small = $images["image_small"];
				$image_big = $images["image_big"];
				$price = (array) $arr2["price"];
				$rawPrice = $price["raw"];
				
				$originalPrice = (array) $arr2["value"];
				$rawOriginalPrice = $originalPrice["raw"];
				
				$discount = (array) $arr2["discount"];
				$rawDiscount = $discount["raw"];
				
				$source = (array) $arr2["source"];
				$sourceName = $source["name"]; 
				
				
				$business = (array) $arr2["business"];
				$locations = (array) $business["locations"];
				$name = $business["name"];//Previously use the 'name' attribute as 'description' in page_info and as 'name' in accouns table. But still NEED to use this name to search for business and place page in facebook.
				
				$location = (array) $locations[0];
				$street = $location["address"];
				$city = $location["locality"];
				$state = $location["state"];
				$zip = $location["zip_code"];
				$latitude = $location["lat"];
				$longitude = $location["lon"];
				$phone = $location["phone"];
				
				$tags = (array) $arr2["tags"];
				$tag_counter = 0;
				foreach ($tags as $singleTag)
					{
					  $tagArr = (array) $singleTag;
					  $tagName = $tagArr["name"];
					  $deals_categories_array[$tag_counter] = $tagName;
					  $tag_counter++;
					}
				
				//Retrieve the business/vendor (not source) facebook page or place by business name/lat&lon/phone/address
	
				//Reminder: User the updated ACCESS_TOKEN
				$facebookAccess_token = $app_access_token;
				
				$facebookPlaceQuery = "https://graph.facebook.com/search?q=" . $name . "&type=place&center=" . $latitude . "," . $longitude . "&access_token=" . $facebookAccess_token;
				
				//Replace the spacer in $name with "%20", which is readable
				$facebookPlaceQuery = str_replace(" ","%20",$facebookPlaceQuery);
				$facebookPlaceQueryJSON = cURL_standard($facebookPlaceQuery);
				$response = json_decode($facebookPlaceQueryJSON);
				
				$responseArray = (array) $response;
				
				$facebookPlaceArray = (array) $responseArray["data"];
				
				$placeFacebook = (array) $facebookPlaceArray[0];
				
				$placeFacebookID = $placeFacebook["id"];
			
				
				$result= generateQueryArray("SELECT * FROM deals_sources where name='" . $sourceName . "'");
				
				if($result == ""){
					
					//This happends when the graph cannot find any result for the $sourceName.
				
					$facebookSourceQuery = "https://graph.facebook.com/search?q=" . $sourceName . "&type=page&access_token=" . $facebookAccess_token;
					$facebookSourceQuery = str_replace(" ","%20",$facebookSourceQuery);
					$facebookSourceQueryJSON = cURL_standard($facebookSourceQuery);
					$facebookSourceResponse = json_decode($facebookSourceQueryJSON);
					
					$facebookSourceResponseArray = (array) $facebookSourceResponse;
					
					$facebookSourceArray = (array) $facebookSourceResponseArray["data"];
					
					if(count($facebookSourceArray) == 0){
						
						//Use the first word in (String)$sourceName to retrieve the source Facebook page. eg. change "travelzoo local deals" into "travelzoo"
						$sourceName = preg_replace('/\W.*/','',$sourceName);
						$facebookSourceQuery = "https://graph.facebook.com/search?q=" . $sourceName . "&type=page&access_token=" . $facebookAccess_token;
						$facebookSourceQueryJSON = cURL_standard($facebookSourceQuery);
						$facebookSourceResponse = json_decode($facebookSourceQueryJSON);
						
						$facebookSourceResponseArray = (array) $facebookSourceResponse;
						
						$facebookSourceArray = (array) $facebookSourceResponseArray["data"];
					}
					
					$facebookSource = (array) $facebookSourceArray[0];
					
					$sourceFacebookID = $facebookSource["id"];
					
					//The 1st Insert(if happend)
					//Insert into deals_sources table
					//Tested fine
					
					mysql_query
					("
						INSERT INTO	deals_sources
						(accountFacebookID, name)
						VALUES ('".addslashes($sourceFacebookID)."', '".addslashes($sourceName)."')
					");
					
				}
				else
				{
					$sourceFacebookID = $result[0]['accountFacebookID'];
				}
				
				
					
				
				
				
				//The 2nd Insert
				//Insert into queue table
				//Tested fine
				mysql_query
				("
					INSERT INTO	queue
					(facebookID, type)
					VALUES ('".addslashes($placeFacebookID)."', 'page')
				");
				
			
				
			
				
				//The 3rd Insert
				//Insert into posts table
				//Tested fine
				mysql_query
				("
					INSERT INTO	posts
					(bubbleID, type, accountFacebookID, name, description, created_time, pic_square, pic_big)
					VALUES ('".addslashes($bubbleID)."', 'deal','".addslashes($placeFacebookID)."', '".addslashes($yipit_title)."', '".addslashes($title)."', '".addslashes(strtotime($date_added))."', '".addslashes($image_small)."', '".addslashes($image_big)."')
				");
				
				
				
				//The 4th Insert
				//Insert into deals_info table
				//Tested fine
				
				mysql_query
				("
					INSERT INTO	deals_info
					(bubbleID, url, end_time, price, value, discount, source, accountFacebookID_venue)
					VALUES ('".addslashes($bubbleID)."', '".addslashes($url)."', '".addslashes(strtotime($end_time))."', '".addslashes($rawPrice)."', '".addslashes($rawOriginalPrice)."', '".addslashes($rawDiscount)."', '".addslashes($sourceFacebookID)."', '".addslashes($placeFacebookID)."')
				");
				
				
				//The 5th Insert
				//Insert into deals_categories table
				//Tested fine
				
				foreach ($deals_categories_array as $singleTag){
					mysql_query
					("
					INSERT INTO	deals_categories
					(bubbleID,name)
					VALUES ('".addslashes($bubbleID)."','".addslashes($tagName)."')
					");  
				}
				
				//Deleted
				//The 3rd Insert
				//Insert into accounts table
				//Tested fine
				/*
				mysql_query
				("
					INSERT INTO	accounts
					(name, type, accountFacebookID)
					VALUES ('".addslashes($name)."', 'page', '".addslashes($placeFacebookID)."')
				");
				*/
				
				
				//Deleted
				//The 4th Insert
				//Instert into pages_info table
				//Tested fine
				/*
				mysql_query
				("
					INSERT INTO	pages_info
					(accountFacebookID, type, description, street, city, state, zip, latitude, longitude, phone)
					VALUES ('".addslashes($placeFacebookID)."',
					'YIPIT BUSINESS',
					'".addslashes($name)."', '".addslashes($street)."', '".addslashes($city)."', '".addslashes($state)."', '".addslashes($zip)."', '".addslashes($latitude)."', '".addslashes($longitude)."', '".addslashes($phone)."')
				");
				*/
				
				
				
				
				//Display the information
				
				$row_num++;
				
				if($row_num%2==1){
					echo "<div class=\"row_dark\">";
				}else{
					echo "<div class=\"row_bright\">";
				}
				
				echo "<li><img src=\"" . $image_small . "\" height=110px width=120px style=\"float:left\" /><span><div class=\"content\"><h4>" . $title . "</h4><p>Deal Ends on " . $end_time . "</p><button><a href=\"" . $url . "\" class=\"dealUrl\">View Deal</a></button></div></span><br /></li></div>";
				
			}
		}
	}
	echo "</ol></section></div>";
?>