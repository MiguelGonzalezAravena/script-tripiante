<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
$request	=	mysql_query("
SELECT COUNT(c.COMMENT_MEMBER_ID) AS cuenta, c.COMMENT_MEMBER_ID, mem.ID_MEMBER, mem.memberName, mem.realName
FROM ({$db_prefix}members AS mem, {$db_prefix}profile_comments AS c)
WHERE c.COMMENT_MEMBER_ID = mem.ID_MEMBER
GROUP BY mem.ID_MEMBER DESC
ORDER BY cuenta DESC
LIMIT 0, 25");
$context['muroscomentados'] = array();
while ($row = mysql_fetch_assoc($request)) {
$context['muroscomentados'][] = array(
'realName' => $row['realName'],
'memberName' => $row['memberName'],
'cuenta' => $row['cuenta'],
);
}
mysql_free_result($request);

echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="es-es"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Muros mas comentados</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>25 muros mas comentados en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Muros mas comentados</title>

<link>' . $boardurl . '/</link>
<description>25 muros mas comentados en ' . $mbname . '</description>';
foreach ($context['muroscomentados'] as $muro) {
echo '<item><title><![CDATA[' . $muro['memberName'] . ' (' . $muro['cuenta'] . ')]]></title><link>' . $boardurl . '/perfil/' . $muro['realName'] . '</link></item>';
}
echo '</channel></rss>';
?> 
