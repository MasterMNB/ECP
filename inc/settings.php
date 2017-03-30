<?php
    // Datenbank Objekt anlegen
    $db = new db();
	// Alle Einstellungen der Seite aus der Datenbank holen und als Konstanten definieren.
	$db->query('SELECT * FROM '.DB_PRE.'ecp_settings');
	foreach ($db->fetch_assoc() as $key => $value) {
		if($key == 'LANGUAGE') define('DEFAULT_LANG', $value);
		@define($key, $value);	
	}
?>