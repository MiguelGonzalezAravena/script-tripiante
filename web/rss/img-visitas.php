<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

$request = mysql_query("
SELECT m.ID_MEMBER, i.ID_MEMBER, i.ID_PICTURE, i.title, m.memberName, m.realName, i.views
FROM ({$db_prefix}gallery_pic AS i, {$db_prefix}members AS m)
WHERE i.ID_MEMBER = m.ID_MEMBER
ORDER BY i.views DESC
LIMIT 0, 25");

echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="spanish"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Imagenes mas vistas</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>Ultimas 25 imagenes mas vistas en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Imagenes mas vistas</title>

<link>' . $boardurl . '/</link>
<description>Ultimas 25 imagenes mas vistas en ' . $mbname . '</description>';
while($row = mysql_fetch_array($request)) {
echo '<item>
<title><![CDATA[' . htmlentities($row['title']) . '  (' . $row['views'] . ')]]></title>
<link>' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '</link>
<description><![CDATA[]]></description>
<comments>' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '#comentar</comments></item>';
}
echo '</channel></rss>';
?> 
