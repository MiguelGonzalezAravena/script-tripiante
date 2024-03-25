<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function BuddiesMain()
{
	isAllowedTo ('profile_extra_own');
	loadTemplate('Buddies');

	switch (@$_GET['sa']) {
		case 'add': BuddyAdd(); break;
		case 'remove': BuddyRemove(); break;
		case 'order': BuddyOrder(); break;
		default: Buddies();
	}
}


function Buddies()
{
	global $db_prefix, $ID_MEMBER, $context, $user_profile, $memberContext,  $txt;

	// approved buddies
	$buddies = array();
	$request = db_query ('SELECT BUDDY_ID FROM ' . $db_prefix . 'buddies 
			WHERE ID_MEMBER = ' . $ID_MEMBER . '
			ORDER BY position ASC, time_updated DESC', __FILE__, __LINE__);
	while ($row = mysql_fetch_assoc($request))
		$buddies[] = $row['BUDDY_ID'];
	mysql_free_result($request);

	// Load all the members up.
	loadMemberData($buddies, false, 'profile');
	$context['buddies'] = array();
	foreach ($buddies as $buddy)
	{
		loadMemberContext($buddy);
		$context['buddies'][$buddy] = $memberContext[$buddy];
	}

	$_GET['action'] = 'profile'; // just for the tab...
	$context['page_title'] = $txt['buddy_center'];
	$context['sub_template'] = 'buddy_center';
}

function BuddyOrder()
{
	global $db_prefix, $ID_MEMBER;

	checkSession('get');
	$_GET['u'] = (int)$_GET['u'];
	$request = db_query ('SELECT position FROM ' . $db_prefix . 'buddies WHERE BUDDY_ID = ' . $_GET['u'] . ' AND ID_MEMBER = ' . $ID_MEMBER, __FILE__, __LINE__);
	list ($old_position) = mysql_fetch_row ($request);	
	if ($_GET['dir'] == 'up')
		$request = db_query ('SELECT BUDDY_ID, position FROM ' . $db_prefix . 'buddies WHERE ID_MEMBER = ' . $ID_MEMBER . ' AND position < ' . $old_position . ' ORDER BY time_updated DESC LIMIT 1', __FILE__, __LINE__);
	else
		$request = db_query ('SELECT BUDDY_ID, position FROM ' . $db_prefix . 'buddies WHERE ID_MEMBER = ' . $ID_MEMBER . ' AND position > ' . $old_position . ' ORDER BY time_updated DESC LIMIT 1', __FILE__, __LINE__);

	list ($buddy_id, $new_position) = mysql_fetch_row ($request);
	$buddy_id = (int)$buddy_id;
	$new_position = (int)$new_position;
	
	if ($new_position == 0)
		$new_position = ($_GET['dir'] == 'up') ? $old_position - 1 : $old_position + 1;
	db_query ('UPDATE ' . $db_prefix . 'buddies SET position = "' . $new_position . '", time_updated = "' . time() . '" WHERE ID_MEMBER = ' . $ID_MEMBER . ' AND BUDDY_ID = ' . $_GET['u'], __FILE__, __LINE__);
	db_query ('UPDATE ' . $db_prefix . 'buddies SET position = "' . $old_position . '", time_updated = "' . time() . '" WHERE ID_MEMBER = ' . $ID_MEMBER . ' AND BUDDY_ID = ' . $buddy_id, __FILE__, __LINE__);
	
	redirectexit('action=buddies');
}

function BuddyAdd()
{
	global $db_prefix, $ID_MEMBER, $sourcedir, $txt, $context, $scripturl, $boardurl;

	$_GET['user'] = $_GET['user'];
	$request = db_query("SELECT * FROM ({$db_prefix}buddies AS b, {$db_prefix}members AS mem) WHERE b.ID_MEMBER = mem.ID_MEMBER AND b.BUDDY_ID = mem.ID_MEMBER AND mem.memberName = '" . $_GET['user'] . "' ", __FILE__, __LINE__);
	if (mysql_num_rows ($request) > 0)
		fatal_error ($txt['buddy_already_added'], false);
	$request = db_query("SELECT realName, memberName FROM {$db_prefix}members WHERE memberName = '" . $_GET['user'] . "'", __FILE__, __LINE__);
	if (mysql_num_rows ($request) < 1)
		redirectexit();

	// Find the new position.
	$request = db_query("SELECT position FROM {$db_prefix}buddies 
			WHERE ID_MEMBER = {$ID_MEMBER}
			ORDER BY position DESC
			LIMIT 1", __FILE__, __LINE__);
	list ($position) = mysql_fetch_row ($request);
	$position = $position + 1;
$request	=	db_query("SELECT * FROM {$db_prefix}members WHERE memberName = '" . $_GET['user'] . "'", __FILE__, __LINE__);
$row		=	mysql_fetch_assoc($request);
	db_query ('INSERT INTO ' . $db_prefix . 'buddies SET ID_MEMBER = ' . $ID_MEMBER . ', BUDDY_ID = ' . $row['ID_MEMBER'] . ', position = ' . $position . ', time_updated = "' . time() . '", requested = ' . $ID_MEMBER, __FILE__, __LINE__);
/*
	$request = db_query ('SELECT position FROM ' . $db_prefix . 'buddies 
			WHERE ID_MEMBER = ' . $row['ID_MEMBER'] . '  
			ORDER BY position DESC
			LIMIT 1', __FILE__, __LINE__);
	list ($position) = mysql_fetch_row ($request);
	$position = $position + 1;
	db_query ('INSERT INTO ' . $db_prefix . 'buddies SET BUDDY_ID = ' . $ID_MEMBER . ', ID_MEMBER = ' . $row['ID_MEMBER'] . ', approved = 1, position = ' . $position . ', time_updated = "' . time() . '", requested = ' . $ID_MEMBER, __FILE__, __LINE__);
*/
	
	redirectexit($boardurl . '/perfil/' . $row['memberName']);
}

function BuddyRemove()
{
	global $db_prefix, $ID_MEMBER, $user_info, $user_profile, $boardurl;

	$_GET['user'] = $_GET['user'];
	
$request	=	db_query("SELECT * FROM {$db_prefix}members WHERE memberName = '" . $_GET['user'] . "'", __FILE__, __LINE__);
$row		=	mysql_fetch_assoc($request);
	db_query ("DELETE FROM {$db_prefix}buddies WHERE ID_MEMBER = {$ID_MEMBER} AND BUDDY_ID = " . $row['ID_MEMBER'] . "", __FILE__, __LINE__);
/*	db_query ('DELETE FROM ' . $db_prefix . 'buddies WHERE BUDDY_ID = ' . $ID_MEMBER . ' AND ID_MEMBER = ' . $row['ID_MEMBER'], __FILE__, __LINE__); */

	// update SMF's system as well...
	$user_info['buddies'] = array_diff($user_info['buddies'], array($row['ID_MEMBER']));
	updateMemberData($ID_MEMBER, array('buddy_list' => "'" . implode(',', $user_info['buddies']) . "'"));

	loadMemberData($row['ID_MEMBER'], false, 'normal');
	$buddies = explode (',', $user_profile[$row['ID_MEMBER']]['buddy_list']);
	$buddies = array_diff($buddies, array($ID_MEMBER));
	updateMemberData($row['ID_MEMBER'], array('buddy_list' => "'" . implode(',', $buddies) . "'"));
	redirectexit($boardurl . '/perfil/' . $row['memberName']);
}

?>