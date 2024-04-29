<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT COUNT(c.ID_COMMENT + gc.ID_COMMENT) AS total, c.ID_MEMBER, mem.ID_MEMBER, mem.realName, mem.memberName, gc.ID_MEMBER, c.ID_COMMENT, gc.ID_COMMENT
  FROM ({$db_prefix}comments as c, {$db_prefix}members as mem, {$db_prefix}gallery_comment AS gc)
  WHERE c.ID_MEMBER = mem.ID_MEMBER
  AND mem.ID_MEMBER = gc.ID_MEMBER
  AND c.ID_MEMBER = gc.ID_MEMBER
  GROUP BY mem.ID_MEMBER
  ORDER BY total DESC
  LIMIT 0, 10", __FILE__, __LINE__);

$title = $mbname . ' - Usuarios que m&aacute;s comentan';
$description = '25 usuarios que m&aacute;s comentan en ' . $mbname;

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

while ($row = mysqli_fetch_assoc($request)) {
  echo '
    <item>
      <title><![CDATA[' . htmlentities(addslashes($row['realName']), ENT_QUOTES, 'UTF-8') . ' (' . $row['total'] . ' com)]]></title>
      <link>' . $boardurl . '/perfil/' . htmlentities(addslashes($row['memberName']), ENT_QUOTES, 'UTF-8'). '</link>
    </item>';
}

echo '
    </channel>
  </rss>';

?>