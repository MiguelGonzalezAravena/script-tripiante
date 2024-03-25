<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$request = mysql_query("
SELECT m.subject, m.ID_TOPIC, t.ID_TOPIC, t.ID_BOARD, t.points, b.name AS bname
FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
WHERE t.ID_TOPIC = m.ID_TOPIC
AND t.ID_BOARD = b.ID_BOARD
ORDER BY t.points DESC
LIMIT 0, 25");

echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="spanish"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Post con mayor puntaje</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>Ultimos 25 post con mayor puntaje en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Post con mayor puntaje</title>

<link>' . $boardurl . '/</link>
<description>Ultimos 25 post con mayor puntaje en ' . $mbname . '</description>';
while ($row =	mysql_fetch_array($request)) {
echo '<item>
<title><![CDATA[' . htmlentities($row['subject']) . ' (' . $row['points'] . ')]]></title>
<link>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html</link>
<description><![CDATA[]]></description>
<comments>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html#comentar</comments></item>';
}
echo '</channel></rss>';
?> 
