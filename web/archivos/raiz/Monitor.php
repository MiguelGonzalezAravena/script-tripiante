<?php
if (!defined('SMF'))
	die('Hacking attempt...');
	
function Monitor()
{
	global $settings, $context, $txt, $db_prefix, $ID_MEMBER, $modSettings;
	
	if($context['user']['is_guest']) {
	fatal_error('Disculpe, usted tiene que estar REGISTRADO para aceder a esta funci&oacute;n.-', false);
	}
	@require_once('SSI.php');
	$context['page_title'] = 'Monitor de usuario';
	loadTemplate('Monitor');

	
/* Monitor de Comentarios */
$request = db_query("
SELECT m.subject, m.ID_TOPIC, t.ID_TOPIC, t.ID_BOARD, t.points, b.name AS bname, b.description, m.ID_MEMBER, c.posterTime, c.comment, c.ID_COMMENT
FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}comments AS c)
WHERE t.ID_TOPIC = c.ID_TOPIC
AND c.ID_TOPIC = m.ID_TOPIC
AND t.ID_BOARD = b.ID_BOARD
AND m.ID_MEMBER = {$ID_MEMBER}
ORDER BY c.ID_COMMENT DESC
LIMIT " . $modSettings['monitor_post_comments'], __FILE__, __LINE__);
$context['monitorcom'] = array();
while ($row = mysql_fetch_assoc($request))
$context['monitorcom'][] = array(
'titulo' => ssi_reducir($row['subject']),
'points' => $row['points'],
'id' => $row['ID_TOPIC'],
'bname' => $row['bname'],
'description' => $row['description'],
'ID_BOARD' => $row['ID_BOARD'],
'comment' => $row['comment'],
'posterTime' => $row['posterTime'],
'ID_COMMENT' => $row['ID_COMMENT'],
);
mysql_free_result($request);
/* Monitor de Comentarios */

/* Monitor de Favoritos Posts */
$request = db_query("
SELECT m.subject, m.ID_TOPIC, t.ID_TOPIC, t.ID_BOARD, t.points, b.name AS bname, b.description, m.ID_MEMBER, c.TYPE, c.ID_TOPIC, c.ID_MEMBER as cmember, c.ID_BOOKMARK, r.ID_MEMBER, r.realName
FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}bookmarks AS c, {$db_prefix}members AS r)
WHERE t.ID_TOPIC = c.ID_TOPIC
AND c.ID_TOPIC = m.ID_TOPIC
AND t.ID_BOARD = b.ID_BOARD
AND m.ID_MEMBER = {$ID_MEMBER}
AND r.ID_MEMBER = c.ID_MEMBER
AND c.TYPE = 'posts'
ORDER BY c.ID_BOOKMARK DESC
LIMIT " . $modSettings['monitor_post_bookmarks'], __FILE__, __LINE__);
$context['monitorfav'] = array();
while ($row = mysql_fetch_assoc($request))
$context['monitorfav'][] = array(
'titulo' => ssi_reducir($row['subject']),
'points' => $row['points'],
'ID_TOPIC' => $row['ID_TOPIC'],
'cmember' => $row['cmember'],
'bname' => $row['bname'],
'description' => $row['description'],
'ID_BOARD' => $row['ID_BOARD'],
'ID_BOOKMARK' => $row['ID_BOOKMARK'],
'realName' => $row['realName'],
);
mysql_free_result($request);
/* Monitor de Favoritos Posts */

/* Monitor de Favoritos Imágenes */
$request = db_query("
SELECT t.title, t.ID_PICTURE, t.ID_MEMBER, t.points, c.TYPE, c.ID_TOPIC, c.ID_MEMBER as cmember, c.ID_BOOKMARK, r.ID_MEMBER, r.realName
FROM ({$db_prefix}gallery_pic AS t, {$db_prefix}bookmarks AS c, {$db_prefix}members AS r)
WHERE t.ID_PICTURE = c.ID_TOPIC
AND t.ID_MEMBER = {$ID_MEMBER}
AND r.ID_MEMBER = c.ID_MEMBER
AND c.TYPE = 'imagen'
ORDER BY c.ID_BOOKMARK DESC
LIMIT " . $modSettings['monitor_image_bookmarks'], __FILE__, __LINE__);
$context['monitorfavimagenes'] = array();
while ($row = mysql_fetch_assoc($request))
$context['monitorfavimagenes'][] = array(
'title' => ssi_reducir($row['title']),
'points' => $row['points'],
'ID_PICTURE' => $row['ID_TOPIC'],
'cmember' => $row['cmember'],
'ID_BOOKMARK' => $row['ID_BOOKMARK'],
'realName' => $row['realName'],
);
mysql_free_result($request);
/* Monitor de Favoritos Imágenes */

/* Monitor de Imágenes */
$request = db_query("
SELECT p.ID_PICTURE, p.ID_MEMBER, p.title, p.points, g.ID_PICTURE, g.ID_COMMENT, g.comment, g.date
FROM ({$db_prefix}gallery_comment AS g, {$db_prefix}gallery_pic AS p)
WHERE p.ID_MEMBER = {$ID_MEMBER}
AND p.ID_PICTURE = g.ID_PICTURE
ORDER BY g.ID_COMMENT DESC
LIMIT " . $modSettings['monitor_image_comments'], __FILE__, __LINE__);
$context['monitorimg'] = array();
while ($row = mysql_fetch_assoc($request))
$context['monitorimg'][] = array(
'ID_PICTURE' => $row['ID_PICTURE'],
'ID_COMMENT' => $row['ID_COMMENT'],
'comment' => $row['comment'],
'date' => $row['date'],
'title' => ssi_reducir($row['title']),
'points' => $row['points'],
);
mysql_free_result($request);
/* Monitor de Favoritos */

/* Monitor de Puntos Posts */
$request = db_query("
SELECT m.subject, m.ID_TOPIC, t.ID_TOPIC, t.ID_BOARD, t.points, b.name AS bname, b.description, m.ID_MEMBER, c.TYPE, c.ID_TOPIC, c.ID_MEMBER as cmember, c.ID_POINTS, c.POINTS, r.ID_MEMBER, r.realName
FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}points AS c, {$db_prefix}members AS r)
WHERE t.ID_TOPIC = c.ID_TOPIC
AND c.ID_TOPIC = m.ID_TOPIC
AND t.ID_BOARD = b.ID_BOARD
AND m.ID_MEMBER = {$ID_MEMBER}
AND r.ID_MEMBER = c.ID_MEMBER
AND c.TYPE = 'post'
ORDER BY c.ID_POINTS DESC
LIMIT " . $modSettings['monitor_post_points'], __FILE__, __LINE__);
$context['monitorpun'] = array();
while ($row = mysql_fetch_assoc($request))
$context['monitorpun'][] = array(
'titulo' => ssi_reducir($row['subject']),
'puntos' => $row['puntos'],
'ID_TOPIC' => $row['ID_TOPIC'],
'cmember' => $row['cmember'],
'bname' => $row['bname'],
'description' => $row['description'],
'ID_BOARD' => $row['ID_BOARD'],
'id' => $row['id'],
'amount' => $row['POINTS'],
'realName' => $row['realName'],
);
mysql_free_result($request);
/* Monitor de puntos */

/* Monitor de Puntos Imágenes */
$request = db_query("
SELECT t.title, t.ID_PICTURE, t.points, t.ID_MEMBER, c.TYPE, c.ID_TOPIC, c.ID_MEMBER as cmember, c.ID_POINTS, c.POINTS, r.ID_MEMBER, r.realName
FROM ({$db_prefix}gallery_pic AS t, {$db_prefix}points AS c, {$db_prefix}members AS r)
WHERE t.ID_PICTURE = c.ID_TOPIC
AND t.ID_MEMBER = {$ID_MEMBER}
AND r.ID_MEMBER = c.ID_MEMBER
AND c.TYPE = 'imagen'
ORDER BY c.ID_POINTS DESC
LIMIT " . $modSettings['monitor_image_points'], __FILE__, __LINE__);
$context['monitorpunimagenes'] = array();
while ($row = mysql_fetch_assoc($request))
$context['monitorpunimagenes'][] = array(
'title' => ssi_reducir($row['title']),
'ID_PICTURE' => $row['ID_PICTURE'],
'cmember' => $row['cmember'],
'amount' => $row['POINTS'],
'realName' => $row['realName'],
);
mysql_free_result($request);
/* Monitor de Puntos Imagenes */

/* Monitor de Yo en Amigos */
$request = db_query("
SELECT b.BUDDY_ID, b.ID_MEMBER, mem.ID_MEMBER, mem.memberName, mem.realName, b.time_updated
FROM ({$db_prefix}buddies AS b, {$db_prefix}members AS mem)
WHERE b.BUDDY_ID = {$ID_MEMBER}
AND b.ID_MEMBER = mem.ID_MEMBER
ORDER BY b.time_updated DESC
LIMIT " . $modSettings['monitor_friends'], __FILE__, __LINE__);
$context['yoamigos'] = array();
while ($row = mysql_fetch_assoc($request))
$context['yoamigos'][] = array(
'memberName' => $row['memberName'],
'time_updated' => timeformat($row['time_updated']),
'realName' => $row['realName'],
);
mysql_free_result($request);
/* Monitor de Yo en Amigos */
}
?>