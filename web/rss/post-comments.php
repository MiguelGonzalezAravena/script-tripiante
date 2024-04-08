<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT t.ID_TOPIC, COUNT(c.ID_TOPIC) as Cuenta, t.subject, t.ID_BOARD, b.name AS bname, b.description, c.ID_TOPIC
  FROM ({$db_prefix}comments as c, {$db_prefix}messages as t, {$db_prefix}boards AS b)
  WHERE t.ID_TOPIC = c.ID_TOPIC
  AND t.ID_BOARD = b.ID_BOARD
  GROUP BY c.ID_TOPIC
  ORDER BY Cuenta DESC
  LIMIT 25", __FILE__, __LINE__);

$title = $mbname . ' - Posts m&aacute;s comentados';
$description = '&Uacute;ltimos 10 posts m&aacute;s comentados en ' . $mbname;

$context['tcomentados'] = array();

while ($row = mysqli_fetch_assoc($request)) {
  $context['tcomentados'][] = array(
    'subject' => ssi_reducir(censorText($row['subject'])),
    'cuenta' => $row['Cuenta'],
    'ID_TOPIC' => $row['ID_TOPIC'],
    'description' => $row['description'],
    'bname' => $row['bname'],
  );
}

mysqli_free_result($request);

echo '<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="spanish">
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

foreach ($context['tcomentados'] as $row) {
  $link = $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html';

  echo '
    <item>
      <title><![CDATA[' . htmlentities($row['subject']) . '&nbsp;(' . $row['cuenta'] . ')]]></title>
      <link>' . $link . '</link>
      <description><![CDATA[]]></description>
      <comments>' . $link . '#comentar</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?> 
