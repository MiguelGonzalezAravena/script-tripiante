<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$id = (int) $_REQUEST['id'];
$count = 1;

$request = db_query("
  SELECT *
  FROM ({$db_prefix}community_comments AS cc, {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c, {$db_prefix}members AS mem)
  WHERE cc.ID_TOPIC = ct.ID_TOPIC
  AND ct.ID_COMMUNITY = c.ID_COMMUNITY
  AND cc.ID_MEMBER = mem.ID_MEMBER
  AND cc.ID_TOPIC = $id
  ORDER BY cc.ID_COMMENT ASC
  LIMIT 0, 25", __FILE__, __LINE__);

$row = mysqli_fetch_assoc($request);

echo '
<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="spanish">
    <channel>
      <image>
        <url>' . $boardurl . '/images/rss.png</url>
        <title>' . $mbname . ' - Comentarios para el tema: ' . $row['subject'] . '</title>
        <link>' . $boardurl . '/</link>
        <width>111</width>
        <height>32</height>
        <description>Comentarios para el tema ' . $row['subject'] . '</description>
      </image>
      <title>' . $mbname . ' - Comentarios para el tema: ' . $row['subject'] . '</title>
      <link>' . $boardurl . '/</link>
      <description>Comentarios para el tema ' . $row['subject'] . '</description>';

while ($row = mysqli_fetch_assoc($request)) {
  $link = $boardurl . '/comunidades/' . $row['friendly_url'] . '/' . $row['ID_TOPIC'] . '/' . ssi_amigable($row2['subject']) . '.html';

  echo '
    <item>
      <title><![CDATA[#' . $count++ . ' Comentario de ' . htmlentities($row['memberName'], ENT_QUOTES, 'UTF-8') . ']]></title>
      <link>' . $link . '#comentarios</link>
      <description><![CDATA[' . htmlentities($row['comment'], ENT_QUOTES, 'UTF-8') . ']]></description>
      <comments>' . $link . '#comentar</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>