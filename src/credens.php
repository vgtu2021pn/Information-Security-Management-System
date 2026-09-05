<?php
	if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
		http_response_code(404);
		header('Content-Type: text/plain; charset=UTF-8');
		echo "Not Found.";
		exit;
	}
	
	$servername = "localhost";
    	$user = "database_user_name";
    	$pw = "database_user_password";
    	$db = "database_name";
?>
