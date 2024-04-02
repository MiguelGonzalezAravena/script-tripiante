<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function RecentPosts() {
  global $context, $txt, $db_prefix;

  loadTemplate('Recent');
  $context['page_title'] = $txt[214];

  $request = db_query("
    SELECT *
    FROM {$db_prefix}comments as c, {$db_prefix}topics AS t
    WHERE c.ID_TOPIC = t.ID_TOPIC", __FILE__, __LINE__);

  $request2 = db_query("
    SELECT *
    FROM {$db_prefix}gallery_pic AS p, {$db_prefix}gallery_comment AS c
    WHERE p.ID_PICTURE = c.ID_PICTURE", __FILE__, __LINE__);

  $context['total_comments'] = mysqli_num_rows($request) + mysqli_num_rows($request2);
}

?>