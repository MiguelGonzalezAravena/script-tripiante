<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$request	=	mysql_query("SELECT COUNT(c.ID_COMMENT + gc.ID_COMMENT) AS total, c.ID_MEMBER, mem.ID_MEMBER, mem.realName, mem.memberName, gc.ID_MEMBER, c.ID_COMMENT, gc.ID_COMMENT
FROM ({$db_prefix}comments as c, {$db_prefix}members as mem, {$db_prefix}gallery_comment AS gc)
WHERE c.ID_MEMBER = mem.ID_MEMBER
AND mem.ID_MEMBER = gc.ID_MEMBER
AND c.ID_MEMBER = gc.ID_MEMBER
GROUP BY mem.ID_MEMBER
ORDER BY total DESC
LIMIT 0, 10");
echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="es-es"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Usuarios que mas comentan</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>25 usuarios que mas comentan en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Usuarios que mas comentan</title>

<link>' . $boardurl . '/</link>
<description>25 usuarios que mas comentan en ' . $mbname . '</description>';
while ($row = mysqli_fetch_assoc($request)) {
echo '<item><title><![CDATA[' . htmlentities($row['realName']) . ' (' . $row['total'] . ' com)]]></title><link>' . $boardurl . '/perfil/' . htmlentities($row['memberName']) . '</link></item>';
}
echo '</channel></rss>';
?> 
