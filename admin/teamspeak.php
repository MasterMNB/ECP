<?php
	if(@$_SESSION['rights']['admin']['teamspeak']['edit'] OR @$_SESSION['rights']['superadmin']) {
		global $db;
		$tpl = new smarty();
		$row = $db->fetch_assoc('SELECT ip, port, qport FROM '.DB_PRE.'ecp_teamspeak');
		$tpl->assign('port', @$row['port']);
		$tpl->assign('ip', @$row['ip']);
		$tpl->assign('qport', @$row['qport']);
		ob_start();
		$tpl->display(DESIGN.'/tpl/admin/teamspeak.html');
		$content = ob_get_contents();
		ob_end_clean();
		main_content(TEAMSPEAK_2, $content, '',1);	
	} else {
		table(ERROR, ACCESS_DENIED);
	}
?>