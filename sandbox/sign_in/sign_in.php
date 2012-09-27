<?php include_once("../../../php/background.php"); ?>

<?php

	
	//include_once("functions/login_all.php");
	
	// Includes read_stream in the permissions request
		if ($_GET['read_stream'] == "true")
		{
			$req_perms .=  ",read_stream";
			echo "$req_perms<br>";
			$login_url  =  $facebook->getLoginUrl(array("req_perms" => "$req_perms"));
		}
	
	if(empty($session))
	{
		echo "<META HTTP-EQUIV='Refresh' Content='0; URL=$login_url'>";
		die();
	}
	
	echo $session["uid"]."<br/>";
	echo $session["access_token"]."<br/>";

	//Gets an extended access token for the user, with a 60 day expiration time
	$extendedAccessTokenGrab = cURL_standard("https://graph.facebook.com/oauth/access_token?client_id=$app_id&client_secret=$app_secret&grant_type=fb_exchange_token&fb_exchange_token=".$session["access_token"]);
	
	$extendedAccessToken = get_string_between($extendedAccessTokenGrab, "access_token=", "&");
	
	print_r($extendedAccessTokenGrab);	

	
	//Make this check if the accountFacebookID is already in the table, and update the table with the new access_token if so
	$existingTest = generateQueryArray
    ("
    	SELECT	count(*)
    	FROM	access_tokens
    	WHERE	accountFacebookID	=	'".$session["uid"]."'
    ");

    if ($existingTest[0]["count(*)"] == 0)
    {
    	mysql_query
    	("
    	    INSERT INTO 
    	    access_tokens
    	    (accountFacebookID, access_token)
    	    VALUES 
    	    ('".$session["uid"]."', '".$session["access_token"]."')
    	");
    }
    else
    {
    	mysql_query
    	("
    	    UPDATE	access_tokens
    	    SET		access_token		=	'$extendedAccessToken'
    	    WHERE	accountFacebookID	=	'".$session["uid"]."'
    	");
    }

	
?>