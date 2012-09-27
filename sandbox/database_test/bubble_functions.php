<?php

include_once("../../php/background.php");







	//USE THIS TO REORDER THE BUBBLES ON THE HOME PAGE
	//this will set the database to rider the bubbles according to this array
	function reorder_bubbles(){
	
		$ray  =  array('713d4d27-ed49-11e1-bf61-aafbeaa37357','2809077e-d5ef-11e1-b249-002590605566','ee14ab0a-d5ed-11e1-b249-002590605566','ee14bc9e-d5ed-11e1-b249-002590605566','e099ace7-ed4b-11e1-bf61-aafbeaa37357','ee14c716-d5ed-11e1-b249-002590605566','138814bc-d5ee-11e1-b249-002590605566','647659d4-ed50-11e1-bf61-aafbeaa37357','ee14d968-d5ed-11e1-b249-002590605566','1385e7e6-d5ee-11e1-b249-002590605566','49438594-ed4c-11e1-bf61-aafbeaa37357','8475045d-ed4f-11e1-bf61-aafbeaa37357');

		mysql_query("UPDATE bubbles_list SET orderID = '-1' WHERE 1");
		
		for($i = 0; $i < count($ray); $i++){
			mysql_query("UPDATE bubbles_list SET orderID = '$i' WHERE bubbleID_bubble = '".$ray[$i]."'");
		}

	}





	//if the bubble doesn't already exist, add it
	//return bubbleID
	/*function create_bubble($bubble_name){
		
		mysql_query("INSERT INTO bubbles (name,bubbleID) VALUES ('$bubble_name',uuid())");
		
		$bubbleIDArray = generateQueryArray("SELECT bubbleID FROM bubbles WHERE name = '$bubble_name'");
		return $bubbleIDArray[0]["bubbleID"];
	}*/
	
	function create_bubble($bubbleID = ""){
		
		if($bubbleID != "")
			$match = generateQueryArray("SELECT count(*) FROM bubbles_connections WHERE bubbleID_bubble = '$bubbleID'");
		if($match[0]["count(*)"] != 0)
			return $bubbleID;
		else{
			$uuid = generateQueryArray("SELECT uuid()");
			return $uuid[0][0];
		}
			
	}
	
	
	
	// Generates top bubble with tag
		function generate_top_bubble($tag)
		{
			
			$bubbleID_tag_atlanta  =  "43bf8b06-d071-11e1-b249-002590605566";
			
			$newBubble = create_bubble();
			tag_bubble($newBubble, $bubbleID_tag_atlanta);
			tag_bubble($newBubble, $tag);
			
		}
		
		
		
	// Generates the top-level bubbles. CURRENTLY DOES NOT PREVENT DUPLICATES.
		function generate_top_bubbles()
		{
			//$tags  =  array("81327104-d028-11e1-b249-002590605566","8132730c-d028-11e1-b249-002590605566","8132726c-d028-11e1-b249-002590605566","8132715e-d028-11e1-b249-002590605566","81327456-d028-11e1-b249-002590605566");
			
						
			$bubbleID_tag_atlanta  =  "43bf8b06-d071-11e1-b249-002590605566";
			
			foreach ($tags as $tag)
			{
				$newBubble = create_bubble();
				tag_bubble($newBubble, $bubbleID_tag_atlanta);
				tag_bubble($newBubble, $tag);
			}
		}
		
	//if the tag doesn't already exist, add it
	//return bubbleID
	function create_tag($tag_name, $type, $bubbleID = "false"){
	
		if($bubbleID == "false")
			mysql_query("INSERT INTO tags_list (tag,type,bubbleID) VALUES ('$tag_name','$type',uuid())");
		else
			mysql_query("INSERT INTO tags_list (tag,type,bubbleID) VALUES ('$tag_name','$type','$bubbleID')");
		
		$bubbleIDArray = generateQueryArray("SELECT bubbleID FROM tags_list WHERE tag = '$tag_name'");
		return $bubbleIDArray[0]["bubbleID"];
	}




	function link_bubbles($bubbleID_parent, $bubbleID_child){
		
		mysql_query("INSERT INTO bubbles_families (bubbleID_parent,bubbleID_child) VALUES ('$bubbleID_parent','$bubbleID_child')");
		echo "<br/>linked bubbles: parent: ".$bubbleID_parent."      child: ".$bubbleID_child."<br/>";
	}




	function tag_bubble($bubbleID_bubble, $bubbleID_tag){
	
		mysql_query("INSERT INTO bubbles_connections (bubbleID_bubble,bubbleID_tag) VALUES ('$bubbleID_bubble','$bubbleID_tag')");
		echo "<br/>tagged bubble: ".$bubbleID_bubble."      tag: ".$bubbleID_tag.")<br/>";
	}
	
	
	
	
	
	function give_tag_points_for_word($tag_id, $word, $points){
	
		
		
		mysql_query("INSERT INTO tags_words (word, points, tag) VALUES ('$word','$points','$tag_id')");
	}
	
	
	
		
	function generate_all_bubbles(){
	
		//get each entry in tag's families
			$tags_families = generateQueryArray("SELECT tag,parent FROM tags_families");
			$numFams = count($tags_families);
			
						
		//for each entry, find bubbles that match that entry
			for($f = 0; $f < $numFams; $f++){

				$bubbleID_parent 	= $tags_families[$f]["parent"];
				$bubbleID_tag 		= $tags_families[$f]["tag"];
				
				echo "<br/>looking for bubbles tagged with ".$bubbleID_parent;
				
				$bubbles_matching = generateQueryArray("SELECT bubbleID_bubble FROM bubbles_connections WHERE bubbleID_tag = '$bubbleID_parent'");
				$num_bubbles_tagged = count($bubbles_matching);
				
				//go through bubble that are tagged with the parent tag
				for($m = 0; $m < $num_bubbles_tagged; $m++){
					$bubbleID = $bubbles_matching[$m]["bubbleID_bubble"];
					
					echo "<br/>    changing bubble ".$bubbleID." to be tagged with ".$bubbleID_tag;
					
					//gather all the tags except the parent one
						$bubbles_tags = generateQueryArray_flat("SELECT bubbleID_tag FROM bubbles_connections WHERE bubbleID_bubble = '$bubbleID' AND bubbleID_tag != '$bubbleID_parent'");
						//add child tag to the end
						array_push($bubbles_tags,$bubbleID_tag);
						
						$numTags = count($bubbles_tags);
					
					//make sure there isn't already a child bubble tagged with all the same tags
						$children = generateQueryArray_flat("SELECT bubbleID_child FROM bubbles_families");
						$num_children = count($children);
						for($c = 0; $c < $num_children; $c++){
							//test each child for similarity to new entry
							$childID = $children[$c];
							$bubbles_tags2 = generateQueryArray_flat("SELECT bubbleID_tag FROM bubbles_connections WHERE bubbleID_bubble = '$childID'");
							$diff = array_diff($bubbles_tags, $bubbles_tags2);
							$cont = false;
							if(count($diff) == 0){
								$cont = true;
								link_bubbles($bubbleID, $childID);
								break;
							}
						}
						if($cont)
							continue;
					
					//duplicate the bubble but change the parent to the tag
						$bubbleID_new = create_bubble();
					
					//tag the new bubble with other tags
						for($t = 0; $t < $numTags; $t++){
							tag_bubble($bubbleID_new, $bubbles_tags[$t]);
						}
						
					//link bubbles
						link_bubbles($bubbleID, $bubbleID_new);
					
				}				
			
			}
			
	}	


	//generate_all_bubbles();
	//generate_top_bubbles();
	
?>