<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

$id = (int) htmlentities($_REQUEST['id']);

$request = mysql_query("
SELECT c.ID_TOPIC, c.ID_MEMBER, c.ID_COMMENT, c.comment, mem.ID_MEMBER, mem.memberName, t.ID_BOARD, b.ID_BOARD, b.description, m.subject, m.ID_TOPIC
FROM ({$db_prefix}comments AS c, {$db_prefix}members AS mem, {$db_prefix}topics AS t, {$db_prefix}boards AS b, {$db_prefix}messages AS m) 
WHERE c.ID_TOPIC = $id
AND c.ID_MEMBER = mem.ID_MEMBER
AND t.ID_BOARD = b.ID_BOARD
AND c.ID_TOPIC = m.ID_TOPIC
AND m.ID_TOPIC = t.ID_TOPIC
AND t.ID_TOPIC = c.ID_TOPIC
ORDER BY c.ID_COMMENT ASC
LIMIT 0, 100
");
$row2 = mysql_fetch_assoc($request);
echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="spanish"><channel><image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Comentarios para el post: ' . htmlentities($row2['subject']) . '</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>Comentarios para el post ' . htmlentities($row2['subject']) . ' de ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Comentarios para el post: ' . htmlentities($row2['subject']) . '</title>
<link>' . $boardurl . '/</link>
<description>Comentarios para el post ' . htmlentities($row2['subject']) . ' de ' . $mbname . '</description>';
$count++;
while ($row = mysql_fetch_assoc($request)) {
echo '<item>
<title><![CDATA[#' . $count++ . ' Comentario de ' . htmlentities($row['memberName']) . ']]></title>
<link>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html</link><description><![CDATA[' . htmlentities($row['comment']) . ']]></description>
<comments>' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html#comentar</comments>
</item>';
}
echo '</channel></rss>';
?> 
