<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<!--CSS STYLES-->
<section>
			<style>

.nav {
	float: middle;
	background-color: rgba(38,172,249,0.9);
	text-transform: 	uppercase;
	text-align: center;
	color: #ededa9 ;
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
	
	ol.deals li .category {
		margin-bottom: 1em;
		font-family: "Apple Symbols";
		text-align:center;
		
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

	$yipitJSON = cURL_standard("https://api.foursquare.com/v2/venues/trending?ll=33.7545,-84.3897&oauth_token=K2FMTE1Q3OJAKRLVNAI15M2KVMY1VYTY4Q1200YPLWROQ1BQ&v=20120626");
	$yipitArray = json_decode($yipitJSON);
	
	$arr = (array) $yipitArray;

	array_shift($arr);
	array_shift($arr);
	$response = $arr;
	$venuesArr = $response["venues"];

	//Count row number, use different styles for odd and even rows.
	$row_num = 0;
	
	echo "<section><div class=\"nav\"><h2><br />foursquare Recommendations In  Atlanta !<br /><br /></div><ol id=\"local-deals\" class=\"deals\">";
	
	if(count($venuesArr)==0){
		echo "Sorry there is no available venues right now. Please try other sites.";
	}
	
	foreach ($arr as $child0)
	{
		$arr0 = (array) $child0;
		
		foreach ($arr0 as $child1)
		{
			$arr1 = (array) $child1;
			
			foreach ($arr1 as $child2)
			{
				$arr2 = (array) $child2;
				
				$venueUrl = $arr2["url"];
				
				$title = $arr2["name"];
				
				$arr3 = (array) $arr2["location"];
				$address = $arr3["address"];
				$postalCode = $arr3["postalCode"];
				
				$arr4 = (array) $arr2["categories"];
				$arr5 = (array) $arr4[0];
				$categoryName = $arr5["name"];
				
				$arr6 = (array) $arr5["icon"];
				$prefix = $arr6["prefix"];
				$prefixLength = strlen($prefix);
				$newPrefixLength = $prefixLength -1;
				$newPrefix = substr($prefix, 0, $newPrefixLength);
				$suffix = $arr6["suffix"];
				$icon = $newPrefix . $suffix;
				
				$row_num++;
				
				if($row_num%2==1){
					echo "<div class=\"row_dark\">";
				}else{
					echo "<div class=\"row_bright\">";
				}
				
				//Display the information
				echo "<li><span  style=\"float:left\",\"text-align:center\"><img src=\"" . $icon . "\" height=90px width=90px />" . "<br /><div class=\"category\">" . $categoryName . "</div></span><span><div class=\"content\"><h4>" . $title . "</h4><p>" . $address . " , " . $postalCode . "</p><button><a href=\"" . $venueUrl . "\" class=\"dealUrl\">View Venue</a></button></div></span><br /></li></div>";
			}
		}
	}
	echo "</ol></section></div>";
?>