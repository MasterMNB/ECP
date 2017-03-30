<?php
	// Sprache auswählen
	if(isset($_SESSION['lang'])) {
		if(file_exists('inc/language/'.$_SESSION['lang'].'.php')) {
			define('LANGUAGE', $_SESSION['lang']);
		} 
	} elseif (isset($_COOKIE['lang'])) {
		if(file_exists('inc/language/'.$_COOKIE['lang'].'.php')) {
			define('LANGUAGE', $_COOKIE['lang']);
		}
	} 
    
	define('CHMOD', 0775);
    define('ECP_TIMEZONE', 'Europe/Berlin'); // http://php.net/manual/en/timezones.php 
    define('ECP_CHARSET', 'ISO-8859-1'); 
    define('ECP_DB_CHARSET', 'latin1'); 
    define('ECP_ENTITIES_FLAGS', defined('ENT_HTML401') ? ENT_COMPAT | ENT_HTML401 : ENT_COMPAT);
?>