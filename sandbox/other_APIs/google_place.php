<?php include_once("../../back_end/background.php"); ?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<!--CSS STYLES-->
<section>
			<style>

.nav {
	float: middle;
	background-color: #62baf1;
	text-transform: uppercase;
	text-align: center;
	color: #1e7630 ;
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
	function insert_into_tables($placeFacebookID, $hours_array){
		if(count($hours_array)!=0){
			foreach($hours_array as $period){
				$close_and_open = (array) $period;
				
				$close = (array) $close_and_open["close"];
				$close_time = (String) $close["time"];
				$close_time = substr_replace($close_time,":",2,0);
				
				$open = (array) $close_and_open["open"];
				$open_time = (String) $open["time"];
				$open_time = substr_replace($open_time,":",2,0);
				
				$day = $close["day"];
				$opening_hours_array[$day] = array($close_time, $open_time);
				switch($day){
					case 0:
						$day = "sun";
						break;
					case 1:
						$day = "mon";
						break;
					case 2:
						$day = "tue";
						break;
					case 3:
						$day = "wed";
						break;
					case 4:
						$day = "thu";
						break;
					case 5:
						$day = "fri";
						break;
					case 6:
						$day = "sat";
						break;
				}
				
				mysql_query
				("
					INSERT INTO	pages_hours
					(accountFacebookID, day, open, close)
					VALUES ('".addslashes($placeFacebookID)."', '".addslashes($day)."', '".addslashes($open_time)."', '".addslashes($close_time)."')
				");
				
			}
		}
			
		mysql_query
		("
			INSERT INTO	queue
			(facebookID, type)
			VALUES ('".addslashes($placeFacebookID)."', 'page')
		");
	}
?>
<?php
	$google_place_api_key = "AIzaSyChkTvQ7LtHZcBHpsLreF7dF9g9ugekEOc";			
	$google_place_query = "https://maps.googleapis.com/maps/api/place/search/json?query=atlanta&location=33.792148,-84.323673&radius=50000&sensor=false&key=" . $google_place_api_key;
	$google_place_json = cURL_standard($google_place_query);
	$response = json_decode($google_place_json);
	
	//Convert the stdClass into an Array object
	$responseArray = (array) $response;
	$results = (array) $responseArray["results"];
	
	$reference_counter =0;
	
	foreach($results as $single_place){
		$single_place_array = (array)$single_place;
		$single_reference = $single_place_array["reference"];
		$reference_array[$reference_counter] = $single_reference;
		$reference_counter++;
	}
	
	echo "<section><div class=\"nav\"><h2><br />Google  Places  In  Atlanta !<br /><br /></div><ol id=\"local-deals\" class=\"deals\">";
	
	//Count row number, use different styles for odd and even rows.
	$row_num = 0;
			
	foreach ($reference_array as $single_reference)
	{
		//Retrive the details of this place using Google Place API
					
		$place_details_query = "https://maps.googleapis.com/maps/api/place/details/json?reference=" . $single_reference . "&sensor=false&key=" . $google_place_api_key;
		$place_details_json = cURL_standard($place_details_query);
		$details_response = json_decode($place_details_json);
		$details_response_array = (array) $details_response;
		$details_result = (array) $details_response_array["result"];
		
		$formatted_address = $details_result["formatted_address"];
		$name = $details_result["name"];;
		$url = $details_result["url"];
		$icon = $details_result["icon"];
		$opening_hours = (array)$details_result["opening_hours"];
		$open_now = $opening_hours["open_now"];
		
		$opening_periods = array();
		$opening_hours_array = array();
		$opening_periods = (array) $opening_hours["periods"];
		foreach($opening_periods as $period){
			$close_and_open = (array) $period;
			
			$close = (array) $close_and_open["close"];
			$close_time = (String) $close["time"];
			$close_time = substr_replace($close_time,":",2,0);
			
			$open = (array) $close_and_open["open"];
			$open_time = (String) $open["time"];
			$open_time = substr_replace($open_time,":",2,0);
			
			$day = $close["day"];
			$opening_hours_array[$day] = array($close_time, $open_time);
		}
		
		//Retrieve the business/vendor (not source) facebook page or place by business name/lat&lon/phone/address
		//Reminder: User the updated ACCESS_TOKEN
		$facebookAccess_token = $app_access_token;
		$facebookPlaceQuery = "https://graph.facebook.com/search?q=" . $name . "&type=place&center=33.792148,-84.323673&access_token=" . $facebookAccess_token;
		//Encode the spacer in $name using "%20"
		$facebookPlaceQuery = str_replace(" ","%20",$facebookPlaceQuery);
		$facebookPlaceQueryJSON = cURL_standard($facebookPlaceQuery);
		$response = json_decode($facebookPlaceQueryJSON);
		$responseArray = (array) $response;
		$facebookPlaceArray = (array) $responseArray["data"];
		$facebook_place = (array) $facebookPlaceArray[0];
		$placeFacebookID = $facebook_place["id"];
		
		//Call function insert_into_tables() to insert data
		//insert_into_tables($placeFacebookID, $opening_periods);
		
		//Display the information
		
		if($row_num%2==1){
			echo "<div class=\"row_dark\">";
		}else{
			echo "<div class=\"row_bright\">";
		}
		$row_num++;
				
		echo "<li><img src=\"" . $icon . "\" height=110px width=120px style=\"float:left\" /><span><div class=\"content\"><h4>" . $name . "</h4><p>Address: " . $formatted_address . " </p><p> Opening Hours: ";
		$counter = 0;
		foreach($opening_hours_array as $daily_opening_hours){
			switch($counter){
				case 0:
					echo "</br>Sun: ";
					break;
				case 1:
					echo "</br>Mon: ";
					break;
				case 2:
					echo "</br>Tue: ";
					break;
				case 3:
					echo "</br>Wed: ";
					break;
				case 4:
					echo "</br>Thu: ";
					break;
				case 5:
					echo "</br>Fri: ";
					break;
				case 6:
					echo "</br>Sat: ";
					break;
			}
			$counter++;
			echo "open at " . $daily_opening_hours[1] . ", ";
			echo "close at " . $daily_opening_hours[0] . ";";
		}
		if($counter == 0){
			echo "Not Available";
		}
		echo "</p><button><a href=\"" . $url . "\" class=\"dealUrl\">View Deal</a></button></div></span><br /></li></div>";

	}
	echo "</ol></section></div>";
?>