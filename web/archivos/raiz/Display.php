<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function Display()
{
	global $scripturl, $txt, $db_prefix, $modSettings, $context, $settings;
	global $options, $sourcedir, $user_info, $ID_MEMBER, $board_info, $topic;
	global $board, $messages_request, $language;

		loadTemplate('Display');
	$topicids	=	(int) $_GET['topic'];

if (empty($topic)) {
fatal_lang_error('smf232', false);
}


	if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
	{
		ob_end_clean();
		header('HTTP/1.1 403 Prefetch Forbidden');
		die;
	}


if (empty($_SESSION['last_read_topic']) || $_SESSION['last_read_topic'] != $topic) {
mysql_query("UPDATE {$db_prefix}topics SET numViews = numViews + 1 WHERE ID_TOPIC = $topic LIMIT 1");
$_SESSION['last_read_topic'] = $topic;
}

$dbresult = mysql_query("SELECT t.tag,l.ID,t.ID_TAG FROM {$db_prefix}tags_log as l, {$db_prefix}tags as t WHERE t.ID_TAG = l.ID_TAG && l.ID_TOPIC = $topic");
$context['topic_tags'] = array();
while($row = mysqli_fetch_assoc($dbresult)) {
$context['topic_tags'][] = array(
'ID' => $row['ID'],
'ID_TAG' => $row['ID_TAG'],
'tag' => $row['tag'],
);
}
mysqli_free_result($dbresult);

if(isset($_REQUEST['tags']) && !isset($_REQUEST['num_replies'])) {
$dbresult = mysql_query("SELECT COUNT(*) as total FROM {$db_prefix}tags_log WHERE ID_TOPIC = " . $topic, __FILE__, __LINE__);
$row = mysqli_fetch_assoc($dbresult);
$totaltags = $row['total'];
mysqli_free_result($dbresult);
$tags = explode(',',htmlentities($_REQUEST['tags'], ENT_QUOTES));
if($totaltags < $modSettings['smftags_set_maxtags']) {
$tagcount = 0;
foreach($tags as $tag) {
if($tagcount >= $modSettings['smftags_set_maxtags']) {
continue;
}
if(empty($tag)) {
continue;
}
if(strlen($tag) < $modSettings['smftags_set_mintaglength']) {
continue;
}
if(strlen($tag) > $modSettings['smftags_set_maxtaglength']) {
continue;
}
$dbresult = mysql_query("SELECT ID_TAG FROM {$db_prefix}tags WHERE tag = '$tag'");
if(db_affected_rows() == 0) {
mysql_query("INSERT INTO {$db_prefix}tags (tag) VALUES ('$tag')");	
$ID_TAG = db_insert_id();
mysql_query("INSERT INTO {$db_prefix}tags_log (ID_TAG,ID_TOPIC, ID_MEMBER) VALUES ($ID_TAG,$topic,$ID_MEMBER)");
$tagcount++;
} else {
$row = mysqli_fetch_assoc($dbresult);
$ID_TAG = $row['ID_TAG'];
$dbresult2= mysql_query("SELECT ID FROM {$db_prefix}tags_log WHERE ID_TAG  =  $ID_TAG  AND ID_TOPIC = $topic");
if(db_affected_rows() != 0) {
continue;
}
mysqli_free_result($dbresult2);
mysql_query("INSERT INTO {$db_prefix}tags_log (ID_TAG,ID_TOPIC, ID_MEMBER) VALUES ($ID_TAG,$topic,$ID_MEMBER)");
$tagcount++;
}
mysqli_free_result($dbresult);
}
}
}
$request = mysql_query("SELECT * FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS ms) WHERE t.ID_TOPIC = $topic AND ms.ID_TOPIC = $topicids LIMIT 1");
if (mysqli_num_rows($request) == 0) {
fatal_lang_error(472, false);
}
$topicinfo = mysqli_fetch_assoc($request);
mysqli_free_result($request);

	$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']);
	censorText($topicinfo['subject']);
	$context['page_title'] = $topicinfo['subject'];
	$topicinfo['isSticky'] = empty($modSettings['enableStickyTopics']) ? '0' : $topicinfo['isSticky'];
	$context['is_locked'] = $topicinfo['locked'];
    $context['board']     = $topicinfo['board'];
    $message['board']     = $topicinfo['board'];
	$context['is_sticky'] = $topicinfo['isSticky'];
	determineTopicClass($context);
	$context['user']['started'] = $ID_MEMBER == $topicinfo['ID_MEMBER_STARTED'] && !$user_info['is_guest'];
	$context['topic_starter_id'] = $topicinfo['ID_MEMBER_STARTED'];
	$context['subject'] = $topicinfo['subject'];
	$context['can_view_post'] = $topicinfo['hiddenOption'];
	$context['num_views'] = $topicinfo['numViews'];

	$request = mysql_query("SELECT ID_MSG, ID_MEMBER FROM {$db_prefix}messages WHERE ID_TOPIC = " . $topic);

	$messages = array();
	$posters = array();
	while ($row = mysqli_fetch_assoc($request)) 	{
	if (!empty($row['ID_MEMBER'])) {
		$posters[] = $row['ID_MEMBER'];
	}
		$messages[] = $row['ID_MSG'];
	}
	mysqli_free_result($request);
	$posters = array_unique($posters);

    if(isset($context['single-post']) && in_array($context['single-post'], $messages))
	$messages = array ($context['single-post']);

	if (!empty($messages)) 
	{
		// What?  It's not like it *couldn't* be only guests in this topic...
		if (!empty($posters))
			loadMemberData($posters);
			
		/* Post Relacionados por Tags */
$request = mysql_query("SELECT t.ID_TAG FROM ({$db_prefix}tags_log AS tl INNER JOIN {$db_prefix}tags AS t ON tl.ID_TAG = t.ID_TAG) INNER JOIN {$db_prefix}messages AS m ON m.ID_TOPIC = tl.ID_TOPIC WHERE m.ID_TOPIC = " . $topic);
while ($row = mysqli_fetch_assoc($request)) {
$context['tags'][] = array(
'id' => $row['ID_TAG']
);
}
mysqli_free_result($request);

		foreach ($context['tags'] as $valtags) 
			{ $valins = $valins.$valtags['id'].", "; }
		$valins= substr($valins,0,strlen($valins)-2);

		$request = mysql_query("
		SELECT m.ID_MSG, m.subject, t.ID_TOPIC, t.ID_BOARD, m.hiddenOption, m.hiddenValue,
		b.name AS bname, b.description, t.numReplies, m.ID_MEMBER, IFNULL(mem.realName, m.posterName) AS posterName,
		mem.hideEmail, m.modifiedTime, mem.ID_MEMBER, tl.ID_TAG, tl.ID_TOPIC, ts.ID_TAG
		FROM ({$db_prefix}topics AS t, {$db_prefix}tags AS ts, {$db_prefix}tags_log AS tl, {$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}members AS mem)
		WHERE m.ID_MSG = t.ID_FIRST_MSG
		AND mem.ID_MEMBER = m.ID_MEMBER
		AND t.ID_BOARD = b.ID_BOARD
		AND m.ID_TOPIC = tl.ID_TOPIC
		AND tl.ID_TAG = ts.ID_TAG AND tl.ID_TAG IN (".$valins.")
		AND tl.ID_TOPIC <> $topic
		ORDER BY RAND()
		LIMIT 0, 10");
		$context['posts10'] = array();
		while ($row = mysqli_fetch_assoc($request)) {
		$row['can_view_post'] = 1;
		if (!empty($modSettings['allow_hiddenPost']) && $row['hiddenOption'] > 0) {
			global $sourcedir;
			require_once($sourcedir . '/HidePost.php');
			$context['current_message'] = $row;
			$row['body'] = getHiddenMessage();
			$row['can_view_post'] = $context['can_view_post'];
		}
		
				$context['posts10'][$row['ID_MSG']] = array(
				'can_view_post' => $row['can_view_post'],
				'id' => $row['ID_TOPIC'],
				'subject' => $row['subject'],
				'bname' => $row['bname'],
				'idb' => $row['ID_BOARD'],
				'description' => $row['description']
				);
		}
mysqli_free_result($request);
/* Post Relacionados por Tags */

$topic	=	$_REQUEST['topic'];		
$request = mysql_query("SELECT t.ID_TOPIC, t.points FROM ({$db_prefix}topics AS t) WHERE t.ID_TOPIC= " . $topic);
while ($row = mysqli_fetch_assoc($request)) {
$context['points-post'] = $row['points'];
}	
mysqli_free_result($request);
	
/* Veamos si hay comentarios */
$request = mysql_query("SELECT * FROM ({$db_prefix}comments) WHERE ID_TOPIC = " . $topic);
$context['haycom'] = mysqli_fetch_assoc($request);

/* Si los hay, dice cuantos... */
$request = mysql_query("SELECT * FROM ({$db_prefix}comments) WHERE ID_TOPIC = " . $topic);
$context['numcom'] =  mysqli_num_rows($request);

/* Marquemos los comentarios */
$request = mysql_query("SELECT c.comment, c.comment AS comentario2, c.ID_TOPIC, c.ID_MEMBER, mem.ID_MEMBER, mem.memberName, mem.realName, c.ID_COMMENT, c.posterTime FROM ({$db_prefix}comments AS c, {$db_prefix}members AS mem)  WHERE c.ID_TOPIC = $topic AND c.ID_MEMBER = mem.ID_MEMBER ORDER BY c.ID_COMMENT ASC ");
$context['comentarios'] = array();
while ($row = mysqli_fetch_assoc($request)) {
$row['comment'] = parse_bbc($row['comment'], '1', $row['ID_MSG']);
$row['comentario0'] = parse_bbc($row['comentario0'], '0', $row['ID_MSG']);
censorText($row['comment']);
censorText($row['comentario2']);
		
$context['comentarios'][] = array(
'comentario2' => $row['comentario2'],
'comment' => $row['comment'],
'citar' => $row['comentario0'],
'user' => $row['ID_MEMBER'],
'nomuser' => $row['realName'],
'nommem' => $row['memberName'],
'id' => $row['ID_COMMENT'],
'fecha' => $row['posterTime'],
);
}
mysqli_free_result($request);

ssi_grupos();
	
$rs = mysql_query("SELECT o.ID_TOPIC FROM ({$db_prefix}bookmarks AS o) WHERE o.ID_TOPIC = $topic AND o.TYPE = 'posts'");
$context['fav1'] = mysqli_num_rows($rs);

$messages_request = mysql_query("
SELECT m.ID_TOPIC, m.subject, m.posterTime, m.posterIP, m.ID_MEMBER, m.modifiedTime, m.modifiedName, m.body, m.hiddenOption, m.hiddenValue, m.ID_BOARD, b.ID_BOARD, b.description, b.name AS bname, m.smileysEnabled, m.posterName, m.posterEmail
FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b)
WHERE m.ID_TOPIC = $topicids
AND b.ID_BOARD = m.ID_BOARD
ORDER BY m.ID_TOPIC ");
}

	loadJumpTo();
	$context['get_message'] = 'prepareDisplayContext';
	$context['allow_hide_email'] = !empty($modSettings['allow_hideEmail']) || ($user_info['is_guest'] && !empty($modSettings['guest_hideContacts']));
$common_permissions = array(
	'can_sticky' => 'make_sticky',
	'can_send_topic' => 'send_topic',
	'can_send_pm' => 'pm_send',
	'can_moderate_forum' => 'moderate_forum'
);
foreach ($common_permissions as $contextual => $perm) {
$context[$contextual] = allowedTo($perm);
}

$anyown_permissions = array(
	'can_move' => 'move',
	'can_lock' => 'lock',
	'can_delete' => 'remove',
	'can_reply' => 'post_reply',
);
foreach ($anyown_permissions as $contextual => $perm) {
$context[$contextual] = allowedTo($perm . '_any') || ($context['user']['started'] && allowedTo($perm . '_own'));
}

	$context['can_sticky'] &= !empty($modSettings['enableStickyTopics']);
	$context['can_reply'] &= empty($topicinfo['locked']) || allowedTo('moderate_board');

	$board_count = 0;
	foreach ($context['jump_to'] as $id => $cat){
		$board_count += count($context['jump_to'][$id]['boards']);
	}
	$context['can_move'] &= $board_count > 1;

	$context['can_remove_post'] = allowedTo('delete_any') || (allowedTo('delete_replies') && $context['user']['started']);

	if (!empty($options['display_quick_reply']))
		checkSubmitOnce('register');
}

function prepareDisplayContext($reset = false)
{
	global $settings, $txt, $modSettings, $scripturl, $options, $user_info;
	global $memberContext, $context, $messages_request, $topic, $ID_MEMBER;

	static $counter = null;
	if ($messages_request == false)
		return false;
	if ($counter === null || $reset)
		$counter = empty($options['view_newest_first']) ? $context['start'] : $context['num_replies'] - $context['start'];
	if ($reset)
		return @mysql_data_seek($messages_request, 0);
	$message = mysqli_fetch_assoc($messages_request);
	if (!$message)
		return false;
		
	$message['subject'] = $message['subject'] != '' ? $message['subject'] : $txt[24];
	$context['can_remove_post'] |= allowedTo('delete_own') && (empty($modSettings['edit_disable_time']) || $message['posterTime'] + $modSettings['edit_disable_time'] * 60 >= time()) && $message['ID_MEMBER'] == $ID_MEMBER;
	if (!loadMemberContext($message['ID_MEMBER']))
	{		$memberContext[$message['ID_MEMBER']]['name'] = $message['posterName'];
		$memberContext[$message['ID_MEMBER']]['id'] = 0;
		$memberContext[$message['ID_MEMBER']]['group'] = $txt[28];
		$memberContext[$message['ID_MEMBER']]['link'] = $message['posterName'];
		$memberContext[$message['ID_MEMBER']]['estado_icon'] = $message['estado_icon'];
		$memberContext[$message['ID_MEMBER']]['email'] = $message['posterEmail'];
		$memberContext[$message['ID_MEMBER']]['hide_email'] = $message['posterEmail'] == '' || (!empty($modSettings['guest_hideContacts']) && $user_info['is_guest']);
		$memberContext[$message['ID_MEMBER']]['is_guest'] = true;
	}
	else
	{
		$memberContext[$message['ID_MEMBER']]['can_view_profile'] = allowedTo('profile_view_any') || ($message['ID_MEMBER'] == $ID_MEMBER && allowedTo('profile_view_own'));
		$memberContext[$message['ID_MEMBER']]['is_topic_starter'] = $message['ID_MEMBER'] == $context['topic_starter_id'];
	}
	$memberContext[$message['ID_MEMBER']]['ip'] = $message['posterIP'];
	censorText($message['body']);
	censorText($message['subject']);
	$disable_unhideafter = false;
	$message['can_view_post'] = 1;
	if (!empty($modSettings['allow_hiddenPost']) && $message['hiddenOption'] > 0)
	{
		global $sourcedir;
		require_once($sourcedir . '/HidePost.php');
		$context['current_message'] = $message;
		$message['body'] = getHiddenMessage();
		$message['can_view_post'] = $context['can_view_post'];
	}

	$message['body'] = parse_bbc($message['body'], $message['smileysEnabled'], $message['ID_MSG']);

	$output = array(
		'can_view_post' => $message['can_view_post'],
		'alternate' => $counter % 2,
		'id' => $message['ID_MSG'],
		'href' => $boardurl . '/post/' . $topic,
		'link' => '<a href="' . $boardurl . '/post/' . $topic . '">' . $message['subject'] . '</a>',
		'member' => &$memberContext[$message['ID_MEMBER']],
		'subject' => $message['subject'],
    	'board' => array(
				'id' => $message['ID_BOARD'],
				'name' => $message['bname'],
				'description' => $message['description'],
				'href' => $boardurl . '/categoria/' . $message['description'],
				'link' => '<a href="' . $boardurl . '/categoria/' . $message['description'] . '" title="' . $message['name'] . '">' . $message['name'] . '</a>'
			),
		'category' => $message['catName'],
		'time' => timeformat($message['posterTime']),
		'timestamp' => forum_time(true, $message['posterTime']),
		'counter' => $counter,
		'modified' => array(
			'time' => timeformat($message['modifiedTime']),
			'timestamp' => forum_time(true, $message['modifiedTime']),
			'name' => $message['modifiedName']
		),
		'body' => $message['body'],
		'can_modify' => (!$context['is_locked'] || allowedTo('moderate_board')) && (allowedTo('modify_any') || (allowedTo('modify_replies') && $context['user']['started']) || (allowedTo('modify_own') && $message['ID_MEMBER'] == $ID_MEMBER && (empty($modSettings['edit_disable_time']) || $message['posterTime'] + $modSettings['edit_disable_time'] * 60 > time()))),
		'can_remove' => allowedTo('delete_any') || (allowedTo('delete_replies') && $context['user']['started']) || (allowedTo('delete_own') && $message['ID_MEMBER'] == $ID_MEMBER && (empty($modSettings['edit_disable_time']) || $message['posterTime'] + $modSettings['edit_disable_time'] * 60 > time())),
		'can_see_ip' => allowedTo('moderate_forum') || ($message['ID_MEMBER'] == $ID_MEMBER && !empty($ID_MEMBER)),
		'can_view_post' => $message['can_view_post'],
	);

	if($disable_unhideafter) 
		$context['user_post_avaible'] = 0;
	
	if (empty($options['view_newest_first']))
		$counter++;
	else
		$counter--;

	return $output;
}

function theme_quickreply_box()
{
	global $txt, $modSettings, $db_prefix;
	global $context, $settings, $user_info;
	
	if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
	{
		$temp1 = $settings['theme_url'];
		$settings['theme_url'] = $settings['default_theme_url'];
		$temp2 = $settings['images_url'];
		$settings['images_url'] = $settings['default_images_url'];
		$temp3 = $settings['theme_dir'];
		$settings['theme_dir'] = $settings['default_theme_dir'];
	}
	$context['smileys'] = array(
		'postform' => array(),
		'popup' => array(),
	);
	loadLanguage('Post');
	if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none')
		$context['smileys']['postform'][] = array();
	elseif ($user_info['smiley_set'] != 'none')
	{
		if (($temp = cache_get_data('posting_smileys', 480)) == null)
		{
			$request = mysql_query("
				SELECT code, filename, description, smileyRow, hidden
				FROM {$db_prefix}smileys
				WHERE hidden IN (0, 2)
				ORDER BY smileyRow, smileyOrder");
			while ($row = mysqli_fetch_assoc($request))
			{
				$row['code'] = htmlspecialchars($row['code']);
				$row['filename'] = htmlspecialchars($row['filename']);
				$row['description'] = htmlspecialchars($row['description']);

				$context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smileyRow']]['smileys'][] = $row;
			}
			mysqli_free_result($request);

			cache_put_data('posting_smileys', $context['smileys'], 480);
		}
		else
			$context['smileys'] = $temp;
	}

	foreach (array_keys($context['smileys']) as $location)
	{
		foreach ($context['smileys'][$location] as $j => $row)
		{
			$n = count($context['smileys'][$location][$j]['smileys']);
			for ($i = 0; $i < $n; $i++)
			{
				$context['smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['code']);
				$context['smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['description']);
			}

			$context['smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
		}
		if (!empty($context['smileys'][$location]))
			$context['smileys'][$location][count($context['smileys'][$location]) - 1]['last'] = true;
	}
	$settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];
	$context['show_bbc'] = !empty($modSettings['enableBBC']) && !empty($settings['show_bbc']);
	if (!empty($modSettings['disabledBBC']))
	{
		$disabled_tags = explode(',', $modSettings['disabledBBC']);
		foreach ($disabled_tags as $tag)
			$context['disabled_tags'][trim($tag)] = true;
	}
	template_quickreply_box();
	if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
	{
		$settings['theme_url'] = $temp1;
		$settings['images_url'] = $temp2;
		$settings['theme_dir'] = $temp3;
	}
}
?>