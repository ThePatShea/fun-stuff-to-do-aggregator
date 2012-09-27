<?php include_once("../background/background.php"); ?>

<?php

	setcookie('fbs_'.$facebook->getAppId(), '', time()-100, '/', 'thecampusbubble.com');
	session_destroy();
	
	echo "You have successfully logged out.";

?>