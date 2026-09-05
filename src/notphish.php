<?php
    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
	http_response_code(404);
	header('Content-Type: text/plain; charset=UTF-8');
	echo "Not Found.";
	exit;
    }
    
    $COOKIE_NAME = 'site_notphish';
    $COOKIE_TTL = 0;
    $COOKIE_SECURE = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443;  
    
    if(isset($_COOKIE[$COOKIE_NAME])) {
        $COOKIE_SECURITY = preg_replace('/[^a-z0-9]/u', '', $_COOKIE[$COOKIE_NAME]);
    } else {
        $COOKIE_SECURITY = md5(bin2hex(random_bytes(16)));

        setcookie($COOKIE_NAME, $COOKIE_SECURITY, $COOKIE_TTL, '/', '', $COOKIE_SECURE, true);
    }
?>
