<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>ECP Update 3.01 -> 3.02 Beta</title>
<style type="text/css">
<!--
body {
	background-color: #333333;
}
body,td,th {
	color: #CCCCCC;
}
a:link {
	color: #CCCCCC;
}
a:visited {
	color: #CCCCCC;
}
a:hover {
	color: #CCCCCC;
}
a:active {
	color: #CCCCCC;
}
-->
</style></head>

<body>
<?php
if(isset($_GET['do']) AND $_GET['do'] == 'update') {
	include('inc/include.php');
	if(VERSION == '3.01') {
		$db->query('ALTER TABLE `'.DB_PRE.'ecp_calendar` ADD `datum2` int(10) unsigned NOT NULL AFTER `datum`;');
        $db->query('CREATE TABLE `'.DB_PRE.'ecp_games_user` (`game_id` int(11) NOT NULL, `user_id` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;');		
		$db->query('ALTER TABLE `'.DB_PRE.'ecp_wars_games` ADD `icon_big` varchar(255) NOT NULL AFTER `icon`;');
        //$db->query('ALTER TABLE `'.DB_PRE.'ecp_settings` ADD `USER_MAX_GAMES` INT( 11 ) NOT NULL AFTER `USER_KLICK_RELOAD`;');
        $db->query('UPDATE '.DB_PRE.'ecp_wars_games SET  icon_big = "Counterstrike.png" WHERE gameshort="CS";');
        $db->query('UPDATE '.DB_PRE.'ecp_wars_games SET  icon_big = "Counterstrike Source.png" WHERE gameshort="CSS";');										
		$db->query('UPDATE '.DB_PRE.'ecp_settings SET VERSION = "3.02 Beta";');
		echo 'Sollte kein Fehler zu sehen sein, war das Update erfolgreich.';
	} else {
		echo 'Update wurde bereits durchgeführt oder du nutzt nicht die Version 3.01';
	}
} else {
?>
<a align="center" href="update.php?do=update">Update von ECP-Version 3.01 auf Version 3.02 Beta starten.</a>
<?php
}
?>
</body>

</html>