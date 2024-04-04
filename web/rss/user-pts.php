<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$request = mysql_query("SELECT * FROM {$db_prefix}members ORDER BY moneyBank DESC LIMIT 0, 25");
echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="0.92" xml:lang="spanish"><channel>
<image>
<url>/images/rss.png</url>
<title>' . $mbname . ' - Top user post puntos</title>
<link>' . $boardurl  .'/</link>
<width>111</width>
<height>32</height>
<description>Usuarios con mas puntos de ' . $mbname . '</description>
</image>
<title>' . $mbname . ' - Top user post puntos</title>

<link>' . $boardurl  .'/</link>
<description>Usuarios con mas puntos de ' . $mbname . '</description>';
while ($row = mysqli_fetch_assoc($request)) {
echo '<item>
<title><![CDATA[' . htmlentities($row['realName']) . ' (' . $row['moneyBank'] . ')]]></title>
<link>' . $boardurl . '/perfil/' . htmlentities($row['memberName']) . '</link>
<description></description>
<comments>' . $boardurl . '/perfil/' . htmlentities($row['memberName']) . '</comments></item>';
}
echo '</channel></rss>';
?>