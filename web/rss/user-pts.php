<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT *
  FROM {$db_prefix}members
  ORDER BY moneyBank DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$title = $mbname . ' - Top user post puntos';
$description = 'Usuarios con m&aacute;s puntos de ' . $mbname;

echo '<?xml version="1.0" encoding="UTF-8"?>
  <rss version="0.92" xml:lang="spanish">
    <channel>
      <image>
        <url>' . $boardurl . '/images/rss.png</url>
        <title>' . $title . '</title>
        <link>' . $boardurl .'/</link>
        <width>111</width>
        <height>32</height>
        <description>' . $description . '</description>
      </image>
      <title>' . $title . '</title>
      <link>' . $boardurl .'/</link>
      <description>' . $description . '</description>';

while ($row = mysqli_fetch_assoc($request)) {
  $link = $boardurl . '/perfil/' . htmlentities(addslashes($row['memberName']), ENT_QUOTES, 'UTF-8');

  echo '
    <item>
      <title><![CDATA[' . htmlentities(addslashes($row['realName']), ENT_QUOTES, 'UTF-8') . '&nbsp;(' . $row['moneyBank'] . ')]]></title>
      <link>' . $link . '</link>
      <description></description>
      <comments>' . $link . '</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>