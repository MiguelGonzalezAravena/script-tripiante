<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$id = htmlentities($_REQUEST['id']); 
$request = mysql_query("
SELECT *
FROM ({$db_prefix}messages AS m, {$db_prefix}members AS mem, {$db_prefix}boards AS b)
WHERE m.ID_MEMBER = mem.ID_MEMBER
AND m.ID_BOARD = b.ID_BOARD
AND mem.memberName = '$id'
ORDER BY m.ID_TOPIC DESC
LIMIT 0, 25");
$row2 = mysqli_fetch_assoc($request);
echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="spanish"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Post creados por el usuario: ' . $row2['memberName'] . '</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>Ultimos 25 post creados por el usuario ' . $row2['memberName'] . ' en ' . $mbname . '</description></image>
<title>' . $mbname . ' - Post creados por el usuario: ' . $row2['memberName'] . '</title>

<link>' . $boardurl . '/</link>
<description>Ultimos 25 post creados por el usuario ' . $row2['memberName'] . ' en ' . $mbname . '</description>';
while($row = mysqli_fetch_assoc($request)) {
echo '<item>
<title><![CDATA[' . htmlentities($row['subject']) . ']]></title>
<link>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html</link><description><![CDATA[]]></description>
<comments>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html</comments></item>';
}
echo '</channel></rss>';
?> 