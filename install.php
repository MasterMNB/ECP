<?php     define('VERSION', '3.02 Beta'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Easy Clanpage Version <?php echo VERSION; ?></title>
<link rel="stylesheet" type="text/css" media="all" href="templates/Standard/style.css" />
</head>

<body>
<div class="content_head" style="background:url('templates/Standard/images/site/menu_empty.jpg') repeat-x;">
  <div class="content_head_link"><strong>Installation - Schritt <?php if(isset($_POST['step'])) echo $_POST['step'].'/4'; else echo 'Lizenzabkommen'; ?></strong></div>
</div>
<div class="content_content">
<?php
    error_reporting(E_ALL);
    define('DESIGN', 'Standard');

    include('inc/functions.php');
    include('inc/smarty/Smarty.class.php');
    include('templates/Standard/design.php');
    switch(@$_POST['step']) {
    	case '1':
    		step1();
    	break;
    	case '2':
    		step2();
    	break;
    	case '3':
	        IF($_POST['dbserver'] == "" OR $_POST['dbname'] == "" OR $_POST['dbuser'] == "" OR $_POST['admin_email'] == ""  OR $_POST['url'] == ""  OR $_POST['clanname'] == ""  OR $_POST['clanshort'] == "") {
	            table('Fehler', 'Es müssen alle Felder mit einem * ausgefüllt werden.');
				step2();
	        } elseif (!@$mysql_conid=mysql_pconnect($_POST['dbserver'], $_POST['dbuser'],$_POST['dbpw'])) {
	            table('Fehler', 'Es konnte keine Verbindung zum Datenbankserver aufgebaut werden. <strong>Grund: '.mysql_error().'</strong>');
				step2();
            } elseif (!@mysql_set_charset('latin1',$mysql_conid)) { //PHP 5 >= 5.2.3
	            table('Fehler', 'Charset latin1 konnte nicht ausgewählt werden. <strong>Grund: '.mysql_error().'</strong>');
				step2();
	        } elseif (!@mysql_select_db($_POST['dbname'])) {
	            table('Fehler', 'Die Datenbank '.$_POST['dbname'].' konnte nicht ausgewählt werden. <strong>Grund: '.mysql_error().'</strong>');
				step2();
	        } else {
	            IF($_POST['tbl_pre'] !== "") {
	                IF(!substr_count($_POST['tbl_pre'], '_')) {
	                    $_POST['tbl_pre'] .= '_';
	                } elseif (strrpos($_POST['tbl_pre'], '_') !== strlen($_POST['tbl_pre'])-1) {
	                    $_POST['tbl_pre'] .= '_';
	                }
	            }
	            if(make_config($_POST['dbserver'], $_POST['dbuser'], $_POST['dbpw'], $_POST['dbname'], $_POST['tbl_pre'])) {
	            	install_tables();
	            	step3();
	            } else {
		            table('Fehler','Es konnte keine Datei in /inc angelegt werden. Überprüfe ob PHP Schreibrechte besitzt');
					step2();
	            }
	        }
    	break;
    	case '4':
    		include('inc/db.daten.php');
    		$mysql_conid=mysql_pconnect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
            mysql_set_charset('latin1',$mysql_conid);//PHP 5 >= 5.2.3
    		mysql_select_db(MYSQL_DATABASE);
            $sql = 'INSERT INTO '.DB_PRE.'ecp_user (username, email, passwort, country, sex, status, registerdate,rID) VALUES
                   (\''.mysql_real_escape_string($_POST['username']).'\',
                   \''.mysql_real_escape_string($_POST['email']).'\',
                   \''.sha1($_POST['pw1']).'\',
                   \'de\',
                   \'male\',
                   \'1\',
                   '.time().', 17);';
            IF(mysql_query($sql)) {
                echo "<p>Die Installation wurde erfolgreich ausgeführt!</p>\n";
                echo "<p>Weitere Einstellungsmöglichkeiten finden sie im Administrationsbereich unter\n";
                echo "Einstellungen. Ebenso kannst Du dort auch das Design ändern.<br>\n";
                echo "<br>\n";
                echo "<b>L&ouml;sche bitte die install.php aus dem Verzeichnis, da diese ausschließlich für eine\n";
                echo "Installation vorgesehen ist.</b><br>\n";
                echo "<br>\n";
                echo "Danach ist die Seite verfügbar.</p>\n";
                echo "<p>Vielen Dank, dass Du dich für <a href=\"http://www.easy-clanpage.de\" target=\"_blank\">\n";
                echo "Easy-Clanpage</a> entschieden haben.</p>\n";
            } else {
                table('Fehler', 'Fehler beim einfügen des Admins in die Datenbank.');
				step3();
            }
    	break;   	    	
    	default:
    		step0();
    }
    function step0() {
		$lizenz = file_get_contents('lizenz.txt');
		?>
		<textarea name="textarea" id="textarea" cols="65" rows="15"><?php echo $lizenz; ?></textarea>
		<br />
		<br />
		<form id="form1" name="form1" method="post" action="install.php">
		  <input type="hidden" value="1" name="step" />
		  <input type="submit" name="button" id="button" value="Akzeptieren und Installation fortsetzen" />
		  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="javascript: window.location.href='http://www.easy-clanpage.de'" value="Ablehnen" />
		</form>
		<?php
    }
    function step1() {
        @chmod("inc",0777);
        @chmod("inc/javascript/tinymce",0777);
        @chmod("templats_c",0777);
        @chmod("downloads",0777);
        @chmod("images/avatar",0777);
        @chmod("images/gallery",0777);
        @chmod("images/ranks",0777);
        @chmod("images/screens",0777);
        @chmod("images/smilies",0777);
        @chmod("images/teams",0777);
        @chmod("images/topics",0777);
        @chmod("images/user",0777);
        @chmod("uploads/forum",0777);    	
    	ob_start();
    	phpinfo();
    	$content = ob_get_contents();
    	ob_end_clean();
    	preg_match('#>Client API version </td><td class="v">(\d\.\d+\.\d+)#', $content, $result);
    	$mysql = (isset($result[1]) ? $result[1] : 'unbekannt');
 ?>
   <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td>&nbsp;</td>
      <td><strong>Benötigt:</strong></td>
      <td><strong>Vorhanden:</strong></td>
    </tr>
    <tr>
      <td>PHP Version:</td>
      <td>&gt;5.2.3</td>
      <td><span class="<?php echo (phpversion() > '5.2.3' ?  'member_aktiv">' : 'member_inaktiv">'); echo phpversion(); ?></span></td>
    </tr>
    <tr>
      <td>MySQL Version:</td>
      <td>&gt;4.0.0</td>
      <td><span class="<?php echo ($mysql > '4.0.0' ?  'member_aktiv">' : 'member_inaktiv">'); echo $mysql; ?></span></td>
    </tr>
    <tr>
      <td>Grafikbibliothek:</td>
      <td>aktiviert</td>
      <td><span class="<?php echo (function_exists('gd_info') ?  'member_aktiv">aktiviert' : 'member_inaktiv">deaktiviert'); ?></span></td>
    </tr>
    <tr>
      <td>PHP Befehl "fsockopen":</td>
      <td>aktiviert</td>
      <td><span class="<?php echo (function_exists('fsockopen') ?  'member_aktiv">aktiviert' : 'member_inaktiv">deaktiviert'); ?></span></td>
    </tr>    
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr> 
    <tr>
      <td><strong>Schreibrechte in:</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>/downloads</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('downloads') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/avatar</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/avatar') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/gallery</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/gallery') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/ranks</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/ranks') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/screens</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/screens') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/smilies</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/smilies') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/teams</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/teams') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/topics</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/topics') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/images/user</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('images/user') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/inc</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('inc') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/inc/javascript/tinymce</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('inc/javascript/tinymce') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>    
    <tr>
      <td>/templates_c</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('templates_c') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
    <tr>
      <td>/uploads/forum</td>
      <td>ja</td>
      <td><span class="<?php echo (is_writeable('uploads/forum') ?  'member_aktiv">ja' : 'member_inaktiv">nein'); ?></span></td>
    </tr>
  </table>
  <br />
  <form method="post">
  <input type="hidden" value="2" name="step" />
  <input type="submit" value="Weiter mit Schritt 2" />    
  </form>
 <?php
    }
    function step2() { 
?>
<form method="POST" action="install.php">
  <table border="0" width="100%" cellspacing="0">
    <tr>
      <td colspan="2">
        <b>Konfiguration von wichtigen Einstellungen:
          <br>&nbsp;</b>
      </td>
    </tr>
    <tr>
      <td width="100%" colspan="2">
        <b>Datenbankeinstellungen:</b>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Adresse vom Datenbankserver:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="dbserver" size="40" value="<?php echo @$_POST['dbserver']; ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Datenbank-Name:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="dbname" size="40" value="<?php echo @$_POST['dbname']; ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Datenbank-Benutzername:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="dbuser" size="40" value="<?php echo @$_POST['dbuser']; ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Datenbank-Passwort:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="dbpw" size="40" value="<?php echo @$_POST['dbpw']; ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000">
       <a href="#" onclick="return false;" title="Falls ein zweites System in einer Datenbank installiert werden soll.">Tabellenprefix</a>:
      </td>
      <td width="70%" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000">
        <input type="text" name="tbl_pre" size="40" value="<?php echo @$_POST['tbl_pre']; ?>">
      </td>
    </tr>
    <tr>
      <td width="100%" colspan="2">&nbsp;
        
      </td>
    </tr>
    <tr>
      <td width="100%" colspan="2">
        <b>Seiteneinstellungen:</b>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Emailadresse des Admins:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="admin_email" size="40" value="<?php echo @$_POST['admin_email']; ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        URL der Seite:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="url" size="40" value="<?php echo (isset($_POST['url']) ? $_POST['url'] : 'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF']) - 11)); ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Clanname:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="clanname" size="40" value="<?php echo @$_POST['clanname']; ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000">
        Clankürzel:
      </td>
      <td width="70%" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000">
        <input type="text" name="clanshort" size="40" value="<?php echo @$_POST['clanshort']; ?>">
        <span class="error">
          *
        </span>
      </td>
    </tr>
  </table>
  <span class="error">
    * Diese Felder müssen ausgefüllt werden.
    <br>
    Bereits existierende Tabellen werden gelöscht!
  </span>
  <p align="center">Der nächste Schritt kann ein paar Sekunden in Anspruch nehmen!<br>
  </p>
    <input type="submit" value="Weiter mit der Installation der DB-Einträge">
  <input type="hidden" value="3" name="step">
  </p>
</form>
<?php
    }
   function step3() {
        $username = @$_POST['username'];
        $email = @$_POST['email'];
?>
<form method="POST" action="install.php">
  <table border="0" width="100%" cellspacing="0">
    <tr>
      <td colspan="2">
        <b>Administrator anlegen:</b>
      <b>
          <br>&nbsp;</b>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Username:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="username" size="40" value="<?php echo $username; ?>">
        <font class="error">
          *
        </font>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Email-Adresse:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="text" name="email" size="40" value="<?php echo $email; ?>">
        <font class="error">
          *
        </font>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-top: 1px solid #000000">
        Passwort:
      </td>
      <td width="70%" style="border-top: 1px solid #000000">
        <input type="password" name="pw1" size="40">
        <font class="error">
          *
        </font>
      </td>
    </tr>
    <tr>
      <td width="30%" style="border-bottom:1px solid #000000; border-top:1px solid #000000; ">
        Passwort wiederholen:
      </td>
      <td width="70%" style="border-bottom:1px solid #000000; border-top:1px solid #000000; ">
        <input type="password" name="pw2" size="40">
        <font class="error">
          *
        </font>
      </td>
    </tr>
    </table>
	<p align="center">&nbsp;</p>
    <input type="submit" value="Weiter mit der Installation des Admins">
  <input type="hidden" value="4" name="step">
  </p>
</form>
<?php
    }    
    function make_config($dbserver, $dbuser, $dbpw, $dbname, $tbl_pre) {
        $file=fopen("inc/db.daten.php","w+");
        fwrite($file,'<?php
    define(\'MYSQL_HOST\', \''.$dbserver.'\');     // MySQL-Serveraddresse
    define(\'MYSQL_USER\', \''.$dbuser.'\');          // Datenbankbenutzer
    define(\'MYSQL_PASS\', \''.$dbpw.'\');              // Datenbankpasswort
    define(\'MYSQL_DATABASE\', \''.$dbname.'\');    // Datenbank
    define(\'DB_PRE\', \''.$tbl_pre.'\');                  // Tabellen prefix
?>');
        fclose($file);
        return file_exists('inc/db.daten.php');
    } 
    function install_tables() {
		$data = "
--
-- Datenbank: `ECP`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_awards`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_awards` (
  `awardID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `eventname` varchar(40) NOT NULL,
  `eventdatum` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `platz` tinyint(1) unsigned NOT NULL,
  `teamID` smallint(4) unsigned NOT NULL,
  `gID` smallint(4) unsigned NOT NULL,
  `preis` varchar(50) NOT NULL,
  `bericht` text NOT NULL,
  `spieler` varchar(400) NOT NULL,
  `eingetragen` int(10) unsigned NOT NULL,
  PRIMARY KEY (`awardID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_buddy`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_buddy` (
  `userID` smallint(5) unsigned NOT NULL,
  `buddyID` smallint(5) unsigned NOT NULL,
  KEY `userID` (`userID`,`buddyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_calendar`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_calendar` (
  `calID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `eventname` varchar(50) NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `datum2` int(10) unsigned NOT NULL,
  `inhalt` text NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  `access` varchar(400) NOT NULL DEFAULT '',
  PRIMARY KEY (`calID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_clankasse`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_clankasse` (
  `empfaenger` varchar(255) NOT NULL,
  `kontonummer` varchar(255) NOT NULL DEFAULT '0',
  `bankleitzahl` varchar(255) NOT NULL DEFAULT '0',
  `IBAN` varchar(25) NOT NULL DEFAULT '',
  `SWIFT` varchar(14) NOT NULL DEFAULT '',
  `institut` varchar(255) NOT NULL,
  `kontostand` float NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_clankasse_auto`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_clankasse_auto` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `verwendung` varchar(255) NOT NULL,
  `intervall` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `betrag` float NOT NULL DEFAULT '0',
  `nextbuch` int(10) unsigned NOT NULL,
  `tagmonat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_clankasse_member`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_clankasse_member` (
  `userID` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `monatgeld` float NOT NULL DEFAULT '0',
  `verwendung` varchar(255) NOT NULL DEFAULT '',
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_clankasse_transaktion`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_clankasse_transaktion` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `geld` float NOT NULL DEFAULT '0',
  `verwendung` varchar(255) NOT NULL DEFAULT '',
  `datum` int(10) NOT NULL DEFAULT '0',
  `userID` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `vonuser` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_cms`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_cms` (
  `cmsID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `headline` text NOT NULL,
  `content` mediumtext NOT NULL,
  `access` varchar(255) NOT NULL DEFAULT '',
  `views` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cmsID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_comments`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_comments` (
  `comID` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `subID` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `bereich` varchar(15) NOT NULL,
  `userID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `author` varchar(30) NOT NULL DEFAULT '',
  `beitrag` text NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `homepage` varchar(255) NOT NULL DEFAULT '',
  `datum` int(10) unsigned NOT NULL,
  `editdatum` int(10) unsigned NOT NULL DEFAULT '0',
  `editby` smallint(5) unsigned NOT NULL DEFAULT '0',
  `edits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `IP` varchar(15) NOT NULL,
  PRIMARY KEY (`comID`),
  KEY `bereich` (`bereich`),
  KEY `subID` (`subID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_downloads`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_downloads` (
  `dID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cID` smallint(4) unsigned NOT NULL,
  `name` varchar(70) NOT NULL,
  `url` varchar(255) NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  `info` text NOT NULL,
  `homepage` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `traffic` bigint(15) unsigned NOT NULL DEFAULT '0',
  `downloads` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `datum` int(10) unsigned NOT NULL,
  `access` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`dID`),
  KEY `cID` (`cID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_downloads_kate`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_downloads_kate` (
  `kID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `subkID` smallint(4) unsigned NOT NULL DEFAULT '0',
  `kname` varchar(40) NOT NULL,
  `beschreibung` text NOT NULL,
  `access` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`kID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_fightus`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_fightus` (
  `fightusID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `gID` smallint(4) unsigned NOT NULL,
  `mID` smallint(4) unsigned NOT NULL,
  `teamID` smallint(4) unsigned NOT NULL,
  `clanname` varchar(40) NOT NULL,
  `homepage` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `icq` varchar(11) NOT NULL,
  `skype` varchar(50) NOT NULL,
  `msn` varchar(255) NOT NULL,
  `datum` int(11) unsigned NOT NULL,
  `wardatum` int(11) unsigned NOT NULL,
  `serverip` varchar(100) NOT NULL,
  `info` text NOT NULL,
  `bearbeitet` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vonID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `IP` varchar(15) NOT NULL,
  PRIMARY KEY (`fightusID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_abo`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_abo` (
  `aboID` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `thID` mediumint(6) NOT NULL,
  `boID` smallint(4) unsigned NOT NULL,
  `userID` smallint(6) NOT NULL,
  PRIMARY KEY (`aboID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_attachments`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_attachments` (
  `attachID` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `bID` smallint(4) unsigned NOT NULL,
  `tID` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `mID` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `userID` smallint(5) unsigned NOT NULL,
  `IP` varchar(15) NOT NULL,
  `uploadzeit` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` mediumint(8) unsigned NOT NULL,
  `downloads` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `strname` varchar(64) NOT NULL,
  `validation` varchar(16) NOT NULL,
  PRIMARY KEY (`attachID`),
  KEY `bID` (`bID`,`tID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_boards`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_boards` (
  `boardID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `boardparentID` smallint(4) unsigned NOT NULL DEFAULT '0',
  `name` varchar(70) NOT NULL DEFAULT '',
  `beschreibung` varchar(500) NOT NULL,
  `threads` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `posts` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `lastpost` int(11) unsigned NOT NULL DEFAULT '0',
  `lastthreadID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lastpostuserID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lastpostuser` varchar(25) NOT NULL DEFAULT '',
  `posi` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `isforum` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `commentsperpost` tinyint(3) unsigned NOT NULL,
  `moneyperpost` float unsigned NOT NULL,
  `attachments` tinyint(3) unsigned NOT NULL,
  `attachmaxsize` mediumint(8) unsigned NOT NULL,
  `rightsread` varchar(255) NOT NULL,
  `threadopen` varchar(255) NOT NULL,
  `postcom` varchar(255) NOT NULL,
  `editcom` varchar(255) NOT NULL,
  `startsurvey` varchar(255) NOT NULL,
  `votesurvey` varchar(255) NOT NULL,
  `attachfiles` varchar(255) NOT NULL,
  `downloadattch` varchar(255) NOT NULL,
  `threadclose` varchar(255) NOT NULL,
  `threaddel` varchar(255) NOT NULL,
  `threadmove` varchar(255) NOT NULL,
  `threadpin` varchar(255) NOT NULL,
  `editmocom` varchar(255) NOT NULL,
  `delcom` varchar(255) NOT NULL,
  PRIMARY KEY (`boardID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_comments`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_comments` (
  `comID` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `tID` mediumint(6) unsigned NOT NULL,
  `boardID` smallint(4) unsigned NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  `postname` varchar(25) NOT NULL,
  `adatum` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `edits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `editdatum` int(10) unsigned NOT NULL DEFAULT '0',
  `edituserID` smallint(6) NOT NULL DEFAULT '0',
  `IP` varchar(15) NOT NULL,
  `attachs` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comID`),
  KEY `tID` (`tID`,`boardID`),
  KEY `userID` (`userID`),
  FULLTEXT KEY `comment` (`comment`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_ratings`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_ratings` (
  `rateID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `userID` smallint(5) unsigned NOT NULL,
  `tID` mediumint(6) unsigned NOT NULL,
  `bID` smallint(4) unsigned NOT NULL,
  `wert` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`rateID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_search`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_search` (
  `searchID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `IP` varchar(15) NOT NULL DEFAULT '',
  `SID` varchar(64) NOT NULL,
  `datum` int(10) NOT NULL DEFAULT '0',
  `stichwort` varchar(100) NOT NULL DEFAULT '',
  `suchart` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fromusername` varchar(40) NOT NULL DEFAULT '',
  `usersuchart` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `foren` varchar(255) NOT NULL DEFAULT '',
  `alterart` enum('>=','<=','=>') NOT NULL DEFAULT '>=',
  `altervalue` smallint(3) unsigned NOT NULL DEFAULT '0',
  `sortart` enum('adatum','threadname','c.posts','views','datum','name','rating') NOT NULL DEFAULT 'adatum',
  `sortorder` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `sqlquery` text NOT NULL,
  `viewas` enum('comments','topic') NOT NULL,
  PRIMARY KEY (`searchID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_survey`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_survey` (
  `fsurveyID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `boardID` smallint(4) unsigned NOT NULL,
  `threadID` mediumint(6) unsigned NOT NULL,
  `comID` mediumint(7) unsigned NOT NULL,
  `ende` int(10) unsigned NOT NULL,
  `frage` varchar(200) NOT NULL,
  `antworten` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`fsurveyID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_survey_answers`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_survey_answers` (
  `answerID` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `fsID` smallint(6) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `votes` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`answerID`),
  KEY `fsID` (`fsID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_survey_votes`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_survey_votes` (
  `voteID` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `fsurID` smallint(5) unsigned NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  `IP` varchar(15) NOT NULL,
  `votedatum` int(10) unsigned NOT NULL,
  PRIMARY KEY (`voteID`),
  KEY `fsurID` (`fsurID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_forum_threads`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_forum_threads` (
  `threadID` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `bID` smallint(4) unsigned NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `threadname` varchar(100) NOT NULL,
  `preview` text NOT NULL,
  `vonID` smallint(5) unsigned NOT NULL,
  `vonname` varchar(25) NOT NULL,
  `views` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `posts` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `lastuserID` smallint(5) unsigned NOT NULL,
  `lastusername` varchar(25) NOT NULL,
  `lastreplay` int(10) unsigned NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `closedmsg` varchar(500) NOT NULL DEFAULT '',
  `fsurveyID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `anhaenge` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rating` int(10) unsigned NOT NULL DEFAULT '0',
  `ratingvotes` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`threadID`),
  KEY `bID` (`bID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_gallery`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_gallery` (
  `galleryID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  `folder` varchar(255) NOT NULL,
  `images` smallint(4) unsigned NOT NULL DEFAULT '0',
  `cID` smallint(4) unsigned NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `access` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`galleryID`),
  KEY `cID` (`cID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_gallery_images`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_gallery_images` (
  `imageID` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `gID` smallint(5) unsigned NOT NULL,
  `filename` varchar(64) NOT NULL,
  `beschreibung` varchar(300) NOT NULL DEFAULT '',
  `klicks` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `uploaded` int(10) unsigned NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`imageID`),
  KEY `gID` (`gID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_gallery_kate`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_gallery_kate` (
  `kateID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `katename` varchar(40) NOT NULL,
  `beschreibung` text NOT NULL,
  `access` varchar(255) NOT NULL DEFAULT '',
  `galleries` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`kateID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_games_user`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_games_user` (
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_groups`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_groups` (
  `groupID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `admin` text NOT NULL,
  `public` text NOT NULL,
  PRIMARY KEY (`groupID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_joinus`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_joinus` (
  `joinID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `username` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  `icq` varchar(15) NOT NULL,
  `msn` varchar(255) NOT NULL,
  `age` tinyint(2) unsigned NOT NULL,
  `country` varchar(3) NOT NULL,
  `teamID` smallint(4) unsigned NOT NULL,
  `comment` text NOT NULL,
  `IP` varchar(15) NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `closedby` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`joinID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_links`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_links` (
  `linkID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `url` varchar(255) NOT NULL,
  `bannerurl` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `hits` mediumint(8) unsigned NOT NULL,
  `eingetragen` int(10) unsigned NOT NULL,
  PRIMARY KEY (`linkID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_lotto`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_lotto` (
  `lottoon` tinyint(1) unsigned NOT NULL,
  `jackpot` float unsigned NOT NULL,
  `preis` smallint(5) unsigned NOT NULL,
  `pro4er` tinyint(2) unsigned NOT NULL,
  `pro3er` tinyint(2) unsigned NOT NULL,
  `pro2er` tinyint(2) unsigned NOT NULL,
  `jackpotraise` float NOT NULL,
  `free_scheine` tinyint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_lotto_gewinner`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_lotto_gewinner` (
  `gewinnID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `userID` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sID` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `gewinn` float NOT NULL DEFAULT '0',
  `art` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gewinnID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_lotto_runden`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_lotto_runden` (
  `rundenID` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `anfang` int(10) NOT NULL DEFAULT '0',
  `ende` int(10) NOT NULL DEFAULT '0',
  `rundenjackpot` float NOT NULL DEFAULT '0',
  `auszahlung` float NOT NULL DEFAULT '0',
  `zahl1` tinyint(2) NOT NULL DEFAULT '0',
  `zahl2` tinyint(2) NOT NULL DEFAULT '0',
  `zahl3` tinyint(2) NOT NULL DEFAULT '0',
  `zahl4` tinyint(2) NOT NULL DEFAULT '0',
  `4er` smallint(5) unsigned NOT NULL DEFAULT '0',
  `3er` smallint(5) unsigned NOT NULL DEFAULT '0',
  `2er` smallint(5) unsigned NOT NULL DEFAULT '0',
  `geld4er` float NOT NULL DEFAULT '0',
  `geld3er` float NOT NULL DEFAULT '0',
  `geld2er` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`rundenID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_lotto_scheine`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_lotto_scheine` (
  `scheinID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `rundenID` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `datum` int(10) NOT NULL DEFAULT '0',
  `zahl1` tinyint(2) NOT NULL DEFAULT '0',
  `zahl2` tinyint(2) NOT NULL DEFAULT '0',
  `zahl3` tinyint(2) NOT NULL DEFAULT '0',
  `zahl4` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`scheinID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_lotto_zeiten`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_lotto_zeiten` (
  `wochentag` smallint(1) unsigned NOT NULL DEFAULT '0',
  `uhrzeit` varchar(5) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_members`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_members` (
  `mID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userID` smallint(5) unsigned NOT NULL,
  `teamID` smallint(4) unsigned NOT NULL,
  `name` varchar(40) NOT NULL,
  `aufgabe` varchar(255) NOT NULL DEFAULT '',
  `aktiv` tinyint(1) unsigned NOT NULL,
  `posi` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_menu`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_menu` (
  `menuID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `headline` varchar(255) NOT NULL,
  `modul` varchar(40) NOT NULL,
  `inhalt` text NOT NULL,
  `hposi` enum('l','r') NOT NULL,
  `vposi` tinyint(3) unsigned NOT NULL,
  `usetpl` tinyint(1) unsigned NOT NULL,
  `design` varchar(40) NOT NULL,
  `access` varchar(400) NOT NULL,
  `lang` varchar(50) NOT NULL,
  PRIMARY KEY (`menuID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_menu_links`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_menu_links` (
  `linkID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `suche` varchar(30) NOT NULL,
  `ersetze` varchar(255) NOT NULL,
  `sprache` varchar(3) NOT NULL,
  PRIMARY KEY (`linkID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_messages`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_messages` (
  `msgID` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `touser` smallint(5) unsigned NOT NULL,
  `fromuser` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(40) NOT NULL,
  `msg` text NOT NULL,
  `del` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fromdel` tinyint(1) unsigned NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `readed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`msgID`),
  KEY `touser` (`touser`,`fromuser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_news`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_news` (
  `newsID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userID` smallint(5) unsigned NOT NULL,
  `topicID` smallint(3) unsigned NOT NULL,
  `access` varchar(255) NOT NULL,
  `lang` tinytext NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `headline` varchar(50) NOT NULL,
  `bodytext` text NOT NULL,
  `extendtext` text NOT NULL,
  `links` text NOT NULL,
  `hits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`newsID`),
  KEY `topicID` (`topicID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_online`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_online` (
  `uID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `betretten` int(10) unsigned NOT NULL,
  `lastklick` int(10) unsigned NOT NULL,
  `IP` varchar(15) NOT NULL,
  `SID` varchar(255) NOT NULL,
  `SIDDATA` text,
  `forum` tinyint(3) unsigned NOT NULL,
  `fboardID` smallint(5) unsigned NOT NULL,
  `fthreadID` mediumint(6) unsigned NOT NULL,
  KEY `uID` (`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_ranks`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_ranks` (
  `rankID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `rankname` varchar(30) NOT NULL,
  `iconname` varchar(255) NOT NULL,
  `abposts` mediumint(6) unsigned NOT NULL,
  `fest` tinyint(1) unsigned NOT NULL,
  `money` float unsigned NOT NULL,
  PRIMARY KEY (`rankID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_server`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_server` (
  `serverID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `gamename` varchar(40) NOT NULL,
  `gametype` varchar(20) NOT NULL,
  `passwort` varchar(40) NOT NULL,
  `aktiv` tinyint(1) unsigned NOT NULL,
  `posi` tinyint(3) unsigned NOT NULL,
  `stat` tinyint(1) NOT NULL,
  `displaymenu` tinyint(1) unsigned NOT NULL,
  `ip` varchar(255) NOT NULL,
  `port` mediumint(5) unsigned NOT NULL,
  `queryport` mediumint(5) unsigned NOT NULL,
  `sport` mediumint(5) unsigned NOT NULL,
  `response` text NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  PRIMARY KEY (`serverID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_server_stats`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_server_stats` (
  `sID` smallint(5) unsigned NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `players` tinyint(3) unsigned NOT NULL,
  KEY `sID` (`sID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_settings`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_settings` (
  `ADMIN_ENTRIES` tinyint(2) unsigned NOT NULL,
  `AVATAR_MAX_SIZE` mediumint(6) unsigned NOT NULL,
  `AVATAR_MAX_X` smallint(3) unsigned NOT NULL,
  `AVATAR_MAX_Y` smallint(3) unsigned NOT NULL,
  `BACKUP_AKTIV` tinyint(1) unsigned NOT NULL,
  `BACKUP_CYCLE` varchar(10) NOT NULL,
  `BACKUP_EMAIL` varchar(255) NOT NULL,
  `CLAN_NAME` varchar(50) NOT NULL,
  `CLAN_NAME_SHORT` varchar(10) NOT NULL,
  `CW_SCREEN_SIZE` smallint(4) NOT NULL,
  `COMMENTS_ORDER` enum('DESC','ASC') NOT NULL,
  `DELETE_UNAKTIV` smallint(4) unsigned NOT NULL DEFAULT '7',
  `DESIGN` varchar(255) NOT NULL,
  `GALLERY_PIC_SIZE` smallint(4) unsigned NOT NULL,
  `GALLERY_THUMB_SIZE` smallint(3) unsigned NOT NULL,
  `GOOGLE_API_KEY` varchar(100) NOT NULL DEFAULT '',
  `HOT_THREAD_POST` smallint(4) unsigned NOT NULL,
  `HOT_THREAD_VIEWS` smallint(5) unsigned NOT NULL,
  `LANGUAGE` varchar(3) NOT NULL,
  `LIMIT_CLANKASSE_TRANS` smallint(2) unsigned NOT NULL,
  `LIMIT_CLANWARS` tinyint(3) unsigned NOT NULL,
  `LIMIT_COMMENTS` tinyint(2) unsigned NOT NULL,
  `LIMIT_FORUM_COMMENTS` tinyint(3) unsigned NOT NULL,
  `LIMIT_GALLERY` tinyint(3) unsigned NOT NULL,
  `LIMIT_GALLERY_PICS` tinyint(3) unsigned NOT NULL,
  `LIMIT_GUESTBOOK` tinyint(3) unsigned NOT NULL,
  `LIMIT_LAST_WARS` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `LIMIT_LAST_THREADS` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `LIMIT_MINI_NEWS` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `LIMIT_LINKS` tinyint(3) unsigned NOT NULL,
  `LIMIT_MEMBERS` tinyint(2) unsigned NOT NULL,
  `LIMIT_MESSAGES` tinyint(3) unsigned NOT NULL,
  `LIMIT_NEWS` tinyint(2) unsigned NOT NULL,
  `LIMIT_NEXT_WARS` tinyint(3) unsigned NOT NULL,
  `LIMIT_SHOUTBOX` tinyint(3) unsigned NOT NULL,
  `LIMIT_SHOUTBOX_MINI` tinyint(3) unsigned NOT NULL,
  `LIMIT_THREADS` tinyint(3) unsigned NOT NULL,
  `LONG_DATE` varchar(20) NOT NULL,
  `MAX_IMG_WIDTH` smallint(5) unsigned NOT NULL DEFAULT '350',
  `MONEY_PER_COMMENT` float unsigned NOT NULL,
  `SHORT_DATE` varchar(20) NOT NULL,
  `ONLINE_RELOAD` mediumint(6) unsigned NOT NULL,
  `POSTS_PER_COMMENTS` tinyint(3) unsigned NOT NULL,
  `PW_MIN_LENGTH` tinyint(2) unsigned NOT NULL,
  `SEND_ACCOUNT_CODE` tinyint(1) unsigned NOT NULL,
  `SERVER_CACHE_REFRESH` smallint(5) unsigned NOT NULL,
  `SERVER_MAX_LOG` smallint(4) unsigned NOT NULL DEFAULT '1000',
  `SERVER_LOG_INTERVALL` tinyint(3) unsigned NOT NULL DEFAULT '15',
  `SHOW_USER_ONLINE` smallint(5) unsigned NOT NULL,
  `SHOUTBOX_MAX_CHARS` smallint(4) unsigned NOT NULL,
  `SMTP_AKTIV` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SMTP_HOST` varchar(200) NOT NULL DEFAULT '',
  `SMTP_PORT` mediumint(5) unsigned NOT NULL DEFAULT '25',
  `SMTP_USER` varchar(50) NOT NULL DEFAULT '',
  `SMTP_PASS` varchar(50) NOT NULL DEFAULT '',
  `SPAM_AWARDS_COMMENTS` smallint(5) unsigned NOT NULL,
  `SPAM_CLANWARS_COMMENTS` smallint(5) unsigned NOT NULL,
  `SPAM_FORUM_COMMENTS` smallint(5) unsigned NOT NULL DEFAULT '30',
  `SPAM_FORUM_THREAD` smallint(5) unsigned NOT NULL DEFAULT '60',
  `SPAM_GALLERY_COMMENTS` smallint(5) unsigned NOT NULL,
  `SPAM_GUESTBOOK` mediumint(6) unsigned NOT NULL,
  `SPAM_GUESTBOOK_COMMENTS` mediumint(6) unsigned NOT NULL,
  `SPAM_NEWS_COMMENTS` smallint(5) unsigned NOT NULL,
  `SPAM_MESSAGE` smallint(5) unsigned NOT NULL,
  `SPAM_DOWNLOADS_COMMENTS` smallint(5) unsigned NOT NULL,
  `SPAM_SHOUTBOX` smallint(5) unsigned NOT NULL,
  `SPAM_SURVEY_COMMENTS` smallint(6) unsigned NOT NULL,
  `SPAM_USER_GB_COMMENTS` mediumint(6) unsigned NOT NULL,
  `STARTSEITE` varchar(50) NOT NULL,
  `SITE_EMAIL` varchar(255) NOT NULL,
  `SITE_EMAIL_NAME` varchar(40) NOT NULL,
  `SITE_TITLE` varchar(255) NOT NULL,
  `SITE_URL` varchar(255) NOT NULL,
  `LIMIT_SURVEY` tinyint(10) unsigned NOT NULL,
  `USER_KLICK_RELOAD` smallint(5) unsigned NOT NULL,
  `USER_PIC_MAX` mediumint(6) unsigned NOT NULL,
  `USER_PIC_X` smallint(3) unsigned NOT NULL,
  `USER_PIC_Y` smallint(3) unsigned NOT NULL,
  `UPLOAD_METHOD` varchar(10) NOT NULL DEFAULT '',
  `VERSION` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_smilies`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_smilies` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `bedeutung` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_stats`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_stats` (
  `installed` int(10) unsigned NOT NULL,
  `dltraffic` bigint(20) unsigned NOT NULL,
  `lastdbbackup` int(10) unsigned NOT NULL,
  `sqlfree` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_stats_browser`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_stats_browser` (
  `type` enum('os','browser') NOT NULL,
  `variable` tinytext NOT NULL,
  `hits` bigint(20) unsigned NOT NULL DEFAULT '0',
  `visits` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_stats_jahr`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_stats_jahr` (
  `jahr` smallint(4) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `visits` int(10) unsigned NOT NULL DEFAULT '0',
  `userhits` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_stats_monat`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_stats_monat` (
  `jahr` smallint(4) unsigned NOT NULL DEFAULT '0',
  `monat` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `visits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `userhits` mediumint(8) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_stats_stunde`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_stats_stunde` (
  `jahr` smallint(4) unsigned NOT NULL DEFAULT '0',
  `monat` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tag` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `stunde` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `hits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `visits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userhits` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_stats_tag`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_stats_tag` (
  `jahr` smallint(4) unsigned NOT NULL DEFAULT '0',
  `monat` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tag` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `hits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `visits` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `userhits` mediumint(6) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_survey`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_survey` (
  `surveyID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `start` int(10) unsigned NOT NULL,
  `ende` int(10) unsigned NOT NULL,
  `frage` varchar(200) NOT NULL,
  `antworten` tinyint(3) unsigned NOT NULL,
  `sperre` mediumint(7) unsigned NOT NULL,
  `access` varchar(255) NOT NULL,
  PRIMARY KEY (`surveyID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_survey_answers`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_survey_answers` (
  `answerID` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `sID` smallint(6) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `votes` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`answerID`),
  KEY `sID` (`sID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_survey_votes`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_survey_votes` (
  `voteID` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `surID` smallint(5) unsigned NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  `IP` varchar(15) NOT NULL,
  `votedatum` int(10) unsigned NOT NULL,
  PRIMARY KEY (`voteID`),
  KEY `surID` (`surID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_teams`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_teams` (
  `tID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `tname` varchar(40) NOT NULL,
  `tpic` varchar(255) NOT NULL,
  `grID` smallint(4) NOT NULL,
  `info` text NOT NULL,
  `cw` tinyint(1) NOT NULL,
  `fightus` tinyint(1) NOT NULL,
  `joinus` tinyint(1) unsigned NOT NULL,
  `posi` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_teamspeak`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_teamspeak` (
  `tsID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `port` mediumint(5) unsigned NOT NULL,
  `qport` mediumint(5) unsigned NOT NULL,
  `response` text NOT NULL,
  PRIMARY KEY (`tsID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_texte`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_texte` (
  `name` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `content2` text NOT NULL,
  `lang` varchar(2) NOT NULL,
  `options` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_topics`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_topics` (
  `tID` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `topicname` varchar(30) NOT NULL,
  `beschreibung` varchar(255) NOT NULL,
  `topicbild` varchar(255) NOT NULL,
  PRIMARY KEY (`tID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_user`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_user` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(40) NOT NULL,
  `lastlogin` int(10) unsigned NOT NULL DEFAULT '0',
  `laststart` int(10) unsigned NOT NULL DEFAULT '0',
  `lastforum` int(10) unsigned NOT NULL DEFAULT '0',
  `country` varchar(3) NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `signatur` text,
  `avatar` varchar(64) NOT NULL DEFAULT '',
  `user_pic` varchar(64) NOT NULL DEFAULT '',
  `registerdate` int(10) unsigned NOT NULL,
  `rID` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `update_rights` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `realname` varchar(40) NOT NULL DEFAULT '',
  `wohnort` varchar(50) NOT NULL DEFAULT '',
  `koord` varchar(100) NOT NULL DEFAULT '',
  `geburtstag` date DEFAULT NULL,
  `homepage` varchar(255) NOT NULL DEFAULT '',
  `icq` varchar(13) NOT NULL DEFAULT '',
  `msn` varchar(255) NOT NULL DEFAULT '',
  `yahoo` varchar(255) NOT NULL DEFAULT '',
  `aim` varchar(255) NOT NULL DEFAULT '',
  `skype` varchar(255) NOT NULL DEFAULT '',
  `xfire` varchar(255) NOT NULL DEFAULT '',
  `clanname` varchar(40) NOT NULL DEFAULT '',
  `clanirc` varchar(40) NOT NULL DEFAULT '',
  `clanhomepage` varchar(255) NOT NULL DEFAULT '',
  `clanhistory` varchar(255) NOT NULL DEFAULT '',
  `cpu` varchar(60) NOT NULL DEFAULT '',
  `mainboard` varchar(60) NOT NULL DEFAULT '',
  `ram` varchar(60) NOT NULL DEFAULT '',
  `gkarte` varchar(60) NOT NULL DEFAULT '',
  `skarte` varchar(60) NOT NULL DEFAULT '',
  `monitor` varchar(60) NOT NULL DEFAULT '',
  `maus` varchar(60) NOT NULL DEFAULT '',
  `tastatur` varchar(60) NOT NULL DEFAULT '',
  `mauspad` varchar(60) NOT NULL DEFAULT '',
  `internet` varchar(60) NOT NULL DEFAULT '',
  `festplatte` varchar(60) NOT NULL DEFAULT '',
  `headset` varchar(60) NOT NULL DEFAULT '',
  `aboutme` text,
  `ondelete` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_user_bans`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_user_bans` (
  `userID` smallint(5) unsigned NOT NULL,
  `vonID` smallint(5) unsigned NOT NULL,
  `bantime` int(10) unsigned NOT NULL,
  `endbantime` int(10) unsigned NOT NULL,
  `grund` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_user_codes`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_user_codes` (
  `userID` smallint(5) unsigned NOT NULL,
  `code` varchar(10) NOT NULL,
  `art` varchar(25) NOT NULL,
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_user_config`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_user_config` (
  `userID` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_user_groups`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_user_groups` (
  `userID` smallint(5) unsigned NOT NULL,
  `gID` smallint(5) unsigned NOT NULL,
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_user_lastvisits`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_user_lastvisits` (
  `userID` smallint(6) NOT NULL,
  `visitID` smallint(6) NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  KEY `userID` (`userID`,`visitID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_user_stats`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_user_stats` (
  `userID` smallint(5) unsigned NOT NULL,
  `clicks` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `logins` smallint(5) unsigned NOT NULL DEFAULT '0',
  `comments` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `money` float NOT NULL DEFAULT '25',
  `msg_s` smallint(5) unsigned NOT NULL DEFAULT '0',
  `msg_r` smallint(5) unsigned NOT NULL DEFAULT '0',
  `profilhits` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `scheine` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `2er` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `3er` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `4er` mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars` (
  `warID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `tID` smallint(4) unsigned NOT NULL,
  `mID` smallint(4) unsigned NOT NULL,
  `gID` smallint(4) unsigned NOT NULL,
  `datum` int(10) unsigned NOT NULL,
  `xonx` varchar(10) NOT NULL,
  `report` text NOT NULL,
  `ownplayers` varchar(255) NOT NULL DEFAULT '',
  `oppplayers` varchar(255) NOT NULL DEFAULT '',
  `oID` smallint(5) unsigned NOT NULL,
  `matchlink` varchar(255) NOT NULL,
  `resultbylocations` tinyint(1) unsigned NOT NULL,
  `result` varchar(4) NOT NULL DEFAULT '',
  `resultscore` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL,
  `server` varchar(255) NOT NULL DEFAULT '',
  `livestream` varchar(255) NOT NULL DEFAULT '',
  `pw` varchar(40) NOT NULL DEFAULT '',
  `hinweise` text NOT NULL,
  `meldefrist` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`warID`),
  KEY `tID` (`tID`,`mID`,`gID`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars_games`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars_games` (
  `gameID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `gamename` varchar(50) NOT NULL,
  `gameshort` varchar(20) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `icon_big` varchar(255) NOT NULL,
  `fightus` tinyint(1) NOT NULL,
  PRIMARY KEY (`gameID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars_locations`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars_locations` (
  `locationID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `locationname` varchar(50) NOT NULL,
  `gID` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`locationID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars_matchtype`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars_matchtype` (
  `matchtypeID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `matchtypename` varchar(50) NOT NULL,
  `fightus` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`matchtypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars_opp`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars_opp` (
  `oppID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `oppname` varchar(40) NOT NULL,
  `oppshort` varchar(15) NOT NULL,
  `homepage` varchar(255) NOT NULL,
  `country` varchar(3) NOT NULL,
  PRIMARY KEY (`oppID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars_scores`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars_scores` (
  `scoreID` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `wID` smallint(5) unsigned NOT NULL,
  `lID` smallint(5) unsigned NOT NULL,
  `ownscore` smallint(5) unsigned NOT NULL,
  `oppscore` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`scoreID`),
  KEY `wID` (`wID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars_screens`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars_screens` (
  `screenID` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `sID` mediumint(6) unsigned NOT NULL,
  `wID` smallint(5) unsigned NOT NULL,
  `filename` varchar(40) NOT NULL,
  `uploaddate` int(11) unsigned NOT NULL,
  PRIMARY KEY (`screenID`),
  KEY `wID` (`wID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v3_ecp_wars_teilnehmer`
--

CREATE TABLE IF NOT EXISTS `v3_ecp_wars_teilnehmer` (
  `warID` smallint(5) unsigned NOT NULL,
  `userID` smallint(5) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `meldedatum` int(10) unsigned NOT NULL,
  KEY `warID` (`warID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";
$data2 = "INSERT INTO `v3_ecp_clankasse` (`empfaenger`, `kontonummer`, `bankleitzahl`, `IBAN`, `SWIFT`, `institut`, `kontostand`) VALUES ('', '0', '0', '', '', '', 0);
INSERT INTO `v3_ecp_groups` (`groupID`, `name`, `admin`, `public`) VALUES (1, 'superadmin', '', '');
INSERT INTO `v3_ecp_groups` (`groupID`, `name`, `admin`, `public`) VALUES (2, 'clanmember', '', 'awards:view=1,com_view=1,com_add=1,com_edit=1,com_del=1]clankasse:view=1]clanwars:view=1,details=1,view_next=1,com_view=1,com_add=1,com_edit=1,com_del=1]contact:view=1]downloads:view=1,download=1,com_view=1,com_add=1,com_edit=1,com_del=1]fightus:submit=1]forum:view=1]gallery:view=1,com_view=1,com_add=1,com_edit=1,com_del=1]guestbook:view=1,add=1,com_view=1,com_add=1,com_edit=1,com_del=1]joinus:view=1]links:view=1]news:view=1,com_view=1,com_add=1,com_edit=1,com_del=1]server:view=1]shoutbox:view=1,add=1]survey:view=1,com_view=1,com_add=1,com_edit=1,com_del=1]teams:view=1]teamspeak:view=1]lotto:view=1,ticket=1]contact:view=1]calendar:view=1]user:view=1,com_view=1,com_add=1,com_edit=1,list=1]stats:view=1]membermap:view=1');
INSERT INTO `v3_ecp_groups` (`groupID`, `name`, `admin`, `public`) VALUES (3, 'comMember', '', 'awards:view=1,com_view=1,com_add=1,com_edit=1]clanwars:view=1,details=1,view_next=1,com_view=1,com_add=1,com_edit=1]contact:view=1]downloads:view=1,download=1,com_view=1,com_add=1,com_edit=1]fightus:submit=1]forum:view=1]gallery:view=1,com_view=1,com_add=1,com_edit=1]guestbook:view=1,add=1,com_view=1,com_add=1,com_edit=1]joinus:view=1]links:view=1]news:view=1,com_view=1,com_add=1,com_edit=1]server:view=1]shoutbox:view=1,add=1]survey:view=1,com_view=1,com_add=1,com_edit=1]teams:view=1]teamspeak:view=1]lotto:view=1,ticket=1]contact:view=1]calendar:view=1]user:view=1,com_view=1,com_add=1,com_edit=1,list=1]stats:view=1]membermap:view=1');
INSERT INTO `v3_ecp_groups` (`groupID`, `name`, `admin`, `public`) VALUES (4, 'visitor', '', 'awards:view=1,com_view=1,com_add=1]calendar:view=1]clanwars:view=1,details=1,view_next=1,com_view=1,com_add=1]contact:view=1]downloads:view=1,download=1,com_view=1,com_add=1]fightus:submit=1]forum:view=1]gallery:view=1,com_view=1,com_add=1]guestbook:view=1,add=1,com_view=1,com_add=1]joinus:view=1]links:view=1]news:view=1,com_view=1,com_add=1]server:view=1]shoutbox:view=1,add=1]survey:view=1,com_view=1,com_add=1]teams:view=1]teamspeak:view=1]user:view=1,com_view=1,com_add=1]lotto:view=1]contact:view=1]calendar:view=1]user:view=1,com_view=1,com_add=1,list=1]stats:view=1]membermap:view=1');
INSERT INTO `v3_ecp_lotto` (`lottoon`, `jackpot`, `preis`, `pro4er`, `pro3er`, `pro2er`, `jackpotraise`, `free_scheine`) VALUES (0, 500, 10, 60, 30, 10, 250, 5);
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (1, 'Main Menu', '<img src=\"templates/Standard/images/site/menu_main.jpg\" alt=\"\" width=\"150\" height=\"27\" />', '', '{news}<br />{downloads}<br />{links}<br />{calendar}<br />{stats}<br />{survey}<br />{contact}<br />{teamspeak}<br />{impressum}<br />{lotto}', 'l', 1, 1, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (2, 'Clan', '<img src=\"templates/Standard/images/site/menu_clan.jpg\" alt=\"\" width=\"150\" height=\"27\" />', '', '{awards}<br />{clanwars}<br />{fightus}<br />{joinus}<br />{teams}<br />{server}<br />{finances}', 'l', 2, 1, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (3, 'Community', '<img src=\"templates/Standard/images/site/menu_community.jpg\" alt=\"\" width=\"150\" height=\"27\" />', '', '{account}<br />{forum}<br />{gallery}<br />{guestbook}<br />{user}<br />{shoutbox}<br />{membermap}', 'l', 3, 1, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (4, 'Account', '<img src=\"templates/Standard/images/site/menu_account.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_account.php', '', 'r', 1, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (5, 'Main Menu', '<img src=\"templates/Version3/images/site/menu_main.jpg\" alt=\"\" />', '', '{news}<br />{downloads}<br />{links}<br />{calendar}<br />{stats}<br />{survey}<br />{contact}<br />{teamspeak}<br />{impressum}<br />{lotto}', 'l', 1, 1, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (6, 'Kalender', '<img src=\"templates/Standard/images/site/menu_kalender.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_calendar.php', '<p>Test</p>', 'r', 4, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (7, 'Online', '<img src=\"templates/Standard/images/site/menu_online.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_online.php', '', 'l', 4, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (8, 'Server', '<img src=\"templates/Standard/images/site/menu_server.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_server.php', '', 'r', 8, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (9, 'Shoutbox', '<img src=\"templates/Standard/images/site/menu_shoutbox.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_shoutbox.php', '', 'l', 6, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (10, 'Zufallsbild', '<img src=\"templates/Standard/images/site/menu_zufallsbild.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_randpic.php', '', 'r', 7, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (11, 'Nextwars', '<img src=\"templates/Standard/images/site/menu_nextwars.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_nextwars.php', '', 'r', 6, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (12, 'Umfrage', '<img src=\"templates/Standard/images/site/menu_umfrage.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_survey.php', '', 'l', 5, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (13, 'Lastwars', '<img src=\"templates/Standard/images/site/menu_lastwars.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_lastwars.php', '', 'r', 5, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (14, 'Last News', '<img src=\"templates/Standard/images/site/menu_lastnews.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_lastnews.php', '', 'r', 3, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (15, 'Last Threads', '<img src=\"templates/Standard/images/site/menu_lastthreads.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_lastthreads.php', '', 'r', 2, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (16, 'Clan', '<img src=\"templates/Version3/images/site/menu_clan.jpg\" alt=\"\" />', '', '{awards}<br />{clanwars}<br />{fightus}<br />{joinus}<br />{teams}<br />{server}<br />{finances}', 'l', 2, 1, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (17, 'Community', '<img src=\"templates/Version3/images/site/menu_community.jpg\" alt=\"\" />', '', '{account}<br />{forum}<br />{gallery}<br />{guestbook}<br />{user}<br />{shoutbox}<br />{membermap}', 'l', 3, 1, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (18, 'Account', '<img src=\"templates/Version3/images/site/menu_account.jpg\" alt=\"\" />', 'modul_account.php', '', 'r', 1, 0, 'Version3', ',2,3,1,', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (19, 'Kalender', '<img src=\"templates/Version3/images/site/menu_calendar.jpg\" alt=\"\" />', 'modul_calendar.php', '<p>Test</p>', 'r', 4, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (20, 'Online', '<img src=\"templates/Version3/images/site/menu_online.jpg\" alt=\"\" />', 'modul_online.php', '', 'l', 4, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (21, 'Server', '<img src=\"templates/Version3/images/site/menu_server.jpg\" alt=\"\" />', 'modul_server.php', '', 'r', 8, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (22, 'Shoutbox', '<img src=\"templates/Version3/images/site/menu_shoutbox.jpg\" alt=\"\" />', 'modul_shoutbox.php', '', 'l', 6, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (23, 'Zufallsbild', '<img src=\"templates/Version3/images/site/menu_zufallsbild.jpg\" alt=\"\" />', 'modul_randpic.php', '', 'r', 7, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (24, 'Nextwars', '<img src=\"templates/Version3/images/site/menu_nextwars.jpg\" alt=\"\" />', 'modul_nextwars.php', '', 'r', 6, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (25, 'Umfrage', '<img src=\"templates/Version3/images/site/menu_vote.jpg\" alt=\"\" />', 'modul_survey.php', '', 'l', 5, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (26, 'Lastwars', '<img src=\"templates/Version3/images/site/menu_lastwars.jpg\" alt=\"\" />', 'modul_lastwars.php', '', 'r', 5, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (27, 'Last News', '<img src=\"templates/Version3/images/site/menu_lastnews.jpg\" alt=\"\" />', 'modul_lastnews.php', '', 'r', 3, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (28, 'Last Threads', '<img src=\"templates/Version3/images/site/menu_lastthreads.jpg\" alt=\"\" />', 'modul_lastthreads.php', '', 'r', 2, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (29, 'Last Downloads', '<img src=\"templates/Standard/images/site/menu_lastdownloads.jpg\" alt=\"\" width=\"150\" height=\"27\" />', 'modul_lastdownloads.php', '', 'r', 9, 0, 'Standard', '', '');
INSERT INTO `v3_ecp_menu` (`menuID`, `name`, `headline`, `modul`, `inhalt`, `hposi`, `vposi`, `usetpl`, `design`, `access`, `lang`) VALUES (30, 'Last Downloads', '<img src=\"templates/Version3/images/site/menu_lastdownloads.jpg\" alt=\"\" />', 'modul_lastdownloads.php', '', 'r', 9, 0, 'Version3', '', '');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (1, '{news}', '<a href=\"?section=news\">News</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (2, '{downloads}', '<a href=\"?section=downloads\">Downloads</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (3, '{links}', '<a href=\"?section=links\">Links</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (4, '{calendar}', '<a href=\"?section=calendar\">Kalender</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (5, '{stats}', '<a href=\"?section=stats\">Statistiken</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (6, '{survey}', '<a href=\"?section=survey\">Umfragen</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (7, '{contact}', '<a href=\"?section=contact\">Kontakt</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (8, '{impressum}', '<a href=\"?section=cms&amp;id=1\">Impressum</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (9, '{awards}', '<a href=\"?section=awards\">Awards</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (10, '{clanwars}', '<a href=\"?section=clanwars\">Clanwars</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (11, '{fightus}', '<a href=\"?section=fightus\">FightUs</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (12, '{joinus}', '<a href=\"?section=joinus\">JoinUs</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (13, '{teams}', '<a href=\"?section=teams\">Teams</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (14, '{server}', '<a href=\"?section=server\">Server</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (15, '{finances}', '<a href=\"?section=clankasse\">Clankasse</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (16, '{account}', '<a href=\"?section=account\">Account</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (17, '{forum}', '<a href=\"?section=forum\">Forum</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (18, '{gallery}', '<a href=\"?section=gallery\">Galerie</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (19, '{guestbook}', '<a href=\"?section=guestbook\">Gästebuch</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (20, '{user}', '<a href=\"?section=user&amp;action=list\">Mitglieder</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (21, '{shoutbox}', '<a href=\"?section=shoutbox\">Shoutbox</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (22, '{admin_overview}', '<a href=\"?section=admin\">Übersicht</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (23, '{teamspeak}', '<a href=\"?section=teamspeak\">Teamspeak</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (24, '{user}', '<a href=\"?section=user&amp;action=list\">Mitglieder</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES (25, '{lotto}', '<a href=\"?section=lotto\">Clan Lotto</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(26, '{account}', '<a href=\"?section=account\">Account</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(27, '{admin_overview}', '<a href=\"?section=admin\">Admin overview</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(28, '{awards}', '<a href=\"?section=awards\">Awards</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(29, '{calendar}', '<a href=\"?section=calendar\">Calendar</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(30, '{clanwars}', '<a href=\"?section=clanwars\">Clanwars</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(31, '{contact}', '<a href=\"?section=contact\">Contact</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(32, '{downloads}', '<a href=\"?section=downloads\">Downloads</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(33, '{fightus}', '<a href=\"?section=fightus\">FightUs</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(34, '{finances}', '<a href=\"?section=clankasse\">Clan cash</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(35, '{forum}', '<a href=\"?section=forum\">Board</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(36, '{gallery}', '<a href=\"?section=gallery\">Gallery</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(37, '{guestbook}', '<a href=\"?section=guestbook\">Guestbook</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(38, '{impressum}', '<a href=\"?section=cms&id=1\">Imprint</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(39, '{joinus}', '<a href=\"?section=joinus\">JoinUs</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(40, '{links}', '<a href=\"?section=links\">Links</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(41, '{lotto}', '<a href=\"?section=lotto\">Clan lottery</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(42, '{news}', '<a href=\"?section=news\">News</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(43, '{server}', '<a href=\"?section=server\">Server</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(44, '{shoutbox}', '<a href=\"?section=shoutbox\">Shoutbox</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(45, '{stats}', '<a href=\"?section=stats\">Statistics</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(46, '{survey}', '<a href=\"?section=survey\">Vote</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(47, '{teamspeak}', '<a href=\"?section=teamspeak\">Teamspeak</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(48, '{teams}', '<a href=\"?section=teams\">Teams</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(49, '{user}', '<a href=\"?section=user&amp;action=list\">Users</a>', 'gb');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(50, '{membermap}', '<a href=\"?section=membermap\">Membermap</a>', 'de');
INSERT INTO `v3_ecp_menu_links` (`linkID`, `suche`, `ersetze`, `sprache`) VALUES(51, '{membermap}', '<a href=\"?section=membermap\">Membermap</a>', 'gb');
INSERT INTO `v3_ecp_settings` (`ADMIN_ENTRIES`, `AVATAR_MAX_SIZE`, `AVATAR_MAX_X`, `AVATAR_MAX_Y`, `BACKUP_AKTIV`, `BACKUP_CYCLE`, `BACKUP_EMAIL`, `CLAN_NAME`, `CLAN_NAME_SHORT`, `CW_SCREEN_SIZE`, `COMMENTS_ORDER`, `DELETE_UNAKTIV`, `DESIGN`, `GALLERY_PIC_SIZE`, `GALLERY_THUMB_SIZE`, `HOT_THREAD_POST`, `HOT_THREAD_VIEWS`, `LANGUAGE`, `LIMIT_CLANKASSE_TRANS`, `LIMIT_CLANWARS`, `LIMIT_COMMENTS`, `LIMIT_FORUM_COMMENTS`, `LIMIT_GALLERY`, `LIMIT_GALLERY_PICS`, `LIMIT_GUESTBOOK`, `LIMIT_LAST_WARS`, `LIMIT_LAST_THREADS`, `LIMIT_MINI_NEWS`, `LIMIT_LINKS`, `LIMIT_MEMBERS`, `LIMIT_MESSAGES`, `LIMIT_NEWS`, `LIMIT_NEXT_WARS`, `LIMIT_SHOUTBOX`, `LIMIT_SHOUTBOX_MINI`, `LIMIT_THREADS`, `LONG_DATE`, `MAX_IMG_WIDTH`, `MONEY_PER_COMMENT`, `SHORT_DATE`, `ONLINE_RELOAD`, `POSTS_PER_COMMENTS`, `PW_MIN_LENGTH`, `SEND_ACCOUNT_CODE`, `SERVER_CACHE_REFRESH`, `SHOW_USER_ONLINE`, `SHOUTBOX_MAX_CHARS`, `SPAM_AWARDS_COMMENTS`, `SPAM_CLANWARS_COMMENTS`, `SPAM_FORUM_COMMENTS`, `SPAM_FORUM_THREAD`, `SPAM_GALLERY_COMMENTS`, `SPAM_GUESTBOOK`, `SPAM_GUESTBOOK_COMMENTS`, `SPAM_NEWS_COMMENTS`, `SPAM_MESSAGE`, `SPAM_DOWNLOADS_COMMENTS`, `SPAM_SHOUTBOX`, `SPAM_SURVEY_COMMENTS`, `SPAM_USER_GB_COMMENTS`, `STARTSEITE`, `SITE_EMAIL`, `SITE_EMAIL_NAME`, `SITE_TITLE`, `SITE_URL`, `LIMIT_SURVEY`, `USER_KLICK_RELOAD`, `USER_PIC_MAX`, `USER_PIC_X`, `USER_PIC_Y`, `VERSION`) VALUES (15, 20480, 70, 70, 0, 'day', '', '".strsave($_POST['clanname'])."', '".strsave($_POST['clanshort'])."', 1024, 'ASC', 7, 'Standard', 1024, 150, 20, 100, 'de', 10, 15, 5, 5, 10, 15, 15, 3, 3, 3, 10, 20, 10, 5, 5, 5, 7, 10, 'd.m.Y H:i', 350, 1.5, 'd.m.y H:i', 1800, 1, 6, 1, 60, 600, 160, 60, 60, 30, 60, 60, 3600, 500, 60, 15, 60, 60, 60, 60, 'modul|news', '".strsave($_POST['admin_email'])."', '".strsave($_POST['clanname'])."', 'Willkommen bei ".strsave($_POST['clanname'])."', '".(strrpos($_POST['url'],'/') != (strlen($_POST['url'])-1) ? strsave($_POST['url'].'/') : strsave($_POST['url']))."', 10, 700, 51200, 200, 200, '".VERSION."');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(1, 'Smile', '1.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(2, 'Verrückt', '2.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(3, 'Hässlich', '3.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(4, '*schnief*', '4.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(5, 'Schüchtern', '5.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(6, '*Freu*', '6.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(7, 'Verwirrend', '7.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(8, 'HEUUUUL', '8.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(9, 'Bääähhhh', '9.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(10, 'lol', '10.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(11, '*schimpf!!!*', '11.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(12, 'Heiligenschein', '12.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(13, 'Küsschen', '13.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(14, 'HÄÄÄÄ????', '14.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(15, 'IHHH stinkt das.', '15.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(16, 'Hmmmm', '16.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(17, 'Fuck U!!!', '17.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(18, 'rolling over floor', '18.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(19, 'Böse', '19.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(20, '*Würg*', '20.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(21, 'Nicht Spammen!', '21.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(22, 'Zurück zum Thema !!', '22.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(23, 'Wenn man keine Ahnung hat', '23.gif');
INSERT INTO `v3_ecp_smilies` (`ID`, `bedeutung`, `filename`) VALUES(24, 'Brrrrr...', '24.gif');
INSERT INTO `v3_ecp_stats` (`installed`, `dltraffic`, `lastdbbackup`, `sqlfree`) VALUES (".time().", 0, 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Mozilla', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Windows Vista', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Unix', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Linux', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Mac OS', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Windows XP', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Windows NT', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Windows 98', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Netscape', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Firefox', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'IE 7', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'IE 6', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'IE 5', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Camino', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Galeon', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Konqueror', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Safari', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'OmniWeb', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Opera', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Bot', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('os', 'Unbekannt', 0, 0);
INSERT INTO `v3_ecp_stats_browser` (`type`, `variable`, `hits`, `visits`) VALUES ('browser', 'Unbekannt', 0, 0);
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('REGISTER_EMAIL', 'Hallo {username},\r\n\r\nHerzlich Willkommen bei {clanname}.\r\n\r\n{pageurl}\r\n\r\nDein Aktivierungscode: {aktivcode}\r\nDein Aktivierungslink: {aktivlink}', 'Aktivierungs Email', 'de', '0');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('THREAD_MOVE', 'Hallo {username},\r\n\r\nein Thema wurde von einem Administrator in ein anderes Forum verschoben.\r\n\r\nDu findest es jetzt unter folgendem Link:\r\n\r\n{link}', 'Thema verschoben', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('THREAD_ABO', 'Hallo {username},\r\n\r\nes wurde grade eine Antwort in einem Thema ({threadname}) von dir geschrieben.\r\n\r\nDu kannst die Antwort unter folgendem Link nachlesen:\r\n\r\n{link}', 'Antwort auf eine Forenthema', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('NEW_JOINUS', 'Hallo {username},\r\n\r\nsoeben wurde von {from_username} eine neue JoinUS Anfrage gestellt.\r\n\r\nMit folgendem Link kommst Du zur Anfragen: <a href=\"?section=admin&amp;site=joinus&amp;func=view&amp;id={id}\">Klick</a>', 'Neue JoinUS Anfrage', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('ACCOUNT_SEND_PW', 'Es wurde eben ein neues Passwort für deinen Account angefodert. Solltest Du diese Aktion nicht hervorgerufen haben irgnorier sie einfach.\r\n\r\nWenn Du dein Passwort ändern willst benutz folgenden Link:\r\n\r\n{link}', 'Neues Passwort', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('NEXT_WAR_MSG', 'Du wurdest soeben für einen Clanwar eingetragen. \r\n\r\nMehr Informationen findest du unter:\r\n{link}', 'Next War', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('NEW_FIGHTUS', 'Hallo {username},\r\n\r\nsoeben wurde vom Clan {from_clan} eine neue FightUs Anfrage gestellt.\r\n\r\nMit folgendem Link kommst Du zur Anfragen: <a href=\"?section=admin&amp;site=fightus&amp;func=view&amp;id={id}\">Klick</a>', 'Neue FightUs Anfrage.', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('LOTTO_WIN', 'Du hast mit deinem Lottoschein, den Du am {datum} abgeschickt hast, gewonnen.<br><br>\r\nDer Lottoschein besaß {richtige} Richtige.\r\n<br>\r\nFolgenede Zahlen wurden gezogen: {zahlen}<br>\r\n<br>\r\nDein Lottoschein hatte folgende Zahlen: {tippzahlen}<br>\r\nDamit bist Du um {gewinn} reicher.<br>\r\n<br>\r\nHerzlichen Glückwunsch.<br><br>\r\nEinen Überblick über die Gewinner kannst Du <a href=\"?section=lotto&amp;action=winlist&amp;runde={rundenid}\">hier</a> abrufen.', 'Clan-Lottogewinn', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('USER_ADD', 'Hallo {username},\r\n\r\ndu wurdest auf {pageurl} angemeldet.\r\n\r\nDu kannst dich mit folgendem Passwort einloggen.\r\n\r\nPasswort: {password}\r\n\r\n{clanname}', 'Neuer Account', 'de', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('REGISTER_EMAIL', 'Hello {username},\r\n\r\nWelcome to {clanname}.\r\n\r\n{pageurl}\r\n\r\nYour Activationcode: {aktivcode}\r\nYour Activationlink: {aktivlink}', 'Activation Email', 'gb', '0');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('THREAD_MOVE', 'Hello {username},\r\n\r\none of your topics has been moved to another board by an admin.\r\n\r\nUse the following link to watch the topic:\r\n\r\n{link}', 'Topic moved', 'gb', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('THREAD_ABO', 'Hello {username},\r\n\r\nsomeone replied to your post in topic ({threadname}) .\r\n\r\nUse the following link to watch the reply:\r\n\r\n{link}', 'Reply to a post', 'gb', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('NEW_JOINUS', 'Hello {username},\r\n\r\na new JoinUs request has just been sent to you by {from_username}.\r\n\r\nUse the following link to watch the request: <a\r\nhref=\"?section=admin&amp;site=joinus&amp;func=view&amp;id={id}\">Klick</a>', 'New JoinUS request', 'gb', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('ACCOUNT_SEND_PW', 'You just requested a new password for your account.\r\nIf you didn''t send this request, just ignore this message. \r\n\r\nTo change your password, please use the following link:\r\n\r\n{link}', 'New Password', 'gb', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('NEXT_WAR_MSG', 'You were just added for a new Clan War. \r\n\r\nFor more informations use the following link:\r\n{link}', 'New Clanwar', 'gb', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('NEW_FIGHTUS', 'Hello {username},\r\n\r\nyou just received a FightUS request from clan {from_clan}.\r\n\r\n\r\nUse the following link to watch the request: <a\r\nhref=\"?section=admin&amp;site=fightus&amp;func=view&amp;id={id}\">Klick</a>', 'New FightUs request', 'gb', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('LOTTO_WIN', 'You have won the lottery with your lottery ticket that you have posted on {datum}.<br><br>\r\nYou named {richtige} right numbers.\r\n\r\nThe following numbers were drawn: {zahlen}<br>\r\n<br>\r\nYour lottery ticket had the numbers: {tippzahlen}<br>\r\nYou increased your prizes with {gewinn}.<br>\r\n<br>\r\nCongratulations.<br><br>\r\nUse the following link to get an overview of your prizes <a\r\nhref=\"?section=lotto&amp;action=winlist&amp;runde={rundenid}\"></a>.', 'Clan-lottery win', 'gb', '');
INSERT INTO `v3_ecp_texte` (`name`, `content`, `content2`, `lang`, `options`) VALUES('USER_ADD', 'Hello {username},\r\n\r\nyou were successfully registered to {pageurl}.\r\n\r\nUse the following password to login.\r\n\r\nPassword: {password}\r\n\r\n{clanname}', 'New Account', 'gb', '');
INSERT INTO `v3_ecp_teamspeak` (`tsID`, `ip`, `port`, `qport`, `response`) VALUES (1, '', 0, 0, '');
INSERT INTO `v3_ecp_user_config` (`userID`) VALUES (1);
INSERT INTO `v3_ecp_user_groups` (`userID`, `gID`) VALUES(1, 1);
INSERT INTO `v3_ecp_user_groups` (`userID`, `gID`) VALUES(1, 3);
INSERT INTO `v3_ecp_user_stats` (`userID`) VALUES(1);
INSERT INTO `v3_ecp_wars_games` (`gameID`, `gamename`, `gameshort`, `icon`, `icon_big`, `fightus`) VALUES (1, 'Counter-Strike', 'CS', 'cs.gif', 'Counterstrike.png', 1);
INSERT INTO `v3_ecp_wars_games` (`gameID`, `gamename`, `gameshort`, `icon`, `icon_big`, `fightus`) VALUES (2, 'Counter-Strike Source', 'CSS', 'css.gif', 'Counterstrike Source.png', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (1, 'de_cbble', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (2, 'de_chateau', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (3, 'de_dust', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (4, 'de_dust2', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (5, 'de_esl_autumn', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (6, 'de_inferno', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (7, 'de_nuke', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (8, 'de_prodigy', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (9, 'de_train', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (10, 'de_tuscan', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (11, 'de_cbble', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (12, 'de_contra', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (13, 'de_dust2', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (14, 'de_inferno', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (15, 'de_nuke', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (16, 'de_strike_rc4', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (17, 'de_train', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (18, 'de_tuscan', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (19, 'de_aztec', 2);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (20, 'de_aztec', 1);
INSERT INTO `v3_ecp_wars_locations` (`locationID`, `locationname`, `gID`) VALUES (22, 'de_tides', 2);
INSERT INTO `v3_ecp_wars_matchtype` (`matchtypeID`, `matchtypename`, `fightus`) VALUES (1, 'ESL', 1);
INSERT INTO `v3_ecp_wars_matchtype` (`matchtypeID`, `matchtypename`, `fightus`) VALUES (2, 'ClanBase', 1);
INSERT INTO `v3_ecp_cms` (`cmsID`, `headline`, `content`, `access`, `views`) VALUES(1, '{\"de\":\"Impressum\",\"gb\":\"Imprint\"}', '{\"de\":\"Verantwortlicher f&uuml;r die Internetpr&auml;sents: <br \\/>Name eintragen<br \\/><br \\/>Haftungsausschluss <br \\/><br \\/>1. Inhalt des Onlineangebotes <br \\/>Der Autor &uuml;bernimmt keinerlei Gew&auml;hr f&uuml;r die Aktualit&auml;t, Korrektheit, Vollst&auml;ndigkeit oder Qualit&auml;t der bereitgestellten Informationen. Haftungsanspr&uuml;che gegen den Autor, welche sich auf Sch&auml;den materieller oder ideeller Art beziehen, die durch die Nutzung oder Nichtnutzung der dargebotenen Informationen bzw. durch die Nutzung fehlerhafter und unvollst&auml;ndiger Informationen verursacht wurden, sind grunds&auml;tzlich ausgeschlossen, sofern seitens des Autors kein nachweislich vors&auml;tzliches oder grob fahrl&auml;ssiges Verschulden vorliegt. <br \\/>Alle Angebote sind freibleibend und unverbindlich. Der Autor beh&auml;lt es sich ausdr&uuml;cklich vor, Teile der Seiten oder das gesamte Angebot ohne gesonderte Ank&uuml;ndigung zu ver&auml;ndern, zu erg&auml;nzen, zu l&ouml;schen oder die Ver&ouml;ffentlichung zeitweise oder endg&uuml;ltig einzustellen. <br \\/><br \\/>2. Verweise und Links <br \\/>Bei direkten oder indirekten Verweisen auf fremde Webseiten (Hyperlinks), die au&szlig;erhalb des Verantwortungsbereiches des Autors liegen, w&uuml;rde eine Haftungsverpflichtung ausschlie&szlig;lich in dem Fall in Kraft treten, in dem der Autor von den Inhalten Kenntnis hat und es ihm technisch m&ouml;glich und zumutbar w&auml;re, die Nutzung im Falle rechtswidriger Inhalte zu verhindern. <br \\/>Der Autor erkl&auml;rt hiermit ausdr&uuml;cklich, dass zum Zeitpunkt der Linksetzung keine illegalen Inhalte auf den zu verlinkenden Seiten erkennbar waren. Auf die aktuelle und zuk&uuml;nftige Gestaltung, die Inhalte oder die Urheberschaft der verlinkten\\/verkn&uuml;pften Seiten hat der Autor keinerlei Einfluss. Deshalb distanziert er sich hiermit ausdr&uuml;cklich von allen Inhalten aller verlinkten \\/verkn&uuml;pften Seiten, die nach der Linksetzung ver&auml;ndert wurden. Diese Feststellung gilt f&uuml;r alle innerhalb des eigenen Internetangebotes gesetzten Links und Verweise sowie f&uuml;r Fremdeintr&auml;ge in vom Autor eingerichteten G&auml;steb&uuml;chern, Diskussionsforen, Linkverzeichnissen, Mailinglisten und in allen anderen Formen von Datenbanken, auf deren Inhalt externe Schreibzugriffe m&ouml;glich sind. F&uuml;r illegale, fehlerhafte oder unvollst&auml;ndige Inhalte und insbesondere f&uuml;r Sch&auml;den, die aus der Nutzung oder Nichtnutzung solcherart dargebotener Informationen entstehen, haftet allein der Anbieter der Seite, auf welche verwiesen wurde, nicht derjenige, der &uuml;ber Links auf die jeweilige Ver&ouml;ffentlichung lediglich verweist. <br \\/><br \\/>3. Urheber- und Kennzeichenrecht <br \\/>Der Autor ist bestrebt, in allen Publikationen die Urheberrechte der verwendeten Bilder, Grafiken, Tondokumente, Videosequenzen und Texte zu beachten, von ihm selbst erstellte Bilder, Grafiken, Tondokumente, Videosequenzen und Texte zu nutzen oder auf lizenzfreie Grafiken, Tondokumente, Videosequenzen und Texte zur&uuml;ckzugreifen. <br \\/>Alle innerhalb des Internetangebotes genannten und ggf. durch Dritte gesch&uuml;tzten Marken- und Warenzeichen unterliegen uneingeschr&auml;nkt den Bestimmungen des jeweils g&uuml;ltigen Kennzeichenrechts und den Besitzrechten der jeweiligen eingetragenen Eigent&uuml;mer. Allein aufgrund der blo&szlig;en Nennung ist nicht der Schluss zu ziehen, dass Markenzeichen nicht durch Rechte Dritter gesch&uuml;tzt sind! <br \\/>Das Copyright f&uuml;r ver&ouml;ffentlichte, vom Autor selbst erstellte Objekte bleibt allein beim Autor der Seiten. Eine Vervielf&auml;ltigung oder Verwendung solcher Grafiken, Tondokumente, Videosequenzen und Texte in anderen elektronischen oder gedruckten Publikationen ist ohne ausdr&uuml;ckliche Zustimmung des Autors nicht gestattet. <br \\/><br \\/>4. Datenschutz <br \\/>Sofern innerhalb des Internetangebotes die M&ouml;glichkeit zur Eingabe pers&ouml;nlicher oder gesch&auml;ftlicher Daten (Emailadressen, Namen, Anschriften) besteht, so erfolgt die Preisgabe dieser Daten seitens des Nutzers auf ausdr&uuml;cklich freiwilliger Basis. Die Inanspruchnahme und Bezahlung aller angebotenen Dienste ist - soweit technisch m&ouml;glich und zumutbar - auch ohne Angabe solcher Daten bzw. unter Angabe anonymisierter Daten oder eines Pseudonyms gestattet. Die Nutzung der im Rahmen des Impressums oder vergleichbarer Angaben ver&ouml;ffentlichten Kontaktdaten wie Postanschriften, Telefon- und Faxnummern sowie Emailadressen durch Dritte zur &Uuml;bersendung von nicht ausdr&uuml;cklich angeforderten Informationen ist nicht gestattet. Rechtliche Schritte gegen die Versender von sogenannten Spam-Mails bei Verst&ouml;ssen gegen dieses Verbot sind ausdr&uuml;cklich vorbehalten. <br \\/><br \\/>5. Rechtswirksamkeit dieses Haftungsausschlusses <br \\/>Dieser Haftungsausschluss ist als Teil des Internetangebotes zu betrachten, von dem aus auf diese Seite verwiesen wurde. Sofern Teile oder einzelne Formulierungen dieses Textes der geltenden Rechtslage nicht, nicht mehr oder nicht vollst&auml;ndig entsprechen sollten, bleiben die &uuml;brigen Teile des Dokumentes in ihrem Inhalt und ihrer G&uuml;ltigkeit davon unber&uuml;hrt.<br \\/>\",\"gb\":\"Disclaimer&nbsp;<br \\/><br \\/>1. Content&nbsp;<br \\/>The author reserves the right not to be responsible for the topicality, correctness, completeness or quality of the information provided. Liability claims regarding damage caused by the use of any information provided, including any kind of information which is incomplete or incorrect,will therefore be rejected.&nbsp;<br \\/>All offers are not-binding and without obligation. Parts of the pages or the complete publication including all offers and information might be extended, changed or partly or completely deleted by the author without separate announcement.&nbsp;<br \\/><br \\/>2. Referrals and links&nbsp;<br \\/>The author is not responsible for any contents linked or referred to from his pages - unless he has full knowledge of illegal contents and would be able to prevent the visitors of his site fromviewing those pages. If any damage occurs by the use of information presented there, only the author of the respective pages might be liable, not the one who has linked to these pages. Furthermore the author is not liable for any postings or messages published by users of discussion boards, guestbooks or mailinglists provided on his page.&nbsp;<br \\/><br \\/>3. Copyright&nbsp;<br \\/>The author intended not to use any copyrighted material for the publication or, if not possible, to indicate the copyright of the respective object.&nbsp;<br \\/>The copyright for any material created by the author is reserved. Any duplication or use of objects such as images, diagrams, sounds or texts in other electronic or printed publications is not permitted without the author''s agreement.&nbsp;<br \\/><br \\/>4. Privacy policy&nbsp;<br \\/>If the opportunity for the input of personal or business data (email addresses, name, addresses) is given, the input of these data takes place voluntarily. The use and payment of all offered services are permitted - if and so far technically possible and reasonable - without specification of any personal data or under specification of anonymized data or an alias. The use of published postal addresses, telephone or fax numbers and email addresses for marketing purposes is prohibited, offenders sending unwanted spam messages will be punished.&nbsp;<br \\/><br \\/>5. Legal validity of this disclaimer&nbsp;<br \\/>This disclaimer is to be regarded as part of the internet publication which you were referred from. If sections or individual terms of this statement are not legal or correct, the content or validity of the other parts remain uninfluenced by this fact.<br \\/>\"}', '', 0);
INSERT INTO `v3_ecp_lotto_zeiten` (`wochentag`, `uhrzeit`) VALUES (1, '18:00');
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(1, 'Newbie LVL 1', 'newbie1.gif', 0, 0, 10);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(2, 'Newbie LVL 2', 'newbie2.gif', 10, 0, 15);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(3, 'Newbie LVL 3', 'newbie3.gif', 20, 0, 20);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(4, 'Newbie LVL 4', 'newbie4.gif', 30, 0, 25);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(5, 'Newbie LVL 5', 'newbie5.gif', 50, 0, 30);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(6, 'Forummember LVL 1', 'leet1.gif', 100, 0, 40);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(7, 'Forummember LVL 2', 'leet2.gif', 150, 0, 50);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(8, 'Forummember LVL 3', 'leet3.gif', 200, 0, 60);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(9, 'Forummember LVL 4', 'leet4.gif', 250, 0, 70);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(10, 'Forummember LVL 5', 'leet5.gif', 300, 0, 80);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(11, 'Veteran LVL 1', 'veteran1.gif', 400, 0, 100);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(12, 'Veteran LVL 2', 'veteran2.gif', 500, 0, 120);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(13, 'Veteran LVL 3', 'veteran3.gif', 600, 0, 140);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(14, 'Veteran LVL 4', 'veteran4.gif', 700, 0, 160);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(15, 'Veteran LVL 5', 'veteran5.gif', 800, 0, 180);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(17, 'Superadmin', 'adminultra.gif', 0, 1, 100);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(18, 'Administrator', 'adminleet.gif', 0, 1, 50);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(19, 'Moderator', 'modstars.gif', 0, 1, 50);
INSERT INTO `v3_ecp_ranks` (`rankID`, `rankname`, `iconname`, `abposts`, `fest`, `money`) VALUES(20, 'Forumgod', 'supreme1.gif', 1000, 0, 200);
";
$updates = "UPDATE `v3_ecp_settings` SET `UPLOAD_METHOD` = 'Flash';
";
		$data = explode(';', $data);
		$updates = explode(';', $updates);
		$data2 = explode(');', $data2);
		for($i = 0; $i<count($data2); $i++) {
			$data2[$i] = $data2[$i].');';
		}
		$data = array_merge($data, $data2, $updates);
		$mysql_conid=mysql_pconnect($_POST['dbserver'], $_POST['dbuser'],$_POST['dbpw']);
		mysql_set_charset('latin1',$mysql_conid);//PHP 5 >= 5.2.3
		mysql_select_db($_POST['dbname']);
		foreach($data AS $value) {
			if(strlen(trim($value)) > 10) {
				mysql_query(str_replace('v3_', trim($_POST['tbl_pre']), $value));
				echo mysql_error();
			}
		}
    }
?>
</div>
</body>
</html>