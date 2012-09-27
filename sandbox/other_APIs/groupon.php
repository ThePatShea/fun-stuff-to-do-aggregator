<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<!--CSS STYLES-->
<section>
			<style>

.nav {
	float: middle;
	background-color: rgba(110,191,40,0.9);
	text-transform: 	uppercase;
	text-align: center;
	color: #31600e ;
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

	$yipitJSON = cURL_standard("http://api.groupon.com/v2/deals.json?channel=getaways&client_id=dde78bfddae508ec039071b790622f674c2e1996&division=atlanta&lat=33.7545&lng=-84.3897&show=dealUrl%2CendAt%2Ctitle%2CsidebarImageUrl");
	$yipitArray = json_decode($yipitJSON);
	
	$arr = (array) $yipitArray;

	//Count row number, use different styles for odd and even rows.
	$row_num = 0;
	
	echo "<section><div class=\"nav\"><h2><br />Groupon  Deals  In  Atlanta !<br /><br /></div><ol id=\"local-deals\" class=\"deals\">";
	
	foreach ($arr as $child0)
	{
		$arr0 = (array) $child0;
		
		foreach ($arr0 as $child1)
		{
			
				$arr1 = (array) $child1;
				
				$dealUrl = $arr1["dealUrl"];
				
				$title = $arr1["title"];
				
				$endAt = $arr1["endAt"];
				
				$image = $arr1["sidebarImageUrl"];
				
				$row_num++;
				
				if($row_num%2==1){
					echo "<div class=\"row_dark\">";
				}else{
					echo "<div class=\"row_bright\">";
				}
				
				//Display the information
				echo "<li><img src=\"" . $image . "\" height=110px width=120px style=\"float:left\" /><span><div class=\"content\"><h4>" . $title . "</h4><p>Deal Ends on " . $endAt . "</p><button><a href=\"" . $dealUrl . "\" class=\"dealUrl\">View Deal</a></button></div></span><br /></li></div>";
			
		}
	}
	echo "</ol></section></div>";
?>