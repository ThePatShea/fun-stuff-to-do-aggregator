<?php include_once("../../back_end/background.php"); ?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<!--CSS STYLES-->
<section>
			<style>

.nav {
	float: middle;
	background-color: rgba(211,194,61,0.9);
	text-transform: 	uppercase;
	text-align: center;
	color: #ffffff ;
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
		margin-left: 40px;
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
	function insert_into_tables($placeFacebookID, $hours){
			mysql_query
			("
				INSERT INTO	queue
				(facebookID, type)
				VALUES ('".addslashes($placeFacebookID)."', 'page')
			");
			
			if(count($hours)!=0){
				$daily_hours = $hours["mon"];
				$open_time = $daily_hours[0];
				$close_time = $daily_hours[1];
				mysql_query
				("
					INSERT INTO	pages_hours
					(accountFacebookID, day, open, close)
					VALUES ('".addslashes($placeFacebookID)."', 'mon', '".addslashes($open_time)."', '".addslashes($close_time)."')
				");
				
				$daily_hours = $hours["tue"];
				$open_time = $daily_hours[0];
				$close_time = $daily_hours[1];
				mysql_query
				("
					INSERT INTO	pages_hours
					(accountFacebookID, day, open, close)
					VALUES ('".addslashes($placeFacebookID)."', 'tue', '".addslashes($open_time)."', '".addslashes($close_time)."')
				");
				
				$daily_hours = $hours["wed"];
				$open_time = $daily_hours[0];
				$close_time = $daily_hours[1];
				mysql_query
				("
					INSERT INTO	pages_hours
					(accountFacebookID, day, open, close)
					VALUES ('".addslashes($placeFacebookID)."', 'wed', '".addslashes($open_time)."', '".addslashes($close_time)."')
				");
				
				$daily_hours = $hours["thu"];
				$open_time = $daily_hours[0];
				$close_time = $daily_hours[1];
				mysql_query
				("
					INSERT INTO	pages_hours
					(accountFacebookID, day, open, close)
					VALUES ('".addslashes($placeFacebookID)."', 'thu', '".addslashes($open_time)."', '".addslashes($close_time)."')
				");
				
				$daily_hours = $hours["fri"];
				$open_time = $daily_hours[0];
				$close_time = $daily_hours[1];
				mysql_query
				("
					INSERT INTO	pages_hours
					(accountFacebookID, day, open, close)
					VALUES ('".addslashes($placeFacebookID)."', 'fri', '".addslashes($open_time)."', '".addslashes($close_time)."')
				");
				
				$daily_hours = $hours["sat"];
				$open_time = $daily_hours[0];
				$close_time = $daily_hours[1];
				mysql_query
				("
					INSERT INTO	pages_hours
					(accountFacebookID, day, open, close)
					VALUES ('".addslashes($placeFacebookID)."', 'sat', '".addslashes($open_time)."', '".addslashes($close_time)."')
				");
				
				if(count($hours)==7){
					$daily_hours = $hours["sun"];
					$open_time = $daily_hours[0];
					$close_time = $daily_hours[1];
					mysql_query
					("
						INSERT INTO	pages_hours
						(accountFacebookID, day, open, close)
						VALUES ('".addslashes($placeFacebookID)."', 'sun', '".addslashes($open_time)."', '".addslashes($close_time)."')
					");
				}
			}
	}
?>

<?php
	$row_num = 0;
	echo "<section><div class=\"nav\"><h2><br />Factuals  In  Atlanta !<br /><br /></div><ol id=\"local-deals\" class=\"deals\">";
	
	//Add your key and secret here
	$key = "aTdii04Vy9QrJOywPOUV0OnEiXVfidpe0E9yhhNU";
	$secret = "tUbigQD71BtDWqoxHoRu1jCFwIJs5JaFDpRVSZit";

	//setup
    require_once('factual_drive/Factual.php');
	$factual = new Factual($key,$secret);

	
	$offsetNumber = -50;
	
	// Search for 'atlanta' in restaurant-us table
	do{
		$offsetNumber +=50;
	 	$query = new FactualQuery;
		$query->search("atlanta");
		$query->within(new FactualCircle(33.792148,-84.323673, 5000));
		$query->limit(50);
		$query->offset($offsetNumber);
	    $res = $factual->fetch("restaurants-us", $query);
		$tempResponse = $res->getData();
		$response = array_merge($response, $tempResponse);
	}while($offsetNumber<450);

	foreach ($response as $place)
	{
		$name = $place["name"];
		$address = $place["address"];
		$website = $place["website"];
		
		$hours_raw = array();
		$hours_array = array();
		$hours = array();
		$hours_raw = $place["hours"];
		
		$counter = 0;
		$hours_raw = explode("\"",$hours_raw);
		foreach($hours_raw as $temp){
			if(strlen($temp)>=4&&strlen($temp)<=5&&!strcasecmp($temp,"Lunch")==0){
				$hours_array[$counter] = $temp;
				$counter++;
			}
		}
		
		if(count($hours_array)==12){
			$hours["mon"] = array($hours_array[0], $hours_array[1]);
			$hours["tue"] = array($hours_array[2], $hours_array[3]);
			$hours["wed"] = array($hours_array[4], $hours_array[5]);
			$hours["thu"] = array($hours_array[6], $hours_array[7]);
			$hours["fri"] = array($hours_array[8], $hours_array[9]);
			$hours["sat"] = array($hours_array[10], $hours_array[11]);
		}
		
		if(count($hours_array)==14){
			$hours["mon"] = array($hours_array[0], $hours_array[1]);
			$hours["tue"] = array($hours_array[2], $hours_array[3]);
			$hours["wed"] = array($hours_array[4], $hours_array[5]);
			$hours["thu"] = array($hours_array[6], $hours_array[7]);
			$hours["fri"] = array($hours_array[8], $hours_array[9]);
			$hours["sat"] = array($hours_array[10], $hours_array[11]);
			$hours["sun"] = array($hours_array[12], $hours_array[13]);
		}
		
		if(count($hours_array)==24){
			$hours["mon"] = array($hours_array[0], $hours_array[3]);
			$hours["tue"] = array($hours_array[4], $hours_array[7]);
			$hours["wed"] = array($hours_array[8], $hours_array[11]);
			$hours["thu"] = array($hours_array[12], $hours_array[15]);
			$hours["fri"] = array($hours_array[16], $hours_array[19]);
			$hours["sat"] = array($hours_array[20], $hours_array[23]);
		}
		
		if(count($hours_array)==28){
			$hours["mon"] = array($hours_array[0], $hours_array[3]);
			$hours["tue"] = array($hours_array[4], $hours_array[7]);
			$hours["wed"] = array($hours_array[8], $hours_array[11]);
			$hours["thu"] = array($hours_array[12], $hours_array[15]);
			$hours["fri"] = array($hours_array[16], $hours_array[19]);
			$hours["sat"] = array($hours_array[20], $hours_array[23]);
			$hours["sun"] = array($hours_array[24], $hours_array[27]);
		}
		
		
		//Retrieve the business/vendor (not source) facebook page or place by business name/lat&lon/phone/address
		//Reminder: User the updated ACCESS_TOKEN
		$facebookAccess_token = $app_access_token;
		$facebookPlaceQuery = "https://graph.facebook.com/search?q=" . $name . "&type=place&center=33.792148,-84.323673&access_token=" . $facebookAccess_token;
		//Encode the spacer in $name using "%20", which is readable
		$facebookPlaceQuery = str_replace(" ","%20",$facebookPlaceQuery);
		$facebookPlaceQueryJSON = cURL_standard($facebookPlaceQuery);
		$response = json_decode($facebookPlaceQueryJSON);
		$responseArray = (array) $response;
		$facebookPlaceArray = (array) $responseArray["data"];
		$facebook_place = (array) $facebookPlaceArray[0];
		$placeFacebookID = $facebook_place["id"];
		
		//Call function insert_into_tables() to insert data
		//insert_into_tables($placeFacebookID, $hours);
		
		//Display the information
		
		$row_num++;
				
		if($row_num%2==1){
			echo "<div class=\"row_dark\">";
		}else{
			echo "<div class=\"row_bright\">";
		}
		
		echo "<li><span  style=\"float:left\",\"text-align:center\"></span><span><div class=\"content\"><h4>" . $name . "</h4><p>Address: " . $address . " </br>Opening Hours: ";
		
		$counter = 0;
		foreach($hours as $daily_hours){
			$counter++;
			switch($counter){
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
				case 7:
					echo "</br>Sun: ";
					break;
			}
			echo "open at " . $daily_hours[0] . ", ";
			echo "close at " . $daily_hours[1] . ";";
			}
			if($counter == 0){
				echo "</br>Not Available";
			}else{
				$total++;
			}
			echo "</p><button><a href=\"" . $website . "\" class=\"dealUrl\">Website</a></button></div></span><br /></li></div>";
		}
	echo "</div></span></li></div>";
?>