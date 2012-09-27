<?php include_once("../../back_end/background.php"); ?>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<!--CSS STYLES-->
<section>
			<style>

.nav {
	float: middle;
	background-color: rgba(170,8,8,0.9);
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
	
	//
	// From http://non-diligent.com/articles/yelp-apiv2-php-example/
	//
	
	
	// Enter the path that the oauth library is in relation to the php file
	require_once ('OAuth.php');
	
	
	// Set your keys here
	$consumer_key = "TPhBuCqrfnJIpOPjS339ig";
	$consumer_secret = "jI69yULIAtO_eLZkVASa8jzVVdc";
	$token = "IpOaGS-IOZjrxiA5GUuuR9fT-kgZY52i";
	$token_secret = "2m4mYs10Y9BvtHaUfvQabf3wSJY";
	
	// Token object built using the OAuth library
	$token = new OAuthToken($token, $token_secret);
	
	// Consumer object built using the OAuth library
	$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
	
	// Yelp uses HMAC SHA1 encoding
	$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
	
	$offsetNumber = -20;
	$businessArr = array();
	do{
		$offsetNumber += 20;
	
		// For example, request business with id 'the-waterboy-sacramento'
		$unsigned_url = "http://api.yelp.com/v2/search?location=Atlanta&cll=33.7545,-84.3897&limit=20&offset=" . $offsetNumber;
		
		// For examaple, search for 'tacos' in 'sf'
		//$unsigned_url = "http://api.yelp.com/v2/search?term=tacos&location=sf";
		
		// Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
		$oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
		
		// Sign the request
		$oauthrequest->sign_request($signature_method, $consumer, $token);
		
		// Get the signed URL
		$signed_url = $oauthrequest->to_url();
		
		// Send Yelp API Call
		$ch = curl_init($signed_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch); // Yelp response
		curl_close($ch);
		
		// Handle Yelp response data
		$response = json_decode($data);
	
		$yelpArr = (array) $response;
		
		$tempBusinessArr = (array) $yelpArr["businesses"];
		
		$businessArr = array_merge($businessArr, $tempBusinessArr);
	}while(count($tempBusinessArr)>=1);

	//Count row number, use different styles for odd and even rows.
	$row_num = 0;
	
	echo "<section><div class=\"nav\"><h2><br />Yelps  In  Atlanta !<br /><br /></div><ol id=\"local-deals\" class=\"deals\">";
	
	if(count($businessArr)==0){
		echo "Sorry there is no available venues right now. Please try other sites.";
	}
	
	foreach ($businessArr as $child0)
	{
		$arr0 = (array) $child0;
			
		$name = $arr0["name"];
			
		$url = $arr0["url"];
			
		$image = $arr0["image_url"];
			
		$ratingImage = $arr0["rating_img_url"];
		
		$location = (array) $arr0["location"];
		$addressArr = (array) $location["address"];
		$address = $addressArr[0];
		
		$coordinate = (array) $location["coordinate"];
		$latitude = $coordinate["latitude"];
		$longitude = $coordinate["longitude"];
		
				
		//Retrieve the business/vendor (not source) facebook page or place by business name/lat&lon/phone/address
			
		//Reminder: User the updated ACCESS_TOKEN
		$facebookAccess_token = $app_access_token;
						
		$facebookPageQuery = "https://graph.facebook.com/search?q=" . $name . "&type=page&center=" . $latitude . "," . $longitude . "&access_token=" . $facebookAccess_token;
						
		//Replace the spacer in $name with "%20", which is readable
		$facebookPageQuery = str_replace(" ","%20",$facebookPageQuery);
		$facebookPageQueryJSON = cURL_standard($facebookPageQuery);
		$response = json_decode($facebookPageQueryJSON);
		$responseArray = (array) $response;
		$facebookPageArray = (array) $responseArray["data"];
		$pageFacebook = (array) $facebookPageArray[0];				
		$pageFacebookID = $pageFacebook["id"];
				
		/*
		//The 1st Insert
		//Insert into queue table
		//Tested fine
		mysql_query
		("
			INSERT INTO	queue
			(facebookID, type)
			VALUES ('".addslashes($pageFacebookID)."', 'page')
		");
		*/
					
		//The 2nd Insert
		//Insert into accounts table
		//Tested fine
		/*
		mysql_query
		("
			INSERT INTO	accounts
			(name, type, accountFacebookID)
			VALUES ('".addslashes($name)."', 'page', '".addslashes($pageFacebookID)."')
			");
		*/
				
				
		//Deleted
		//The 3rd Insert
		//Instert into pages_info table
		//Tested fine
		/*
		mysql_query
		("
			INSERT INTO	pages_info
			(accountFacebookID, type, description, street, city, state, zip, latitude, longitude, phone)
			VALUES ('".addslashes($pageFacebookID)."',
			'YELP BUSINESS','".addslashes($name)."', '".addslashes($street)."', '".addslashes($city)."', '".addslashes($state)."', '".addslashes($zip)."', '".addslashes($latitude)."', '".addslashes($longitude)."', '".addslashes($phone)."')
		");
		*/
		
		
		//Display the information
		
		$row_num++;
				
		if($row_num%2==1){
			echo "<div class=\"row_dark\">";
		}else{
			echo "<div class=\"row_bright\">";
		}
		
		echo "<li><span  style=\"float:left\",\"text-align:center\"><img src=\"" . $image . "\" height=112px width=130px /></span><span><div class=\"content\"><h4>" . $name . "</h4><p>@" . $address . " <br /><img src=\"" . $ratingImage . "\"/><br /></p><button><a href=\"" . $url . "\" class=\"dealUrl\">View Tickets</a></button></div></span></li></div>";
		
	}
	echo "</ol></section></div>";
?>