<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT COUNT(c.COMMENT_MEMBER_ID) AS cuenta, c.COMMENT_MEMBER_ID, mem.ID_MEMBER, mem.memberName, mem.realName
  FROM ({$db_prefix}members AS mem, {$db_prefix}profile_comments AS c)
  WHERE c.COMMENT_MEMBER_ID = mem.ID_MEMBER
  GROUP BY mem.ID_MEMBER DESC
  ORDER BY cuenta DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$context['muroscomentados'] = array();

while ($row = mysqli_fetch_assoc($request)) {
  $context['muroscomentados'][] = array(
    'realName' => censorText($row['realName']),
    'memberName' => censorText($row['memberName']),
    'cuenta' => $row['cuenta'],
  );
}

mysqli_free_result($request);

$title = $mbname . ' - Muros m&aacute;s comentados';
$description = '25 muros m&aacute;s comentados en ' . $mbname;

echo '<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="es-es">
    <channel>
      <image>
        <url>' . $boardurl . '/images/rss.png</url>
        <title>' . $title . '</title>
        <link>' . $boardurl . '/</link>
        <width>111</width>
        <height>32</height>
        <description>' . $description . '</description>
      </image>
      <title>' . $title . '</title>
      <link>' . $boardurl . '/</link>
      <description>' . $description . '</description>';

foreach ($context['muroscomentados'] as $row) {
  echo '
    <item>
      <title><![CDATA[' . $row['memberName'] . '&nbsp;(' . $row['cuenta'] . ')]]></title>
      <link>' . $boardurl . '/perfil/' . $row['realName'] . '</link>
    </item>';
}

echo '
    </channel>
  </rss>';

?>
