<?php
if (!defined('SMF'))
  die('Hacking attempt...');

@require_once('SSI.php');

function Bookmarks() {
  global $txt, $context, $ID_MEMBER, $return, $db_prefix, $boardurl;

  loadTemplate('Bookmarks2');
  loadLanguage('Bookmarks2');
  isAllowedTo('make_bookmarks');

  $context['page_title'] = $txt['bookmarks'];
  $context['sub_action'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : '';

  switch ($context['sub_action']) {
    case 'delete':
    $return = !empty($_POST['remove_bookmarks']) ? deleteBookmark($_POST['remove_bookmarks']) : '';
    break;
  }

  $request = db_query("
    SELECT ms.date, ms.points, ms.ID_PICTURE, ms.title, ms.ID_MEMBER, mem.realName, mem.ID_MEMBER, mem.memberName, bm.ID_MEMBER, bm.TYPE, bm.ID_TOPIC
    FROM ({$db_prefix}bookmarks AS bm, {$db_prefix}gallery_pic AS ms, {$db_prefix}members AS mem)
    WHERE bm.ID_MEMBER = $ID_MEMBER
    AND ms.ID_PICTURE = bm.ID_TOPIC
    AND mem.ID_MEMBER = ms.ID_MEMBER
    AND bm.TYPE = 'imagen'
    ORDER BY bm.ID_TOPIC DESC", __FILE__, __LINE__);

  $context['bookmarks2'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['bookmarks2'][] = array(
      'id' => $row['ID_PICTURE'],
      'memberName' => $row['memberName'],
      'realName' => $row['realName'],
      'points' => $row['points'],
      'poster' => array(
        'id' => $row['ID_MEMBER'],
        'name' => $row['realName'],
        'href' => empty($row['ID_MEMBER']) ? '' : $boardurl . '/perfil/' .$row['realName'],
        'link' => empty($row['ID_MEMBER']) ? $row['realName'] : '<a href="' . $boardurl . '/perfil/' . $row['realName'] . '">' . $row['realName'] . '</a>'
      ),
      'title' => censorText($row['title']),
      'new_href' => $boardurl . '/imagenes/ver/' . $row['ID_TOPIC'] . '/',
    );
  }

  mysqli_free_result($request);
}

function deleteBookmark($topic_ids, $id_member = null) {
  global $txt, $context, $db_prefix, $db_connection;

  if ($id_member == null)
    $id_member = $context['user']['id'];

  foreach ($topic_ids as $index => $id)
    $topic_ids[$index] = (int) $id;

  $topics = implode(',', $topic_ids);

  $result = db_query("
    DELETE FROM {$db_prefix}bookmarks
    WHERE ID_TOPIC IN($topics)
    AND ID_MEMBER = $id_member
    AND TYPE = 'imagen'", __FILE__, __LINE__);

  $deleted = mysqli_affected_rows($db_connection);

  if ($result)
    return sprintf($txt['bookmark_delete_success'], $deleted);
  else
    return sprintf($txt['bookmark_delete_failure'], $deleted);
}
?>