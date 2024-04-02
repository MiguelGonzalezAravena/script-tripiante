<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$request	=	mysql_query("
SELECT COUNT(p.ID_MEMBER) AS cuenta, p.ID_PICTURE, mem.ID_MEMBER, mem.memberName, mem.realName, p.ID_MEMBER
FROM ({$db_prefix}gallery_pic AS p, {$db_prefix}members AS mem)
WHERE p.ID_MEMBER = mem.ID_MEMBER
GROUP BY mem.ID_MEMBER DESC
ORDER BY cuenta DESC
LIMIT 0, 25");
$context['imagenuser'] = array();
while ($row = mysqli_fetch_assoc($request)) {
$context['imagenuser'][] = array(
'realName' => $row['realName'],
'memberName' => $row['memberName'],
'cuenta' => $row['cuenta'],
);
}
mysqli_free_result($request);

echo'<?xml version="1.0" encoding="UTF-8"?>
<rss version="0.92" xml:lang="spanish">
	<channel>
	 <image>
    <url>' . $boardurl  .'/images/rss.png</url>
    <title>' . $mbname . ' -  Usuarios con mas imagenes</title>
    <link>' . $boardurl . '/</link>

    <width>111</width>
    <height>32</height>
    <description>25 Usuarios con mas imagenes en ' . $mbname . '</description>
  </image>
	    <title>' . $mbname . ' -  Usuarios con mas imagenes</title>
    <link>' . $boardurl . '</link>
    <description>25 Usuarios con mas imagenes en ' . $mbname . '</description>';
foreach ($context['imagenuser'] as $imagenuser) {
echo '<item><title><![CDATA[' . $imagenuser['realName'] . ' (' . $imagenuser['cuenta'] . ')]]></title>
<link>' . $boardurl . '/perfil/' . $imagenuser['memberName'] . '</link>
<description><![CDATA[]]></description>
<comments>' . $boardurl . '/perfil/' . $imagenuser['memberName'] . '</comments></item>';
}
echo'	</channel>
</rss>
';  ?> 
