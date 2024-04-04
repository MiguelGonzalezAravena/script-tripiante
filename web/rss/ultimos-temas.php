<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
$request = mysql_query("SELECT * FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY ORDER BY ct.ID_TOPIC DESC LIMIT 0, 25");
echo '<?xml version="1.0" encoding="UTF-8" ?><rss version="0.92" xml:lang="es-es">
<channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Ultimos temas</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>Ultimos 10 temas de las comunidades en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Ultimos temas</title>
<link>' . $boardurl . '/</link>
<description>Ultimos 10 temas de las comunidades en ' . $mbname . '</description>';
while ($row = mysqli_fetch_assoc($request)){
echo '<item><title><![CDATA[' . htmlentities($row['subject'], ENT_QUOTES, "UTF-8") . ' - Comunidad: ' . htmlentities($row['title'], ENT_QUOTES, "UTF-8") . ']]></title>
<link>' . $boardurl . '/comunidades/' . $row['friendly_url'] . '/' . $row['ID_TOPIC'] . '/' . ssi_amigable($row['subject']) . '.html</link>
<description><![CDATA[' . htmlentities($row['body'], ENT_QUOTES, "UTF-8") . ']]></description>
<comments>' . $boardurl . '/comunidades/' . $row['friendly_url'] . '/' . $row['ID_TOPIC'] . '/' . ssi_amigable($row['subject']) . '.html#comentarios</comments></item>';
}
echo '</channel></rss>';
?> 