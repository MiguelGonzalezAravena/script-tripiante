<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$id = (int) $_REQUEST['id'];

$request = db_query("
  SELECT
    c.ID_TOPIC, c.ID_MEMBER, c.ID_COMMENT, c.comment, mem.ID_MEMBER,
    mem.memberName, t.ID_BOARD, b.ID_BOARD, b.description, m.subject, m.ID_TOPIC
  FROM ({$db_prefix}comments AS c, {$db_prefix}members AS mem, {$db_prefix}topics AS t, {$db_prefix}boards AS b, {$db_prefix}messages AS m) 
  WHERE c.ID_TOPIC = $id
  AND c.ID_MEMBER = mem.ID_MEMBER
  AND t.ID_BOARD = b.ID_BOARD
  AND c.ID_TOPIC = m.ID_TOPIC
  AND m.ID_TOPIC = t.ID_TOPIC
  AND t.ID_TOPIC = c.ID_TOPIC
  ORDER BY c.ID_COMMENT ASC
  LIMIT 0, 100", __FILE__, __LINE__);

$row = mysqli_fetch_assoc($request);

$title = $mbname . ' - Comentarios para el post: ' . htmlentities($row['subject']);
$description = 'Comentarios para el post ' . htmlentities($row['subject']) . ' de ' . $mbname;
$count = 0;

echo '<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="spanish">
    <channel>
      <image>
        <url>' . $boardurl . '/images/rss.png</url>
        <title>' . $title . '</title>
        <link>' . $boardurl . '/</link>
        <width>111</width>
        <height>32</height>
        <description>' . $description . '</description>
      </image>
      <title>' . $title . '</title>
      <link>' . $boardurl . '/</link>
      <description>' . $description . '</description>';

while ($row = mysqli_fetch_assoc($request)) {
  $link = $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html';

  echo '
    <item>
      <title><![CDATA[#' . $count++ . ' Comentario de ' . htmlentities($row['memberName']) . ']]></title>
      <link>' . $link . '</link>
      <description><![CDATA[' . htmlentities($row['comment']) . ']]></description>
      <comments>' . $link . '#comentar</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>