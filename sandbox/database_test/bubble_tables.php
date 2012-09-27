<?php

	include_once("../../php/background.php");
	
	
	function name_bubble($bubbleID){
		$tags = generateQueryArray_flat("SELECT bubbleID_tag FROM bubbles_connections WHERE bubbleID_bubble = '$bubbleID'");
		$num = count($tags);
		
		for($c = 0; $c < $num; $c++){
			if($c != 0)
				$finalName .= " ";
				
			$tag = $tags[$c];
			$name = generateQuery_singleVar("SELECT tag FROM tags_list WHERE bubbleID = '$tag'");
			if($name == "")
				$name = generateQuery_singleVar("SELECT name FROM accounts WHERE accountFacebookID = '$tag'");
			
			$nick = generateQuery_singleVar("SELECT nickname FROM nicknames WHERE bubbleID = '$tag'");
			if($nick != "")
				$name = $nick;
			
			$finalName .= $name;
		}
		
		return $finalName;
		
	}



	
	function translate_bubbles_connections_table(){
		echo "<div class='header_small'>innerID</div>";
		echo "<div class='header_small'>bubbleID_bubble</div>";
		echo "<div class='header_small'>bubble</div>";
		echo "<div class='header_small'>bubbleID_tag</div>";
		echo "<div class='header_small'>tag</div>";
	
	
		$bubbles_connections = generateQueryArray("
								SELECT bubbleID_bubble, bubbleID_tag, innerID
								FROM bubbles_connections
								ORDER BY bubbleID_bubble
								");
		
		$numConnections = count($bubbles_connections);
		
		echo "<div class='container'>";
			for($c = 0; $c < $numConnections; $c++){
				
				$innerID		 	= $bubbles_connections[$c]["innerID"];
				$bubbleID_bubble 	= $bubbles_connections[$c]["bubbleID_bubble"];
				$bubbleID_tag		= $bubbles_connections[$c]["bubbleID_tag"];
			
				//$bubbleArray	= generateQueryArray("SELECT name FROM bubbles WHERE bubbleID = '$bubbleID_bubble'");
				//$bubble = $bubbleArray[0]["name"];
				$bubbleName = name_bubble($bubbleID_bubble);
				$tagArray		= generateQueryArray("SELECT tag FROM tags_list WHERE bubbleID = '$bubbleID_tag'");
				$tag = $tagArray[0]["tag"];
				if($tag == ""){
					$tagArray		= generateQueryArray("SELECT name FROM accounts WHERE accountFacebookID = '$bubbleID_tag'");
					$tag = $tagArray[0]["name"];
				}
				
				echo "<div class='row'>";
					echo "<div class='cell_small'>".$innerID."</div>";
					echo "<div class='cell_small'>".$bubbleID_bubble."</div>";
					echo "<div class='cell_small'>".$bubbleName."</div>";
					echo "<div class='cell_small'>".$bubbleID_tag."</div>";
					echo "<div class='cell_small'>".$tag."</div>";
				echo "</div>";
						
			
			}
		echo "</div>";
	
	}
	
	
	
	
	function translate_bubbles_families_table(){
		
		echo "<div class='header_small'>innerID</div>";
		echo "<div class='header_small'>bubbleID_parent</div>";
		echo "<div class='header_small'>parent</div>";
		echo "<div class='header_small'>bubbleID_child</div>";
		echo "<div class='header_small'>child</div>";
	
	
		$bubbles_families = generateQueryArray("
								SELECT bubbleID_parent, bubbleID_child, innerID
								FROM bubbles_families
								ORDER BY bubbleID_parent
								");
		
		$numConnections = count($bubbles_families);
		
		echo "<div class='container'>";
			for($c = 0; $c < $numConnections; $c++){
				
				$innerID			= $bubbles_families[$c]["innerID"];
				$bubbleID_parent 	= $bubbles_families[$c]["bubbleID_parent"];
				$bubbleID_child		= $bubbles_families[$c]["bubbleID_child"];
			
				//$bubbleArray	= generateQueryArray("SELECT name FROM bubbles WHERE bubbleID = '$bubbleID_parent'");
				//$bubble = $bubbleArray[0]["name"];
				//$bubbleArray2	= generateQueryArray("SELECT name FROM bubbles WHERE bubbleID = '$bubbleID_child'");
				//$bubble2 = $bubbleArray2[0]["name"];
				$bubbleName1 = name_bubble($bubbleID_parent);
				$bubbleName2 = name_bubble($bubbleID_child);

				
				echo "<div class='row'>";
					echo "<div class='cell_small'>".$innerID."</div>";
					echo "<div class='cell_small'>".$bubbleID_parent."</div>";
					echo "<div class='cell_small'>".$bubbleName1."</div>";
					echo "<div class='cell_small'>".$bubbleID_child."</div>";
					echo "<div class='cell_small'>".$bubbleName2."</div>";
				echo "</div>";
						
			
			}
		echo "</div>";
	
	}
	
	
	
	
	function translate_tags_families_table(){

		echo "<div class='header'>innerID</div>";
		echo "<div class='header'>tag</div>";
		echo "<div class='header'>parent</div>";
	
		$tags_families = generateQueryArray("
								SELECT parent, tag, innerID
								FROM tags_families
								ORDER BY parent
								");
		
		$numConnections = count($tags_families);
		
		echo "<div class='container'>";
			for($c = 0; $c < $numConnections; $c++){
				
				$innerID	= $tags_families[$c]["innerID"];
				$parent 	= $tags_families[$c]["parent"];
				$tag		= $tags_families[$c]["tag"];
			
				$tagArray	= generateQueryArray("SELECT tag FROM tags_list WHERE bubbleID = '$parent'");
				$tag1 = $tagArray[0]["tag"];
				$tagArray2	= generateQueryArray("SELECT tag FROM tags_list WHERE bubbleID = '$tag'");
				$tag2 = $tagArray2[0]["tag"];
				
				if($tag1 == "")
					$tag1 = generateQuery_singleVar("SELECT name FROM accounts WHERE accountFacebookID = '$parent'");
				if($tag2 == "")
					$tag2 = generateQuery_singleVar("SELECT name FROM accounts WHERE accountFacebookID = '$tag'");

				
				echo "<div class='row'>";
					echo "<div class='cell'>".$innerID."</div>";
					echo "<div class='cell'>".$tag2."</div>";
					echo "<div class='cell'>".$tag1."</div>";
				echo "</div>";
						
			
			}
		echo "</div>";
	
	}


	
	
	function translate_tags_table(){
		
		echo "<div class='header'>innerID</div>";
		echo "<div class='header'>bubbleID</div>";
		echo "<div class='header'>tag</div>";
	
		
		$tagArray = generateQueryArray("
								SELECT innerID, bubbleID, tag
								FROM tags
								");
		
		$numConnections = count($tagArray);
		
			for($c = 0; $c < $numConnections; $c++){
				
				$innerID			= $tagArray[$c]["innerID"];
				$bubbleID 			= $tagArray[$c]["bubbleID"];
				$tag				= $tagArray[$c]["tag"];
			
				$tagArray2	= generateQueryArray("SELECT tag FROM tags_list WHERE bubbleID = 'aedc1538-d06e-11e1-b249-002590605566'");
				if(count($tagArray2[0]) < 1)
					continue;
				else
					$tag2 = $tagArray2[0]["tag"];
				
				
					echo "<br/>".$innerID.". . . . . . . . . .".$bubbleID." . . . . . . . . . ".$tag."<br/>";
				
						
			
			}
	
	}




	echo "<div class='container'>";
		translate_bubbles_families_table();
	echo "</div>";
		
	echo "<div class='container'>";
		translate_bubbles_connections_table();
	echo "</div>";
	
	echo "<div class='container'>";
		translate_tags_families_table();
	echo "</div>";
?>


<style>

	.container
	{
		width:			1600px;
	}
	
	
	.cell
	{
		color:			black;
		text-align:		center;
		font-size:		18;
		height:			20px;
		width:			400px;
		display:		inline;
		float:			left;
		padding:		10px 20px;
		border:			2px solid black;
		background:		inherit;
	}
	.cell_small
	{
		color:			black;
		text-align:		center;
		font-size:		18;
		height:			40px;
		width:			250px;
		display:		inline;
		float:			left;
		padding:		10px 20px;
		border:			2px solid black;
		background:		inherit;
	}
	
	.row:nth-child(odd)
	{
		background:		#70A4D2;
	}
	
	.row:nth-child(even)
	{
		background:		#ADCCE9;
	}
	
		
	.header
	{
		color:			black;
		background: 	#FFCA80;
		text-align:		center;
		font-size:		18;
		height:			20px;
		width:			400px;
		display:		inline;
		float:			left;
		padding:		10px 20px;
		border:			2px solid black;
	
	}
	.header_small
	{
		color:			black;
		background: 	#FFCA80;
		text-align:		center;
		font-size:		18;
		height:			20px;
		width:			250px;
		display:		inline;
		float:			left;
		padding:		10px 20px;
		border:			2px solid black;
	
	}




</style>