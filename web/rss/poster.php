<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$request = mysql_query("SELECT m.realName, m.posts FROM {$db_prefix}members AS m ORDER BY m.posts DESC LIMIT 0, 25");
$context['rssuser'] = array();
while ($row = mysqli_fetch_assoc($request)) {
$row['realName'] = parse_bbc($row['realName'], 1, $row['posts']); 
$row['realName'] = strtr($func['substr'](str_replace('<br />', "\n", $row['realName']), 0, 400 - 3), array("\n" => '<br />'));
censorText($row['realName']);
censorText($row['subject']);
$context['rssuser'][] = array(
  'posts' => $row['posts'],
  'realName' => $row['realName'],
);
}
mysqli_free_result($request);

echo'<?xml version="1.0" encoding="UTF-8"?>
<rss version="0.92" xml:lang="spanish">
  <channel>
   <image>
    <url>' . $boardurl  .'/images/rss.png</url>
    <title>' . $mbname . ' -  Top poster</title>
    <link>' . $boardurl . '/</link>

    <width>111</width>
    <height>32</height>
    <description>Usuarios con mas posts en ' . $mbname . '</description>
  </image>
      <title>' . $mbname . ' -  Top poster</title>
    <link>' . $boardurl . '</link>
    <description>Usuarios con mas posts en ' . $mbname . '</description>';
foreach($context['rssuser'] as $rssuser){
echo '<item><title><![CDATA[' . $rssuser['realName'] . ' (' . $rssuser['posts'] . ')]]></title>
<link>' . $boardurl . '/perfil/' . $rssuser['realName'] . '</link>
<description><![CDATA[]]></description>
<comments>' . $boardurl . '/perfil/' . $rssuser['realName'] . '</comments></item>';
}
echo '</channel>
</rss>
';
?> 