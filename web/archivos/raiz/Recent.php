<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function RecentPosts()
{
	global $context, $txt, $db_prefix;

	loadTemplate('Recent');
	$context['page_title'] = $txt[214];

	$request = mysql_query("
	SELECT *
	FROM {$db_prefix}comments as c, {$db_prefix}topics AS t
	WHERE c.ID_TOPIC = t.ID_TOPIC");
	$request2 = mysql_query("
	SELECT * 
	FROM {$db_prefix}gallery_pic AS p, {$db_prefix}gallery_comment AS c
	WHERE p.ID_PICTURE = c.ID_PICTURE");
	
	$context['total_comments'] = mysql_num_rows($request) + mysql_num_rows($request2);
}
?>