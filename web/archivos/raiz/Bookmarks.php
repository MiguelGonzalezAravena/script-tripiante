<?php
if (!defined('SMF'))
	die('Hacking attempt...');
@require_once('SSI.php');
function Bookmarks()
{
	global $txt, $context, $scripturl, $settings, $modSettings, $user_info, $ID_MEMBER, $return, $smcFunc, $db_prefix;

	loadTemplate('Bookmarks');
	isAllowedTo('make_bookmarks');

	$context['page_title'] = $txt['bookmarks'];

	$context['sub_action'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : '';
	
	switch ($context['sub_action'])
	{
		case 'delete':
		$return = !empty($_POST['remove_bookmarks']) ? deleteBookmark($_POST['remove_bookmarks']) : '';
		break;
	}

}

function deleteBookmark($topic_ids, $id_member = null)
{
	global $txt, $context, $db_prefix;

	if ($id_member == null)
		$id_member = $context['user']['id'];

	foreach ($topic_ids as $index => $id)
		$topic_ids[$index] = (int) $id;

	$topics = implode(',', $topic_ids);

	$result = db_query("DELETE FROM {$db_prefix}bookmarks WHERE ID_TOPIC IN($topics) AND ID_MEMBER = $id_member AND TYPE = 'posts' ", __FILE__, __LINE__);
	
	$deleted = mysql_affected_rows();

	if ($result)
		return sprintf($txt['bookmark_delete_success'], $deleted);
	else
		return sprintf($txt['bookmark_delete_failure'], $deleted);
}
?>