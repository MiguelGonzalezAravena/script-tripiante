<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function PrintImg() {
  global $db_prefix, $img, $context;

  $img = (int) $_REQUEST['img'];

  if (empty($img))
    fatal_lang_error(472, false);

  // Get the topic starter information.
  $request = db_query("
    SELECT g.date, g.ID_MEMBER, mem.memberName, mem.realName, g.ID_PICTURE, mem.ID_MEMBER, g.filename
    FROM ({$db_prefix}gallery_pic AS g, {$db_prefix}members AS mem)
    WHERE g.ID_PICTURE = $img
    AND mem.ID_MEMBER = g.ID_MEMBER
    ORDER BY g.ID_PICTURE DESC
    LIMIT 1", __FILE__, __LINE__);

  if (mysqli_num_rows($request) == 0)
    fatal_lang_error('smf232');

  $row = mysqli_fetch_assoc($request);

  mysqli_free_result($request);

  // Lets "output" all that info.
  loadTemplate('Printpage2');
  $context['template_layers'] = array('print');
  $context['poster_name'] = $row['memberName'];
  $context['date'] = timeformat($row['date'], false);

  // Split the topics up so we can print them.
  $request = db_query("
    SELECT g.ID_PICTURE, g.title, g.date, g.ID_MEMBER, mem.ID_MEMBER, g.filename
    FROM ({$db_prefix}gallery_pic AS g, {$db_prefix}members AS mem)
    WHERE g.ID_PICTURE = $img
    AND mem.ID_MEMBER = g.ID_MEMBER
    ORDER BY g.ID_PICTURE DESC
    LIMIT 1", __FILE__, __LINE__);

  $context['image'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    // Censor the subject and message.
    censorText($row['title']);

    $context['image'][] = array(
      'title' => $row['title'],
      'ID_PICTURE' => $row['ID_PICTURE'],
      'filename' => $row['filename'],
      'member' => $row['memberName'],
      'time' =>  timeformat($row['date'], false),
      'timestamp' => forum_time(true, $row['date']),
    );

    if (!isset($context['topic_subject']))
      $context['topic_subject'] = $row['title'];
  }

  mysqli_free_result($request);
}

?>