<?php  include_once("background.php");  ?>

<?php

	function agg_search($searchQueries)
	{
		$count_searchQueries = count($searchQueries);
		
		for ($i = 0; $i < $count_searchQueries; $i++)
		{
			agg_search_single($searchQueries[$i]);
		}
	}
		
		function agg_search_single($query)
		{
			global $facebook;
			
			$infoArray  =  $facebook->api($query);
			
			$count_infoArray = count($infoArray["data"]);
			
			for ($i = 0; $i < $count_infoArray; $i++)
			{
				add_to_queue($infoArray["data"][$i]["id"],"page");
				echo $infoArray["data"][$i]["name"]." ----- ".$infoArray["data"][$i]["id"]."<br><br>";
			}
		}
		
		
	
	$searchQueries[0] = "search?q=%20&type=place&center=33.755,-84.39&distance=10000&limit=5000";
	$searchQueries[1] = "search?q=atlanta&type=place&limit=5000";
	$searchQueries[2] = "search?q=GA&type=place&limit=5000";
	
	agg_search($searchQueries);
	
?>