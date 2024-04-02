<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
$request = mysql_query("
SELECT t.ID_TOPIC, COUNT(c.ID_TOPIC) as Cuenta, t.subject, t.ID_BOARD, b.name AS bname, b.description, c.ID_TOPIC
FROM ({$db_prefix}comments as c, {$db_prefix}messages as t, {$db_prefix}boards AS b)
WHERE t.ID_TOPIC = c.ID_TOPIC
AND t.ID_BOARD = b.ID_BOARD
GROUP BY c.ID_TOPIC
ORDER BY Cuenta DESC
LIMIT 25
", __FILE__, __LINE__);
$context['tcomentados'] = array();
while ($row = mysqli_fetch_assoc($request)){
$context['tcomentados'][] = array(
'subject' => ssi_reducir($row['subject']),
'cuenta' => $row['Cuenta'],
'ID_TOPIC' => $row['ID_TOPIC'],
'description' => $row['description'],
'bname' => $row['bname'],
);
}
mysqli_free_result($request);

echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="spanish"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Posts mas comentados</title>
<link>' . $boardurl . '/</link>
<width>111</width>
<height>32</height>
<description>Ultimos 10 posts mas comentados en ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Posts mas comentados</title>

<link>' . $boardurl . '/</link>
<description>Ultimos 10 posts mas comentados en ' . $mbname . '</description>';
foreach($context['tcomentados'] as $total) {
echo '<item>
<title><![CDATA[' . htmlentities($total['subject']) . ' (' . $total['cuenta'] . ')]]></title>
<link>' . $boardurl . '/post/' . $total['ID_TOPIC'] . '/' . $total['description'] . '/' . ssi_amigable($total['subject']) . '.html</link>
<description><![CDATA[]]></description>
<comments>' . $boardurl . '/post/' . $total['ID_TOPIC'] . '/' . $total['description'] . '/' . ssi_amigable($total['subject']) . '.html#comentar</comments></item>';
}
echo '</channel></rss>';
?> 
