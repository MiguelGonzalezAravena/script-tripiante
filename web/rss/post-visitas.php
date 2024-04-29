<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT m.subject, t.numViews, t.ID_BOARD, t.ID_TOPIC, b.name, b.description
  FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
  WHERE t.ID_BOARD = b.ID_BOARD
  AND m.ID_TOPIC = t.ID_TOPIC
  ORDER BY t.numViews DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$title = $mbname . ' - Post m&aacute;s visitados';
$description = '&Uacute;ltimos 25 post m&aacute;s visitados en ' . $mbname;

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
      <title><![CDATA[' . htmlentities(addslashes($row['subject']), ENT_QUOTES, 'UTF-8') . '&nbsp;(' . $row['numViews'] . ')]]></title>
      <link>' . $link . '</link>
      <description><![CDATA[]]></description>
      <comments>' . $link . '#comentar</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>