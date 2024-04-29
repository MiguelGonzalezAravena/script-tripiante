<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT *FROM ({$db_prefix}comments AS c, {$db_prefix}members AS mem, {$db_prefix}messages AS m)WHERE c.ID_MEMBER = mem.ID_MEMBER
  AND m.ID_TOPIC = c.ID_TOPIC
  GROUP BY c.ID_TOPICORDER BY c.ID_COMMENT DESC
  LIMIT 25", __FILE__, __LINE__);

$context['comment'] = array();

while ($row = mysqli_fetch_assoc($request)) {
  $row['comment'] = parse_bbc($row['comment'], 1, $row['ID_TOPIC']);
  $row['comment'] = strtr(substr(str_replace('<br />', "\n", $row['comment']), 0, 400 - 3), array("\n" => '<br />'));

  $context['comments'][] = array(
    'comment' => censorText($row['comment']),
    'titulo' => censorText($row['subject']),
    'nom-user' => $row['realName'],
    'id_comment' => $row['id_coment'],
    'id' => $row['id_post'],
  );
}

mysqli_free_result($request);

$contando = 1;
$title = $mbname . ' - Comentarios de los post';
$description = '&Uacute;ltimos 25 comentarios de los post en ' . $mbname;

echo '<?xml version="1.0" encoding="UTF-8"?>
  <rss version="0.92" xml:lang="spanish">
    <channel>
      <image>
        <url>' . $boardurl . '/images/rss.png</url>
        <title>' . $title . '</title>
        <link>' . $boardurl . '</link>
        <width>111</width>
        <height>32</height>
        <description>' . $description . '</description>
      </image>
      <title>' . $title . '</title>
      <link>' . $boardurl . '</link>
      <description>' . $description . '</description>';

foreach($context['comments'] as $row) {
  echo '
    <item>
      <title><![CDATA[' . $row['nom-user'] . ' - ' . $row['titulo'] .']]></title>
      <link>' . $boardurl . '/post/' . $row['id'] . '#cmt_' . $row['id_comment'] .'</link>
      <description><![CDATA[' . $row['comment'] . ']]>
      </description>
      <comments>' . $boardurl . '/post/' . $row['id'] . '#comentar</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>