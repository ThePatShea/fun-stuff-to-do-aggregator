<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<!--CSS STYLES-->
<section>
			<style>

.nav {
	float: middle;
	background-color: rgba(20,95,159,0.9);
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

	//Scrapes a standard URL
	    function cURL_standard($url)
	    {
	    	$ch 					= curl_init($url);
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    	$curl_scraped_page 		= curl_exec($ch);
	    	curl_close($ch);
	    	$data 					= $curl_scraped_page;
	    	
	    	return $data;
	    }

	$yipitJSON = cURL_standard("http://api.seatgeek.com/2/events?lat=33.7545&lon=-84.3897&range=10mi");
	$yipitArray = json_decode($yipitJSON);
	
	
	$arr = (array) $yipitArray;

	//Remove the first object, meta, from the array. So the remained is the events array.
	array_shift($arr);
	$eventsArr = $arr;

	//Count row number, use different styles for odd and even rows.
	$row_num = 0;
	
	echo "<section><div class=\"nav\"><h2><br />SeatGeek Tickets  In  Atlanta !<br /><br /></div><ol id=\"local-deals\" class=\"deals\">";
	
	if(count($eventsArr)==0){
		echo "Sorry there is no available events right now. Please try other sites.";
	}
	
	foreach ($eventsArr as $child0)
	{
		$arr0 = (array) $child0;
		
		foreach ($arr0 as $child1)
		{
			$arr1 = (array) $child1;
			
			$title = $arr1["title"];
			
			$url = $arr1["url"];
			
			$stat = (array) $arr1["stats"];
			$listingCount = $stat["listing_count"];
			$lowestPrice = $stat["lowest_price"];
			if($listingCount == null){
				$listingCount = 0;
			}
			if($lowestPrice == null){
				$lowestPrice = 0;
			}
			
			$performers = (array) $arr1["performers"];
			$performer = (array) $performers[0];
			$image = $performer["image"];
			if($image == null){
				$image = "http://1.bp.blogspot.com/_ky1bf81QrMw/TUlSgZKc0vI/AAAAAAAABA0/K4ClLDL5opM/s1600/no_photo_male.jpg";
			}
			
			$venue = (array) $arr1["venue"];
			$address =$venue["address"];
			$venueName = $venue["name"];
			
			
			$row_num++;
				
			if($row_num%2==1){
				echo "<div class=\"row_dark\">";
			}else{
				echo "<div class=\"row_bright\">";
			}
			
			//Display the information
		echo "<li><span  style=\"float:left\",\"text-align:center\"><img src=\"" . $image . "\" height=127px width=130px /></span><span><div class=\"content\"><h4>" . $title . "</h4><p>@" . $venueName . ", " . $address . "<br /> Lowest Price: " . $lowestPrice . " <br /> Listing Count: " . $listingCount . " <br /></p><button><a href=\"" . $url . "\" class=\"dealUrl\">View Tickets</a></button></div></span></li></div>";
		}
	}
	echo "</ol></section></div>";
?>