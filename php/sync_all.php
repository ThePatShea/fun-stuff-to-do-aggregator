<?php

        include_once("background.php");
	
	sync_query("agg_events_from_users"  , "friends");
	sync_query("sync_events_basic_info" , "full"   );
	sync_query("sync_pages_basic_info"  , "full"   );
	sync_query("agg_events_from_pages_and_groups" , "graph");

?>
