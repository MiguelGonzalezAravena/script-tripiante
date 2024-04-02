<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$request = mysql_query("
SELECT m.subject, t.numViews, t.ID_BOARD, t.ID_TOPIC, b.name, b.description
FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
WHERE t.ID_BOARD = b.ID_BOARD
AND m.ID_TOPIC = t.ID_TOPIC
ORDER BY t.numViews DESC
LIMIT 0, 25");

echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="spanish"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Post mas visitados</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>Ultimos 25 post mas visitados en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Post mas visitados</title>

<link>' . $boardurl . '/</link>
<description>Ultimos 25 post mas visitados en ' . $mbname . '</description>
';
while ($row =	mysqli_fetch_array($request)) {
  echo '<item>
  <title><![CDATA[' . htmlentities($row['subject']) . ' (' . htmlentities($row['numViews']) . ')]]></title>
  <link>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html</link>
  <description><![CDATA[]]></description>
  <comments>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html#comentar</comments></item>';
}
echo '</channel></rss>';
?> 
