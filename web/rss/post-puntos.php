<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT m.subject, m.ID_TOPIC, t.ID_TOPIC, t.ID_BOARD, t.points, b.name AS bname, b.description
  FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
  WHERE t.ID_TOPIC = m.ID_TOPIC
  AND t.ID_BOARD = b.ID_BOARD
  ORDER BY t.points DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$title = $mbname . ' - Post con mayor puntaje';
$description = '&Uacute;ltimos 25 post con mayor puntaje en ' . $mbname;

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

while ($row = mysqli_fetch_array($request)) {
  $link = $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html';

  echo '
    <item>
      <title><![CDATA[' . htmlentities($row['subject']) . '&nbsp;(' . $row['points'] . ')]]></title>
      <link>' . $link . '</link>
      <description><![CDATA[]]></description>
      <comments>' . $link . '#comentar</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>