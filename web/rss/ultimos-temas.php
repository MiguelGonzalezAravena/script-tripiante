<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT * FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c
  WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
  ORDER BY ct.ID_TOPIC DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$title = $mbname . ' - &Uacute;ltimos temas';
$description = '&Uacute;ltimos 10 temas de las comunidades en ' . $mbname;

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
  $link = $boardurl . '/comunidades/' . $row['friendly_url'] . '/' . $row['ID_TOPIC'] . '/' . ssi_amigable($row['subject']) . '.html';

  echo '
    <item>
      <title><![CDATA[' . htmlentities(addslashes($row['subject']), ENT_QUOTES, 'UTF-8') . ' - Comunidad: ' . htmlentities(addslashes($row['title']), ENT_QUOTES, 'UTF-8') . ']]></title>
      <link>' . $link . '</link>
      <description><![CDATA[' . htmlentities($row['body'], ENT_QUOTES, 'UTF-8') . ']]></description>
      <comments>' . $link . '#comentarios</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>