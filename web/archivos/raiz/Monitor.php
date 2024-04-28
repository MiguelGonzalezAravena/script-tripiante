<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function Monitor() {
  global $context, $db_prefix, $ID_MEMBER, $modSettings;

  if ($context['user']['is_guest']) {
    fatal_error('Disculpe, usted tiene que estar REGISTRADO para acceder a esta funci&oacute;n.-', false);
  }

  @require_once('SSI.php');
  $context['page_title'] = 'Monitor de usuario';
  loadTemplate('Monitor');

  // Comentarios en mis posts
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

  while ($row = mysqli_fetch_assoc($request)) {
    $row['subject'] = censorText($row['subject']);
    $context['monitorcom'][] = array(
      'titulo' => $row['subject'],
      'short_title' => htmlentities(ssi_reducir($row['subject']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['subject'], ENT_QUOTES, 'ISO-8859-1'),
      'points' => $row['points'],
      'id' => $row['ID_TOPIC'],
      'bname' => $row['bname'],
      'description' => $row['description'],
      'ID_BOARD' => $row['ID_BOARD'],
      'comment' => $row['comment'],
      'posterTime' => $row['posterTime'],
      'ID_COMMENT' => $row['ID_COMMENT'],
    );
  }

  mysqli_free_result($request);

  // Comentarios en mis imágenes
  $request = db_query("
    SELECT p.ID_PICTURE, p.ID_MEMBER, p.title, p.points, g.ID_PICTURE, g.ID_COMMENT, g.comment, g.date
    FROM ({$db_prefix}gallery_comment AS g, {$db_prefix}gallery_pic AS p)
    WHERE p.ID_MEMBER = {$ID_MEMBER}
    AND p.ID_PICTURE = g.ID_PICTURE
    ORDER BY g.ID_COMMENT DESC
    LIMIT " . $modSettings['monitor_image_comments'], __FILE__, __LINE__);

  $context['monitorimg'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $row['subject'] = censorText($row['subject']);
    $context['monitorimg'][] = array(
      'ID_PICTURE' => $row['ID_PICTURE'],
      'ID_COMMENT' => $row['ID_COMMENT'],
      'comment' => $row['comment'],
      'date' => $row['date'],
      'title' => $row['title'],
      'short_title' => htmlentities(ssi_reducir($row['title']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1'),
      'points' => $row['points'],
    );
  }

  mysqli_free_result($request);

  // Mis imágenes en favoritos
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

  while ($row = mysqli_fetch_assoc($request)) {
    $row['title'] = censorText($row['title']);
    $context['monitorfavimagenes'][] = array(
      'title' => $row['title'],
      'short_title' => htmlentities(ssi_reducir($row['title']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1'),
      'points' => $row['points'],
      'ID_PICTURE' => $row['ID_TOPIC'],
      'cmember' => $row['cmember'],
      'ID_BOOKMARK' => $row['ID_BOOKMARK'],
      'realName' => censorText($row['realName']),
    );
  }

  mysqli_free_result($request);

  // Puntos obtenidos (imágenes)
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

  while ($row = mysqli_fetch_assoc($request)) {
    $row['title'] = censorText($row['title']);
    $context['monitorpunimagenes'][] = array(
      'title' => $row['title'],
      'short_title' => htmlentities(ssi_reducir($row['title']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1'),
      'ID_PICTURE' => $row['ID_PICTURE'],
      'cmember' => $row['cmember'],
      'amount' => $row['POINTS'],
      'realName' => censorText($row['realName']),
    );
  }

  mysqli_free_result($request);

  // Puntos obtenidos (posts)
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

  while ($row = mysqli_fetch_assoc($request)) {
    $row['subject'] = censorText($row['subject']);
    $context['monitorpun'][] = array(
      'titulo' => $row['subject'],
      'short_title' => htmlentities(ssi_reducir($row['subject']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['subject'], ENT_QUOTES, 'ISO-8859-1'),
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
  }

  mysqli_free_result($request);

  // Mis posts en favoritos
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

  while ($row = mysqli_fetch_assoc($request)) {
    $row['subject'] = censorText($row['subject']);
    $context['monitorfav'][] = array(
      'titulo' => $row['subject'],
      'short_title' => htmlentities(ssi_reducir($row['subject']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['subject'], ENT_QUOTES, 'ISO-8859-1'),
      'points' => $row['points'],
      'ID_TOPIC' => $row['ID_TOPIC'],
      'cmember' => $row['cmember'],
      'bname' => $row['bname'],
      'description' => $row['description'],
      'ID_BOARD' => $row['ID_BOARD'],
      'ID_BOOKMARK' => $row['ID_BOOKMARK'],
      'realName' => $row['realName'],
    );
  }

  mysqli_free_result($request);

  // Yo en Amigos
  $request = db_query("
    SELECT b.BUDDY_ID, b.ID_MEMBER, mem.ID_MEMBER, mem.memberName, mem.realName, b.time_updated
    FROM ({$db_prefix}buddies AS b, {$db_prefix}members AS mem)
    WHERE b.BUDDY_ID = {$ID_MEMBER}
    AND b.ID_MEMBER = mem.ID_MEMBER
    ORDER BY b.time_updated DESC
    LIMIT " . $modSettings['monitor_friends'], __FILE__, __LINE__);

  $context['yoamigos'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['yoamigos'][] = array(
      'memberName' => censorText($row['memberName']),
      'time_updated' => date("d.m.Y H:i:s", $row['time_updated']),
      'realName' => censorText($row['realName']),
    );
  }

  mysqli_free_result($request);
}

?>