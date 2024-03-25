<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
$request	=	mysql_query("
SELECT COUNT(c.ID_PICTURE) AS cuenta, p.ID_PICTURE, c.ID_PICTURE, p.title
FROM ({$db_prefix}gallery_pic AS p, {$db_prefix}gallery_comment AS c)
WHERE c.ID_PICTURE = p.ID_PICTURE
GROUP BY p.ID_PICTURE DESC
ORDER BY cuenta DESC
LIMIT 0, 25");
$context['topimagenescom'] = array();
while ($row = mysql_fetch_assoc($request)) {
$context['topimagenescom'][] = array(
'title' => ssi_reducir($row['title']),
'cuenta' => $row['cuenta'],
'ID_PICTURE' => $row['ID_PICTURE'],
);
}
mysql_free_result($request);

echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="spanish"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Imagenes mas comentadas</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>25 imagenes mas comentadas en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Imagenes mas comentadas</title>

<link>' . $boardurl . '/</link>
<description>25 imagenes mas comentadas en ' . $mbname . '</description>';
foreach ($context['topimagenescom'] as $tice) {
echo '<item>
<title><![CDATA[' . htmlentities($tice['title']) . ' - (' . $tice['cuenta']  . ')]]></title>
<link>' . $boardurl . '/imagenes/ver/' . $tice['ID_PICTURE'] . '</link></item>';
}
echo '</channel></rss>';
?> 
