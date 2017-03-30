<?php
	if(@$_SESSION['rights']['public']['teamspeak']['view'] OR @$_SESSION['rights']['superadmin']) {
		global $db;
		include_once('ts2status.php');
		$row = $db->fetch_assoc('SELECT ip, port, qport FROM '.DB_PRE.'ecp_teamspeak WHERE tsID= 1');
		$fp = @TSConn($row['ip'],$row['port'],$row['qport']);
		if($fp) {
			if(isset($_GET['ajax'])) {
				ob_end_clean();
				if(isset($_GET['cID'])) {
					$err = false;
					$cID 	= (int)$_GET['cID'];
					$type	= (int)$_GET['type'];	
				} else {
					$err = true;
				}	
				echo '<table cellpadding="0" cellspacing="0" width="98%" align="center">';			
				if($type==0) {
					echo defaultInfo($row['ip'],$row['qport'],$row['port']);
				} else if($type==1) {
					echo channelInfo($row['ip'],$row['qport'],$row['port'],$cID,1);
				} else if($type==2) {
					echo userInfo($row['ip'],$row['qport'],$row['port'], $cID);
				}
				echo '</table>';
				die();
			} else {
				$tpl = new smarty;	
				$tpl->assign('tsinfo', '<table cellpadding="0" cellspacing="0" width="98%" align="center">'.defaultInfo($row['ip'],$row['qport'],$row['port']).'</table>');
				$info = getTSInfo($row['ip'],$row['port'],$row['qport']);
				$users = getTSUsers($row['ip'],$row['port'],$row['qport']);
				$tpl->assign('ip', $row['ip']);
				$tpl->assign('port', $row['port']);
				$channels = getTSChannelInfo($row['ip'],$row['port'],$row['qport']);
				$tpl->assign('channels', $channels);
				$tpl->assign('users', $users);
				foreach($info AS $key=>$value) $tpl->assign($key, $value);
				ob_start();
				$tpl->display(DESIGN.'/tpl/teamspeak/teamspeak.html');
				$content = ob_get_contents();
				ob_end_clean();
				main_content(TEAMSPEAK_2, $content, '',1);				
			}
		} else {
		 	table(ERROR, SERVER_OFFLINE);
		}			
	} else
		echo table(ACCESS_DENIED, NO_ACCESS_RIGHTS);
?>